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
                <h4 class="pull-left page-title">Toplu SMS Gönder</h4>
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
                                        Sitenizin en alt(footer) kısmında yer alan bülten alanından eklenen GSM Numaralarını bu alanda görebilir ve Toplu SMS gönderebilirsiniz. Ayrıca, toplu SMS gönderme işlemlerini burada yapabilirsiniz.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=toplu_sms" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="gonderilenler" class="col-sm-3 control-label">Gönderilecek GSM Listesi</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="5" id="gonderilenler" name="gonderilenler"><?php
                                    $data1 = explode(",", $gayarlar->bulten_gsm);
                                    $data2 = [];
                                    $data2ler = $db->query("SELECT telefon FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi=0 AND sms_izin=1 AND telefon != '' ");
                                    while ($row = $data2ler->fetch()) {
                                        $data2[] = $row->telefon;
                                    }
                                    $datalar = array_unique(array_merge($data1, $data2));
                                    $datalar = array_diff($datalar, ["", " "]);
                                    echo implode("\n", $datalar);
                                    ?></textarea>
                                    <br />
                                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="DataKaydet();">Datayı Güncelle</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mesaj" class="col-sm-1 control-label">İçerik</label>
                                <div class="col-sm-11">
                                    <textarea class="form-control" rows="9" id="mesaj" name="mesaj"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-11">
                                    Sisteme kayıtlı toplam <b><?= count($datalar); ?></b> adet GSM bulunmaktadır.
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="TopluMailGonder();">Toplu SMS Gönder</button>
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
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true // set focus to editable area after initializing summernote
    });
});

function DataKaydet() {
    $("#forms").attr("action", "ajax.php?p=gsmler");
    AjaxFormS('forms', 'form_status');
}

function TopluMailGonder() {
    $("#forms").attr("action", "ajax.php?p=toplu_sms");
    AjaxFormS('forms', 'form_status');
}
</script>