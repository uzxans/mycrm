<?php
require_once __DIR__ . '/check_auth.php';
if (!$user) {
    header('Location: ' . root() . '/');
    die;
}
include_once __DIR__ . '/components/header.php';
?>

<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

if (isset($_GET['print'])) echo 'onload="window.print();"' ?>

<?php if ($user['hr_view'] or $user['hr_edit'] or $user['hr_valid']) { ?>
    <div class=" py-3 gap-3">
        <h3 class="mb-2 text-center">Управление заявками</h3>
        <div class="d-flex gap-2 ">
            <?php if ($user['hr_edit']) { ?>
<!--                <button class="btn btn-primary d-print-none w-auto mu_btn_flex " onclick="location='/hrs/0'">-->
                <button class="btn btn-primary d-print-none w-auto mu_btn_flex " onclick="location='/hrs/new_add_hr'">
                    <i class="bi bi-person-add"></i>
                    Добавить работника
                </button>
            <?php } ?>
            <a class="btn btn-danger ml-xl-5 w-auto mu_btn_flex" href="/my-page.php"> <i class="bi bi-bell"></i>Мои заявки</a>
        </div>
    </div>
    <div class="d-md-none">
        <button class="btn btn-primary w-100 mb-3 fill_demo" id="toggle_filter">
            <i class="bi bi-filter-circle"></i> Фильтр
        </button>
    </div>
    <?php
    try {
        // Запрос и вывод записей
        if (isset($_GET['pages'])) {
            $page = $_GET['pages'];
        } else {
            $page = 1;
        }
        $kol = 30;  // количество записей для вывода
        if (isset($_GET['print'])) {
            $kol = 1000;
        }

        $art = ($page * $kol) - $kol;
        if ($_SERVER['QUERY_STRING'] == '') {
            $href = '?pages=';
        } elseif (str_contains($_SERVER['QUERY_STRING'], 'pages=')) {
            $href = '?' . strstr($_SERVER['QUERY_STRING'], 'pages=', true) . 'pages=';
        } else {
            $href = '?' . $_SERVER['QUERY_STRING'] . '&pages=';
        }

        //проверка GET параметров
        if (!isset($_GET['objects']) or $_GET['objects'] == 'all') {
            $object = "LIKE '%'";
        } else {
            $object = '= ' . $_GET['objects'];
        }
        if (!isset($_GET['status'])) {
            $_GET['status'] = 'all';
        }
        if (!isset($_GET['hrcreator'])) {
            $_GET['hrcreator'] = '';
        }

        if (!isset($_GET['metro'])) {
            $_GET['metro'] = '';
        }

        if (!isset($_GET['namepersonal'])) {
            $_GET['namepersonal'] = '';
        }
        if ($user['dop_user'] == 1) {
            if (!isset($_GET['dop_status_hr'])) {
                $_GET['dop_status_hr'] = '';
            }
        }


        if (!isset($_GET['date_create_from']) || empty($_GET['date_create_from'])) {
            $_GET['date_create_from'] = ''; // Оставляем пустым, если не указана
        } else {
            $_GET['date_create_from'] = date('Y-m-d', strtotime($_GET['date_create_from'])); // Преобразуем в формат Y-m-d
        }

        if (!isset($_GET['date_create_to']) || empty($_GET['date_create_to'])) {
            $_GET['date_create_to'] = date('Y-m-d'); // Если пусто, устанавливаем текущую дату
        } else {
            $_GET['date_create_to'] = date('Y-m-d', strtotime($_GET['date_create_to'])); // Преобразуем в формат Y-m-d
        }


//        // Обработка date_create_from
//        if (!isset($_GET['date_create_from']) || empty($_GET['date_create_from'])) {
//            $date_create_from = ''; // Оставляем пустым, если не указана
//        } else {
//            // Преобразуем в начало дня: Y-m-d 00:00:00
//            $date_create_from = date('Y-m-d 00:00:00', strtotime($_GET['date_create_from']));
//        }
//
//// Обработка date_create_to
//        if (!isset($_GET['date_create_to']) || empty($_GET['date_create_to'])) {
//            // Если пусто, устанавливаем текущую дату и время конца дня
//            $date_create_to = date('Y-m-d 23:59:59');
//        } else {
//            // Преобразуем в конец дня: Y-m-d 23:59:59
//            $date_create_to = date('Y-m-d 23:59:59', strtotime($_GET['date_create_to']));
//        }




        // получаем основные данные
        $prepare = "SELECT hrapp.id, `date`, `creator`, `candidate`, `tel`, `metro`, `dop_status_hr`,  `date_napomnit`,  objects.name, `department`, `status`, `sogl` , `country` FROM `hrapp` JOIN `objects` ON objects.id = hrapp.objects ";


        $stmt222 = pdo()->query('SELECT * FROM status_hr  WHERE id != -1000');
        $results = $stmt222->fetchAll(PDO::FETCH_ASSOC);

        $where_status = array();
        foreach ($results as $status) {
            $where_status[$status['id']] = "= " . $status['id']; // Сохраняем ID статуса в качестве ключа
        }

        $where = 'WHERE `status` ' . $where_status[$_GET['status']] . ' AND `objects` ' . $object .
            ' AND `metro` LIKE \'%' . $_GET['metro'] . '%\' ';

        if ($user['dop_user'] == 1) {
            $where .= ' AND `dop_status_hr` = ' . $user['id'];
        } else {
            $where .= ' AND `dop_status_hr` IS NULL ';
        }

        // Добавляем проверку по диапазону дат (>= и <= вместо LIKE)
        if (!empty($_GET['date_create_from'])) {
            $where .= ' AND `date` >= \'' . $_GET['date_create_from'] . '\'';
        }

        $where .= ' AND `date` <= \'' . $_GET['date_create_to'] . '\'';

        if (!empty($_GET['hrcreator'])) {
            $where .= ' AND (`creator` = ' . $_GET['hrcreator'] . ')';
        }

        if (!empty($_GET['namepersonal'])) {
            $where .= ' AND (`candidate` LIKE \'%' . $_GET['namepersonal'] . '%\')';
        }

        // Определяем все количество записей в таблице
        $stmt = pdo()->prepare("SELECT COUNT(*) FROM `hrapp` " . $where);
        $stmt->execute();
        $row = $stmt->fetch();
        $total = $row[0];
        $str_pag = ceil($total / $kol); // Количество страниц для пагинации

        // Определяем первую и последнюю запись на странице
        //$prepare = $prepare . $where . "ORDER BY hrapp.date DESC LIMIT :art, :kol";

        //$prepare = $prepare . $where . " ORDER BY hrapp.id DESC, hrapp.date DESC LIMIT :art, :kol";
        $prepare = $prepare . $where . " ORDER BY hrapp.date DESC, hrapp.id DESC LIMIT :art, :kol";

//        print_r($prepare);
        $stmt = pdo()->prepare($prepare);
        $stmt->bindParam('art', $art, PDO::PARAM_INT);
        $stmt->bindParam('kol', $kol, PDO::PARAM_INT);
        $stmt->execute();


        // получаем все филиалы для сортировки
        $stmt2 = pdo()->prepare("SELECT `id`, `name`, `status_obj` FROM `objects` WHERE `status_obj` = 0");
        $stmt2->execute();
        // получаем все метро станции
        $stmt3 = pdo()->prepare("SELECT * FROM `metro` ORDER BY `name_metro`");
        $stmt3->execute();

        // массив для статусов
        $statArr = array();
        foreach ($results as $status) {
            if ($status['id'] == -1000) {
                continue;
            } else {
                $statArr[$status['id']] = "" . $status['name_status']; // Пример формирования массива
            }
        }



        if ($user['dop_user'] == 1) {
            $where .= ' AND `dop_status_hr` = ' . $user['id'];
        } else {
            $where .= ' AND `dop_status_hr` IS NULL';
        }


        if ($user['dop_user'] == 1) {
            // Извлекаем уникальные имена менеджеров из таблицы hrs
            $managerQuery = pdo()->query("SELECT DISTINCT meneger FROM `objects` WHERE `dop_status` = " . $user['id']);
            $hrcreator = $managerQuery->fetchAll(PDO::FETCH_COLUMN);
        } else {
            // Извлекаем уникальные имена менеджеров из таблицы hrs
            $managerQuery = pdo()->query("SELECT DISTINCT creator FROM `hrapp`");
            $hrcreator = $managerQuery->fetchAll(PDO::FETCH_COLUMN);
        }


        ?>

        <div class="mb-5 bg-body-tertiary" id="filter">
            <div class="none_btn" id="close_filter">
                <i class="bi bi-x"></i>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <div class="mb-4">
                        <div class="form-label">Статус для отбора</div>
                        <select class="form-select" name="status" id="status">
                            <option value="all">ВСЕ</option>
                            <?php foreach ($statArr as $eng => &$rus) { ?>
                                <option value="<?= $eng ?>" <?php if (isset($_GET['status']) and $_GET['status'] == $eng) echo 'selected' ?>><?= $rus ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-3">
                    <div class="mb-lg">
                        <div class="form-label">Выбор объекта</div>
                        <select
                                class="form-select form-select"
                                name="object"
                                id="object">
                            <option value="all">ВСЕ</option>
                            <?php $query = $user['dop_user'] == 1
                                ? 'SELECT `id`, `name`, `status_obj` FROM `objects` WHERE `status_obj` = 0 AND `dop_status` = ' . $user['id'] . ' ORDER BY `name` ASC'
                                : 'SELECT `id`, `name`, `status_obj` FROM `objects` WHERE `status_obj` = 0 AND `dop_status`= 1 ORDER BY `name` DESC';
                            foreach (pdo()->query($query) as $row) {
                                ?>
                                <option value="<?= $row['id'] ?>"
                                    <?php if (isset($_GET['objects']) && $_GET['objects'] == $row['id']) echo 'selected' ?>>
                                    <?= $row['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-3">
                    <div class="form-label">Выбор HR</div>
                    <select class="form-select form-select" id="creator" name="creator">
                        <option value="">Все HR</option>
                        <?php foreach ($hrcreator as $manager) { ?>
                            <option value="<?= htmlspecialchars($manager) ?>"
                                <?php if (isset($_GET['hrcreator']) and $_GET['hrcreator'] == $manager) echo 'selected' ?>
                            >
                                <?php
                                $stmt2 = pdo()->prepare('SELECT `name`, `username` FROM `users` WHERE `id` = :id order by `name`');
                                $stmt2->execute(['id' => htmlspecialchars($manager)]);
                                $firstcoord = $stmt2->fetch(PDO::FETCH_ASSOC);
                                echo $firstcoord['name'];
                                ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 mb-3">
                    <div class="form-label">Выбор метро</div>
                    <select class="form-select form-select" name="metro" id="metro">
                        <option value="">Все</option>
                        <?php foreach (pdo()->query('SELECT `id`, `name_metro` FROM `metro` ORDER BY `name_metro`') as $row) { ?>
                            <option value="<?= $row['id'] ?>" <?php if (isset($_GET['metro']) and $_GET['metro'] == $row['id']) echo 'selected' ?>><?= $row['name_metro'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-4 col-md-4 mb-3">
                    <div class="position-relative">
                        <div class="form-label">ФИО работника</div>
                        <input class="form-control" type="text" id="namepersonal"
                               value="<?= $_GET['namepersonal'] ?? '' ?>">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 mb-3">
                    <div class="position-relative">
                        <div class="form-label">Дата создания с</div>
<!--                        <input class="form-control srch" type="date" id="date_create_from"-->
<!--                               value="--><?php //= !empty($_GET['date_create_from']) ? date('Y-m-d', strtotime($_GET['date_create_from'])) : '' ?><!--">-->


                                                <input class="form-control srch" type="date" id="date_create_from"
                               value="<?= $_GET['date_create_from'] ?? ''?>">
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-3">
                    <div class="mb-3 position-relative">
                        <div class="form-label">Дата создания до</div>
<!--                        <input class="form-control srch" type="date" id="date_create_to"-->
<!--                               value="--><?php //= !empty($_GET['date_create_to']) ? date('Y-m-d', strtotime($_GET['date_create_to'])) : '' ?><!--">-->

                        <input class="form-control srch" type="date" id="date_create_to"
                               value="<?= $_GET['date_create_to'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 align-self-center" align="center">
                    <div class="mb-3 position-relative">
                        <button class="btn btn-primary desk_kiltr" id="btn_srch"
                                onclick="updateUrlWithParameters()">Применить фильтр
                        </button>

                        <script>
                            function updateUrlWithParameters() {
                                const status = encodeURIComponent(document.getElementById('status')?.value || '');
                                const object = encodeURIComponent(document.getElementById('objects')?.value || 'all');
                                const candidate = encodeURIComponent(document.getElementById('creator')?.value || '');
                                const metro = encodeURIComponent(document.getElementById('metro')?.value || '');
                                const namepersonal = encodeURIComponent(document.getElementById('namepersonal')?.value || '');
                                const dateCreateFrom = encodeURIComponent(document.getElementById('date_create_from')?.value || '');
                                const dateCreateTo = encodeURIComponent(document.getElementById('date_create_to')?.value || '');

                                const query = `?status=${status}&object=${object}&hrcreator=${candidate}&metro=${metro}&namepersonal=${namepersonal}&date_create_from=${dateCreateFrom}&date_create_to=${dateCreateTo}`;

                                // Переход на новую страницу
                                location.href = query;
                            }

                        </script>
                    </div>
                    <script>
                        document.querySelectorAll('.srch').forEach(item => {
                            item.addEventListener("keypress", function (event) {
                                if (event.key === "Enter") {
                                    event.preventDefault();
                                    document.getElementById("btn_srch").click();
                                }
                            })
                        });
                    </script>
                </div>
            </div>
        </div>


        <?php include_once __DIR__ . '/hrs/table_user.php'; ?>

        <div class="card-footer mb-3 d-print-none">
            <nav aria-label="Page hrs">
                <ul class="pagination flex-wrap m-2">
                    <li class="page-item <?php if ($page == 1) echo 'disabled' ?>">
                        <a class="page-link" href="<?php echo $href . ($page - 1) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span> Предыдущая
                        </a>
                    </li>

                    <?php if ($page > 2) : ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $href . ($page - 1) ?>"><?php echo $page - 1 ?></a>
                        </li>
                    <?php endif; ?>


                    <?php if ($str_pag > 1): ?>
                        <li class="page-item <?php if ($page != 1 && $page != $str_pag) echo 'active' ?>">
                            <a class="page-link" href="<?php echo $href . $page ?>"><?php echo $page ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if ($page < $str_pag - 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $href . ($page + 1) ?>"><?php echo $page + 1 ?></a>
                        </li>
                    <?php endif; ?>


                    <?php if ($page < $str_pag): ?>
                        <li class="page-item <?php if ($page == $str_pag) echo 'active' ?>">
                            <a class="page-link" href="<?php echo $href . $str_pag ?>"><?php echo $str_pag ?></a>
                        </li>
                    <?php endif; ?>

                    <li class="page-item <?php if ($page == $str_pag or $str_pag == 0) echo 'disabled' ?>">
                        <a class="page-link" href="<?php echo $href . ($page + 1) ?>" aria-label="Next">Следующая
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <div class="col-auto">
                <button class="btn btn-secondary btn-sm d-print-none"
                        onclick="window.open('/hrs.php?print&amp;status='+document.getElementById('status').value+
                                                    '&amp;object='+document.getElementById('objects').value+
                                                    '&amp;candidate='+document.getElementById('candidate').value
                                                    //'&amp;manager='+document.getElementById('manager').value
                                    )">На печать
                </button>
            </div>
            <div class="col-auto">
                <form action="exel_hr.php" method="post">
                    <input type="hidden" id="type" name="type" value="hr">
                    <input type="hidden" id="prepare" name="prepare" value="<?php echo $prepare; ?>">
                    <button type="submit" id="export" name="export" class="btn btn-secondary btn-sm d-print-none">
                        Экспорт в Excel
                    </button>
                </form>
            </div>
        </div>
    <?php } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "<a> Access Denied </a>";
} ?>
</div>

<?php include_once __DIR__ . '/components/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.querySelector('#toggle_filter'); // Кнопка для открытия фильтра
        const closeButton = document.querySelector('#close_filter');  // Кнопка для закрытия фильтра
        const filterElement = document.querySelector('#filter');      // Сам фильтр

        if (toggleButton && closeButton && filterElement) {
            // Открыть/закрыть фильтр по кнопке
            toggleButton.addEventListener('click', function () {
                filterElement.classList.toggle('active');
            });

            // Закрыть фильтр по кнопке "x"
            closeButton.addEventListener('click', function () {
                filterElement.classList.remove('active');
            });
        } else {
            console.error('One or more elements are missing from the DOM.');
        }
    });
</script>