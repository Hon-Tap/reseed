<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';


?>

<h2>Add News / Post</h2>

<form action="<?= admin_url('handlers/post-handler.php') ?>"
      method="POST"
      enctype="multipart/form-data"
      class="form-card">

    <label>Title</label>
    <input type="text" name="title" required>

    <label>Slug (optional)</label>
    <input type="text" name="slug">

    <label>Excerpt</label>
    <textarea name="excerpt" rows="3" required></textarea>

    <label>Content</label>
    <textarea name="content" rows="8" required></textarea>

    <label>Author</label>
    <input type="text" name="author" required>

    <label>Published At</label>
    <input type="datetime-local" name="published_at">

    <label>Media Type</label>
    <select name="media_type">
        <option value="image">Image</option>
        <option value="video">Video</option>
    </select>

    <label>Upload File</label>
    <input type="file" name="media_file">

    <label>Media URL (optional for video)</label>
    <input type="url" name="media_url">

    <label>
        <input type="checkbox" name="featured" value="1"> Featured
    </label>

    <button type="submit" name="add" class="btn">Save Post</button>

</form>

<style>
.form-card {max-width:600px; padding:20px; background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.form-card input, .form-card textarea, .form-card select {width:100%; padding:10px; margin-bottom:15px; border-radius:6px; border:1px solid #bbb;}
</style>

<?php include "includes/admin_footer.php"; ?>
