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

// GET verilerini sanitize etme
$sil = filter_input(INPUT_GET, 'sil', FILTER_SANITIZE_NUMBER_INT);

// Silme işlemi
if ($hesap->tipi != 2 && $sil) {
    $stmt = $db->prepare("DELETE FROM sehirler_501 WHERE id = :id");
    $stmt->execute(['id' => $sil]);
    header("Location: index.php?p=sehirler");
    exit;
}

// POST verilerini işleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hesap->tipi != 2) {
    $idler = $_POST['id'] ?? [];
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    foreach ($idler as $id) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        if ($action === 'sil') {
            $stmt = $db->prepare("DELETE FROM sehirler_501 WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }
    }

    header("Location: index.php?p=sehirler");
    exit;
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">İl ve İlçe Blokları (Anasayfa)</h4>
            </div>
        </div>
        <div class="panel-group" id="accordion-test-2">
            <div class="panel panel-pink panel-color">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseOne-2" aria-expanded="true" class="collapsed">
                            Açıklamalar ve Talimatlar
                        </a>
                    </h4>
                </div>
                <div id="collapseOne-2" class="panel-collapse collapse" aria-expanded="true">
                    <div class="panel-body">
                        Sitenizin anasayfasında il ve ilçelerin daha çok dikkat çekebilmesi için eklenmiş olan il ve ilçe bloklarını bu alanda yönetebilirsiniz.
                        Faaliyet gösterdiğiniz illeri veya ilçeleri, bunlara ait ne kadar ilan adedinin bulunduğununu, satılık mı kiralık mı yoksa günlük kiralık mı olduğunu belirleyebilirsiniz.
                    </div>
                </div>
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
                                    <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=sehir_ekle';"> <i class="fa fa-plus"></i> Yeni Şehir Ekle</button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula
                                    <ul class="dropdown-menu">
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
                                        <th>Lokasyon</th>
                                        <th>Emlak Durum</th>
                                        <th>Sıra</th>
                                        <th>Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sorgu = $db->query("SELECT * FROM sehirler_501 WHERE dil = '{$dil}' ORDER BY id DESC LIMIT 0, 500");
                                    while ($row = $sorgu->fetch()) {
                                        $il = $db->query("SELECT il_adi FROM il WHERE id = " . $row->il)->fetch();
                                        if ($row->ilce != 0) {
                                            $ilce = $db->query("SELECT ilce_adi FROM ilce WHERE id = " . $row->ilce)->fetch();
                                        }
                                    ?>
                                    <tr>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?= sanitizeInput((string)$row->id); ?>" type="checkbox" name="id[]" value="<?= sanitizeInput((string)$row->id); ?>">
                                                <label for="checkbox<?= sanitizeInput((string)$row->id); ?>"></label>
                                            </div>
                                        </td>
                                        <td><?= ($row->ilce == 0) ? sanitizeInput($il->il_adi) : sanitizeInput($ilce->ilce_adi); ?></td>
                                        <td><?= sanitizeInput($row->emlak_durum); ?></td>
                                        <td><?= sanitizeInput($row->sira); ?></td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="index.php?p=sehir_duzenle&id=<?= sanitizeInput((string)$row->id); ?>">Düzenle</a></li>
                                                    <li><a href="index.php?p=sehirler&sil=<?= sanitizeInput((string)$row->id); ?>">Sil</a></li>
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