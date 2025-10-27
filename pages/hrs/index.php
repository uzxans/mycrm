<?php
try {
// 1. Получаем все статусы
$st = pdo()->prepare("SELECT * FROM status_hr WHERE id != -1000 ORDER BY id ASC");
$st->execute();
$statuses = $st->fetchAll(PDO::FETCH_ASSOC);

// 3. Получаем все объекты
$stmt3 = pdo()->prepare("SELECT `id`, `name`, `status_obj` FROM object WHERE status_obj != -1 ORDER BY id ASC");
$stmt3->execute();
$objects = $stmt3->fetchAll(PDO::FETCH_ASSOC);

// 4. Получаем все объекты
$stmt4 = pdo()->prepare("SELECT `id`, `name`, `dir_img` FROM users WHERE dop_user != 1 and position != 7 and position != 1 ORDER BY id ASC");
$stmt4->execute();
$hrs = $stmt4->fetchAll(PDO::FETCH_ASSOC);

// 2. Получаем всех кандидатов
$sql = "SELECT 
            h.*,
            o.name AS object_name,
            m.name_metro AS metro_name,
            uc.name AS creator_name,
            us.name AS sogl_name
        FROM hrapp AS h
        LEFT JOIN object AS o ON o.id = h.object
        LEFT JOIN metro AS m ON m.id = h.metro
        LEFT JOIN users AS uc ON uc.id = h.creator
        LEFT JOIN users AS us ON us.id = h.sogl
        ORDER BY h.id DESC
        LIMIT 30";


$stmt = pdo()->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Группируем по статусу
$grouped = [];
foreach ($rows as $row) {
    $grouped[$row['status']][] = $row;
}

?>



<div class="main">

    <div class="header_info">
        <h1>Управление заявками</h1>
    </div>
    <!-- End header_info -->
    <div class="container_crm">
        <!-- Desktop start filtrs -->
        <div class="filter_container mb-5">
            <p>Фильтр</p>
            <div class="filter">
                <div class="filter-input" id="filterInput">
                    <div class="tags" id="tags"></div>
                    <input type="text" placeholder="Фильтр..." readonly />
                    <button class="toggle" id="toggleBtn">
                        <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                    d="M18 18.5469L13.8983 14.4452M13.8983 14.4452C14.5999 13.7436 15.1565 12.9107 15.5362 11.994C15.9159 11.0773 16.1113 10.0948 16.1113 9.10254C16.1113 8.11031 15.9159 7.12781 15.5362 6.21111C15.1565 5.29442 14.5999 4.46149 13.8983 3.75988C13.1967 3.05827 12.3638 2.50172 11.4471 2.12202C10.5304 1.74231 9.54789 1.54687 8.55566 1.54688C7.56344 1.54688 6.58093 1.74231 5.66424 2.12202C4.74754 2.50172 3.91461 3.05827 3.213 3.75988C1.79604 5.17684 1 7.09865 1 9.10254C1 11.1064 1.79604 13.0282 3.213 14.4452C4.62996 15.8622 6.55178 16.6582 8.55566 16.6582C10.5596 16.6582 12.4814 15.8622 13.8983 14.4452Z"
                                    stroke="#596059" stroke-width="1.45354" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <div class="dropdown" id="dropdown">
                    <div class="tabs">
                        <button data-tab="status" class="active">СТАТУС</button>
                        <button data-tab="object">ВЫБОР ОБЪЕКТА</button>
                        <button data-tab="date">ДАТА</button>
                        <button data-tab="hr">HR</button>
                        <button data-tab="metro">МЕТРО</button>
                        <button data-tab="name">ФИО</button>

                    </div>

                    <div class="tab-content active" id="status">
                        <div class="title_tab_content">Статус для отбора</div>
                        <div class="option_box">
                            <div class="option status_work" data-value="Все">Все</div>
                            <?php foreach ($statuses as $status) { ?>
                                <div class="option <?php echo $status['color'];?>" data-value="<?php echo
                                $status['id'];?>"><?php echo $status['name_status'];?></div>
                           <?php }?>
                        </div>
                        <div class="btn_filter_null">
                            <button class="btn btn-defoult">Сохранить</button>
                            <button class="btn btn-defoult">Сбросить</button>
                            <button class="closeFilter btn btn-defoult">Закрыть</button>
                        </div>
                    </div>


                    <!--Object-->
                    <div class="tab-content" id="object">
                        <div class="title_tab_content">Выбор объекта</div>
                        <select class="form-select my-3">
                            <option data-value="">Выбрать</option>
                            <?php foreach ($objects as $object) { ?>
                                <option class="option" data-value="<?= htmlspecialchars($object['id']) ?>"><?= htmlspecialchars($object['name']) ?></option>
                            <?php }?>
                        </select>
                    </div>

                    <!--Data-->
                    <div class="tab-content" id="date">
                        <div class="title_tab_content">Выбор даты</div>
                        <input class="form-control flatpickr-input active my-3" id="daterange" type="text" readonly="readonly">
                    </div>

                    <!--Data-->
                    <div class="tab-content" id="hr">
                        <div class="title_tab_content">Выбор HR</div>
                        <select class="form-select my-3" aria-label="Default select example">
                            <option data-value="" selected>Выбрать</option>
                            <?php foreach ($hrs as $hr) { ?>
                            <option data-value="<?php echo $hr['id']?>"><?php echo $hr['name']?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <!--metro-->
                    <div class="tab-content" id="metro">
                        <div class="title_tab_content">Выбрать станцию</div>
                        <input class="form-control my-3" type="text" data-value="" placeholder="Поиск в ручную">
                        <div class="title_tab_content">Найти станцию на карте</div>
                        <img src="" alt="">
                    </div>


                    <!--FIO-->
                    <div class="tab-content name" id="name">
                        <div class="title_tab_content my-3">Поиск ФИО</div>
                        <input class="form-control" type="text" data-value="" data-text="">
                    </div>
                </div>
            </div>
        </div>
        <!--Desktop End filtrs -->

        <div class="d-flex my-tisket_btn_box">
            <div class="mb-2 d-flex my-tisket_btn">

                <!--Mobile Filter-->
                <div class="btn_mob_filter">
                    <button class="btn_srm" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
                        <img src="./accets/fonts/icon/filter-mob.svg" alt="">
                    </button>

                    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
                         aria-labelledby="offcanvasWithBothOptionsLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Настройка фильтра</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <!-- Контейнер активных фильтров -->
                            <div class="active-tags" id="activeTags"></div>

                            <div class="tab-content" id="status">
                                <div class="title_tab_content">Статус для отбора</div>
                                <div class="option_box">
                                    <div class="option status_fired" data-value="1">Соискатель</div>
                                    <div class="option status_work" data-value="100">Работает</div>
                                    <div class="option status_refusal" data-value="-1">Отказ</div>
                                    <div class="option status_rezerv" data-value="6">Резерв</div>
                                    <div class="option status_remind" data-value="5">Напомнить</div>
                                    <div class="option status_fired" data-value="10">Уволен</div>
                                    <div class="option status_blacklist" data-value="9">Черный список</div>
                                    <div class="option status_didnotcall" data-value="8">Не дозвон</div>
                                </div>
                            </div>

                            <div class="tab-content" id="object">
                                <div class="title_tab_content">Выбор объекта</div>
                                <select class="form-select my-3" aria-label="Default select example">
                                    <option selected>Выбрать</option>
                                    <option class="option" data-value="4">Обухова</option>
                                    <option class="option" data-value="7">ПМП</option>
                                </select>
                            </div>

                            <!--Data-->
                            <div class="tab-content" id="date">
                                <div class="title_tab_content">Выбор даты</div>
                                <input class="form-control flatpickr-input active my-3" id="daterange" type="text"
                                       readonly="readonly">
                            </div>

                            <!--Hr-->
                            <div class="tab-content" id="hr">
                                <div class="title_tab_content">Выбор HR</div>
                                <select class="form-select my-3" aria-label="Default select example">
                                    <option selected>Выбрать</option>
                                    <option data-value="4">Виктория</option>
                                    <option data-value="3">Руслан</option>
                                </select>
                            </div>

                            <!--metro-->
                            <div class="tab-content" id="metro">
                                <div class="title_tab_content">Выбрать станцию</div>
                                <input class="form-control my-3" type="text" data-value="" placeholder="Поиск в ручную">
                                <div class="title_tab_content">Найти станцию на карте</div>
                                <img src="" alt="">
                            </div>


                            <!--FIO-->
                            <div class="tab-content name" id="name">
                                <div class="title_tab_content">Поиск ФИО</div>
                                <input class="form-control" type="text" data-value="" data-text="">
                            </div>


                        </div>
                    </div>
                </div>
                <!--End-Mobile Filter-->

                <button class="btn_srm mr-2"><span>Мои заявки</span>
                    <img src="./accets/fonts/icon/my-tiket-mob.svg" alt="">
                </button>
                <button class="btn_srm"><span>Экспорт в Excel</span> <img src="./accets/fonts/icon/export-exel.svg" alt="">
                </button>
            </div>
            <div class="mb-2 btn-check-box">
                <input type="radio" class="btn-check" name="options-base" id="option5" autocomplete="off" checked>
                <label class="btn btn-white" for="option5"><img src="./accets/fonts/icon/kanban.svg" alt=""></label>

                <input type="radio" class="btn-check" name="options-base" id="option6" autocomplete="off">
                <label class="btn btn-white" for="option6"><img src="./accets/fonts/icon/mob-filter.svg" alt=""></label>
            </div>
        </div>


        <!-- Mob-card and List -->
        <?php include 'components/list.php' ?>
        <!-- End Mob-card and List -->



        <!--User-Kanban-->
        <div class="board-container">
            <div class="board" id="board">
                    <?php foreach ($statuses as $status): ?>
                        <?php $statusCode = $status['id']; ?>
                        <div class="column col" data-status="<?= $statusCode ?>">
                            <h3 class="status_box <?= $status['color'] ?>">
                                <?= htmlspecialchars($status['name_status']) ?>
                            </h3>
                            <button class="btn_add_user mb-3" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasRightAddNewUser-<?php echo $row['id']?>" aria-controls="offcanvasRightAddNewUser-<?php echo $row['id']?>" data-userid="-<?php echo $row['id']?>">
                                Добавить работника <i class="add-button-icon"></i>
                            </button>
                            <!--Add hrs new user-->
                            <?php include 'components/add_new_user_modal.php' ?>
                            <!--End Add hrs new user-->

                            <div class="cards">

                                <?php if (!empty($grouped[$statusCode])): ?>

                                    <?php foreach ($grouped[$statusCode] as $row): ?>
                                        <div class="card"
                                             data-status="<?= $row['status'] ?>"
                                             data-id="<?= $row['id'] ?>"
                                        type="button" data-bs-toggle="offcanvas"
                                             data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">

                                            <div class="name" data-name="<?php echo $row['candidate'];?>">
                                                <h3><?php echo $row['candidate'];?></h3>
                                                <div class="flag"><?php echo $row['country'];?></div>
                                            </div>
                                            <div class="cart_body">
                                                <li><img src="./accets/fonts/icon/data-icon.svg" alt=""><?= htmlspecialchars($row['department']) ?></li>
                                                <li data-phone="<?= htmlspecialchars($row['tel']) ?>"><img src="./accets/fonts/icon/phone-icon.svg" alt=""><?= htmlspecialchars($row['tel']) ?>
                                                </li>
                                                <li data-object="<?php echo htmlspecialchars($row['object']);?>"><img src="./accets/fonts/icon/building-icon.svg" alt=""><?= htmlspecialchars($row['object_name']) ?></li>
                                                <li data-hr="<?php echo $row['creator']?>"><img src="./accets/fonts/icon/hr-icon.svg" alt="">
                                                    <?php echo $row['creator_name']?></li>
                                                <li data-metro="<?php echo $row['metro']?>"><img src="./accets/fonts/icon/metro-icon.svg" alt=""><?php echo $row['metro_name'];?></li>
                                            </div>
                                            <hr class="hr-cart">
                                            <div data-date="<?= htmlspecialchars($row['date']) ?>" class="data"><?= htmlspecialchars($row['date']) ?></div>
                                        </div>
                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <div class="text-muted small">Нет данных</div>

                                <?php endif; ?>

                            </div>
                        </div>

                    <?php endforeach; ?>


            </div>

            <!-- Зоны автоскролла -->
            <div class="auto-scroll-zone left" id="zoneLeft"></div>
            <div class="auto-scroll-zone right" id="zoneRight"></div>

            <!-- Визуальные подсказки -->
            <div class="scroll-indicator left" id="indicatorLeft"></div>
            <div class="scroll-indicator right" id="indicatorRight"></div>
        </div>
        <!--End kanban-->




        <!--Modal HR INFO-->
    <?php include 'components/modal_user_info.php' ?>
        <!--END-Modal HR INFO-->
    </div>
    <?php } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }?>

    <!-- end container_crm -->
</div>
<!-- end main -->