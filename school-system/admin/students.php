<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$message = '';
$templateRows = [
    ['name', 'email', 'roll', 'class', 'section', 'dob', 'year'],
    ['John Doe', 'john@example.com', '3', '10', 'A', '2008-05-12', '2026'],
];

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

if (isset($_GET['download']) && $_GET['download'] === 'template') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_template.csv"');
    $output = fopen('php://output', 'w');
    foreach ($templateRows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

$manualMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manual_submit'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $roll = (int) ($_POST['roll'] ?? 0);
    $class = (int) ($_POST['class'] ?? 0);
    $section = trim($_POST['section'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $year = (int) ($_POST['year'] ?? 0);

    if ($name && $email && $roll && $class && $section && $dob && $year) {
        $pdo->beginTransaction();
        $studentId = 'VA' . str_pad((string) $roll, 2, '0', STR_PAD_LEFT) . $year;
        $password = password_hash(str_replace('-', '', $dob), PASSWORD_DEFAULT);

        $userStmt = $pdo->prepare('INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $userStmt->execute([$name, $email, $password, 'student', 'active']);
        $userId = (int) $pdo->lastInsertId();

        $studentStmt = $pdo->prepare('INSERT INTO students (user_id, roll, class, section, dob, year, student_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $studentStmt->execute([$userId, $roll, $class, $section, $dob, $year, $studentId]);

        $pdo->commit();
        $manualMessage = 'Student created successfully.';
    } else {
        $manualMessage = 'Please fill in all fields.';
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
        <h2>Step 1: Download CSV Template</h2>
        <p class="muted">Download, fill, then upload. Works for CSV or Excel (saved as CSV).</p>
        <a class="button-secondary" href="/school-system/admin/students.php?download=template">Download CSV Template</a>
    </section>

    <section class="card">
        <h2>Step 2: Upload Student Sheet</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="student_file" accept=".xlsx,.csv" required>
            <button type="submit">Upload</button>
        </form>
        <p class="muted">Student ID format: VA + Roll(2 digits) + Year (e.g., VA032026).</p>
        <p class="muted">Default password: DOB in YYYYMMDD format (hashed before saving).</p>
    </section>

    <section class="card">
        <h2>Step 3: Add Student Manually</h2>
        <p class="muted">Use this form when you want to add a single student without a file.</p>
        <?php if ($manualMessage): ?>
            <div class="alert"><?php echo htmlspecialchars($manualMessage); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="manual_submit" value="1">
            <label>Full Name</label>
            <input type="text" name="name" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Roll</label>
            <input type="number" name="roll" min="1" required>
            <label>Class</label>
            <input type="number" name="class" min="1" required>
            <label>Section</label>
            <input type="text" name="section" required>
            <label>Date of Birth</label>
            <input type="date" name="dob" required>
            <label>Year</label>
            <input type="number" name="year" min="2000" required>
            <button type="submit">Create Student</button>
        </form>
    </section>

    <section class="card">
        <h2>Promote All Students</h2>
        <p>Moves students to the next class for the current year.</p>
        <a class="button-secondary" href="/school-system/admin/promote.php">Run Promotion</a>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
