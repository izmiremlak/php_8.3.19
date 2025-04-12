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

// İl ID'si alınır ve güvenli hale getirilir
$il = intval($_GET["il_id"]);
$varsa = intval($_GET["varsa"]); // Kullanılmayan bir değişken gibi görünüyor

// HTML çıktısı için başlangıç
?>
<option value=""><?= htmlspecialchars(dil("TX56"), ENT_QUOTES, 'UTF-8'); ?></option>
<?php

// İl ID'si boşsa işlem durdurulur
if ($il == '') {
    die();
}

// İl bilgisi kontrol edilir
$kontrol = $db->prepare("SELECT * FROM il WHERE id=?");
$kontrol->execute([$il]);

if ($kontrol->rowCount() < 1) {
    die();
}
$il = $kontrol->fetch(PDO::FETCH_OBJ);

// İlçeler alınır ve sıralanır
$ilceler = $db->query("SELECT * FROM ilce WHERE il_id=" . $il->id . " ORDER BY ilce_adi ASC");

// İlçeler döngü ile HTML olarak yazdırılır
while ($row = $ilceler->fetch(PDO::FETCH_OBJ)) {
    ?>
    <option value="<?= intval($row->id); ?>"><?= htmlspecialchars($row->ilce_adi, ENT_QUOTES, 'UTF-8'); ?></option>
    <?php
}