<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name && $email) {
        $pdo->beginTransaction();
        $password = password_hash('teacher@123', PASSWORD_DEFAULT);

        $userStmt = $pdo->prepare('INSERT INTO users (name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $userStmt->execute([$name, $email, $password, 'teacher', 'active']);
        $userId = (int) $pdo->lastInsertId();

        $teacherStmt = $pdo->prepare('INSERT INTO teachers (user_id, department, phone) VALUES (?, ?, ?)');
        $teacherStmt->execute([$userId, $department, $phone]);

        $pdo->commit();
        $message = 'Teacher created with default password teacher@123.';
    } else {
        $message = 'Name and email are required.';
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Create Teacher</h1>
        <p>Auto-generate login credentials for teachers.</p>
    </header>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="card">
        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Department</label>
            <input type="text" name="department">
            <label>Phone</label>
            <input type="text" name="phone">
            <button type="submit">Create Teacher</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
