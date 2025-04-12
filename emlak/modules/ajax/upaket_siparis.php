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

// PDO hata modunu ayarla
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Girdi verilerini güvenli hale getirme
$id = intval($_GET["id"]);
$periyodu = intval($_GET["periyod"]);
$odeme = htmlspecialchars($_GET["odeme"], ENT_QUOTES, 'UTF-8');

// Girdi doğrulama
if ($id == 0 || strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
    die();
}

// Paket sorgulama
$sorgula = $db->prepare("SELECT * FROM uyelik_paketleri_501 WHERE id=?");
$sorgula->execute(array($id));
if ($sorgula->rowCount() < 1) {
    die();
}
$paket = $sorgula->fetch(PDO::FETCH_OBJ);
$ucretler = json_decode($paket->ucretler, true);
$secilen = $ucretler[$periyodu];

// Periyod kontrolü
if ($secilen["periyod"] == '') {
    die();
}

// PayPal ödemesi kontrolü
if ($_SESSION["custom"] == '' && $odeme == "paypal") {
    die('<span class="error">Hay aksi bir sorun oluştu. Lütfen tekrar deneyiniz.</span>');
}

// PayPal ödemesi işlemleri
if ($odeme == "paypal") {
    $customs = $_SESSION["custom"];
    $custom = base64_decode($customs);
    $custom = json_decode($custom, true);
    if ($custom["satis"] != "uyelik_paketi") {
        die('<span class="error">Hay aksi bir sorun oluştu. Lütfen tekrar deneyiniz.</span>');
    }

    $fiyat_int = $secilen["tutar"];
?>
<script type="text/javascript">
$("#OdemeButon").slideUp(500, function() {
    $("#SipGoster").slideDown(500);
});

function PayPal_Yonlendir() {
    $("#PaypalLocation").submit();
}
setTimeout("PayPal_Yonlendir();", 500);
</script>
<FORM ACTION="https://www.paypal.com/cgi-bin/webscr" METHOD="POST" id="PaypalLocation">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?= htmlspecialchars($gayarlar->paypal_odeme_email, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="item_number" value="<?= time(); ?>">
<input type="hidden" name="custom" value="<?= htmlspecialchars($customs, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="cancel_return" value="<?= SITE_URL . "odeme-basarisiz"; ?>">
<input type="hidden" name="amount" value="<?= $fiyat_int; ?>">
<input type="hidden" name="currency_code" value="<?= htmlspecialchars($fonk->currency_code(dil("UYELIKP_PBIRIMI")), ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="item_name" value="<?= htmlspecialchars($fonk->eng_cevir($paket->baslik . " " . dil("PAY_NAME2")), ENT_QUOTES, 'UTF-8'); ?>">
</FORM>
<?php
} // Ödeme PayPal ise End...

// Havale/EFT ödemesi işlemleri
if ($odeme == "havale_eft") {
    $odeme_yontemi = "Ücretsiz Kayıt";
    $tarih = $fonk->datetime();
    $durum = 0;
    $hesap_id = $hesap->id;

    $hesapp = $hesap;
    $adsoyad = $hesapp->adi;
    $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
    $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
    $baslik = $paket->baslik . " " . dil("PAY_NAME2");

    $fiyat = $gvn->para_str($secilen["tutar"]) . " " . dil("UYELIKP_PBIRIMI");
    $neresi = "paketlerim";

    $fonk->bildirim_gonder(array($adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi), "siparisiniz_alindi", $hesapp->e[...]
    
    $expiry = "+" . $secilen["sure"];
    $expiry .= ($secilen["periyod"] == "gunluk") ? ' day' : '';
    $expiry .= ($secilen["periyod"] == "aylik") ? ' month' : '';
    $expiry .= ($secilen["periyod"] == "yillik") ? ' year' : '';
    $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";

    try {
        $query = $db->prepare("INSERT INTO upaketler_501 SET acid=?, pid=?, adi=?, tutar=?, durum=?, odeme_yontemi=?, tarih=?, btarih=?, sure=?, periyod=?, aylik_ilan_limit=?, ilan_resim_lim[...]
        $query->execute(array($hesap_id, $paket->id, $paket->baslik, $secilen["tutar"], $durum, $odeme_yontemi, $tarih, $btarih, $secilen["sure"], $secilen["periyod"], $paket->aylik_ilan_limit, $[...
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, __DIR__ . '/logs/error_log.txt');
        die($e->getMessage());
    }

    $fonk->yonlendir("paketlerim", 3000);
?>
<script type="text/javascript">
$("#OdemePencere").hide(100, function() {
    $("#TamamDiv").show(100);
});
$('html, body').animate({ scrollTop: 250 }, 500);
</script>
<?php
} // Ödeme Havale/EFT ise End...