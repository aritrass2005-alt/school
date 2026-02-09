<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'student') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$studentStmt = $pdo->prepare('SELECT id FROM students WHERE user_id = ? LIMIT 1');
$studentStmt->execute([$_SESSION['user_id']]);
$student = $studentStmt->fetch();

$results = [];
if ($student) {
    $resultStmt = $pdo->prepare('SELECT exam_name, file, published FROM results WHERE student_id = ? ORDER BY id DESC');
    $resultStmt->execute([$student['id']]);
    $results = $resultStmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Results</h1>
        <p>Download your published results.</p>
    </header>

    <section class="card">
        <?php if (!$results): ?>
            <p class="muted">Results will appear once published.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Exam</th>
                        <th>Status</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['exam_name']); ?></td>
                            <td><?php echo $result['published'] ? 'Published' : 'Pending'; ?></td>
                            <td>
                                <?php if ($result['published'] && $result['file']): ?>
                                    <a href="<?php echo htmlspecialchars($result['file']); ?>" target="_blank">Download</a>
                                <?php else: ?>
                                    <span class="muted">Not available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
