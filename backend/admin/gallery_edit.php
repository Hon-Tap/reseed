<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';



$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM gallery WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();
?>

<h2>Edit Gallery Item</h2>

<div class="form-card">
    <form action="<?= admin_url('handlers/gallery-handler.php') ?>"
      method="POST"
      enctype="multipart/form-data"
      id="editGalleryForm">
        <input type="hidden" name="id" value="<?= $item['id'] ?>">

        <label for="caption">Caption</label>
        <input type="text" id="caption" name="caption" value="<?= htmlspecialchars($item['caption']) ?>" required>

        <label for="category">Category</label>
        <input type="text" id="category" name="category" value="<?= htmlspecialchars($item['category']) ?>" required>

        <p>Current Image:</p>
        <div class="image-preview">
            <img id="currentPreview" src="../uploads/gallery/<?= htmlspecialchars($item['filename']) ?>" alt="Current Image">
        </div>

        <label for="image">Change Image (optional)</label>
        <input type="file" id="image" name="image" accept="image/*" onchange="previewNewImage(event)">

        <div class="image-preview" id="newPreviewContainer">
            <img id="newPreview" src="" alt="New Image Preview" style="display:none;">
        </div>

        <button class="btn" name="update">Update</button>
    </form>
</div>

<script>
function previewNewImage(event) {
    const preview = document.getElementById('newPreview');
    const container = document.getElementById('newPreviewContainer');
    const file = event.target.files[0];
    if(file){
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}
</script>

<style>
.form-card {
    max-width: 500px;
    margin: 2rem auto;
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
.form-card h2 {
    text-align: center;
    margin-bottom: 1.5rem;
}
.form-card label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.form-card input[type="text"],
.form-card input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 1rem;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1rem;
}
.image-preview {
    text-align: center;
    margin-bottom: 1rem;
}
.image-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 10px;
    object-fit: cover;
}
.btn {
    display: block;
    width: 100%;
    background-color: #0c4bb8;
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
}
.btn:hover {
    background-color: #0961a8;
}
</style>

<?php include "includes/admin_footer.php"; ?>
