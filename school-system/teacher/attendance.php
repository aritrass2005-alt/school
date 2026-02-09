<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'teacher') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = (int) ($_POST['student_id'] ?? 0);
    $status = $_POST['status'] ?? 'present';
    $attendanceDate = $_POST['date'] ?? date('Y-m-d');

    if ($attendanceDate !== date('Y-m-d')) {
        $message = 'Attendance can only be marked for today.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO attendance (student_id, date, status, marked_by, locked) VALUES (?, ?, ?, ?, 0)');
        $stmt->execute([$studentId, $attendanceDate, $status, $_SESSION['user_id']]);
        $message = 'Attendance saved.';
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Mark Attendance</h1>
        <p>Attendance is locked after midnight. Admin can unlock.</p>
    </header>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="card">
        <form method="POST">
            <label>Student ID</label>
            <input type="number" name="student_id" min="1" required>
            <label>Date</label>
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
            <label>Status</label>
            <select name="status">
                <option value="present">Present</option>
                <option value="absent">Absent</option>
            </select>
            <button type="submit">Save Attendance</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
