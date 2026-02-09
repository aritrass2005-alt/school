<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['student_file'])) {
    $fileName = $_FILES['student_file']['name'] ?? '';
    $tmpName = $_FILES['student_file']['tmp_name'] ?? '';
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['xlsx', 'csv'];

    if (!in_array($extension, $allowed, true)) {
        $message = 'Invalid file type. Upload .xlsx or .csv.';
    } else {
        $destination = __DIR__ . '/../uploads/results/' . uniqid('students_', true) . '.' . $extension;
        if (move_uploaded_file($tmpName, $destination)) {
            $message = 'File uploaded. Ready to import using PhpSpreadsheet.';
        } else {
            $message = 'Upload failed. Try again.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Student Import</h1>
        <p>Upload Excel/CSV and auto-create student logins.</p>
    </header>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="card">
        <h2>Upload Student Sheet</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="student_file" accept=".xlsx,.csv" required>
            <button type="submit">Upload</button>
        </form>
        <p class="muted">Student ID format: VA + Roll(2 digits) + Year (e.g., VA032026).</p>
        <p class="muted">Default password: DOB in YYYYMMDD format (hashed before saving).</p>
    </section>

    <section class="card">
        <h2>Promote All Students</h2>
        <p>Moves students to the next class for the current year.</p>
        <a class="button-secondary" href="/school-system/admin/promote.php">Run Promotion</a>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
