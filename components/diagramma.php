<?php
$stmt5 = pdo()->prepare("SELECT * FROM `hrapp`");
$stmt5->execute();
$row5 = $stmt5->fetch();
$rowCount = $stmt5->rowCount();

$stmt11 = pdo()->prepare("SELECT COUNT(*) AS count FROM `hrapp` WHERE `status` = 100");
$stmt11->execute();
$row11 = $stmt11->fetch();
$rowCountPunkt = $row11['count'];

$stmt234 = pdo()->prepare("SELECT * FROM `objects`");
$stmt234->execute();
$row234 = $stmt234->fetch();
$rowCountObject = $stmt234->rowCount();


$stmt6 = pdo()->prepare("SELECT * FROM `users`");
$stmt6->execute();
$row6 = $stmt6->fetch();
$row_count = $stmt6->rowCount();
?>

<div class="row mb-2 mt-4">
    <div class="col-md-4 mb-2">
        <div class="row">
            <div class="col-12">
                <div class="card" style="background-color:rgba(255, 99, 132, 0.2);">
                    <div class="p-2">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Количество сотрудников</h5>
                            </div>
                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="bi bi-bag-check-fill"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3"><?=$row_count?></h1>
                        <div class="mb-0">
                            <span class="badge badge-success-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                            <span class="text-muted">В компании</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card" style="background-color:rgba(54, 162, 235, 0.2);">
                    <div class="p-2">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Количество обЪектов </h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3"><?=$rowCountObject?></h1>
                        <div class="mb-0">
                            <span class="badge badge-success-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                            <span class="text-muted">В компании</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <canvas id="myPieChart" width="200" height="200"></canvas>
    </div>
    <div class="col-md-4 mb-2">
        <div class="row">
            <div class="col-12">
                <div class="card" style="background-color:rgba(255, 206, 86, 0.2);">
                    <div class="p-2">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">База</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="bi bi-airplane-engines"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3"><?=$rowCount?></h1>
                        <div class="mb-0">
                            <span class="badge badge-success-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                            <span class="text-muted">Количество рабочих</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card" style="background-color:rgba(75, 192, 192, 0.2);">
                    <div class="p-2">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Действующие сотрудники</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="bi bi-box-seam-fill"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3"><?=$rowCountPunkt?></h1>
                        <div class="mb-0">
                            <span class="badge badge-success-light"> <i class="mdi mdi-arrow-bottom-right"></i></span>
                            <span class="text-muted">Работают в объектах</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Получение данных из PHP
    var total = <?= $row_count ?>;
    var rowCountHrapp = <?= $rowCountObject ?>;
    var rowCount = <?= $rowCount ?>;
    var rowCountPunkt = <?= $rowCountPunkt ?>;

    // Получение контекста 2d из canvas элемента
    var ctx = document.getElementById('myPieChart').getContext('2d');

    // Создание новой круговой диаграммы
    var myPieChart = new Chart(ctx, {
        type: 'pie', // Тип диаграммы
        data: {
            labels: ['Количество сотрудников', 'Количество обЪектов', 'База', 'Действующие сотрудники'], // Метки для данных
            datasets: [{
                label: 'Данные',
                data: [total, rowCountHrapp, rowCount, rowCountPunkt], // Данные из PHP
                backgroundColor: [ // Цвета для каждого сегмента
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [ // Цвета границ для каждого сегмента
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, // Адаптивность диаграммы
            plugins: {
                legend: {
                    position: 'top', // Позиция легенды
                },
                title: {
                    display: true,
                    text: 'Обзор данных' // Заголовок диаграммы
                }
            }
        }
    });
</script>