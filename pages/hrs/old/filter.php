<div class="col-12 col-md-12 mt-5">
    <div class="row g-2 gy-3">
        <div class="col-auto col-md-2">
            <div class="form-label">������ ��� ������</div>
            <select class="form-select" name="status" id="status" onchange="location='/hrs.php?status='+this.value+'&amp;objects='+document.getElementById('objects').value">
                <option value="all">���</option>
                <?php foreach ($statArr as $eng=>&$rus) { ?>
                    <option value="<?=$eng?>" <?php if (isset($_GET['status']) and $_GET['status']==$eng) echo 'selected'?>><?=$rus?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-auto col-md-2">
            <div class="form-label">����� �������</div>
            <select class="form-select" name="object" id="object" onchange="location='/hrs.php?status='+document.getElementById('status').value+'&amp;objects='+this.value">
                <option value="all">���</option>
                <?php while ($row = $stmt2->fetch()) { ?>
                    <option value="<?=$row['id']?>" <?php if (isset($_GET['objects']) and $_GET['objects']==$row['id']) echo 'selected'?>><?=$row['name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-auto col-md-2">
            <div class="form-label">����� HR</div>
            <select class="form-select form-select" id="creator" name="creator"
                    onchange="location='/hrs.php?status=' + document.getElementById('status').value + '&objects=' + document.getElementById('objects').value + '&hrcreator=' + this.value + '&metro=' + document.getElementById('metro').value"
            >
                <option value="all">��� HR</option>
                <?php foreach ($hrcreator as $manager) { ?>
                    <option value="<?= htmlspecialchars($manager) ?>" <?php if ($_GET['meneger'] == $manager) echo 'selected' ?>>
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
        <div class="col-auto col-md-2">
            <div class="form-label">����� �����</div>
            <select class="form-select form-select" name="metro" id="metro" onchange="location='/hrs.php?status=' + document.getElementById('status').value + '&objects=' + document.getElementById('objects').value +'&creator=' + document.getElementById('creator').value + '&metro=' + this.value">
                <option value="">���</option>
                <?php while($row=$stmt3->fetch()){ ?>
                    <option value="<?=$row['id']?>" <?php if (isset($_GET['metro']) and $_GET['metro']==$row['id']) echo 'selected'?>><?=$row['name_metro']?></option>
                    <!--                                            <option value="--><?php //=$row['id']?><!--">--><?php //=$row['name_metro']?><!--</option>-->
                <?php } ?>
            </select>
        </div>

        <div class="col-auto col-md-3">
            <div class="form-label">����� �� ������ ��������</div>
            <input class="form-control srch" type="text" name="tel" id="tel" value="<?=$_GET['tel']?>"/>
        </div>
        <div class="col-auto col-md-3 ">
            <div class="form-label">����� ���������</div>
            <div class="search-box">
                <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
                    <input
                        class="form-control search-input search form-control-sm" type="text" placeholder="�����"
                        aria-label="Search" value="<?=$_GET['candidate']?>"  />
                    <span class="fas fa-search search-box-icon"></span>
                </form>
            </div>
        </div>
        <div class="col-auto col-md-3 mt-5">
            <button class="btn btn-sm btn-phoenix-secondary bg-white hover-bg-100 me-2" id="btn_srch" onclick="applyFilter()">
                ��������� ������
            </button>
        </div>

        <script>
            function applyFilter() {
                var status = document.getElementById('status').value;
                var object = document.getElementById('objects').value;
                var tel = document.getElementById('tel').value;
                var metro = document.getElementById('metro').value;
                var date = document.getElementById('date').value;
                var candidate = document.getElementById('candidate').value;

                // �������� �� ���������� ������ �������� � ������ ������ ����� (�� ����� �����������)

                // �������� ���������� � URL
                location.href = '/hrs.php?status=' + status +
                    '&objects=' + object +
                    '&tel=' + tel +
                    '&metro=' + metro +
                    '&date=' + date +
                    '&candidate=' + candidate;
            }
        </script>

    </div>
    <script>
        document.querySelectorAll('.srch').forEach(item => {
            item.addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    applyFilter(); // �������� �������, ������� ������������ ����������
                }
            })
        });
    </script>
</div>