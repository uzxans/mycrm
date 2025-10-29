<?php
// pages/hrs/load.php
// POST JSON: { offset, limit, filters }
try {
    header('Content-Type: application/json; charset=utf-8');

    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
    if (!is_array($input)) {
        // fallback to POST form data
        $input = $_POST;
    }

    $offset = isset($input['offset']) ? (int)$input['offset'] : 0;
    $limit = isset($input['limit']) ? (int)$input['limit'] : 30;
    if ($limit <= 0) $limit = 30;
    if ($limit > 200) $limit = 200; // safety cap

    $filters = isset($input['filters']) && is_array($input['filters']) ? $input['filters'] : [];

    $whereParts = [];
    $params = [];

    // apply filters only when any filter supplied
    if (!empty($filters)) {
        // status - can be array or single
        if (!empty($filters['status'])) {
            $statusVals = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            // remove empty or 'Все' placeholders
            $statusVals = array_values(array_filter($statusVals, function($v){
                return $v !== '' && $v !== null && $v !== 'Все' && $v !== 'all' && $v !== 'All';
            }));
            if (!empty($statusVals)) {
                // ensure ints
                $placeholders = [];
                foreach ($statusVals as $sv) {
                    $placeholders[] = '?';
                    $params[] = (int)$sv;
                }
                $whereParts[] = 'h.status IN (' . implode(',', $placeholders) . ')';
            }
        }

        // object id
        if (isset($filters['object']) && $filters['object'] !== '') {
            $whereParts[] = 'h.object = ?';
            $params[] = (int)$filters['object'];
        }

        // hr (creator)
        if (!empty($filters['hr'])) {
            $whereParts[] = 'h.creator = ?';
            $params[] = (int)$filters['hr'];
        }

        // name (candidate) - substring search
        if (!empty($filters['name'])) {
            $whereParts[] = 'h.candidate LIKE ?';
            $params[] = '%' . $filters['name'] . '%';
        }

        // metro - try match metro.name_metro
        if (!empty($filters['metro'])) {
            $whereParts[] = 'm.name_metro LIKE ?';
            $params[] = '%' . $filters['metro'] . '%';
        }

        // daterange - try to parse "YYYY-MM-DD to YYYY-MM-DD" or "YYYY-MM-DD - YYYY-MM-DD" or single date
        if (!empty($filters['daterange'])) {
            $dr = trim($filters['daterange']);
            // split by common separators
            $parts = preg_split('/\\s*(?:to|–|—|-|\\u2013|\\u2014)\\s*/iu', $dr);
            $parts = array_map('trim', array_filter($parts, 'strlen'));
            if (count($parts) >= 2) {
                // use first two parts
                $start = $parts[0];
                $end = $parts[1];
                // simple validation YYYY-MM-DD
                if (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $start) && preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $end)) {
                    $whereParts[] = 'DATE(h.date) BETWEEN ? AND ?';
                    $params[] = $start;
                    $params[] = $end;
                }
            } else {
                // single date
                if (preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $dr)) {
                    $whereParts[] = 'DATE(h.date) = ?';
                    $params[] = $dr;
                }
            }
        }
    }

    // Build SQL
    $sql = "SELECT \
                h.*,\
                o.name AS object_name,\
                m.name_metro AS metro_name,\
                uc.name AS creator_name,\
                us.name AS sogl_name\
            FROM hrapp AS h\
            LEFT JOIN object AS o ON o.id = h.object\
            LEFT JOIN metro AS m ON m.id = h.metro\
            LEFT JOIN users AS uc ON uc.id = h.creator\
            LEFT JOIN users AS us ON us.id = h.sogl";

    if (!empty($whereParts)) {
        $sql .= ' WHERE ' . implode(' AND ', $whereParts);
    }

    $sql .= ' ORDER BY h.id DESC';

    // LIMIT/OFFSET: cast to int and append as safe literals
    $offset = max(0, $offset);
    $limit = max(1, $limit);
    $sql .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);

    // prepare and execute
    $stmt = pdo()->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send rows
    echo json_encode(['rows' => $rows, 'count' => count($rows)]);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>