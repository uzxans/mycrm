<?php
require_once __DIR__ . '/../check_auth.php';




// убираем запись из БД
$stmt = pdo()->prepare("DELETE FROM `file_object` WHERE `id` = :id");
$stmt->execute(['id' => $_GET['id']]);

#echo $dir;

flash('Документ удалён!', 'success');
header('Location: '. root(). '/objects/' . $_GET['object_id']);
?>