<?php
declare(strict_types=1);

require_once "includes/admin_auth.php";
require_once "../includes/config.php";
require_once "includes/admin_header.php";

/* Delete */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([(int)$_POST['delete_id']]);
    header("Location: contacts.php");
    exit;
}

/* Fetch */
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

    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-3xl font-bold text-gray-800">Inbox</h2>
      <p class="text-gray-500 text-sm">
        <?= count($contacts) ?> message(s) received
      </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

      <?php if (!$contacts): ?>
        <div class="p-12 text-center text-gray-400">
          <i class="fa-regular fa-envelope text-3xl mb-3"></i>
          <p>No messages yet</p>
        </div>
      <?php else: ?>

        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-100 border-b border-gray-200 text-gray-600 text-sm uppercase font-semibold">
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

                <td class="px-6 py-4 text-sm text-gray-500">
                  <?= date('M d, Y', strtotime($c['created_at'])) ?>
                </td>

                <td class="px-6 py-4 font-semibold text-gray-800">
                  <?= htmlspecialchars($c['name']) ?>
                </td>

                <td class="px-6 py-4 text-sm">
                  <div><?= htmlspecialchars($c['email']) ?></div>
                  <div class="text-gray-400"><?= htmlspecialchars($c['phone']) ?></div>
                </td>

                <td class="px-6 py-4 text-sm text-gray-700 max-w-md">
                  <?= nl2br(htmlspecialchars($c['message'])) ?>
                </td>

                <td class="px-6 py-4 text-right">
                  <form method="POST" onsubmit="return confirm('Delete this message permanently?')">
                    <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
                    <button
                      class="p-2 text-red-600 hover:bg-red-50 rounded-md transition"
                      title="Delete"
                    >
                      <i class="fa-solid fa-trash"></i>
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

<?php include "includes/admin_footer.php"; ?>
