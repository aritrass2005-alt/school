<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'teacher') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['note_file'])) {
    $fileName = $_FILES['note_file']['name'] ?? '';
    $tmpName = $_FILES['note_file']['tmp_name'] ?? '';
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'docx', 'pptx'];

    if (!in_array($extension, $allowed, true)) {
        $message = 'Invalid file type. Upload PDF or Office files.';
    } else {
        $destination = __DIR__ . '/../uploads/notes/' . uniqid('note_', true) . '.' . $extension;
        if (move_uploaded_file($tmpName, $destination)) {
            $message = 'Notes uploaded.';
        } else {
            $message = 'Upload failed.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Upload Notes</h1>
        <p>Share notes with your class.</p>
    </header>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="card">
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="note_file" required>
            <button type="submit">Upload Notes</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
