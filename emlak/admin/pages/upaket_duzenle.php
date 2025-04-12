<?php
// PHP 8.3.17 özelliklerini kullanarak kodları güncelleyip güvenlik önlemleri ekleyelim
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

// Paket bilgilerini veritabanından çekme
$snc = $db->prepare("SELECT * FROM upaketler_501 WHERE id = :ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch(PDO::FETCH_OBJ);
} else {
    header("Location: index.php?p=upaketler");
    exit;
}

// Üye bilgilerini veritabanından çekme
$uye = $db->prepare("SELECT id, unvan, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = ?");
$uye->execute([$snc->acid]);
if ($uye->rowCount() > 0) {
    $uye = $uye->fetch(PDO::FETCH_OBJ);
    $name = ($uye->unvan == '') ? $uye->adsoyad : $uye->unvan;
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Paket Düzenle</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=upaket_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;" enctype="multipart/form-data">
                            <?php if ($name != ''): ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Üye</label>
                                <div class="col-sm-9">
                                    <span style="display:block; margin-top:6px;">
                                        <a href="index.php?p=uye_duzenle&id=<?= sanitizeInput((string)$uye->id); ?>"><?= sanitizeInput($name); ?></a>
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($snc->durum == 1): ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Kalan Süre</label>
                                <div class="col-sm-9">
                                    <span style="display:block; margin-top:6px;">
                                        <?php
                                        $bugun = date("Y-m-d");
                                        $kgun = $fonk->gun_farki($snc->btarih, $bugun);
                                        if ($kgun > 0) {
                                            echo '<strong style="color:green"><i class="fa fa-clock-o"></i> ' . sanitizeInput((string)$kgun) . ' gün kaldı.</strong>';
                                        } elseif ($kgun == 0) {
                                            echo '<strong style="color:orange"><i class="fa fa-clock-o"></i> Bugün sona eriyor.</strong>';
                                        } else {
                                            echo '<strong style="color:red"><i class="fa fa-clock-o"></i> Süresi Doldu.</strong>';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Durum:</label>
                                <div class="col-sm-9">
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="durum1" value="1" name="durum" <?= ($snc->durum == 1) ? 'checked' : ''; ?>>
                                        <label for="durum1">Onaylandı</label>
                                    </div>
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="durum2" value="2" name="durum" <?= ($snc->durum == 2) ? 'checked' : ''; ?>>
                                        <label for="durum2">İptal Edildi</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="odeme_sekli" class="col-sm-3 control-label">Ödeme Yöntemi</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="odeme_sekli" name="odeme_yontemi">
                                        <?php
                                        foreach ($oyontemleri as $yontem) {
                                            echo '<option ' . (($snc->odeme_yontemi == $yontem) ? 'selected' : '') . '>' . sanitizeInput($yontem) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tutar" class="col-sm-3 control-label">Tutar</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="tutar" name="tutar" value="<?= sanitizeInput($gvn->para_str($snc->tutar)); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Paketin Bitiş Süresi</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" name="sure" value="<?= sanitizeInput((string)$snc->sure); ?>" placeholder="">
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control" name="periyod">
                                        <?php
                                        foreach ($periyod as $k => $v) {
                                            echo '<option value="' . sanitizeInput((string)$k) . '"' . (($snc->periyod == $k) ? ' selected' : '') . '>' . sanitizeInput($v) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="btarih_con" <?= ($snc->durum != 1) ? 'style="display:none"' : ''; ?>>
                                <label for="btarih" class="col-sm-3 control-label">Paketin Bitiş Tarihi</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="btarih" name="btarih" value="<?= ($snc->btarih == '') ? '' : sanitizeInput(date("d.m.Y", strtotime($snc->btarih))); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="adi" class="col-sm-3 control-label">Paket Adı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="adi" name="adi" value="<?= sanitizeInput($snc->adi); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="aylik_ilan_limit" class="col-sm-3 control-label">Aylık İlan Ekleme Limiti</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="aylik_ilan_limit" name="aylik_ilan_limit" value="<?= sanitizeInput((string)$snc->aylik_ilan_limit); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ilan_resim_limit" class="col-sm-3 control-label">İlana Resim Ekleme Limiti</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="ilan_resim_limit" name="ilan_resim_limit" value="<?= sanitizeInput((string)$snc->ilan_resim_limit); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="danisman_limit" class="col-sm-3 control-label">Danışman Ekleme Limiti</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="danisman_limit" name="danisman_limit" value="<?= sanitizeInput((string)$snc->danisman_limit); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Anasayfa Danışman Hediyesi</label>
                                <div class="col-sm-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="danisman_onecikar" type="checkbox" name="danisman_onecikar" value="1" <?= ($snc->danisman_onecikar == 1) ? 'checked' : ''; ?>>
                                        <label for="danisman_onecikar"><strong>Evet</strong></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="danisman_onecikar_con" <?= ($snc->danisman_onecikar == 0) ? 'style="display:none"' : ''; ?>>
                                <label class="col-sm-3 control-label">Anasayfada Gösterme Süresi</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="danisman_onecikar_sure" name="danisman_onecikar_sure" value="<?= sanitizeInput((string)$snc->danisman_onecikar_sure); ?>">
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control" name="danisman_onecikar_periyod">
                                        <?php
                                        foreach ($periyod as $k => $v) {
                                            echo '<option value="' . sanitizeInput((string)$k) . '"' . (($snc->danisman_onecikar_periyod == $k) ? ' selected' : '') . '>' . sanitizeInput($v) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">İlan Yayında Kalma Süresi</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" name="ilan_yayin_sure" value="<?= sanitizeInput((string)$snc->ilan_yayin_sure); ?>" placeholder="">
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control" name="ilan_yayin_periyod">
                                        <?php
                                        foreach ($periyod as $k => $v) {
                                            echo '<option value="' . sanitizeInput((string)$k) . '"' . (($snc->ilan_yayin_periyod == $k) ? ' selected' : '') . '>' . sanitizeInput($v) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick=" AjaxFormS('forms', 'form_status');">Kaydet</button>
                            </div>
                        </form>
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
<link href="assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
<link href="assets/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css">
<style>
.colorpicker-2x .colorpicker-saturation {
    width: 200px;
    height: 200px;
}

.colorpicker-2x .colorpicker-hue,
.colorpicker-2x .colorpicker-alpha {
    width: 30px;
    height: 200px;
}

.colorpicker-2x .colorpicker-color,
.colorpicker-2x .colorpicker-color div {
    height: 30px;
}
</style>
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>
<script src="assets/js/inputmask.js"></script>
<script type="text/javascript" src="assets/plugins/colorpicker/js/bootstrap-colorpicker.js"></script>
<script>
jQuery(document).ready(function() {
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true, // set focus to editable area after initializing summernote
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0], editor, welEditable);
        }
    });
    // colorpicker start
    $(function() {
        $('.cp2').colorpicker({
            customClass: 'colorpicker-2x',
            sliders: {
                saturation: {
                    maxLeft: 200,
                    maxTop: 200
                },
                hue: {
                    maxTop: 200
                },
                alpha: {
                    maxTop: 200
                }
            }
        });
    });
    function UcretEkle() {
        $("#kapsayici").append('<div class="row ucret"><div class="col-sm-2"><input type="text" class="form-control" name="sure[]" value="" placeholder="Süre"></div><div class="col-sm-2"><select class="form-control" name="periyod[]"><?php foreach ($periyod as $k => $v) { echo '<option value="' . sanitizeInput((string)$k) . '">' . sanitizeInput($v) . '</option>'; } ?></select></div><div class="col-sm-2"><input type="text" class="form-control" name="tutar[]" value="" placeholder="Tutar"></div><div class="col-sm-2"><button type="button" class="btn btn-danger ucret_sil">Sil</button></div></div>');
    }
    $("#kapsayici").on('click', '.ucret_sil', function() {
        var parent = $(this).parents(".ucret");
        parent.remove();
    });
    $("input[name='durum']").change(function() {
        var hangisi = $("input[name='durum']:checked").val();
        if (hangisi == 1) {
            $("#btarih_con").slideDown(600);
        } else {
            $("#btarih_con").slideUp(600);
        }
    });
    $("#danisman_onecikar").change(function() {
        var durum = $(this).prop("checked");
        if (durum) {
            $("#danisman_onecikar_con").slideDown(400);
            $("#danisman_onecikar_sure").focus();
        } else {
            $("#danisman_onecikar_con").slideUp(400);
        }
    });
});
</script>