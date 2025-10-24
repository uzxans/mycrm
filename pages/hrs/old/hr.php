<?php
require_once __DIR__ . '/../check_auth.php';
if (!$user) {
    header('Location: '. root() .'/index.php');
    die;
}
$url = basename($_SERVER['REQUEST_URI'], "?print"); // получаем ID заявки из URL
if ($url != 0) {
    $stmt = pdo()->prepare("SELECT `candidate` FROM `hrapp` WHERE `id` = :id");
    $stmt->execute(['id' => $url]);
    if (!$stmt->rowCount()) {
        flash('Заявка не найдена', 'danger');
        header('Location: '. root() .'/hrs.php');
        die;
    }
}

// получение имен всех статусов для вывода
$stmt1 = pdo()->prepare('SELECT id, name_status, color FROM status_hr');
$stmt1 -> execute();
?>
<?php
include_once __DIR__ . '/../templates/header.php';
?>
    <!-- Подключаем Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-dropdown{
            background-color: #212529;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #a0a4a8;}
        .select2-container--default .select2-selection--single {
            background-color: #212529;}
        .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #212529;
        }
    </style>

<div <?php if(isset($_GET['print'])) echo 'onload="window.print();"'?>>
    <div class="row mb-2">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mt-3">
                    <li class="breadcrumb-item"><a href="#" onclick="goBack()">Назад</a></li>
                    <li class="breadcrumb-item active" aria-current="page" id="title_page">Заявка</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6">
            <a class="btn btn-outline-primary" href="/hr/new_add_hr">Добавить работника</a>
        </div>
    </div>
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
            <?php if ($url != 0) {
                if ($row['status']==0) { ?>
                    <td><span class="btn btn-danger">Уволен</span></td>
                <?php } elseif ($row['status']==-1) { ?>
                    <td><span class="btn btn-danger">Отменено создателем</span></td>
                <?php }
                elseif ($row['status']==6) { ?>
                    <p class="btn btn-danger">Резерв</p>
                    <?php } elseif ($row['status']==-1) { ?>
                    <td><span class="badge bg-success">Черный список</span></td>
                    <?php } elseif  ($row['status']==101) { ?>
                    <td><span class="badge bg-success">Завершено</span></td>
                <?php } elseif (explode('-',$row['sogl'])[$row['status']-1]==$user['id']) { ?>
                    <td><span class="badge bg-warning">У вас</span></td>
                <?php }
            }
            ?>

            <?php if (isset($sogl) and $sogl==$user['id']) { ?>

            <?php } ?>
            <?php if ($row['status']  !==  5){?>
            <style>
                .add_new_box{
                    display:none;
                }
                .active{
                    display: block;
                }
            </style>
            <?php } ?>
            <form method="post" action="do_add_hr.php" enctype="multipart/form-data" class="">
                <input type="hidden" id="id" name="id" value="<?php echo $url; ?>">
                <?php if ($url!=0 ){ ?>
                    <div class="row">
                        <?php if ($user['tdf_edit']){ ?>
                        <div class="col-md-3 col-12">
                            <label for="status" class="form-label">Статус</label>
                            <select id="pay_type" class="mb-5 form-select
                            <?php
                            if ($row['status']==100) echo 'bg-success text-white';
                            if ($row['status']==5) echo 'bg-info';
                            if ($row['status']==6) echo 'bg-secondary text-white';
                            if ($row['status']==-1) echo 'bg-danger text-white';
                            if ($row['status']==0) echo 'bg-danger text-white';
                            if ($row['status']==7) echo 'bg-warning';
                            if ($row['status']==1) echo 'bg-primary text-white';
                            if ($row['status']==10) echo 'bg-primary text-white';
                            if ($row['status']==8) echo 'bg-black';
                            ?>" name="newstatus">
                                <?php if (explode('-',$row['sogl'])[$row['status']-1]==$user['id']) { ?>
                                    <option selected value="1">У вас</option>
                                <?php } ?>
                                <option <?php if ($row['status']==1) echo "selected"; ?>  value="1">Соискатель</option>
                                <option <?php if ($row['status']==-1) echo "selected"; ?>  value="-1">Отказ</option>
                                <option <?php if ($row['status']==100) echo "selected"; ?> value="100">Согласова́ть</option>
                                <option <?php if ($row['status']==7) echo "selected"; ?> value="7">Не дозвон</option>
                                <option <?php if ($row['status']==5) echo "selected"; ?>  value="5">Напомнить</option>
                                <option <?php if ($row['status']==6) echo "selected"; ?>  value="6">Резерв</option>
                                <option <?php if ($row['status']==0) echo "selected"; ?>  value="0">Уволить</option>
                                <option <?php if ($row['status']==8) echo "selected"; ?>  value="8">Черный список</option>
                                <option <?php if ($row['status']==10) echo "selected"; ?>  value="10">Приглашен</option>
                                <option value="1">На утверждение</option>
                            </select>
                        </div>
                        <?php } ?>

                        <div class="mb-3 col-12 col-md-2 add_new_box">
                            <label for="department" class="form-label">Когда напомнить?</label>
                            <input type="date" class="form-control" name="date_napomnit" value="<?=$row['date_napomnit'] ?? ''; ?>">
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="mb-3 col-12 col-md-4">
                        <label for="candidate" class="form-label">Кандидат Ф.И.О</label>
                        <input type="text" class="form-control" id="candidate" name="candidate" value="<?php echo $row['candidate'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3 col-12 col-md-2">
                        <label for="department" class="form-label">Гражданство</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?php echo $row['country'] ?? ''; ?>">
                    </div>
                    <div class="mb-3 col-12 col-md-2">
                        <label for="department" class="form-label">Дата рождения</label>
                        <input type="text" class="form-control" id="ageInput"  name="age" value="<?php echo $row['age'] ?? ''; ?>" placeholder="дд.мм.гг">
                    </div>
                    <div class="mb-3 col-12 col-md-4">
                        <label for="salary" class="form-label">Телефон номер</label>
                        <input type="tel" class="form-control art-stranger" id="tel" name="tel" value="<?php
                        $formattedNumber = formatPhoneNumber($row["tel"]);
                        echo $formattedNumber ?>">
                    </div>
                    <div class="mb-3 col-12 col-md-6">
                        <label for="salary" class="form-label">Адрес</label>
                        <input type="text" class="form-control" id="address" name="adres" value="<?php echo $row['adres'] ?? ''; ?>">
                    </div>
                    <div class="mb-3 col-12 col-md-3">
                        <label for="salary" class="form-label">Метро</label>
                        <select class="form-select js-example-basic-single" name="metro">
                            <?php
                            while ($row8 = $stmt8->fetch()) {  ?>
                                <option value="<?= $row8['id']?>"
                                    <?php if ($row['metro'] == $row8['id']){
                                        echo 'selected'; } ?>>
                                    <?= $row8['name_metro']?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3 col-12 col-md-3">
                        <label for="department" class="form-label">Профессия</label>
                        <input type="text" class="form-control" id="department" name="department" value="<?php echo $row['department'] ?? ''; ?>" required>
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                        <label for="object" class="form-label">Объект</label>
                        <select class="form-select" id="object" name="object" required="">
                            <?php if ($url == 0) { ?>
                                <option value="">- не выбран -</option>
                            <?php } ?>

                            <?php if ($row['status'] == 100) { ?>
                                <?php
                                $objectName = '';
                                foreach ($objects as $row3) {
                                    if ($row['objects'] == $row3['name']) {
                                        $objectName = $row3['id'];
                                        break;
                                    }
                                }
                                ?>
                                <option value="<?php echo $objectName; ?>" selected><?=$row['objects']?></option>
                            <?php } ?>

                            <?php if ($row['status'] != 100) { ?>
                                <?php foreach ($objects as $row3) { ?>
                                    <option value="<?=$row3['id']?>"
                                        <?php if ($row['objects'] == $row3['name']) echo 'selected';?>
                                    ><?=$row3['name']?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>

                        <div class="col-md-3 col-12">
                            <label for="inputAddress" class="form-label">Менеджер объекта</label>
                            <input type="text" class="form-control" name="creator" id="inputAddress" value=" <?php
                            $managerName = '';
                            $stmt3 = pdo()->prepare("SELECT `id`, `name` FROM `users`");
                            $stmt3->execute();
                            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                if ($row['sogl'] == $row3['id']) {
                                    $managerName = $row3['name'];
                                    break;
                                }
                            }
                            echo $managerName;
                            ?>"  disabled = "disabled">
                        </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label for="filename" class="form-label required">Фото для пропуска (.png, .jpg)</label>
                        <?php if ($url != 0 and $row['form_dir'] != null) { ?>
                            <div class="d-grid">
                                <input class="mb-2 form-control" type="file" accept="image/jpeg" name="filename">
                                <input type="hidden" name="oldfilename" value="<?=$row['form_dir']?>">
                                <img data-fancybox class="rounded mb-2" width="80" src="<?=$row['form_dir']?>" alt="">
                            </div>
                        <?php } else { ?>
                            <input type="file" accept="image/jpeg" class="form-control" name="filename" value="">
                        <?php } ?>
                    </div>
                    <?php if($url!=0) { ?>
                    <div class="col-md-3 col-12">
                        <label for="inputAddress" class="form-label">HR</label>
                        <div class="d-flex align-items-center mb-3 gap-2">
                            <?php
                            $idhr = $row['creator'];
                            $stmt40 = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :idhr");
                            $stmt40->bindParam(':idhr', $idhr);
                            $stmt40->execute();
                            $row40 = $stmt40->fetch();
                            if(!$row40['dir_img']){
                                $imageHr = '/../assets/userimg.jpg';
                            }else{
                                $imageHr = '/../'.$row40['dir_img'];
                            }
                            ?>
                            <img src="<?=$imageHr ?>" alt="" class="rounded-circle mb-2 border p-1 bg-light-subtle" width="60" height="60">
                            <p name="hr_id" value="<?php echo $row40['id'];?>"><?php echo $row['username'];?> <br>
                                <small><?php dataFormat($row['date']); ?></small></p>
                            <?php if ($user['id'] == 1){?>
                                <button class="btn btn-danger">Изменить</button>
                            <?php }?>
                        </div>



                        <div class="d-flex justify-content-between mb-3">
                            <?php if ($row40['position'] == 7){?>
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            <?php }
                            elseif ($row['creator']== $user['id'] || $row['sogl']== $user['id'] || $row['status']==6 || $user['id'] == 1 ){?>
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            <?php }  ?>
                        </div>

                    </div>
                    <?php } ?>
                    <div class="col-md-9 mb-3 mt-3">
                        <div class="form-floating">
                            <textarea style="height:20dvh ;" class="form-control" name="notes" placeholder="Leave a comment here" id="floatingTextarea2" ><?php echo $row['notes'] ?? '';?></textarea>
                            <label for="floatingTextarea2">Комментарии</label>
                        </div>
                    </div>
                </div>

            </form>

            <?php if ($url!=0){ ?>
                    <hr class="border border-primary border-3 opacity-75 mt-5">
                <h3 class="mt-4">ОТЗЫВ</h3>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Дата</th>
                        <th scope="col">Комментарии</th>
                        <th scope="col">Где работал</th>
                        <th scope="col">Менеджер</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while($row = $stmt5->fetch()){
                        if ($row["id"] == $url) {
                            ?>
                            <tr>
                                <th scope="row"><?= $row['id']; ?></th>
                                <th scope="row"><?= $row['date']; ?></th>
                                <td><?= $row['comment'];?></td>
                                <td><?php
                                    $stmt2 = pdo()->prepare('SELECT `name` FROM `objects` WHERE `id` = :id');
                                    $stmt2->execute(['id'=>$row['objects']]);
                                    $obj = $stmt2->fetch(PDO::FETCH_ASSOC);
                                    echo $obj['name'];?></td>
                                <td><?php
                                    $stmt = pdo()->prepare('SELECT `name`, `username` FROM `users` WHERE `id` = :id');
                                    $stmt->execute(['id'=>$row['meneger']]);
                                    $firstcoord = $stmt->fetch(PDO::FETCH_ASSOC);

                                    echo $firstcoord['name'];?></td>
                            </tr>
                        <?php } }?>
                </table>
            <?php }
        }
        catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "<a> Access Denied </a>";
    } ?>
</div>

<script>
    $(document).ready(function() {
        // Добавляем обработчик события 'change' на элемент выпадающего списка
        $('#pay_type').on('change', function() {
            // Получаем выбранное значение из выпадающего списка
            var selectedValue = $(this).val();

            // Проверяем, равно ли выбранное значение "3" (значение опции "Остаток")
            if (selectedValue === '5') {
                // Если да, то добавляем класс "active" к div с классом "col-lg-2 col-md-4 col-sm-6"
                $('.add_new_box').addClass('active');
            } else {
                // Если нет, то удаляем класс "active" у div с классом "col-lg-2 col-md-4 col-sm-6"
                $('.add_new_box').removeClass('active');
            }
        });
    });
</script>

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

<script>
    document.getElementById('title_page').innerText =document.getElementById('candidate').value ;
</script>



<?php include_once __DIR__ . '/../templates/footer.php'; ?>
