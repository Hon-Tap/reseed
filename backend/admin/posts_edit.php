<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';

// Fetch Post
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "<div class='p-10 text-center'><h2 class='text-2xl font-bold text-red-600'>Post not found.</h2><a href='posts.php' class='text-blue-500 underline'>Return to list</a></div>";
    include $backendPath . '/admin/includes/admin_footer.php';
    exit;
}

// CSRF check
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gray-50 min-h-screen p-6 md:p-10">
  <div class="max-w-6xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
      <div>
        <nav class="flex text-gray-500 text-sm mb-2">
          <a href="posts.php" class="hover:text-green-600 transition">Posts</a>
          <span class="mx-2">/</span>
          <span class="text-gray-800 font-medium">Edit Post</span>
        </nav>
        <h2 class="text-3xl font-extrabold text-gray-900">Update Content</h2>
      </div>
    </div>

    <form action="<?= admin_url('post-handler.php') ?>" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <input type="hidden" name="id" value="<?= $post['id'] ?>">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Post Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition" required>
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Slug</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" class="w-full px-4 py-2 rounded-lg border bg-gray-50 text-gray-500 font-mono text-sm outline-none">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Excerpt</label>
            <textarea name="excerpt" rows="3" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition" required><?= htmlspecialchars($post['excerpt']) ?></textarea>
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Full Content</label>
            <textarea name="content" rows="10" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition" required><?= htmlspecialchars($post['content']) ?></textarea>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
          <h3 class="text-xs font-bold uppercase text-gray-400 mb-4 tracking-widest">Settings</h3>
          
          <div class="mb-4">
            <label class="block text-xs font-bold text-gray-700 mb-1">Author</label>
            <input type="text" name="author" value="<?= htmlspecialchars($post['author']) ?>" class="w-full px-3 py-2 rounded-lg border outline-none" required>
          </div>

          <div class="mb-4">
            <label class="block text-xs font-bold text-gray-700 mb-1">Published At</label>
            <?php 
              $dateValue = !empty($post['published_at']) ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '';
            ?>
            <input type="datetime-local" name="published_at" value="<?= $dateValue ?>" class="w-full px-3 py-2 rounded-lg border outline-none text-sm">
          </div>

          <label class="flex items-center space-x-3 cursor-pointer p-2 mb-6 rounded hover:bg-gray-50 border border-dashed border-gray-200 transition">
            <input type="checkbox" name="featured" value="1" class="w-4 h-4 text-green-600 rounded" <?= $post['featured'] ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-gray-700">Featured Post</span>
          </label>

          <button type="submit" name="update" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow transition">
            Update Post
          </button>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
          <h3 class="text-xs font-bold uppercase text-gray-400 mb-4 tracking-widest">Media Asset</h3>
          <select name="media_type" class="w-full mb-4 px-3 py-2 rounded border bg-gray-50 text-sm">
            <option value="image" <?= $post['media_type'] === 'image' ? 'selected' : '' ?>>Image</option>
            <option value="video" <?= $post['media_type'] === 'video' ? 'selected' : '' ?>>Video</option>
          </select>

          <?php if (!empty($post['cover_image'])): ?>
            <div class="mb-4 rounded-lg overflow-hidden border bg-gray-100">
                <img src="../uploads/posts/<?= htmlspecialchars($post['cover_image']) ?>" class="w-full h-32 object-cover">
            </div>
          <?php endif; ?>

          <input type="file" name="media_file" class="text-xs block w-full mb-4">
          <input type="url" name="media_url" value="<?= htmlspecialchars($post['media_url'] ?? '') ?>" placeholder="External URL" class="w-full px-3 py-2 rounded border text-xs">
        </div>
      </div>
    </form>
  </div>
</div>

<?php include $backendPath . '/admin/includes/admin_footer.php'; ?>