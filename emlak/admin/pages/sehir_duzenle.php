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

// Şehir bilgilerini veritabanından çekme
$snc = $db->prepare("SELECT * FROM sehirler_501 WHERE id = :ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch();
} else {
    header("Location: index.php?p=sehirler");
    exit;
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Şehir Blok Düzenle</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=sehir_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;" enctype="multipart/form-data">
                            <?php
                            $ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
                            $ulkelerc = $ulkeler->rowCount();
                            if ($ulkelerc > 1) {
                            ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Ülke <span style="color:red">*</span></label>
                                <div class="col-sm-9">
                                    <select id="ulke_id" name="ulke_id" class="form-control" onchange="ajaxHere('ajax.php?p=il_getir&ulke_id=' + this.options[this.selectedIndex].value, 'il'); yazd('il', '');">
                                        <option value="">Seçiniz</option>
                                        <?php
                                        while ($row = $ulkeler->fetch()) {
                                        ?>
                                        <option value="<?= sanitizeInput((string)$row->id); ?>" <?= ($snc->ulke_id == $row->id) ? 'selected' : ''; ?>><?= sanitizeInput($row->ulke_adi); ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">İl</label>
                                <div class="col-sm-9">
                                    <select name="il" id="il" class="form-control" onchange="ajaxHere('ajax.php?p=ilce_getir&il_id=' + this.options[this.selectedIndex].value, 'ilce');">
                                        <option value="">Seçiniz</option>
                                        <?php
                                        if ($ulkelerc < 2) {
                                            $ulke = $ulkeler->fetch();
                                            $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id = " . $ulke->id . " ORDER BY id ASC");
                                            while ($row = $sql->fetch()) {
                                                if ($row->id == $snc->il) {
                                                    $il_adi = $row->il_adi;
                                                }
                                        ?>
                                        <option value="<?= sanitizeInput((string)$row->id); ?>" <?= ($row->id == $snc->il) ? 'selected' : ''; ?>><?= sanitizeInput($row->il_adi); ?></option>
                                        <?php
                                            }
                                        } else {
                                            $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id = " . $snc->ulke_id . " ORDER BY id ASC");
                                            while ($row = $sql->fetch()) {
                                                if ($row->id == $snc->il) {
                                                    $il_adi = $row->il_adi;
                                                }
                                        ?>
                                        <option value="<?= sanitizeInput((string)$row->id); ?>" <?= ($row->id == $snc->il) ? 'selected' : ''; ?>><?= sanitizeInput($row->il_adi); ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">İlçe</label>
                                <div class="col-sm-9">
                                    <select name="ilce" id="ilce" class="form-control" onchange="ajaxHere('ajax.php?p=mahalle_getir&ilce_id=' + this.options[this.selectedIndex].value, 'mahalle');">
                                        <option value="">Seçiniz</option>
                                        <?php
                                        if ($snc->il != 0) {
                                            $sql = $db->query("SELECT id, ilce_adi FROM ilce WHERE il_id = " . $snc->il . " ORDER BY id ASC");
                                            while ($row = $sql->fetch()) {
                                        ?>
                                        <option value="<?= sanitizeInput((string)$row->id); ?>" <?= ($row->id == $snc->ilce) ? 'selected' : ''; ?>><?= sanitizeInput($row->ilce_adi); ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Mahalle</label>
                                <div class="col-sm-9">
                                    <select name="mahalle" id="mahalle" class="form-control">
                                        <option value="">Seçiniz</option>
                                        <?php
                                        if ($snc->mahalle != 0 || $snc->ilce != 0) {
                                            if ($snc->ilce == 0 && $snc->mahalle != 0) {
                                                $ilcene = $db->query("SELECT ilce_id, id FROM mahalle_koy WHERE id = " . $snc->mahalle);
                                                if ($ilcene->rowCount() > 0) {
                                                    $ilcene = $ilcene->fetch()->ilce_id;
                                                }
                                            } elseif ($snc->ilce != 0) {
                                                $ilcene = $snc->ilce;
                                            } else {
                                                $ilcene = 0;
                                            }
                                            if ($ilcene != 0) {
                                                $semtler = $db->query("SELECT * FROM semt WHERE ilce_id = " . $ilcene);
                                                if ($semtler->rowCount() > 0) {
                                                    while ($srow = $semtler->fetch()) {
                                                        $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE semt_id = " . $srow->id . " AND ilce_id = " . $ilcene . " ORDER BY mahalle_adi ASC");
                                                        if ($mahalleler->rowCount() > 0) {
                                        ?>
                                        <optgroup label="<?= sanitizeInput((string)$srow->semt_adi); ?>">
                                            <?php
                                            while ($row = $mahalleler->fetch()) {
                                                if ($snc->mahalle == $row->id) {
                                                    $mahalle_adi = $row->mahalle_adi;
                                                }
                                            ?>
                                            <option value="<?= sanitizeInput((string)$row->id); ?>" <?= ($snc->mahalle == $row->id) ? 'selected' : ''; ?>><?= sanitizeInput($row->mahalle_adi); ?></option>
                                            <?php
                                            }
                                            ?>
                                        </optgroup>
                                        <?php
                                                        }
                                                    }
                                                } else {
                                                    $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE ilce_id = " . $ilcene . " ORDER BY mahalle_adi ASC");
                                                    while ($row = $mahalleler->fetch()) {
                                                        if ($snc->mahalle == $row->id) {
                                                            $mahalle_adi = $row->mahalle_adi;
                                                        }
                                        ?>
                                        <option value="<?= sanitizeInput((string)$row->id); ?>" <?= ($snc->mahalle == $row->id) ? 'selected' : ''; ?>><?= sanitizeInput($row->mahalle_adi); ?></option>
                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="permalink" class="col-sm-3 control-label">Emlak Durumu</label>
                                <div class="col-sm-9">
                                    <div class="radio radio-info radio-inline">
                                        <input type="radio" id="inlineRadio3" value="satilik" name="emlak_durum" <?= ($snc->emlak_durum == 'satilik') ? 'checked' : ''; ?>>
                                        <label for="inlineRadio3">Satılık</label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" id="inlineRadio4" value="kiralik" name="emlak_durum" <?= ($snc->emlak_durum == 'kiralik') ? 'checked' : ''; ?>>
                                        <label for="inlineRadio4">Kiralık</label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" id="inlineRadio5" value="gunluk_kiralik" name="emlak_durum" <?= ($snc->emlak_durum == 'gunluk_kiralik') ? 'checked' : ''; ?>>
                                        <label for="inlineRadio5">Günlük Kiralık</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sira" class="col-sm-3 control-label">Sıra No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sira" name="sira" value="<?= sanitizeInput($snc->sira); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="resim" class="col-sm-3 control-label">Arkaplan Görsel</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="resim" name="resim" value="">
                                    <?= ($snc->resim != '') ? '<img src="../uploads/' . sanitizeInput($snc->resim) . '" id="resim_src" weight="' . sanitizeInput((string)$gorsel_boyutlari['sehirler']['orjinal']['width']) . '" />' : ''; ?>
                                    <p style="margin-left:10px;font-size:13px;margin-top:5px;">Yükleyeceğiniz görselin boyutları <?= sanitizeInput((string)$gorsel_boyutlari['sehirler']['orjinal']['width']); ?> x <?= sanitizeInput((string)$gorsel_boyutlari['sehirler']['orjinal']['height']); ?> piksel olmalıdır.</p>
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

    });
</script>