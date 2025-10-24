<?php
require_once __DIR__ . '/../check_post.php';
require_once __DIR__ . '/../check_auth.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// проверяем кто создает заявку
if (!$user['hr_edit']) {
    flash('У вас нет прав на это действие', 'danger');
    header('Location: '. root() .'/hrs.php');
    die;
}

    $id = $_POST['id'];

    if ($_FILES["filename"]["error"] === UPLOAD_ERR_OK) {
        $name_dir = '';
        // обработка запроса $_POST загрузка файлов
        if ($_FILES && $_FILES["filename"]["error"] == UPLOAD_ERR_OK) {
            $fileExtension = pathinfo($_FILES["filename"]["name"], PATHINFO_EXTENSION);
            $name_dir = '../docs/hrs/' . $id . '.' . $fileExtension;
            move_uploaded_file($_FILES["filename"]["tmp_name"], $name_dir);
        } else {
            flash('Ошибка загрузки файла', 'danger');
            header('Location: ' . root() . '/hrs-new/' . $id);
        }
    } else {
        $name_dir = $_POST['oldfilename'];
    }

    $status = $_POST['newstatus'];

    if (empty($status)) {
        $status ="1";
    }
    $date_napomnit= $_POST['date_napomnit'];
    $notes = $_POST['notes'];
    $object1 = $_POST['objects'];
    $object = preg_replace('/\s+/', '', $object1);
    $tel = preg_replace('/\D/', '', $_POST['tel']); // Убираем все символы кроме цифр
    $country = $_POST['country'];
    $age = $_POST['age'];
    $adres = $_POST['adres'];
    $metro = $_POST['metro'];
    $data_update = date('Y-m-d');
    $inn_patent = $_POST['inn_patent'] ?? '';
    $date_patent = $_POST['date_patent'] ?? '';

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
    $candidate = $_POST['candidate'];
    $stmt = pdo()->prepare("UPDATE `hrapp` SET 
                    `candidate` = ?,
                    `objects` = ?,
                    `form_dir` = ?,
                    `status` = ?, 
                    `date_napomnit` = ?, 
                    `notes` = ?,
                    `sogl` = ?,
                    `country` = ?,
                    `tel` = ?,
                    `age` = ?,
                    `adres` = ?,
                    `metro` = ?,
                    `date` = ?,
                    `date_patent` = ?,
                    `inn_patent` = ?
                    WHERE `id` = ?");
    $stmt->execute([
        $candidate,
        $object,
        $name_dir,
        $status,
        $date_napomnit,
        $notes,
        $sogl,
        $country,
        $tel,
        $age,
        $adres,
        $metro,
        $data_update,
        $date_patent,
        $inn_patent,
        $id
    ]);
    flash('Заявка успешно обновлено!', 'success');
    header('Location: '. root() .'/hrs-new/'.$id);
    if (!$user['dop_user']==1) {
        require_once __DIR__ . '/telegramm_bot.php';
    }
?>