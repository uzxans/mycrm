<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__.'/../check_post.php';
require_once __DIR__.'/../check_auth.php';
// проверяем кто меняет пароль
if (!$user['adm']) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: '. root(). '/map.php');
    die;
}
// если это добавление пользователя
if ($_POST['id'] == 0) {
    // Проверить не пустое ли поле Пароля!!
    if ($_POST['password'] == '') {
        flash('Введите пароль!', 'danger');
        header('Location: '. root(). '/user/0'); // Возврат на форму создания пользователя
        die; // Остановка выполнения скрипта
    }
    // Проверим, не занято ли имя пользователя
    $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `username` = :username");
    $stmt->execute(['username' => $_POST['username']]);
    if ($stmt->rowCount() > 0) {
        flash('Этот логин уже занят.', 'danger');
        header('Location: '. root(). '/user/0'); // Возврат на форму создания пользователя
        die; // Остановка выполнения скрипта
    }
    // Добавим пользователя в базу
    $execute = array();
    $prepare = sql_add("users", $_POST, $execute);
    #echo $prepare . '<br>';
    $stmt = pdo()->prepare($prepare);
    foreach ($execute as $name => &$val) {
        $stmt->bindParam($name, $val, PDO::PARAM_STR);
        #echo $name . '=>' . $val . '<br>';
    }
    $stmt->execute();
    flash('Пользователь успешно добавлен!', 'success');
    header('Location: '. root(). '/admin.php');
}
else {
    // Проверим, не занято ли имя пользователя
    $stmt = pdo()->prepare("SELECT `id` FROM `users` WHERE `username` = :username");
    $stmt->execute(['username' => $_POST['username']]);
    $count = $stmt->rowCount();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($count > 0 and $_POST['id']!=$row['id']) {
        flash('Этот логин уже используется.', 'warning');
        header('Location: '. root(). '/user/'. $_POST['id']); // Возврат на форму пользователя
        die; // Остановка выполнения скрипта
    }
    // проверяем наличие пользователя с указанным ID
    $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :id");
    $stmt->execute(['id' => $_POST['id']]);
    if (!$stmt->rowCount()) {
        flash('Пользователь с такими данными не зарегистрирован', 'danger');
        header('Location: '. root(). '/admin.php');
        die;
    }
    $user1 = $stmt->fetch(PDO::FETCH_ASSOC);
    // меняем пароль
    if ($_POST['password']) {
        $stmt = pdo()->prepare('UPDATE `users` SET `password` = :newpass WHERE `id` = :id');
        $stmt->execute([
            'id' => $user1['id'],
            'newpass' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ]);
    }
    // меняем остальное
    $execute = array();
    $prepare = sql_edit("users", $_POST, $execute);
    #echo $prepare . '<br>';
    $stmt = pdo()->prepare($prepare);
    foreach ($execute as $name => &$val) {
        $stmt->bindParam($name, $val, PDO::PARAM_STR);
        #echo $name . '=>' . $val . '<br>';
    }
    $stmt->execute();

    // Обработка объектов бригадира (если это бригадир)
    if ($_POST['position'] == 9) {

        // Удаляем старые записи
        $stmt = pdo()->prepare("DELETE FROM brigadir WHERE id_user = ?");
        $stmt->execute([$_POST['id']]);

        // Добавляем новые выбранные объекты (если они есть)
        if (!empty($_POST['objects'])) {
            $stmt = pdo()->prepare("INSERT INTO brigadir (id_user, objects) VALUES (?, ?)");
            foreach ($_POST['objects'] as $objectId) {
                $stmt->execute([$_POST['id'], $objectId]);
            }
        }
    }

    flash('Данные успешно изменены!', 'success');
    header('Location: '. root(). '/admin.php');
}
?>