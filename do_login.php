<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/check_post.php';
require_once __DIR__.'/boot.php';
require_once __DIR__.'/check_db.php';

session_start();

// Проверяем, был ли отправлен запрос на вход
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем учетные данные пользователя (здесь может быть ваша проверка)
    // проверяем наличие пользователя с указанным юзернеймом
    $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `username` = :username");
    $stmt->execute(['username' => $_POST['username']]);
    if (!$stmt->rowCount()) {
        flash('Пользователь с такими данными не зарегистрирован', 'danger');
        header('Location: '. root(). '/');
        die;
    }
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // проверяем пароль
    if (password_verify($_POST['password'], $user['password'])) {
        // Если учетные данные верны

        // Проверяем, необходимо ли обновить алгоритм хеширования
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = pdo()->prepare('UPDATE `users` SET `password` = :password WHERE `username` = :username');
            $stmt->execute([
                'username' => $_POST['username'],
                'password' => $newHash,
            ]);
        }

        // Генерируем уникальный токен для пользователя
        $token = bin2hex(random_bytes(16)); // Генерируем случайную последовательность байтов

        // Если отмечен флажок "Запомнить меня"
        if ($_POST['remember_me']) {
            // Сохраняем токен в базе данных для данного пользователя
            $stmt = pdo()->prepare('UPDATE `users` SET `remember_token` = :token WHERE `username` = :username');
            $stmt->execute([
                'username' => $_POST['username'],
                'token' => $token,
            ]);
            // Устанавливаем токен как куки на стороне пользователя
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
        }

        // Устанавливаем сессию для аутентификации
        $_SESSION['user_id'] = $user['id'];

        // Перенаправляем пользователя в личный кабинет
        header('Location: '. root(). '/profile');
        exit();
    } else {
        // Если пароль неверный, отправляем пользователя обратно на страницу входа с сообщением об ошибке
        flash('Неверный пароль', 'danger');
        header('Location: '. root(). '/');
        exit();
    }
}
?>
