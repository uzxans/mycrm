<div class="card mb-3">
    <div class="table-responsive">
        <table class="table white table-striped align-middle card-table">
            <thead class="table-light">
            <tr>
                <th>Id</th>
                <th>Кандидат Ф.И.О</th>
                <th>Объект</th>
                <th>Профессия</th>
                <th>Телефон номер</th>
                <th>Метро</th>
                <th>Менеджер объекта</th>
                <th>HR</th>
                <th>Статус</th>
                <th class="w-8"></th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $stmt->fetch()){ ?>
                <tr>
                    <td class="align-middle"><?php echo $row["id"] ?></td>
                    <!--                      <td class="align-middle">--><?php //echo  date_format(date_create($row["date"]), 'd.m.Y'); ?><!--</td>-->
                    <td class="align-middle"><a class=" link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover" href="hr/<?php echo $row["id"]?>"><?php echo $row["candidate"] ?></a>  <br>
                        <?php echo  date_format(date_create($row["date"]), 'd.m.Y'); ?>
                        <?php if ($user['otf_view']){?>
                            <br><a type="button" class="btn btn-sm btn-link" href="/hr/delete_hr.php?id=<?=$row['id']?>">Удалить</a><?php } ?>
                    </td>
                    <td class="align-middle"><?php echo $row["name"] ?></td>
                    <td class="align-middle"><?php echo $row["department"] ?></td>
                    <td class="align-middle"><a href="tel: <?php echo $row["tel"] ?>"><?php echo $row["tel"] ?></a></td>
                    <td class="align-middle"><?php
                        $metro= $row["metro"] ;
                        $stmt31 = pdo()->prepare("SELECT `id`, `name_metro` FROM `metro` WHERE `id`=$metro");
                        $stmt31->execute();
                        $row31 = $stmt31->fetch();
                        echo $row31['name_metro'];
                        ?></td>
                    <td class="align-middle"> <?php
                        $managerName = '';
                        $stmt3 = pdo()->prepare("SELECT `id`, `name` FROM `users`");
                        $stmt3->execute();
                        while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                            if ($row['sogl'] == $row3['id']) {
                                $managerName = $row3['name'];
                                break;
                            }
                        }
                        echo $managerName;
                        ?></td>
                    <td class="align-middle"><?php
                        $hrName= $row['creator'];
                        $stmt30 = pdo()->prepare("SELECT `id`, `name` FROM `users` WHERE `id`=$hrName");
                        $stmt30->execute();
                        $row30 = $stmt30->fetch();
                        echo $row30['name'];
                        ?></td>
                    <td class="align-middle"><?php if ($row['status']==0) { ?>
                            <span class="badge bg-danger">Уволен</span>
                        <?php } elseif ($row['status']==-1) { ?>
                            <span class="badge bg-danger">Отменено</span>
                        <?php } elseif ($row['status']==5) { ?>
                            <span class="badge bg-info mb-2">Напомнить</span>
                            <i class="badge
                             <?php
                            $current_date = date('Y-m-d');
                            if ($current_date==$row['date_napomnit']){
                                echo 'bg-danger';
                            } else{echo 'bg-info';}
                            ?>
                             "><?=$row['date_napomnit']?></i>
                        <?php } elseif ($row['status']==6) { ?>
                            <span class="badge bg-secondary">Резерв</span>
                        <?php } elseif ($row['status']==7) { ?>
                            <span class="badge bg-warning">Не дозвон</span>
                        <?php }elseif ($row['status']==8) { ?>
                            <span class="badge bg-black">Черный список</span>
                        <?php }
                        elseif ($row['status']==100) { ?>
                            <span class="badge bg-success">Работает</span>
                        <?php } elseif ($row['status']==101) { ?>
                            <span class="badge bg-success">Завершено</span>
                        <?php } elseif (explode('-',$row['sogl'])[$row['status']-1]==$user['id']) { ?>
                            <span class="badge bg-warning">У вас</span>
                        <?php } else { ?>
                            <span class="badge bg-primary">Соискатель <?php //echo $row["status"] ?></span>
                        <?php } ?>
                    </td>
                    <td class="align-middle">
                        <a class="btn btn-sm btn-outline-primary ali" href="hr-new/<?php echo $row["id"]?>">Перейти <i class="bi bi-arrow-right-short"></i></a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>