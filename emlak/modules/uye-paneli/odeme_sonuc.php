<?php
$customs = $_SESSION["custom"];
if ($fonk->bosluk_kontrol($customs) == false) {
    $custom = base64_decode($customs);
    $custom = json_decode($custom, true);
}
?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . $gayarlar->belgeler_resim . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= dil("TX549"); ?></h1>
            <div class="sayfayolu">
                <!--span>...</span-->
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
                    <h4 class="uyepaneltitle"><?= dil("TX549"); ?></h4>

                    <?php

                    $fonk->iyzico_cek();

                    class CheckoutFormSample
                    {

                        public function should_retrieve_checkout_form_auth()
                        {
                            # create request class
                            $request = new \Iyzipay\Request\RetrieveCheckoutFormAuthRequest();
                            $request->setLocale(\Iyzipay\Model\Locale::TR);
                            $request->setConversationId("123456789");
                            $request->setToken($GLOBALS['gvn']->harf_rakam($_POST["token"]));

                            # make request
                            $checkoutFormAuth = \Iyzipay\Model\CheckoutFormAuth::retrieve($request, Sample::options());

                            # print result
                            #return $checkoutFormAuth;

                            return array(
                                'pay_status' => $checkoutFormAuth->getPaymentStatus(),
                                'status' => $checkoutFormAuth->getstatus(),
                                'errorCode' => $checkoutFormAuth->geterrorCode(),
                                'errorMessage' => $checkoutFormAuth->geterrorMessage(),
                            );
                        }
                    }

                    $odeme = 0;

                    $customx = $customs;

                    if ($fonk->bosluk_kontrol($customx) == false) {

                        $sample = new CheckoutFormSample();
                        $sonuc = $sample->should_retrieve_checkout_form_auth();
                        $satis = $custom['satis'];

                        if ($custom['acid'] != '') {
                            $hesapp = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = ? ");
                            $hesapp->execute([$custom['acid']]);
                            $hesapp = $hesapp->fetch(PDO::FETCH_OBJ);
                        }

                        if ($sonuc['status'] == 'success' && $sonuc['pay_status'] == 'SUCCESS') {
                            $odeme = 1;

                            if ($satis == "doping_ekle") {

                                $kontrol = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi = 4 AND id = ?");
                                $kontrol->execute([$custom["ilan_id"]]);
                                if ($kontrol->rowCount() < 1) {
                                    die();
                                }
                                $snc = $kontrol->fetch(PDO::FETCH_OBJ);

                                $odeme_yontemi = "Kredi Kartı";
                                $tarih = $fonk->datetime();
                                $durum = 1;
                                $hesap_id = $hesapp->id;

                                $adsoyad = $hesapp->adi;
                                $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
                                $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
                                $baslik = $snc->baslik . " " . dil("PAY_NAME");

                                $fiyat = $gvn->para_str($custom["toplam_tutar"]) . " " . dil("DOPING_PBIRIMI");
                                $neresi = "dopinglerim";

                                $fonk->bildirim_gonder(
                                    array(
                                        $adsoyad,
                                        $hesapp->email,
                                        $hesapp->parola,
                                        $baslik,
                                        $fiyat,
                                        date("d.m.Y H:i", strtotime($fonk->datetime())),
                                        SITE_URL . $neresi
                                    ),
                                    "siparis_onaylandi",
                                    $hesapp->email,
                                    $hesapp->telefon
                                );

                                try {
                                    $group = $db->prepare("INSERT INTO dopingler_group_501 SET acid=?, ilan_id=?, tutar=?, tarih=?, odeme_yontemi=?, durum=?");
                                    $group->execute([$hesap_id, $custom["ilan_id"], $custom["toplam_tutar"], $tarih, $odeme_yontemi, $durum]);
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
                                        $olustur->execute([
                                            $hesap_id,
                                            $custom["ilan_id"],
                                            $dop["did"],
                                            $dop["tutar"],
                                            $dop["adi"],
                                            $dop["sure"],
                                            $dop["periyod"],
                                            $tarih,
                                            $btarih,
                                            $durum,
                                            $gid
                                        ]);
                                    } catch (PDOException $e) {
                                        die($e->getMessage());
                                    }
                                }
                            } elseif ($satis == "uyelik_paketi") {

                                $id = $custom["paket"];
                                $periyodu = $custom["periyod"];

                                if ($id == 0 || strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
                                    die("?ok fazla bekleme yaptınız.");
                                }

                                $sorgula = $db->prepare("SELECT * FROM uyelik_paketleri_501 WHERE id = ?");
                                $sorgula->execute([$id]);
                                if ($sorgula->rowCount() < 1) {
                                    die();
                                }

                                $paket = $sorgula->fetch(PDO::FETCH_OBJ);
                                $ucretler = json_decode($paket->ucretler, true);
                                $secilen = $ucretler[$periyodu];

                                if ($secilen["periyod"] == '') {
                                    die();
                                }

                                $odeme_yontemi = "Kredi Kartı";
                                $tarih = $fonk->datetime();
                                $durum = 1;
                                $hesap_id = $hesapp->id;

                                $adsoyad = $hesapp->adi;
                                $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
                                $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
                                $baslik = $paket->baslik . " " . dil("PAY_NAME2");

                                $fiyat = $gvn->para_str($secilen["tutar"]) . " " . dil("UYELIKP_PBIRIMI");
                                $neresi = "paketlerim";

                                $fonk->bildirim_gonder(
                                    array(
                                        $adsoyad,
                                        $hesapp->email,
                                        $hesapp->parola,
                                        $baslik,
                                        $fiyat,
                                        date("d.m.Y H:i", strtotime($fonk->datetime())),
                                        SITE_URL . $neresi
                                    ),
                                    "siparis_onaylandi",
                                    $hesapp->email,
                                    $hesapp->telefon
                                );

                                $expiry = "+" . $secilen["sure"];
                                $expiry .= ($secilen["periyod"] == "gunluk") ? ' day' : '';
                                $expiry .= ($secilen["periyod"] == "aylik") ? ' month' : '';
                                $expiry .= ($secilen["periyod"] == "yillik") ? ' year' : '';
                                $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";

                                try {
                                    $query = $db->prepare("INSERT INTO upaketler_501 SET acid=?, pid=?, adi=?, tutar=?, durum=?, odeme_yontemi=?, tarih=?, btarih=?, sure=?, periyod=?, aylik_ilan_limit=?, ilan_resim_limit=?, ilan_yayin_sure=?, ilan_yayin_periyod=?, danisman_limit=?, danisman_onecikar=?, danisman_onecikar_sure=?, danisman_onecikar_periyod=?");
                                    $query->execute([
                                        $hesap_id,
                                        $paket->id,
                                        $paket->baslik,
                                        $secilen["tutar"],
                                        $durum,
                                        $odeme_yontemi,
                                        $tarih,
                                        $btarih,
                                        $secilen["sure"],
                                        $secilen["periyod"],
                                        $paket->aylik_ilan_limit,
                                        $paket->ilan_resim_limit,
                                        $paket->ilan_yayin_sure,
                                        $paket->ilan_yayin_periyod,
                                        $paket->danisman_limit,
                                        $paket->danisman_onecikar,
                                        $paket->danisman_onecikar_sure,
                                        $paket->danisman_onecikar_periyod
                                    ]);
                                } catch (PDOException $e) {
                                    die($e->getMessage());
                                }
                            } elseif ($satis == "danisman_onecikar") {

                                $id = $custom["danisman"];
                                $periyodu = $custom["periyod"];

                                if ($id == 0 || strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
                                    die("?ok fazla bekleme yaptınız.");
                                }

                                $kontrol = $db->prepare("SELECT id, adi, soyadi, avatar, onecikar, onecikar_btarih FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = ?");
                                $kontrol->execute([$id]);

                                if ($kontrol->rowCount() == 0) {
                                    die();
                                }
								
<?php
$danisman = $kontrol->fetch(PDO::FETCH_OBJ);

$ua = $fonk->UyelikAyarlar();
$secilen = $ua["danisman_onecikar_ucretler"][$periyodu];

if ($secilen["periyod"] == '') {
    die();
}

$odeme_yontemi = "Kredi Kartı";
$tarih = $fonk->datetime();
$durum = 1;
$hesap_id = $hesapp->id;

$adsoyad = $hesapp->adi;
$adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
$adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
$baslik = $danisman->adsoyad . " " . dil("PAY_NAME3");

$fiyat = $gvn->para_str($secilen["tutar"]) . " " . dil("DONECIKAR_PBIRIMI");
$neresi = "eklenen-danismanlar";

$fonk->bildirim_gonder(
    array(
        $adsoyad,
        $hesapp->email,
        $hesapp->parola,
        $baslik,
        $fiyat,
        date("d.m.Y H:i", strtotime($fonk->datetime())),
        SITE_URL . $neresi
    ),
    "siparis_onaylandi",
    $hesapp->email,
    $hesapp->telefon
);

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

$daUpdate = $db->query("UPDATE hesaplar SET onecikar=1, onecikar_btarih='" . $btarih . "' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $danisman->id);

} else {
    $odeme = 0;
    $hata  = "Geçersiz bir sipariş.";
}

} else {
    $hata  = "Status not success: " . htmlspecialchars($sonuc['errorMessage'], ENT_QUOTES, 'UTF-8');
}
} else {
    $hata  = "Geçersiz bir custom girildi.";
}

if ($odeme == 1) {
?>
<div style="margin-top:60px;margin-bottom:60px;text-align:center;">
    <i style="font-size:80px;color:green;" class="fa fa-check"></i>
    <h2 style="color:green;font-weight:bold;"><?= dil("TX550"); ?></h2>
    <br />
    <h4><?= dil("TX551"); ?><br></h4><br><br>
    <?php
    if ($customs != '') {
        if ($custom["satis"] == "doping_ekle") {
            if ($_SESSION["advfrom"] == "insert") {
                header("Refresh:3; url=ilan-olustur?id=" . $custom["ilan_id"] . "&asama=3");
                echo dil("TX552");
            } elseif ($_SESSION["advfrom"] == "adv") {
                header("Refresh:3; url=uye-paneli?rd=ilan_duzenle&id=" . $custom["ilan_id"] . "&goto=doping");
                echo dil("TX552");
            }
        } elseif ($custom["satis"] == "uyelik_paketi") {
            header("Refresh:2; url=paketlerim");
            echo dil("TX552");
        } elseif ($custom["satis"] == "danisman_onecikar") {
            header("Refresh:2; url=eklenen-danismanlar");
            echo dil("TX552");
        }
        unset($_SESSION["custom"]);
        unset($_SESSION["advfrom"]);
    }
    ?>
</div>

<?php } else { ?>

<div style="margin-top:60px;margin-bottom:60px;text-align:center;">
    <i style="font-size:80px;color:red;" class="fa fa-close"></i>
    <h2 style="color:red;font-weight:bold;"><?= dil("TX553"); ?></h2>
    <br />
    <h4><?= htmlspecialchars($hata, ENT_QUOTES, 'UTF-8'); ?><br></h4><br><br>
    <?php
    if ($customs != '') {
        if ($custom["satis"] == "doping_ekle") {
            if ($_SESSION["advfrom"] == "insert") {
                header("Refresh:3; url=ilan-olustur?id=" . $custom["ilan_id"] . "&asama=3");
                echo dil("TX552");
            } elseif ($_SESSION["advfrom"] == "adv") {
                header("Refresh:3; url=uye-paneli?rd=ilan_duzenle&id=" . $custom["ilan_id"] . "&goto=doping&odeme=true");
                echo dil("TX552");
            }
        } elseif ($custom["satis"] == "uyelik_paketi") {
            header("Refresh:2; url=paketlerim");
            echo dil("TX552");
        } elseif ($custom["satis"] == "danisman_onecikar") {
            header("Refresh:2; url=eklenen-danismanlar");
            echo dil("TX552");
        }
        unset($_SESSION["custom"]);
        unset($_SESSION["advfrom"]);
    }
    ?>
</div>
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
