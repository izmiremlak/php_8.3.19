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

// Silme işlemi
if ($gvn->rakam($_GET["sil"]) != "" && $hesap->tipi != 2) {
    $id = filter_input(INPUT_GET, 'sil', FILTER_SANITIZE_NUMBER_INT);
    if (is_numeric($id)) {
        $stmt = $db->prepare("DELETE FROM menuler_501 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: index.php?p=menuler");
        exit;
    }
}

// Ekleme veya güncelleme işlemi
$duzenle = filter_input(INPUT_GET, 'duzenle', FILTER_SANITIZE_NUMBER_INT);
$ekle = filter_input(INPUT_GET, 'ekle', FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hesap->tipi != 2) {
    $ustu = filter_input(INPUT_POST, 'ustu', FILTER_SANITIZE_NUMBER_INT);
    $baslik = sanitizeInput($_POST['baslik']);
    $sira = filter_input(INPUT_POST, 'sira', FILTER_SANITIZE_NUMBER_INT);
    $sayfa = filter_input(INPUT_POST, 'sayfa', FILTER_SANITIZE_NUMBER_INT);
    $url = sanitizeInput($_POST['url']);
    $target = sanitizeInput($_POST['target']);

    if ($baslik == "") {
        $fonk->uyari("Lütfen başlık yaz.");
    } else {
        if ($duzenle == "") {
            // Ekleme işlemi
            $query = $db->prepare("INSERT INTO menuler_501 (dil, ustu, baslik, sira, sayfa, url, target) VALUES (:dil, :ustu, :baslik, :sira, :sayfa, :url, :target)");
            $query->execute(['dil' => $dil, 'ustu' => $ustu, 'baslik' => $baslik, 'sira' => $sira, 'sayfa' => $sayfa, 'url' => $url, 'target' => $target]);
        } else {
            // Güncelleme işlemi
            $query = $db->prepare("UPDATE menuler_501 SET ustu = :ustu, baslik = :baslik, sira = :sira, sayfa = :sayfa, url = :url, target = :target WHERE id = :id");
            $query->execute(['ustu' => $ustu, 'baslik' => $baslik, 'sira' => $sira, 'sayfa' => $sayfa, 'url' => $url, 'target' => $target, 'id' => $duzenle]);
            $fonk->tamam("Başarıyla Güncellendi.");
        }
    }
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Menüler</h4>
            </div>
        </div>
        
        <div class="row">
            <!-- Kolon 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=menuler&ekle=1';"> <i class="fa fa-plus"></i> Yeni Menü Ekle</button>
                        <div style="clear:both;"></div><br/><br/>
                        <div class="adminmenu">
                            <?php $fonk->admin_menu_listesi(); ?>
                            <div style="clear:both;"></div>
                            <h3>Menu Ekle/Düzenle</h3>
                            <form action="" method="POST" id="forms" role="form" class="form-horizontal">
                                <?php
                                if ($duzenle != '') {
                                    $srg = $db->prepare("SELECT * FROM menuler_501 WHERE id = :id");
                                    $srg->execute(['id' => $duzenle]);
                                    $dt = $srg->fetch();
                                }
                                ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Üst Menu Seç:</label>
                                    <div class="col-sm-9">
                                        <select name="ustu" class="form-control">
                                            <option value="0">Yok</option>
                                            <?php $fonk->selectbox_menu_list(0, false, 0, $dt->ustu); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Başlık:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="baslik" value="<?= sanitizeInput($dt->baslik); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="target" class="col-sm-3 control-label">Yeni sekme de aç:</label>
                                    <div class="col-sm-8" style="margin-top:7px;">
                                        <input type="checkbox" id="target_check" class="stm-checkbox" value="_blank" name="target" <?= ($dt->target == '_blank') ? 'checked' : ''; ?>>
                                        <label style="float:left;margin-right:10px;" for="target_check" class="stm-checkbox-label">Aktif</label><span style="margin-left:10px;font-size:14px;margin-top:5px;">Aktif/Pasif</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Sıra:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="sira" value="<?= sanitizeInput($dt->sira); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Sayfa Seç:</label>
                                    <div class="col-sm-9">
                                        <select name="sayfa" class="form-control">
                                            <option value="0">Yok</option>
                                            <?php
                                            $sql = $db->query("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi != 4 AND dil = :dil ORDER BY baslik ASC", ['dil' => $dil]);
                                            while ($row = $sql->fetch()) {
                                                echo '<option value="' . sanitizeInput($row->id) . '" ' . (($dt->sayfa == $row->id) ? 'selected' : '') . '>' . sanitizeInput($row->baslik) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Sayfa URL:</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="url" value="<?= sanitizeInput($dt->url); ?>" class="form-control">
                                        <span style="font-size:13px;font-weight:bold;">Eğer harici yönlendirme yapmak istiyorsanız bu alanı kullanınız.</span>
                                    </div>
                                </div>
                                <div align="right">
                                    <button type="submit" class="btn btn-purple waves-effect waves-light">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Kolon 1 sonu -->
        </div>
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
<script type="text/javascript">
<?php if ($duzenle != '' || $ekle != '') { ?>
$('html, body').animate({scrollTop: 500}, 1000);
<?php } ?>
</script>