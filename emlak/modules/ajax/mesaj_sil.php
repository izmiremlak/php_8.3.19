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

// PDO hata modunu ayarla
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Anlık sohbet kapalıysa işlem durdurulur
if ($gayarlar->anlik_sohbet == 0) {
    die();
}

// Kullanıcı bilgileri alınır
$bid = $hesap->id;
$basd = $hesap->adi . ' ' . $hesap->soyadi;
$uid = intval($_GET["uid"]); // Kullanıcı ID'si güvenli hale getirilir

// Gerekli dosya dahil edilir
include "methods/chat.lib.php";

// İlk sohbet durumu kontrol edilir
if ($ilkSohbet == 1) {
    die();
}

// Mesajın kimden geldiği kontrol edilir ve mesaj silme işlemi yapılır
if ($MesajLine->kimden == $bid) {
    $db->query("UPDATE mesaj_iletiler_501 SET gsil='1' WHERE mid=" . $MesajLine->id);
} elseif ($MesajLine->kime == $bid) {
    $db->query("UPDATE mesaj_iletiler_501 SET asil='1' WHERE mid=" . $MesajLine->id);
}