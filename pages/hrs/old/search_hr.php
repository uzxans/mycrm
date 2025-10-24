<?php
require_once __DIR__.'/check_auth.php';

// Получаем данные из запроса
$requestData = json_decode(file_get_contents('php://input'), true);
$search = $requestData['search'] ?? '';

// Убираем пробелы и спецсимволы
function normalizePhone($input) {
    return preg_replace('/[^0-9]/', '', $input); // Оставляем только цифры
}

// Функция для проверки, является ли запрос числовым
function isPhone($search) {
    return preg_match('/^[0-9\-\+\(\)\s]*$/', $search); // Проверка на телефон (цифры, пробелы, дефисы, скобки)
}

if (!empty($search)) {
    $normalizedSearch = normalizePhone($search);
    $lowercaseSearch = mb_strtolower(trim($search), 'UTF-8'); // Убираем пробелы по краям и преобразуем в нижний регистр

    if (isPhone($search)) {
        // Если это номер телефона, ищем по полю `tel`
        $stmt = pdo()->prepare("
            SELECT * 
            FROM `hrapp` 
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(`tel`, ' ', ''), '-', ''), '(', ''), ')', '') LIKE :normalized
        ");
        $stmt->bindValue(':normalized', '%' . $normalizedSearch . '%', PDO::PARAM_STR); // Поиск по номеру
    } else {
        // Иначе ищем по имени кандидата
        $stmt = pdo()->prepare("
            SELECT `candidate`, `tel`
            FROM `hrapp`
            WHERE LOWER(`candidate`) LIKE :lowercaseSearch
        ");
        $stmt->bindValue(':lowercaseSearch', '%' . $lowercaseSearch . '%', PDO::PARAM_STR); // Поиск по имени (без учета регистра)
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} else {
    echo json_encode([]); // Если пустой запрос, возвращаем пустой массив
}
?>
