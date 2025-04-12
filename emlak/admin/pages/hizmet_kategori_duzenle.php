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

// GET parametresini güvenli şekilde al
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Veritabanından kategori bilgilerini çek
$snc = $db->prepare("SELECT * FROM kategoriler_501 WHERE id=:ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch();
} else {
    header("Location:index.php?p=hizmet_kategoriler");
    exit;
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Hizmet Kategori Düzenle</h4>
            </div>
        </div>
        
        <div class="row">
            <!-- Col 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=hizmet_kategori_duzenle&id=<?= sanitizeInput($snc->id); ?>" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="baslik" class="col-sm-3 control-label">Kategori Başlık</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="baslik" name="baslik" value="<?= sanitizeInput($snc->baslik); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sira" class="col-sm-3 control-label">Kategori Sıra Nosu</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sira" name="sira" value="<?= sanitizeInput($snc->sira); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="resim2" class="col-sm-3 control-label">Arkaplan Görseli</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resim2" name="resim2" value="">
                                    <?= ($snc->resim2 != '') ? '<img src="../uploads/thumb/'.sanitizeInput($snc->resim2).'" id="resim2_src" width="150" />' : ''; ?>
                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= sanitizeInput($gorsel_boyutlari['kategoriler']['resim2']['orjin_x']); ?> x <?= sanitizeInput($gorsel_boyutlari['kategoriler']['resim2']['orjin_y']); ?> olmalıdır.</p>
                                </div>
                            </div>
                            <?= $fonk->bilgi(" Google'da rekabeti düşük kelimelerde organik olarak ilk sayfaya yükselmek için mutlaka aşağıdaki bilgileri doldurunuz. Sadece sayfa içeriği ile değil, SEO başlık, kelimeler ve açıklama ile de optimize ediniz."); ?>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">SEO Başlık (Title)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="title" name="title" value="<?= sanitizeInput($snc->title); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keywords" class="col-sm-3 control-label">SEO Kelimeler (Keywords)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="keywords" name="keywords" value="<?= sanitizeInput($snc->keywords); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-sm-3 control-label">SEO Açıklama (Description)</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="5" id="description" name="description"><?= sanitizeInput($snc->description); ?></textarea>
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms','form_status');">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Col1 end -->
        </div>
        <!-- row end -->
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
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: true // set focus to editable area after initializing summernote
        });
    });
</script>