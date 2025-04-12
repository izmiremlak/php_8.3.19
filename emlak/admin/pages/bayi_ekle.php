<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Bayi Ekle</title>
    <link rel="stylesheet" href="assets/css/admin.min.css">
</head>
<body>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">Yeni Bayi Ekle</h4>
                </div>
            </div>
            <div class="row">
                <!-- Col 1 -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div id="form_status"></div>
                            <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=bayi_ekle" onsubmit="return false;" enctype="multipart/form-data">
                                <!-- Lokasyon Alanı -->
                                <div class="form-group">
                                    <label for="lokasyon" class="col-sm-3 control-label">Lokasyon</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="lokasyon" name="lokasyon" value="<?= htmlspecialchars($lokasyon ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Sıra Alanı -->
                                <div class="form-group">
                                    <label for="sira" class="col-sm-3 control-label">Sıra</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="sira" name="sira" value="<?= htmlspecialchars($sira ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Adres Alanı -->
                                <div class="form-group">
                                    <label for="adres" class="col-sm-3 control-label">Adres</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="adres" name="adres" value="<?= htmlspecialchars($adres ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Telefon Alanı -->
                                <div class="form-group">
                                    <label for="telefon" class="col-sm-3 control-label">Telefon</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="telefon" name="telefon" value="<?= htmlspecialchars($telefon ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- GSM Alanı -->
                                <div class="form-group">
                                    <label for="gsm" class="col-sm-3 control-label">GSM</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="gsm" name="gsm" value="<?= htmlspecialchars($gsm ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- E-Posta Alanı -->
                                <div class="form-group">
                                    <label for="email" class="col-sm-3 control-label">E-Posta</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
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
                                                    var coords = new GLatLng(41.003917, 28.967299);
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
                                        <input type="text" id="coords" name="google_maps" style="display:none;" />
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
    <script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
    <script src="assets/plugins/notifications/notify-metro.js"></script>
    <script src="assets/plugins/notifications/notifications.js"></script>
</body>
</html>