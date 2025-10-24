<?php
require_once __DIR__ . '/../../check_post.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_id = intval($_POST['comment_id']);
    $new_text = trim($_POST['comment_text']);

    if (!empty($new_text)) {
        $stmt = pdo()->prepare("UPDATE comments SET comment_text = ? WHERE id = ?");
        $stmt->execute([$new_text, $comment_id]);

        echo json_encode(["status" => "success", "comment_text" => $new_text]);
    } else {
        echo json_encode(["status" => "error", "message" => "Комментарий не может быть пустым"]);
    }
}
?>
