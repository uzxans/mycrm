class PageLoader {
    constructor() {
        this.contentArea = document.getElementById('content-area');
        this.loading = document.getElementById('loading');
        this.currentPage = this.getCurrentPage();

        this.init();
    }

    init() {
        // Обработка кликов по навигации
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = link.getAttribute('data-page');
                this.loadPage(page);
            });
        });

        // Обработка кнопки "Назад" в браузере
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.page) {
                this.loadPage(e.state.page, false);
            }
        });
    }

    getCurrentPage() {
        // Получаем текущую страницу из пути URL
        const path = window.location.pathname;
        const page = path.split('/').filter(part => part !== '').pop() || 'home';
        return page;
    }

    async loadPage(page, pushState = true) {
        this.showLoading();
        this.updateActiveLink(page);

        try {
            // Используем ЧПУ URL для AJAX запроса
            const response = await fetch(`/${page}?ajax=true`);

            if (!response.ok) {
                throw new Error('Ошибка загрузки страницы');
            }

            const content = await response.text();
            this.contentArea.innerHTML = content;

            // Обновляем URL в браузере (красивый ЧПУ)
            if (pushState) {
                history.pushState({ page: page }, '', `/${page}`);
            }

            this.currentPage = page;
            this.onContentLoaded();

        } catch (error) {
            console.error('Ошибка:', error);
            this.contentArea.innerHTML = '<div class="error">Ошибка загрузки страницы</div>';
        } finally {
            this.hideLoading();
        }
    }

    showLoading() {
        this.loading.style.display = 'block';
    }

    hideLoading() {
        this.loading.style.display = 'none';
    }

    updateActiveLink(page) {
        // Убираем активный класс у всех ссылок
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Добавляем активный класс текущей ссылке
        const activeLink = document.querySelector(`[data-page="${page}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }

    onContentLoaded() {
        // Инициализация скриптов после загрузки контента
        this.initForms();
        this.initDynamicElements();

        // Вызываем кастомное событие
        window.dispatchEvent(new CustomEvent('pageLoaded', {
            detail: { page: this.currentPage }
        }));
    }

    initForms() {
        // Обработка форм для AJAX отправки
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        });
    }

    initDynamicElements() {
        // Инициализация динамических элементов
        // Например, каруселей, модальных окон и т.д.
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const form = e.target;

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                body: formData
            });

            if (response.ok) {
                // Обработка успешной отправки формы
                const result = await response.text();
                // Можно обновить часть контента или показать сообщение
            }
        } catch (error) {
            console.error('Ошибка отправки формы:', error);
        }
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    window.pageLoader = new PageLoader();

    // Автоматически делаем активной текущую страницу
    const currentPage = window.pageLoader.getCurrentPage();
    window.pageLoader.updateActiveLink(currentPage);
});