<?php
  require_once __DIR__ . '/check_auth.php';
  if (!$user) {
    header('Location: '. root(). '/map.php');
    die;
  }
?>
 <?php
    //include_once __DIR__.'/top.php';
    include_once __DIR__ . '/templates/header.php'; // add </div> before </body>
  ?>
  <div class="container-lg py-4">

      <?php
        if ($user['adm']) {
          try {
            $stmt = pdo()->prepare("SELECT * FROM `branches` ");
            $stmt->execute();
            $stmt2 = pdo()->prepare("SELECT `id`, `name` FROM `users` ");
            $stmt2->execute();
            $row2 = array();
            while($row = $stmt2->fetch()) {
                $row2[$row['id']] = $row['name'];
            }
      ?>
            <div class="d-flex justify-content-between mb-3">
              <h3>Сотрудники в штате</h3>
              <button class="btn btn-primary" onclick="location='/user/0'">Новый пользователь</button>
            </div>
          <?php
            $stmt = pdo()->prepare("SELECT * FROM `users` ");
            $stmt->execute(); ?>
            <div class="card">
              <div class="table-responsive">
                <table class='table mb-0'> 
                  <thead class="table-light">
                    <tr>
                      <th>Id</th>
                      <th>Имя</th>
                      <th>Должность</th>
                      <th>Логин</th>
                      <th class="w-1"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($row = $stmt->fetch()){ ?>
                    <tr <?php if ($row["dop_user"] == 1) { ?> class="table-primary" <?php } ?>>
                      <td><?php echo $row["id"] ?></td>
                      <td><?php echo $row["name"] ?></td>
                      <td><?php
                          $status_user = $row["position"];
                          $stmt3 = pdo()->prepare("SELECT `id`, `role` FROM `position_user` WHERE `id` = :status_user");
                          $stmt3->bindParam(':status_user', $status_user, PDO::PARAM_INT); // Предполагая, что id является числовым типом данных
                          $stmt3->execute();
                          $row3 = $stmt3->fetch();
                          echo $row3["role"]  ?></td>
                      <td><?php echo $row["username"] ?></td>
                      <td><a href="user/<?php echo $row["id"]?>">Перейти</a></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <?php }
        catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
      } else {
          echo "<a> Access Denied </a>";
      } ?>
  </div>

<?php include_once __DIR__ . '/templates/footer.php'; ?>