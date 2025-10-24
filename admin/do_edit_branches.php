<?php
require_once __DIR__.'/../check_post.php';
require_once __DIR__.'/../check_auth.php';
// проверяем кто меняет филиалы
if (!$user['adm']) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: '. root(). '/map.php');
    die;
}

function make_array($post) {
    $res = array();
    foreach ($post as $branch => &$val) {
        if ($val) {
            $arr = explode('-', $branch);
            $res[$arr[1]][$arr[0]] = $val;
        }
    }
    return $res;
}

$res = make_array($_POST);
foreach ($res as $branch_id => &$arr) {
    $execute=array();
    $prepare = sql_edit('branches', $arr, $execute);
    # echo $prepare . '<br>';
    $stmt = pdo()->prepare($prepare);
    $stmt->bindParam('id', $branch_id, PDO::PARAM_STR);
    foreach ($execute as $name => &$val) {
        $stmt->bindParam($name, $val, PDO::PARAM_STR);
        # echo $name . '=>' . $val . '<br>';
    }
    $stmt->execute();
}
flash('Данные успешно изменены!', 'success');
header('Location: '. root() .'/admin.php');


?>