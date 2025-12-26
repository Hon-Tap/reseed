<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/config.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

/* ===================== SEARCH ===================== */
$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("
    SELECT id, title, slug, status, location, start_date, end_date,
           cover_image, media_type, featured, created_at
    FROM projects
    WHERE title ILIKE ?
    ORDER BY created_at DESC
");
$stmt->execute(["%{$search}%"]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Public uploads path */
$uploadPath = UPLOAD_URL . '/projects/';
?>


<!-- Tailwind (page-local, like posts.php) -->
<script src="https://cdn.tailwindcss.com"></script>

<div class="p-6 bg-gray-50 min-h-screen">
  <div class="max-w-7xl mx-auto">

    <!-- ================= HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
      <div>
        <h2 class="text-3xl font-bold text-gray-800">Projects</h2>
        <p class="text-gray-500 text-sm">Manage and monitor your restoration projects.</p>
      </div>

      <div class="mt-4 md:mt-0">
        <a href="<?= admin_url('projects_add.php') ?>"
           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4v16m8-8H4"/>
          </svg>
          Add New Project
        </a>
      </div>
    </div>

    <!-- ================= SEARCH ================= -->
    <div class="mb-6">
      <form method="GET" class="relative max-w-sm">
        <input type="text"
               name="search"
               value="<?= htmlspecialchars($search) ?>"
               placeholder="Search projects..."
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition">
        <div class="absolute left-3 top-2.5 text-gray-400">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
      </form>
    </div>

    <!-- ================= TABLE ================= -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-100 border-b border-gray-200 text-gray-600 text-sm uppercase font-semibold">
            <th class="px-6 py-4">Media</th>
            <th class="px-6 py-4">Project Details</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4">Timeline</th>
            <th class="px-6 py-4 text-right">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">
        <?php if ($projects): ?>
          <?php foreach ($projects as $p): ?>
            <tr class="hover:bg-gray-50 transition">

              <!-- Media -->
              <td class="px-6 py-4">
                <?php if ($p['media_type'] === 'image' && $p['cover_image']): ?>
                  <img src="<?= $uploadPath . $p['cover_image'] ?>"
                       class="w-16 h-16 object-cover rounded-lg shadow-sm border">
                <?php else: ?>
                  <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                    </svg>
                  </div>
                <?php endif; ?>
              </td>

              <!-- Details -->
              <td class="px-6 py-4">
                <div class="font-bold text-gray-800 text-lg">
                  <?= htmlspecialchars($p['title']) ?>
                  <?php if ($p['featured']): ?>
                    <span class="text-yellow-500 ml-1">★</span>
                  <?php endif; ?>
                </div>

                <div class="text-gray-500 text-xs mt-1 font-mono">
                  <?= htmlspecialchars($p['slug']) ?>
                </div>

                <div class="text-gray-500 text-xs flex items-center mt-1">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  </svg>
                  <?= htmlspecialchars($p['location']) ?>
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-4">
                <?php
                  $status = strtolower($p['status'] ?? 'planned');
                  $color  = $status === 'completed' ? 'green'
                           : ($status === 'ongoing' ? 'blue' : 'gray');
                ?>
                <span class="px-3 py-1 text-xs font-bold uppercase rounded-full
                             bg-<?= $color ?>-100 text-<?= $color ?>-700">
                  <?= htmlspecialchars($status) ?>
                </span>
              </td>

              <!-- Timeline -->
              <td class="px-6 py-4 text-xs text-gray-600">
                <div><span class="text-gray-400">Start:</span> <?= $p['start_date'] ?></div>
                <div><span class="text-gray-400">End:</span> <?= $p['end_date'] ?: 'Ongoing' ?></div>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end space-x-2">
                  <a href="<?= admin_url('projects_edit.php?id=' . $p['id']) ?>"
                     class="p-2 text-blue-600 hover:bg-blue-50 rounded-md transition"
                     title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </a>

                  <form action="<?= admin_url('handlers/project-handler.php') ?>"
                    method="POST"
                    onsubmit="return confirm('Delete this project permanently?')">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button name="delete"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-md transition"
                            title="Delete">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6"/>
                      </svg>
                    </button>
                  </form>
                </div>
              </td>

            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
              No projects found matching your criteria.
            </td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

<?php include "includes/admin_footer.php"; ?>
