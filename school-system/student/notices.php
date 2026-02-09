<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'student') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$noticeStmt = $pdo->query('SELECT title, description, created_at FROM notices ORDER BY created_at DESC');
$notices = $noticeStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Notices</h1>
        <p>Stay updated with school announcements.</p>
    </header>

    <section class="card">
        <?php if (!$notices): ?>
            <p class="muted">No notices yet.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($notices as $notice): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($notice['title']); ?></strong>
                        <span class="muted"><?php echo htmlspecialchars($notice['created_at']); ?></span>
                        <p><?php echo htmlspecialchars($notice['description']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
