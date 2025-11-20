<div class="sidebar activeSidebar">
    <div class="logo_box">
        <img src="./accets/img/logo/logo.svg" alt="" class="logo_img">
        <img src="./accets/img/logo/logo_mob.svg" alt="" class="logo_img_mob">
    </div>
    <!-- <button class="btn_menu"><img src="./accets/img/btn_menu.svg" alt=""></button> -->
    <button class="btn_menu">
        <div class="icon-container">
            <!-- Исходный SVG с точками -->
            <svg class="dots-icon" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 10 16"
                 fill="none">
                <rect width="4" height="4" rx="2" fill="#22BB77" />
                <rect x="6" width="4" height="4" rx="2" fill="#22BB77" />
                <rect y="6" width="4" height="4" rx="2" fill="#22BB77" />
                <rect x="6" y="6" width="4" height="4" rx="2" fill="#22BB77" />
                <rect y="12" width="4" height="4" rx="2" fill="#22BB77" />
                <rect x="6" y="12" width="4" height="4" rx="2" fill="#22BB77" />
            </svg>

            <!-- SVG со стрелкой (замените на свою стрелку) -->
            <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"
                 fill="none">
                <path d="M15 18L9 12L15 6" stroke="#22BB77" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round" />
            </svg>
        </div>
    </button>


    <!-- <div class="btn_menu_Mob btn btn-primary">
      <i class="bi bi-list"></i>
    </div> -->
    <div class="menu">
        <ul>
            <li><a href=""><i class="search-big-icon"></i><span>Поиск</span></a></li>
            <?php if ($user['adm']) { ?>
            <li><a class="" href="<?php poddomen()?>/admin" data-page="admin"><i class="icon-admin"></i>
                    <span>Админ</span></a></li>
            <?php } ?>
            <li><a href="<?php poddomen()?>/analitics"><i class="analytics-icon"></i><span>Аналитика</span></a></li>
            <?php if ($user['ob_view']) { ?>
            <li><a href="<?php poddomen()?>/objects" data-page="objects"><i class="objects-analitic-icon"></i><span>Объекты</span></a></li>
            <?php } ?>
            <?php if ($user['hr_view']) { ?>
            <li><a href="<?php poddomen()?>/hrs" data-page="hrs"><i class="person4-icon"></i><span>HR</span></a></li>
            <?php } ?>
            <li><a href="<?php poddomen()?>/my-page"><i class="icon-big-application-white"></i><span>Мои заявки</span></a></li>
            <li><a href="<?php poddomen()?>/profile" data-page="profile"><i class="icon-account"></i><span>Мой аккаунт</span></a></li>
            <hr>
            <li><a href=""><i class="video-little-icon"></i><span>Видео встреча</span></a></li>
            <li><a href=""><i class="icon-knowledge"></i><span>База знаний</span></a></li>
            <li><a href=""><i class="icon-support"></i><span>Тех. поддержка</span></a></li>
        </ul>
    </div>
    <!-- end menu -->

    <div class="exit_box">
        <form method="post" action="/new/do_logout.php">
            <button class="icon_btn">
                <i class="exit-icon"></i>
                <span class="alertcrm">Выход из системы</span>
            </button>
        </form>

        <button id="themeToggleBtn" class="icon_btn">
            <span class="alertcrm">Переключить тему</span>
            <i id="themeIcon" class="sun-icon"></i>
        </button>
    </div>

    <button class="btn btn-primary menu_btn_close_mob"><i class="bi bi-x-lg"></i></button>
</div>
<!-- end sidebar -->
