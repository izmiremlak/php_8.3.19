<?php
// Dil ekleme sayfası

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
    $son_dil = $db->query("SELECT kisa_adi FROM diller_501 WHERE kisa_adi='" . $dil . "' ORDER BY id DESC")->fetch(PDO::FETCH_OBJ);
} catch (Exception $e) {
    logError($e->getMessage());
    echo '<div class="alert alert-danger" role="alert">' . sanitizeInput($e->getMessage()) . '</div>';
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Yeni Dil Ekle</h4>
            </div>
        </div>
        <div class="row">
            <!-- Col 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="panel panel-fill panel-danger">
                            <div class="panel-heading">
                                <h3 class="panel-title">Dil Ekleme/Düzenleme (MultiLanguage Kullanımı)</h3>
                            </div>
                            <div class="panel-body">
                                <p>Yeni dil ekleme/düzenleme ve multilanguage kullanımı nasıl olur detaylı bilgi almak için <strong><a style="color:white;cursor:pointer;" data-toggle="modal" data-target=".bs-example-modal-lg">buraya tıklayın</a></strong>.</p>
                            </div>
                        </div>
                        <!--  Modal content for the above example -->
                        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h4 class="modal-title" id="myLargeModalLabel">Dil Ekleme/Düzenleme (MultiLanguage Kullanımı) Açıklamalar</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>Çoğu yazılımlarımızda standart olarak gelen&nbsp;<span style="font-weight: bold;">Sınırsız Çoklu Dil (Multi Language)</span> sisteminin kullanımı hakkında detaylı bilgi almak için <strong><a style="color:white;cursor:pointer;" data-toggle="modal" data-target=".bs-example-modal-lg">buraya tıklayın</a></strong>.</p>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=dil_ekle" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="adi" class="col-sm-3 control-label">Dil Adı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="adi" name="adi" value="" placeholder="Dilin tam adını yazın.">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="kisa_adi" class="col-sm-3 control-label">Dil Ön Eki</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="kisa_adi" name="kisa_adi" maxlength="2" value="" placeholder="Dilin Ön Eki Örn: en,ar,fr">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="gosterim_adi" class="col-sm-3 control-label">Dil Gösterim Adı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="gosterim_adi" name="gosterim_adi" value="" placeholder="Dilin gösterim adı örn : English">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sira" class="col-sm-3 control-label">Sıra No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sira" name="sira" value="" placeholder="">
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label for="resim" class="col-sm-3 control-label">Dilin Simgesi</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resim" name="resim" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Gizli</label>
                                <div class="col-sm-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="durum_check" type="checkbox" name="durum" value="1">
                                        <label for="durum_check"><strong>Gizle</strong></label><br>
                                    </div>
                                    <span>(Gizlerseniz site tarafında listelenmeyecektir.)</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Diğer Dilden Kopyala</label>
                                <div class="col-sm-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="kopyala_check" type="checkbox" name="kopyala" value="1">
                                        <label for="kopyala_check"><strong>Aktif</strong></label><br>
                                    </div>
                                    <span>(Aktif ettiğiniz taktirde,  "<b><?= $dil; ?></b>" dile ait tüm içerikler yeni dile kopyalanacaktır. Böylelikle üzerinden tercüme işlemi yapabilirsiniz.)</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="degiskenler" class="col-sm-3 control-label">Değişkenler</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="20" id="degiskenler" name="degiskenler"><?= @file_get_contents("../" . THEME_DIR . "diller/" . $son_dil->kisa_adi . ".txt"); ?></textarea>
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
</body>
</html>