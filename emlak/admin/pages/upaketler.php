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

// Paketlerin durumunu güncelle
$db->query("UPDATE upaketler_501 SET durumb = '1' WHERE durumb = 0");

// GET verilerini sanitize etme
$sil = filter_input(INPUT_GET, 'sil', FILTER_SANITIZE_NUMBER_INT);
$onayla = filter_input(INPUT_GET, 'onayla', FILTER_SANITIZE_NUMBER_INT);

// Paket silme ve onaylama işlemleri
if ($hesap->tipi != 2) {
    if ($sil !== null) {
        $db->prepare("DELETE FROM upaketler_501 WHERE id = ?")->execute([$sil]);
        header("Location: index.php?p=upaketler");
        exit;
    }

    if ($onayla !== null) {
        $paket = $db->prepare("SELECT * FROM upaketler_501 WHERE id = ?");
        $paket->execute([$onayla]);
        $paket = $paket->fetch();

        $hesapp = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = ?");
        $hesapp->execute([$paket->acid]);
        $hesapp = $hesapp->fetch();

        $adsoyad = $hesapp->adi . ' ' . $hesapp->soyadi;
        $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
        $baslik = $paket->adi . " " . dil("PAY_NAME2");
        $fiyat = $gvn->para_str($paket->tutar) . " " . dil("UYELIKP_PBIRIMI");
        $neresi = "paketlerim";

        $fonk->bildirim_gonder(
            [
                $adsoyad,
                $hesapp->email,
                $hesapp->parola,
                $baslik,
                $fiyat,
                date("d.m.Y H:i", strtotime($fonk->datetime())),
                SITE_URL . $neresi
            ],
            "siparis_onaylandi",
            $hesapp->email,
            $hesapp->telefon
        );

        $db->prepare("UPDATE upaketler_501 SET durum = '1' WHERE id = ?")->execute([$onayla]);
        header("Location: index.php?p=upaketler");
        exit;
    }

    // POST verilerini işleme
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idler = $_POST["id"] ?? [];
        $action = $_POST["action"] ?? '';

        if (count($idler) > 0) {
            foreach ($idler as $id) {
                $id = (int) sanitizeInput($id);
                if ($action === 'sil') {
                    $db->prepare("DELETE FROM upaketler_501 WHERE id = ?")->execute([$id]);
                } elseif ($action === 'onayla') {
                    $paket = $db->prepare("SELECT * FROM upaketler_501 WHERE id = ?");
                    $paket->execute([$id]);
                    $paket = $paket->fetch();

                    $hesapp = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = ?");
                    $hesapp->execute([$paket->acid]);
                    $hesapp = $hesapp->fetch();

                    $adsoyad = $hesapp->adi . ' ' . $hesapp->soyadi;
                    $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
                    $baslik = $paket->adi . " " . dil("PAY_NAME2");
                    $fiyat = $gvn->para_str($paket->tutar) . " " . dil("UYELIKP_PBIRIMI");
                    $neresi = "paketlerim";

                    $fonk->bildirim_gonder(
                        [
                            $adsoyad,
                            $hesapp->email,
                            $hesapp->parola,
                            $baslik,
                            $fiyat,
                            date("d.m.Y H:i", strtotime($fonk->datetime())),
                            SITE_URL . $neresi
                        ],
                        "siparis_onaylandi",
                        $hesapp->email,
                        $hesapp->telefon
                    );

                    $db->prepare("UPDATE upaketler_501 SET durum = '1' WHERE id = ?")->execute([$id]);
                }
            }
        }

        header("Location: index.php?p=upaketler");
        exit;
    }
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Satılan Mağaza Paketleri</h4>
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
                                        <li><a href="#" onclick="TumuOnayla();">Seçilenleri Onayla</a></li>
                                        <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                    </ul>
                                </div>
                                <div class="btn-group">
                                    <button type="button" onclick="window.location.href='index.php?p=uyelik_paketleri';" class="btn btn-danger waves-effect waves-light"><i class="fa fa-cog"></i> Üyelik Paketleri</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default m-t-20">
                        <div class="panel-body">
                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th width="6%">Seç</th>
                                        <th width="10%">Üye</th>
                                        <th>Paket</th>
                                        <th>Alış Tarihi</th>
                                        <th>Tutar</th>
                                        <th>Ödeme Yöntemi</th>
                                        <th>Durum</th>
                                        <th width="15%">Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sorgu = $db->query("SELECT * FROM upaketler_501 ORDER BY durum ASC, id DESC LIMIT 0, 500");
                                    while ($row = $sorgu->fetch()) {
                                        $uye = $db->prepare("SELECT id, unvan, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = ?");
                                        $uye->execute([$row->acid]);
                                        if ($uye->rowCount() > 0) {
                                            $uye = $uye->fetch();
                                            $name = ($uye->unvan == '') ? $uye->adsoyad : $uye->unvan;
                                        }
                                    ?>
                                    <tr>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?= sanitizeInput((string)$row->id); ?>" type="checkbox" name="id[]" value="<?= sanitizeInput((string)$row->id); ?>">
                                                <label for="checkbox<?= sanitizeInput((string)$row->id); ?>"></label>
                                            </div>
                                        </td>
                                        <td><a href="index.php?p=uye_duzenle&id=<?= sanitizeInput((string)$uye->id); ?>" target="_blank"><?= sanitizeInput($name); ?></a></td>
                                        <td><?= sanitizeInput($row->adi); ?></td>
                                        <td><?= date("d.m.Y H:i", strtotime($row->tarih)); ?></td>
                                        <td><strong title="<?= sanitizeInput($row->sure) . " " . sanitizeInput($periyod[$row->periyod]); ?>"><?= sanitizeInput($gvn->para_str($row->tutar)); ?> <?= sanitizeInput($row->pbirimi); ?></strong></td>
                                        <td><?= sanitizeInput($row->odeme_yontemi); ?></td>
                                        <td id="upaket<?= sanitizeInput((string)$row->id); ?>_durum"><?php
                                        echo ($row->durum == 0) ? '<strong style="color:red">Onay Bekleniyor</strong>' : '';
                                        echo ($row->durum == 1) ? '<strong style="color:green">Onaylandı</strong>' : '';
                                        echo ($row->durum == 2) ? '<strong style="color:black">İptal Edildi</strong>' : '';
                                        ?></td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="index.php?p=upaket_duzenle&id=<?= sanitizeInput((string)$row->id); ?>">Düzenle</a></li>
                                                    <li><a href="javascript:if(confirm('Gerçekten onaylamak istiyor musunuz?')){ajaxHere('ajax.php?p=upaketler&onayla=<?= sanitizeInput((string)$row->id); ?>','upaket<?= sanitizeInput((string)$row->id); ?>_durum');}">Onayla</a></li>
                                                    <li><a href="index.php?p=upaketler&sil=<?= sanitizeInput((string)$row->id); ?>">Sil</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
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
                            <script>
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

                            function TumuOnayla() {
                                $("#action_hidden").val("onayla");
                                swal({
                                    title: "Seçilenleri Onayla",
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
                            <div id="hidden_result" style="display:none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
