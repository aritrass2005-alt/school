<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendanceId = (int) ($_POST['attendance_id'] ?? 0);
    if ($attendanceId > 0) {
        $stmt = $pdo->prepare('UPDATE attendance SET locked = 0 WHERE id = ?');
        $stmt->execute([$attendanceId]);
        $message = 'Attendance record unlocked.';
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Attendance Control</h1>
        <p>Unlock attendance entries after midnight (admin only).</p>
    </header>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="card">
        <form method="POST">
            <label>Attendance ID to Unlock</label>
            <input type="number" name="attendance_id" min="1" required>
            <button type="submit">Unlock Attendance</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
