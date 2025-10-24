<?php

require_once __DIR__.'/../check_auth.php';
if (!$user) {
  header('Location: '. root() .'/index.php');
  die;
}
$url = basename($_SERVER['REQUEST_URI']); // получаем ID пользователя из URL
?>
 <?php
    include_once __DIR__ . '/../templates/header.php';  // add </div> before </body>
  ?>
  <div class="container-lg py-4">
      <?php if ($url==0) { ?>
        <h3>Создание новый кабинет сотруднику</h3>
      <?php } else { ?>
        <h3>Редактирование пользователя</h3>
      <?php } ?>

        <?php if ($user['adm']) { 

        // массив для прав доступа
        $acceses = array('adm'=>'Администратор',
                          'adr_view'=>'Адресная книга - просмотр всех записей',
                          'adr_edit'=>'Адресная книга - создание и редактирование всех записей',

                          'hr_view'=>'HR - просмотр всех заявок (по мимо своих)',
                          'hr_edit'=>'HR - создание и редактирование своих заявок',
                          'hr_valid'=>'HR - согласовние заявок',
                          'tdf_edit'=>'HR - изменить статус заявок',
                          'otf_view'=>'HR- удалить заявку',

                          'otf_edit'=>'Объекты - удалить заявку',
                        'ob_view'=>'Объекты - просмотр всех заявок (по мимо своих)',
                        'ob_edit'=>'Объекты - создание и редактирование своих заявок',

                        'director'=>'Анализ',
                        'prodajnik'=>'Отдел продаж',
                        'dop_user'=>'Скрытый пользователь',
//                        'otf_valid'=>'Колл-центр'
//                          'stf_view'=>'STF - просмотр всех заявок (по мимо своих)',
//                          'stf_edit'=>'STF - создание и редактирование своих заявок',
//                          'tdf_view'=>'TDF - просмотр всех заявок (по мимо своих)',
                        );
            try {
                if ($url != 0) { // если 0 - то новый пользователь
                    // проверяем наличие пользователя с указанным ID
                    $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `id` = :id");
                    $stmt->execute(['id' => $url]);
                    if (!$stmt->rowCount()) {
                        flash('Пользователь не найден', 'danger');
                        header('Location: '. root(). '/admin.php');
                        die;
                    }
                    $row = $stmt->fetch();
                } ?>
                <form method="post" action="../admin/do_edit_user.php">
                  <input type="hidden" id="id" name="id" value="<?php echo $url; ?>">
                  <div class="mb-3">
                    <label for="name" class="form-label">Имя пользователя</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name'] ?? ''; ?>" required>
                  </div>
                  <div class="mb-3">
                      <label for="position" class="form-label">Должность</label>
                      <select class="form-select mt-2" id="position" name="position">
                          <option value="2" <?php echo ($row['position'] == 2) ? 'selected' : '' ?>>Менеджер по объектам</option>
                          <option value="3" <?php echo ($row['position'] == 3) ? 'selected' : '' ?>>HR</option>
                          <option value="6" <?php echo ($row['position'] == 6) ? 'selected' : '' ?>>Директор</option>
                          <option value="1" <?php echo ($row['position'] == 1) ? 'selected' : '' ?>>Админ</option>
                          <option value="5" <?php echo ($row['position'] == 5) ? 'selected' : '' ?>>Менеджер отдела продаж</option>
                          <option value="7" <?php echo ($row['position'] == 7) ? 'selected' : '' ?>>Уволен</option>
                          <option value="8" <?php echo ($row['position'] == 8) ? 'selected' : '' ?>>HR-начальник</option>
                          <option value="9" <?php echo ($row['position'] == 9) ? 'selected' : '' ?>>Бригадир</option>
                      </select>
                  </div>
                  <div class="mb-3">
                    <label for="username" class="form-label">Логин</label>
                      <div class="input-group mb-3">
                          <span class="input-group-text">@</span>
                          <div class="form-floating">
                              <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['username'] ?? ''; ?>" required>
                              <label for="floatingInputGroup1">Username</label>
                          </div>
                      </div>
                  </div>
                  <div class="mb-3">
                    <label for="password" class="form-label">Новый пароль</label>
                    <?php if ($url == 0) { ?>
                      <input type="text" class="form-control" id="password" name="password" required>
                    <?php } else { ?>
                      <input type="text" class="form-control" id="password" name="password">
                    <?php } ?>
                  </div>
                  <div class="mb-3">
                    <label for="access_rights" class="form-label">Права доступа</label>
                    <div>
                      <?php foreach ($acceses as $eng => &$rus) { ?>
                        <label class="form-check">
                            <input type='hidden' value='0' name='<?=$eng?>'>
                            <input class="form-check-input" type="checkbox" name="<?=$eng?>" id="<?=$eng?>" <?php if (isset($row[$eng]) && $row[$eng] == 1) echo "checked"; ?>>
                            <span class="form-check-label"><?=$rus?></span>
                        </label>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="d-flex">
                    <button type="submit" class="btn btn-primary mr-3">Сохранить</button>
                      <?php if($url != 0){ ?>
                    <button type="submit" class="btn btn-danger">Удалить</button>
                    <?php }?>
                  </div>
                </form>



                <div class="card p-3">
                    <form action="" method="post">
                        <?php if($row['position'] === 9){
                            // Получаем текущие объекты бригадира
                            $currentObjects = [];
                            if ($url != 0) {
                                $stmtCurrent = pdo()->prepare("SELECT objects FROM brigadir WHERE id_user = ?");
                                $stmtCurrent->execute([$url]);
                                $currentObjects = $stmtCurrent->fetchAll(PDO::FETCH_COLUMN);
                            }

                            $stmt33 = pdo()->prepare("SELECT * FROM `objects`");
                            $stmt33->execute();
                            $row33 = $stmt33->fetchAll();
                            foreach ($row33 as $object) { ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="objects[]"
                                           value="<?php echo $object['id'];?>"
                                           id="object_<?php echo $object['id'];?>"
                                        <?php echo in_array($object['id'], $currentObjects) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="object_<?php echo $object['id'];?>">
                                        <?php echo $object['name'];?>
                                    </label>
                                </div>
                            <?php }
                        } ?>
                        <input type="hidden" value="<?php echo $row['id'] ?>" name="user_id">
                        <button type="submit" class="btn btn-primary my-3">Сохранить</button>
                    </form>
                </div>



                <?php
            }
            catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
            }
        } else {
            echo "<a> Access Denied </a>";
        } ?>
  </div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>