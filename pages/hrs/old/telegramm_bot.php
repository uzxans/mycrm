<?php
require_once __DIR__ . '/../check_auth.php';

$stmt = pdo()->prepare('SELECT `id`, `name`, `username` FROM `users` WHERE `id` = :id');
$stmt->execute(['id'=>$sogl]);
$firstcoord = $stmt->fetch(PDO::FETCH_ASSOC);


$stmt2 = pdo()->prepare('SELECT `name` FROM `objects` WHERE `id` = :id');
$stmt2->execute(['id'=>$_POST['objects']]);
$obj = $stmt2->fetch(PDO::FETCH_ASSOC);


$stmt = pdo()->prepare('SELECT * FROM `metro` WHERE `id` = :id');
$stmt->execute(['id'=>$_POST['metro']]);
$metroo = $stmt->fetch(PDO::FETCH_ASSOC);


if ($status == 1){
    $status= 'Соискатель';
} elseif ($status == -1){
    $status= 'Отказ';
}elseif ($status == 0){
    $status= 'Уволено';
}elseif ($status == 5){
    $status= 'Напомнить';
}elseif ($status == 6){
    $status= 'Резерв';
}elseif ($status == 7){
    $status = 'Не дозвонился';
}elseif ($status == 100){
    $status = 'Работает';
}



// Формирование текста сообщения
$message .= "Кандидат: " . $_POST['candidate'] . "\n";
$message .= "Дата рождения: " . $_POST['age'] . "\n";
$message .= "Гражданство: " . $_POST['country'] . "\n";
$message .= "Адрес: " . $_POST['adres'] . "\n";
$message .= "Метро: " . $metroo['name_metro'] . "\n";
$message .= "HR: " . $user['name'] . "\n";
$message .= "Профессия: " . $_POST['department'] . "\n";
$message .= "Телефон номер: " . $tel. "\n";
$message .= "Объект: " . $obj['name'] . "\n";
$message .= "Менеджер объекта: "  .$firstcoord['name']. "\n";
$message .= "Дата: " . date('d-m-Y') . "\n";
$message .= "Статус: " . $status. "\n";
$message .= "Комментарии: " . $_POST['notes']. "\n";
$message .= "URL: https://srmglobal.ru/hrs-new/" . $id . "\n";

// Отправка сообщения через Telegram бота
$telegramApiUrl = "https://api.telegram.org/bot" . $telegramBotToken . "/sendMessage";
$telegramApiParams = [
    'chat_id' => $telegramChatId,
    'text' => $message,
];

// Используйте соответствующую библиотеку или функцию для отправки запроса на Telegram API
// Ниже приведен пример с использованием функции curl
$curl = curl_init($telegramApiUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $telegramApiParams);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

// Проверка ответа Telegram API
if (!$response) {
    // Обработка ошибки отправки сообщения
    echo "Ошибка отправки сообщения через Telegram";
} else {
    // Успешно отправлено
    echo "Сообщение успешно отправлено через Telegram";
}
?>
