<?php
require_once __DIR__ . '/../check_auth.php';
if (!$user) {
    header('Location: ' . root() . '/index.php');
    die;
}
$url = basename($_SERVER['REQUEST_URI'], "?print"); // получаем ID заявки из URL
if ($url != 0) {
    $stmt = pdo()->prepare("SELECT `candidate` FROM `hrapp` WHERE `id` = :id");
    $stmt->execute(['id' => $url]);
    if (!$stmt->rowCount()) {
        flash('Заявка не найдена', 'danger');
        header('Location: ' . root() . '/hrs.php');
        die;
    }
}

// получение имен всех статусов для вывода
$stmt1 = pdo()->prepare('SELECT id, name_status, color FROM status_hr');
$stmt1->execute();

include_once __DIR__ . '/../templates/header.php';
?>
<!-- Подключаем Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .select2-dropdown {
        background-color: #212529;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #a0a4a8;
    }

    .select2-container--default .select2-selection--single {
        background-color: #212529;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #212529;
    }
</style>

<div <?php if (isset($_GET['print'])) echo 'onload="window.print();"' ?>>
    <div class="row mb-2 align-items-center">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mt-3 align-items-center">
                    <li class="breadcrumb-item"><a class="btn btn-outline-primary" href="#" onclick="goBack()"><i
                                    class="bi bi-arrow-left"></i>Назад</a></li>
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
            $stmt = pdo()->prepare("SELECT `date`, `creator`, `country`, `date_napomnit`, `tel`, `age`, `adres`, `metro`, `hr_dop`, `date_edit`, `date_patent`, `inn_patent`,  users.name AS username, `candidate`, objects.name AS objects, `department`, `form_dir`, hrapp.status, `sogl`, hrapp.notes FROM `hrapp` JOIN `objects` ON objects.id = hrapp.objects JOIN `users` ON users.id = hrapp.creator WHERE hrapp.id = :id");
            $stmt->execute(['id' => $url]);
            $row = $stmt->fetch();
            $sogl = explode('-', $row['sogl'])[$row['status'] - 1] ?? 0;
            $len = count(explode('-', $row['sogl']));
            $last = $len == $row['status'];
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

            $stmt9 = pdo()->prepare("SELECT * FROM `status_hr`");
            $stmt9->execute();
            $statuses = $stmt9->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <?php if ($row['status'] !== 5) { ?>
                <style>
                    .add_new_box {
                        display: none;
                    }

                    .active {
                        display: block;
                    }
                </style>
            <?php } ?>

            <form method="post" action="/hr/do_add_hr.php" enctype="multipart/form-data" class="">
                <input type="hidden" id="id" name="id" value="<?php echo $url; ?>">
                <div class="row">
                    <div class="col-md-6 col-xl-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center mb-1 gap-2">
                                            <?php
                                            $imgSrc = $row['form_dir'] ? $row['form_dir'] : '/../assets/userimg.jpg';
                                            ?>
                                            <img data-fancybox class="rounded-circle mb-2 border p-1 bg-light-subtle"
                                                 width="60" height="60" src="<?= $imgSrc ?>"
                                                 alt="<?= $row['candidate'] ?>">
                                            <input type="text" class="form-control" id="candidate" name="candidate"
                                                   value="<?= $row['candidate'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-7 col-12 mb-3">
                                        <input class="mb-2 form-control" type="file" accept="image/jpeg"
                                               name="filename">
                                        <input type="hidden" name="oldfilename" value="<?= $row['form_dir'] ?>">
                                    </div>

                                    <?php if ($user['tdf_edit']) { ?>
                                        <div class="col-md-5 col-12 mb-3">
                                            <select id="pay_type" class="form-select
                                                <?php
                                                // Применяем стили в зависимости от текущего статуса
                                                foreach ($statuses as $status) {
                                                    if ($status['id'] == $row['status']) {
                                                        echo $status['color']; // Используем поле color из БД
                                                        break;
                                                    }
                                                }
                                                ?>" name="newstatus">
                                                    <!-- Специальный вариант "У вас" (если нужно оставить) -->
                                                    <?php if (explode('-', $row['sogl'])[$row['status'] - 1] == $user['id']) { ?>
                                                        <option selected value="1">У вас</option>
                                                    <?php } ?>
                                                    <!-- Основные варианты из БД -->
                                                    <?php foreach ($statuses as $status): ?>
                                                        <option value="<?= $status['id'] ?>"
                                                            <?= ($status['id'] == $row['status']) ? 'selected' : '' ?> >
                                                            <?= htmlspecialchars($status['name_status']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12 add_new_box mb-2">
                                            <label for="department" class="form-label">Когда напомнить?</label>
                                            <input type="date" class="form-control" name="date_napomnit" value="<?= $row['date_napomnit'] ?? ''; ?>">
                                        </div>
                                    <?php } ?>
                                    <?php if ($row['status'] == 100) { ?>
                                        <div class="mb-2 col-md-6">
                                            <label for="inn_patent" class="form-label">ИНН патента</label>
                                            <input type="number" class="form-control" id="inn_patent" name="inn_patent"
                                                   value="<?= $row['inn_patent'] ?>">
                                        </div>
                                        <div class="mb-2 col-md-6">
                                            <label for="date_patent" class="form-label">Дата оканчания</label>
                                            <input type="date" class="form-control" id="date_patent" name="date_patent"
                                                   value="<?= $row['date_patent'] ?>" placeholder="дд.мм.гг">
                                        </div>
                                    <?php } ?>
                                    <div class="mb-2 col-md-6">
                                        <label for="department" class="form-label">Дата рождения</label>
                                        <input type="text" class="form-control" id="ageInput" name="age"
                                               value="<?= $row['age'] ?>" placeholder="дд.мм.гг">
                                    </div>
                                    <div class="mb-2 col-12 col-md-6">
                                        <label for="salary" class="form-label">Телефон номер</label>
                                        <input type="tel" class="form-control art-stranger" id="tel" name="tel" value="<?php
                                               $formattedNumber = formatPhoneNumber($row["tel"]);
                                               echo $formattedNumber ?>">
                                    </div>

                                    <div class="mb-2 col-md-6">
                                        <label for="department" class="form-label">Гражданство</label>
                                        <input type="text" class="form-control" id="country" name="country"
                                               value="<?php echo $row['country'] ?? ''; ?>">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label for="department" class="form-label">Профессия</label>
                                        <input type="text" class="form-control" id="department" name="department" value="<?= $row['department']; ?>" required>
                                    </div>
                                </div>

                                <?php if ($row['notes']) { ?>
                                    <div class="alert alert-info" role="alert">
                                        <?= $row['notes'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-6">
                        <div class="card p-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-2 col-12 col-md-8">
                                        <label for="object" class="form-label">Объект</label>
                                        <select class="form-select" id="object" name="object" required="">
                                            <?php if ($row['objects']) { ?>
                                                <?php foreach ($objects as $row3) { ?>
                                                    <option value="<?= $row3['id'] ?>"
                                                        <?php if ($row['objects'] == $row3['name']) echo 'selected'; ?> ><?= $row3['name'] ?>
                                                    </option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-2 col-12 col-md-4">
                                        <label for="inputAddress" class="form-label">Менеджер объекта</label>
                                        <input type="text" class="form-control" name="creator" id="inputAddress"
                                               value="<?php
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
                                               ?>" disabled="disabled">
                                    </div>
                                    <div class="mb-2 col-12 col-md-6">
                                        <label for="salary" class="form-label">Адрес</label>
                                        <input type="text" class="form-control" id="address" name="adres"
                                               value="<?= $row['adres']; ?>">
                                    </div>

                                    <div class="mb-2 col-12 col-md-6">
                                        <label for="salary" class="form-label">Метро</label>
                                        <select class="form-select js-example-basic-single" name="metro">
                                            <?php
                                            while ($row8 = $stmt8->fetch()) { ?>
                                                <option value="<?= $row8['id'] ?>"
                                                    <?php if ($row['metro'] == $row8['id']) {
                                                        echo 'selected';
                                                    } ?>>
                                                    <?= $row8['name_metro'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="inputAddress" class="form-label">HR</label>
                                        <div class="d-flex align-items-center mb-3 gap-2">
                                            <?php
                                            if ($row['creator']) {
                                                $idhr = $row['creator'];
                                                $stmt40 = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :idhr");
                                                $stmt40->bindParam(':idhr', $idhr);
                                                $stmt40->execute();
                                                $row40 = $stmt40->fetch();
                                                if (!$row40['dir_img']) {
                                                    $imageHr = '/../assets/userimg.jpg';
                                                } else {
                                                    $imageHr = '/../' . $row40['dir_img'];
                                                } ?>
                                                <img id="hrUpdateImg" src="<?= $imageHr ?>" alt=""
                                                     class="rounded-circle mb-2 border p-1 bg-light-subtle" width="60"
                                                     height="60">
                                                <p name="hr_id"
                                                   value="<?php echo $row40['id']; ?>"><?php echo $row40['name']; ?><br>
                                                    <small><?php echo $row['date_edit']; ?></small>
                                                </p>
                                            <?php } else {
                                                $idhr = $row['hr_dop'];
                                                $stmt40 = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :idhr");
                                                $stmt40->bindParam(':idhr', $idhr);
                                                $stmt40->execute();
                                                $row40 = $stmt40->fetch();
                                                if (!$row40['dir_img']) {
                                                    $imageHr = '/../assets/userimg.jpg';
                                                } else {
                                                    $imageHr = '/../' . $row40['dir_img'];
                                                }

                                                ?>
                                                <img id="hrUpdateImg" src="<?= $imageHr ?>" alt=""
                                                     class="rounded-circle mb-2 border p-1 bg-light-subtle" width="60"
                                                     height="60">
                                                <p name="hr_id"
                                                   value="<?php echo $row40['id']; ?>"><?php echo $row40['name']; ?><br>
                                                    <small><?php dataFormat($row['date']); ?></small>
                                                </p>
                                            <?php } ?>

                                        </div>

                                        <div class="btn btn-danger" data-bs-toggle="modal"
                                             data-bs-target="#exampleModal-hr">Изменить
                                        </div>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModal-hr" tabindex="-1"
                                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Изменить
                                                            HR</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <select class="form-select" id="hrSelect"
                                                                aria-label="Default select example">
                                                            <option selected>Все</option>
                                                            <?php
                                                            $stmt400 = pdo()->prepare("SELECT * FROM `users` WHERE `dop_user` != '1'");
                                                            $stmt400->execute();
                                                            $row400 = $stmt400->fetch();
                                                            foreach ($stmt400 as $row400) {
                                                                ?>
                                                                <option value="<?php echo $row400['id']; ?>"><?php echo $row400['name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" id="id_post" value="<?php echo $url; ?>">
                                                        <?php if (empty($row['hr_dop'])) { ?>
                                                            <input type="hidden" id="hr_original"
                                                                   value="<?php echo $row['creator']; ?>"> <?php } else { ?>
                                                            <input type="hidden" id="hr_original"
                                                                   value="<?php echo $row['hr_dop']; ?>"> <?php } ?>
                                                        <button type="button" class="btn btn-primary" id="saveHrBtn">
                                                            Сохранить
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        document.getElementById('saveHrBtn').addEventListener('click', function () {
                                            var selectedHrId = document.getElementById('hrSelect').value;
                                            var id = document.getElementById('id_post').value; // Убедитесь, что элемент с id="id_post" существует
                                            var hr_original = document.getElementById('hr_original').value; // Убедитесь, что элемент с id="id_post" существует

                                            // Отправка данных на сервер с помощью AJAX
                                            var xhr = new XMLHttpRequest();
                                            xhr.open('POST', '/../hr/comment/update_hr.php', true);
                                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                            xhr.onload = function () {
                                                if (xhr.status === 200) {
                                                    // Обновление интерфейса без перезагрузки страницы
                                                    var response = JSON.parse(xhr.responseText);
                                                    console.log(response);
                                                    if (response.success) {
                                                        // Обновляем данные на странице
                                                        document.querySelector('p[name="hr_id"]').innerText = response.newHrName;
                                                        document.getElementById('hrUpdateImg').src = response.newHrImage;
                                                        // Закрываем модальное окно
                                                        var modal = bootstrap.Modal.getInstance(document.getElementById('exampleModal-hr'));
                                                        modal.hide(); // Исправлено: modal.hide()
                                                    } else {
                                                        alert('Ошибка при обновлении HR');
                                                    }
                                                }
                                            };
                                            // Правильная передача данных в xhr.send
                                            xhr.send('hr_id=' + encodeURIComponent(selectedHrId) + '&id=' + encodeURIComponent(id) + '&hr_original=' + encodeURIComponent(hr_original));
                                        });
                                    </script>


                                    <?php if ($row['hr_dop']) { ?>
                                        <div class="col-md-6 mb-2">
                                            <label for="inputAddress" class="form-label">Добавил HR</label>
                                            <div class="d-flex align-items-center mb-3 gap-2">
                                                <?php
                                                $idhr = $row['hr_dop'];
                                                $stmt40 = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :idhr");
                                                $stmt40->bindParam(':idhr', $idhr);
                                                $stmt40->execute();
                                                $row40 = $stmt40->fetch();
                                                if (!$row40['dir_img']) {
                                                    $imageHr = '/../assets/userimg.jpg';
                                                } else {
                                                    $imageHr = '/../' . $row40['dir_img'];
                                                }
                                                ?>
                                                <img src="<?= $imageHr ?>" alt=""
                                                     class="rounded-circle mb-2 border p-1 bg-light-subtle" width="60"
                                                     height="60">
                                                <p name="hr_id"
                                                   value="<?php echo $row40['id']; ?>"><?php echo $row['username']; ?>
                                                    <br>
                                                    <small><?php dataFormat($row['date']); ?></small></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  <!-- end row -->


                <div class="d-flex justify-content-between mb-4 col-md-12">
                    <?php if ($row40['position'] == 7) { ?>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    <?php } elseif ($row['creator'] == $user['id'] || $row['sogl'] == $user['id'] || $row['status'] == 6 || $user['id'] == 1) {
                        ?>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    <?php } ?>
                </div>
            </form>

            <div class="col-md-12">
                <ul class="timeline" id="timeline">
                </ul>

                <button id="show-comment-modal" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#commentModal">Добавить комментарий
                </button>
                <input type="hidden" value="<?php echo $url; ?>" name="worker_id">
                <!-- Модальное окно для добавления комментария -->
                <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="commentModalLabel">Добавить комментарий</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <textarea type="text" name="comment_text" id="new-comment" class="form-control" rows="3"
                                          placeholder="Введите комментарий"></textarea>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" value="<?php echo $url; ?>" name="worker_id">
                                <input type="hidden" value="<?= $user['id']; ?>" name="user_id">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                <button id="add-comment" class="btn btn-success">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function () {
                        let worker_id = $('input[name="worker_id"]').val();
                        let user_id = $('input[name="user_id"]').val();

                        // Загрузка комментариев при открытии страницы
                        $.get("/../hr/comment/get_comments.php", {worker_id: '<?php echo $url; ?>'}, function (data) {
                            let comments = JSON.parse(data);
                            comments.forEach(comment => {
                                $('#timeline').append(`
                <li class="timeline-item" data-id="${comment.id}">
                    <div class="timeline-info mr-4">
                        <span><i class="bi bi-calendar4-week mr-1"></i>${comment.created_at}</span>
                    </div>
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <h5>${comment.name}</h5>
                        <p>${comment.comment_text}</p>
                        <button class="edit-comment btn btn-sm btn-warning"><i class="bi bi-pen"></i></button>
                        <button class="delete-comment btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i></button>
                    </div>
                </li>
            `);
                            });
                        });

                        // Добавление комментария
                        $('#add-comment').click(function () {
                            let text = $('#new-comment').val().trim();
                            if (text) {
                                $.post("/../hr/comment/add_comment.php", {
                                    worker_id: worker_id,
                                    user_id: user_id,
                                    comment_text: text
                                }, function (response) {
                                    let res = JSON.parse(response);
                                    if (res.status === "success") {
                                        $('#timeline').append(`
                        <li class="timeline-item" data-id="${res.id}">
                            <div class="timeline-info mr-4">
                                <span><i class="bi bi-calendar4-week mr-1"></i> ${new Date().toLocaleDateString()}</span>
                            </div>
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h5><?php echo $user['name']; ?></h5>
                                <p>${res.comment_text}</p>
                                <button class="edit-comment btn btn-sm btn-warning"><i class="bi bi-pen"></i></button>
                                <button class="delete-comment btn btn-sm btn-danger"><i class="bi bi-trash3-fill"></i></button>
                            </div>
                        </li>
                    `);
                                        $('#new-comment').val('');
                                        $('#commentModal').modal('hide');
                                    }
                                });
                            }
                        });


                        // Редактирование комментария
                        // Открытие модального окна и загрузка текущего текста комментария
                        $(document).on('click', '.edit-comment', function () {
                            currentCommentItem = $(this).closest('.timeline-item');
                            commentId = currentCommentItem.attr('data-id');
                            let commentText = currentCommentItem.find('p').text();

                            $('#edit-comment-text').val(commentText); // Загружаем текст в textarea
                            $('#editCommentModal').modal('show'); // Открываем модальное окно
                        });
                        // Сохранение отредактированного комментария
                        $('#save-comment-btn').click(function () {
                            let newText = $('#edit-comment-text').val().trim();

                            if (newText) {
                                $.post("/../hr/comment/edit_comment.php", {
                                    comment_id: commentId,
                                    comment_text: newText
                                }, function (response) {
                                    let res = JSON.parse(response);
                                    if (res.status === "success") {
                                        currentCommentItem.find('p').text(res.comment_text);
                                        $('#editCommentModal').modal('hide'); // Закрываем модальное окно
                                    } else {
                                        alert("Ошибка: " + res.error);
                                    }
                                });
                            }
                        });

                        // Удаление комментария
                        // Открываем модальное окно и запоминаем комментарий для удаления
                        $(document).on('click', '.delete-comment', function () {
                            commentItem = $(this).closest('.timeline-item');
                            commentId = commentItem.attr('data-id');
                            $('#deleteCommentModal').modal('show');
                        });

                        // Удаление после подтверждения
                        $('#confirm-delete-btn').click(function () {
                            $.post("/../hr/comment/delete_comment.php", {comment_id: commentId}, function (response) {
                                let res = JSON.parse(response);
                                if (res.status === "success") {
                                    commentItem.fadeOut(300, function () {
                                        $(this).remove();
                                    }); // Красиво скрываем перед удалением
                                    $('#deleteCommentModal').modal('hide');
                                } else {
                                    alert("Ошибка: " + res.error);
                                }
                            });
                        });
                    });
                </script>

            </div>

            <?php if ($url != 0) { ?>
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
                    <?php while ($row = $stmt5->fetch()) {
                        if ($row["id"] == $url) {
                            ?>
                            <tr>
                                <th scope="row"><?= $row['id']; ?></th>
                                <th scope="row"><?= $row['date']; ?></th>
                                <td><?= $row['comment']; ?></td>
                                <td><?php
                                    $stmt2 = pdo()->prepare('SELECT `name` FROM `objects` WHERE `id` = :id');
                                    $stmt2->execute(['id' => $row['objects']]);
                                    $obj = $stmt2->fetch(PDO::FETCH_ASSOC);
                                    echo $obj['name']; ?></td>
                                <td><?php
                                    $stmt = pdo()->prepare('SELECT `name`, `username` FROM `users` WHERE `id` = :id');
                                    $stmt->execute(['id' => $row['meneger']]);
                                    $firstcoord = $stmt->fetch(PDO::FETCH_ASSOC);

                                    echo $firstcoord['name']; ?></td>
                            </tr>
                        <?php }
                    } ?>
                </table>
            <?php }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "<a> Access Denied </a>";
    } ?>
</div>

<script>
    $(document).ready(function () {
        // Добавляем обработчик события 'change' на элемент выпадающего списка
        $('#pay_type').on('change', function () {
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
    ageInput.addEventListener('input', function (event) {
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
    $(document).ready(function () {
        // Инициализация Select2 на элементе select
        $('#metro').select2();
    });
</script>

<script>
    document.getElementById('title_page').innerText = document.getElementById('candidate').value;
</script>


<!-- Модальное окно для редактирования комментария -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCommentModalLabel">Редактирование комментария</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea id="edit-comment-text" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" id="save-comment-btn" class="btn btn-success">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для подтверждения удаления -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCommentModalLabel">Удаление комментария</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этот комментарий?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" id="confirm-delete-btn" class="btn btn-danger">Удалить</button>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
