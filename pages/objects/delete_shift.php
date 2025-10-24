<?php
require_once __DIR__ . '/../check_post.php';
require_once __DIR__ . '/../check_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['shift_id'])) {
        $shiftId = $_POST['shift_id'];
        $url = $_POST['url'];
        // Подготовка и выполнение запроса на удаление
        $stmt = pdo()->prepare("DELETE FROM `time_hr` WHERE id = :shift_id");
        $stmt->execute(['shift_id' => $shiftId]);

        // Перенаправление обратно на страницу с таблицей (например, на index.php)
        header('Location: ' . $url);
        exit;
    }
}
?>
