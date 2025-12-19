<?php
// gallery-import.php
// Import images from /uploads/images/ into the gallery table

require_once __DIR__ . '/../includes/config.php';

$folder = __DIR__ . '/../uploads/images/';
$files = glob($folder . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

if (!$files) {
    exit("No image files found in $folder");
}

$added = 0;
$skipped = 0;

foreach ($files as $file) {
    $filename = basename($file);

    try {
        // Check if the file already exists in DB
        $stmt = $pdo->prepare("SELECT id FROM gallery WHERE filename = ?");
        $stmt->execute([$filename]);

        if ($stmt->rowCount() > 0) {
            echo "Skipped (already exists): $filename<br>";
            $skipped++;
            continue;
        }

        // Insert new image entry
        $insert = $pdo->prepare("
            INSERT INTO gallery (filename, caption, category, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $insert->execute([
            $filename,
            null,        // caption (can edit later in admin)
            'default'    // category
        ]);

        echo "Added: $filename<br>";
        $added++;

    } catch (PDOException $e) {
        echo "Error importing $filename: " . $e->getMessage() . "<br>";
    }
}

echo "<hr>";
echo "Import complete! Added: $added, Skipped: $skipped.";
?>
