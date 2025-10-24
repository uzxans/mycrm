<?php
function getPageContent() {
    $requested_page = $_GET['page'] ?? 'home';
    $allowed_pages = ['profile', 'hrs', 'objects', 'admin'];

    if (!in_array($requested_page, $allowed_pages)) {
        $requested_page = '404';
    }

    $page_path = "pages/{$requested_page}/index.php";

    if (!file_exists($page_path)) {
        $page_path = "pages/404/index.php";
        if (!file_exists($page_path)) {
            return "pages/404.php";
        }
    }

    return $page_path;
}

// Проверяем AJAX запрос
if (isset($_GET['ajax']) && $_GET['ajax'] == 'true') {
    // Только контент для AJAX
    $page_content = getPageContent();
    include_once $page_content;
    exit;
}

// Обычная загрузка (полная страница)
$page_content = getPageContent();
include_once __DIR__.'/templates/header.php';?>
    <div class="item_container">
<?php include_once __DIR__.'/templates/sidebar.php'; ?>
<?php include_once $page_content; ?>
    </div>
    <!--item_container-->
<?php include_once __DIR__.'/templates/footer.php'; ?>