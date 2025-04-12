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

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $gvn->rakam($_GET["id"]);
$periyodu = $gvn->zrakam($_GET["periyod"]);
$odeme = $gvn->harf_rakam($_GET["odeme"]);

if ($id == 0 || strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
    die();
}

// Danışman bilgilerini kontrol et
$kontrol = $db->prepare("SELECT id, concat_ws(' ', adi, soyadi) AS adsoyad, avatar, onecikar, onecikar_btarih FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND kid=?");
$kontrol->execute(array($id, $hesap->id));

if ($kontrol->rowCount() == 0) {
    die();
}

$danisman = $kontrol->fetch(PDO::FETCH_OBJ);

$ua = $fonk->UyelikAyarlar();

if (strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
    header("Location:eklenen-danismanlar");
    die();
}

$secilen = $ua["danisman_onecikar_ucretler"][$periyodu];
if ($secilen["periyod"] == '') {
    die();
}

if ($_SESSION["custom"] == '' && $odeme == "paypal") {
    die('<span class="error">Hay aksi bir sorun oluştu. Lütfen tekrar deneyiniz.</span>');
}

if ($odeme == "paypal") { // PayPal geliyorsa...
    $customs = $_SESSION["custom"];
    $custom = base64_decode($customs);
    $custom = json_decode($custom, true);
    if ($custom["satis"] != "danisman_onecikar") {
        die('<span class="error">Hay aksi bir sorun oluştu. Lütfen tekrar deneyiniz.</span>');
    }

    $fiyat_int = $secilen["tutar"];
?>
<script type="text/javascript">
$("#OdemeButon").slideUp(500, function(){
    $("#SipGoster").slideDown(500);
});

function PayPal_Yonlendir(){
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
<input type="hidden" name="amount" value="<?= htmlspecialchars($fiyat_int, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="currency_code" value="<?= htmlspecialchars($fonk->currency_code(dil("UYELIKP_PBIRIMI")), ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="item_name" value="<?= htmlspecialchars($fonk->eng_cevir($danisman->adsoyad . " " . dil("PAY_NAME3")), ENT_QUOTES, 'UTF-8'); ?>">
</FORM>
<?php
} // Ödeme PayPal ise End...

if ($odeme == "havale_eft") { // Banka Havale / EFT geliyorsa...
    $odeme_yontemi = "Ücretsiz Kayıt";
    $tarih = $fonk->datetime();
    $durum = 0;
    $hesap_id = $hesap->id;

    $hesapp = $hesap;
    $adsoyad = $hesapp->adi;
    $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
    $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
    $baslik = $danisman->adsoyad . " " . dil("PAY_NAME3");

    $fiyat = $gvn->para_str($secilen["tutar"]) . " " . dil("DONECIKAR_PBIRIMI");
    $neresi = "eklenen-danismanlar";

    $fonk->bildirim_gonder(array($adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi), "siparisiniz_alindi", $hesapp->e[...]

    $expiry = "+" . $secilen["sure"];
    $expiry .= ($secilen["periyod"] == "gunluk") ? ' day' : '';
    $expiry .= ($secilen["periyod"] == "aylik") ? ' month' : '';
    $expiry .= ($secilen["periyod"] == "yillik") ? ' year' : '';
    $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";

    try {
        $query = $db->prepare("INSERT INTO onecikan_danismanlar_501 SET acid=?, did=?, durum=?, sure=?, periyod=?, tarih=?, btarih=?, odeme_yontemi=?, tutar=?");
        $query->execute(array($hesap_id, $danisman->id, $durum, $secilen["sure"], $secilen["periyod"], $fonk->datetime(), $btarih, $odeme_yontemi, $secilen["tutar"]));
    } catch (PDOException $e) {
        die($e->getMessage());
    }

    $fonk->yonlendir("eklenen-danismanlar", 3000);
?>
<script type="text/javascript">
$("#OdemePencere").hide(100, function(){
    $("#TamamDiv").show(100);
});
$('html, body').animate({scrollTop: 250}, 500);
</script>
<?php
} // Ödeme Havale/EFT ise End..