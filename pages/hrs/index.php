<style>
    .btn-check:checked + .btn, :not(.btn-check) + .btn:active, .btn:first-child:active, .btn.active, .btn.show{
        background-color: #2b7;
    }

    .card.dragging {
        opacity: 0.5;
        transform: scale(0.97);
    }

    .column.drag-over {
        background-color: rgba(0, 123, 255, 0.1);
        border: 2px dashed #007bff;
    }

</style>
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

// 4. Получаем все users
$stmt4 = pdo()->prepare("SELECT `id`, `name`, `dir_img` FROM users WHERE position != 7 and position != 1 ORDER BY id ASC");
$stmt4->execute();
$hrAdd = $stmt4->fetchAll(PDO::FETCH_ASSOC);

// 4. Получаем все metro
$stmt5 = pdo()->prepare("SELECT `id`, `name_metro` FROM metro");
$stmt5->execute();
$metros = $stmt5->fetchAll(PDO::FETCH_ASSOC);

// 2. Получаем всех кандидатов
$sql = "SELECT 
            h.*,
            o.name AS object_name,
            m.name_metro AS metro_name,
            uc.name AS hr,
            us.name AS manager_name
        FROM hrapp AS h
        LEFT JOIN object AS o ON o.id = h.object
        LEFT JOIN metro AS m ON m.id = h.metro
        LEFT JOIN users AS uc ON uc.id = h.hr
        LEFT JOIN users AS us ON us.id = h.manager
        ORDER BY h.id DESC
        LIMIT 30";


$stmt = pdo()->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Группируем по статусу
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
                        <button data-tab="full_name">ФИО</button>

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
<!--                            <button class="btn btn-defoult">Сохранить</button>-->
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

                        <div class="btn_filter_null">
                            <!--                            <button class="btn btn-defoult">Сохранить</button>-->
                            <button class="btn btn-defoult">Сбросить</button>
                            <button class="closeFilter btn btn-defoult">Закрыть</button>
                        </div>
                    </div>

                    <!--Data-->
                    <div class="tab-content" id="date">
                        <div class="title_tab_content">Выбор даты</div>
                        <input class="form-control flatpickr-input active my-3" id="daterange" type="text" readonly="readonly">

                        <div class="btn_filter_null">
                            <!--                            <button class="btn btn-defoult">Сохранить</button>-->
                            <button class="btn btn-defoult">Сбросить</button>
                            <button class="closeFilter btn btn-defoult">Закрыть</button>
                        </div>
                    </div>

                    <!--HR-->
                    <div class="tab-content" id="hr">
                        <div class="title_tab_content">Выбор HR</div>
                        <select class="form-select my-3">
                            <option data-value="" selected>Выбрать</option>
                            <?php foreach ($hrAdd as $hruser) { ?>
                                <option data-value="<?php echo $hruser['id']?>"><?php echo $hruser['name']?></option>
                            <?php } ?>
                        </select>

                        <div class="btn_filter_null">
                            <!--                            <button class="btn btn-defoult">Сохранить</button>-->
                            <button class="btn btn-defoult">Сбросить</button>
                            <button class="closeFilter btn btn-defoult">Закрыть</button>
                        </div>
                    </div>

                    <!--metro-->
                    <div class="tab-content" id="metro">
                        <div class="title_tab_content">Выбрать станцию</div>
                        <input class="form-control my-3" type="text" data-value="" placeholder="Поиск в ручную">
                        <div class="title_tab_content">Найти станцию на карте</div>
                        <img src="" alt="">

                        <div class="btn_filter_null">
                            <button class="closeFilter btn btn-defoult">Закрыть</button>
                        </div>
                    </div>


                    <!--FIO-->
                    <div class="tab-content" id="full_name">
                        <div class="title_tab_content my-3">Поиск ФИО</div>
                        <input class="form-control" type="text" data-value="" data-text="">
                        <div class="btn_filter_null mt-3">
                            <button class="closeFilter btn btn-defoult">Закрыть</button>
                        </div>
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
                            <div class="tab-content name full_name" id="full_name">
                                <div class="title_tab_content my-3">Поиск ФИО</div>
                                <input class="form-control" type="text" data-value="" data-text="">
                                <div class="btn_filter_null mt-3">
                                    <button class="closeFilter btn btn-defoult">Закрыть</button>
                                </div>
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
                    <?php
                    // Статусы для исключения (укажите нужные ID)
                    $excludedStatuses = [-1]; // пример ID статусов для исключения
                    foreach ($statuses as $status): ?>
                        <?php $statusCode = $status['id'];
                        // Пропускаем исключенные статусы
                        if (in_array($statusCode, $excludedStatuses)) {
                            continue;
                        }
                        ?>
                        <div class="column col" data-status="<?= $statusCode ?>">
                            <h3 class="status_box <?= $status['color'] ?>">
                                <?= htmlspecialchars($status['name_status']) ?>
                            </h3>
                            <button class="btn_add_user mb-3" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasRightAddNewUser" aria-controls="offcanvasRightAddNewUser-<?php echo $row['id']?>" data-userid="-<?php echo $row['id']?>">
                                Добавить работника <i class="add-button-icon"></i>
                            </button>

                            <div class="cards">

                            </div>

                        </div>
                        <!--User info modal-->
                        <?php include 'components/modal_user_info.php' ?>

                        <!--Add hrs new user-->
                        <?php include 'components/add_new_user_modal.php' ?>
                        <!--End Add hrs new user-->
                    <?php endforeach; ?>

            </div>

            <!-- Зоны автоскролла -->
            <div class="auto-scroll-zone left" id="zoneLeft"></div>
            <div class="auto-scroll-zone right" id="zoneRight"></div>

            <!-- Визуальные подсказки -->
            <div class="scroll-indicator left" id="indicatorLeft"></div>
            <div class="scroll-indicator right" id="indicatorRight"></div>

            <!-- Load More button -->
            <div class="text-center my-4 allertInfoBtn" style="    z-index: 10;
    position: absolute;
    bottom: 0;
    left: 50%;">
                <button id="hr-load-more" class="btn btn-primary">Загрузить ещё</button>
            </div>
        </div>
        <!--End kanban-->
    </div>
    <?php } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }?>

    <!-- end container_crm -->
</div>
<!-- end main -->

<script>
    console.log('help 2025 - FIXED VERSION');

    function initDragAndDrop() {
        document.querySelectorAll(".card").forEach(card => {
            card.draggable = true;
            card.addEventListener("dragstart", e => {
                if (!card.dataset.id) card.dataset.id = Math.random().toString(36).substr(2, 9);
                e.dataTransfer.setData("id", card.dataset.id);
            });
        });

        document.querySelectorAll(".column").forEach(col => {
            col.addEventListener("dragover", e => e.preventDefault());
            col.addEventListener("drop", e => {
                e.preventDefault();

                const id = e.dataTransfer.getData("id");
                const card = document.querySelector(`.card[data-id="${id}"]`);
                if (!card) return;

                card.dataset.status = col.dataset.status;
                const cardsContainer = col.querySelector(".cards");
                const firstCard = cardsContainer.querySelector(".card");

                if (firstCard && firstCard !== card) {
                    cardsContainer.insertBefore(card, firstCard);
                } else {
                    cardsContainer.appendChild(card);
                }

                console.log(`🟢 Карточка ${id} перемещена в статус "${col.dataset.status}"`);
                $.ajax({
                    url: '/new/api/hrs/update_status.php',
                    method: 'POST',
                    data: {
                        id: id,
                        status: col.dataset.status
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log('Success:', data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        });

        console.log("✅ Drag-and-drop инициализирован");
    }

    document.addEventListener('DOMContentLoaded', function() {
        // ====================
        // DOM элементы
        // ====================
        const filterInput = document.getElementById("filterInput");
        const dropdown = document.getElementById("dropdown");
        const toggleBtn = document.getElementById("toggleBtn");
        const tabs = document.querySelectorAll(".tabs button");
        const tabContents = document.querySelectorAll(".tab-content");
        const tagsContainer = document.getElementById("tags");
        const loadMoreBtn = document.getElementById('hr-load-more');

        // ====================
        // Активные фильтры
        // ====================
        let activeFilters = {
            status: [],
            object: [],
            date: [],
            hr: [],
            metro: [],
            full_name: '',  // ⚠️ ИЗМЕНЕНО: строка вместо массива
            phone: []
        };

        let datePicker = null;
        let offset = 0;
        const limit = 30;

        const multiSelectTabs = ["status", "hr", "object", "metro"];

        // ====================
        // Сбор фильтров для сервера
        // ====================
        function collectFiltersForServer() {
            console.log("🔍 ACTIVE FILTERS:", activeFilters);

            const filters = {
                status: activeFilters.status || [],
                object: activeFilters.object || [],
                hr: activeFilters.hr || [],
                metro: activeFilters.metro || [],
                full_name: activeFilters.full_name || '',  // ⚠️ Просто строка
                date_from: activeFilters.date?.[0]?.from || '',
                date_to: activeFilters.date?.[0]?.to || ''
            };

            console.log("📤 FINAL FILTERS SENT TO SERVER:", filters);
            return filters;
        }

        // ====================
        // Обновление видимости колонок по статусу
        // ====================
        function updateColumnsVisibility() {
            document.querySelectorAll(".column").forEach(col => {
                const colStatus = col.dataset.status || 'unknown';
                if (activeFilters.status.length === 0) {
                    col.style.display = "";
                } else {
                    col.style.display = activeFilters.status.includes(colStatus) ? "" : "none";
                }
            });
        }

        // ====================
        // Инициализация flatpickr
        // ====================
        datePicker = flatpickr("#daterange", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "ru",
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const from = instance.formatDate(selectedDates[0], "Y-m-d");
                    const to = instance.formatDate(selectedDates[1], "Y-m-d");
                    const value = `${from} — ${to}`;
                    const tab = instance.input.closest(".tab-content").id;

                    activeFilters.date = [{ from, to }];
                    addTag(value, tab);
                    loadData(false);
                }
            }
        });

        // ====================
        // Открытие / закрытие фильтра
        // ====================
        filterInput?.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.classList.add("open");
        });

        toggleBtn?.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.classList.toggle("open");
        });

        document.addEventListener("click", (e) => {
            const closeBtn = document.querySelector(".closeFilter");
            if ((!filterInput?.contains(e.target) && !dropdown.contains(e.target)) ||
                (closeBtn && closeBtn.contains(e.target))) {
                dropdown.classList.remove("open");
            }
        });

        // ====================
        // Табы
        // ====================
        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                tabs.forEach(t => t.classList.remove("active"));
                tab.classList.add("active");
                tabContents.forEach(c => c.classList.remove("active"));
                document.getElementById(tab.dataset.tab).classList.add("active");
            });
        });

        // ====================
        // Клики по опциям фильтра
        // ====================
        document.querySelectorAll(".option").forEach(option => {
            option.addEventListener("click", () => {
                const value = option.dataset.value;
                const tab = option.closest(".tab-content").id;
                const text = option.textContent.trim();
                handleSelection(value, tab, text, option);
            });
        });

        function handleSelection(value, tab, text, option) {
            if (tab === "date") return;

            if (!activeFilters[tab]) activeFilters[tab] = [];

            if (value === "Все" || value === "Выбрать" || !value) {
                activeFilters[tab] = [];
                clearTags(tab);
            } else {
                if (!multiSelectTabs.includes(tab)) {
                    activeFilters[tab] = [value];
                    clearTags(tab);
                    addTag(value, tab, text);
                } else {
                    const index = activeFilters[tab].indexOf(value);
                    if (index === -1) {
                        activeFilters[tab].push(value);
                        addTag(value, tab, text);
                        option.classList.add("active");
                    } else {
                        activeFilters[tab].splice(index, 1);
                        removeTag(value, tab);
                        option.classList.remove("active");
                    }
                }
            }

            if (tab === 'status') updateColumnsVisibility();
            loadData(false);
        }

        // ====================
        // Обработка select
        // ====================
        document.querySelectorAll(".tab-content select").forEach(select => {
            select.addEventListener("change", () => {
                const selectedOptions = Array.from(select.selectedOptions);
                const tab = select.closest(".tab-content").id;
                if (!multiSelectTabs.includes(tab)) clearTags(tab);

                selectedOptions.forEach(selected => {
                    const value = selected.dataset.value || selected.value;
                    const text = selected.textContent.trim();

                    if (value && value !== "" && value !== "Выбрать") {
                        if (!activeFilters[tab].includes(value)) {
                            activeFilters[tab].push(value);
                            addTag(value, tab, text);
                        }
                    }
                });
                loadData(false);
            });
        });

        // ====================
        // ⚠️ ИСПРАВЛЕННАЯ Обработка input
        // ====================
        document.querySelectorAll(".tab-content input:not(.flatpickr-input)").forEach(input => {
            let searchTimeout;
            input.addEventListener("input", () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const value = input.value.trim();
                    const tab = input.closest(".tab-content")?.id;

                    console.log("⌨️ INPUT TYPING:", tab, "value:", value);

                    if (!tab) {
                        console.warn("⚠️ Не найден tab для input");
                        return;
                    }

                    // ⚠️ СПЕЦИАЛЬНАЯ ОБРАБОТКА для full_name
                    if (tab === 'full_name') {
                        activeFilters.full_name = value;  // Сохраняем как строку
                        clearTags(tab);
                        if (value) {
                            addTag(value, tab, value);
                        }
                        console.log("✅ full_name сохранен:", activeFilters.full_name);
                    } else if (multiSelectTabs.includes(tab)) {
                        // Для других множественных полей
                        const names = value.split(",").map(v => v.trim()).filter(v => v);
                        activeFilters[tab] = names;
                        clearTags(tab);
                        names.forEach(n => addTag(n, tab, n));
                    } else {
                        // Для остальных полей
                        if (value) {
                            activeFilters[tab] = [value];
                            clearTags(tab);
                            addTag(value, tab, value);
                        } else {
                            activeFilters[tab] = [];
                            clearTags(tab);
                        }
                    }

                    console.log("📊 Current activeFilters:", JSON.parse(JSON.stringify(activeFilters)));
                    loadData(false);
                }, 400);
            });
        });

        // ====================
        // Теги
        // ====================
        function addTag(value, tab, text) {
            if ([...tagsContainer.children].some(tag => tag.dataset.value === value && tag.dataset.tab === tab)) return;

            const tag = document.createElement("div");
            tag.className = "tag";
            tag.dataset.value = value;
            tag.dataset.tab = tab;
            tag.innerHTML = `${text || value} <span class="remove">×</span>`;

            tag.querySelector(".remove").addEventListener("click", () => {
                tag.remove();

                if (tab === "date") {
                    activeFilters.date = [];
                    if (datePicker) datePicker.clear();
                } else if (tab === "full_name") {
                    activeFilters.full_name = '';  // Очищаем строку
                } else {
                    activeFilters[tab] = (activeFilters[tab] || []).filter(v => v !== value);
                    const correspondingOption = document.querySelector(`.option[data-value="${value}"]`);
                    if (correspondingOption) correspondingOption.classList.remove('active');
                }

                if (tab === 'status') updateColumnsVisibility();
                loadData(false);
            });

            tagsContainer.appendChild(tag);
        }

        function removeTag(value, tab) {
            const tag = tagsContainer.querySelector(`.tag[data-value="${value}"][data-tab="${tab}"]`);
            if (tag) tag.remove();
        }

        function clearTags(tab) {
            [...tagsContainer.children]
                .filter(tag => tag.dataset.tab === tab)
                .forEach(tag => tag.remove());

            if (tab === 'full_name') {
                activeFilters[tab] = '';
            } else {
                activeFilters[tab] = [];
            }

            if (tab === 'status') updateColumnsVisibility();
        }

        // ====================
        // Основная функция загрузки
        // ====================
        async function loadData(isLoadMore = false) {
            const button = document.getElementById("hr-load-more");

            if (!isLoadMore) {
                offset = 0;
            }

            try {
                if (button) {
                    button.textContent = "Загрузка...";
                    button.disabled = true;
                }

                const filters = collectFiltersForServer();
                filters.offset = offset;
                filters.limit = limit;

                console.log("📤 Отправляем запрос с фильтрами:", filters);

                // Формируем тело запроса
                const body = new URLSearchParams();
                for (const [key, value] of Object.entries(filters)) {
                    if (Array.isArray(value)) {
                        if (value.length > 0) {
                            value.forEach(item => {
                                if (item) {
                                    body.append(`${key}[]`, item);
                                }
                            });
                        }
                    } else if (value !== undefined && value !== null && value !== '') {
                        body.append(key, value);
                    }
                }

                console.log("📦 BODY PARAMS:", Array.from(body.entries()));
                console.log("🌐 Request body string:", body.toString());

                const response = await fetch("/new/api/hrs/load.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body
                });

                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }

                const data = await response.json();
                console.log("📥 Ответ от сервера:", data);

                const rows = data.rows || data;

                if (!isLoadMore) {
                    document.querySelectorAll(".cards").forEach(c => c.innerHTML = "");
                }

                if (!rows || rows.length === 0) {
                    console.info("⚠️ Нет новых записей для загрузки.");

                    if (isLoadMore && button) {
                        const info = document.createElement("div");
                        info.className = "load-more-info";
                        info.textContent = "Все данные загружены";
                        info.style.textAlign = "center";
                        info.style.marginTop = "10px";
                        info.style.color = "#555";
                        button.parentElement.appendChild(info);

                        setTimeout(() => {
                            info.remove();
                        }, 3000);
                    }

                    return;
                }

                rows.forEach(row => {
                    const card = createCardElement(row);
                    const targetColumn = document.querySelector(`.column[data-status="${row.status}"] .cards`);
                    if (targetColumn) {
                        targetColumn.appendChild(card);
                    } else {
                        console.warn(`🟠 Не найдена колонка с data-status="${row.status}"`);
                    }
                });

                initDragAndDrop();
                offset += rows.length;
                console.log(`📈 Загружено ${rows.length} записей. Новый offset = ${offset}`);

            } catch (err) {
                console.error("❌ Ошибка loadData:", err);
                alert("Ошибка загрузки данных");
            } finally {
                if (button) {
                    button.textContent = "Загрузить ещё";
                    button.disabled = false;
                }
            }
        }

        function createCardElement(row) {
            const card = document.createElement("div");
            card.className = "card";
            card.dataset.id = row.id;
            card.dataset.status = row.status;

            card.innerHTML = `
            <div class="name" data-name="${row.full_name || ''}">
                <h3>${row.full_name || 'Не указано'}</h3>
                <div class="flag">${row.country || ''}</div>
            </div>
            <div class="cart_body">
                <li><img src="./accets/fonts/icon/data-icon.svg" alt="">${row.profession || ''}</li>
                <li data-phone="${row.phone || ''}"><img src="./accets/fonts/icon/phone-icon.svg" alt="">${row.phone || 'Не указано'}</li>
                <li data-object="${row.object || ''}"><img src="./accets/fonts/icon/building-icon.svg" alt="">${row.object_name || ''}</li>
                <li data-hr="${row.hr || ''}"><img src="./accets/fonts/icon/hr-icon.svg" alt="">${row.hr || ''}</li>
                <li data-metro="${row.metro || ''}"><img src="./accets/fonts/icon/metro-icon.svg" alt="">${row.metro_name || ''}</li>
            </div>
            <hr class="hr-cart">
            <div data-date="${row.date_add || ''}" class="data">${row.date_add || ''}</div>
        `;

            card.addEventListener('click', () => openCandidateModal(row));
            return card;
        }

        // ====================
        // Открытие модалки с данными кандидата
        // ====================
        function openCandidateModal(row) {
            const offcanvasEl = document.getElementById('offcanvasRight');
            if (!offcanvasEl) return console.error("❌ Не найден offcanvasRight");

            const form = offcanvasEl.querySelector('#candidateForm');
            if (!form) return console.error("❌ Не найдена форма candidateForm");

            for (const [key, value] of Object.entries(row)) {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) input.value = value || '';
            }

            const titleEl = offcanvasEl.querySelector('.offcanvas-title');
            if (titleEl) titleEl.textContent = row.full_name || 'Кандидат';

            const hrTitle = offcanvasEl.querySelector('.hrtitle');
            if (hrTitle) hrTitle.textContent = row.hr || 'HR';

            offcanvasEl.dataset.candidateId = row.id;
            loadCandidateComments(row.id);

            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
        }

        async function loadCandidateComments(candidateId) {
            const commentsList = document.getElementById('commentsList');
            if (!commentsList) return;

            commentsList.innerHTML = '<div class="text-muted small">Загрузка комментариев...</div>';

            try {
                const response = await fetch(`/new/api/hrs/get_comments.php?id=${encodeURIComponent(candidateId)}`);
                if (!response.ok) throw new Error('Ошибка загрузки комментариев');

                const data = await response.json();

                if (!data || data.length === 0) {
                    commentsList.innerHTML = '<div class="text-muted small">Комментариев пока нет</div>';
                    return;
                }

                commentsList.innerHTML = data.map(c => `
                <li class="comment_one">
                    <div class="comment_name">${c.manager || 'Неизвестный HR'}</div>
                    <div class="comment_body">
                        <textarea class="text_comment" name="comments" id="" row="3">${c.comments || 'Комментарий отсутствует'}</textarea>
                        <div class="comment_body_footer">
                            <div class="btn_box_comment">
                                <button class="btn" type="button"><img src="./accets/fonts/icon/comment_trashcan-outline.svg" alt=""></button>
                                <button class="btn" type="button"><img src="./accets/fonts/icon/comment_edit.svg" alt=""></button>
                            </div>
                            <div class="comment_date">
                                <img src="./accets/fonts/icon/calendar.svg" alt="">
                                (${c.date || '—'})
                            </div>
                        </div>
                    </div>
                </li>
            `).join('');

            } catch (err) {
                console.error('❌ Ошибка при загрузке комментариев:', err);
                commentsList.innerHTML = '<div class="text-danger small">Не удалось загрузить комментарии</div>';
            }
        }

        // ====================
        // Кнопки "Сбросить" и "Закрыть"
        // ====================
        document.querySelectorAll('.btn_filter_null button').forEach(button => {
            if (button.textContent.includes('Сбросить')) {
                button.addEventListener('click', function() {
                    activeFilters = {
                        status: [],
                        object: [],
                        metro: [],
                        hr: [],
                        full_name: '',
                        phone: [],
                        date: []
                    };
                    tagsContainer.innerHTML = '';
                    if (datePicker) datePicker.clear();
                    document.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                    document.querySelectorAll('input:not(.flatpickr-input)').forEach(i => i.value = '');
                    document.querySelectorAll('.option.active').forEach(o => o.classList.remove('active'));
                    document.querySelectorAll(".column").forEach(c => c.style.display = "");
                    loadData(false);
                });
            }

            if (button.textContent.includes('Закрыть')) {
                button.addEventListener('click', () => dropdown.classList.remove("open"));
            }
        });

        // ====================
        // Инициализация
        // ====================
        if (loadMoreBtn) loadMoreBtn.addEventListener('click', () => loadData(true));
        loadData(false);
    });
</script>

<!-- Toast Container (один раз на страницу) -->
<div aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastContainer"></div>
</div>

<script>
    // === ЗАЩИТА ОТ ПОВТОРНОГО ПОДКЛЮЧЕНИЯ ===
    if (window.candidateFormHandlerAttached) {
        console.warn('Обработчик формы уже есть — пропускаем');
    } else {
        window.candidateFormHandlerAttached = true;

        // === Уведомления ===
        function showToast(message, type = 'success', delay = 3000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const id = 'toast-' + Date.now();
            const bg = type === 'danger' ? 'bg-danger' : 'bg-success';

            container.insertAdjacentHTML('beforeend', `
                <div id="${id}" class="toast align-items-center text-white ${bg} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `);

            const toast = new bootstrap.Toast(document.getElementById(id), { delay });
            toast.show();
            document.getElementById(id).addEventListener('hidden.bs.toast', e => e.target.remove());
        }

        // === Обработчик формы (один на всю страницу) ===
        document.addEventListener('submit', async (e) => {
            const form = e.target.closest('#candidateForm');
            if (!form) return;

            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Сохранение...';
            }

            try {
                const res = await fetch('/new/api/hrs/update_candidate.php', {
                    method: 'POST',
                    body: new FormData(form)
                });

                if (!res.ok) throw new Error('Network error');
                const result = await res.json();

                if (result.success) {
                    showToast('Данные сохранены!', 'success');

                    // === ОБНОВЛЕНИЕ КАРТОЧКИ НА СТРАНИЦЕ ===
                    const candidateId = form.querySelector('input[name="id"]').value;
                    const card = document.querySelector(`.card[data-id="${candidateId}"]`);
                    if (card && result.candidate) {
                        updateCard(card, result.candidate);
                    }

                    bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRight'))?.hide();
                } else {
                    showToast('Ошибка: ' + result.message, 'danger');
                }
            } catch (err) {
                console.error(err);
                showToast('Ошибка соединения', 'danger');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Сохранить';
                }
            }
        });

        // === Функция обновления карточки ===
        function updateCard(card, data) {
            // ФИО
            const nameEl = card.querySelector('.name h3');
            if (nameEl) nameEl.textContent = data.full_name || data.candidate || 'Не указано';

            // Флаг (страна)
            const flagEl = card.querySelector('.flag');
            if (flagEl) flagEl.textContent = data.country || '';

            // Телефон
            const phoneLi = card.querySelector('li[data-phone]');
            if (phoneLi) {
                const phone = data.phone || data.tel || 'Не указано';
                phoneLi.setAttribute('data-phone', phone);
                phoneLi.querySelector('img').nextSibling.textContent = ' ' + phone;
            }

            // Объект
            const objectLi = card.querySelector('li[data-object]');
            if (objectLi) {
                objectLi.setAttribute('data-object', data.object || '');
                objectLi.querySelector('img').nextSibling.textContent = ' ' + (data.object_name || '');
            }

            // Метро
            const metroLi = card.querySelector('li[data-metro]');
            if (metroLi) {
                metroLi.setAttribute('data-metro', data.metro || '');
                metroLi.querySelector('img').nextSibling.textContent = ' ' + (data.metro_name || '');
            }

            // Дата
            const dateEl = card.querySelector('.data');
            if (dateEl) dateEl.textContent = data.date || '';

            // Перемещение в нужную колонку (если статус изменился)
            if (data.status) {
                const newColumn = document.querySelector(`.column[data-status="${data.status}"] .cards`);
                const currentColumn = card.parentElement;
                if (newColumn && currentColumn !== newColumn) {
                    newColumn.appendChild(card);
                }
            }

            // Визуальная подсветка
            card.style.transition = 'background 0.6s';
            card.style.background = '#d4edda';
            setTimeout(() => card.style.background = '', 800);
        }

        console.log('Обработчик формы candidateForm подключён (ОДИН РАЗ)');
    }
</script>




