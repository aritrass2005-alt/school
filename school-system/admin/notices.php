<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title) {
        $stmt = $pdo->prepare('INSERT INTO notices (title, description, created_by, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$title, $description, $_SESSION['user_id']]);
        $message = 'Notice published.';
    } else {
        $message = 'Title is required.';
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Create Notice</h1>
        <p>Share updates with all users.</p>
    </header>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <section class="card">
        <form method="POST">
            <label>Title</label>
            <input type="text" name="title" required>
            <label>Description</label>
            <textarea name="description" rows="4"></textarea>
            <button type="submit">Publish Notice</button>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
