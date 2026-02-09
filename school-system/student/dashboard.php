<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'student') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$studentStmt = $pdo->prepare('SELECT id, class, section FROM students WHERE user_id = ? LIMIT 1');
$studentStmt->execute([$_SESSION['user_id']]);
$student = $studentStmt->fetch();
$studentId = $student['id'] ?? 0;

$noticeStmt = $pdo->query('SELECT title, description, created_at FROM notices ORDER BY created_at DESC LIMIT 3');
$notices = $noticeStmt->fetchAll();

$attendanceTotalStmt = $pdo->prepare('SELECT COUNT(*) FROM attendance WHERE student_id = ?');
$attendanceTotalStmt->execute([$studentId]);
$totalAttendance = (int) $attendanceTotalStmt->fetchColumn();

$attendancePresentStmt = $pdo->prepare('SELECT COUNT(*) FROM attendance WHERE student_id = ? AND status = ?');
$attendancePresentStmt->execute([$studentId, 'present']);
$presentAttendance = (int) $attendancePresentStmt->fetchColumn();

$attendancePercent = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100) : 0;

$resultsStmt = $pdo->prepare('SELECT exam_name, published FROM results WHERE student_id = ? ORDER BY id DESC LIMIT 3');
$resultsStmt->execute([$studentId]);
$results = $resultsStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Student Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>.</p>
    </header>

    <section class="stats-grid">
        <div class="stat-card">
            <h3>Attendance</h3>
            <p><?php echo $attendancePercent; ?>%</p>
            <div class="progress">
                <div class="progress-bar" style="width: <?php echo $attendancePercent; ?>%"></div>
            </div>
        </div>
        <div class="stat-card">
            <h3>Class</h3>
            <p><?php echo htmlspecialchars($student['class'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($student['section'] ?? ''); ?></p>
        </div>
        <div class="stat-card">
            <h3>Upcoming Exams</h3>
            <p>Check Google Form links in Exams.</p>
        </div>
    </section>

    <section class="card">
        <h2>Latest Notices</h2>
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

    <section class="card">
        <h2>Recent Results</h2>
        <?php if (!$results): ?>
            <p class="muted">Results will appear once published.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($results as $result): ?>
                    <li><?php echo htmlspecialchars($result['exam_name']); ?> - <?php echo $result['published'] ? 'Published' : 'Pending'; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
