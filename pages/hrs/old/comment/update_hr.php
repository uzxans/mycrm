<?php
include_once __DIR__ . '/../../check_auth.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hrId = $_POST['hr_id'];
    $id = $_POST['id']; // ID записи, которую нужно обновить
    $dateEdit = date('Y-m-d H:i');
    $hr_original = $_POST['hr_original'];

    if(!empty($hr_original)) {
        $stmt = pdo()->prepare("UPDATE `hrapp` SET `hr_dop` = :hr_original WHERE `id` = :id");
        $stmt->bindParam(':hr_original', $hr_original, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Здесь должен быть код для обновления HR в базе данных
    // Например:
    $stmt1 = pdo()->prepare("UPDATE `hrapp` SET `creator` = :hr_id, `date_edit` = :date_edit WHERE `id` = :id");
    $stmt1->bindParam(':hr_id', $hrId, PDO::PARAM_INT);
    $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt1->bindParam(':date_edit', $dateEdit, PDO::PARAM_STR);
    $stmt1->execute();

    // Получаем данные нового HR для обновления интерфейса
    $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :hr_id");
    $stmt->bindParam(':hr_id', $hrId);
    $stmt->execute();
    $newHr = $stmt->fetch();

    $response = [
        'success' => true,
        'newHrName' => $newHr['name'],
        'newHrImage' => $newHr['dir_img'] ? '/../'.$newHr['dir_img'] : '/../assets/userimg.jpg'
    ];

    echo json_encode($response);
}