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

// Üyelik ayarlarını alma
$ua = $fonk->UyelikAyarlar();
$ucretler = $ua["danisman_onecikar_ucretler"];
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Danışman Öne Çıkartma Ücretleri</h4>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="alert alert-info" role="alert">Anasayfada, danışmanların "Öne Çıkan Danışmanlar" alanında yayınlanması için gereken ücretleri buradan ayarlayabilirsiniz.</div>
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=onecikarma_ayarlar" onsubmit="return false;" enctype="multipart/form-data">
                            <div id="kapsayici">
                                <?php foreach ($ucretler as $ucret) { ?>
                                <div class="row ucret">
                                    <div class="col-sm-2"><input type="text" class="form-control" name="sure[]" value="<?= sanitizeInput((string)$ucret["sure"]); ?>" placeholder="Süre"></div>
                                    <div class="col-sm-2">
                                        <select class="form-control" name="periyod[]">
                                            <?php foreach ($periyod as $k => $v) { ?>
                                            <option value="<?= sanitizeInput((string)$k); ?>"<?= ($ucret["periyod"] == $k) ? " selected" : ''; ?>><?= sanitizeInput($v); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control" name="tutar[]" value="<?= sanitizeInput($gvn->para_str($ucret["tutar"])); ?>" placeholder="Tutar"></div>
                                    <div class="col-sm-2"><button type="button" class="btn btn-danger waves-effect waves-light ucret_sil"><i class="fa fa-trash"></i> Sil</button></div>
                                </div>
                                <?php } ?>
                            </div>
                            <div align="left" style="margin-top:5px;">
                                <button type="button" class="btn btn-success waves-effect waves-light" onclick="UcretEkle();"><i class="fa fa-plus"></i> Periyod Ekle</button>
                            </div>
                            <br>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms', 'form_status');">Güncelle</button>
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
        focus: true, // set focus to editable area after initializing summernote
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0], editor, welEditable);
        }
    });
});

function UcretEkle() {
    $("#kapsayici").append('<div class="row ucret"><div class="col-sm-2"><input type="text" class="form-control" name="sure[]" value="" placeholder="Süre"></div><div class="col-sm-2"><select class="form-control" name="periyod[]"><?php foreach ($periyod as $k => $v) { ?><option value="<?= sanitizeInput((string)$k); ?>"><?= sanitizeInput($v); ?></option><?php } ?></select></div><div class="col-sm-2"><input type="text" class="form-control" name="tutar[]" value="" placeholder="Tutar"></div><div class="col-sm-2"><button type="button" class="btn btn-danger waves-effect waves-light ucret_sil"><i class="fa fa-trash"></i> Sil</button></div></div>');
}

$(document).ready(function() {
    $("#kapsayici").on('click', '.ucret_sil', function() {
        var parent = $(this).parents(".ucret");
        parent.remove();
    });
});
</script>