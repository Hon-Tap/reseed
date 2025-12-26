<?php
declare(strict_types=1);

$backendPath = dirname(__DIR__);

require_once $backendPath . '/includes/config.php';
require_once $backendPath . '/admin/includes/admin_auth.php';
require_once $backendPath . '/admin/includes/admin_header.php';


/* ===================== DELETE (POST ONLY) ===================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'], $_POST['id'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        die('Security check failed');
    }

    $id = (int) $_POST['id'];
    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: contacts.php?success=deleted');
    exit;
}

/* ===================== FETCH ===================== */

$stmt = $pdo->query("
    SELECT id, name, email, phone, message, created_at
    FROM contacts
    ORDER BY created_at DESC
");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<script src="https://cdn.tailwindcss.com"></script>

<style>
    .inbox-card {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
    }
    .inbox-card:hover {
        border-left-color: #10b981;
        background-color: #f8fafc;
    }
    .btn-action {
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
    }
    .btn-reply { color: #64748b; }
    .btn-reply:hover { color: #10b981; background-color: #f0fdf4; }
    .btn-delete { color: #94a3b8; }
    .btn-delete:hover { color: #ef4444; background-color: #fef2f2; }
</style>

<div class="p-8 bg-[#f1f5f9] min-h-screen">
    <div class="max-w-6xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-[#0f172a] tracking-tight">Inbox</h1>
                <p class="text-slate-500 mt-2 font-medium">
                    Manage inquiries from the <span class="text-emerald-600 font-bold"><?= count($contacts) ?></span> messages received.
                </p>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> Message removed
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
            
            <?php if (!$contacts): ?>
                <div class="p-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-envelope-open text-slate-300 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Your inbox is empty</h3>
                </div>
            <?php else: ?>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Sender</th>
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest">Message</th>
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Date</th>
                                <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($contacts as $c): ?>
                                <?php 
                                    // Pre-format the email reply link
                                    $subject = rawurlencode("Re: Inquiry regarding ReSEED - " . $c['name']);
                                    $mailto = "mailto:" . htmlspecialchars($c['email']) . "?subject=" . $subject;
                                ?>
                                <tr class="inbox-card group">
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-slate-800 text-base"><?= htmlspecialchars($c['name']) ?></div>
                                        <div class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($c['email']) ?></div>
                                    </td>
                                    
                                    <td class="px-8 py-6">
                                        <div class="text-slate-600 leading-relaxed max-w-sm lg:max-w-md line-clamp-2 hover:line-clamp-none transition-all">
                                            <?= nl2br(htmlspecialchars($c['message'])) ?>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6 text-right whitespace-nowrap">
                                        <span class="text-slate-500 text-xs font-bold">
                                            <?= date('M d, Y', strtotime($c['created_at'])) ?>
                                        </span>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="<?= $mailto ?>" class="btn-action btn-reply" title="Reply to <?= htmlspecialchars($c['name']) ?>">
                                                <i class="fa-solid fa-reply"></i>
                                            </a>

                                            <form method="POST" onsubmit="return confirm('Archive this message permanently?')" class="inline">
                                                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <button name="delete" class="btn-action btn-delete" title="Delete">
                                                    <i class="fa-solid fa-trash-can"></i>
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

<?php require_once $adminRoot . '/includes/admin_footer.php'; ?>