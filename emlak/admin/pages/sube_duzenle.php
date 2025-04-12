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

// Şube bilgilerini veritabanından çekme
$snc = $db->prepare("SELECT * FROM subeler_bayiler_501 WHERE id = :ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch();
} else {
    header("Location: index.php?p=subeler");
    exit;
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Şube Düzenle</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=sube_duzenle&id=<?= sanitizeInput((string)$snc->id); ?>" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="lokasyon" class="col-sm-3 control-label">Lokasyon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="lokasyon" name="lokasyon" value="<?= sanitizeInput($snc->lokasyon); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sira" class="col-sm-3 control-label">Sıra</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sira" name="sira" value="<?= sanitizeInput($snc->sira); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="adres" class="col-sm-3 control-label">Adres</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="adres" name="adres" value="<?= sanitizeInput($snc->adres); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="telefon" class="col-sm-3 control-label">Telefon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="telefon" name="telefon" value="<?= sanitizeInput($snc->telefon); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="gsm" class="col-sm-3 control-label">GSM</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="gsm" name="gsm" value="<?= sanitizeInput($snc->gsm); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">E-Posta</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="email" name="email" value="<?= sanitizeInput($snc->email); ?>" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="google_maps" class="col-sm-3 control-label">Google Harita Ekle</label>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">&nbsp;</label>
                                        <div class="col-sm-11">
                                            <h3><i class="fa fa-search"></i> Harita'da Arayın</h3>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">Şehir</label>
                                        <div class="col-sm-11">
                                            <select name="map_il" class="form-control" onchange="yazdir();ajaxHere('ajax.php?p=ilce_getir_string&il_adi=' + this.options[this.selectedIndex].value, 'ilce');">
                                                <option value="">Seçiniz</option>
                                                <?php
                                                $sql = $db->query("SELECT id, il_adi FROM il ORDER BY id ASC");
                                                while ($row = $sql->fetch()) {
                                                ?>
                                                <option><?= sanitizeInput($row->il_adi); ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">İlçe</label>
                                        <div class="col-sm-11">
                                            <select onchange="yazdir();" name="map_ilce" id="map_ilce" class="form-control">
                                                <option value="">Önce Şehir Seçiniz</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">Mahalle</label>
                                        <div class="col-sm-11">
                                            <input onchange="yazdir();" type="text" class="form-control" name="map_mahalle" value="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">Cadde</label>
                                        <div class="col-sm-11">
                                            <input onchange="yazdir();" type="text" class="form-control" name="map_cadde" value="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">Sokak</label>
                                        <div class="col-sm-11">
                                            <input onchange="yazdir();" type="text" class="form-control" name="map_sokak" value="">
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" id="map_adres" name="map_adres" placeholder="Adres yazınız..." style="display: none;">
                                    <input type="text" id="coords" name="google_maps" value="<?= sanitizeInput($snc->google_maps); ?>" style="display:none;" />
                                    <div id="map" style="width: 100%; height: 300px"></div>
                                    <?php
                                    $coords = ($snc->maps == '') ? "41.003917,28.967299" : $snc->maps;
                                    list($lat, $lng) = explode(",", $coords);
                                    ?>
                                    <input type="hidden" value="<?= sanitizeInput($lat); ?>" id="g_lat">
                                    <input type="hidden" value="<?= sanitizeInput($lng); ?>" id="g_lng">
                                    <script type="text/javascript">
                                    function initMap() {
                                        var g_lat = parseFloat(document.getElementById("g_lat").value);
                                        var g_lng = parseFloat(document.getElementById("g_lng").value);
                                        var map = new google.maps.Map(document.getElementById('map'), {
                                            dragable: true,
                                            zoom: 15,
                                            center: { lat: g_lat, lng: g_lng }
                                        });
                                        var geocoder = new google.maps.Geocoder();
                                        var marker = new google.maps.Marker({
                                            position: {
                                                lat: g_lat,
                                                lng: g_lng
                                            },
                                            map: map,
                                            draggable: true
                                        });
                                        jQuery('#map_adres').on('change', function() {
                                            var val = $(this).val();
                                            geocodeAddress(marker, geocoder, map, val);
                                        });
                                        google.maps.event.addListener(marker, 'dragend', function() {
                                            dragend(marker);
                                        });
                                    }

                                    function geocodeAddress(marker, geocoder, resultsMap, address) {
                                        if (address) {
                                            geocoder.geocode({ 'address': address }, function(results, status) {
                                                if (status === 'OK') {
                                                    resultsMap.setCenter(results[0].geometry.location);
                                                    marker.setMap(resultsMap);
                                                    marker.setPosition(results[0].geometry.location);
                                                    dragend(marker);
                                                } else {
                                                    console.log('Geocode was not successful for the following reason: ' + status + " word: " + address);
                                                }
                                            });
                                        }
                                    }

                                    function dragend(marker) {
                                        var lat = marker.getPosition().lat();
                                        var lng = marker.getPosition().lng();
                                        $("#coords").val(lat + "," + lng);
                                    }
                                    </script>
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
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true // set focus to editable area after initializing summernote
    });
});
function yazdir() {
    var il = $("select[name='map_il']").val();
    var ilce = $("select[name='map_ilce']").val();
    var maha = $("input[name='map_mahalle']").val();
    var cadde = $("input[name='map_cadde']").val();
    var sokak = $("input[name='map_sokak']").val();
    var neler = "";
    if (il != undefined) {
        neler += il;
        if (ilce != undefined) {
            neler += ", " + ilce;
            if (maha != undefined) {
                neler += ", " + maha;
            }
            if (cadde != undefined) {
                neler += ", " + cadde;
            }
            if (sokak != undefined) {
                neler += ", " + sokak;
            }
        }
    }
    $("input[name='map_adres']").val(neler);
    GetMap();
}

function GetMap() {
    $("#map_adres").trigger("change");
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= sanitizeInput($gayarlar->google_api_key); ?>&callback=initMap"></script>