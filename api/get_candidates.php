<?php
require_once '../boot.php';
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Разрешаем CORS если нужно
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Получаем метод запроса
$method = $_SERVER['REQUEST_METHOD'];

// Получаем данные из URL (для GET, PATCH, DELETE запросов)
$requestUri = $_SERVER['REQUEST_URI'];
$uriParts = explode('/', $requestUri);
$candidateId = end($uriParts);

// Обрабатываем разные методы
switch ($method) {
    case 'GET':
        getCandidate($candidateId);
        break;
    case 'POST':
        addComment();
        break;
    case 'PATCH':
        updateCandidate();
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

// ====================
// Получение данных кандидата
// ====================
function getCandidate($id) {
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid candidate ID']);
        return;
    }

    try {
        $pdo = pdo();

        // Основные данные кандидата
        $stmt = $pdo->prepare('
            SELECT 
                h.*,
                o.name as object_name,
                m.name as metro_name,
                hr.name as hr_name
            FROM `hrapp` h
            LEFT JOIN `objects` o ON h.object = o.id
            LEFT JOIN `metro` m ON h.metro = m.id  
            LEFT JOIN `hr` hr ON h.hr = hr.id
            WHERE h.`id` = ?
        ');
        $stmt->execute([$id]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$candidate) {
            http_response_code(404);
            echo json_encode(['error' => 'Candidate not found']);
            return;
        }

        // Комментарии кандидата
        $commentsStmt = $pdo->prepare('
            SELECT 
                c.*,
                hr.name as author_name
            FROM `comments` c
            LEFT JOIN `hr` hr ON c.author_id = hr.id
            WHERE c.`candidate_id` = ?
            ORDER BY c.created_at DESC
        ');
        $commentsStmt->execute([$id]);
        $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Формируем ответ
        $response = [
            'id' => (int)$candidate['id'],
            'full_name' => $candidate['name'] ?? '',
            'status' => (int)$candidate['status'],
            'profession' => $candidate['profession'] ?? '',
            'phone' => $candidate['phone'] ?? '',
            'citizenship' => $candidate['citizenship'] ?? '',
            'birth_date' => $candidate['birth_date'] ?? '',
            'inn' => $candidate['inn'] ?? '',
            'end_date' => $candidate['end_date'] ?? '',
            'object_id' => (int)$candidate['object'],
            'object_name' => $candidate['object_name'] ?? '',
            'object_manager' => $candidate['object_manager'] ?? '',
            'address' => $candidate['address'] ?? '',
            'metro_id' => (int)$candidate['metro'],
            'metro_name' => $candidate['metro_name'] ?? '',
            'hr_id' => (int)$candidate['hr'],
            'hr_name' => $candidate['hr_name'] ?? '',
            'photo' => $candidate['photo'] ?? './accets/img/hr_info_img.png',
            'comments' => $comments
        ];

        echo json_encode($response);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// ====================
// Добавление комментария
// ====================
function addComment() {
    // Получаем JSON данные
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['candidate_id']) || !isset($input['text'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    try {
        $pdo = pdo();

        // В реальном приложении author_id должен браться из сессии
        $author_id = 1; // Заглушка - заменить на реальный ID пользователя

        $stmt = $pdo->prepare('
            INSERT INTO `comments` (candidate_id, author_id, text, created_at)
            VALUES (?, ?, ?, NOW())
        ');

        $success = $stmt->execute([
            $input['candidate_id'],
            $author_id,
            $input['text']
        ]);

        if ($success) {
            echo json_encode(['success' => true, 'comment_id' => $pdo->lastInsertId()]);
        } else {
            throw new Exception('Failed to insert comment');
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// ====================
// Обновление данных кандидата
// ====================
function updateCandidate() {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['field']) || !isset($input['value'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing field or value']);
        return;
    }

    // Получаем ID из URL
    $requestUri = $_SERVER['REQUEST_URI'];
    $uriParts = explode('/', $requestUri);
    $candidateId = end($uriParts);

    if (!$candidateId || !is_numeric($candidateId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid candidate ID']);
        return;
    }

    // Разрешенные поля для обновления
    $allowedFields = [
        'full_name', 'status', 'profession', 'phone', 'citizenship',
        'birth_date', 'inn', 'end_date', 'object_id', 'object_manager',
        'address', 'metro_id'
    ];

    $field = $input['field'];
    $value = $input['value'];

    if (!in_array($field, $allowedFields)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid field']);
        return;
    }

    // Маппинг полей (если имена в JS и БД разные)
    $fieldMapping = [
        'full_name' => 'name',
        'object_id' => 'object',
        'metro_id' => 'metro'
    ];

    $dbField = $fieldMapping[$field] ?? $field;

    try {
        $pdo = pdo();
        $stmt = $pdo->prepare("UPDATE `hrapp` SET `$dbField` = ? WHERE `id` = ?");
        $success = $stmt->execute([$value, $candidateId]);

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Failed to update candidate');
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>