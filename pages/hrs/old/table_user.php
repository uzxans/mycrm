<div class="my_grid mb-3">
    <?php while($row = $stmt->fetch()){ ?>
        <div class="card_new_item item_grid bg-body-tertiary" onclick="location='/hr-new/<?=$row["id"]?>'">
            <div>
                <p class="status_my_hr"><?php if ($row['status']==11) { ?>
                        <span class="badge bg-danger shadow-danger">Уволен</span>
                    <?php } elseif ($row['status']==-1) { ?>
                        <span class="badge bg-danger shadow-danger">Отменено</span>
                    <?php } elseif ($row['status']==5) { ?>
                                <span class="badge bg-info shadow-info">Напомнить
                                        <i class="
                                                 <?php
                                        $current_date = date('Y-m-d');
                                        if ($current_date==$row['date_napomnit']){
                                            echo 'text-danger';
                                        } else{echo 'text-white';}
                                        ?>">
                                            <?php dataFormat($row['date_napomnit']) ?>
                                        </i>
                                </span>
                    <?php } elseif ($row['status']==6) { ?>
                        <span class="badge bg-secondary shadow-secondary">Резерв</span>
                    <?php } elseif ($row['status']==7) { ?>
                        <span class="badge bg-warning shadow-warning">Не дозвон</span>
                    <?php }elseif ($row['status']==8) { ?>
                        <span class="badge bg-black shadow-black">Черный список</span>
                    <?php }
                    elseif ($row['status']==100) { ?>
                        <span class="badge bg-success shadow-success">Работает</span>
                    <?php } elseif ($row['status']==101) { ?>
                        <span class="badge bg-success shadow-success">Завершено</span>
                    <?php } elseif (explode('-',$row['sogl'])[$row['status']-1]==$user['id']) { ?>
                        <span class="badge bg-primary shadow-primary">У вас</span>
                    <?php } else { ?>
                        <span class="badge bg-primary shadow-primary">Соискатель <?php //echo $row["status"] ?></span>
                    <?php } ?>

                    <?php if ($row['country']){ ?>
                        <span class="date"><i class="bi bi-flag-fill"></i> <?=$row["country"]?></span>
                    <?php } ?>
                </p>
                <h6 class="title_3 mt-3"><?=$row["candidate"] ?>
                </h6>
<!--                <hr class="border-primary opacity-50">-->
            </div>

            <ul class="list-group list-group-flush customer">
                <li class="list-group-item bg-body-tertiary p-0 mb-2">
                    <i class="bi bi-clipboard-check"></i>
                    <?php echo $row["department"] ?></li>
                <li class="list-group-item bg-body-tertiary p-0 mb-2">
                    <i class="bi bi-phone"></i>
                    <a style="font-size:14px;" class="link-offset-2 link-underline link-underline-opacity-0" href="tel:<?php echo $row["tel"] ?>" >

                        <span><?php
                            $formattedNumber = formatPhoneNumber($row["tel"]);
                            echo $formattedNumber ?></span>
                    </a>
                </li>
                <li class="list-group-item bg-body-tertiary p-0 mb-2 title_3">
                    <i class="bi bi-geo-alt-fill"></i>
                    <?= $row["name"] ?>
                </li>
                <li class="list-group-item bg-body-tertiary p-0 mb-2"><span class="badge bg-primary">Hr</span>
                    <?php
                    $hrName= $row['creator'];
                    $stmt30 = pdo()->prepare("SELECT `id`, `name` FROM `users` WHERE `id`=$hrName");
                    $stmt30->execute();
                    $row30 = $stmt30->fetch();
                    echo $row30['name'];
                    ?>
                </li>
                <li class="list-group-item bg-body-tertiary p-0 mb-2"><i class="bi bi-pin-map-fill"></i> <?php
                    $metro= $row["metro"] ;
                    $stmt31 = pdo()->prepare("SELECT `id`, `name_metro` FROM `metro` WHERE `id`=$metro");
                    $stmt31->execute();
                    $row31 = $stmt31->fetch();
                    echo $row31['name_metro'];
                    ?>   <div class="date"><i class="bi bi-calendar3"></i>
                        <?php dataFormat($row['date']); ?>
                    </div></li>
            </ul>
        </div>
    <?php } ?>
</div>

