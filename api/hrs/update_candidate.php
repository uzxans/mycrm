<?php
require_once '../../boot.php';
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$id = $_POST['id'] ?? null;
$full_name = $_POST['full_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$country = $_POST['country'] ?? '';
$profession = $_POST['profession'] ?? '';
$birth_date = $_POST['birth_date'] ?? '';

if ($id) {
    $stmt = pdo()->prepare("UPDATE hrapp SET full_name=?, phone=?, country=?, profession=?, birth_date=? WHERE id=?");
    $success = $stmt->execute([$full_name, $phone, $country, $profession, $birth_date, $id]);
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'message' => 'Не указан ID']);
}

