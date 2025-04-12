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

// Kullanıcı girdisini sanitize etme
function sanitizeInput(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Talep formlarını güncelle
$db->query("UPDATE mail_501 SET durumb = '1' WHERE tipi = 2 AND durumb = 0");

// GET verilerini sanitize etme
$okundu = filter_input(INPUT_GET, 'okundu', FILTER_SANITIZE_NUMBER_INT);
$okunmadi = filter_input(INPUT_GET, 'okunmadi', FILTER_SANITIZE_NUMBER_INT);
$sil = filter_input(INPUT_GET, 'sil', FILTER_SANITIZE_NUMBER_INT);

// Talep formu işlemleri
if ($hesap->tipi != 2) {
    if ($okundu !== null) {
        $db->prepare("UPDATE mail_501 SET durum = '1' WHERE id = ?")->execute([$okundu]);
        header("Location: index.php?p=talep_formlari");
        exit;
    } elseif ($okunmadi !== null) {
        $db->prepare("UPDATE mail_501 SET durum = '0' WHERE id = ?")->execute([$okunmadi]);
        header("Location: index.php?p=talep_formlari");
        exit;
    } elseif ($sil !== null) {
        $db->prepare("DELETE FROM mail_501 WHERE id = ?")->execute([$sil]);
        header("Location: index.php?p=talep_formlari");
        exit;
    }

    // POST verilerini işleme
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idler = $_POST["id"] ?? [];
        $action = $_POST["action"] ?? '';

        if (count($idler) > 0) {
            foreach ($idler as $id) {
                $id = (int) sanitizeInput($id);
                if ($action === 'okundu') {
                    $db->prepare("UPDATE mail_501 SET durum = '1' WHERE id = ?")->execute([$id]);
                } elseif ($action === 'okunmadi') {
                    $db->prepare("UPDATE mail_501 SET durum = '0' WHERE id = ?")->execute([$id]);
                } elseif ($action === 'sil') {
                    $db->prepare("DELETE FROM mail_501 WHERE id = ?")->execute([$id]);
                }
            }
        }

        header("Location: index.php?p=talep_formlari");
        exit;
    }
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Talep Formları</h4>
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
                                    <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onclick="TumuOkundu();">Okundu</a></li>
                                        <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default m-t-20">
                        <div class="panel-body">
                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th>Seç</th>
                                        <th>Adı Soyadı</th>
                                        <th>E-Posta</th>
                                        <th>Telefon</th>
                                        <th>Tarih</th>
                                        <th>Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sorgu = $db->query("SELECT * FROM mail_501 WHERE tipi = 2 ORDER BY durum ASC, id DESC LIMIT 0, 500");
                                    while ($msg = $sorgu->fetch()) {
                                        $custom = json_decode($msg->customs, true);
                                    ?>
                                    <tr <?= ($msg->durum == 0) ? 'style="background-color:#fde1e1"' : ''; ?>>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?= sanitizeInput((string)$msg->id); ?>" type="checkbox" name="id[]" value="<?= sanitizeInput((string)$msg->id); ?>">
                                                <label for="checkbox<?= sanitizeInput((string)$msg->id); ?>"></label>
                                            </div>
                                        </td>
                                        <td><?= sanitizeInput($msg->adsoyad); ?></td>
                                        <td><?= sanitizeInput($msg->email); ?></td>
                                        <td><?= sanitizeInput($msg->telefon); ?></td>
                                        <td><?= date("d.m.Y H:i", strtotime($msg->tarih)); ?></td>
                                        <td>
                                            <div id="myModal<?= sanitizeInput((string)$msg->id); ?>" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Talep Detaylar</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>IP Adresi: <strong><?= sanitizeInput($msg->ip); ?></strong></p>
                                                            <?php
                                                            foreach ($custom as $k => $v) {
                                                                if ($k != "acid") {
                                                            ?><p><?= sanitizeInput($k); ?>: <strong><?= sanitizeInput($v); ?></strong></p><?php
                                                                }
                                                            }
                                                            ?>
                                                            <p>Talebi ile ilgili detaylar;<br />
                                                            <strong><?= sanitizeInput($msg->mesaj); ?></strong></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#" data-toggle="modal" data-target="#myModal<?= sanitizeInput((string)$msg->id); ?>">Detaylar</a></li>
                                                    <li><a href="index.php?p=talep_formlari&okundu=<?= sanitizeInput((string)$msg->id); ?>">Okundu</a></li>
                                                    <li><a href="index.php?p=talep_formlari&sil=<?= sanitizeInput((string)$msg->id); ?>">Sil</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
<link href="assets/vendor/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">
<link href="assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
<style type="text/css">
</style>
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="assets/vendor/sweetalert/dist/sweetalert.min.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.datatable').dataTable();
});

function TumuOkundu() {
    $("#action_hidden").val("okundu");
    swal({
        title: "Seçilenleri Okundu Olarak İşaretleme",
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

function TumuOkunmadi() {
    $("#action_hidden").val("okunmadi");
    swal({
        title: "Seçilenleri Okunmadı Olarak İşaretle",
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

<script>
jQuery(document).ready(function() {
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true, // set focus to editable area after initializing summernote
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0], editor, welEditable);
        }
    });
});
</script>