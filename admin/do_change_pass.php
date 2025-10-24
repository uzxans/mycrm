<?php
require_once __DIR__.'/../check_post.php';
require_once __DIR__.'/../check_auth.php';

// проверяем текущий пароль
if (password_verify($_POST['password'], $user['password'])) {
    // Проверяем новые пароли
    if ($_POST['new_pass'] == $_POST['rep_pass']) {
        // Проверяем, не нужно ли использовать более новый алгоритм
        // или другую алгоритмическую стоимость
        // Например, если вы поменяете опции хеширования
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = pdo()->prepare('UPDATE `users` SET `password` = :password WHERE `username` = :username');
            $stmt->execute([
                'username' => $user['username'],
                'password' => $newHash,
            ]);
        }
        // меняем пароль
        $stmt = pdo()->prepare('UPDATE `users` SET `password` = :newpass WHERE `username` = :username');
        $stmt->execute([
            'username' => $user['username'],
            'newpass' => password_hash($_POST['new_pass'], PASSWORD_DEFAULT),
        ]);
        flash('Пароль успешно изменен!', 'success');
        header('Location: '. root(). '/map.php');
        die;
    }
    else {
        flash('Поля новый пароль и повторите пароль не совпадают', 'danger');
        header('Location: '. root(). '/index.php');
    }
}
else {
    flash('Неверный текущий пароль', 'danger');
    header('Location: '. root(). '/index.php');
}
?>