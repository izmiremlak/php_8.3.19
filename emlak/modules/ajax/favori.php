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

// Sayfa bilgilerini kontrol et
$kontrol = $db->prepare("SELECT id FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND tipi=4");
$kontrol->execute(array($id));

if ($kontrol->rowCount() == 0) {
    die();
}

$ilan = $kontrol->fetch(PDO::FETCH_OBJ);

// Favori kontrolü
$favKontrol = $db->query("SELECT id FROM favoriler_501 WHERE acid=" . intval($hesap->id) . " AND ilan_id=" . intval($id));
if ($favKontrol->rowCount() > 0) {
    $neid = $favKontrol->fetch(PDO::FETCH_OBJ)->id;
    try {
        $db->query("DELETE FROM favoriler_501 WHERE id=" . intval($neid));
        echo 1;
    } catch (PDOException $e) {
        echo 0;
    }
} else {
    try {
        $db->query("INSERT INTO favoriler_501 SET acid='" . intval($hesap->id) . "', ilan_id='" . intval($id) . "', tarih='" . $fonk->datetime() . "' ");
        echo 1;
    } catch (PDOException $e) {
        echo 0;
    }
}