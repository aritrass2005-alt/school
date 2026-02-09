<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$totalStudents = $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$totalTeachers = $pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn();
$attendanceTodayStmt = $pdo->prepare('SELECT COUNT(*) FROM attendance WHERE date = ?');
$attendanceTodayStmt->execute([date('Y-m-d')]);
$attendanceToday = $attendanceTodayStmt->fetchColumn();
$latestNoticeStmt = $pdo->query('SELECT title, created_at FROM notices ORDER BY created_at DESC LIMIT 1');
$latestNotice = $latestNoticeStmt->fetch();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>
    </header>

    <section class="stats-grid">
        <div class="stat-card">
            <h3>Total Students</h3>
            <p><?php echo (int) $totalStudents; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Teachers</h3>
            <p><?php echo (int) $totalTeachers; ?></p>
        </div>
        <div class="stat-card">
            <h3>Attendance Today</h3>
            <p><?php echo (int) $attendanceToday; ?></p>
        </div>
        <div class="stat-card">
            <h3>Latest Notice</h3>
            <p><?php echo $latestNotice ? htmlspecialchars($latestNotice['title']) : 'No notices yet'; ?></p>
            <?php if ($latestNotice): ?>
                <span class="muted"><?php echo htmlspecialchars($latestNotice['created_at']); ?></span>
            <?php endif; ?>
        </div>
    </section>

    <section class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-grid">
            <a class="action-card" href="/school-system/admin/students.php">Import Students</a>
            <a class="action-card" href="/school-system/admin/teachers.php">Create Teacher</a>
            <a class="action-card" href="/school-system/admin/attendance.php">Attendance Lock</a>
            <a class="action-card" href="/school-system/admin/notices.php">Create Notice</a>
        </div>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
