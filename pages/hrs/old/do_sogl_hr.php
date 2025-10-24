<?php
require_once __DIR__ . '/../check_post.php';
require_once __DIR__ . '/../check_auth.php';
// проверяем кто согласует
if (!$user['hr_valid']) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: '. root() .'/hrs.php');
    die;
}

$num = $_POST['status'];
if ($_POST['action']=='accept') {
        $_POST['status'] = 100;
        flash('Заявка успешно одобрена!', 'success');
        $stat = 1;
} elseif ($_POST['action']=='reject') {
    $_POST['status'] = 0;
    flash('Заявка отклонена!', 'warning');
    $stat = 0;
}
elseif ($_POST['action']=='expectation') {
    $_POST['status'] = 5;
    flash('Заявка в ожидание!', 'info');
    $stat = 5;
}


// записываем в таблицу hrapp 'status'
$stmt = pdo()->prepare('UPDATE `hrapp` SET `status` = :status WHERE `id` = :id');
$stmt->execute(['id'=>$_POST['id'], 'status'=>$_POST['status']]);


$id = $_POST['id'];
$sogl = $_POST['creator_id'];
$notes = $_POST['note'];
$status= $_POST['status'];
$date= date('Y-m-d');
$stmt2 = pdo()->prepare("INSERT INTO `hrcomment` (`id`, `date`, `meneger`, `notes`, `status`) VALUES (?, ?, ?, ?, ?)");
$stmt2->execute([$id, $date, $sogl, $notes, $status]);


header('Location: '. root() .'/hrs.php');

?>