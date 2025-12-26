<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';



$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id=?");
$stmt->execute([$id]);
$project = $stmt->fetch();
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gray-50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <nav class="flex text-gray-500 text-sm mb-2">
                    <a href="projects.php" class="hover:text-green-600 transition">Projects</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-800 font-medium">Edit Project</span>
                </nav>
                <h2 class="text-3xl font-extrabold text-gray-900">Update Project Details</h2>
            </div>
        </div>

        <form action="<?= admin_url('handlers/project-handler.php') ?>"
      method="POST"
      enctype="multipart/form-data"
      class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <input type="hidden" name="id" value="<?= $project['id'] ?>">

                        <label class="block text-sm font-bold text-gray-700 mb-1">Project Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" 
                               class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" value="<?= htmlspecialchars($project['slug']) ?>" 
                               class="w-full px-4 py-2 rounded-lg border bg-gray-50 text-gray-500 outline-none transition font-mono text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Project Summary (Short)</label>
                        <textarea name="summary" rows="3" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition"><?= htmlspecialchars($project['summary']) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Full Description</label>
                        <textarea name="description" rows="10" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none transition"><?= htmlspecialchars($project['description']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-xs font-bold uppercase text-gray-400 mb-4 tracking-widest">Settings</h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Project Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-lg border bg-white focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="Ongoing" <?= $project['status']=='Ongoing'?'selected':'' ?>>Ongoing</option>
                            <option value="Completed" <?= $project['status']=='Completed'?'selected':'' ?>>Completed</option>
                            <option value="Planned" <?= $project['status']=='Planned'?'selected':'' ?>>Planned</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" value="<?= htmlspecialchars($project['location']) ?>" class="w-full px-3 py-2 rounded-lg border outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">Start Date</label>
                            <input type="date" name="start_date" value="<?= $project['start_date'] ?>" class="w-full text-xs px-2 py-2 rounded border">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase">End Date</label>
                            <input type="date" name="end_date" value="<?= $project['end_date'] ?>" class="w-full text-xs px-2 py-2 rounded border">
                        </div>
                    </div>

                    <label class="flex items-center space-x-3 cursor-pointer p-2 rounded hover:bg-gray-50 border border-dashed border-gray-200 transition">
                        <input type="checkbox" name="featured" value="1" class="w-4 h-4 text-green-600 rounded" <?= $project['featured']?'checked':'' ?>>
                        <span class="text-sm font-medium text-gray-700">Featured Project</span>
                    </label>

                    <button type="submit" name="update" class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-sm transition">
                        Update Project
                    </button>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="text-xs font-bold uppercase text-gray-400 mb-4 tracking-widest">Media Asset</h3>
                    
                    <div class="mb-4">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2">Media Type</label>
                        <select name="media_type" class="w-full px-3 py-2 rounded border bg-gray-50 text-sm outline-none">
                            <option value="image" <?= $project['media_type']=='image'?'selected':'' ?>>Image</option>
                            <option value="video" <?= $project['media_type']=='video'?'selected':'' ?>>Video</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <?php if($project['cover_image']): ?>
                            <div class="rounded-lg overflow-hidden border bg-black">
                            <?php if($project['media_type']=='image'): ?>
                                <img src="../uploads/projects/<?= $project['cover_image'] ?>" class="w-full h-32 object-cover opacity-80">
                            <?php else: ?>
                                <video class="w-full h-32 object-cover"><source src="../uploads/projects/<?= $project['cover_image'] ?>"></video>
                            <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <input type="file" name="media_file" class="text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition">
                    
                    <div class="mt-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">External Media URL</label>
                        <input type="url" name="media_url" value="<?= htmlspecialchars($project['media_url']) ?>" class="w-full px-3 py-2 rounded border text-xs" placeholder="https://youtube.com/...">
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<?php include "includes/admin_footer.php"; ?>