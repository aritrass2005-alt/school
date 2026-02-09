<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'teacher') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['exam_title'] ?? '');
    $link = trim($_POST['google_form_link'] ?? '');
    $class = trim($_POST['class'] ?? '');
    $section = trim($_POST['section'] ?? '');

    if ($title && $link) {
        $stmt = $pdo->prepare('INSERT INTO google_forms (exam_title, google_form_link, class, section, created_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$title, $link, $class, $section, $_SESSION['user_id']]);
        $message = 'Exam link saved.';
    } else {
        $message = 'Exam title and link are required.';
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Google Form Exams</h1>
        <p>Create a new exam link for students.</p>
    </header>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="card">
        <form method="POST">
            <label>Exam Title</label>
            <input type="text" name="exam_title" required>
            <label>Google Form Link</label>
            <input type="url" name="google_form_link" required>
            <label>Class</label>
            <input type="text" name="class">
            <label>Section</label>
            <input type="text" name="section">
            <button type="submit">Save Exam Link</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
