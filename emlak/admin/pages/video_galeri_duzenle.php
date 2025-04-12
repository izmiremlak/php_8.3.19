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

// ID'yi güvenli bir şekilde al
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$snc = $db->prepare("SELECT * FROM video_galeri WHERE id = :ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch(PDO::FETCH_OBJ);
} else {
    header("Location:index.php?p=video_galeri");
    exit;
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Video Galeri Düzenle</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=video_galeri_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="baslik" class="col-sm-3 control-label">Başlık</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="baslik" name="baslik" value="<?= sanitizeInput($snc->baslik); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="kategori_id" class="col-sm-3 control-label">Kategori</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="kategori_id" id="kategori_id">
                                        <option value="">Yok</option>
                                        <?php
                                        $kats = $db->prepare("SELECT * FROM kategoriler_501 WHERE tipi = 2 AND dil = ? ORDER BY sira ASC");
                                        $kats->execute([$dil]);
                                        while ($kat = $kats->fetch(PDO::FETCH_OBJ)) {
                                        ?>
                                        <option value="<?= sanitizeInput((string)$kat->id); ?>" <?= ($kat->id == $snc->kategori_id) ? 'selected' : ''; ?>><?= sanitizeInput($kat->baslik); ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sira" class="col-sm-3 control-label">Sıra</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sira" name="sira" value="<?= sanitizeInput((string)$snc->sira); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="youtube" class="col-sm-3 control-label">Youtube Video URL</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="youtube" name="youtube" value="<?= sanitizeInput($snc->youtube); ?>" placeholder="Örn: https://www.youtube.com/watch?v=EO_vhggvUCE">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="resim" class="col-sm-3 control-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <img src="<?= sanitizeInput($snc->resim); ?>" width="250" id="resim_src">
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick=" AjaxFormS('forms', 'form_status');">Kaydet</button>
                            </div>
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
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>