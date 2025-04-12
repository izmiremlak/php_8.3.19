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

// Reklam bilgilerini veritabanından çekme
$snc = $db->prepare("SELECT * FROM reklamlar_199 WHERE id = :ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch();
} else {
    header("Location: index.php?p=reklamlar");
    exit;
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Reklam Düzenle</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=reklam_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;" enctype="multipart/form-data">
                            <?php if ($snc->suresiz == 0): ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Kalan Süre</label>
                                <div class="col-sm-9">
                                    <span style="display:block;margin-top:7px;">
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
                                <label for="baslik" class="col-sm-3 control-label">Başlık</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="baslik" name="baslik" value="<?= sanitizeInput($snc->baslik); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tipi" class="col-sm-3 control-label">Alanı</label>
                                <div class="col-sm-9">
                                    <select name="tipi" id="tipi" class="form-control">
                                        <?php
                                        foreach ($reklam_alanlari as $id => $name) {
                                            echo '<option value="' . sanitizeInput((string)$id) . '" ' . ($snc->tipi == $id ? 'selected' : '') . '>' . sanitizeInput($name) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Durumu</label>
                                <div class="col-sm-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="durum_check" type="checkbox" name="durum" value="1" <?= ($snc->durum == 1) ? 'checked' : ''; ?>>
                                        <label for="durum_check"><strong>Gizle</strong></label><br>
                                    </div>
                                    <span>Gizlerseniz reklam hiç bir alanda yayınlanamaz.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Süresiz</label>
                                <div class="col-sm-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="suresiz_check" type="checkbox" name="suresiz" value="1" <?= ($snc->suresiz == 1) ? 'checked' : ''; ?>>
                                        <label for="suresiz_check"><strong>Aktif</strong></label><br>
                                    </div>
                                    <span>Aktif ederseniz reklam süresiz şekilde yayınlanır.</span>
                                </div>
                            </div>
                            <div class="form-group" id="btarih_con" <?= ($snc->suresiz == 1) ? 'style="display:none"' : ''; ?>>
                                <label for="btarih" class="col-sm-3 control-label">Bitiş Tarihi</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="btarih" name="btarih" value="<?= ($snc->btarih == '') ? '' : sanitizeInput(date("d.m.Y", strtotime($snc->btarih))); ?>" placeholder="Örn:25.05.2017">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="kodu" class="col-sm-3 control-label">Kaynak Kodu</label>
                                <div class="col-sm-9">
                                    <textarea name="kodu" id="kodu" class="form-control"><?= sanitizeInput($snc->kodu); ?></textarea>
                                    <span>Masaüstü için kaynak kod</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mobil_kodu" class="col-sm-3 control-label">Kaynak Kodu</label>
                                <div class="col-sm-9">
                                    <textarea name="mobil_kodu" id="mobil_kodu" class="form-control"><?= sanitizeInput($snc->mobil_kodu); ?></textarea>
                                    <span>Responsive (Telefon, Tablet, TV) için kaynak kod</span>
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
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>
<script src="assets/js/inputmask.js"></script>
<script>
    jQuery(document).ready(function() {
        $('.summernote').summernote({
            height: 200, // editör yüksekliğini ayarla
            minHeight: null, // editörün minimum yüksekliğini ayarla
            maxHeight: null, // editörün maksimum yüksekliğini ayarla
            focus: true, // summernote başlatıldıktan sonra düzenlenebilir alana odaklan
            onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0], editor, welEditable);
            }
        });

        $("#suresiz_check").change(function() {
            var durum = $(this).prop("checked");
            if (durum) {
                $("#btarih_con").slideUp(400);
            } else {
                $("#btarih_con").slideDown(400);
                $("#btarih").focus();
            }
        });
    });
</script>