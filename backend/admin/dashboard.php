<?php
declare(strict_types=1);

/**
 * Admin Dashboard
 * --------------------------------------------------
 * Central overview for ReSEED Admin Panel
 */

$adminDir = __DIR__;

require_once $adminDir . '/includes/admin_auth.php';
require_once $adminDir . '/includes/admin_header.php';

/* ==================================================
   DASHBOARD METRICS
================================================== */

try {
    $stats = [
        'projects' => (int) $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
        'posts'    => (int) $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
        'contacts' => (int) $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
        'admins'   => (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    ];
} catch (Throwable $e) {
    $stats = [
        'projects' => 0,
        'posts'    => 0,
        'contacts' => 0,
        'admins'   => 0,
    ];
}
?>

<style>
/* ==================================================
   DASHBOARD LAYOUT
================================================== */

.dashboard-header {
    margin-bottom: 40px;
}

.dashboard-header h1 {
    font-size: 2.3rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.03em;
    margin-bottom: 6px;
}

.dashboard-header p {
    color: #64748b;
    font-size: 1.05rem;
}

/* ==================================================
   STATS GRID
================================================== */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
}

.stat-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 28px;
    display: flex;
    align-items: center;
    gap: 20px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    transition: transform .2s ease, box-shadow .2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 30px rgba(15, 23, 42, 0.08);
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    font-size: 24px;
    color: #ffffff;
}

/* Gradient Themes */
.bg-green  { background: linear-gradient(135deg, #22c55e, #166534); }
.bg-blue   { background: linear-gradient(135deg, #3b82f6, #1e40af); }
.bg-orange { background: linear-gradient(135deg, #f97316, #9a3412); }
.bg-purple { background: linear-gradient(135deg, #a855f7, #6b21a8); }

.stat-info h3 {
    margin: 0;
    font-size: 1.9rem;
    font-weight: 800;
    color: #1e293b;
}

.stat-info span {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
}

/* ==================================================
   QUICK ACTIONS
================================================== */

.section-title {
    margin: 56px 0 24px;
    font-size: 1.25rem;
    font-weight: 800;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title::after {
    content: "";
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
}

.action-card {
    background: #ffffff;
    padding: 32px;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
    display: flex;
    flex-direction: column;
    transition: all .25s ease;
}

.action-card:hover {
    border-color: #22c55e;
    box-shadow: 0 25px 40px rgba(15, 23, 42, 0.12);
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f0fdf4;
    color: #166534;
    display: grid;
    place-items: center;
    font-size: 20px;
    margin-bottom: 20px;
}

.action-card h4 {
    margin: 0 0 10px;
    font-size: 1.25rem;
    font-weight: 800;
}

.action-card p {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
    flex: 1;
    margin-bottom: 24px;
}

.action-card a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 800;
    text-decoration: none;
    background: #f8fafc;
    color: #166534;
    transition: all .2s ease;
}

.action-card a:hover {
    background: #166534;
    color: #ffffff;
}
</style>

<!-- ==================================================
     DASHBOARD VIEW
================================================== -->

<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p>System overview and administration for <strong>ReSEED</strong>.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-green"><i class="fa-solid fa-leaf"></i></div>
        <div class="stat-info">
            <h3><?= $stats['projects'] ?></h3>
            <span>Projects</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-blue"><i class="fa-solid fa-pen-nib"></i></div>
        <div class="stat-info">
            <h3><?= $stats['posts'] ?></h3>
            <span>Blog Posts</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-orange"><i class="fa-solid fa-envelope-open-text"></i></div>
        <div class="stat-info">
            <h3><?= $stats['contacts'] ?></h3>
            <span>Inquiries</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-purple"><i class="fa-solid fa-user-gear"></i></div>
        <div class="stat-info">
            <h3><?= $stats['admins'] ?></h3>
            <span>Admins</span>
        </div>
    </div>
</div>

<div class="section-title">Quick Actions</div>

<div class="quick-actions">
    <div class="action-card">
        <div class="action-icon"><i class="fa-solid fa-plus"></i></div>
        <h4>Project Hub</h4>
        <p>Manage restoration projects, field updates, and environmental impact records.</p>
        <a href="<?= ADMIN_BASE_URL ?>/projects.php">Go to Projects</a>
    </div>

    <div class="action-card">
        <div class="action-icon"><i class="fa-solid fa-newspaper"></i></div>
        <h4>Content Creator</h4>
        <p>Create and publish blog posts, news, and community success stories.</p>
        <a href="<?= ADMIN_BASE_URL ?>/posts.php">Manage Blog</a>
    </div>

    <div class="action-card">
        <div class="action-icon"><i class="fa-solid fa-message"></i></div>
        <h4>Communication</h4>
        <p>You have <?= $stats['contacts'] ?> total messages awaiting review.</p>
        <a href="<?= ADMIN_BASE_URL ?>/contacts.php">Open Inbox</a>
    </div>
</div>

<?php require_once $adminDir . '/includes/admin_footer.php'; ?>
