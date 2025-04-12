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

// Kullanıcı oturum kontrolü
if ($hesap->id == '') {
    die();
}

// Girdi verilerini güvenli hale getirme
$id = intval($_GET["ilan_id"]);

// İlan kontrolü
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND id=?");
$kontrol->execute(array($id));
if ($kontrol->rowCount() < 1) {
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

// Kullanıcı yetki kontrolü
$ilan_aktifet = ($hesap->tipi == 1) ? 1 : $hesap->ilan_aktifet;
$acc = $db->query("SELECT id, kid, ilan_aktifet FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($snc->acid != $hesap->id AND $hesap->id != $kid) {
    die();
}
$kurumsal = $db->prepare("SELECT ilan_aktifet FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
$kurumsal->execute(array($kid));
if ($kurumsal->rowCount() > 0) {
    $kurumsal_data = $kurumsal->fetch(PDO::FETCH_OBJ);
    $ilan_aktifet = ($kurumsal_data->ilan_aktifet == 0) ? $ilan_aktifet : $kurumsal_data->ilan_aktifet;
}

// Video dosyasını sil
if ($snc->video != '') {
    $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $snc->video;
    if (file_exists($nirde)) {
        unlink($nirde);
    }
}

// Veritabanında video bilgisini güncelle
$db->query("UPDATE sayfalar SET video='' WHERE site_id_555=501 AND id=" . $id);

?>
<script type="text/javascript">
$("#VideoVarContent").slideUp(300, function() {
    $("#galeri_video_ekle").slideDown(300);
});
$('html, body').animate({ scrollTop: 250 }, 500);
</script>