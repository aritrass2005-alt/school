<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, name, email, password, role, status FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] !== 'active') {
            $error = 'Account is inactive. Contact admin.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: /school-system/admin/dashboard.php');
                exit;
            }

            if ($user['role'] === 'teacher') {
                header('Location: /school-system/teacher/dashboard.php');
                exit;
            }

            header('Location: /school-system/student/dashboard.php');
            exit;
        }
    } else {
        $error = 'Invalid credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School System Login</title>
    <link rel="stylesheet" href="/school-system/assets/css/styles.css">
</head>
<body class="login-body">
    <div class="login-card">
        <h1>School System</h1>
        <p class="muted">Single login for Admin, Teacher, and Student.</p>
        <?php if ($error): ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" placeholder="admin@mail.com" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="********" required>
            <button type="submit">Login</button>
        </form>
        <p class="hint">Default admin: admin@mail.com / 123456</p>
    </div>
</body>
</html>
