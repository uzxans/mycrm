<?php
require_once __DIR__ . '/../../check_post.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $worker_id = intval($_POST['worker_id']);
    $user_id = intval($_POST['user_id']);
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {
        $stmt = pdo()->prepare("INSERT INTO comments (worker_id, user_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$worker_id, $user_id, $comment_text]);

        echo json_encode(["status" => "success", "id" => pdo()->lastInsertId(), "comment_text" => $comment_text]);
    } else {
        echo json_encode(["status" => "error", "message" => "Комментарий не может быть пустым"]);
    }
}
?>
