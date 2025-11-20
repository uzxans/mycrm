<?php
require_once '../../boot.php';
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = pdo()->prepare("SELECT h.date, h.manager, h.comments, u.name AS manager
FROM hrcomment h
LEFT JOIN users u ON u.id = h.manager
WHERE h.id = ?
ORDER BY h.date DESC
");
$stmt->execute([$id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($comments);


