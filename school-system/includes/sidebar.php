<?php
$role = $_SESSION['role'] ?? 'guest';
?>
<aside class="sidebar">
    <div class="brand">School System</div>
    <nav>
        <a href="/school-system/index.php">Dashboard</a>
        <?php if ($role === 'admin'): ?>
            <a href="/school-system/admin/students.php">Students</a>
            <a href="/school-system/admin/teachers.php">Teachers</a>
            <a href="/school-system/admin/attendance.php">Attendance</a>
            <a href="/school-system/admin/notices.php">Notices</a>
        <?php elseif ($role === 'teacher'): ?>
            <a href="/school-system/teacher/students.php">Students</a>
            <a href="/school-system/teacher/attendance.php">Attendance</a>
            <a href="/school-system/teacher/notes.php">Notes</a>
            <a href="/school-system/teacher/exams.php">Exams</a>
        <?php else: ?>
            <a href="/school-system/student/notices.php">Notices</a>
            <a href="/school-system/student/results.php">Results</a>
            <a href="/school-system/student/routines.php">Routines</a>
        <?php endif; ?>
        <a href="/school-system/auth/logout.php" class="danger">Logout</a>
    </nav>
</aside>
