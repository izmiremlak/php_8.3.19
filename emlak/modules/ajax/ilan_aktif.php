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

// Kullanıcı giriş kontrolü
if ($hesap->id == '') {
    die();
}

$id = $gvn->rakam($_GET["id"]);

// İlanı kontrol et
$kontrol = $db->prepare("SELECT acid, id, durum, ilan_no FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND tipi=4");
$kontrol->execute(array($id));
if ($kontrol->rowCount() == 0) {
    die();
}
$ilan = $kontrol->fetch(PDO::FETCH_OBJ);

// Kullanıcı yetkisi kontrolü
$acc = $db->query("SELECT id, kid FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . intval($ilan->acid))->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($ilan->acid != $hesap->id && $hesap->id != $kid) {
    die();
}

// Çoklu ilan kontrolü
$multi = $db->query("SELECT id, ilan_no FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . intval($ilan->ilan_no) . " ORDER BY id ASC");
$multict = $multi->rowCount();
$multif = $multi->fetch(PDO::FETCH_OBJ);
$multidids = $db->query("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . intval($ilan->ilan_no))->fetch(PDO::FETCH_OBJ)->ids;
$mulid = ($multict > 1 && $ilan->id == $multif->id) ? " IN(" . $multidids . ")" : "=" . intval($ilan->id);

// İlan durumu kontrolü
if ($ilan->durum != 3) {
    die();
}

// İlan durumunu güncelle
$db->query("UPDATE sayfalar SET durum=1 WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id" . $mulid);
?>
<script type="text/javascript">
$(function(){
    $("#row_<?= htmlspecialchars($id, ENT_QUOTES); ?>").css({"background-color" : '#CFEFD4'});
    $("#row_<?= htmlspecialchars($id, ENT_QUOTES); ?>").animate({opacity : 0.1}, 1000, function(){
        $("#row_<?= htmlspecialchars($id, ENT_QUOTES); ?>").fadeOut(100);
    });
});
</script>