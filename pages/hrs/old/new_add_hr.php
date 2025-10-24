<?php
require_once __DIR__ . '/../check_auth.php';
if (!$user) {
    header('Location: '. root() .'/index.php');
    die;
}
?>
<?php
include_once __DIR__ . '/../templates/header.php';
?>

<h3>Новый кандидат</h3>
<p id="error"></p>
<div <?php if(isset($_GET['print'])) echo 'onload="window.print();"'?>>

    <?php if ($user['hr_view'] or $user['hr_edit'] or $user['hr_valid']) {

        try {
            // показ готовой заявки
            // получение данных заявки для вывода
            $stmt = pdo()->prepare("SELECT `date`, `creator`, `country`, `date_napomnit`, `tel`, `age`, `adres`, `metro`,  users.name AS username, `candidate`, objects.name AS objects, `department`, `form_dir`, hrapp.status, `sogl`, hrapp.notes FROM `hrapp` JOIN `objects` ON objects.id = hrapp.objects JOIN `users` ON users.id = hrapp.creator WHERE hrapp.id = :id");
            $stmt->execute(['id' => $url]);
            $row = $stmt->fetch();
            $sogl = explode('-',$row['sogl'])[$row['status']-1] ?? 0;
            $len = count(explode('-',$row['sogl']));
            $last = $len==$row['status'];
            // получение имен всех согласующих для вывода
            $stmt2 = pdo()->prepare("SELECT `coord_num`, users.name, `coord_date`, `status`, `note` FROM `hrcoord` JOIN `users` ON users.id = hrcoord.coord_name WHERE hrcoord.id = :id ORDER BY `coord_num`");
            $stmt2->execute(['id' => $url]);



            if ($user['dop_user'] == 1) {
                $stmt3 = pdo()->prepare("SELECT `id`, `meneger`, `name`, `dop_status` FROM `objects` WHERE `dop_status` = :user_id");
                $stmt3->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
                $stmt3->execute();
            } else {
                $stmt3 = pdo()->prepare("SELECT `id`, `meneger`, `name`, `status_obj` FROM `objects` WHERE `status_obj` = 0 AND `dop_status` = 1 ORDER BY `name` ASC");
                $stmt3->execute();
            }

// Кэшируем все результаты запроса в массиве
            $objects = $stmt3->fetchAll(PDO::FETCH_ASSOC);


            // получение имен пользователей для выбора согласующих
            $stmt4 = pdo()->prepare("SELECT `id`, `name` FROM `users` WHERE `hr_valid` = 1");
            $stmt4->execute();

            $stmt5 = pdo()->prepare("SELECT `id`, `comment`, `objects`, `meneger`, `date` FROM `hrhistory`");
            $stmt5->execute();
            // для новой заявки
            // получение всех наименований филиалов
            $stmt3 = pdo()->prepare("SELECT `id`, `name` FROM `objects` ORDER BY `name` ASC");
            $stmt3->execute();
            // получение имен пользователей для выбора согласующих
            $stmt4 = pdo()->prepare("SELECT `id`, `name` FROM `users` WHERE `hr_valid` = 1");
            $stmt4->execute();

            $stmt8 = pdo()->prepare("SELECT * FROM `metro` ORDER BY `name_metro`");
            $stmt8->execute();
            ?>
            <form method="post" action="ajax_add_hr.php" enctype="multipart/form-data" class="">
                <div class="row">
                    <div class="mb-3 col-12 col-md-4">
                        <label for="candidate" class="form-label">Кандидат Ф.И.О</label>
                        <input type="text" class="form-control" id="candidate" name="candidate" required>
                    </div>
                    <div class="mb-3 col-12 col-md-2">
                        <label for="department" class="form-label">Гражданство</label>
                        <input type="text" class="form-control" id="country" name="country">
                    </div>
                    <div class="mb-3 col-12 col-md-2">
                        <label for="department" class="form-label">Дата рождения</label>
                        <input type="text" class="form-control" id="ageInput"  name="age" placeholder="дд.мм.гг">
                    </div>
                    <div class="mb-3 col-12 col-md-4">
                        <label for="salary" class="form-label">Телефон номер</label>
                        <input type="tel" class="form-control" id="tel" name="tel">
                    </div>
                    <div class="mb-3 col-12 col-md-4">
                        <label for="salary" class="form-label">Адрес</label>
                        <input type="text" class="form-control" id="address" name="adres">
                    </div>
                    <div class="mb-3 col-12 col-md-4">
                        <label for="metro" class="form-label">Метро</label>
                        <select class="form-select js-example-basic-single" name="metro">
                            <?php while ($row8 = $stmt8->fetch()) { ?>
                                <option value="<?= $row8['id'] ?>">
                                    <?= $row8['name_metro'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3 col-12 col-md-4">
                        <label for="object" class="form-label">Объект</label>
                        <select class="form-select" id="object" name="object" required="">
                            <option value="">- не выбран -</option>
                                <?php foreach ($objects as $row3) { ?>
                                    <option value="<?=$row3['id']?>"
                                        <?php if ($row['objects'] == $row3['name']) echo 'selected';?>
                                    ><?=$row3['name']?></option>
                                <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3 col-12 col-md-4">
                        <label for="department" class="form-label">Профессия</label>
                        <input type="text" class="form-control" id="department" name="department" required>
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label for="filename" class="form-label">Фото для пропуска</label>
                            <input type="file" accept="image/jpeg" class="form-control" name="filename" value="">
                    </div>
                    <div class="mb-3 mt-3">
                        <div class="form-floating">
                            <textarea style="height:20dvh ;" class="form-control" name="notes" placeholder="Leave a comment here" id="floatingTextarea2" ><?php echo $row['notes'] ?? '';?></textarea>
                            <label for="floatingTextarea2">Комментарии</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
    <?php
        }
        catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "<a> Access Denied </a>";
    } ?>
</div>

<script>
    // Находим поле ввода по id
    const ageInput = document.getElementById('ageInput');

    // Добавляем обработчик события ввода текста
    ageInput.addEventListener('input', function(event) {
        // Получаем текущее значение поля ввода
        let value = event.target.value;

        // Удаляем все символы, кроме цифр
        value = value.replace(/\D/g, '');

        // Добавляем точки между группами цифр (дд.мм.гг)
        if (value.length > 4) {
            value = value.substring(0, 2) + '.' + value.substring(2, 4) + '.' + value.substring(4, 8);
        } else if (value.length > 2) {
            value = value.substring(0, 2) + '.' + value.substring(2, 4);
        }

        // Обновляем значение поля ввода
        event.target.value = value;
    });
</script>

<script>
    $(document).ready(function() {
        // Инициализация Select2 на элементе select
        $('#metro').select2();
    });
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('form').on('submit', function(event) {
            event.preventDefault(); // Отменяем стандартную отправку формы

            var formData = new FormData(this); // Создаем объект FormData для отправки данных формы

            $.ajax({
                url: $(this).attr('action'), // URL для отправки данных (в вашем случае это ajax_add_hr.php)
                type: 'POST',
                data: formData,
                contentType: false, // Указываем, что контент будет отправлен как multipart/form-data
                processData: false, // Отключаем обработку данных jQuery
                success: function(response) {
                    if (response.status === 'success') {
                        // Если сервер вернул успешный ответ, очищаем форму
                        $('form')[0].reset();
                        console.log(response);
                        window.location.href = '/hr-new/' + response.id;
                    } else {
                        $('#error').html('<div class="alert alert-warning" role="alert">' + response.message + '</div>');
                        console.error(response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error(textStatus, errorThrown);
                    alert("Произошла ошибка при отправке формы.");
                }
            });
        });
    });
</script>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>

