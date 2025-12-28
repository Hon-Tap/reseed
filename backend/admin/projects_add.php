<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Default Start Date to Today
$today = date('Y-m-d');
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-slate-50 min-h-screen p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900">Add New Project</h2>
                <p class="text-slate-500 mt-1">Register a new community initiative or conservation project.</p>
            </div>
            <a href="projects.php" class="text-slate-400 hover:text-emerald-600 transition">
                <i class="fa-solid fa-circle-xmark text-2xl"></i>
            </a>
        </div>

        <form action="project-handler.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Project Title</label>
                        <input type="text" name="title" required placeholder="e.g., Solar Water Initiative"
                               class="w-full px-4 py-3 rounded-xl border focus:ring-2 focus:ring-emerald-500 outline-none transition text-lg">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Slug (Optional)</label>
                            <input type="text" name="slug" placeholder="project-url-format"
                                   class="w-full px-3 py-2 rounded-lg border bg-slate-50 font-mono text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Location</label>
                            <input type="text" name="location" required placeholder="District or City"
                                   class="w-full px-3 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Short Summary</label>
                        <textarea name="summary" rows="2" required placeholder="A brief one-sentence hook..."
                                  class="w-full px-4 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Full Description</label>
                        <textarea name="description" rows="10" required placeholder="Detailed project breakdown..."
                                  class="w-full px-4 py-2 rounded-lg border outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-widest">Project Meta</h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-lg border bg-slate-50 focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                            <option value="Planned">Planned</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Start Date</label>
                            <input type="date" name="start_date" value="<?= $today ?>" class="w-full text-xs px-2 py-2 rounded border outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">End Date</label>
                            <input type="date" name="end_date" class="w-full text-xs px-2 py-2 rounded border outline-none">
                        </div>
                    </div>

                    <label class="flex items-center space-x-3 cursor-pointer p-3 rounded-xl border border-dashed border-slate-200 hover:bg-emerald-50 transition mb-6">
                        <input type="checkbox" name="featured" value="1" class="w-5 h-5 text-emerald-600 rounded">
                        <span class="text-sm font-bold text-slate-700">Featured Project</span>
                    </label>

                    <button type="submit" name="add" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-emerald-100 transition">
                        Save Project
                    </button>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-widest">Media</h3>
                    <select name="media_type" class="w-full mb-4 px-3 py-2 rounded-lg border bg-slate-50 text-sm">
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>
                    <input type="file" name="media_file" class="text-xs block w-full mb-4 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <input type="url" name="media_url" placeholder="YouTube URL" class="w-full px-3 py-2 rounded-lg border text-xs outline-none">
                </div>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>