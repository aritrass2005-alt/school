<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'student') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$routineStmt = $pdo->query('SELECT class, section, file FROM routines ORDER BY id DESC LIMIT 10');
$routines = $routineStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Routines</h1>
        <p>Download class routines.</p>
    </header>

    <section class="card">
        <?php if (!$routines): ?>
            <p class="muted">No routines uploaded yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Section</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($routines as $routine): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($routine['class']); ?></td>
                            <td><?php echo htmlspecialchars($routine['section']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($routine['file']); ?>" target="_blank">Download</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
