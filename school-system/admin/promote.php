<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /school-system/auth/login.php');
    exit;
}

$stmt = $pdo->prepare('UPDATE students SET class = class + 1 WHERE year = ?');
$stmt->execute([2026]);

header('Location: /school-system/admin/students.php');
exit;
