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

// GET verilerini sanitize etme
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Veritabanından yorumu çekme
$snc = $db->prepare("SELECT * FROM musteri_yorumlar WHERE id = :id");
$snc->execute(['id' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch();
} else {
    header("Location: index.php?p=musteri_yorumlar");
    exit;
}
?>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Müşteri Yorumu Düzenle</h4>
            </div>
        </div>
        
        <div class="row">
            <!-- Kolon 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=musteri_yorum_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;">
                            <div class="form-group">
                                <label for="adsoyad" class="col-sm-3 control-label">Adı Soyadı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="adsoyad" name="adsoyad" value="<?= sanitizeInput($snc->adsoyad); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="firma" class="col-sm-3 control-label">Firma Adı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="firma" name="firma" value="<?= sanitizeInput($snc->firma); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="resim" class="col-sm-3 control-label">Firma Logosu</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resim" name="resim" value="">
                                    <?= ($snc->resim != '') ? '<img src="../uploads/thumb/' . sanitizeInput($snc->resim) . '" id="resim_src" width="150" />' : ''; ?>
                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= $gorsel_boyutlari['musteri_yorumlar']['thumb_x']; ?> x <?= $gorsel_boyutlari['musteri_yorumlar']['thumb_y']; ?> piksel olmalıdır.</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sira" class="col-sm-3 control-label">Sıra</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sira" name="sira" value="<?= sanitizeInput($snc->sira); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mesaj" class="col-sm-3 control-label">Mesaj</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="3" id="mesaj" name="mesaj" placeholder=""><?= sanitizeInput($snc->mesaj); ?></textarea>
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms','form_status');">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Kolon 1 sonu -->
        </div><!-- Satır sonu -->
    </div>
</div>
<script>
var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css">
<link href="assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>
<script>
jQuery(document).ready(function() {
    $('.wysihtml5').wysihtml5();
    $('.summernote').summernote({
        height: 200, // editör yüksekliğini ayarla
        minHeight: null, // editörün minimum yüksekliğini ayarla
        maxHeight: null, // editörün maksimum yüksekliğini ayarla
        focus: true // summernote başlatıldıktan sonra düzenlenebilir alana odaklan
    });
});
</script>