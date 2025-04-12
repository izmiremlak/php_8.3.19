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
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Seo Linkler</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="panel-group" id="accordion-test-2">
                            <div class="panel panel-pink panel-color">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseOne-2" aria-expanded="true" class="collapsed">
                                            Açıklamalar ve Talimatlar
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne-2" class="panel-collapse collapse" aria-expanded="true">
                                    <div class="panel-body">
                                        Google'da daha iyi pozisyonlarda bulunmak ve aramalarda ön sıralarda yer almak için, aşağıdaki alanlardan istediğiniz şekilde seo link ekleme yapabilirsiniz.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=seolinkler" onsubmit="return false;" enctype="multipart/form-data">
                            <div id="lokasyonlar_list1">
                                <?php
                                $sql = $db->query("SELECT * FROM referanslar_501 WHERE dil = '{$dil}' ORDER BY sira ASC");
                                while ($row = $sql->fetch()) {
                                ?>
                                <input type="text" class="form-control" name="baslik[]" value="<?= sanitizeInput($row->adi); ?>" placeholder="Başlık" style="float:left;width:19%;margin-right:5px;">
                                <input type="text" class="form-control" name="link[]" value="<?= sanitizeInput($row->website); ?>" placeholder="URL Adresi" style="float:left;width:19%;margin-right:5px;">
                                <input type="text" class="form-control" name="sira[]" value="<?= sanitizeInput($row->sira); ?>" placeholder="Sıra" style="float:left;width:5%;margin-right:5px;margin-bottom:10px;">
                                <div style="clear:both;"></div>
                                <?php
                                }
                                ?>
                            </div>
                            <script type="text/javascript">
                            function lokasyon_ekle1() {
                                $("#lokasyonlar_list1").append('<input type="text" class="form-control" name="baslik[]" value="" placeholder="Başlık" style="float:left;width:19%;margin-right:5px;margin-bottom:10px;"><input type="text" class="form-control" name="link[]" value="" placeholder="URL Adresi" style="float:left;width:19%;margin-right:5px;margin-bottom:10px;"><input type="text" class="form-control" name="sira[]" value="" placeholder="Sıra" style="float:left;width:5%;margin-right:5px;margin-bottom:10px;"><div style="clear:both;"></div>');
                            }
                            </script>
                            <button type="button" onclick="lokasyon_ekle1();" class="btn btn-default waves-effect m-b-5">+ Alan Ekle</button>
                            <div style="clear:both;"></div>
                            <br>
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