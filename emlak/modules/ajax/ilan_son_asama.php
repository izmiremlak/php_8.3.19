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

$id = intval($_GET["id"]);

// İlan kontrolü
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi=4 AND id=? AND ekleme=0");
$kontrol->execute([$id]);
if ($kontrol->rowCount() < 1) {
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

$multi = $db->query("SELECT id, ilan_no FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . $snc->ilan_no . " ORDER BY id ASC");
$multict = $multi->rowCount();
$multif = $multi->fetch(PDO::FETCH_OBJ);
$multidids = $db->query("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . $snc->ilan_no)->fetch(PDO::FETCH_OBJ)->ids;
$mulid = ($multict > 1 && $snc->id == $multif->id) ? " IN(" . $multidids . ")" : "=" . $snc->id;
$mulidx = ($multict > 1) ? " IN(" . $multidids . ")" : "=" . $snc->id;

$ilan_aktifet = ($hesap->tipi == 1) ? 1 : $hesap->ilan_aktifet;
$acc = $db->query("SELECT id, kid, ilan_aktifet FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($snc->acid != $hesap->id && $hesap->id != $kid) {
    die();
}
$kurumsal = $db->prepare("SELECT ilan_aktifet FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
$kurumsal->execute([$kid]);
if ($kurumsal->rowCount() > 0) {
    $ilan_aktifet = ($kurumsal->ilan_aktifet == 0) ? $ilan_aktifet : $kurumsal->ilan_aktifet;
}

$adsoyad = $hesap->adi . ' ' . $hesap->soyadi;
$otarih = date("d.m.Y", strtotime($fonk->datetime()));

$durumagore = ($ilan_aktifet == 1) ? dil("TX663") : dil("TX664");

$hesapp = $hesap;
$adsoyad = $hesapp->adi;
$adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
$adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
$fonk->bildirim_gonder([$adsoyad, $snc->id, $snc->baslik, $durumagore, $otarih], "ilan_olusturuldu", $hesapp->email, $hesapp->telefon);

$db->query("UPDATE sayfalar SET ekleme='1' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id" . $mulidx);