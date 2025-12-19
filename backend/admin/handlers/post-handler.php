<?php
include "../../includes/config.php";

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/reseed/uploads/posts/';
if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

// ADD
if(isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']) ?: strtolower(preg_replace('/[^a-z0-9]+/','-', $title));
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $author = trim($_POST['author']);
    $published_at = $_POST['published_at'] ?: null;
    $media_type = $_POST['media_type'];
    $media_url = $_POST['media_url'] ?: null;
    $featured = isset($_POST['featured']) ? 1 : 0;

    $media_file = null;
    if(isset($_FILES['media_file']) && $_FILES['media_file']['error']===0) {
        $file = $_FILES['media_file'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = $media_type=='image'?['jpg','jpeg','png','webp']:['mp4','webm','ogg'];
        if(!in_array(strtolower($ext), $allowed)) die("Invalid file type.");
        $media_file = time().'_'.rand(1000,9999).'.'.$ext;
        move_uploaded_file($file['tmp_name'], $uploadDir.$media_file);
    }

    $stmt = $pdo->prepare("INSERT INTO posts(title,slug,excerpt,content,author,published_at,cover_image,media_type,media_url,featured,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,NOW())");
    $stmt->execute([$title,$slug,$excerpt,$content,$author,$published_at,$media_file,$media_type,$media_url,$featured]);

    header("Location: ../posts.php");
    exit();
}

// UPDATE
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']) ?: strtolower(preg_replace('/[^a-z0-9]+/','-', $title));
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $author = trim($_POST['author']);
    $published_at = $_POST['published_at'] ?: null;
    $media_type = $_POST['media_type'];
    $media_url = $_POST['media_url'] ?: null;
    $featured = isset($_POST['featured']) ? 1 : 0;

    $media_file = null;
    if(isset($_FILES['media_file']) && $_FILES['media_file']['error']===0) {
        $file = $_FILES['media_file'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = $media_type=='image'?['jpg','jpeg','png','webp']:['mp4','webm','ogg'];
        if(!in_array(strtolower($ext), $allowed)) die("Invalid file type.");
        $media_file = time().'_'.rand(1000,9999).'.'.$ext;
        move_uploaded_file($file['tmp_name'], $uploadDir.$media_file);
    }

    if($media_file) {
        $stmt = $pdo->prepare("UPDATE posts SET title=?,slug=?,excerpt=?,content=?,author=?,published_at=?,cover_image=?,media_type=?,media_url=?,featured=? WHERE id=?");
        $stmt->execute([$title,$slug,$excerpt,$content,$author,$published_at,$media_file,$media_type,$media_url,$featured,$id]);
    } else {
        $stmt = $pdo->prepare("UPDATE posts SET title=?,slug=?,excerpt=?,content=?,author=?,published_at=?,media_type=?,media_url=?,featured=? WHERE id=?");
        $stmt->execute([$title,$slug,$excerpt,$content,$author,$published_at,$media_type,$media_url,$featured,$id]);
    }

    header("Location: ../posts.php");
    exit();
}

// DELETE
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT cover_image FROM posts WHERE id=?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();
    if($file && file_exists($uploadDir.$file)) unlink($uploadDir.$file);
    $pdo->prepare("DELETE FROM posts WHERE id=?")->execute([$id]);
    header("Location: ../posts.php");
    exit();
}
