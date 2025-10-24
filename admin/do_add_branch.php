<?php
require_once __DIR__.'/../check_post.php';
require_once __DIR__.'/../check_auth.php';
// проверяем кто добавляет филиал
if (!$user['adm']) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: '. root(). '/map.php');
    die;
}
$stmt = pdo()->prepare("INSERT INTO `branches` (`name`) VALUES (:branch_name)");
$stmt->execute(['branch_name' => $_POST['branch_name']]);
flash('Филиал успешно создан! Незабудьте выбрать глав созданного филиала', 'success');
header('Location: '. root() .'/admin.php');

?>