<?php
// Tema dizini tanımlı değilse çıkış yap
if (!defined("THEME_DIR")) {
    die();
}

// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= htmlspecialchars(dil("TX188"), ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($dayarlar->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($dayarlar->description, ENT_QUOTES, 'UTF-8'); ?>" /> 
<meta name="robots" content="All" />  
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<?php $iletisim = true; include THEME_DIR."inc/head.php"; ?>
</head>
<body>

<?php include THEME_DIR."inc/header.php"; ?>

<div class="headerbg" <?= ($gayarlar->iletisim_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->iletisim_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX189"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <a href="index.html"><?= htmlspecialchars(dil("TX136"), ENT_QUOTES, 'UTF-8'); ?></a> 
                <i class="fa fa-caret-right" aria-hidden="true"></i> 
                <span><strong><?= htmlspecialchars(dil("TX189"), ENT_QUOTES, 'UTF-8'); ?></strong></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">

<div class="content" id="bigcontent">

</div>

<div class="clear"></div>

<div class="iletisim">
    <div class="iletisimdetay">
        <table width="100%" border="0">
            <tr>
                <td colspan="2"><h3><strong><?= htmlspecialchars(dil("TX190"), ENT_QUOTES, 'UTF-8'); ?></strong></h3></td>
            </tr>

            <?php if ($dayarlar->telefon != '') { ?>
            <tr>
                <td width="30%"><strong><?= htmlspecialchars(dil("TX191"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
                <td width="70%"><a href="tel:<?= htmlspecialchars($dayarlar->telefon, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($dayarlar->telefon, ENT_QUOTES, 'UTF-8'); ?></a></td>
            </tr>
            <?php } ?>

            <?php if ($dayarlar->gsm != '') { ?>
            <tr>
                <td width="30%"><strong><?= htmlspecialchars(dil("TX193"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
                <td width="70%"><a href="tel:<?= htmlspecialchars($dayarlar->gsm, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($dayarlar->gsm, ENT_QUOTES, 'UTF-8'); ?></a></td>
            </tr>
            <?php } ?>

            <?php if ($dayarlar->faks != '') { ?>
            <tr>
                <td width="30%"><strong><?= htmlspecialchars(dil("TX192"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
                <td width="70%"><?= htmlspecialchars($dayarlar->faks, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php } ?>

            <?php if ($dayarlar->adres != '') { ?>
            <tr>
                <td width="30%"><strong><?= htmlspecialchars(dil("TX194"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
                <td width="70%"><?= htmlspecialchars($dayarlar->adres, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php } ?>

            <?php if ($dayarlar->google_maps != '') { ?>
            <tr>
                <td colspan="2">
                    <?php
                    // Şube verilerini çek
                    $subeler = $db->query("SELECT id FROM subeler_bayiler_501 WHERE turu=0");
                    ?>
                    <?php if ($subeler->rowCount() > 0) { ?>
                        <a class="subebayibtn" href="subeler">
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                            <strong><?= htmlspecialchars(dil("TX195"), ENT_QUOTES, 'UTF-8'); ?></strong>
                        </a>
                    <?php } ?>

                    <?php
                    // Google Maps koordinatlarını al
                    $coords = $dayarlar->google_maps;
                    list($lat, $lng) = explode(",", $coords);
                    ?>
                    <div id="map" style="width: 100%; height: 250px"></div>
                    <input type="hidden" value="<?= htmlspecialchars($lat, ENT_QUOTES, 'UTF-8'); ?>" id="g_lat">
                    <input type="hidden" value="<?= htmlspecialchars($lng, ENT_QUOTES, 'UTF-8'); ?>" id="g_lng">

                    <script type="text/javascript">
                        function initMap() {
                            var g_lat = parseFloat(document.getElementById("g_lat").value);
                            var g_lng = parseFloat(document.getElementById("g_lng").value);
                            var map = new google.maps.Map(document.getElementById('map'), {
                                zoom: 12,
                                center: {lat: g_lat, lng: g_lng}
                            });
                            var geocoder = new google.maps.Geocoder();
                            
                            var marker = new google.maps.Marker({
                                position: {
                                    lat: g_lat,
                                    lng: g_lng
                                },
                                map: map
                            });
                        }
                    </script>
                    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($gayarlar->google_api_key, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap"></script>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    
    <div class="iletisimform">
        <form action="ajax.php?p=iletisim" method="POST" id="iletisim_form">
            <table width="100%" border="0">
                <tr>
                    <td width="100%"><h3><strong><?= htmlspecialchars(dil("TX196"), ENT_QUOTES, 'UTF-8'); ?></strong></h3></td>
                </tr>
                <tr>
                    <td><input name="adsoyad" type="text" placeholder="<?= htmlspecialchars(dil("TX197"), ENT_QUOTES, 'UTF-8'); ?>"></td>
                </tr>
                <tr>
                    <td><input name="email" type="text" placeholder="<?= htmlspecialchars(dil("TX198"), ENT_QUOTES, 'UTF-8'); ?>"></td>
                </tr>
                <tr>
                    <td><input name="telefon" type="text" placeholder="<?= htmlspecialchars(dil("TX199"), ENT_QUOTES, 'UTF-8'); ?>" id="gsm"></td>
                </tr>
                <tr>
                    <td><textarea name="mesaj" cols="" rows="4" placeholder="<?= htmlspecialchars(dil("TX200"), ENT_QUOTES, 'UTF-8'); ?>"></textarea></td>
                </tr>
                <tr>
                    <td style="border:none;">
                        <span style="font-size:13px;"><?= htmlspecialchars(dil("TX201"), ENT_QUOTES, 'UTF-8'); ?></span>
                        <input name="cevap" type="text" placeholder="<?= htmlspecialchars(dil('SORU'), ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                </tr>
                <tr>
                    <td style="border:none;">
                        <a href="javascript:void(0);" onclick="AjaxFormS('iletisim_form','iletisim_sonuc');" class="btn">
                            <i class="fa fa-paper-plane" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX202"), ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <div id="iletisim_sonuc"></div>
                    </td>
                </tr>
            </table>
        </form>
        
        <!-- TAMAM MESAJ -->
        <div style="margin-top:70px;margin-bottom:70px;text-align:center; display:none" id="IletisimTamam">
            <i style="font-size:80px;color:green;" class="fa fa-check"></i>
            <h2 style="color:green;font-weight:bold;"><?= htmlspecialchars(dil("TX203"), ENT_QUOTES, 'UTF-8'); ?></h2>
            <br/>
            <h4><?= htmlspecialchars(dil("TX204"), ENT_QUOTES, 'UTF-8'); ?></h4>
        </div>
        <!-- TAMAM MESAJ -->
    </div>

    <div class="clear"></div>
</div>

</div>

</body>
</html>