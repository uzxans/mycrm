<?php
require_once __DIR__.'/../check_auth.php';
    if (!$user) {
        header('Location: '. root(). '/');
        die;
    }
if (!$user) {
    header('Location: '. root() .'/index.php');
    die;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"> -->

    <!-- Подключаем flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

    <link rel="stylesheet" href="./style/css/main.css">
    <link rel="stylesheet" href="./accets/fonts/style.css">
    <title>Document</title>
</head>
<body>
<div class="header_mob container py-2">
    <img src="./accets/img/logo/logo_mob.svg" alt="" class="logo_img_mob">
    <a href="/"><img src="./accets/icon/user_mob_prof.svg" alt=""></a>
</div>

<div class="item_container">

          <?php include_once __DIR__ . '/../components/sidebar.php';?>


    <main class="col-md-10 ms-sm-auto col-lg-10" id="main">
        <div class="poeition-sticky mob_header">
            <div class="d-flex flex-nowrap p-2" id="header" style="">

                <a class="navbar-brand col-md-3 col-lg-2 me-0 px-2 fs-6 text-white logo_mobile" style="width: 10rem;" href="/director.php"><img class="" src="/assets/LogoV3.png" alt="logo" ></a>

                <div class="search_active">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Введите имя или телефон"
                               aria-label="search" aria-describedby="basic-addon1">
                        <button type="button" id="searchButton" class="btn btn-sm btn-outline-secondary  bg-body-tertiary" data-bs-toggle="modal" data-bs-target="#exampleModal1">
                            <i class="bi bi-search"></i>
                            Поиск</button>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-secondary p-2 d-md-none mob_search"><i class="bi bi-search"></i></button>

                <li class="nav-item ">
                    <?php
                    $stmt4 = pdo()->prepare("SELECT * FROM `users`");
                    $stmt4->execute();
                    $row4 = $stmt4->fetch();
                    if (!empty($user['dir_img'])) {
                        $imageSource = $user['dir_img'];
                    } else {
                        $imageSource = "/../assets/userimg.jpg";
                    }
                    ?>

                    <div class="dropdown ">
                        <a href="/lk" class="obj_img_user">
                            <img src="../<?php echo $imageSource; ?>" alt="">
                        </a>
                        <!--                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" style="left:-3rem;">-->
                        <!--                    <li><a class="dropdown-item" href="/lk">Профиль</a></li>-->
                        <!--                    <li><button class="dropdown-item btn btn-sm btn-outline-secondary" id="fullscreenBtn"><i class="bi bi-arrows-fullscreen"></i></button></li>-->
                        <!--                    <li><hr class="dropdown-divider"></li>-->
                        <!--                    <li>-->
                        <!--                        <form method="post" action="/do_logout">-->
                        <!--                            <button class="dropdown-item" type="submit" class="w3-bar-item w3-button bg-primary">Выйти</button>-->
                        <!--                        </form>-->
                        <!--                    </li>-->
                        <!--                </ul>-->
                    </div>
                </li>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Результат поиска</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="searchResults" class="card-container row"></div> <!-- Контейнер для отображения карточек -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>

