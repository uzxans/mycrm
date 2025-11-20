
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
    metro: [],
    hr: [],
    name: [],
    phone: [],
    date: []
};

    let datePicker = null;
    let offset = 0;

    const multiSelectTabs = ["status", "hr", "object", "metro", "name"];


    // ====================
    // Сбор фильтров для сервера
    // ====================
    function collectFiltersForServer() {
    const filters = {
    object: activeFilters.object || [],
    creator: activeFilters.hr || [],
    metro: activeFilters.metro || [],
    status: activeFilters.status || [],
    search: activeFilters.name && activeFilters.name.length > 0 ? activeFilters.name : []
};

    if (activeFilters.date && activeFilters.date.length > 0) {
    const dateFilter = activeFilters.date[0];
    if (dateFilter.from && dateFilter.to) {
    filters.date_from = dateFilter.from;
    filters.date_to = dateFilter.to;
}
}
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
    // Одиночный выбор
    activeFilters[tab] = [value];
    clearTags(tab);
    addTag(value, tab, text);
} else {
    // Множественный выбор
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
    // Обработка input
    // ====================
    document.querySelectorAll(".tab-content input:not(.flatpickr-input)").forEach(input => {
    let searchTimeout;
    input.addEventListener("input", () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
    const value = input.value.trim();
    const tab = input.closest(".tab-content").id;

    if (multiSelectTabs.includes(tab)) {
    // Множественный ввод через запятую
    const names = value.split(",").map(v => v.trim()).filter(v => v);
    activeFilters[tab] = names;
    clearTags(tab);
    names.forEach(n => addTag(n, tab, n));
} else {
    if (value) {
    activeFilters[tab] = [value];
    clearTags(tab);
    addTag(value, tab, value);
} else {
    activeFilters[tab] = [];
    clearTags(tab);
}
}
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
    activeFilters[tab] = [];
    if (tab === 'status') updateColumnsVisibility();
}

    // ====================
    // Основная функция загрузки
    // ====================
    const limit = 30; // например, по 20 записей за раз — подстрой под свой API

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

    // Собираем фильтры
    const filters = typeof collectFiltersForServer === 'function' ? collectFiltersForServer() : {
    object: [],
    hr: [],
    metro: [],
    status: [],
    search: ""
};

    // Добавляем пагинацию
    filters.offset = offset;
    filters.limit = limit;

    console.log("📤 Отправляем запрос:", {
    url: "/new/api/hrs/load.php",
    filters
});

    // Формируем тело запроса
    const body = new URLSearchParams();
    for (const [key, value] of Object.entries(filters)) {
    if (Array.isArray(value)) {
    value.forEach(item => {
    if (item) {
    body.append(`${key}[]`, item);
}
});
} else if (value !== undefined && value !== null && value !== "") {
    body.append(key, value);
}
}

    // Отправляем запрос
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

    // Если не loadMore — очищаем карточки
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

    // Удаление через 5 секунд
    setTimeout(() => {
    info.remove();
}, 3000);
}

    return;
}

    // Добавляем карточки в DOM
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

    // Увеличиваем offset
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
    // card.dataset.bsToggle = "offcanvas";
    // card.dataset.bsTarget=  `#offcanvas-${row.id}`;
    // card.dataset.ariaControls = "offcanvasRight";



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

    // Добавляем клик для открытия модалки
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

    // Заполняем поля формы
    for (const [key, value] of Object.entries(row)) {
    const input = form.querySelector(`[name="${key}"]`);
    if (input) input.value = value || '';
}

    // Заголовок
    const titleEl = offcanvasEl.querySelector('.offcanvas-title');
    if (titleEl) titleEl.textContent = row.full_name || 'Кандидат';

    const hrTitle = offcanvasEl.querySelector('.hrtitle');
    if (hrTitle) hrTitle.textContent = row.hr || 'HR';

        // Открываем Offcanvas
    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
    offcanvas.show();
    console.log('hello');
}



    // ====================
    // Кнопки "Сбросить" и "Закрыть"
    // ====================
    document.querySelectorAll('.btn_filter_null button').forEach(button => {
    if (button.textContent.includes('Сбросить')) {
    button.addEventListener('click', function() {
    activeFilters = { status: [], object: [], metro: [], hr: [], name: [], phone: [], date: [] };
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