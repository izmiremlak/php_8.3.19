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

// Veritabanı hata modunu ayarla
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $gvn->rakam($_GET["id"]);
$from = $gvn->harf_rakam($_GET["from"]);
$odeme = $gvn->harf_rakam($_GET["odeme"]);

// İlan kontrolü
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi=4 AND id=?");
$kontrol->execute(array($id));
if ($kontrol->rowCount() < 1) {
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

// Hesap kontrolü
$acc = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . intval($snc->acid))->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($snc->acid != $hesap->id && $hesap->id != $kid) {
    die();
}

// Ödeme yöntemi kontrolü
if ($_SESSION["custom"] == '' || ($odeme != "havale_eft" && $odeme != "paypal")) {
    die('<span class="error">Hay aksi bir sorun oluştu. Lütfen tekrar deneyiniz.</span>');
}

$customs = $_SESSION["custom"];
$custom = base64_decode($customs);
$custom = json_decode($custom, true);

if ($custom["satis"] != "doping_ekle") {
    die('<span class="error">Hay aksi bir sorun oluştu. Lütfen tekrar deneyiniz.</span>');
}

// PayPal ödemesi
if ($odeme == "paypal") {
    $fiyat_int = $custom["toplam_tutar"];
    ?>
    <script type="text/javascript">
    $("#OdemeButon").slideUp(500,function(){
        $("#SipGoster").slideDown(500);
    });

    function PayPal_Yonlendir(){
        $("#PaypalLocation").submit();
    }
    setTimeout("PayPal_Yonlendir();", 500);
    </script>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="POST" id="PaypalLocation">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="<?= htmlspecialchars($gayarlar->paypal_odeme_email, ENT_QUOTES); ?>">
        <input type="hidden" name="item_number" value="<?= time(); ?>">
        <input type="hidden" name="custom" value="<?= htmlspecialchars($customs, ENT_QUOTES); ?>">
        <input type="hidden" name="cancel_return" value="<?= SITE_URL . "odeme-basarisiz"; ?>">
        <input type="hidden" name="amount" value="<?= htmlspecialchars($fiyat_int, ENT_QUOTES); ?>">
        <input type="hidden" name="currency_code" value="<?= htmlspecialchars($fonk->currency_code(dil("DOPING_PBIRIMI")), ENT_QUOTES); ?>">
        <input type="hidden" name="item_name" value="<?= htmlspecialchars($fonk->eng_cevir($snc->baslik . " " . dil("PAY_NAME")), ENT_QUOTES); ?>">
    </form>
    <?php
} // Ödeme PayPal ise End...

// Havale/EFT ödemesi
if ($odeme == "havale_eft") {
    $odeme_yontemi = "Ücretsiz Kayıt";
    $tarih = $fonk->datetime();
    $durum = 0;
    $hesap_id = $hesap->id;

    $hesapp = $hesap;
    $adsoyad = $hesapp->adi;
    $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
    $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
    $baslik = $snc->baslik . " " . dil("PAY_NAME");

    $fiyat = $gvn->para_str($custom["toplam_tutar"]) . " " . dil("DOPING_PBIRIMI");
    $neresi = "dopinglerim";

    $fonk->bildirim_gonder(
        array($adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi),
        "siparisiniz_alindi",
        $hesapp->email,
        $hesapp->telefon
    );

    try {
        $group = $db->prepare("INSERT INTO dopingler_group_501 SET acid=?, ilan_id=?, tutar=?, tarih=?, odeme_yontemi=?, durum=?");
        $group->execute(array($hesap_id, $custom["ilan_id"], $custom["toplam_tutar"], $tarih, $odeme_yontemi, $durum));
        $gid = $db->lastInsertId();
    } catch (PDOException $e) {
        die($e->getMessage());
    }

    $dopingler_501 = $custom["dopingler_501"];
    foreach ($dopingler_501 as $dop) {
        $expiry = "+" . $dop["sure"];
        $expiry .= ($dop["periyod"] == "gunluk") ? ' day' : '';
        $expiry .= ($dop["periyod"] == "aylik") ? ' month' : '';
        $expiry .= ($dop["periyod"] == "yillik") ? ' year' : '';
        $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";
        try {
            $olustur = $db->prepare("INSERT INTO dopingler_501 SET acid=?, ilan_id=?, did=?, tutar=?, adi=?, sure=?, periyod=?, tarih=?, btarih=?, durum=?, gid=?");
            $olustur->execute(array($hesap_id, $custom["ilan_id"], $dop["did"], $dop["tutar"], $dop["adi"], $dop["sure"], $dop["periyod"], $tarih, $btarih, $durum, $gid));
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    if ($from == "insert") {
        unset($_SESSION["custom"]);
        $fonk->yonlendir("aktif-ilanlar", 5000);
        ?>
        <script type="text/javascript">
        $(document).ready(function(){
            $(".ilanasamax").removeAttr("id");
            $(".islem_tamam").attr("id","asamaaktif");
        });
        $("#doping_ekle").hide(1,function(){
            $("#TamamDiv").show(1);
            ajaxHere('ajax.php?p=ilan_son_asama&id=<?= $snc->id; ?>','asama_result');
        });
        $('html, body').animate({scrollTop: 250}, 500);
        </script>
        <?php
    } else {
        $fonk->yonlendir("dopinglerim", 1);
    }
} // Ödeme Havale/EFT ise End..