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

// İlan ve resim ID'lerini güvenli hale getirme
$id = intval($_GET["ilan_id"]);
$resim_id = intval($_GET["resim_id"]);

// İlan kontrolü
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND id=?");
$kontrol->execute(array($id));
if ($kontrol->rowCount() < 1) {
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

// Kullanıcı yetki kontrolü
$acc = $db->query("SELECT id,kid FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($snc->acid != $hesap->id AND $hesap->id != $kid) {
    die();
}

// Resim ID kontrolü
if ($resim_id == '' OR $resim_id == 0) {
    die();
}

// Resim bilgilerini al
$qqq = $db->prepare("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=? AND id=? ");
$qqq->execute(array($snc->id, $resim_id));
$qqq = $qqq->fetch(PDO::FETCH_OBJ);

// Dosya bilgilerini al ve sil
$pinfo = pathinfo("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/" . $qqq->resim);
$folder = $pinfo["dirname"] . "/";
$ext = $pinfo["extension"];
$fname = $pinfo["filename"];
$bname = $pinfo["basename"];

@unlink($folder . "thumb/" . $bname);
@unlink($folder . $bname);
@unlink($folder . $fname . "_original." . $ext);

// Veritabanından resim kaydını sil
try {
    $sil = $db->prepare("DELETE FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=? AND id=? ");
    $sil->execute(array($snc->id, $resim_id));
} catch (PDOException $e) {
    // Hata mesajını log dosyasına yaz
    error_log($e->getMessage(), 3, __DIR__ . '/logs/error_log.txt');
    die();
}
?>

<script type="text/javascript">
$(function(){
    // Resim satırlarını animasyon ile gizle
    $("#xrow_<?=$resim_id;?>").css({"background-color" : '#EFCFCF'});
    $("#xrow_<?=$resim_id;?>").animate({opacity : 0.1}, 1000, function(){
        $("#xrow_<?=$resim_id;?>").fadeOut(100);
    });

    $("#xrowd_<?=$resim_id;?>").css({"background-color" : '#EFCFCF'});
    $("#xrowd_<?=$resim_id;?>").animate({opacity : 0.1}, 1000, function(){
        $("#xrowd_<?=$resim_id;?>").fadeOut(100);
    });
});
</script>