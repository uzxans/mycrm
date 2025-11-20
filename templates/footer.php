<div class="footer_mob">
    <ul class="footer_mob_box">
        <a class="footer_mob_box_a" href=""><i class="icon-big-application-white"></i></a>
        <a class="footer_mob_box_a" href=""><i class="analytics-icon"></i></a>
        <a class="footer_mob_box_a" href=""><i class="objects-analitic-icon"></i></a>
        <a class="footer_mob_box_a" href=""><i class="person2-icon"></i></a>
        <a class="footer_mob_box_a" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRightMenuMobile"
           aria-controls="offcanvasRightMenuMobile"><i class="list-large-icon"></i></a>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightMenuMobile"
             aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasRightLabel">
                    <img src="./accets/img/logo/logo.svg" alt="" class="logo_img">
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="mobile_menu_content">
                    <ul>
                        <li><a class="active" href="#"><i class="icon-admin"></i><span>Администрирование</span></a></li>
                        <li><a href=""><i class="search-big-icon"></i><span>Поиск</span></a></li>
                        <li><a href=""><i class="analytics-icon"></i><span>Аналитика</span></a></li>
                        <li><a href="/object.html"><i class="objects-analitic-icon"></i><span>Объекты</span></a></li>
                        <li><a href=""><i class="person2-icon"></i><span>HR</span></a></li>
                        <li><a href=""><i class="icon-big-application"></i><span>Мои заявки</span></a></li>
                        <li><a href=""><i class="icon-account"></i><span>Мой аккаунт</span></a></li>
                        <hr>
                        <li><a href=""><i class="video-little-icon"></i><span>Видео встреча</span></a></li>
                        <li><a href=""><i class="icon-knowledge"></i><span>База знаний</span></a></li>
                        <li><a href=""><i class="icon-support"></i><span>Тех. поддержка</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

    </ul>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

<script src="/new/accets/js/ajaxpage.js"></script>


<script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"
        integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE"
        crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>



<script src="/new/accets/js/main.js"></script>

<script>
    function loadAppropriateScript() {
        // Удаляем предыдущие скрипты если они есть
        const oldScripts = document.querySelectorAll('script[data-device-script]');
        oldScripts.forEach(script => script.remove());

        // Загружаем новый скрипт
        const script = document.createElement('script');
        script.setAttribute('data-device-script', 'true');

        if (window.innerWidth <= 480) {
            script.src = '/new/accets/js/mobile-main.js';
        } else {
            script.src = '/new/accets/js/desktop-main.js?v=1.14';
        }

        document.head.appendChild(script);
    }

    // Загружаем при старте
    loadAppropriateScript();

    // Обновляем при изменении размера окна (опционально)
    window.addEventListener('resize', function () {
        loadAppropriateScript();
    });
</script>
</body>
</html>