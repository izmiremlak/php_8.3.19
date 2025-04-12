<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Girdi verilerini al ve kontrol et
$id = $gvn->zrakam($_GET["id"]);
$periyodu = $gvn->zrakam($_GET["periyod"]);
$odeme = $gvn->harf_rakam($_GET["odeme"]);

if ($id == 0 || strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
    header("Location:uyelik-paketleri");
    die();
}

// Paket bilgilerini sorgula
$sorgula = $db->prepare("SELECT * FROM uyelik_paketleri_501 WHERE id=?");
$sorgula->execute([$id]);
if ($sorgula->rowCount() < 1) {
    header("Location:uyelik-paketleri");
    die();
}
$paket = $sorgula->fetch(PDO::FETCH_OBJ);
$ucretler = json_decode($paket->ucretler, true);
$secilen = $ucretler[$periyodu];

if ($secilen["periyod"] == '') {
    header("Location:uyelik-paketleri");
    die();
}
?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX570"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <span><?= htmlspecialchars(dil("TX571"), ENT_QUOTES, 'UTF-8'); ?></span>
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
                    <h4 class="uyepaneltitle"><?= htmlspecialchars(dil("TX572"), ENT_QUOTES, 'UTF-8'); ?></h4>

                    <?php
                    if ($odeme == "paypal" || $odeme == "paytr" || $odeme == "iyzico") {
                        $customs = array();
                        $customs["acid"] = $hesap->id;
                        $customs["satis"] = "uyelik_paketi";
                        $customs["paket"] = $paket->id;
                        $customs["periyod"] = $periyodu;
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
                                <?= $dayarlar->hesap_numaralari; ?>
                            </p>

                            <div style="text-align:center;margin-top:25px;">
                                <a class="btn" href="javascript:;" onclick="ajaxHere('ajax.php?p=upaket_siparis&id=<?= (int)$paket->id; ?>&periyod=<?= (int)$periyodu; ?>&odeme=havale_eft','SipSonuc');"><?= htmlspecialchars(dil("TX544"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-angle-right" aria-hidden="true"></i></a>
                                <div class="clear"></div>
                                <div id="SipSonuc"></div>
                                <a class="btn" href="javascript:window.history.back();"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                            </div>
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
                        <?php
                        $fonk->paytr_frame(
                            htmlspecialchars($hesap->adi, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($hesap->soyadi, ENT_QUOTES, 'UTF-8'),
                            htmlspecialchars($hesap->email, ENT_QUOTES, 'UTF-8'),
                            htmlspecialchars($hesap->adres, ENT_QUOTES, 'UTF-8'),
                            htmlspecialchars($hesap->telefon, ENT_QUOTES, 'UTF-8'),
                            htmlspecialchars($urunadi, ENT_QUOTES, 'UTF-8'),
                            $ftutar,
                            $oid
                        );
                        ?>
                        <!-- SanalPos frame kodu end -->
                        <a class="btn" href="javascript:window.history.back();"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

                    <?php } elseif ($odeme == "iyzico") { // Ödeme iyzico ile ise... ?>
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
                                $basketItems = array();
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
                                return $checkoutFormInitialize;
 
 
<?php
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
<?php } else {
    echo '<span class="error">Hata Mesajı: ' . htmlspecialchars($sonuc->geterrorMessage(), ENT_QUOTES, 'UTF-8') . '</span>';
} ?>
<a class="btn" href="javascript:window.history.back();"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

<?php } elseif ($odeme == "paypal") { // Ödeme PayPal ile ise... ?>
    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= htmlspecialchars(dil("TX543"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <?php
    if ($fonk->bosluk_kontrol($_SESSION["custom"]) == true) {
        $_SESSION["custom"] = $customs;
    }
    ?>
    <center>
        <H4><?= htmlspecialchars(dil("TX545"), ENT_QUOTES, 'UTF-8'); ?></H4>
    </center>

    <div id="OdemeButon" style="text-align:center;margin-top:25px;"><a class="btn" href="javascript:;" onclick="ajaxHere('ajax.php?p=upaket_siparis&id=<?= (int)$paket->id; ?>&periyod=<?= (int)$periyodu; ?>&odeme=paypal','SipSonuc');"><?= htmlspecialchars(dil("TX546"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a></div>

    <h4 style="color:green;margin-top:20px; display:none" id="SipGoster"><?= htmlspecialchars(dil("TX547"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <a class="btn" href="javascript:window.history.back();"><?= htmlspecialchars(dil("TX515"), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

    <div class="clear"></div>
    <div id="SipSonuc"></div>

<?php } else {
    unset($_SESSION["custom"]);
?>
    <form action="uyelik-paketi-satinal" method="GET" id="OdemeYontemiForm">
        <input type="hidden" name="id" value="<?= (int)$id; ?>">
        <input type="hidden" name="periyod" value="<?= (int)$periyodu; ?>">

        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td bgcolor="#eee"><h5><strong><?= htmlspecialchars(dil("TX535"), ENT_QUOTES, 'UTF-8'); ?></strong></h5></td>
                <td align="center" bgcolor="#eee"><h5><strong><?= htmlspecialchars(dil("TX536"), ENT_QUOTES, 'UTF-8'); ?></strong></h5></td>
            </tr>

            <tr>
                <td><strong><?= htmlspecialchars($paket->baslik, ENT_QUOTES, 'UTF-8'); ?></strong>
                    <br />
                    <span><?= htmlspecialchars(dil("TX594"), ENT_QUOTES, 'UTF-8'); ?>: <strong><?= ($paket->aylik_ilan_limit == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->aylik_ilan_limit, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars(dil("TX595"), ENT_QUOTES, 'UTF-8'); ?></strong> <a href="#" class="tooltip-bottom" data-tooltip="<?= htmlspecialchars(str_replace("[aylik_ilan_limit]", ($paket->aylik_ilan_limit == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->aylik_ilan_limit, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars(dil("TX595"), ENT_QUOTES, 'UTF-8'), dil("TX596")), ENT_QUOTES, 'UTF-8'); ?>"><i style="margin-left: 7px; font-size: 16px;" class="fa fa-question-circle-o" aria-hidden="true"></i></a></span>
                    <br />
                    <span><?= htmlspecialchars(dil("TX587"), ENT_QUOTES, 'UTF-8'); ?>: <strong><?= ($paket->ilan_resim_limit == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->ilan_resim_limit, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars(dil("TX581"), ENT_QUOTES, 'UTF-8'); ?></strong> <a href="#" class="tooltip-bottom" data-tooltip="<?= htmlspecialchars(str_replace("[ilan_resim_limit]", ($paket->ilan_resim_limit == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->ilan_resim_limit, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars(dil("TX581"), ENT_QUOTES, 'UTF-8'), dil("TX597")), ENT_QUOTES, 'UTF-8'); ?>"><i style="margin-left: 7px; font-size: 16px;" class="fa fa-question-circle-o" aria-hidden="true"></i></a></span>
                    <br />
                    <span><?= htmlspecialchars(dil("TX588"), ENT_QUOTES, 'UTF-8'); ?>: <strong><?= ($paket->ilan_yayin_sure == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->ilan_yayin_sure, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($periyod[$paket->ilan_yayin_periyod], ENT_QUOTES, 'UTF-8'); ?></strong> <a href="#" class="tooltip-bottom" data-tooltip="<?= htmlspecialchars(str_replace("[ilan_yayin|sure|periyod]", ($paket->ilan_yayin_sure == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->ilan_yayin_sure, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($periyod[$paket->ilan_yayin_periyod], ENT_QUOTES, 'UTF-8'), dil("TX599")), ENT_QUOTES, 'UTF-8'); ?>"><i style="margin-left: 7px; font-size: 16px;" class="fa fa-question-circle-o" aria-hidden="true"></i></a></span>
                    <br />

                    <?php if ($hesap->turu == 1) { ?>
                        <span><?= htmlspecialchars(dil("TX600"), ENT_QUOTES, 'UTF-8'); ?>: <strong><?= ($paket->danisman_limit == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->danisman_limit, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars(dil("TX581"), ENT_QUOTES, 'UTF-8'); ?></strong> <a href="#" class="tooltip-bottom" data-tooltip="<?= htmlspecialchars(str_replace("[danisman_limit]", ($paket->danisman_limit == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->danisman_limit, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars(dil("TX581"), ENT_QUOTES, 'UTF-8'), dil("TX601")), ENT_QUOTES, 'UTF-8'); ?>"><i style="margin-left: 7px; font-size: 16px;" class="fa fa-question-circle-o" aria-hidden="true"></i></a></span>
                        <br />
                        <span><?= htmlspecialchars(dil("TX589"), ENT_QUOTES, 'UTF-8'); ?> <a href="#" class="tooltip-bottom" data-tooltip="<?= htmlspecialchars(dil("TX602"), ENT_QUOTES, 'UTF-8'); ?>"><i style="margin-left: 7px; font-size: 16px;" class="fa fa-question-circle-o" aria-hidden="true"></i></a></span>
                        <br />
                        <span><?= htmlspecialchars(dil("TX590"), ENT_QUOTES, 'UTF-8'); ?> <a href="#" class="tooltip-bottom" data-tooltip="<?= htmlspecialchars(dil("TX603"), ENT_QUOTES, 'UTF-8'); ?>"><i style="margin-left: 7px; font-size: 16px;" class="fa fa-question-circle-o" aria-hidden="true"></i></a></span>
                        <br />
                        <span><?= htmlspecialchars(dil("TX591"), ENT_QUOTES, 'UTF-8'); ?> <a href="#" class="tooltip-bottom" data-tooltip="<?= htmlspecialchars(dil("TX604"), ENT_QUOTES, 'UTF-8'); ?>"><i style="margin-left: 7px; font-size: 16px;" class="fa fa-question-circle-o" aria-hidden="true"></i></a></span>
                        <br />
                        <?php if ($paket->danisman_onecikar == 1) { ?>
                            <span><?= htmlspecialchars(dil("TX605"), ENT_QUOTES, 'UTF-8'); ?>: <strong><?= ($paket->danisman_onecikar_sure == 0) ? htmlspecialchars(dil("TX622"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($paket->danisman_onecikar_sure, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($periyod[$paket->danisman_onecikar_periyod], ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars(dil("TX583"), ENT_QUOTES, 'UTF-8'); ?></strong> <a href="#" class="tooltip-bottom" data-tooltip="<?= htmlspecialchars(dil("TX606"), ENT_QUOTES, 'UTF-8'); ?>"><i style="margin-left: 7px; font-size: 16px;" class="fa fa-question-circle-o" aria-hidden="true"></i></a></span>
                        <?php } else { ?>
                            <span style="line-height: 39px;">--</span>
                        <?php } ?>
                    <?php } ?>
                </td>
                <td align="center">(<?= ($secilen["sure"] == 0 || $secilen["sure"] == 1) ? '' : htmlspecialchars($secilen["sure"], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($periyod[$secilen["periyod"]], ENT_QUOTES, 'UTF-8'); ?>)<br><strong><?= htmlspecialchars($gvn->para_str($secilen["tutar"]), ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars(dil("UYELIKP_PBIRIMI"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
            </tr>

        </table>

        <H4 style="float:right;margin-top:25px;margin-bottom:25px;" id="ToplamOdenecek"><?= htmlspecialchars(dil("TX523"), ENT_QUOTES, 'UTF-8'); ?>: <strong><font id="toplam_tutar"><?= htmlspecialchars($gvn->para_str($secilen["tutar"]), ENT_QUOTES, 'UTF-8'); ?></font> <?= htmlspecialchars(dil("UYELIKP_PBIRIMI"), ENT_QUOTES, 'UTF-8'); ?></strong></H4>

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
                <input id="odeme4" class="radio-custom" name="odeme" value="iyzico" type="radio" style="width:100px;">
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

        <script type="text/javascript">
            function OdemeYap() {
                var odeme_yontemi = $("input[name='odeme']:checked").val();
                if (odeme_yontemi == '' || odeme_yontemi == undefined) {
                    $("#OdemeYontemiForm_output").html('<span class="error"><?= htmlspecialchars(dil("TX542"), ENT_QUOTES, 'UTF-8'); ?></span>');
                } else {
                    $("#OdemeYontemiForm").submit();
                }
            }
        </script>
<?php } ?>

</div>

</div>
</div>
<div class="sidebar">
    <?php include THEME_DIR . "inc/uyepanel_sidebar.php"; ?>
</div>
</div>
<div class="clear"></div>

</div>