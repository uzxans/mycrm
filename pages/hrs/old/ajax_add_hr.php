<?php

require_once __DIR__ . '/../check_post.php';
require_once __DIR__ . '/../check_auth.php';

// проверяем кто создает заявку
if (!$user['hr_edit']) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: ' . root() . '/hrs.php');
    die;
}

// Убедимся, что это AJAX-запрос
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Получение данных из формы
        $candidate = $_POST['candidate'];
        $country = $_POST['country'];
        $age = $_POST['age'];
        $tel = preg_replace('/\D/', '', $_POST['tel']); // Убираем все символы кроме цифр
        $adres = $_POST['adres'];
        $metro = $_POST['metro'];
        $object = $_POST['objects'];
        $department = $_POST['department'];
        $notes = $_POST['notes'];
        $creator = $user['id'];
        $date= date('Y-m-d');
        $status= '1';

        $managerName = '';
        $stmt3 = pdo()->prepare("SELECT `id`, `meneger` FROM `objects`");
        $stmt3->execute();
        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            if ($object == $row3['id']) {
                $managerName = $row3['meneger'];
                break;
            }
        }

        $sogl = $managerName;

        // Проверяем дублирование номера телефона
        $stmt = pdo()->prepare("SELECT COUNT(*) AS count FROM `hrapp` WHERE `tel` = :tel");
        $stmt->execute([':tel' => $tel]);
        $rowCheck = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rowCheck['count'] > 0) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Этот номер телефона уже существует в базе данных.',
            ]);
            exit;
        }

        // Если ошибок нет, вставляем данные в базу
        $stmtInsert = pdo()->prepare("INSERT INTO `hrapp` (`candidate`, `country`, `age`, `tel`, `adres`, `metro`, `objects`, `department`, `notes`, `creator`, `date`, `sogl`) 
                                     VALUES (:candidate, :country, :age, :tel, :adres, :metro, :objects, :department, :notes, :creator, :date, :sogl)");
        $stmtInsert->execute([
            ':candidate' => $candidate,
            ':country' => $country,
            ':age' => $age,
            ':tel' => $tel,
            ':adres' => $adres,
            ':metro' => $metro,
            ':objects' => $object,
            ':department' => $department,
            ':notes' => $notes,
            ':creator' => $creator,
            ':date' => $date,
            ':sogl' => $sogl,
        ]);

        $id = pdo()->lastInsertId();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'id' => $id,
            'message' => 'Кандидат успешно добавлен!',
        ]);

// Подготовка данных из базы
        $stmt2 = pdo()->prepare('SELECT `name` FROM `objects` WHERE `id` = :id');
        $stmt2->execute(['id' => $_POST['objects']]);
        $obj = $stmt2->fetch(PDO::FETCH_ASSOC);

        if (!$obj) {
            error_log('Объект не найден в базе данных.');
            $obj['name'] = 'Неизвестно';
        }

        $stmt = pdo()->prepare('SELECT `name`, `username` FROM `users` WHERE `id` = :id');
        $stmt->execute(['id' => $sogl]);
        $firstcoord = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$firstcoord) {
            error_log('Менеджер объекта не найден в базе данных.');
            $firstcoord['name'] = 'Неизвестно';
        }

// Формирование сообщения
        $message = "Новый кандидат \n";
        $message .= "Кандидат: " . $candidate . "\n";
        $message .= "Дата рождения: " . $_POST['age'] . "\n";
        $message .= "Гражданство: " . $country . "\n";
        $message .= "Адрес: " . $adres. "\n";
        $message .= "HR: " . $user['name'] . "\n";
        $message .= "Профессия: " . $department . "\n";
        $message .= "Телефон номер: " . $_POST['tel'] . "\n";
        $message .= "Объект: " . $obj['name'] . "\n";
        $message .= "Менеджер объекта: "  .$firstcoord['name']. "\n";
        $message .= "Дата: " . date('d-m-Y') . "\n";
        $message .= "Статус: Соискатель \n";
        $message .= "Комментарии: " . $_POST['notes']. "\n";
        $message .= "URL: https://srmglobal.ru/hrs-new/" . $id . "\n";

// Ограничение длины сообщения
        if (strlen($message) > 4096) {
            $message = substr($message, 0, 4093) . '...';
        }

// Отправка сообщения в Telegram
        $telegramBotToken = '6085226555:AAE34W5z62U8lpsdlGhAwItdcr9Gdp4pAW4';
        $chatId = '-1001591451200';

        $url = "https://api.telegram.org/bot$telegramBotToken/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === FALSE) {
            error_log('Ошибка отправки сообщения в Telegram: ' . error_get_last()['message']);
        } else {
            $responseData = json_decode($response, true);
            if (!$responseData['ok']) {
                error_log('Telegram API вернул ошибку: ' . $responseData['description']);
            }
        }

        flash('Кандидат успешно добавлен', 'success');


    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Ошибка: ' . $e->getMessage(),
        ]);
    }
}
?>
