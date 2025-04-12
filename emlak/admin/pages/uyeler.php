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

// Üyelik türlerini belirleme
$turler = explode(",", dil("UYELIK_TURLERI"));
$turu = filter_input(INPUT_GET, 'turu', FILTER_SANITIZE_NUMBER_INT);
$xturu = (int) $turu;

if ($turu === '0') {
    $turun = " AND turu=0";
    $turu = $turler[$turu] . " ";
} elseif ($turu === '1') {
    $turun = " AND turu=1";
    $turu = $turler[$turu] . " ";
} elseif ($turu === '2') {
    $turun = " AND turu=2";
    $turu = $turler[$turu] . " ";
} else {
    $turu = '';
    $turun = '';
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title"><?= sanitizeInput($turu); ?>Üyeler</h4>
            </div>
        </div>
        <form action="" method="POST" id="SelectForm">
            <input type="hidden" name="action" value="" id="action_hidden">
            <div class="row">
                <div class="col-lg-12 col-md-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="btn-toolbar" role="toolbar">
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default m-t-20">
                        <div class="panel-body">
                            <button type="button" class="btn btn-success waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=uye_ekle';">
                                <i class="fa fa-plus" aria-hidden="true"></i> <strong> Üye Ekle</strong>
                            </button>
                            <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=uyeler';">
                                <i class="fa fa-angle-right" aria-hidden="true"></i> <strong> Tüm Üyeler</strong>
                            </button>
                            <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=uyeler&turu=0';">
                                <i class="fa fa-angle-right" aria-hidden="true"></i> <strong> Bireysel Üyeler</strong>
                            </button>
                            <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=uyeler&turu=1';">
                                <i class="fa fa-angle-right" aria-hidden="true"></i> <strong> Kurumsal Üyeler</strong>
                            </button>
                            <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=uyeler&turu=2';">
                                <i class="fa fa-angle-right" aria-hidden="true"></i> <strong> Danışmanlar</strong>
                            </button>
                            <br><br>
                            <?php
                            if ($hesap->tipi != 2) {
                                $sil = filter_input(INPUT_GET, 'sil', FILTER_SANITIZE_NUMBER_INT);
                                if ($sil !== null) {
                                    $snc = $db->query("SELECT tipi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $sil)->fetch(PDO::FETCH_OBJ);
                                    if ($snc->tipi != 1) {
                                        $db->prepare("DELETE FROM hesaplar WHERE site_id_555=501 AND id=? OR kid=?")->execute([$sil, $sil]);
                                        $db->prepare("DELETE FROM sayfalar WHERE site_id_555=501 AND acid=?")->execute([$sil]);
                                        $db->prepare("DELETE FROM dopingler_501 WHERE acid=?")->execute([$sil]);
                                        $db->prepare("DELETE FROM dopingler_group_501 WHERE acid=?")->execute([$sil]);
                                        $db->prepare("DELETE FROM mesajlar_501 WHERE kimden=? OR kime=?")->execute([$sil, $sil]);
                                        $db->prepare("DELETE FROM mesaj_iletiler_501 WHERE gid=?")->execute([$sil]);
                                        $db->prepare("DELETE FROM onecikan_danismanlar_501 WHERE acid=?")->execute([$sil]);
                                        $db->prepare("DELETE FROM upaketler_501 WHERE acid=?")->execute([$sil]);
                                        $db->prepare("DELETE FROM engelli_kisiler_501 WHERE kim=? OR kimi=?")->execute([$sil, $sil]);
                                        $db->prepare("DELETE FROM favoriler_501 WHERE acid=?")->execute([$sil]);
                                        header("Location: index.php?p=uyeler&turu=" . $xturu);
                                    }
                                }
                            }
                            ?>
                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th style="display:none">Seç</th>
                                        <th>Üyelik Türü</th>
                                        <th>Adı Soyadı</th>
                                        <th>E-Posta</th>
                                        <th>Telefon</th>
                                        <th>Oluşturma Tarihi</th>
                                        <th>Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sorgu		= $db->query("SELECT id,kid,tipi,turu,unvan,email,telefon,olusturma_tarih,concat_ws(' ',adi,soyadi) AS adsoyad FROM hesaplar WHERE site_id_555=501 AND tipi!=2".$turun." ORDER BY id DESC LIMIT 0,500");
                                    while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                                        if ($row->turu == 2) {
                                            $uye = $db->prepare("SELECT id, unvan, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                            $uye->execute([$row->kid]);
                                            if ($uye->rowCount() > 0) {
                                                $uye = $uye->fetch(PDO::FETCH_OBJ);
                                                $name = ($uye->unvan == '') ? $uye->adsoyad : $uye->unvan;
                                            }
                                        } elseif ($row->turu == 1) {
                                            $name = ($row->unvan == '') ? $row->adsoyad : $row->unvan;
                                        } else {
                                            $uye = '';
                                            $name = '';
                                        }
                                    ?>
                                    <tr>
                                        <td style="display:none" class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?= sanitizeInput((string)$row->id); ?>" type="checkbox" name="id[]" value="<?= sanitizeInput((string)$row->id); ?>">
                                                <label for="checkbox<?= sanitizeInput((string)$row->id); ?>"></label>
                                            </div>
                                        </td>
                                        <td><b><?= sanitizeInput($turler[$row->turu]); ?></b></td>
                                        <td><b><a href="index.php?p=uye_duzenle&id=<?= sanitizeInput((string)$row->id); ?>"><?= sanitizeInput($row->adsoyad); ?></a></b><?= ($name != '') ? '<br>' . sanitizeInput($name) : ''; ?></td>
                                        <td><?= ($row->tipi == 1) ? '---' : sanitizeInput($row->email); ?></td>
                                        <td><?= sanitizeInput($row->telefon); ?></td>
                                        <td><?= date("d.m.Y H:i", strtotime($row->olusturma_tarih)); ?></td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="index.php?p=uye_duzenle&id=<?= sanitizeInput((string)$row->id); ?>">Görüntüle</a></li>
                                                    <?php if ($row->tipi == 0) { ?><li><a href="index.php?p=uyeler&turu=<?= sanitizeInput((string)$xturu); ?>&sil=<?= sanitizeInput((string)$row->id); ?>">Sil</a></li><?php } ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    if ($xturu == 2) {
                                        $sorgu = $db->query("SELECT * FROM danismanlar_501 ORDER BY id DESC LIMIT 0, 500");
                                        while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <tr id="danisman<?= sanitizeInput((string)$row->id); ?>">
                                        <td class="mail-select" style="display:none">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkboxx<?= sanitizeInput((string)$row->id); ?>" type="checkbox" name="idx[]" value="<?= sanitizeInput((string)$row->id); ?>">
                                                <label for="checkboxx<?= sanitizeInput((string)$row->id); ?>"></label>
                                            </div>
                                        </td>
                                        <td>Danışman</td>
                                        <td><?= sanitizeInput($row->adsoyad); ?></td>
                                        <td><?= sanitizeInput($row->email); ?></td>
                                        <td><?= sanitizeInput($row->gsm); ?></td>
                                        <td><?= date("d.m.Y H:i", strtotime($row->tarih)); ?></td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="index.php?p=danisman_duzenle&id=<?= sanitizeInput((string)$row->id); ?>">Düzenle</a></li>
                                                    <li><a href="javascript:if(confirm('Gerçekten silmek istiyor musunuz?')){ajaxHere('ajax.php?p=danismanlar&sil=<?= sanitizeInput((string)$row->id); ?>','danisman<?= sanitizeInput((string)$row->id); ?>');}">Sil</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                        }
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
        $('.datatable').dataTable({
            responsive: true
        });
    });

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
        }, function() {
            $("#SelectForm").submit();
        });
    }

    function HatSil() {
        $("#action_hidden").val("hatsil");
        swal({
            title: "Seçilen Üyelerin Hatırlatmalarını Sil",
            text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",
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
<div id="hidden_result" style="display:none"></div>
