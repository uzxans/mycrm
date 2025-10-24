<?php
require_once __DIR__ . '/../check_post.php';
require_once __DIR__ . '/../check_auth.php';
// проверяем кто завершает
if (!$user['hr_edit']) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: '. root() .'/hrs.php');
    die;
}
if (isset($_POST['creator']) and $_POST['creator']==$user['id']) {
    if ($_POST['action']=='accept') {
        $_POST['status']++;
        flash('Заявка завершена', 'success');
    } elseif ($_POST['action']=='reject') {
        $_POST['status']=-1;
        flash('Заявка отменена', 'warning');
    }
    $stmt = pdo()->prepare('UPDATE `hrapp` SET `status` = :status WHERE `id` = :id');
    $stmt->execute(['id'=>$_POST['id'], 'status'=>$_POST['status']]);
    $stmt = pdo()->prepare('UPDATE `hrcoord` SET `coord_date` = :coord_date, `status` = :status, `note` = :note WHERE `id` = :id AND `coord_num` = 100');
    $stmt->execute(['id'=>$_POST['id'], 'coord_date'=>date('Y-m-d'), 'status'=>$_POST['status'], 'note'=>$_POST['note']]);
    // убираем оставшихся согласующих
    $stmt = pdo()->prepare('UPDATE `hrcoord` SET `status` = :status WHERE `id` = :id AND `status` IS NULL');
    $stmt->execute(['status'=>2, 'id'=>$_POST['id']]);
} else {
    flash('Невозможно закрыть заявку', 'danger');
}
header('Location: '. root() .'/hrs.php');

?>