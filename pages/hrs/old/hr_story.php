<?php
require_once __DIR__ . '/../check_auth.php';
if (!$user) {
    header('Location: '. root() .'/index.php');
    die;
}
$url = basename($_SERVER['REQUEST_URI'], "?print"); // получаем ID заявки из URL
if ($url != 0) {
    $stmt = pdo()->prepare("SELECT `candidate` FROM `hrapp` WHERE `id` = :id");
    $stmt->execute(['id' => $url]);
    if (!$stmt->rowCount()) {
        flash('Заявка не найдена', 'danger');
        header('Location: '. root() .'/hrs.php');
        die;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HR заявка</title>
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/dashboard.css?v1</body>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,500,500i,600,600i,700,700i&amp;subset=latin-ext">
</head>
<body <?php if(isset($_GET['print'])) echo 'onload="window.print();"'?>>
<?php
//include_once __DIR__.'/../top.php';
include_once __DIR__.'/../sidebar.php';  // add </div> before </body>
?>
<div class="container-lg py-4">

    <?php if ($user['hr_view'] or $user['hr_edit'] or $user['hr_valid']) {

        try {
            if ($url != 0) { // показ готовой заявки
                // получение данных заявки для вывода
                $stmt = pdo()->prepare("SELECT `date`, `creator`, users.name AS username, `candidate`, branches.name AS objects, `department`, `manager`, `salary`, `form_dir`, `status`, `sogl`, `notes` FROM `hrapp` JOIN `branches` ON branches.id = hrapp.objects JOIN `users` ON users.id = hrapp.creator WHERE hrapp.id = :id");
                $stmt->execute(['id' => $url]);
                $row = $stmt->fetch();
                $sogl = explode('-',$row['sogl'])[$row['status']-1] ?? 0;
                $len = count(explode('-',$row['sogl']));
                $last = $len==$row['status'];
                // получение имен всех согласующих для вывода
                $stmt2 = pdo()->prepare("SELECT `coord_num`, users.name, `coord_date`, `status`, `note` FROM `hrcoord` JOIN `users` ON users.id = hrcoord.coord_name WHERE hrcoord.id = :id ORDER BY `coord_num`");
                $stmt2->execute(['id' => $url]);
            } else { // для новой заявки
                // получение всех наименований филиалов
                $stmt3 = pdo()->prepare("SELECT `id`, `name` FROM `branches`");
                $stmt3->execute();
                // получение имен пользователей для выбора согласующих
                $stmt4 = pdo()->prepare("SELECT `id`, `name` FROM `users` WHERE `hr_valid` = 1");
                $stmt4->execute();
                $row4 = array();
                while($rowloc = $stmt4->fetch()) {
                    $row4[$rowloc['id']] = $rowloc['name'];
                }
            } ?>
            <?php if ($url != 0) {
                if ($row['status']==0) { ?>
                    <td><span class="badge bg-danger">Отклонено</span></td>
                <?php } elseif ($row['status']==-1) { ?>
                    <td><span class="badge bg-danger">Отменено создателем</span></td>
                <?php } elseif ($row['status']==100) { ?>
                    <td><span class="badge bg-success">Согласовано</span></td>
                <?php } elseif ($row['status']==101) { ?>
                    <td><span class="badge bg-success">Завершено</span></td>
                <?php } elseif (explode('-',$row['sogl'])[$row['status']-1]==$user['id']) { ?>
                    <td><span class="badge bg-warning">У вас</span></td>
                <?php } } ?>
            <?php if (isset($sogl) and $row['creator']==$user['id'] and $row['status'] > 0 and $row['status'] < 101) { ?>
                <form method="post" action="do_closed_hr.php">
                    <input type="hidden" id="id" name="id" value="<?=$url?>">
                    <input type="hidden" id="status" name="status" value="<?=$row['status']?>">
                    <input type="hidden" id="creator" name="creator" value="<?=$row['creator']?>">
                    <div class="mb-3">
                        <label class="form-label">Выберите действие: </label>
                        <input type="text" class="form-control" id="note" name="note" placeholder="Комментарий">
                    </div>
                    <div class="d-flex mb-3">
                        <?php if ($row['status']==100) { ?>
                            <button type="submit" class="btn btn-success me-1" name="action" value="accept">Завершить</button>
                        <?php } ?>
                        <button type="submit" class="btn btn-danger" name="action" value="reject">Отменить</button>
                    </div>
                </form>
            <?php } ?>
            <?php if (isset($sogl) and $sogl==$user['id']) { ?>
                <form method="post" action="do_sogl_hr.php">
                    <input type="hidden" id="id" name="id" value="<?=$url?>">
                    <input type="hidden" id="last" name="last" value="<?=$last?>">
                    <input type="hidden" id="status" name="status" value="<?=$row['status']?>">
                    <input type="hidden" id="date" name="date" value="<?=$row['date']?>">
                    <input type="hidden" id="candidate" name="candidate" value="<?=$row['candidate']?>">
                    <input type="hidden" id="creator" name="creator" value="<?=$row['username']?>">
                    <input type="hidden" id="creator_id" name="creator_id" value="<?=$row['creator']?>">
                    <input type="hidden" id="object" name="object" value="<?=$row['objects']?>">
                    <input type="hidden" id="len" name="len" value="<?=$len?>">
                    <div class="mb-3">
                        <label for="note" class="form-label">Выберите действие: </label>
                        <input type="text" class="form-control" id="note" name="note" placeholder="Комментарий">
                    </div>
                    <div class="d-flex mb-3">
                        <button type="submit" class="btn btn-success me-1" name="action" value="accept">Согласовать</button>
                        <button type="submit" class="btn btn-danger" name="action" value="reject">Отказать</button>
                    </div>
                </form>
            <?php } ?>
            <form method="post" action="do_add_hr.php" enctype="multipart/form-data">
                <input type="hidden" id="id" name="id" value="<?php echo $url; ?>">
                <div class="mb-3 col-4">
                    <label for="candidate" class="form-label">Кандидат</label>
                    <input type="text" class="form-control" id="candidate" name="candidate" value="<?php echo $row['candidate'] ?? ''; ?>" required <?php if ($url!=0) echo 'disabled = "disabled"'?>>
                </div>
                <div class="row">
                    <div class="mb-3 col-4">
                        <label for="object" class="form-label">Объект</label>
                        <select class="form-select" id="object" name="object" required <?php if ($url!=0) echo 'disabled = "disabled"'?>>
                            <option value="">- не выбран -</option>
                            <?php if ($url!=0) { ?>
                                <option value="selected" selected><?=$row['objects']?></option>
                            <?php } else {
                                while($row3 = $stmt3->fetch()) { ?>
                                    <option value="<?=$row3['id']?>"><?=$row3['name']?></option>
                                <?php } } ?>
                        </select>
                    </div>
                    <div class="mb-3 col-4">
                        <label for="department" class="form-label">Отдел</label>
                        <input type="text" class="form-control" id="department" name="department" value="<?php echo $row['department'] ?? ''; ?>" required <?php if ($url!=0) echo 'disabled = "disabled"'?>>
                    </div>
                    <div class="mb-3 col-4">
                        <label for="manager" class="form-label">Руководитель</label>
                        <input type="text" class="form-control" id="manager" name="manager" value="<?php echo $row['manager'] ?? ''; ?>" required <?php if ($url!=0) echo 'disabled = "disabled"'?>>
                    </div>
                    <div class="mb-3 col-4">
                        <label for="salary" class="form-label">Зарплата</label>
                        <input type="text" class="form-control" id="salary" name="salary" value="<?php echo $row['salary'] ?? ''; ?>" required <?php if ($url!=0) echo 'disabled = "disabled"'?>>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="filename" class="form-label required">Анкета (*файл .pdf)</label><br>
                    <?php if ($url != 0) { //print_r($row);?>
                        <a href="<?=$row['form_dir']?>">Скачать прикреплённую анкету</a>
                    <?php } else { ?>
                        <input type="file" accept=".pdf,application/pdf" class="form-control" name="filename" required>
                    <?php } ?>
                </div>
                <div class="border p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <b>Создатель заявки</b><br>
                            <?php if ($url == 0) {
                                echo $user['name'];
                            } else {
                                echo $row['username'];
                            } ?>
                        </div>
                        <div class="text-h1 text-muted">
                            <i>
                                <?php if (isset($row)) echo date("d.m.Y",strtotime($row['date'])); ?>
                            </i>
                        </div>
                    </div>
                </div>
                <?php if ($url!=0) {
                    while ($row2 = $stmt2->fetch()) { ?>
                        <div class="border p-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <?php if ($row2['coord_num']=='100') { ?>
                                        <b>Итоговое решение</b><br>
                                    <?php } else { ?>
                                        <b>Согласующий №<?=$row2['coord_num']?></b><br>
                                    <?php } ?>
                                    <?=$row2['name']?>
                                    <p class="text-muted m-0"><?=$row2['note']?></p>
                                </div>
                                <?php if ($row2['status']=='') { ?>
                                    <div class="text-h1 text-muted">
                                        <i>В очереди</i>
                                    </div>
                                <?php } elseif ($row2['status']=='2') { ?>
                                    <div class="text-h1 text-muted">
                                        <i>- не требуется -</i>
                                    </div>
                                <?php } elseif ($row2['status']=='101') { ?>
                                    <div class="text-h1 text-success">
                                        <i><b>Завершен, <?=date("d.m.Y",strtotime($row2['coord_date']))?></b></i>
                                    </div>
                                <?php } elseif ($row2['status']=='-1') { ?>
                                    <div class="text-h1 text-danger">
                                        <i><b>Отменено создателем, <?=date("d.m.Y",strtotime($row2['coord_date']))?></b></i>
                                    </div>
                                <?php } elseif ($row2['status']=='1') { ?>
                                    <div class="text-h1 text-success">
                                        <i><b>Согласован, <?=date("d.m.Y",strtotime($row2['coord_date']))?></b></i>
                                    </div>
                                <?php } elseif ($row2['status']=='0') { ?>
                                    <div class="text-h1 text-danger">
                                        <i><b>Отказан, <?=date("d.m.Y",strtotime($row2['coord_date']))?></b></i>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else {
                    // количество согласующих по умолчанию
                    $soglmin=3;
                    $soglmax=15;
                    $sogl=$soglmin;
                    for ($i=1; $i<=$soglmax; $i++) { ?>
                        <div class="border p-3 <?php if ($i>$soglmin) echo 'd-none" id="sogl-'.$i ?>">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <b>Согласующий №<?=$i?></b><br>
                                    <select class="form-select" id="s<?=$i?>" name="sogl-<?=$i?>" <?php if ($i<=$soglmin) echo 'required'?>>
                                        <option value="">- не выбран -</option>
                                        <?php foreach($row4 as $id => &$name) {?>
                                            <option value="<?=$id?>"><?=$name?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="text-h1 text-muted">
                                    <i>В очереди</i>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <input type="hidden" id="sogl-100" name="sogl-100" value="<?php echo $user['id']; ?>">
                <?php } ?>
                <?php if ($url == 0) { ?>
                    <a href="javascript:" id="add_sogl" onclick="newSogl()">Добавить согласующего</a> <br>
                    <a href="javascript:" id="rem_sogl" onclick="delSogl()" class="text-danger d-none">Удалить согласующего</a>
                <?php } ?>
                <div class="mb-3 mt-3">
                    <label for="notes" class="form-label">Заметки</label>
                    <textarea class="form-control" name="notes" <?php if ($url!=0) echo 'disabled = "disabled"'?>><?php echo $row['notes'] ?? '';?></textarea>
                </div>
                <?php if ($url == 0) { ?>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                <?php } ?>
            </form>
            <input type="hidden" id="sogl" value="<?=$sogl?>">
            <input type="hidden" id="min_sogl" value="<?=$soglmin?>">
            <input type="hidden" id="max_sogl" value="<?=$soglmax?>">
        <?php }
        catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "<a> Access Denied </a>";
    } ?>
</div>
<script>
    function newSogl() {
        var minx = parseInt(document.getElementById("min_sogl").value);
        var maxx = parseInt(document.getElementById("max_sogl").value);
        var count = document.getElementById("sogl").value;
        if (count >= maxx){
            alert('Достигнуто максимальное число согласующих!');
            return false;
        }
        count++;
        document.getElementById("s"+count).required = true;
        document.getElementById("sogl-"+count).classList.remove("d-none");
        document.getElementById("sogl").value = count;
        if (count === minx+1) {
            document.getElementById("rem_sogl").classList.remove("d-none");
        }
        if (count === maxx) {
            document.getElementById("add_sogl").classList.add("d-none");
        }
    }
    function delSogl() {
        var minx = parseInt(document.getElementById("min_sogl").value);
        var maxx = parseInt(document.getElementById("max_sogl").value);
        var count = document.getElementById("sogl").value;
        if (count <= minx){
            alert('Достигнуто минимальное число согласующих!');
            return false;
        }
        document.getElementById("s"+count).required = false;
        document.getElementById("sogl-"+count).classList.add("d-none");
        count--;
        document.getElementById("sogl").value = count;
        if (count === minx) {
            document.getElementById("rem_sogl").classList.add("d-none");
        }
        if (count === maxx-1) {
            document.getElementById("add_sogl").classList.remove("d-none");
        }
    }
</script>
</div>
</body>
</html>