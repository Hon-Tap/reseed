<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin Contacts (Inbox)
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__) . '/includes/config.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

/* ===================== DELETE (POST ONLY) ===================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'], $_POST['id'])) {
    $id = (int) $_POST['id'];

    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
    $stmt->execute([$id]);

    header('Location: contacts.php');
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

<div class="p-6 bg-gray-50 min-h-screen">
  <div class="max-w-7xl mx-auto">

    <!-- ================= HEADER ================= -->

    <div class="mb-8">
      <h2 class="text-3xl font-bold text-gray-800">Inbox</h2>
      <p class="text-gray-500 text-sm">
        <?= count($contacts) ?> message<?= count($contacts) === 1 ? '' : 's' ?> received
      </p>
    </div>

    <!-- ================= CONTENT ================= -->

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

      <?php if (!$contacts): ?>

        <div class="p-12 text-center text-gray-400">
          <svg class="mx-auto mb-4 w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <p>No messages yet</p>
        </div>

      <?php else: ?>

        <table class="w-full border-collapse text-sm">
          <thead>
            <tr class="bg-gray-100 border-b border-gray-200 text-gray-600 uppercase font-semibold text-xs">
              <th class="px-6 py-4">Date</th>
              <th class="px-6 py-4">Sender</th>
              <th class="px-6 py-4">Contact</th>
              <th class="px-6 py-4">Message</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
            <?php foreach ($contacts as $c): ?>
              <tr class="hover:bg-gray-50 transition">

                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                  <?= date('M d, Y', strtotime($c['created_at'])) ?>
                </td>

                <td class="px-6 py-4 font-semibold text-gray-800">
                  <?= htmlspecialchars($c['name']) ?>
                </td>

                <td class="px-6 py-4">
                  <div><?= htmlspecialchars($c['email']) ?></div>
                  <?php if ($c['phone']): ?>
                    <div class="text-xs text-gray-400"><?= htmlspecialchars($c['phone']) ?></div>
                  <?php endif; ?>
                </td>

                <td class="px-6 py-4 text-gray-700 max-w-md">
                  <?= nl2br(htmlspecialchars($c['message'])) ?>
                </td>

                <td class="px-6 py-4 text-right">
                  <form method="POST"
                        onsubmit="return confirm('Delete this message permanently?')"
                        class="inline">
                    <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                    <button name="delete"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-md transition"
                            title="Delete">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                             a2 2 0 01-1.995-1.858L5 7
                             m5 4v6m4-6v6
                             m1-10V4a1 1 0 00-1-1h-4
                             a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </form>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      <?php endif; ?>

    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
