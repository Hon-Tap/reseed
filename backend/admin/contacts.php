<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';
require_once $backendPath . '/admin/includes/csrf.php';

/*
|--------------------------------------------------------------------------
| Action Handler (Delete)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        die('Security check failed');
    }

    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: contacts.php?status=deleted");
    exit;
}

/*
|--------------------------------------------------------------------------
| Data Loading
|--------------------------------------------------------------------------
*/
try {
    // We select everything to ensure no column is missed
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $contacts = [];
    $error = "Database Error: " . $e->getMessage();
}
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-[#f8fafc] min-h-screen p-6 md:p-10">
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-4">
                    Inbox
                    <span class="bg-emerald-100 text-emerald-600 text-xs uppercase tracking-widest px-3 py-1 rounded-full">
                        <?= count($contacts) ?> Messages
                    </span>
                </h1>
                <p class="text-slate-500 mt-2 font-medium">Direct inquiries from your website contact form.</p>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
                <div class="flex items-center gap-3 bg-white border-l-4 border-rose-500 shadow-sm px-6 py-4 rounded-xl animate-bounce">
                    <i class="fa-solid fa-trash-can text-rose-500"></i>
                    <span class="text-sm font-bold text-slate-700">Message permanently removed.</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <?php if (empty($contacts)): ?>
                <div class="py-32 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-slate-50 rounded-full mb-6">
                        <i class="fa-solid fa-envelope-open text-slate-200 text-4xl"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800">No messages yet</h2>
                    <p class="text-slate-400 mt-2">When people contact you, they will appear here.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase font-black tracking-[0.2em] border-b border-slate-50">
                                <th class="px-8 py-6">Sender Details</th>
                                <th class="px-8 py-6">Message Preview</th>
                                <th class="px-8 py-6 text-right">Received</th>
                                <th class="px-8 py-6 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($contacts as $c): 
                                $date = new DateTime($c['created_at']);
                                $mailto = "mailto:" . htmlspecialchars($c['email']) . "?subject=" . rawurlencode("Re: Inquiry from " . $c['name']);
                            ?>
                            <tr class="group hover:bg-slate-50/80 transition-all cursor-default">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-sm uppercase">
                                            <?= mb_substr($c['name'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 leading-none mb-1"><?= htmlspecialchars($c['name']) ?></div>
                                            <div class="text-xs text-slate-400 font-bold"><?= htmlspecialchars($c['email']) ?></div>
                                            <?php if(!empty($c['phone'])): ?>
                                                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($c['phone']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-8 py-6">
                                    <div class="max-w-xs xl:max-w-md">
                                        <p class="text-slate-600 text-sm line-clamp-2 leading-relaxed">
                                            <?= htmlspecialchars($c['message']) ?>
                                        </p>
                                    </div>
                                </td>

                                <td class="px-8 py-6 text-right">
                                    <div class="text-xs font-black text-slate-400 uppercase tracking-tighter">
                                        <?= $date->format('M d') ?>
                                    </div>
                                    <div class="text-[10px] text-slate-300 font-bold">
                                        <?= $date->format('h:i A') ?>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= $mailto ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Reply Email">
                                            <i class="fa-solid fa-reply text-xs"></i>
                                        </a>

                                        <button onclick="viewMessage(<?= htmlspecialchars(json_encode($c)) ?>)" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-sky-600 hover:text-white transition-all shadow-sm" title="Quick View">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>

                                        <form method="POST" onsubmit="return confirm('Delete this inquiry?')" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <button name="delete" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-400 hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="msgModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-10">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h3 id="modalName" class="text-3xl font-black text-slate-900"></h3>
                    <p id="modalMeta" class="text-emerald-600 font-bold text-sm mt-1"></p>
                </div>
                <button onclick="closeModal()" class="text-slate-300 hover:text-slate-900 transition-colors">
                    <i class="fa-solid fa-circle-xmark text-3xl"></i>
                </button>
            </div>
            
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 min-h-[200px]">
                <p id="modalBody" class="text-slate-700 leading-relaxed whitespace-pre-wrap font-medium"></p>
            </div>

            <div class="mt-8 flex justify-end">
                <button onclick="closeModal()" class="px-8 py-3 bg-slate-900 text-white font-black rounded-2xl hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                    Done Reading
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('msgModal');

function viewMessage(data) {
    document.getElementById('modalName').textContent = data.name;
    document.getElementById('modalMeta').textContent = `${data.email} • ${data.phone || 'No phone'}`;
    document.getElementById('modalBody').textContent = data.message;
    modal.classList.remove('hidden');
}

function closeModal() {
    modal.classList.add('hidden');
}

// Close modal on background click
window.onclick = function(event) {
    if (event.target == modal) closeModal();
}
</script>

<?php require_once $backendPath . '/admin/includes/admin_footer.php'; ?>