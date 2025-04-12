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

// Ülke ID'sini al ve kontrol et
$ulke_id = $gvn->rakam($_GET["ulke_id"]);

// Varsayılan seçenek
echo '<option value="">' . dil('TX264') . '</option>';

// Ülke ID'si boşsa çıkış yap
if (empty($ulke_id)) {
    die();
}

// Ülke bilgilerini kontrol et
$kontrol = $db->prepare("SELECT * FROM ulkeler_501 WHERE id=?");
$kontrol->execute(array($ulke_id));

if ($kontrol->rowCount() < 1) {
    die();
}
$ulke = $kontrol->fetch(PDO::FETCH_OBJ);

// İllerin listesini getir ve ekrana yazdır
$iller = $db->query("SELECT * FROM il WHERE ulke_id=" . intval($ulke->id) . " ORDER BY il_adi ASC");
while ($row = $iller->fetch(PDO::FETCH_OBJ)) {
    echo '<option value="' . htmlspecialchars($row->id, ENT_QUOTES) . '">' . htmlspecialchars($row->il_adi, ENT_QUOTES) . '</option>';
}