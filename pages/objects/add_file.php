<div class="card mb-2">
    <div class="table-responsive">
        <table class="table p-2 fs--1  border-top border-200">
            <thead>
            <tr>
                <th>№</th>
                <th>Название файла</th>
                <th colspan="2">Файл</th>
                <?php if ($st=='') { ?>
                    <th class="d-print-none">Действия</th>
                <?php } ?>
            </tr>
            </thead>
            <tbody id="items">
            <?php $i=0;
            if ($url!=0) {
                while ($row4 = $stmt8->fetch(PDO::FETCH_ASSOC)) {
                    $i++;
                    if ($st!='') { ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$row4['name_file']?></td>z
                            <td><?=$row4['url_file']?></td>
                        </tr>
                    <?php } else { ?>
                        <tr id="item_old_<?=$i?>">
                            <td><?=$i?></td>
                            <td><p><?=$row4['name_file']?></p>
                                <!--                                <input type="text" required name="item---><?php //=$i?><!---name_file" value="--><?php //=$row4['name_file']?><!--" class="form-control">-->
                            </td>
                            <td>
                                <?php
                                if ($row4['url_file'] != ''){
                                    //Узнаем расширение документа
                                    $fileExtension = pathinfo($row4['url_file'], PATHINFO_EXTENSION);
                                    if (strtolower($fileExtension) == 'jpeg' || strtolower($fileExtension) == 'jpg' || strtolower($fileExtension) == 'png') { ?>
                                        <a href="<?=$row4['url_file'];?>" data-fancybox="gallery" data-caption="<?php echo $row4['name_file']; ?>">
                                            <img src="<?=$row4['url_file'];?>" height="100" alt="">
                                        </a>
                                    <?php } elseif(strtolower($fileExtension) == 'mp4'){ ?>
                                        <a href="<?= $row4['url_file']; ?>" data-fancybox >
                                        <video controls height="100">
                                            <source
                                                    src="<?=$row4['url_file'];?>"
                                                    type="video/mp4" />
                                            Your browser doesn't support HTML5 video tag.
                                        </video>
                                        </a>
                                    <?php  } else { ?>
                                        <a href="<?=$row4['url_file'];?>" data-fancybox>
                                            <i class="bi fs-2 bi-file-earmark-word"></i></a>
                                    <?php } }
                                ?>
                            </td>
                            <td>
                                <a class="btn btn-success" href="<?=$row4['url_file']?>" download><i class="bi bi-cloud-arrow-down"></i></a>
                            </td>
                            <?php

                            $lastPart = basename(((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); // Получает последнюю часть URL
                            ?>
                            <td><a class="btn btn-danger" href="delete_file.php?id=<?=$row4['id']?>&object_id=<?=$lastPart?>"><i class="bi bi-trash3"></i></a>
                            </td>

                        </tr>
                    <?php }
                }
            } ?>
            </tbody>
        </table>
        <input type="hidden" id="new_num" value="<?=$i?>">
        <input type="hidden" id="all_num" name="all_num" value="<?=$i?>">
    </div>
</div>

<?php if ($url==0 or $st=='') { ?>
    <div class="col-lg-4 col-md-6 col-12 mb-2 p-0">
        <a class="btn btn-primary text-white mb-2" onclick="addItem()">
            Добавить файл
        </a>
    </div>
<?php } ?>
<script>
    function addItem() {
        var new_num = parseInt(document.getElementById("new_num").value);
        new_num++;
        document.getElementById("new_num").value = new_num;
        var all_num = parseInt(document.getElementById("all_num").value);
        all_num++;
        document.getElementById("all_num").value = all_num;
        var html ='<td>'+new_num+'</td>\n' +
            '<td><input type="text" required name="item-'+new_num+'-name_file" class="form-control"></td>\n' +
            '<td><input type="file" required name="item-'+new_num+'-url_file" class="form-control"></td>\n' +
            '<td class="d-print-none"><a class="btn btn-outline-danger btn-sm" onclick="deleteItem(\'item_new_'+new_num+'\')">Удалить</a></td>\n';
        var row = document.getElementById("items").insertRow();
        row.setAttribute("id", "item_new_"+new_num);
        row.innerHTML = html;
    }
    function deleteItem(item_id) {
        var all_num = parseInt(document.getElementById("all_num").value);
        all_num--;
        document.getElementById("all_num").value = all_num;
        document.getElementById(item_id).remove();
    }
</script>