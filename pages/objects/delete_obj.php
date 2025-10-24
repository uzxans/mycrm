<?php
require_once __DIR__ . '/../check_post.php';
require_once __DIR__ . '/../check_auth.php';

// проверяем кто создает заявку
if (!$user['otf_edit']) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: '. root() .'/objects.php');
    die;
}
// если это ошибка
if ($_POST['id'] != 0) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: '. root() .'/objects');
    die;
}


// Проверьте, что запрос является POST запросом
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получите данные из запроса
    $postId = $_POST['post_id'];

    // Выполните запрос на удаление
    $stmt = pdo()->prepare('UPDATE `objects` SET `status_obj` = 1 WHERE `id` = :post_id');
    $stmt->execute(['post_id' => $postId]);

    // Проверьте, было ли успешно удаление
    if ($stmt->rowCount() > 0) {
        // Успешно удалено
        flash('Пост успешно удален!', 'success');
    } else {
        // Ошибка удаления
        flash('Ошибка удаления поста.', 'danger');
    }
}

// Перенаправьте пользователя на страницу, где отображаются посты (например, index.php или posts.php)
flash('Заявка успешно удалена!', 'danger');
header('Location: '. root() .'/objects');
exit();
?>
