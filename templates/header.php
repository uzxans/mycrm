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

require_once __DIR__.'/../boot.php';
?><html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title id="title">SystemReg</title>
    <!-- Подключаем flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo root(); ?>/accets/style/css/main.css">
    <link rel="stylesheet" href="<?php echo root(); ?>/accets/fonts/style.css">

    <!-- Page loading styles -->
    <style>
        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 20px 30px;
            border-radius: 8px;
            z-index: 10000;
            font-size: 16px;
        }

        .loading:after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {
            0%, 20% { color: rgba(0,0,0,0); text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
            40% { color: white; text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
            60% { text-shadow: .25em 0 0 white, .5em 0 0 rgba(0,0,0,0); }
            80%, 100% { text-shadow: .25em 0 0 white, .5em 0 0 white; }
        }
    </style>
</head>
<body>
<!-- Page loading spinner -->
<div class="loading" id="loading">Загрузка...</div>


<div class="header_mob container py-2">
    <img src="./accets/img/logo/logo_mob.svg" alt="" class="logo_img_mob">
    <a href="/"><img src="./accets/icon/user_mob_prof.svg" alt=""></a>
</div>


