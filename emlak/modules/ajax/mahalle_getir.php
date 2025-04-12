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

// İlçe ID'si alınır ve güvenli hale getirilir
$ilce = intval($_GET["ilce_id"]);
$varsa = intval($_GET["varsa"]); // Kullanılmayan bir değişken gibi görünüyor

// HTML çıktısı için başlangıç
echo '<option value="">' . htmlspecialchars(dil("TX266"), ENT_QUOTES, 'UTF-8') . '</option>';

// İlçe ID'si boşsa işlem durdurulur
if ($ilce == '') {
    die();
}

// İlçe bilgisi kontrol edilir
$kontrol = $db->prepare("SELECT * FROM ilce WHERE id=?");
$kontrol->execute([$ilce]);

if ($kontrol->rowCount() < 1) {
    die();
}
$ilce = $kontrol->fetch(PDO::FETCH_OBJ);

// Semtler alınır ve sıralanır
$semtler = $db->query("SELECT * FROM semt WHERE ilce_id=" . $ilce->id . " ORDER BY semt_adi ASC");

if ($semtler->rowCount() > 0) {
    while ($srow = $semtler->fetch(PDO::FETCH_OBJ)) {
        $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE semt_id=" . $srow->id . " AND ilce_id=" . $ilce->id . " ORDER BY mahalle_adi ASC");
        if ($mahalleler->rowCount() > 0) {
            echo '<optgroup label="' . htmlspecialchars($srow->semt_adi, ENT_QUOTES, 'UTF-8') . '">';
            while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                echo '<option value="' . intval($row->id) . '">' . htmlspecialchars($row->mahalle_adi, ENT_QUOTES, 'UTF-8') . '</option>';
            }
            echo '</optgroup>';
        }
    }
} else {
    $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE ilce_id=" . $ilce->id . " ORDER BY mahalle_adi ASC");
    while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
        echo '<option value="' . intval($row->id) . '">' . htmlspecialchars($row->mahalle_adi, ENT_QUOTES, 'UTF-8') . '</option>';
    }
}