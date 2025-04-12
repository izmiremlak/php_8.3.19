<?php
// Veritabanı bağlantısı ve güvenlik işlemleri
$id = $gvn->rakam($_GET["id"]);
$snc = $db->prepare("SELECT * FROM subeler_bayiler_501 WHERE id=:ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch(PDO::FETCH_OBJ);
} else {
    header("Location:index.php?p=bayiler");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayi Düzenle</title>
    <link rel="stylesheet" href="assets/css/admin.min.css">
</head>
<body>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">Bayi Düzenle</h4>
                </div>
            </div>
            <div class="row">
                <!-- Col 1 -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div id="form_status"></div>
                            <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=bayi_duzenle&id=<?= htmlspecialchars($snc->id, ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return false;" enctype="multipart/form-data">
                                <!-- Lokasyon Alanı -->
                                <div class="form-group">
                                    <label for="lokasyon" class="col-sm-3 control-label">Lokasyon</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="lokasyon" name="lokasyon" value="<?= htmlspecialchars($snc->lokasyon, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Sıra Alanı -->
                                <div class="form-group">
                                    <label for="sira" class="col-sm-3 control-label">Sıra</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="sira" name="sira" value="<?= htmlspecialchars($snc->sira, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Adres Alanı -->
                                <div class="form-group">
                                    <label for="adres" class="col-sm-3 control-label">Adres</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="adres" name="adres" value="<?= htmlspecialchars($snc->adres, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Telefon Alanı -->
                                <div class="form-group">
                                    <label for="telefon" class="col-sm-3 control-label">Telefon</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="telefon" name="telefon" value="<?= htmlspecialchars($snc->telefon, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- GSM Alanı -->
                                <div class="form-group">
                                    <label for="gsm" class="col-sm-3 control-label">GSM</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="gsm" name="gsm" value="<?= htmlspecialchars($snc->gsm, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- E-Posta Alanı -->
                                <div class="form-group">
                                    <label for="email" class="col-sm-3 control-label">E-Posta</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="email" name="email" value="<?= htmlspecialchars($snc->email, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Google Haritası Alanı -->
                                <div class="form-group">
                                    <label for="google_maps" class="col-sm-3 control-label">Google Haritası Ekle</label>
                                    <div class="col-sm-9">
                                        <script src="http://maps.google.com/maps?file=api&amp;v=3&amp;key=AIzaSyAwyu2l9Pq7A0iBRv-jsbTCe6y2DTzkavM" type="text/javascript"></script>
                                        <script type="text/javascript">
                                            function initialize() {
                                                if (GBrowserIsCompatible()) {
                                                    var hrt = new GMap2(document.getElementById("hrt"));
                                                    hrt.addControl(new GMapTypeControl(1));
                                                    hrt.addControl(new GLargeMapControl());
                                                    hrt.enableContinuousZoom();
                                                    hrt.enableDoubleClickZoom();
                                                    var coords = new GLatLng(<?= htmlspecialchars($snc->maps, ENT_QUOTES, 'UTF-8'); ?>);
                                                    hrt.setCenter(coords, 15);
                                                    var im = new GMarker(coords, {draggable: true});
                                                    GEvent.addListener(im, "drag", function() {
                                                        document.getElementById("coords").value = im.getPoint().toUrlValue();
                                                    });
                                                    hrt.addOverlay(im);
                                                }
                                            }
                                            window.onload = function() { initialize(); }
                                        </script>
                                        <div id="hrt" style="width: 100%; height: 300px"></div>
                                        <input type="text" id="coords" name="google_maps" value="<?= htmlspecialchars($snc->google_maps, ENT_QUOTES, 'UTF-8'); ?>" style="display:none;" />
                                    </div>
                                </div>
                                <!-- Kaydet Butonu -->
                                <div align="right">
                                    <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms', 'form_status');">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Col1 end -->
            </div><!-- row end -->
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
</body>
</html>