<?php
// Hata loglama fonksiyonu
function logError($errorMessage) {
    error_log($errorMessage, 3, '/path/to/error.log');
}

// Kullanıcıdan gelen veriyi temizleme fonksiyonu
function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// ID'yi güvenli bir şekilde al ve sanitize et
$id = $gvn->rakam($_GET["id"]);
$snc = $db->prepare("SELECT * FROM kategoriler_501 WHERE id=:ids");
$snc->execute(array('ids' => $id));

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch(PDO::FETCH_OBJ);
} else {
    header("Location:index.php?p=foto_galeri");
}
?>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Foto Galeri Düzenle</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs tabs">
                    <li class="active tab">
                        <a href="#tab1" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Galeri Bilgisi</span>
                        </a>
                    </li>
                    <li class="tab">
                        <a href="#tab2" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Fotoğraflar</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab1">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div id="form_status"></div>
                                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=foto_galeri_duzenle&id=<?= sanitizeInput($snc->id) ?>" onsubmit="return false;" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="baslik" class="col-sm-3 control-label">Başlık</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="baslik" name="baslik" value="<?= sanitizeInput($snc->baslik) ?>" placeholder="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="sira" class="col-sm-3 control-label">Sıra No</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="sira" name="sira" value="<?= sanitizeInput($snc->sira) ?>" placeholder="">
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
                    </div> <!-- tab1 end -->
                    <div class="tab-pane" id="tab2">
                        <?= $fonk->bilgi("Yükleme işlemi tamamlandığında lütfen sayfayı yenileyiniz.") ?>
                        <div class="m-b-30">
                            <form action="#" class="dropzone" id="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file" multiple="multiple">
                                </div>
                            </form>
                        </div>
                        <div id="silsnc"></div>
                        <div class="row port">
                            <div class="portfolioContainer">
                                <?php
                                $sql = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND galeri_id=" . $snc->id . " AND dil='" . $dil . "' ORDER BY id DESC");
                                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <div class="col-sm-6 col-lg-3 col-md-4 webdesign illustrator" id="foto_<?= sanitizeInput($row->id) ?>">
                                        <div class="gal-detail thumb">
                                            <a href="/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/<?= sanitizeInput($row->resim) ?>" class="image-popup">
                                                <img src="/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/<?= sanitizeInput($row->resim) ?>" class="thumb-img" alt="<?= sanitizeInput($row->baslik) ?>">
                                            </a>
                                            <h4 align="center">
                                                <a href="javascript:;" onclick="ajaxHere('ajax.php?p=galeri_foto_sil&id=<?= sanitizeInput($row->id) ?>','silsnc');">
                                                    <button class="btn btn-icon waves-effect waves-light btn-danger m-b-5"><i class="fa fa-remove"></i></button>
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
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
<link rel="stylesheet" href="assets/vendor/magnific-popup/dist/magnific-popup.css">
<link href="assets/vendor/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css">
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
            height: 200, // editor yüksekliğini ayarla
            minHeight: null, // editor'ün minimum yüksekliği
            maxHeight: null, // editor'ün maksimum yüksekliği
            focus: true // başlatıldıktan sonra editöre odaklan
        });
    });
</script>
<script>
    var gurl = 'ajax.php?p=foto_galeri_duzenle&id=<?= sanitizeInput($snc->id) ?>&galeri=1';
</script>
<script src="assets/vendor/dropzone/dist/dropzone_galeri.js"></script>
<script type="text/javascript" src="assets/vendor/isotope/dist/isotope.pkgd.min.js"></script>
<script type="text/javascript" src="assets/vendor/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.image-popup').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            mainClass: 'mfp-fade',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1] // 0 - mevcut, 1 - sonraki resmi önceden yükler
            }
        });
    });
</script>