<?php
function pdo(): PDO
{
    static $pdo;

    if (!$pdo) {
        $config = include __DIR__ . '/../../config.php';
        $dsn = 'mysql:dbname='.$config['db_name'].';host='.$config['db_host'].';charset=utf8mb4';
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}

try {
    $worker_id = intval($_GET['worker_id'] ?? 0);

    error_log("Worker ID: " . $worker_id); // Отладочный вывод

    $pdo = pdo();

    $stmt = $pdo->prepare("SELECT c.id, c.comment_text, c.created_at, u.name FROM comments c 
                       JOIN users u ON c.user_id = u.id WHERE c.worker_id = ? ORDER BY c.created_at ASC");
    $stmt->execute([$worker_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($comments);
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>
