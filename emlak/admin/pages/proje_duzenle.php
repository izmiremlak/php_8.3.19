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

// GET verilerini sanitize etme
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Proje bilgilerini veritabanından çekme
$snc = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND id=:ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch();
} else {
    header("Location: index.php?p=projeler");
    exit;
}
?>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Proje Düzenle</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs tabs">
                    <li class="active tab">
                        <a href="#tab1" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Proje Bilgisi</span>
                        </a>
                    </li>
                    <li class="tab">
                        <a href="#tab2" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Foto Galeri</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab1">
                        <div class="row">
                            <!-- Kolon 1 -->
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div id="form_status"></div>
                                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=proje_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;">
                                            <div class="form-group">
                                                <label for="baslik" class="col-sm-3 control-label">Başlık</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="baslik" name="baslik" value="<?= sanitizeInput($snc->baslik); ?>" placeholder="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Danışman</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="danisman_id">
                                                        <option value="0">Yok</option>
                                                        <?php
                                                        $sql = $db->query("SELECT id, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi=0 AND turu=2 ORDER BY id DESC");
                                                        while ($row = $sql->fetch()) {
                                                            echo '<option value="' . sanitizeInput((string)$row->id) . '" ' . ($row->id == $snc->danisman_id ? 'selected' : '') . '>' . sanitizeInput($row->adsoyad) . '</option>';
                                                        }

                                                        $sql = $db->query("SELECT * FROM danismanlar_501 ORDER BY id ASC");
                                                        while ($row = $sql->fetch()) {
                                                            echo '<option value="' . sanitizeInput((string)$row->id) . '" ' . ($row->id == $snc->danisman_id ? 'selected' : '') . '>' . sanitizeInput($row->adsoyad) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group" style="display:none">
                                                <label for="kisa_icerik" class="col-sm-3 control-label">Kısa Açıklama</label>
                                                <div class="col-sm-9">
                                                    <textarea rows="5" class="form-control" id="kisa_icerik" name="kisa_icerik"><?= sanitizeInput($snc->kisa_icerik); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group" style="display:none">
                                                <label class="col-sm-3 control-label">Türü</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="turu">
                                                        <option value="0" <?= ($snc->turu == 0) ? 'selected' : ''; ?>>Devam Eden Projeler</option>
                                                        <option value="1" <?= ($snc->turu == 1) ? 'selected' : ''; ?>>Tamamlanan Projeler</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group" style="display:none">
                                                <label for="permalink" class="col-sm-3 control-label">Öne Çıkan Proje:</label>
                                                <div class="col-sm-9">
                                                    <div class="radio radio-info radio-inline">
                                                        <input type="radio" id="inlineRadio1" value="1" name="onecikan" <?= ($snc->onecikan == '1') ? 'checked' : ''; ?>>
                                                        <label for="inlineRadio1">Evet</label>
                                                    </div>
                                                    <div class="radio radio-inline">
                                                        <input type="radio" id="inlineRadio2" value="0" name="onecikan" <?= ($snc->onecikan == '0') ? 'checked' : ''; ?>>
                                                        <label for="inlineRadio2">Hayır</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="resim" class="col-sm-3 control-label">Listeleme Görseli</label>
                                                <div class="col-sm-9">
                                                    <input type="file" class="form-control" id="resim" name="resim" value="">
                                                    <?= ($snc->resim != '') ? '<img src="../uploads/thumb/' . sanitizeInput($snc->resim) . '" id="resim_src" width="150" />' : ''; ?>
                                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= sanitizeInput($gorsel_boyutlari['projeler']['resim']['width']); ?> x <?= sanitizeInput($gorsel_boyutlari['projeler']['resim']['height']); ?> piksel olmalıdır.</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="resim2" class="col-sm-3 control-label">Arkaplan Görseli</label>
                                                <div class="col-sm-9">
                                                    <input type="file" class="form-control" id="resim2" name="resim2" value="">
                                                    <?= ($snc->resim2 != '') ? '<img src="../uploads/thumb/' . sanitizeInput($snc->resim2) . '" id="resim2_src" width="150" />' : ''; ?>
                                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= sanitizeInput($gorsel_boyutlari['projeler']['resim']['width']); ?> x <?= sanitizeInput($gorsel_boyutlari['projeler']['resim']['height']); ?> piksel olmalıdır.</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="icerik" class="col-sm-1 control-label">İçerik</label>
                                                <div class="col-sm-11">
                                                    <textarea class="summernote form-control" rows="9" id="icerik" name="icerik"><?= sanitizeInput($snc->icerik); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="maps" class="col-sm-3 control-label">Google Maps Harita Url</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="maps" name="maps" value="<?= sanitizeInput($snc->maps); ?>">
                                                </div>
                                            </div>
                                            <?= $fonk->bilgi("Google'da rekabeti düşük kelimelerde organik olarak ilk sayfaya yükselmek için mutlaka aşağıdaki bilgileri doldurunuz. Sadece sayfa içeriği ile sınırlı kalmayınız."); ?>
                                            <div class="form-group">
                                                <label for="title" class="col-sm-3 control-label">SEO Başlık (Title)</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="title" name="title" value="<?= sanitizeInput($snc->title); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="keywords" class="col-sm-3 control-label">SEO Kelimeler (Keywords)</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="keywords" name="keywords" value="<?= sanitizeInput($snc->keywords); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="description" class="col-sm-3 control-label">SEO Açıklama (Description)</label>
                                                <div class="col-sm-9">
                                                    <textarea class="form-control" rows="5" id="description" name="description"><?= sanitizeInput($snc->description); ?></textarea>
                                                </div>
                                            </div>
                                            <div align="right">
                                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms', 'form_status');">Kaydet</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Kolon 1 sonu -->
                        </div><!-- Satır sonu -->
                    </div> <!-- tab1 sonu -->
                    <div class="tab-pane" id="tab2">
                        <?= $fonk->bilgi("Yükleme işlemi tamamlandığında lütfen sayfayı yenileyiniz."); ?>
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
                                $sql = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . sanitizeInput((string)$snc->id) . " AND dil='$dil' ORDER BY id DESC");
                                while ($row = $sql->fetch()) {
                                ?>
                                <div class="col-sm-6 col-lg-3 col-md-4 webdesign illustrator" id="foto_<?= sanitizeInput((string)$row->id); ?>">
                                    <div class="gal-detail thumb">
                                        <a href="../uploads/<?= sanitizeInput($row->resim); ?>" class="image-popup"><img src="../uploads/thumb/<?= sanitizeInput($row->resim); ?>" class="thumb-img" alt="<?= sanitizeInput($row->resim); ?>"></a>
                                        <h4 align="center">
                                            <a href="javascript:;" onclick="ajaxHere('ajax.php?p=galeri_foto_sil&id=<?= sanitizeInput((string)$row->id); ?>', 'silsnc');"><button class="btn btn-icon btn-danger"><i class="fa fa-trash-o"></i></button></a>
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
        height: 200, // editör yüksekliğini ayarla
        minHeight: null, // editörün minimum yüksekliğini ayarla
        maxHeight: null, // editörün maksimum yüksekliğini ayarla
        focus: true // summernote başlatıldıktan sonra düzenlenebilir alana odaklan
    });
});
</script>
<script>
var gurl = 'ajax.php?p=proje_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>&galeri=1';
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
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        }
    });
});
</script>