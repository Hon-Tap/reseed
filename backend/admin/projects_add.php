<?php
include "includes/admin_auth.php";
include "../includes/config.php";
include "includes/admin_header.php";
?>

<h2>Add Project</h2>

<form action="handlers/project-handler.php" method="POST" enctype="multipart/form-data" class="form-card">

    <label>Title</label>
    <input type="text" name="title" required>

    <label>Slug (optional)</label>
    <input type="text" name="slug">

    <label>Summary</label>
    <textarea name="summary" rows="3"></textarea>

    <label>Description</label>
    <textarea name="description" rows="8"></textarea>

    <label>Location</label>
    <input type="text" name="location">

    <label>Start Date</label>
    <input type="date" name="start_date">

    <label>End Date</label>
    <input type="date" name="end_date">

    <label>Status</label>
    <select name="status">
        <option value="Ongoing">Ongoing</option>
        <option value="Completed">Completed</option>
        <option value="Planned">Planned</option>
    </select>

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

    <button type="submit" name="add" class="btn">Save Project</button>

</form>

<style>
.form-card {max-width:600px; padding:20px; background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.form-card input, .form-card textarea, .form-card select {width:100%; padding:10px; margin-bottom:15px; border-radius:6px; border:1px solid #bbb;}
</style>

<?php include "includes/admin_footer.php"; ?>
