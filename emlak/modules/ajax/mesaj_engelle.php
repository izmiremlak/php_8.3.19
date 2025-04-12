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

// Kullanıcı ID'si ve ad soyad alınır
$bid = $hesap->id;
$basd = $hesap->adi . ' ' . $hesap->soyadi;
$uid = intval($_GET["uid"]); // Kullanıcı ID'si güvenli hale getirilir

// Gerekli dosya dahil edilir
include "methods/chat.lib.php";

// Kullanıcı engelleme/engel kaldırma işlemi yapılır
if ($BenEngel == 1) {
    $db->query("DELETE FROM engelli_kisiler_501 WHERE kim = $bid AND kimi = $uid");
} else {
    $db->query("INSERT INTO engelli_kisiler_501 SET kim = $bid, kimi = $uid, tarih = '{$fonk->datetime()}'");
}