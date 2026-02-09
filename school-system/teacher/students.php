<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'teacher') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$studentsStmt = $pdo->query('SELECT student_id, roll, class, section FROM students ORDER BY roll ASC LIMIT 50');
$students = $studentsStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<main class="content">
    <header class="page-header">
        <h1>Student List</h1>
        <p>Search and review your students.</p>
    </header>

    <section class="card">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Roll</th>
                    <th>Class</th>
                    <th>Section</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['roll']); ?></td>
                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                        <td><?php echo htmlspecialchars($student['section']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
