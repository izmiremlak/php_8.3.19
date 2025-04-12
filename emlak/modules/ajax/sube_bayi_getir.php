<?php
// Hata yönetimi için ayarları yapılandır
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error_log.txt'); // Hata log dosyasının yolu
error_reporting(E_ALL); // Tüm hataları raporla

// Hataları sitede göster
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='color: red;'><b>Hata:</b> [$errno] $errstr - $errfile:$errline</div>";
    return true;
}
set_error_handler("customErrorHandler");

// ID'yi güvenli hale getirme
$id = intval($_GET["id"]);

// Veritabanı sorgusu
$sql = $db->prepare("SELECT * FROM subeler_bayiler_501 WHERE id=? AND dil=?");
$sql->execute(array($id, $dil));

// Veri kontrolü
if ($sql->rowCount() == 0) {
    die(dil("TX116"));
}
$veri = $sql->fetch(PDO::FETCH_OBJ);

?>

<div class="bayisubedetay">
<table width="100%" border="0" cellpadding="5">
  <tbody>
    <tr>
      <td colspan="2" align="center"><h4><strong><?= htmlspecialchars($veri->lokasyon, ENT_QUOTES, 'UTF-8'); ?></strong></h4></td>
    </tr>
    <?php if ($veri->adres != "") { ?>
    <tr>
      <td width="20%"><strong><?= dil("TX47"); ?></strong></td>
      <td width="80%"><?= htmlspecialchars($veri->adres, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <?php } ?>
    
    <?php if ($veri->telefon != "") { ?>
    <tr>
      <td width="20%"><strong><?= dil("TX48"); ?></strong></td>
      <td width="80%"><?= htmlspecialchars($veri->telefon, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <?php } ?>
    
    <?php if ($veri->gsm != "") { ?>
    <tr>
      <td width="20%"><strong><?= dil("TX49"); ?></strong></td>
      <td width="80%"><?= htmlspecialchars($veri->gsm, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <?php } ?>
    
    <?php if ($veri->email != "") { ?>
    <tr>
      <td width="20%"><strong><?= dil("TX50"); ?></strong></td>
      <td width="80%"><?= htmlspecialchars($veri->email, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <?php } ?>
    
    <?php if ($veri->google_maps != "") { ?>
    <tr>
      <td colspan="2" align="center">
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAwyu2l9Pq7A0iBRv-jsbTCe6y2DTzkavM" type="text/javascript"></script>
        <script type="text/javascript">
          function initialize() {
            if (typeof google !== 'undefined') {
              var hrt = new google.maps.Map(document.getElementById("hrt"), {
                zoom: 15,
                center: new google.maps.LatLng(<?= htmlspecialchars($veri->google_maps, ENT_QUOTES, 'UTF-8'); ?>),
                mapTypeId: google.maps.MapTypeId.ROADMAP
              });
              var marker = new google.maps.Marker({
                position: new google.maps.LatLng(<?= htmlspecialchars($veri->google_maps, ENT_QUOTES, 'UTF-8'); ?>),
                map: hrt,
                draggable: true
              });
              google.maps.event.addListener(marker, "drag", function() {
                document.getElementById("coords").value = marker.getPosition().toUrlValue();
              });
            }
          }
          window.onload = function() {
            initialize();
          }
        </script>
        <div id="hrt" style="width: 100%; height: 300px"></div>
        <input type="text" id="coords" style="display:none;" />
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>