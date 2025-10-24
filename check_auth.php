<?php
require_once __DIR__.'/boot.php';

$user = null;

// Проверяем, если сессия уже установлена
if (check_auth()) {
    // Получим данные пользователя по сохранённому идентификатору
    $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif (isset($_COOKIE['remember_token'])) {
    // Если сессия не установлена, но установлен куки с токеном "Запомнить меня"
    $token = $_COOKIE['remember_token'];

    // Проверяем наличие пользователя с данным токеном
    $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `remember_token` = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Если пользователь найден, устанавливаем сессию для аутентификации
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
    }
}
?>
