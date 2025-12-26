<?php
declare(strict_types=1);

/**
 * Admin Dashboard
 * Path: C:\xampp1\htdocs\reseed\backend\admin\dashboard.php
 */

$adminRoot = __DIR__;

/* ==================================================
   BOOTSTRAP
================================================== */
require_once $adminRoot . '/includes/admin_auth.php';
require_once $adminRoot . '/includes/admin_header.php';

/* ==================================================
   DASHBOARD METRICS (Optimized for PostgreSQL)
================================================== */
try {
    // Single trip to the database for all counts
    $sql = "SELECT 
        (SELECT COUNT(*) FROM projects) as projects_count,
        (SELECT COUNT(*) FROM posts) as posts_count,
        (SELECT COUNT(*) FROM contacts) as contacts_count,
        (SELECT COUNT(*) FROM users) as admins_count";
    
    $statsData = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    $stats = [
        'projects' => (int)($statsData['projects_count'] ?? 0),
        'posts'    => (int)($statsData['posts_count'] ?? 0),
        'contacts' => (int)($statsData['contacts_count'] ?? 0),
        'admins'   => (int)($statsData['admins_count'] ?? 0),
    ];

    // Fetch 3 most recent inquiries for a "Recent Activity" feel
    $recentInquiries = $pdo->query("SELECT name, email, created_at FROM contacts ORDER BY created_at DESC LIMIT 3")->fetchAll();

} catch (Throwable $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $stats = ['projects' => 0, 'posts' => 0, 'contacts' => 0, 'admins' => 0];
    $recentInquiries = [];
}
?>

<style>
/* ==================================================
   ENHANCED UI STYLES
================================================== */
:root {
    --glass-bg: rgba(255, 255, 255, 0.7);
    --glass-border: rgba(255, 255, 255, 0.3);
}

.dashboard-header {
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.dashboard-header h1 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.04em;
    margin: 0;
}

.dashboard-header p { color: #64748b; font-size: 1.1rem; margin-top: 5px; }

/* Stats Grid with Glassmorphism subtle touch */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
}

.stat-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    font-size: 22px;
    color: #fff;
}

.bg-green  { background: linear-gradient(135deg, #10b981, #059669); }
.bg-blue   { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.bg-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
.bg-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.stat-info h3 { font-size: 2rem; font-weight: 800; color: #1e293b; margin: 0; }
.stat-info span { font-size: 0.85rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; }

/* Quick Actions & Recent Activity Layout */
.dashboard-content-split {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 32px;
    margin-top: 48px;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.action-card {
    background: #fff;
    padding: 24px;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    text-decoration: none;
    transition: all 0.2s ease;
}

.action-card:hover { border-color: #10b981; background: #f0fdf4; }
.action-card i { color: #10b981; margin-bottom: 15px; font-size: 1.5rem; display: block; }
.action-card h4 { margin: 0 0 5px; color: #1e293b; font-weight: 700; }
.action-card p { font-size: 0.9rem; color: #64748b; margin: 0; }

/* Activity Sidebar */
.activity-card {
    background: #fff;
    padding: 24px;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
}

.activity-item {
    padding: 12px 0;
    border-bottom: 1px solid #f8fafc;
}

.activity-item:last-child { border: none; }
.activity-item strong { display: block; font-size: 0.95rem; color: #1e293b; }
.activity-item small { color: #94a3b8; font-size: 0.8rem; }

@media (max-width: 1024px) {
    .dashboard-content-split { grid-template-columns: 1fr; }
}
</style>

<div class="dashboard-header">
    <div>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h1>
        <p>System overview for <strong>ReSEED</strong></p>
    </div>
    <div class="date-badge" style="background: #fff; padding: 10px 20px; border-radius: 12px; font-weight: 600; color: #64748b; border: 1px solid #f1f5f9;">
        <i class="fa-regular fa-calendar"></i> <?= date('M d, Y') ?>
    </div>
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
        <div class="stat-icon bg-orange"><i class="fa-solid fa-envelope"></i></div>
        <div class="stat-info">
            <h3><?= $stats['contacts'] ?></h3>
            <span>Inquiries</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-purple"><i class="fa-solid fa-user-shield"></i></div>
        <div class="stat-info">
            <h3><?= $stats['admins'] ?></h3>
            <span>Team</span>
        </div>
    </div>
</div>

<div class="dashboard-content-split">
    <div class="main-column">
        <h2 class="section-title"><i class="fa-solid fa-bolt"></i> Quick Actions</h2>
        <div class="quick-actions">
            <a href="projects.php" class="action-card">
                <i class="fa-solid fa-folder-plus"></i>
                <h4>Manage Projects</h4>
                <p>Update restoration progress.</p>
            </a>
            <a href="posts.php" class="action-card">
                <i class="fa-solid fa-plus"></i>
                <h4>New Blog Post</h4>
                <p>Share a success story.</p>
            </a>
            <a href="contacts.php" class="action-card">
                <i class="fa-solid fa-inbox"></i>
                <h4>Inbox</h4>
                <p>View latest inquiries.</p>
            </a>
            <a href="users.php" class="action-card">
                <i class="fa-solid fa-users-cog"></i>
                <h4>Settings</h4>
                <p>Manage admin access.</p>
            </a>
        </div>
    </div>

    <div class="side-column">
        <h2 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</h2>
        <div class="activity-card">
            <?php if (empty($recentInquiries)): ?>
                <p style="color: #94a3b8; font-size: 0.9rem;">No recent messages.</p>
            <?php else: ?>
                <?php foreach($recentInquiries as $msg): ?>
                    <div class="activity-item">
                        <strong><?= htmlspecialchars((string)$msg['name']) ?> sent a message</strong>
                        <small><?= date('M d, H:i', strtotime((string)$msg['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once $adminRoot . '/includes/admin_footer.php'; ?>