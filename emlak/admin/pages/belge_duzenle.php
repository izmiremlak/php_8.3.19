<?php
// Veritabanı bağlantısı ve güvenlik işlemleri
$id = $gvn->rakam($_GET["id"] ?? '');
$snc = $db->prepare("SELECT * FROM belgeler WHERE id=:ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch(PDO::FETCH_OBJ);
} else {
    header("Location:index.php?p=belgeler");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belge Düzenle</title>
    <link rel="stylesheet" href="assets/css/admin.min.css">
</head>
<body>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">Belge Düzenle</h4>
                </div>
            </div>
            <div class="row">
                <!-- Col 1 -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div id="form_status"></div>
                            <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=belge_duzenle&id=<?= htmlspecialchars($snc->id, ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return false;" enctype="multipart/form-data">
                                <!-- Başlık Alanı -->
                                <div class="form-group">
                                    <label for="baslik" class="col-sm-3 control-label">Başlık</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="baslik" name="baslik" value="<?= htmlspecialchars($snc->baslik, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Slayt Başlık">
                                    </div>
                                </div>
                                <!-- Sıra Nosu Alanı -->
                                <div class="form-group">
                                    <label for="sira" class="col-sm-3 control-label">Sıra Nosu</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="sira" name="sira" value="<?= htmlspecialchars($snc->sira, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Slayt Sıra Nosu">
                                    </div>
                                </div>
                                <!-- Dosya Alanı -->
                                <div class="form-group">
                                    <label for="dosya" class="col-sm-3 control-label">Dosya</label>
                                    <div class="col-sm-9">
                                        <input type="file" class="form-control" id="dosya" name="dosya" value="">
                                        <p style="margin-left:10px;font-size:13px;margin-top:5px;">Dosyayı indirmek için <a href="../<?= htmlspecialchars($snc->link, ENT_QUOTES, 'UTF-8'); ?>">tıklayın</a>.</p>
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