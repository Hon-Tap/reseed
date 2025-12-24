<?php
declare(strict_types=1);

$adminDir = __DIR__; 

require_once $adminDir . '/includes/admin_auth.php';
require_once $adminDir . '/includes/admin_header.php';

/* ===================== METRICS ===================== */
try {
    $stats = [
        'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
        'posts'    => $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
        'contacts' => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
        'admins'   => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    ];
} catch (Throwable $e) {
    $stats = ['projects' => 0, 'posts' => 0, 'contacts' => 0, 'admins' => 0];
}
?>

<style>
    .dashboard-header { margin-bottom: 40px; }
    .dashboard-header h1 { 
        font-size: 2.25rem; 
        font-weight: 800; 
        color: #0f172a; 
        letter-spacing: -0.025em;
        margin-bottom: 8px;
    }
    .dashboard-header p { color: #64748b; font-size: 1.1rem; }

    /* Stats Grid */
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
        gap: 24px; 
    }
    .stat-card { 
        background: white; 
        border-radius: 20px; 
        padding: 28px; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
        display: flex; 
        align-items: center; 
        gap: 20px;
        transition: transform 0.2s ease;
    }
    .stat-card:hover { transform: translateY(-4px); }
    
    .stat-icon { 
        width: 64px; 
        height: 64px; 
        border-radius: 16px; 
        display: grid; 
        place-items: center; 
        font-size: 24px; 
        color: #fff; 
    }

    /* Gradients matching your theme */
    .bg-green  { background: linear-gradient(135deg, #22c55e, #166534); }
    .bg-blue   { background: linear-gradient(135deg, #3b82f6, #1e40af); }
    .bg-orange { background: linear-gradient(135deg, #f97316, #9a3412); }
    .bg-purple { background: linear-gradient(135deg, #a855f7, #6b21a8); }

    .stat-info h3 { margin: 0; font-size: 1.8rem; font-weight: 800; color: #1e293b; }
    .stat-info span { font-size: 0.9rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

    /* Quick Actions */
    .section-title { 
        margin: 48px 0 24px; 
        font-size: 1.25rem; 
        font-weight: 700; 
        color: #1e293b; 
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title::after { content: ""; flex: 1; height: 1px; background: #e2e8f0; }

    .quick-actions { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
        gap: 24px; 
    }
    .action-card { 
        background: white; 
        padding: 32px; 
        border-radius: 20px; 
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .action-card:hover { 
        border-color: #22c55e;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08);
    }
    .action-icon {
        width: 48px;
        height: 48px;
        background: #f0fdf4;
        color: #166534;
        border-radius: 12px;
        display: grid;
        place-items: center;
        margin-bottom: 20px;
        font-size: 20px;
    }
    .action-card h4 { margin: 0 0 12px; font-size: 1.2rem; font-weight: 700; }
    .action-card p { color: #64748b; line-height: 1.6; font-size: 0.95rem; flex: 1; margin-bottom: 24px; }
    .action-card a { 
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        background: #f8fafc;
        border-radius: 12px;
        font-weight: 700; 
        color: #166534; 
        transition: all 0.2s;
        text-decoration: none;
    }
    .action-card a:hover { background: #166534; color: white; }
</style>

<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p>System overview and management for <strong>ReSEED</strong> project.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-green"><i class="fa-solid fa-leaf"></i></div>
        <div class="stat-info">
            <h3><?= (int)$stats['projects'] ?></h3>
            <span>Projects</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-blue"><i class="fa-solid fa-pen-nib"></i></div>
        <div class="stat-info">
            <h3><?= (int)$stats['posts'] ?></h3>
            <span>Blog Posts</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-orange"><i class="fa-solid fa-envelope-open-text"></i></div>
        <div class="stat-info">
            <h3><?= (int)$stats['contacts'] ?></h3>
            <span>Inquiries</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon bg-purple"><i class="fa-solid fa-user-gear"></i></div>
        <div class="stat-info">
            <h3><?= (int)$stats['admins'] ?></h3>
            <span>Admins</span>
        </div>
    </div>
</div>

<div class="section-title">Quick Actions</div>

<div class="quick-actions">
    <div class="action-card">
        <div class="action-icon"><i class="fa-solid fa-plus"></i></div>
        <h4>Project Hub</h4>
        <p>Update restoration progress, add GPS locations, and manage field activity reports.</p>
        <a href="projects.php">Go to Projects</a>
    </div>

    <div class="action-card">
        <div class="action-icon"><i class="fa-solid fa-newspaper"></i></div>
        <h4>Content Creator</h4>
        <p>Draft new articles or success stories to keep your supporters and donors engaged.</p>
        <a href="posts.php">Manage Blog</a>
    </div>

    <div class="action-card">
        <div class="action-icon"><i class="fa-solid fa-message"></i></div>
        <h4>Communication</h4>
        <p>You have <?= (int)$stats['contacts'] ?> total messages. Check for new partnership requests.</p>
        <a href="contacts.php">Open Inbox</a>
    </div>
</div>

<?php require_once $adminDir . '/includes/admin_footer.php'; ?>