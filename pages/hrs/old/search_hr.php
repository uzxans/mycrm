<?php
require_once __DIR__.'/check_auth.php';

// �������� ������ �� �������
$requestData = json_decode(file_get_contents('php://input'), true);
$search = $requestData['search'] ?? '';

// ������� ������� � �����������
function normalizePhone($input) {
    return preg_replace('/[^0-9]/', '', $input); // ��������� ������ �����
}

// ������� ��� ��������, �������� �� ������ ��������
function isPhone($search) {
    return preg_match('/^[0-9\-\+\(\)\s]*$/', $search); // �������� �� ������� (�����, �������, ������, ������)
}

if (!empty($search)) {
    $normalizedSearch = normalizePhone($search);
    $lowercaseSearch = mb_strtolower(trim($search), 'UTF-8'); // ������� ������� �� ����� � ����������� � ������ �������

    if (isPhone($search)) {
        // ���� ��� ����� ��������, ���� �� ���� `tel`
        $stmt = pdo()->prepare("
            SELECT * 
            FROM `hrapp` 
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(`tel`, ' ', ''), '-', ''), '(', ''), ')', '') LIKE :normalized
        ");
        $stmt->bindValue(':normalized', '%' . $normalizedSearch . '%', PDO::PARAM_STR); // ����� �� ������
    } else {
        // ����� ���� �� ����� ���������
        $stmt = pdo()->prepare("
            SELECT `candidate`, `tel`
            FROM `hrapp`
            WHERE LOWER(`candidate`) LIKE :lowercaseSearch
        ");
        $stmt->bindValue(':lowercaseSearch', '%' . $lowercaseSearch . '%', PDO::PARAM_STR); // ����� �� ����� (��� ����� ��������)
    }

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} else {
    echo json_encode([]); // ���� ������ ������, ���������� ������ ������
}
?>
