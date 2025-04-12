<?php
// Yeni danışman ekleme sayfası

// Veritabanı bağlantısı ve güvenlik işlemleri
require_once 'config.php'; // Veritabanı ve diğer ayarların yüklendiği dosya

// Hata loglama fonksiyonu
function logError($errorMessage) {
    error_log($errorMessage, 3, '/path/to/error.log');
}

function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Hata işleme
try {
    // İşlem kodları burada
} catch (Exception $e) {
    logError($e->getMessage());
    echo '<div class="alert alert-danger" role="alert">' . sanitizeInput($e->getMessage()) . '</div>';
}
?>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Yeni Danışman Ekle</h4>
            </div>
        </div>
        <div class="row">
            <!-- Col 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=danisman_ekle" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="adsoyad" class="col-sm-3 control-label">Adı Soyadı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="adsoyad" name="adsoyad" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="gsm" class="col-sm-3 control-label">GSM No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="gsm" name="gsm" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="telefon" class="col-sm-3 control-label">Telefon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="telefon" name="telefon" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">E-Posta</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="email" name="email" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="resim" class="col-sm-3 control-label">Resim</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resim" name="resim">
                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= $gorsel_boyutlari['danismanlar']['thumb_x']; ?> x <?= $gorsel_boyutlari['danismanlar']['thumb_y']; ?> olmalıdır.</p>
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms', 'form_status');">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- Col1 end -->
        </div><!-- row end -->
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