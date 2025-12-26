<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';



/* ===================== FETCH POST ===================== */
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "<p class='p-6'>Post not found.</p>";
    include "includes/admin_footer.php";
    exit;
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gray-50 min-h-screen p-6 md:p-10">
  <div class="max-w-6xl mx-auto">

    <!-- ================= HEADER ================= -->
    <div class="mb-8 flex items-center justify-between">
      <div>
        <nav class="flex text-gray-500 text-sm mb-2">
          <a href="posts.php" class="hover:text-green-600 transition">Posts</a>
          <span class="mx-2">/</span>
          <span class="text-gray-800 font-medium">Edit Post</span>
        </nav>
        <h2 class="text-3xl font-extrabold text-gray-900">Update Post Content</h2>
      </div>
    </div>

    <!-- ================= FORM ================= -->
    <form
  action="<?= admin_url('handlers/post-handler.php') ?>"
  method="POST"
  enctype="multipart/form-data"
  class="grid grid-cols-1 lg:grid-cols-3 gap-8"
>


      <input type="hidden" name="id" value="<?= $post['id'] ?>">

      <!-- ================= MAIN CONTENT ================= -->
      <div class="lg:col-span-2 space-y-6">

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Post Title</label>
            <input
              type="text"
              name="title"
              value="<?= htmlspecialchars($post['title']) ?>"
              class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition"
              required
            >
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Slug</label>
            <input
              type="text"
              name="slug"
              value="<?= htmlspecialchars($post['slug']) ?>"
              class="w-full px-4 py-2 rounded-lg border bg-gray-50 text-gray-500 outline-none font-mono text-sm"
            >
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Excerpt (Short Summary)</label>
            <textarea
              name="excerpt"
              rows="3"
              class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition"
              required
            ><?= htmlspecialchars($post['excerpt']) ?></textarea>
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Full Content</label>
            <textarea
              name="content"
              rows="10"
              class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition"
              required
            ><?= htmlspecialchars($post['content']) ?></textarea>
          </div>

        </div>
      </div>

      <!-- ================= SIDEBAR ================= -->
      <div class="space-y-6">

        <!-- ===== Settings ===== -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
          <h3 class="text-xs font-bold uppercase text-gray-400 mb-4 tracking-widest">Settings</h3>

          <div class="mb-4">
            <label class="block text-xs font-bold text-gray-700 mb-1">Author</label>
            <input
              type="text"
              name="author"
              value="<?= htmlspecialchars($post['author']) ?>"
              class="w-full px-3 py-2 rounded-lg border outline-none"
              required
            >
          </div>

          <div class="mb-4">
            <label class="block text-xs font-bold text-gray-700 mb-1">Published At</label>
            <input
              type="datetime-local"
              name="published_at"
              value="<?= $post['published_at']
                ? date('Y-m-d\TH:i', strtotime($post['published_at']))
                : '' ?>"
              class="w-full px-3 py-2 rounded-lg border outline-none text-sm"
            >
          </div>

          <label class="flex items-center space-x-3 cursor-pointer p-2 rounded hover:bg-gray-50 border border-dashed border-gray-200 transition">
            <input
              type="checkbox"
              name="featured"
              value="1"
              class="w-4 h-4 text-green-600 rounded"
              <?= $post['featured'] ? 'checked' : '' ?>
            >
            <span class="text-sm font-medium text-gray-700">Featured Post</span>
          </label>

          <button
            type="submit"
            name="update"
            class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-sm transition"
          >
            Update Post
          </button>

          <a href="posts.php" class="block text-center mt-4 text-xs text-gray-400 hover:text-red-500 transition">
            Cancel and go back
          </a>
        </div>

        <!-- ===== Media Asset ===== -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
          <h3 class="text-xs font-bold uppercase text-gray-400 mb-4 tracking-widest">Media Asset</h3>

          <div class="mb-4">
            <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Media Type</label>
            <select
              name="media_type"
              class="w-full px-3 py-2 rounded border bg-gray-50 text-sm outline-none"
            >
              <option value="image" <?= $post['media_type'] === 'image' ? 'selected' : '' ?>>Image</option>
              <option value="video" <?= $post['media_type'] === 'video' ? 'selected' : '' ?>>Video</option>
            </select>
          </div>

          <?php if (!empty($post['cover_image'])): ?>
            <div class="mb-4 rounded-lg overflow-hidden border bg-black">
              <?php if ($post['media_type'] === 'image'): ?>
                <img
                  src="../uploads/posts/<?= htmlspecialchars($post['cover_image']) ?>"
                  class="w-full h-32 object-cover opacity-80"
                >
              <?php else: ?>
                <video class="w-full h-32 object-cover" controls>
                  <source src="../uploads/posts/<?= htmlspecialchars($post['cover_image']) ?>">
                </video>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <input
            type="file"
            name="media_file"
            class="text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full
                   file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition"
          >

          <div class="mt-4">
            <label class="block text-xs font-bold text-gray-700 mb-1">External Media URL</label>
            <input
              type="url"
              name="media_url"
              value="<?= htmlspecialchars($post['media_url'] ?? '') ?>"
              class="w-full px-3 py-2 rounded border text-xs"
              placeholder="https://youtube.com/..."
            >
          </div>

        </div>

      </div>
    </form>
  </div>
</div>

<?php include "includes/admin_footer.php"; ?>
