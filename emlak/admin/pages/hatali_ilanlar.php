<?php
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

// Hatalı ilan bildirimlerini okundu olarak işaretleme
$db->query("UPDATE mail_501 SET durumb='1' WHERE tipi=1 AND durumb=0");

?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Hatalı İlan Bildirimleri</h4>
            </div>
        </div>
        
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
                        <?php
                        if ($hesap->tipi != 2) {
                            $okundu = filter_input(INPUT_GET, 'okundu', FILTER_SANITIZE_NUMBER_INT);
                            $okunmadi = filter_input(INPUT_GET, 'okunmadi', FILTER_SANITIZE_NUMBER_INT);
                            $sil = filter_input(INPUT_GET, 'sil', FILTER_SANITIZE_NUMBER_INT);
                        
                            if ($okundu) {
                                $db->query("UPDATE mail_501 SET durum='1' WHERE id=$okundu");
                                header("Location:index.php?p=hatali_ilanlar");
                                exit;
                            } elseif ($okunmadi) {
                                $db->query("UPDATE mail_501 SET durum='0' WHERE id=$okunmadi");
                                header("Location:index.php?p=hatali_ilanlar");
                                exit;
                            } elseif ($sil) {
                                $db->query("DELETE FROM mail_501 WHERE id=$sil");
                                header("Location:index.php?p=hatali_ilanlar");
                                exit;
                            }
                        
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $idler = $_POST["id"] ?? [];
                                $action = $_POST["action"] ?? '';
                        
                                if (count($idler) > 0) {
                                    foreach ($idler as $id) {
                                        $id = (int) $id;
                                        if ($action === 'okundu') {
                                            $db->query("UPDATE mail_501 SET durum='1' WHERE id=$id");
                                        } elseif ($action === 'okunmadi') {
                                            $db->query("UPDATE mail_501 SET durum='0' WHERE id=$id");
                                        } elseif ($action === 'sil') {
                                            $db->query("DELETE FROM mail_501 WHERE id=$id");
                                        }
                                    }
                                }
                                header("Location:index.php?p=hatali_ilanlar");
                                exit;
                            }
                        }
                        ?>
                        <form action="" method="POST" id="SelectForm">
                            <input type="hidden" name="action" value="" id="action_hidden">    
                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th>Seç</th>
                                        <th>Üye</th>
                                        <th>İlan</th>
                                        <th>Tarih</th>
                                        <th>Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sorgu = $db->query("SELECT * FROM mail_501 WHERE tipi=1 ORDER BY durum ASC, id DESC LIMIT 0,500");
                                    while ($msg = $sorgu->fetch()) {
                                        $custom = json_decode($msg->customs, true);
                                        $ilan = $db->prepare("SELECT id, url, baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                        $ilan->execute([$custom["ilan_id"]]);
                                        if ($ilan->rowCount() > 0) {
                                            $ilan = $ilan->fetch();
                                            $baslik = $ilan->baslik;
                                        }
                                    ?>
                                    <tr <?= ($msg->durum == 0) ? 'style="background-color:#fde1e1"' : ''; ?>>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?= sanitizeInput($msg->id); ?>" type="checkbox" name="id[]" value="<?= sanitizeInput($msg->id); ?>">
                                                <label for="checkbox<?= sanitizeInput($msg->id); ?>"></label>
                                            </div>
                                        </td>
                                        <td><a href="index.php?p=uye_duzenle&id=<?= sanitizeInput($custom["acid"]); ?>" target="_blank"><?= sanitizeInput($msg->adsoyad); ?></a></td>
                                        <td><a href="index.php?p=ilan_duzenle&id=<?= sanitizeInput($ilan->id); ?>" target="_blank"><?= sanitizeInput($baslik); ?></a></td>
                                        <td><?= date("d.m.Y H:i", strtotime($msg->tarih)); ?></td>
                                        <td>
                                            <div id="myModal<?= sanitizeInput($msg->id); ?>" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title">Bildirim Detaylar</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>IP Adresi: <strong><?= sanitizeInput($msg->ip); ?></strong></p>
                                                            <p>İlan: <a href="index.php?p=ilan_duzenle&id=<?= sanitizeInput($ilan->id); ?>" target="_blank"><strong><?= sanitizeInput($baslik); ?></strong></a></p>
                                                            <p><?= sanitizeInput($msg->mesaj); ?></p>
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
                                                    <li><a href="#" data-toggle="modal" data-target="#myModal<?= sanitizeInput($msg->id); ?>">Detaylar</a></li>
                                                    <li><a href="index.php?p=hatali_ilanlar&okundu=<?= sanitizeInput($msg->id); ?>">Okundu</a></li>
                                                    <li><a href="index.php?p=hatali_ilanlar&sil=<?= sanitizeInput($msg->id); ?>">Sil</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
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
            text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Evet, Hemen!",
            closeOnConfirm: false
        }, function(){
            $("#SelectForm").submit();
        });
    }

    function TumuOkunmadi() {
        $("#action_hidden").val("okunmadi");
        swal({
            title: "Seçilenleri Okunmadı Olarak İşaretle",
            text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Evet, Hemen!",
            closeOnConfirm: false
        }, function(){
            $("#SelectForm").submit();
        });
    }

    function TumuSil() {
        $("#action_hidden").val("sil");
        swal({
            title: "Seçilenleri Sil",
            text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Evet, Hemen!",
            closeOnConfirm: false
        }, function(){
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