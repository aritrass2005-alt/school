<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /school-system/auth/login.php');
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header('Location: /school-system/admin/dashboard.php');
    exit;
}

if ($_SESSION['role'] === 'teacher') {
    header('Location: /school-system/teacher/dashboard.php');
    exit;
}

header('Location: /school-system/student/dashboard.php');
exit;
