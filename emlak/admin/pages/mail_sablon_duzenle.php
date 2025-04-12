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

// Veritabanından şablonu çekme
$snc = $db->prepare("SELECT * FROM mail_sablonlar_501 WHERE id = :id");
$snc->execute(['id' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch();
} else {
    header("Location: index.php?p=mail_sablonlar_501 SET");
    exit;
}
?>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Bildirim Şablonu Düzenle</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="" onclick="window.history.back()"><i class="fa fa-angle-double-left"></i> Bildirim Şablonları</a></li>
                    <li class="active">Bildirim Şablonu Düzenle</li>
                </ol>
            </div>
        </div>
        
        <div class="row">
            <!-- Kolon 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=mail_sablon_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Şablon Adı</label>
                                <div class="col-sm-9">
                                    <span style="margin-top: 9px; height: 25px; display: block;">
                                        <strong><?= sanitizeInput($snc->adi); ?></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label class="col-sm-3 control-label">Şablon Adı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="adi" value="<?= sanitizeInput($snc->adi); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label class="col-sm-3 control-label">Şablon Key</label>
                                <div class="col-sm-9">
                                    <span style="margin-top: 9px; height: 25px; display: block;">
                                        <strong><?= sanitizeInput($snc->tag); ?></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label for="degiskenler" class="col-sm-3 control-label">Değişkenler</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="9" id="degiskenler" name="degiskenler"><?= sanitizeInput($snc->degiskenler); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label class="col-sm-3 control-label">Yönetici E-Postaları</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="yemails" value="<?= sanitizeInput($snc->yemails); ?>" placeholder="Virgül ile ayırarak yazınız.">
                                    <span>Doldurursanız sadece burada yazan e-postalara mail gidecektir.</span>
                                </div>
                            </div>
                            <div class="form-group" style="display:none">
                                <label class="col-sm-3 control-label">Yönetici Telefonları</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="yphones" value="<?= sanitizeInput($snc->yphones); ?>" placeholder="Virgül ile ayırarak yazınız.">
                                    <span>Doldurursanız sadece burada yazan telefonlara sms gidecektir.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Üye'ye E-Posta Gönder</label>
                                <div class="col-sm-9">
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="ubildirim_sec1" value="1" name="ubildirim" <?= ($snc->ubildirim == 1) ? 'checked' : ''; ?>>
                                        <label for="ubildirim_sec1">Aktif</label>
                                    </div>
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="ubildirim_sec2" value="0" name="ubildirim" <?= ($snc->ubildirim == 0) ? 'checked' : ''; ?>>
                                        <label for="ubildirim_sec2">Pasif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Üye'ye SMS Gönder</label>
                                <div class="col-sm-9">
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="sbildirim_sec1" value="1" name="sbildirim" <?= ($snc->sbildirim == 1) ? 'checked' : ''; ?>>
                                        <label for="sbildirim_sec1">Aktif</label>
                                    </div>
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="sbildirim_sec2" value="0" name="sbildirim" <?= ($snc->sbildirim == 0) ? 'checked' : ''; ?>>
                                        <label for="sbildirim_sec2">Pasif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Yönetici'ye E-Posta Gönder</label>
                                <div class="col-sm-9">
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="abildirim_sec1" value="1" name="abildirim" <?= ($snc->abildirim == 1) ? 'checked' : ''; ?>>
                                        <label for="abildirim_sec1">Aktif</label>
                                    </div>
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="abildirim_sec2" value="0" name="abildirim" <?= ($snc->abildirim == 0) ? 'checked' : ''; ?>>
                                        <label for="abildirim_sec2">Pasif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Yönetici'ye SMS Gönder</label>
                                <div class="col-sm-9">
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="ysbildirim_sec1" value="1" name="ysbildirim" <?= ($snc->ysbildirim == 1) ? 'checked' : ''; ?>>
                                        <label for="ysbildirim_sec1">Aktif</label>
                                    </div>
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="ysbildirim_sec2" value="0" name="ysbildirim" <?= ($snc->ysbildirim == 0) ? 'checked' : ''; ?>>
                                        <label for="ysbildirim_sec2">Pasif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ubildirim" <?= ($snc->ubildirim != 1) ? 'style="display:none"' : ''; ?>>
                                <label for="konu" class="col-sm-3 control-label">E-Posta Konu Başlığı (Üye)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="konu" name="konu" value="<?= sanitizeInput($snc->konu); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group abildirim" <?= ($snc->abildirim != 1) ? 'style="display:none"' : ''; ?>>
                                <label for="konu2" class="col-sm-3 control-label">E-Posta Konu Başlığı (Yönetici)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="konu2" name="konu2" value="<?= sanitizeInput($snc->konu2); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group ubildirim" <?= ($snc->ubildirim != 1) ? 'style="display:none"' : ''; ?>>
                                <label for="icerik" class="col-sm-2 control-label">Üye'ye Gidecek E-Posta</label>
                                <div class="col-sm-10">
                                    <textarea class="summernote form-control" rows="9" id="icerik" name="icerik"><?= sanitizeInput($snc->icerik); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group abildirim" <?= ($snc->abildirim != 1) ? 'style="display:none"' : ''; ?>>
                                <label for="icerik" class="col-sm-2 control-label">Yönetici'ye Gidecek E-Posta</label>
                                <div class="col-sm-10">
                                    <textarea class="summernote form-control" rows="9" id="icerik2" name="icerik2"><?= sanitizeInput($snc->icerik2); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group sbildirim" <?= ($snc->sbildirim != 1) ? 'style="display:none"' : ''; ?>>
                                <label for="icerik3" class="col-sm-2 control-label">Üye'ye Gidecek SMS</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" rows="9" id="icerik3" name="icerik3"><?= sanitizeInput($snc->icerik3); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group ysbildirim" <?= ($snc->ysbildirim != 1) ? 'style="display:none"' : ''; ?>>
                                <label for="icerik4" class="col-sm-2 control-label">Yönetici'ye Gidecek SMS</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" rows="9" id="icerik4" name="icerik4"><?= sanitizeInput($snc->icerik4); ?></textarea>
                                </div>
                            </div>
                            <div class="alert alert-info" role="alert">
                                <?php
                                $dgsknler = explode(',', $snc->degiskenler);
                                $degiskenler = '';

                                foreach ($dgsknler as $degisken) {
                                    if ($degisken != '') {
                                        $degiskenler .= '{' . sanitizeInput($degisken) . '}&nbsp;,&nbsp;';
                                    }
                                }

                                $degiskenler = rtrim($degiskenler, '&nbsp;,&nbsp;');
                                ?>
                                Değişkenler: <strong><?= $degiskenler; ?></strong> Belirtilen değişkenleri içerik'de istediğiniz yere koyarak bildirim'de belirtilmesini sağlayabilirsiniz.
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms','form_status');">Kaydet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- Kolon 1 sonu -->
        </div><!-- Satır sonu -->
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
        height: 200, // editör yüksekliğini ayarla
        minHeight: null, // editörün minimum yüksekliğini ayarla
        maxHeight: null, // editörün maksimum yüksekliğini ayarla
        focus: true, // summernote başlatıldıktan sonra düzenlenebilir alana odaklan
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0], editor, welEditable);
        }
    });

    $("input[name=ubildirim]").change(function() {
        var secilen = $(this).val();
        if (secilen == 0) {
            $(".ubildirim").slideUp(500);
        } else {
            $(".ubildirim").slideDown(500);
        }
    });

    $("input[name=abildirim]").change(function() {
        var secilen = $(this).val();
        if (secilen == 0) {
            $(".abildirim").slideUp(500);
        } else {
            $(".abildirim").slideDown(500);
        }
    });

    $("input[name=sbildirim]").change(function() {
        var secilen = $(this).val();
        if (secilen == 0) {
            $(".sbildirim").slideUp(500);
        } else {
            $(".sbildirim").slideDown(500);
        }
    });

    $("input[name=ysbildirim]").change(function() {
        var secilen = $(this).val();
        if (secilen == 0) {
            $(".ysbildirim").slideUp(500);
        } else {
            $(".ysbildirim").slideDown(500);
        }
    });
});

function tumunu_gizle() {
    $(".ubildirim, .abildirim, .sbildirim, .ysbildirim").slideUp(500);
}
</script>