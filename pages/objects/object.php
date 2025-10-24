<?php include_once __DIR__ . '/../templates/header.php';
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
?>
<?php
$url = basename($_SERVER['REQUEST_URI'], "?print"); // получаем ID заявки из URL
if ($url != 0) {
    $stmt = pdo()->prepare("SELECT `name` FROM `objects` WHERE `id` = :id");
    $stmt->execute(['id' => $url]);
    if (!$stmt->rowCount()) {
        flash('Заявка не найдена', 'danger');
        header('Location: ' . root() . '/objects');
        die;
    }
}
if ($user['ob_edit']) {
$maxfiles = 3;
try {
if ($url != 0) {
    // Получение данных заявки для вывода
    $stmt = pdo()->prepare("SELECT `data`, meneger, numpersonal, name, data_end, adress_obj, notes, inn, tel_number, obj_dir1, obj_dir2, obj_dir3, dop_status FROM `objects` WHERE objects.id = :id");
    $stmt->execute(['id' => $url]);
    $row = $stmt->fetch();

    $idhr = $row['meneger'];
    $stmt40 = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :idhr");
    $stmt40->bindParam(':idhr', $idhr);
    $stmt40->execute();
    $row40 = $stmt40->fetch();
    // получение имен всех согласующих для вывода
    $stmt3 = pdo()->prepare("SELECT `id`, `candidate`,`date`, `tel`, `status`, `objects`, `notes`, `department`, `form_dir` FROM `hrapp`");
    $stmt3->execute();

    if ($url != 0) {
        $stmt4 = pdo()->prepare("SELECT `description`, url FROM `objectmaterial` WHERE `id` =:id");
        $stmt4->execute(['id' => $url]);
    }
}
// получение File objects
$stmt8 = pdo()->prepare("SELECT * FROM `file_object` where `id_object` = $url");
$stmt8->execute();
?>
<?php if ($url == 0) { ?>
    <div class="d-flex justify-content-between mb-3  py-4">
        <h3 class="title">Новый объект</h3>
    </div>
<?php } ?>

<?php if ($url != 0) { ?>
    <div class="row mb-5 mt-4 bg-body-tertiary rounded p-3">
        <div class="col-lg-6 ">
            <h3><?php echo $row['name'] ?? ''; ?></h3>
            <p class="text-700 mt-2"><span class="fas fa-phone"></span> <a
                        href="tel:<?php echo $row['tel_number'] ?? ''; ?>"><?php echo $row['tel_number'] ?? ''; ?></a></p>
            <div class="row g-0">
                <div class="col-6 col-xl-6">
                    <div class="d-flex flex-column flex-center align-items-sm-start flex-md-row justify-content-md-between flex-xxl-column p-3 ps-sm-3 ps-md-4 p-md-3 h-100 border-1 border-bottom border-end">
                        <div class="d-flex align-items-center mb-1"><span
                                    class="fa-solid fa-square fs--3 me-2 text-primary"
                                    data-fa-transform="up-2"></span><span class="mb-0 fs--1 text-900">Менеджер</span>
                        </div>
                        <?php if ($url != 0 ) {
                            $stmt2 = pdo()->prepare('SELECT `name`, `username` FROM `users` WHERE `id` = :id');
                            $stmt2->execute(['id' => $row['meneger']]);
                            $firstcoord = $stmt2->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <h3 class="fw-semi-bold ms-xl-3 ms-xxl-0 pe-md-2 pe-xxl-0 mb-0 mb-sm-3"><?= $firstcoord['name'] ?></h3>
                        <?php  }  ?>
                    </div>
                </div>
                <div class="col-6 col-xl-6">
                    <div class="d-flex flex-column flex-center align-items-sm-start flex-md-row justify-content-md-between flex-xxl-column p-3 ps-sm-3 ps-md-4 p-md-3 h-100 border-1 border-bottom border-end-md-0 border-end-xl">
                        <div class="d-flex align-items-center mb-1"><span
                                    class="fa-solid fa-square fs--3 me-2 text-success"
                                    data-fa-transform="up-2"></span><span class="mb-0 fs--1 text-900">Работают</span>
                        </div>

                        <?php
                        $id = $url;
                        $stmt5 = pdo()->prepare("SELECT COUNT(*) AS count FROM `hrapp` WHERE `status` = 100 AND `objects` = :id");
                        $stmt5->bindParam(':id', $id, PDO::PARAM_INT); // Предполагается, что id - это целое число
                        $stmt5->execute();
                        $result = $stmt5->fetch(PDO::FETCH_ASSOC);
                        $count = $result['count'];
                        ?>
                        <h3 class="fw-semi-bold ms-xl-3 ms-xxl-0 pe-md-2 pe-xxl-0 mb-0 mb-sm-3"><?php echo $count; ?></h3>
                    </div>
                </div>
                <div class="col-6 col-xl-6">
                    <div class="d-flex flex-column flex-center align-items-sm-start flex-md-row justify-content-md-between flex-xxl-column p-3 ps-sm-3 ps-md-4 p-md-3 h-100 border-1 border-bottom border-end-md-0 border-end-xl">
                        <div class="d-flex align-items-center mb-1"><span
                                    class="fa-solid fa-square fs--3 me-2 text-success"
                                    data-fa-transform="up-2"></span><span class="mb-0 fs--1 text-900">Количество</span>
                        </div>
                        <h3 class="fw-semi-bold ms-xl-3 ms-xxl-0 pe-md-2 pe-xxl-0 mb-0 mb-sm-3"><?php echo $row['numpersonal'] ?? ''; ?></h3>
                    </div>
                </div>
                <div class="col-6 col-xl-6">
                    <div class="d-flex flex-column flex-center align-items-sm-start flex-md-row justify-content-md-between flex-xxl-column p-3 ps-sm-3 ps-md-4 p-md-3 h-100 border-1 border-bottom border-end">
                        <div class="d-flex align-items-center mb-1"><span
                                    class="fa-solid fa-square fs--3 me-2 text-danger"
                                    data-fa-transform="up-2"></span><span class="mb-0 fs--1 text-900">Потребуется</span>
                        </div>
                        <?php $nado = $row['numpersonal'] - $count; ?>
                        <h3 class="fw-semi-bold ms-xl-3 ms-xxl-0 pe-md-2 pe-xxl-0 mb-0 mb-sm-3" id="nado"><?php
                            if (substr($nado, 0, 1) === '-') {
                                echo "не нужны";
                            } else {
                                echo $nado;
                            }
                            ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 ">
            <div class="h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="mb-2">Дата</h5>
                            <h6 class="text-700"><?php
                                if (!$row['data'] || $row['data'] === '0000-00-00') {
                                    echo date('d.m.Y');
                                } else {
                                    dataFormat($row['data']);
                                }
                                ?></h6>
                        </div>
                    </div>
                    <div class="pb-4 pt-3">
                        <canvas id="myChart"></canvas>
                        <!--                                    <div class="echart-top-coupons" style="height:100px;width:100%;"></div>-->
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bullet-item bg-primary me-2"></div>
                            <h6 class="text-900 fw-semi-bold flex-1 mb-0 mr-1">В объекте работают </h6>
                            <h6 class="text-900 fw-semi-bold mb-0"> <?php echo $count; ?></h6>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bullet-item bg-danger me-2"></div>
                            <h6 class="text-900 fw-semi-bold flex-1 mb-0 mr-1">Потребуется </h6>
                            <h6 class="text-900 fw-semi-bold mb-0"> <?php
                                if (substr($nado, 0, 1) === '-') {
                                    echo "не нужны";
                                } else {
                                    echo $nado;
                                }
                                ?></h6>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <hr>
<?php } ?>
<div class="row">
    <form method="POST" action="add_object.php" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" value="<?php echo $url; ?>">
        <input type="hidden" value="<?php if(!$row['dop_status']){echo '1';}else{echo $row['dop_status'];} ?>" name="dop_statuss">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <label for="exampleInputEmail1" class="form-label">Название объекта</label>
                <input type="text" class="form-control" id="name" name="name" value='<?php echo $row['name'] ?? ''; ?>' >
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <label for="exampleInputEmail1" class="form-label">ИНН</label>
                <input type="number" class="form-control" id="inn" name="inn" value="<?php echo $row['inn'] ?? ''; ?>">
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <label for="exampleInputEmail1" class="form-label">Телефон</label>
                <input type="tel" class="form-control art-stranger" id="tel_number" name="tel_number"
                       value="<?php echo $row['tel_number'] ?? ''; ?>">
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <label for="exampleInputPassword1" class="form-label">Дата</label>
                <input type="date" class="form-control" id="data" name="data" value="<?php
                if (!$row['data'] || $row['data'] === '0000-00-00') {
                    echo date('Y-m-d');
                } else {
                    echo $row['data'];
                } ?>">
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <label for="exampleInputPassword1" class="form-label">Дата окончание</label>
                <input type="date" class="form-control" id="data_end" name="data_end"
                       value="<?php echo $row['data_end'] ?? ''; ?>">
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <label for="exampleInputPassword1" class="form-label">Количество рабочих</label>
                <input type="number" class="form-control" id="numpersonal" name="numpersonal"
                       value="<?php echo $row['numpersonal'] ?? ''; ?>">
            </div>
            <div class="col-lg-8 col-md-6 col-12 mb-3">
                <label for="exampleInputPassword1" class="form-label">Адресс</label>
                <input class="form-control" type="text" id="addressinput" placeholder="Введите адрес"
                       name="addressinput" value="<?php echo $row['adress_obj'] ?? ''; ?>" >
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <?php
                $stmt20 = pdo()->prepare("SELECT * FROM `users` ");
                $stmt20->execute(); ?>
                <label for="object" class="form-label">Менеджер</label>
                <?php if($user['dop_user'] == 1) { ?>
                    <input type="text" value="<?= $user['name'] ?>" class="form-control" disabled>
                    <input type="hidden" name="dop_user" value="<?= $user['id'] ?>">
                <?php }else { ?>
                <select class="form-select" id="object" name="men_name" value="<?php echo $row['meneger'] ?? ''; ?>"
                        required >
                    <?php if ($url == 0) { ?>
                    <option>- не выбран -</option>
                    <?php }?>

                    <?php if ($url != 0) {
                        $stmt2 = pdo()->prepare('SELECT `name`, `username` FROM `users` WHERE `id` = :id');
                        $stmt2->execute(['id' => $row['meneger']]);
                        $firstcoord = $stmt2->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <option value="<?= $row['meneger'] ?>" selected><?= $firstcoord['name'] ?></option>
                    <?php } else {
                        while ($row = $stmt20->fetch()) { ?>
                            <option value="<?php echo $row["id"] ?>"><?php echo $row["name"] ?></option>
                        <?php }
                    }
                     if ($user['id'] == $row['meneger'] || $user['id'] == 1) {
                         while ($row = $stmt20->fetch()) { ?>
                             <option value="<?php echo $row["id"] ?>"><?php echo $row["name"] ?></option>
                         <?php }
                     }
                    ?>
                </select> <?php } ?>
            </div>
            <div class="mb-2">
                <p>Файлы</p>
                <?php include_once __DIR__ . '/add_file.php'; ?>
            </div>

            <div class="col-12 mb-3 mt-3">
                <div class="form-floating">
                    <textarea class="form-control" name="notes" placeholder="Leave a comment here"
                              id="floatingTextarea2" style="height: 100px"><?php echo $row['notes'] ?? ''; ?></textarea>
                    <label for="floatingTextarea2">Заметки</label>
                </div>
            </div>
        </div>

        <?php
        $idhr = $row['meneger'];
        $stmt40 = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :idhr");
        $stmt40->bindParam(':idhr', $idhr);
        $stmt40->execute();
        $row40 = $stmt40->fetch();
        if ($url == 0 || $row40['position'] == 7) { ?>
            <button type="submit" class="btn btn-primary mt-2">Сохранить</button>
        <?php } elseif ($user['ob_edit'] == 1) { ?>
            <button type="submit" class="btn btn-primary mt-2">Сохранить данные</button>
        <?php } else{ echo 'нет прав'; } ?>

    </form>





    <?php if ($url != 0){

    $stmt7 = pdo()->prepare("SELECT `id`, meneger FROM `objects` WHERE objects.id = :id");
    $stmt7->execute(['id' => $url]);
    $row7 = $stmt7->fetch();
    if ($row7['meneger'] == htmlspecialchars($user['id']) || htmlspecialchars($user['id']) == 1){ ?>

    <div id="rabotniki" class="col-12 mt-3">
        <hr class="border border-primary border-3 opacity-75">
        <div class="d-flex gap-3 mb-3">
            <h3>Работники</h3>
            <button class="btn btn-primary" onclick="location='/../time/work_user?url=<?= $url ?>'">Часы работы</button>
        </div>

        <div class="card mb-3">
            <div class="table-responsive">
                <table class="table align-middle card-table mb-0 ">
                    <thead class="table-light">
                    <tr>
                        <th>Id</th>
                        <th>Работник Ф.И.О</th>
                        <th>Профессия</th>
                        <th>Телефон номер</th>
                        <th>Комментарии</th>
                        <th>Часы работы</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $stmt3->fetch()) {
                        if ($row["status"] == 100) {
                            if ($row["objects"] == $url) { ?>
                                <tr>
                                <td>
                                    <a class="obj_img_user p-1" href="/hr-new/<?= $row["id"] ?>">
                                        <?php if(!$row["form_dir"]) { $row["form_dir"] = "/../assets/userimg.jpg"; } ?>
                                        <img  src="<?php echo $row["form_dir"] ?>" alt="">
                                    </a>
                                </td>
                                <td><a href="/hr-new/<?= $row["id"] ?>"><?php echo $row["candidate"] ?></a></td>
                                <td><?php echo $row["department"] ?></td>
                                <td><a href="tel:<?php echo $row["tel"] ?>"><?php echo $row["tel"] ?></a></td>
                                <td><?php echo $row["notes"] ?></td>
                                <td>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal-<?php echo $row['id']; ?>">
                                        Посмотреть
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal-<?php echo $row['id']; ?>" tabindex="-1"
                                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5"
                                                        id="exampleModalLabel"><?php echo $row['candidate']; ?></h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-striped">
                                                        <tr>
                                                            <td>Дата</td>
                                                            <td>Начало</td>
                                                            <td>Конец</td>
                                                            <td>Время сколько отработал</td>
                                                            <td>Удалить смену</td>
                                                        </tr>
                                                        <?php
                                                        $iduser = $row['id'];
                                                        $stmt4 = pdo()->prepare("SELECT * FROM `time_hr` WHERE iduser = :iduser");
                                                        $stmt4->execute(['iduser' => $iduser]);
                                                        $row4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);

                                                        $dateWorkHours = [];

                                                        // Обрабатываем записи
                                                        foreach ($row4 as $value) {
                                                            $date = $value['date'];
                                                            $start = $value['start'];
                                                            $end = $value['end'];

                                                            if (!empty($start) && !empty($end)) {
                                                                $startTime = strtotime($start);
                                                                $endTime = strtotime($end);
                                                                $intervalInSeconds = $endTime - $startTime;

                                                                $hoursWorked = floor($intervalInSeconds / 3600);
                                                                $minutesWorked = floor(($intervalInSeconds % 3600) / 60);

                                                                if (!isset($dateWorkHours[$date])) {
                                                                    $dateWorkHours[$date] = ['hours' => 0, 'minutes' => 0];
                                                                }

                                                                // Добавляем часы и минуты к дате
                                                                $dateWorkHours[$date]['hours'] += $hoursWorked;
                                                                $dateWorkHours[$date]['minutes'] += $minutesWorked;

                                                                // Корректируем минуты, если их больше 60
                                                                if ($dateWorkHours[$date]['minutes'] >= 60) {
                                                                    $dateWorkHours[$date]['hours'] += floor($dateWorkHours[$date]['minutes'] / 60);
                                                                    $dateWorkHours[$date]['minutes'] = $dateWorkHours[$date]['minutes'] % 60;
                                                                }
                                                            }
                                                        }

                                                        // Теперь выводим результаты после обработки всех данных
                                                        foreach ($row4 as $value) {
                                                            $date = $value['date'];
                                                            if (isset($dateWorkHours[$date])) {
                                                                $time = $dateWorkHours[$date];
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                        $formatter = new \IntlDateFormatter('ru_RU', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                                                                        $timestamp = strtotime($value['date']);
                                                                        echo $formatter->format($timestamp);?>
                                                                    </td>
                                                                    <td><?php echo $value['start']; ?></td>
                                                                    <td><?php echo $value['end']; ?></td>
                                                                    <td><?php echo $time['hours'] . " ч " . $time['minutes'] . " м"; ?></td>
                                                                    <td>
                                                                        <!-- Кнопка для удаления смены -->
                                                                        <form method="POST" action="delete_shift.php">
                                                                            <input type="hidden" name="shift_id" value="<?php echo $value['id']; ?>">
                                                                            <input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                                                            <button class="btn-sm btn-danger" type="submit" onclick="return confirm('Вы уверены, что хотите удалить эту смену?');">Удалить</button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            <?php }
                                                        }

                                                        ?>

                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary"
                                                            data-bs-dismiss="modal">Закрыть
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </td>

                                <td>
                                <!-- Button trigger modal -->
                                <button onclick="openModal(<?php echo $row['id']; ?>)" type="button"
                                        class="btn btn-sm btn-danger">
                                    Уволить
                                </button>
                                <div id="modal" class="modal">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <button class="btn btn-close" onclick="closeModal()"></button>
                                            <form action="deleteuser.php" method="post" id="modal">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="newObject"
                                                       value="<?php echo $row['objects']; ?>">
                                                <input type="hidden" name="newStatus"
                                                       value="<?php echo $row['status']; ?>">
                                                <input type="hidden" name="meneger" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="date" value="<?php echo date('Y-m-d') ?>">
                                                <div class="mb-3">
                                                    <label for="exampleFormControlTextarea1" class="form-label">Почему
                                                        хотите уволить <?php echo $row["candidate"] ?>a ?</label>
                                                    <textarea class="form-control" id="exampleFormControlTextarea1"
                                                              rows="3" name="comment"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-danger">
                                                    Уволить работника из объекта
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                            </td>
                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php }
        } ?>
        <?php }
        catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
        } else {
            echo "<a> Access Denied </a>";
        } ?>
    </div>
    <?php include_once __DIR__ . '/../templates/footer.php'; ?>

    <script>
        function openModal(id) {
            document.querySelector('#modal input[name="id"]').value = id;
            const modal = document.getElementById("modal");
            modal.style.display = "block";
        }

        function closeModal() {
            const modal = document.getElementById("modal");
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            const modal = document.getElementById("modal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        var workers = <?php echo $count; ?>; // Убедитесь, что $count определено перед этим кодом
        // Здесь задайте ваше значение переменной $nado
        var nado = document.getElementById('nado').textContent; // Преобразуем $count_user в число
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut', // Измените на 'pie', чтобы создать Pie Chart
            data: {
                datasets: [{
                    data: [nado, workers],
                    backgroundColor: ['red', 'blue']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 70
            }

        });
    </script>

