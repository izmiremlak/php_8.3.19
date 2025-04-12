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

?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Yeni Hizmet Ekle</h4>
            </div>
        </div>
        
        <div class="row">
            <!-- Col 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=hizmet_ekle" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="baslik" class="col-sm-3 control-label">Başlık</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="baslik" name="baslik" maxlength="32" value="" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="kategori_id" class="col-sm-3 control-label">Kategori</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="kategori_id" id="kategori_id">
                                        <option value="">Yok</option>
                                        <?php
                                        $kats = $db->query("SELECT * FROM kategoriler_501 WHERE tipi=4 AND dil='".sanitizeInput($dil)."' ORDER BY sira ASC");
                                        while ($kat = $kats->fetch()) {
                                        ?>
                                        <option value="<?= sanitizeInput($kat->id); ?>"><?= sanitizeInput($kat->baslik); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label for="kisa_icerik" class="col-sm-3 control-label">Kısa Açıklama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="kisa_icerik" name="kisa_icerik" maxlength="135" value="" placeholder="">
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label for="link" class="col-sm-3 control-label">Harici Link</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="link" name="link" value="" placeholder="">
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label for="icon" class="col-sm-3 control-label">Font Awesome Icon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="icon" name="icon" value="" placeholder="örnek: fa fa-chrome">
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label for="anasayfa" class="col-sm-3 control-label">Anasayfada Görünsün:</label>
                                <div class="col-sm-9">
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="inlineRadio1" value="1" name="anasayfa">
                                        <label for="inlineRadio1">Evet</label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" id="inlineRadio2" value="0" name="anasayfa">
                                        <label for="inlineRadio2">Hayır</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="resim" class="col-sm-3 control-label">Listeleme Görseli</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resim" name="resim" value="">
                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= sanitizeInput($gorsel_boyutlari['hizmetler']['resim1']['orjin_x']); ?> x <?= sanitizeInput($gorsel_boyutlari['hizmetler']['resim1']['orjin_y']); ?> olmalıdır.</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="resim2" class="col-sm-3 control-label">Arkaplan Görseli</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resim2" name="resim2" value="">
                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= sanitizeInput($gorsel_boyutlari['hizmetler']['resim2']['orjin_x']); ?> x <?= sanitizeInput($gorsel_boyutlari['hizmetler']['resim2']['orjin_y']); ?> olmalıdır.</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="icerik" class="col-sm-1 control-label">İçerik</label>
                                <div class="col-sm-11">
                                    <textarea class="summernote form-control" rows="9" id="icerik" name="icerik"></textarea>
                                </div>
                            </div>
                            <?= $fonk->bilgi(" Google'da rekabeti düşük kelimelerde organik olarak ilk sayfaya yükselmek için mutlaka aşağıdaki bilgileri doldurunuz. Sadece sayfa içeriği ile değil, SEO başlık, kelimeler ve açıklama ile de optimize ediniz."); ?>
                            <div class="form-group">
                                <label for="title" class="col-sm-3 control-label">SEO Başlık (Title)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="title" name="title" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keywords" class="col-sm-3 control-label">SEO Kelimeler (Keywords)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="keywords" name="keywords" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-sm-3 control-label">SEO Açıklama (Description)</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="5" id="description" name="description"></textarea>
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
            focus: true, // set focus to editable area after initializing summernote
            onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0], editor, welEditable);
            }
        });
    });
</script>