<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

if ($hesap->turu != 1) {
    die();
}

$id = $gvn->rakam($_GET["id"]);

// Kullanıcı kontrolü
$kontrol = $db->prepare("SELECT id, adi, soyadi, avatar, onecikar, onecikar_btarih FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND kid=?");
$kontrol->execute([$id, $hesap->id]);

if ($kontrol->rowCount() == 0) {
    die();
}

$danisman = $kontrol->fetch(PDO::FETCH_OBJ);

$ua = $fonk->UyelikAyarlar();
$periyodu = $gvn->zrakam($_GET["periyod"]);
$odeme = $gvn->harf_rakam($_GET["odeme"]);

?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX632"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <span><?= htmlspecialchars(dil("TX633"), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">

<div class="uyepanel">

<div class="content">

<div class="uyedetay">
<div class="uyeolgirisyap">
    <h4 class="uyepaneltitle"><?= htmlspecialchars(dil("TX632"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <?php
    if ($odeme == "paypal" || $odeme == "paytr" || $odeme == "iyzico") {
        if (strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
            header("Location:eklenen-danismanlar");
            die();
        }
        $secilen = $ua["danisman_onecikar_ucretler"][$periyodu];
        if ($secilen["periyod"] == '') {
            header("Location:eklenen-danismanlar");
            die();
        }
        $customs = [];
        $customs["acid"] = $hesap->id;
        $customs["satis"] = "danisman_onecikar";
        $customs["danisman"] = $danisman->id;
        $customs["periyod"] = $periyodu;
        $customs["tutar"] = $secilen["tutar"];
        $customs = $fonk->json_encode_tr($customs);
        $customs = base64_encode($customs);
    }
    ?>

    <?php if ($odeme == "havale_eft") { // Ödeme Banka Havale/EFT ile ise... ?>

    <div id="TamamDiv" style="display:none">
    <!-- TAMAM MESAJ -->
    <div style="margin-bottom:70px;text-align:center;" id="BasvrTamam">
        <i style="font-size:80px;color:green;" class="fa fa-check"></i>
        <h2 style="color:green;font-weight:bold;"><?= htmlspecialchars(dil("TX573"), ENT_QUOTES, 'UTF-8'); ?></h2>
        <br/>
        <h4><?= htmlspecialchars(dil("TX574"), ENT_QUOTES, 'UTF-8'); ?></h4>
    </div>
    <!-- TAMAM MESAJ -->
    </div>

    <div id="OdemePencere">
    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= htmlspecialchars(dil("TX543"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <p>
    <?= htmlspecialchars($dayarlar->hesap_numaralari, ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <div style="text-align:center;margin-top:25px;">
        <a class="btn" href="javascript:;" onclick="ajaxHere('ajax.php?p=danisman_onecikar_siparis&id=<?= htmlspecialchars($danisman->id, ENT_QUOTES, 'UTF-8'); ?>&periyod=<?= htmlspecialchars($periyodu, ENT_QUOTES, 'UTF-8'); ?>&odeme=<?= htmlspecialchars($odeme, ENT_QUOTES, 'UTF-8'); ?>','SipSonuc');"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
        <div class="clear"></div>
        <div id="SipSonuc"></div>

        <a class="btn" href="javascript:window.history.back();"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
    </div>
    <?php } elseif ($odeme == "paytr") { // Ödeme PayTR ile ise... ?>

    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= htmlspecialchars(dil("TX543"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <?php
    if ($fonk->bosluk_kontrol($_SESSION["custom"]) == true) {
        $_SESSION["custom"] = $customs;
    }

    $fiyat_int = $secilen["tutar"];
    $urunadi = $paket->baslik;
    $oid = time();
    $ftutar = ($fiyat_int * 100);
    $ftutar = (stristr($ftutar, ".")) ? explode(".", $ftutar)[0] : $ftutar;

    $sipce = $db->prepare("INSERT INTO paytr_checks_501 SET acid=?, oid=?, status=?, custom=?, tarih=?, tutar=?");
    $sipce->execute([$hesap->id, $oid, 'waiting', $customs, $fonk->datetime(), $fiyat_int]);
    ?>
    <!-- SanalPos frame kodu -->
    <?php $fonk->paytr_frame(htmlspecialchars($hesap->adi . " " . $hesap->soyadi, ENT_QUOTES, 'UTF-8'), htmlspecialchars($hesap->email, ENT_QUOTES, 'UTF-8'), htmlspecialchars($hesap->adres, ENT_QUOTES, 'UTF-8'), $oid, $ftutar, $urunadi); ?>
    <!-- SanalPos frame kodu end -->
    <a class="btn" href="javascript:window.history.back();"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
    <?php } elseif ($odeme == "iyzico") { // Ödeme Banka Havale/EFT ile ise... ?>

    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= htmlspecialchars(dil("TX543"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <?php
    if ($fonk->bosluk_kontrol($_SESSION["custom"]) == true) {
        $_SESSION["custom"] = $customs;
    }

    $fonk->iyzico_cek();

    class CheckoutFormSample
    {

        public function should_initialize_checkout_form($tutar, $adi, $soyadi, $email, $site_url)
        {
            # create request class
            $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
            $request->setLocale(\Iyzipay\Model\Locale::TR);
            $request->setConversationId("65465464646");
            $request->setPrice($tutar);
            $request->setPaidPrice($tutar);
            $request->setBasketId("BI101");
            $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
            $request->setCallbackUrl($site_url . "odeme-sonuc");
            $buyer = new \Iyzipay\Model\Buyer();
            $buyer->setId("BY789");
            $buyer->setName($adi);
            $buyer->setSurname($soyadi);
            $buyer->setEmail($email);
            $buyer->setIdentityNumber("74300864791");
            $buyer->setRegistrationAddress("Address");
            $buyer->setIp($_SERVER['REMOTE_ADDR']);
            $buyer->setCity("Istanbul");
            $buyer->setCountry("Turkey");
            $buyer->setZipCode("34732");
            $request->setBuyer($buyer);
            $shippingAddress = new \Iyzipay\Model\Address();
            $shippingAddress->setContactName("Jane Doe");
            $shippingAddress->setCity("Istanbul");
            $shippingAddress->setCountry("Turkey");
            $shippingAddress->setAddress("Address");
            $shippingAddress->setZipCode("34742");
            $request->setShippingAddress($shippingAddress);
            $billingAddress = new \Iyzipay\Model\Address();
            $billingAddress->setContactName("Jane Doe");
            $billingAddress->setCity("Istanbul");
            $billingAddress->setCountry("Turkey");
            $billingAddress->setAddress("Address");
            $billingAddress->setZipCode("34742");
            $request->setbillingAddress($billingAddress);
            $basketItems = [];
            $firstBasketItem = new \Iyzipay\Model\BasketItem();
            $firstBasketItem->setId("BI101");
            $firstBasketItem->setName("Test");
            $firstBasketItem->setCategory1("Test1");
            $firstBasketItem->setCategory2("Test2");
            $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
            $firstBasketItem->setPrice($tutar);
            $basketItems[0] = $firstBasketItem;
            $request->setBasketItems($basketItems);
            # make request
            $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, Sample::options());
            # print result
            return $checkoutFormInitialize;
        }
    }

    $fiyat_int = $secilen["tutar"];

    $sample = new CheckoutFormSample();
    $sonuc = $sample->should_initialize_checkout_form($fiyat_int, htmlspecialchars($hesap->adi, ENT_QUOTES, 'UTF-8'), htmlspecialchars($hesap->soyadi, ENT_QUOTES, 'UTF-8'), htmlspecialchars($hesap->email, ENT_QUOTES, 'UTF-8'), SITE_URL);
    $stat = $sonuc->getstatus();
    if ($stat == 'success') {
        echo $sonuc->getCheckoutFormContent();
    ?>
    <div style="width: 80%;margin-top: 20px;">
    <div id="iyzipay-checkout-form" class="responsive"></div>
    </div>
    <?php
    } else {
        echo '<span class="error">Hata Mesajı: ' . htmlspecialchars($sonuc->geterrorMessage(), ENT_QUOTES, 'UTF-8') . '</span>';
    }
    ?>
    <a class="btn" href="javascript:window.history.back();"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

    <?php } elseif ($odeme == "paypal") { // Ödeme PayPal ile ise... ?>

    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= htmlspecialchars(dil("TX543"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <?php
    if ($fonk->bosluk_kontrol($_SESSION["custom"]) == true) {
        $_SESSION["custom"] = $customs;
    }
    ?>
    <center>
    <h4><?= htmlspecialchars(dil("TX545"), ENT_QUOTES, 'UTF-8'); ?></h4>
    </center>

    <div id="OdemeButon" style="text-align:center;margin-top:25px;"><a class="btn" href="javascript:;" onclick="ajaxHere('ajax.php?p=danisman_onecikar_siparis&id=<?= htmlspecialchars($danisman->id, ENT_QUOTES, 'UTF-8'); ?>&periyod=<?= htmlspecialchars($periyodu, ENT_QUOTES, 'UTF-8'); ?>&odeme=<?= htmlspecialchars($odeme, ENT_QUOTES, 'UTF-8'); ?>','SipSonuc');"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a></div>

    <h4 style="color:green;margin-top:20px; display:none" id="SipGoster"><?= htmlspecialchars(dil("TX547"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <a class="btn" href="javascript:window.history.back();"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

    <div class="clear"></div>
    <div id="SipSonuc"></div>
    <?php } else {
        unset($_SESSION["custom"]);
    ?>
    <form action="danisman-one-cikar" method="GET" id="OdemeYontemiForm">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">

    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td bgcolor="#eee"><h5><strong><?= htmlspecialchars(dil("TX535"), ENT_QUOTES, 'UTF-8'); ?></strong></h5></td>
            <td align="center" bgcolor="#eee"><h5><strong><?= htmlspecialchars(dil("TX536"), ENT_QUOTES, 'UTF-8'); ?></strong></h5></td>
        </tr>

        <tr>
            <td><strong><?= htmlspecialchars($danisman->adi . " " . $danisman->soyadi, ENT_QUOTES, 'UTF-8'); ?></strong> - <?= htmlspecialchars(dil("TX634"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td align="center">
            <select name="periyod">
            <option value=""><?= htmlspecialchars(dil("TX264"), ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
            $ucretler = $ua["danisman_onecikar_ucretler"];
            foreach ($ucretler as $idi => $urow) {
                $suresi = $urow["sure"];
                $periyodu = $periyod[$urow["periyod"]];
                $tutar = $gvn->para_str($urow["tutar"]);
            ?><option value="<?= htmlspecialchars($idi, ENT_QUOTES, 'UTF-8'); ?>" data-money="<?= htmlspecialchars($tutar, ENT_QUOTES, 'UTF-8'); ?>"><?php echo ($suresi != 0) ? htmlspecialchars($suresi, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($periyodu, ENT_QUOTES, 'UTF-8') : htmlspecialchars(dil("TX578"), ENT_QUOTES, 'UTF-8'); ?></option><?php
            }
            ?>
            </select>
            </td>
        </tr>
    </table>

    <h4 style="float:right;margin-top:25px;margin-bottom:25px;" id="ToplamOdenecek"><?= htmlspecialchars(dil("TX523"), ENT_QUOTES, 'UTF-8'); ?>: <strong><font id="toplam_tutar">0</font> <?= htmlspecialchars(dil("TX524"), ENT_QUOTES, 'UTF-8'); ?></strong></h4>

    <div class="clear"></div>
    <hr style="border: 1px solid #eee;">
    <br>
    <div style="width: 200px; margin: auto;">
    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= htmlspecialchars(dil("TX537"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <input id="odeme1" class="radio-custom" name="odeme" value="havale_eft" type="radio" style="width:100px;">
    <label for="odeme1" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?= htmlspecialchars(dil("TX538"), ENT_QUOTES, 'UTF-8'); ?></span></label>
    <div class="clear"></div>

<?php if ($gayarlar->paytr == 1) { ?>
    <input id="odeme2" class="radio-custom" name="odeme" value="paytr" type="radio" style="width:100px;">
    <label for="odeme2" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?= htmlspecialchars(dil("TX539"), ENT_QUOTES, 'UTF-8'); ?></span></label>
    <div class="clear"></div>
<?php } ?>

<?php if ($gayarlar->iyzico == 1) { ?>
    <input id="odeme3" class="radio-custom" name="odeme" value="iyzico" type="radio" style="width:100px;">
    <label for="odeme3" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?= htmlspecialchars(dil("TX539"), ENT_QUOTES, 'UTF-8'); ?></span></label>
    <div class="clear"></div>
<?php } ?>

<?php if ($gayarlar->paypal == 1) { ?>
    <input id="odeme4" class="radio-custom" name="odeme" value="paypal" type="radio" style="width:100px;">
    <label for="odeme4" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext">PayPal</span></label>
    <div class="clear"></div>
<?php } ?>

</div>
<br>
<hr style="border: 1px solid #eee;">

<div class="clear"></div>

<div class="clear"></div>
<br />

<div align="right">
    <a style="margin-left: 15px;" class="btn" href="javascript:void(0);" onclick="OdemeYap();" id="DopingleButon"><i class="fa fa-check" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX540"), ENT_QUOTES, 'UTF-8'); ?></a>
</div>

<div id="OdemeYontemiForm_output"></div>

</form>

<script type="text/javascript">
$(document).ready(function() {

    $("select[name='periyod']").change(function() {
        var money = $("select[name='periyod'] option:selected").attr("data-money");
        if (money == '' || money == undefined) {
            $("#toplam_tutar").html(0);
        } else {
            $("#toplam_tutar").html(money);
        }
    });

});

function OdemeYap() {
    var odeme_yontemi = $("input[name='odeme']:checked").val();
    var periyod = $("select[name='periyod'] option:selected").val();
    if (periyod == '' || periyod == undefined) {
        $("#OdemeYontemiForm_output").html('<span class="error"><?= htmlspecialchars(dil("TX610"), ENT_QUOTES, 'UTF-8'); ?></span>');
    } else if (odeme_yontemi == '' || odeme_yontemi == undefined) {
        $("#OdemeYontemiForm_output").html('<span class="error"><?= htmlspecialchars(dil("TX542"), ENT_QUOTES, 'UTF-8'); ?></span>');
    } else {
        $("#OdemeYontemiForm").submit();
    }
}
</script>
<?php } ?>

</div>

</div>
</div><div class="sidebar">
<?php include THEME_DIR . "inc/uyepanel_sidebar.php"; ?>
</div>
</div>
<div class="clear"></div>

</div>
