<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'teacher') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$studentsCount = $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$today = date('Y-m-d');
$attendanceStmt = $pdo->prepare('SELECT COUNT(*) FROM attendance WHERE date = ? AND marked_by = ?');
$attendanceStmt->execute([$today, $_SESSION['user_id']]);
$attendanceCount = $attendanceStmt->fetchColumn();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Teacher Dashboard</h1>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>
    </header>

    <section class="stats-grid">
        <div class="stat-card">
            <h3>Total Students</h3>
            <p><?php echo (int) $studentsCount; ?></p>
        </div>
        <div class="stat-card">
            <h3>Attendance Marked Today</h3>
            <p><?php echo (int) $attendanceCount; ?></p>
        </div>
        <div class="stat-card">
            <h3>Quick Links</h3>
            <p>Mark attendance and upload notes.</p>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
