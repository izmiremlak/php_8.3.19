<?php
// Hata loglama fonksiyonu
function logError($errorMessage) {
    error_log($errorMessage, 3, '/path/to/error.log');
}

// Kullanıcıdan gelen veriyi temizleme fonksiyonu
function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Yeni Foto Slayt Ekle</h4>
            </div>
        </div>
        <div class="row">
            <!-- Col 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=foto_slider_ekle" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="baslik" class="col-sm-3 control-label">Slayt Başlığı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="baslik" name="baslik" value="" placeholder="Slayt Başlığı Yazın">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="aciklama" class="col-sm-3 control-label">Açıklama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="aciklama" name="aciklama" value="" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="link" class="col-sm-3 control-label">Harici Link</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="link" name="link" value="" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sira" class="col-sm-3 control-label">Slayt Sıra Nosu</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sira" name="sira" value="" placeholder="Slayt Sıra Nosu Yazın">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="resim" class="col-sm-3 control-label">Slayt Görseli Seçiniz</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resim" name="resim" value="">
                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= sanitizeInput($gorsel_boyutlari['slider']['orjin_x']) ?> x <?= sanitizeInput($gorsel_boyutlari['slider']['orjin_y']) ?> olmalıdır.</p>
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms','form_status');">Kaydet</button>
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