<?php
require_once __DIR__ . '/../check_auth.php';

// убираем запись из БД
$stmt = pdo()->prepare("DELETE FROM `hrapp` WHERE `id` = :id");
$stmt->execute(['id' => $_GET['id']]);
#echo $dir;
flash('Пользователь удалён!', 'success');
header('Location: '. root(). '/hrs.php');
?>