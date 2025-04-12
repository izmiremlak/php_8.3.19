<?php
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

if ($_GET["id"]) {
    $id = $gvn->rakam($_GET["id"]);

    $sql = $db->prepare("SELECT * FROM subeler_bayiler_501 WHERE id=? AND dil=?");
    $sql->execute([$id, $dil]);

    if ($sql->rowCount() == 0) {
        header("Location:subeler");
        die();
    }
    $veri = $sql->fetch(PDO::FETCH_OBJ);
}
?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= htmlspecialchars(dil("TX224"), ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($dayarlar->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($dayarlar->description, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<?php $galeri = true; include THEME_DIR . "inc/head.php"; ?>

</head>
<body>

<?php include THEME_DIR . "inc/header.php"; ?>

<div class="headerbg" <?= ($gayarlar->subeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->subeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX224"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <a href="index.html"><?= htmlspecialchars(dil("TX136"), ENT_QUOTES, 'UTF-8'); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i> <span><strong><?= htmlspecialchars(dil("TX224"), ENT_QUOTES, 'UTF-8'); ?></strong></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">

<div class="content" id="bigcontent">

</div>

<div class="clear"></div>

<div class="subelerbayiler">

<div class="lokasyonsec">
    <h3><?= htmlspecialchars(dil("TX225"), ENT_QUOTES, 'UTF-8'); ?></h3>
    <select name="lokasyon" id="lokasyon" onchange="sube_bayi_getir();">
        <option value=""><?= htmlspecialchars(dil("TX226"), ENT_QUOTES, 'UTF-8'); ?></option>
        <?php
        $sql = $db->query("SELECT * FROM subeler_bayiler_501 WHERE turu=0 AND dil='" . htmlspecialchars($dil, ENT_QUOTES, 'UTF-8') . "' ORDER BY sira ASC");
        while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
        ?><option value="<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>" <?= ($id == $row->id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->lokasyon, ENT_QUOTES, 'UTF-8'); ?></option>
        <?php
        }
        ?>
    </select>
    <script type="text/javascript">
    function sube_bayi_getir() {
        vals = document.getElementById("lokasyon").value;
        if (vals != "") {
            window.location.href = 'subeler?id=' + vals;
        }
    }
    </script>
</div>

<div class="bayiblgileri" id="bayiblgileri">
    <?php
    if ($_GET["id"]) {
    ?>

    <div class="bayisubedetay">
        <table width="100%" border="0" cellpadding="5">
            <tbody>
            <tr>
                <td colspan="2" align="center"><h4><strong><?= htmlspecialchars($veri->lokasyon, ENT_QUOTES, 'UTF-8'); ?></strong></h4></td>
            </tr>
            <?php if ($veri->adres != "") { ?>
            <tr>
                <td width="20%"><strong>Adres</strong></td>
                <td width="80%"><?= htmlspecialchars($veri->adres, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php } ?>

            <?php if ($veri->telefon != "") { ?>
            <tr>
                <td width="20%"><strong>Telefon</strong></td>
                <td width="80%"><?= htmlspecialchars($veri->telefon, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php } ?>

            <?php if ($veri->gsm != "") { ?>
            <tr>
                <td width="20%"><strong>Gsm</strong></td>
                <td width="80%"><?= htmlspecialchars($veri->gsm, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php } ?>

            <?php if ($veri->email != "") { ?>
            <tr>
                <td width="20%"><strong>E-Posta</strong></td>
                <td width="80%"><?= htmlspecialchars($veri->email, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
            <?php } ?>

            <?php if ($veri->google_maps != "") { ?>
            <tr>
                <td colspan="2" align="center">

                <?php
                $coords = htmlspecialchars($veri->google_maps, ENT_QUOTES, 'UTF-8');
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
            </tbody>
        </table>
    </div>
    <?php } ?>

</div><!-- .bayibilgileri end -->

</div>

</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>