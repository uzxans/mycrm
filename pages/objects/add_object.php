<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
require_once __DIR__ . '/../check_post.php';
require_once __DIR__ . '/../check_auth.php';

// проверяем кто создает заявку
//if (!$user['ob_edit']) {
//    flash('У вас нет прав на это действие', 'danger');
//    header('Location: '. root() .'/objects.php');
//    die;
//}




// Получаем данные из формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $inn = $_POST['inn'];
    $tel_number = $_POST['tel_number'];
    $data = $_POST['data'];
    $data_end = $_POST['data_end'];
    $numpersonal = $_POST['numpersonal'] ?? '1';
    $addressinput = $_POST['addressinput'];
    $notes = $_POST['notes'];
    $men_name = $_POST['men_name'] ?? $user['id'];
    $dop_status = $_POST['dop_statuss'];
    // Далее вы можете выполнить запрос на обновление или вставку данных в базу данных
    // Например, если у вас есть поле "id" в таблице "objects", то вы можете сделать так:
    if ($id != 0 ) {
        $stmt = pdo()->prepare( "UPDATE `objects` SET 
                    `name` = ?, 
                    `inn` = ?,
                    `tel_number` = ?,
                    `data` = ?, 
                    `data_end` = ?,
                    `numpersonal` = ?,
                    `notes` = ?,
                    `meneger` = ?,
                    `adress_obj` = ?,
                    `dop_status` = ?
                    WHERE `id` = ?");
        $stmt->execute([
            $name,
            $inn,
            $tel_number,
            $data,
            $data_end,
            $numpersonal,
            $notes,
            $men_name,
            $addressinput,
            $dop_status,
            $id]);
        $id_object = $id;
        // Обработка загрузки файлов и текстовых данных
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $items = array();

            // Собираем данные из POST
            foreach ($_POST as $name => $val) {
                if (str_starts_with($name, 'item-')) {
                    $parts = explode('-', $name);
                    $itemId = $parts[1];
                    $fieldName = $parts[2];
                    $items[$itemId][$fieldName] = $val;
                }
            }

            // Обрабатываем файлы
            foreach ($_FILES as $name => $fileData) {
                if (str_starts_with($name, 'item-')) {
                    $parts = explode('-', $name);
                    $itemId = $parts[1];
                    $fieldName = $parts[2];

                    if ($fieldName === 'url_file' && $fileData['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = 'uploads/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
                        $newFilename = uniqid() . 'objects' . $extension;
                        $destination = $uploadDir . $newFilename;

                        if (move_uploaded_file($fileData['tmp_name'], $destination)) {
                            $items[$itemId]['url_file'] = $destination;
                            if (empty($items[$itemId]['name_file'])) {
                                $items[$itemId]['name_file'] = $fileData['name'];
                            }
                        }
                    }
                }
            }

            // Записываем в базу данных (исправленная версия)
            if (!empty($items)) {
                // Лучше использовать подготовленные выражения для каждого элемента
                $stmt = pdo()->prepare('INSERT INTO `file_object` (`id_object`, `name_file`, `url_file`) VALUES (:id_object, :name_file, :url_file)');

                foreach ($items as $item) {
                    if (!empty($item['name_file']) && !empty($item['url_file'])) {
                        try {
                            $stmt->execute([
                                ':id_object' => $id_object,
                                ':name_file' => $item['name_file'],
                                ':url_file' => $item['url_file']
                            ]);
                        } catch (PDOException $e) {
                            // Обработка ошибки
                            error_log("Database error: " . $e->getMessage());
                            // Можно добавить вывод сообщения пользователю
                        }
                    }
                }
            }
        }

        flash('Заявка успешно обновлено!', 'success');
        header('Location: '. root() .'/objects/' . $id);
    }  else {
        $stmt = pdo()->prepare("INSERT INTO `objects` (`name`, `inn`, `tel_number`, `data`, `data_end`, `meneger`, `numpersonal`, `adress_obj`, `notes`, `dop_status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $inn, $tel_number, $data, $data_end, $men_name, $numpersonal, $addressinput, $notes, $dop_status]);

        $pdo = pdo();
        $id_object = $pdo->lastInsertId();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $items = array();

            // Собираем данные из POST
            foreach ($_POST as $name => $val) {
                if (str_starts_with($name, 'item-')) {
                    $parts = explode('-', $name);
                    $itemId = $parts[1];
                    $fieldName = $parts[2];
                    $items[$itemId][$fieldName] = $val;
                }
            }

            // Обрабатываем файлы
            foreach ($_FILES as $name => $fileData) {
                if (str_starts_with($name, 'item-')) {
                    $parts = explode('-', $name);
                    $itemId = $parts[1];
                    $fieldName = $parts[2];

                    if ($fieldName === 'url_file' && $fileData['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = 'uploads/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
                        $newFilename = uniqid() . 'objects' . $extension;
                        $destination = $uploadDir . $newFilename;

                        if (move_uploaded_file($fileData['tmp_name'], $destination)) {
                            $items[$itemId]['url_file'] = $destination;
                            if (empty($items[$itemId]['name_file'])) {
                                $items[$itemId]['name_file'] = $fileData['name'];
                            }
                        }
                    }
                }
            }

            // Записываем в базу данных (исправленная версия)
            if (!empty($items)) {
                // Лучше использовать подготовленные выражения для каждого элемента
                $stmt = pdo()->prepare('INSERT INTO `file_object` (`id_object`, `name_file`, `url_file`) VALUES (:id_object, :name_file, :url_file)');

                foreach ($items as $item) {
                    if (!empty($item['name_file']) && !empty($item['url_file'])) {
                        try {
                            $stmt->execute([
                                ':id_object' => $id_object,
                                ':name_file' => $item['name_file'],
                                ':url_file' => $item['url_file']
                            ]);
                        } catch (PDOException $e) {
                            // Обработка ошибки
                            error_log("Database error: " . $e->getMessage());
                            // Можно добавить вывод сообщения пользователю
                        }
                    }
                }
            }
        }
        
        // Выполнение SQL-запроса и проверка результата
        if ($stmt->rowCount() > 0) {
            // Успешно сохранено
            flash('Заявка успешно добавлена!', 'success');
            header('Location: '. root() .'/objects.php');
            die; // Добавьте эту строку, чтобы остановить дальнейшее выполнение скрипта
        } else {
            // Ошибка сохранения
            echo 'Ошибка сохранения данных';
        }
    }
}
?>