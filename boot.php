<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$two_months = 60 * 60 * 24 * 30 * 2;
session_set_cookie_params($two_months);
// Инициализируем сессию
session_start();

// Простой способ сделать глобально доступным подключение в БД
function pdo(): PDO
{
    static $pdo;

    if (!$pdo) {
        $config = include __DIR__.'/config.php';
        // Подключение к БД
        $dsn = 'mysql:dbname='.$config['db_name'].';host='.$config['db_host'].';charset=utf8mb4';
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}

function flash(?string $message = null, ?string $color = null)
{
    if ($message) {
        $_SESSION['flash'] = $message;
        $_SESSION['flash_color'] = $color;
    } else {
        if (!empty($_SESSION['flash'])) { ?>
            <div class="alert alert-<?php echo $_SESSION['flash_color'] ?? 'dark' ?> alert-dismissible fade show" role="alert">
                <strong>Вниание !</strong> <?=$_SESSION['flash']?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php }
        unset($_SESSION['flash']);
    }
}
function check_auth(): bool
{
    return !!($_SESSION['user_id'] ?? false);
}
function check_post(): bool
{
    return (count($_POST)==0);
}
function root() {
    return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'].'/new';
}
function sql_edit($table, $args, &$execute)
{
    $prepare='UPDATE `'. $table . '` SET';
    foreach ($args as $name => &$value) {
        if ($name != 'password') {
            if ($name != 'id') {
                $prepare = $prepare . ' `' . $name . '` = :' . $name . ',';
            }
            if ($value == 'on') $value = 1; //для чекбокса
            $execute[$name] = $value;
        }
    }
    $prepare = substr($prepare,0,-1); // уаляем последний символ
    $prepare = $prepare . ' WHERE `id` = :id';
    return $prepare;
}
function sql_add($table, $args, &$execute)
{
    $prepare = 'INSERT INTO `'. $table . '` (';
    $temp = 'VALUES (';
    foreach ($args as $name => &$value) {
        if ($name != 'id' and !str_starts_with($name, 'sogl-')) {
            $prepare = $prepare . '`' . $name . '`, ';
            $temp = $temp . ':' . $name . ', ';
            if ($value == 'on') $value = 1; //для чекбокса
            if ($name == 'password') $value = password_hash($value, PASSWORD_DEFAULT);
            $execute[$name] = $value;
        }
    }
    $prepare = substr($prepare,0,-2); // удаляем запятую
    $temp = substr($temp,0,-2); // удаляем запятую
    $prepare = $prepare . ') ' . $temp . ')';
    return $prepare;
}

// Beautiful print_r;
function beautiful_print_r($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Beautiful var_dump;
function beautiful_var_dump($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}


function formatPhoneNumber($phoneNumber) {
    // Убираем все, кроме цифр
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    // Проверяем длину номера
    if (strlen($phoneNumber) == 11) {
        // Форматируем номер в (XXX) XXX-XXXX
        $formattedNumber = sprintf("+7(%s) %s-%s-%s",
            substr($phoneNumber, 1, 3),
            substr($phoneNumber, 4, 3),
            substr($phoneNumber, 7, 2),
            substr($phoneNumber, 9, 2)
        );
        return $formattedNumber;
    } else {
        // Возвращаем оригинальный номер, если он не имеет длину 10 цифр
        return $phoneNumber;
    }
}



// Функция для уменьшения размера изображения
function resizeImage($file, $max_width, $max_height) {
    list($orig_width, $orig_height) = getimagesize($file);
    $width = $orig_width;
    $height = $orig_height;

    // Узнаем, нужно ли изменять размеры
    if ($width > $max_width || $height > $max_height) {
        $ratio = $orig_width / $orig_height;

        if ($max_width / $max_height > $ratio) {
            $max_width = $max_height * $ratio;
        } else {
            $max_height = $max_width / $ratio;
        }

        $image_p = imagecreatetruecolor($max_width, $max_height);
        $image = imagecreatefromjpeg($file);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $max_width, $max_height, $orig_width, $orig_height);

        // Сохраняем уменьшенное изображение
        imagejpeg($image_p, $file, 90);
    }
}


function dataFormat($data){
    // Устанавливаем локаль на русский язык
    setlocale(LC_TIME, 'ru_RU.UTF-8');
    // Предполагая, что $row["date"] содержит строку даты в формате "Y-m-d" (например, "2023-10-25")
    // Преобразуем дату в удобочитаемый формат
    $readableFormat = strftime("%e %B %Y", strtotime($data));
    echo $readableFormat;
}


function poddomen()
{
    echo '/new';
}

function probels($data)
{

// Удаляет все пробелы (пробел)
    $text = str_replace(' ', '', $data);

// Удаляет все пробельные символы (пробелы, табы, переносы строк)
    $text_regex = preg_replace('/\s+/', '', $data);
echo $text_regex;
}

function removeAllWhitespace($data)
{
    return preg_replace('/\s+/', '', $data);
}