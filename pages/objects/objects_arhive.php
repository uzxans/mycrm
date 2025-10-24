<?php include_once __DIR__.'/components/header.php';?>
<?php if (True){//$user['stf_view']) { ?>
    <div class="d-flex flex-wrap justify-content-between mb-3  py-4">
        <h3 class="title">Список объектов</h3>
        <?php if ($user['ob_edit']) { ?>
            <button class="btn btn-primary d-print-none btn-md-sm" onclick="location='/objects/0'"><i class="bi bi-building-add"></i> Добавить объект</button>
        <?php } ?>
    </div>
    <?php
    try {
        // Запрос и вывод записей
        if (isset($_GET['pages'])){
            $page = $_GET['pages'];
        }else {
            $page = 1;
        }
        $kol = 30;  // количество записей для вывода
        if (isset($_GET['print'])) {$kol = 1000;}
        $art = ($page * $kol) - $kol;
        if ($_SERVER['QUERY_STRING'] == '') {
            $href = '?pages=';
        } elseif (str_contains($_SERVER['QUERY_STRING'],'pages=')) {
            $href = '?' . strstr($_SERVER['QUERY_STRING'], 'pages=', true) . 'pages=';
        } else {
            $href = '?' . $_SERVER['QUERY_STRING'] . '&pages=';
        }

        // Извлекаем уникальные имена менеджеров из таблицы объектов
        $managerQuery = pdo()->query("SELECT DISTINCT meneger FROM `objects`");
        $statArr = $managerQuery->fetchAll(PDO::FETCH_COLUMN);

        $prepare = "SELECT objects.id, objects.data, objects.meneger, objects.	numpersonal, objects.name, objects.data_end, objects.adress_obj, objects.tel_number, objects.inn FROM `objects`";
        $where = 'WHERE `meneger` LIKE \'%'.$_GET['meneger'].'%\' AND `status_obj` = 1 ';

        // Определяем все количество записей в таблице
        $stmt = pdo()->prepare("SELECT COUNT(*) FROM `objects`");
        $stmt->execute();
        $row = $stmt->fetch();
        $total = $row[0]; // всего записей
        $str_pag = ceil($total / $kol); // Количество страниц для пагинации
        if (isset($_GET['print'])) {
            $art = 0;
            $kol = $total;
        }

        $prepare = $prepare . $where .  "ORDER BY objects.id DESC LIMIT :art, :kol";
        $stmt = pdo()->prepare($prepare);
        $stmt->bindParam('art', $art, PDO::PARAM_INT);
        $stmt->bindParam('kol', $kol, PDO::PARAM_INT);
        $stmt->execute();

        ?>
        <div class="row d-print-none d-flex">
            <div class="col-md-4">
                <div class="mb-3 position-relative">
                    <div class="form-label">Менеджер объекта</div>
                    <select class="form-select" id="manager" name="manager" onchange="updateManagerSelection(this)">
                        <option value="">Все менеджеры</option>
                        <?php foreach ($statArr as $manager) { ?>
                            <option value="<?= htmlspecialchars($manager) ?>" <?php if ($_GET['meneger'] === $manager) echo 'selected' ?>>
                                <?php
                                $stmt2 = pdo()->prepare('SELECT `name`, `username` FROM `users` WHERE `id` = :id');
                                $stmt2->execute(['id' => htmlspecialchars($manager)]);
                                $firstcoord = $stmt2->fetch(PDO::FETCH_ASSOC);
                                echo $firstcoord['name'];
                                ?>
                            </option>
                        <?php } ?>
                    </select>
                    <script>
                        function updateManagerSelection(selectElement) {
                            var selectedValue = selectElement.value;
                            var newUrl = '/objects.php?meneger=' + encodeURIComponent(selectedValue);
                            location.href = newUrl;
                        }
                    </script>
                </div>
            </div>
            <div class="col-lg-2 col-md-12 align-self-end" align="center">
                <div class="mb-3 position-relative">
                    <button class="btn btn-primary" id="btn_srch"
                            onclick="location='/hrs.php?status='+document.getElementById('status').value+
                                          '&amp;objects='+document.getElementById('objects').value+
                                          '&amp;candidate='+document.getElementById('candidate').value
                                          //'&amp;manager='+document.getElementById('manager').value
                            ">Применить фильтр
                    </button>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
            <?php while($row = $stmt->fetch()){ ?>
                <div class="col-md-6 col-xl-4 ">
                    <a href="/object/<?php echo $row["id"]?>" class="card overflow-hidden border border-hover-primary  navbar-brand  text-decoration-none">
                        <div class="p-3 bg-transparent"><i class="bi bi-building"></i> <?=$row["name"] ?></div>
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-whatsapp"></i> <?=$row["tel_number"] ?></h5>
                            <p class="card-text"><i class="bi bi-geo-alt"></i> <?=$row["adress_obj"] ?></p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="progress mb-2" style="height:8px;" role="progressbar" aria-label="Animated striped example" aria-valuenow="<?=$row["numpersonal"] ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?=$row["numpersonal"] ?>%"><?=$row["numpersonal"] ?></div>
                            </div>
                            <div class="row justify-content-between align-items-center">
                                <div class="col-4 d-flex align-items-center">
                                    <img src="https://web-global.ru/wp-content/uploads/2024/01/520cf04fee256fba14b482296267ca5f.jpg" class="img-thumbnail card-img-top rounded-circle" style="width:50px;" alt="...">
                                    <img src="https://web-global.ru/wp-content/uploads/2024/01/520cf04fee256fba14b482296267ca5f.jpg" class="img-thumbnail card-img-top rounded-circle" style="width:50px; margin-left: -20px;" alt="...">
                                    <img src="https://web-global.ru/wp-content/uploads/2024/01/520cf04fee256fba14b482296267ca5f.jpg" class="img-thumbnail card-img-top rounded-circle" style="width:50px; margin-left: -20px;" alt="...">
                                    <h5 class="mb-0">+<?=$row["numpersonal"] ?></h5>
                                </div>
                                <div class="col-6 d-flex align-items-center">
                                    <?php if ($user['otf_edit']) { ?>
                                    <button type="button" class="btn btn-sm btn-link" href="/object/delete_obj.php?id=<?=$row['id']?>">Удалить</button><?php } ?>
                                    <p class="text-end mb-0">
                                        <?php
                                        $stmt2 = pdo()->prepare('SELECT `name`, `username` FROM `users` WHERE `id` = :id');
                                        $stmt2->execute(['id' => $row['meneger']]);
                                        $firstcoord = $stmt2->fetch(PDO::FETCH_ASSOC);
                                        echo $firstcoord['name'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>

        <div class="card-footer d-print-none">
            <nav aria-label="Page stfs">
                <ul class="pagination m-2">
                    <li class="page-item <?php if ($page==1) echo 'disabled' ?>">
                        <a class="page-link" href="<?php echo $href.$page-1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span> Предыдущая
                        </a>
                    </li>
                    <?php // формируем пагинацию
                    for ($i = 1; $i <= $str_pag; $i++){
                        $active = '';
                        if ($i==$page) $active = ' active';
                        echo '<li class="pages-item' .$active. '"><a class="pages-link" href="'.$href.$i.'"> '.$i.' </a></li>';
                    }?>
                    <li class="page-item <?php if ($page==$str_pag or $str_pag==0) echo 'disabled' ?>">
                        <a class="page-link" href="<?php echo $href.$page+1 ?>" aria-label="Next">Следующая
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <div class="col-auto">
                <button class="btn btn-secondary btn-sm d-print-none" onclick="window.print()">На печать</button>
            </div>
            <div class="col-auto">
                <form action="export_excel.php" method="post">
                    <input type="hidden" id="type" name="type" value="stf">
                    <input type="hidden" id="prepare" name="prepare" value="<?php echo $prepare; ?>">
                    <button type="submit" id="export" name="export" class="btn btn-secondary btn-sm d-print-none">Экспорт в Excel</button>
                </form>
            </div>
        </div>
    <?php }
    catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "<a> Access Denied </a>";
} ?>
<?php include_once __DIR__.'/components/footer.php';?>
