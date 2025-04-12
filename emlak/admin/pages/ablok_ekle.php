<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Servis Ekle</title>
    <link rel="stylesheet" href="assets/css/admin.min.css">
</head>
<body>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">Yeni Servis Ekle</h4>
                </div>
            </div>
            <div class="row">
                <!-- Col 1 -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div id="form_status"></div>
                            <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=ablok_ekle" onsubmit="return false;" enctype="multipart/form-data">
                                <!-- Başlık Alanı -->
                                <div class="form-group">
                                    <label for="baslik" class="col-sm-3 control-label">Başlık:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="baslik" name="baslik" value="<?= htmlspecialchars($baslik ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Sıra Alanı -->
                                <div class="form-group">
                                    <label for="sira" class="col-sm-3 control-label">Sıra:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="sira" name="sira" value="<?= htmlspecialchars($sira ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- URL Adresi Alanı -->
                                <div class="form-group">
                                    <label for="url" class="col-sm-3 control-label">Var ise URL Adresi:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="url" name="url" value="<?= htmlspecialchars($url ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                    </div>
                                </div>
                                <!-- Icon Kodu Alanı -->
                                <div class="form-group">
                                    <label for="icon" class="col-sm-3 control-label">Font Avesome / Ion Icons Icons Kodu:</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="1" id="icon" name="icon"><?= htmlspecialchars($icon ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                </div>
                                <!-- Görsel Icon Alanı -->
                                <div class="form-group">
                                    <label for="resim" class="col-sm-3 control-label">Veya Görsel Icon</label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control" id="resim" name="resim" value="">
                                        <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= htmlspecialchars($gorsel_boyutlari['abloklar']['orjin_x'], ENT_QUOTES, 'UTF-8'); ?> x <?= htmlspecialchars($gorsel_boyutlari['abloklar']['orjin_y'], ENT_QUOTES, 'UTF-8'); ?> olmalıdır.</p>
                                    </div>
                                </div>
                                <!-- Açıklama Alanı -->
                                <div class="form-group">
                                    <label for="aciklama" class="col-sm-3 control-label">Açıklama:</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" rows="3" id="aciklama" name="aciklama"><?= htmlspecialchars($aciklama ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
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