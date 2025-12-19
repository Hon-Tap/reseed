<?php
include "includes/admin_auth.php";
include "includes/admin_header.php";

// --- Metrics ---
try {
    $stats = [
        'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
        'posts'    => $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
        'contacts' => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
        'admins'   => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    ];
} catch (Throwable $e) {
    $stats = ['projects'=>0,'posts'=>0,'contacts'=>0,'admins'=>0];
}
?>

<style>
.dashboard-header{
  margin-bottom:32px;
}
.dashboard-header h1{
  font-size:2rem;
  margin-bottom:6px;
}
.dashboard-header p{
  color:var(--muted);
}

.stats-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:24px;
}

.stat-card{
  background:var(--surface);
  border-radius:var(--radius);
  padding:24px;
  box-shadow:var(--shadow);
  display:flex;
  align-items:center;
  gap:18px;
}

.stat-icon{
  width:56px;
  height:56px;
  border-radius:14px;
  display:grid;
  place-items:center;
  font-size:22px;
  color:#fff;
}

.bg-green{background:linear-gradient(135deg,#16a34a,#15803d)}
.bg-blue{background:linear-gradient(135deg,#2563eb,#1e40af)}
.bg-orange{background:linear-gradient(135deg,#f97316,#c2410c)}
.bg-purple{background:linear-gradient(135deg,#7c3aed,#5b21b6)}

.stat-info h3{
  margin:0;
  font-size:1.6rem;
}
.stat-info span{
  font-size:.85rem;
  color:var(--muted);
  font-weight:600;
}

.quick-actions{
  margin-top:40px;
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
  gap:24px;
}

.action-card{
  background:var(--surface);
  padding:26px;
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  transition:.25s;
}
.action-card:hover{
  transform:translateY(-6px);
}
.action-card h4{
  margin-top:0;
}
.action-card p{
  color:var(--muted);
}
.action-card a{
  display:inline-block;
  margin-top:12px;
  font-weight:700;
  color:var(--primary);
}
</style>

<div class="dashboard-header">
  <h1>Dashboard</h1>
  <p>System overview & content control</p>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon bg-green"><i class="fa-solid fa-diagram-project"></i></div>
    <div class="stat-info">
      <h3><?= $stats['projects'] ?></h3>
      <span>Projects</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon bg-blue"><i class="fa-solid fa-newspaper"></i></div>
    <div class="stat-info">
      <h3><?= $stats['posts'] ?></h3>
      <span>Posts</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon bg-orange"><i class="fa-solid fa-envelope"></i></div>
    <div class="stat-info">
      <h3><?= $stats['contacts'] ?></h3>
      <span>Messages</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon bg-purple"><i class="fa-solid fa-user-shield"></i></div>
    <div class="stat-info">
      <h3><?= $stats['admins'] ?></h3>
      <span>Admins</span>
    </div>
  </div>
</div>

<div class="quick-actions">
  <div class="action-card">
    <h4>Manage Projects</h4>
    <p>Create, update and showcase field activities.</p>
    <a href="projects.php">Go to Projects →</a>
  </div>

  <div class="action-card">
    <h4>Publish News</h4>
    <p>Keep donors and communities informed.</p>
    <a href="posts.php">Manage Posts →</a>
  </div>

  <div class="action-card">
    <h4>View Messages</h4>
    <p>Read and respond to inquiries.</p>
    <a href="contacts.php">Open Inbox →</a>
  </div>
</div>

<?php include "includes/admin_footer.php"; ?>
