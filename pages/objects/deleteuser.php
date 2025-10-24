<?php
require_once __DIR__ . '/../check_post.php';
require_once __DIR__ . '/../check_auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $object= $_POST['newObject'];
    $comment = $_POST['comment'];
    $meneger = $_POST['meneger'];
    $date = $_POST['date'];

// Обновляем статус и объект в базе данных
    $stmt = pdo()->prepare("UPDATE `hrapp` SET 
                    `objects` = ?, 
                    `status` = ?
                    WHERE `id` = ?");
    $stmt->execute([
        $object,
        6,
        $id]);


    $stmt2 = pdo()->prepare("INSERT INTO `hrhistory` (`id`, `comment`, `objects`, `meneger`, `date`) VALUES (?, ?, ?, ?, ?)");
    $stmt2->execute([$id, $comment, $object, $meneger, $date]);
// Перенаправьте пользователя на страницу, где отображаются посты (например, index.php или posts.php)
    flash('Заявка успешно удалена!', 'danger');
    header('Location: ' . root() . '/objects.php');
    exit();
}
?>
