<?php
// PHP 8.3.17 özelliklerini kullanarak kodları güncelleyip güvenlik önlemleri ekleyelim
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

// Oturum başlatma
session_start();

// Veritabanı bağlantısı (PDO kullanarak)
$dsn = 'mysql:host=localhost;dbname=emlak_sitesi';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
];

try {
    $db = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Veritabanı bağlantısı başarısız.');
}

// Kullanıcı girdilerini sanitize etme
function sanitizeInput(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Yazılar</h4>
            </div>
        </div>
        <form action="" method="POST" id="SelectForm">
            <input type="hidden" name="action" value="" id="action_hidden">
            <div class="row">
                <div class="col-lg-12 col-md-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="btn-toolbar" role="toolbar">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=yazi_ekle';"> <i class="fa fa-plus"></i> Yeni Yazı Ekle</button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default m-t-20">
                        <div class="panel-body">
                            <?php
                            if ($hesap->tipi != 2) {
                                $sil = filter_input(INPUT_GET, 'sil', FILTER_SANITIZE_NUMBER_INT);
                                if ($sil !== null) {
                                    $db->prepare("DELETE FROM sayfalar WHERE site_id_555=501 AND id = ?")->execute([$sil]);
                                    header("Location:index.php?p=yazilar");
                                    exit;
                                }

                                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                    $idler = $_POST["id"] ?? [];
                                    $action = $_POST["action"] ?? '';

                                    if (count($idler) > 0) {
                                        foreach ($idler as $id) {
                                            $id = (int) sanitizeInput($id);
                                            if ($action === 'sil') {
                                                $db->prepare("DELETE FROM sayfalar WHERE site_id_555=501 AND id = ?")->execute([$id]);
                                            }
                                        }
                                    }

                                    header("Location:index.php?p=yazilar");
                                    exit;
                                }
                            }
                            ?>
                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th>Seç</th>
                                        <th>Başlık</th>
                                        <th>Url Adresi</th>
                                        <th>Tarih</th>
                                        <th>Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sorgu = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi = 1 AND dil = ? ORDER BY id DESC LIMIT 0, 500");
                                    $sorgu->execute([$dil]);
                                    while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <tr>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?= sanitizeInput((string)$row->id); ?>" type="checkbox" name="id[]" value="<?= sanitizeInput((string)$row->id); ?>">
                                                <label for="checkbox<?= sanitizeInput((string)$row->id); ?>"></label>
                                            </div>
                                        </td>
                                        <td><?= sanitizeInput($row->baslik); ?></td>
                                        <td><?= ($dayarlar->permalink === 'Evet') ? sanitizeInput($row->url) . '.html' : 'index.php?p=sayfa&id=' . sanitizeInput((string)$row->id); ?></td>
                                        <td><?= sanitizeInput($row->tarih); ?></td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="index.php?p=yazi_duzenle&id=<?= sanitizeInput((string)$row->id); ?>">Düzenle</a></li>
                                                    <li><a href="index.php?p=yazilar&sil=<?= sanitizeInput((string)$row->id); ?>">Sil</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
<link href="assets/vendor/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="assets/vendor/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.datatable').dataTable();
    });

    function TumuSil() {
        $("#action_hidden").val("sil");
        swal({
            title: "Seçilenleri Sil",
            text: "Bu işlemi gerçekten yapmak istiyor musunuz?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Evet, Hemen!",
            closeOnConfirm: false
        }, function() {
            $("#SelectForm").submit();
        });
    }
</script>