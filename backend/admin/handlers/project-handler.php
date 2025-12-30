<?php

declare(strict_types=1);

/**
 * Admin – Project Handler (Cloudinary Optimized)
 * Path: /backend/admin/handlers/project-handler.php
 */

/* -------------------------------------------------
 | 1. Bootstrap & Environment
 * ------------------------------------------------- */

$baseDir = dirname(__DIR__, 3);

if (!file_exists($baseDir . '/vendor/autoload.php')) {
    $baseDir = dirname(__DIR__, 2);
}

require_once $baseDir . '/vendor/autoload.php';
require_once $baseDir . '/backend/includes/config.php';
require_once $baseDir . '/backend/admin/includes/csrf.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

/* -------------------------------------------------
 | 2. Cloudinary Init
 * ------------------------------------------------- */

if (!getenv('CLOUDINARY_URL')) {
    header('Location: ../projects.php?error=cloudinary_missing');
    exit;
}

Configuration::instance(getenv('CLOUDINARY_URL'));

/* -------------------------------------------------
 | 3. Helpers
 * ------------------------------------------------- */

function generateUniqueProjectSlug(string $title, PDO $pdo, int $excludeId = 0): string
{
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));

    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM projects WHERE slug = ? AND id != ?'
    );
    $stmt->execute([$slug, $excludeId]);

    if ($stmt->fetchColumn() > 0) {
        $slug .= '-' . bin2hex(random_bytes(2));
    }

    return $slug;
}

function uploadProjectMedia(array $file, string $mediaType): ?string
{
    if (empty($file['tmp_name'])) {
        return null;
    }

    try {
        $upload = new UploadApi();

        $result = $upload->upload($file['tmp_name'], [
            'folder'        => 'reseed/projects',
            'resource_type' => $mediaType === 'video' ? 'video' : 'image',
            'quality'       => 'auto',
            'fetch_format'  => 'auto'
        ]);

        return $result['secure_url'] ?? null;

    } catch (Throwable $e) {
        error_log('Project Media Upload Error: ' . $e->getMessage());
        return null;
    }
}

/* -------------------------------------------------
 | 4. Guard
 * ------------------------------------------------- */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../projects.php');
    exit;
}

csrf_verify($_POST['csrf_token'] ?? null);

/* -------------------------------------------------
 | 5. Input
 * ------------------------------------------------- */

$id          = isset($_POST['id']) ? (int) $_POST['id'] : null;
$title       = trim($_POST['title'] ?? '');
$location    = trim($_POST['location'] ?? '');
$summary     = trim($_POST['summary'] ?? '');
$description = trim($_POST['description'] ?? '');
$status      = $_POST['status'] ?? 'Planned';
$startDate   = $_POST['start_date'] ?? null;
$endDate     = $_POST['end_date'] ?? null;
$featured    = isset($_POST['featured']) ? 1 : 0;
$mediaType   = $_POST['media_type'] ?? 'image';

/* -------------------------------------------------
 | 6. Media Resolution
 * ------------------------------------------------- */

$coverMedia = null;

if ($mediaType === 'url') {
    $coverMedia = filter_var($_POST['media_url'] ?? '', FILTER_SANITIZE_URL) ?: null;
} else {
    $coverMedia = uploadProjectMedia($_FILES['media_file'] ?? [], $mediaType);
}

/* -------------------------------------------------
 | 7. Transaction
 * ------------------------------------------------- */

try {
    $pdo->beginTransaction();

    $slugInput = trim($_POST['slug'] ?? '');
    $slugBase  = $slugInput !== '' ? $slugInput : $title;
    $slug      = generateUniqueProjectSlug($slugBase, $pdo, $id ?? 0);

    if (isset($_POST['add'])) {

        $sql = "
            INSERT INTO projects (
                title, slug, location, summary, description,
                status, start_date, end_date, featured,
                cover_media, media_type, created_at
            ) VALUES (
                :title, :slug, :location, :summary, :description,
                :status, :start_date, :end_date, :featured,
                :cover_media, :media_type, NOW()
            )
        ";

    } else {

        $mediaSql = $coverMedia ? ', cover_media = :cover_media, media_type = :media_type' : '';

        $sql = "
            UPDATE projects SET
                title = :title,
                slug = :slug,
                location = :location,
                summary = :summary,
                description = :description,
                status = :status,
                start_date = :start_date,
                end_date = :end_date,
                featured = :featured
                $mediaSql
            WHERE id = :id
        ";
    }

    $stmt = $pdo->prepare($sql);

    $params = [
        'title'       => $title,
        'slug'        => $slug,
        'location'    => $location,
        'summary'     => $summary,
        'description' => $description,
        'status'      => $status,
        'start_date'  => $startDate,
        'end_date'    => $endDate,
        'featured'    => $featured
    ];

    if ($id) {
        $params['id'] = $id;
    }

    if ($coverMedia || isset($_POST['add'])) {
        $params['cover_media'] = $coverMedia;
        $params['media_type']  = $mediaType;
    }

    $stmt->execute($params);
    $pdo->commit();

    $action = isset($_POST['add']) ? 'created' : 'updated';
    header("Location: ../projects.php?success=$action");
    exit;

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Project Handler Error: ' . $e->getMessage());
    header('Location: ../projects.php?error=db_failure');
    exit;
}
