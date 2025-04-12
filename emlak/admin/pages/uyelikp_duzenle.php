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

// Kullanıcı girdilerini sanitize etme
function sanitizeInput(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$snc = $db->prepare("SELECT * FROM uyelik_paketleri_501 WHERE id=:ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch(PDO::FETCH_OBJ);
} else {
    header("Location:index.php?p=uyelik_paketleri");
    exit;
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
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=uyelikp_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="baslik" class="col-sm-3 control-label">Başlık</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="baslik" name="baslik" value="<?= sanitizeInput((string)$snc->baslik); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sira" class="col-sm-3 control-label">Sıra No</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="sira" name="sira" value="<?= sanitizeInput((string)$snc->sira); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="renk" class="col-sm-3 control-label">Renk</label>
                                <div class="col-sm-2">
                                    <div class="cp2 input-group colorpicker-component">
                                        <input name="renk" type="text" value="<?= sanitizeInput((string)$snc->renk); ?>" class="form-control" />
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Listeden Gizle</label>
                                <div class="col-sm-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="gizle" type="checkbox" name="gizle" value="1"<?= ($snc->gizle == 1) ? " checked" : ''; ?>>
                                        <label for="gizle"><strong>Gizle</strong></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="gizle_con" <?= ($snc->gizle == 0) ? 'style="display:none"' : ''; ?>>
                                <label class="col-sm-3 control-label">Satın Alma Linki</label>
                                <div class="col-sm-9">
                                    <span style="display:block;margin-top:7px;"><?= SITE_URL . "uyelik-paketi-satinal?id=" . sanitizeInput((string)$snc->id) . "&periyod=0"; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="aylik_ilan_limit" class="col-sm-3 control-label">Aylık İlan Ekleme Limiti</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="aylik_ilan_limit" name="aylik_ilan_limit" value="<?= sanitizeInput((string)$snc->aylik_ilan_limit); ?>" placeholder="">
                                </div>
                                <span style="font-size:14px;">Sınırsız/Limitsiz için 0 yazınız.</span>
                            </div>
                            <div class="form-group">
                                <label for="ilan_resim_limit" class="col-sm-3 control-label">İlana Resim Ekleme Limiti</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="ilan_resim_limit" name="ilan_resim_limit" value="<?= sanitizeInput((string)$snc->ilan_resim_limit); ?>" placeholder="">
                                </div>
                                <span style="font-size:14px;">Sınırsız/Limitsiz için 0 yazınız.</span>
                            </div>
                            <div class="form-group">
                                <label for="danisman_limit" class="col-sm-3 control-label">Danışman Ekleme Limiti</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="danisman_limit" name="danisman_limit" value="<?= sanitizeInput((string)$snc->danisman_limit); ?>" placeholder="">
                                </div>
                                <span style="font-size:14px;">Sınırsız/Limitsiz için 0 yazınız.</span>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Anasayfa Danışman Hediyesi</label>
                                <div class="col-sm-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="danisman_onecikar" type="checkbox" name="danisman_onecikar" value="1"<?= ($snc->danisman_onecikar == 1) ? " checked" : ''; ?>>
                                        <label for="danisman_onecikar"><strong>Evet</strong></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="danisman_onecikar_con" <?= ($snc->danisman_onecikar == 0) ? 'style="display:none"' : ''; ?>>
                                <label class="col-sm-3 control-label">Anasayfada Gösterme Süresi</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" id="danisman_onecikar_sure" name="danisman_onecikar_sure" value="<?= sanitizeInput((string)$snc->danisman_onecikar_sure); ?>" placeholder="">
                                </div>
                                <div class="col-sm-2">
                                    <select class="form-control" name="danisman_onecikar_periyod">
                                        <?php
                                        foreach ($periyod as $k => $v) {
                                            ?><option value="<?= sanitizeInput((string)$k); ?>"<?= ($snc->danisman_onecikar_periyod == $k) ? " selected" : ''; ?>><?= sanitizeInput($v); ?></option><?php
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
                                            ?><option value="<?= sanitizeInput((string)$k); ?>"<?= ($snc->ilan_yayin_periyod == $k) ? " selected" : ''; ?>><?= sanitizeInput($v); ?></option><?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Ücretlendirme Periyodu</label>
                                <div class="col-sm-9">
                                    <div id="kapsayici">
                                        <?php
                                        if ($snc->ucretler != '') {
                                            $ucretler = json_decode($snc->ucretler, true);
                                            foreach ($ucretler as $periyode) {
                                        ?>
                                        <div class="row ucret">
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control" name="sure[]" value="<?= sanitizeInput((string)$periyode["sure"]); ?>" placeholder="Süre">
                                            </div>
                                            <div class="col-sm-2">
                                                <select class="form-control" name="periyod[]">
                                                    <?php
                                                    foreach ($periyod as $k => $v) {
                                                        ?><option value="<?= sanitizeInput((string)$k); ?>"<?= ($periyode["periyod"] == $k) ? " selected" : ''; ?>><?= sanitizeInput($v); ?></option><?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control" name="ucret[]" value="<?= sanitizeInput((string)$periyode["ucret"]); ?>" placeholder="Ücret">
                                            </div>
                                            <div class="col-sm-1">
                                                <button type="button" class="btn btn-danger ucret_sil"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div align="left" style="margin-top:5px;">
                                        <button type="button" class="btn btn-success waves-effect waves-light" onclick="UcretEkle();"><i class="fa fa-plus"></i> Periyod Ekle</button>
                                    </div>
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms', 'form_status');">Kaydet</button>
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
    });

    //colorpicker start
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
        $("#kapsayici").append('<div class="row ucret"><div class="col-sm-2"><input type="text" class="form-control" name="sure[]" value="" placeholder="Süre"></div><div class="col-sm-2"><select class="form-control" name="periyod[]"><?php foreach ($periyod as $k => $v) { ?><option value="<?= sanitizeInput((string)$k); ?>"><?= sanitizeInput($v); ?></option><?php } ?></select></div><div class="col-sm-2"><input type="text" class="form-control" name="ucret[]" value="" placeholder="Ücret"></div><div class="col-sm-1"><button type="button" class="btn btn-danger ucret_sil"><i class="fa fa-trash"></i></button></div></div>');
    }

    $(document).ready(function() {
        $("#kapsayici").on('click', '.ucret_sil', function() {
            var parent = $(this).parents(".ucret");
            parent.remove();
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

        $("#gizle").change(function() {
            var durum = $(this).prop("checked");
            if (durum) {
                $("#gizle_con").slideDown(400);
            } else {
                $("#gizle_con").slideUp(400);
            }
        });
    });
</script>