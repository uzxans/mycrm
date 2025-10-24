<script>
    function removeNonDigits(event) {
        // Получаем текущее значение поля ввода
        let input = event.target.value;
        // Удаляем все символы, кроме цифр
        input = input.replace(/\D/g, '');
        // Устанавливаем очищенное значение обратно в поле ввода
        event.target.value = input;
    }
</script>

<div class="d-md-none mb-3">
    <a class="btn btn-success w-100" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
        <i class="bi bi-filter-circle"></i> Фильр
    </a>
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Фильр</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <div class="form-label">Статус для отбора</div>
                        <select class="form-select" name="status" id="status">
                            <option value="all">ВСЕ</option>
                            <?php foreach ($statArr as $eng=>&$rus) { ?>
                                <option value="<?=$eng?>" <?php if (isset($_GET['status']) and $_GET['status']==$eng) echo 'selected'?>><?=$rus?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="mb-lg">
                        <div class="form-label">Выбор объекта</div>
                        <select class="form-select" name="object" id="object">
                            <option value="all">ВСЕ</option>
                            <?php foreach( pdo()->query('SELECT `id`, `name`, `status_obj` FROM `objects` WHERE `status_obj` = 0') as $row) { ?>
                                <option value="<?=$row['id']?>" <?php if (isset($_GET['objects']) and $_GET['objects']==$row['id']) echo 'selected'?>><?=$row['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-label">Выбор HR</div>
                    <select class="form-select form-select" id="creator" name="creator">
                        <option value="">Все HR</option>
                        <?php foreach ($hrcreator as $manager) { ?>
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
                </div>
                <div class="col-12 mb-3">
                    <div class="form-label">Выбор метро</div>
                    <select class="form-select form-select" name="metro" id="metro" >
                        <option value="">Все</option>
                        <?php while($row=$stmt3->fetch()){ ?>
                            <option value="<?=$row['id']?>" <?php if (isset($_GET['metro']) and $_GET['metro']==$row['id']) echo 'selected'?>><?=$row['name_metro']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-label">Поиск по номеру</div>
                    <input class="form-control" type="text" name="tel" id="tel" value="<?=$_GET['tel']?>" oninput="removeNonDigits(event)" />
                </div>
                <div class="col-12 mb-3">
                    <div class="position-relative">
                        <div class="form-label">Кандидат</div>
                        <input class="form-control" type="text" id="candidate" value="<?=$_GET['candidate']?>">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3 position-relative">
                        <div class="form-label">Дата создания с</div>
                        <input class="form-control srch" type="date" id="date_create_from" value="<?=$_GET['date_create_from']?>" onchange="check()">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3 position-relative">
                        <div class="form-label">Дата создания до</div>
                        <input class="form-control srch" type="date" id="date_create_to" value="<?=$_GET['date_create_to']?>" onchange="check()">
                    </div>
                </div>
                <script>
                    function check() {
                        var x = document.getElementById(date_create_from).value;
                        alert('The new value is: '+x);
                    }
                </script>
                <div class="col-12" align="center">
                    <div class="mb-3 position-relative">
                        <button class="btn btn-primary" id="btn_srch"
                                onclick="location='?status='+document.getElementById('status').value+
                                          '&amp;objects='+document.getElementById('objects').value+
                                          '&amp;hrcreator='+document.getElementById('creator').value+
                                          '&amp;metro='+document.getElementById('metro').value+
                                          '&amp;tel='+document.getElementById('tel').value+
                                          '&amp;candidate='+document.getElementById('candidate').value+
                                          '&amp;date_create_from='+document.getElementById('date_create_from').value+
                                          '&amp;date_create_to='+document.getElementById('date_create_to').value
                            ">Применить фильтр
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
