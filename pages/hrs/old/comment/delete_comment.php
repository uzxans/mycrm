<?php
require_once __DIR__ . '/../../check_post.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_id = intval($_POST['comment_id']);

    $stmt = pdo()->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);

    echo json_encode(["status" => "success"]);
}
?>
