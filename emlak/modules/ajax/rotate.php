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

// POST kontrolü
if (!$_POST) {
    die("Post gelmedi");
}

// Kimlik doğrulama ve güvenli hale getirme
$id = intval($_GET["id"]);
if ($id == '' || !is_numeric($id) || strlen($id) > 15 || $hesap->id == '') {
    die("Geçersiz kimlik");
}

// Görsel kontrolü
$sorgula = $db->prepare("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND sayfa_id!=0 ");
$sorgula->execute(array($id));
if ($sorgula->rowCount() < 1) {
    die("Geçersiz görsel");
}
$gorsel = $sorgula->fetch(PDO::FETCH_OBJ);

// İlan kontrolü
$snc = $db->prepare("SELECT acid,id FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi=4 AND id=?");
$snc->execute(array($gorsel->sayfa_id));
if ($snc->rowCount() < 1) {
    die("Geçersiz ilan");
}
$snc = $snc->fetch(PDO::FETCH_OBJ);

// Kullanıcı yetki kontrolü
if ($hesap->tipi != 1) {
    $acc = $db->query("SELECT id,kid FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
    $kid = $acc->kid;
    if ($snc->acid != $hesap->id AND $hesap->id != $kid) {
        die("Geçersiz yetki");
    }
}

// Döndürme derecesi kontrolü ve güvenli hale getirme
$derece = intval($_POST["rotate"]);
if ($derece == '' || strlen($derece) == 1 || strlen($derece) > 4 || $derece > 360 || $derece < -360) {
    die("Lütfen döndürün!");
}

$dhyphen = str_replace("-", "", $derece);
if ($derece < 0) {
    $before = $derece;
    $derece = 360 - $dhyphen;
}

// Görsel ayarları ve döndürme işlemi
$gorsel_adi = $gorsel->resim;
$uzanti = $fonk->uzanti($gorsel_adi);
$radi = str_replace($uzanti, "", $gorsel_adi);
$original_name = $radi . "_original" . $uzanti;
$watermark = ($gayarlar->stok == 1) ? 'watermark.png' : '';

$ayarla = $fonk->gorsel_ayarla("uploads", $original_name, "", false, false, false, $derece);
$ayarla = $fonk->gorsel_ayarla("uploads", $original_name, $radi, false, $gorsel_boyutlari['foto_galeri']['orjin_x'], $gorsel_boyutlari['foto_galeri']['orjin_y'], false, $watermark);
$ayarla = $fonk->gorsel_ayarla("uploads", $original_name, $radi, true, $gorsel_boyutlari['foto_galeri']['thumb_x'], $gorsel_boyutlari['foto_galeri']['thumb_y'], false, $watermark);

if ($ayarla) {
    echo "OK";
}