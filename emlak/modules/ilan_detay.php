<?php
declare(strict_types=1);

if (!defined("THEME_DIR")) {
    die();
}

error_reporting(E_ALL); // Tüm hata raporlamalarını aç
ini_set('display_errors', '1'); // Hataları ekranda göster
ini_set('log_errors', '1'); // Hataları log dosyasına yaz
ini_set('error_log', __DIR__ . '/error_log.log'); // Log dosyasının yolu

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($sayfay->durum != 1) {
    if ($hesap->id == '') {
        include THEME_DIR . "404.php";
        die();
    } else {
        if ($hesap->tipi != 1) {
            $kid = $db->query("SELECT kid FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . (int)$sayfay->acid)->fetch(PDO::FETCH_OBJ)->kid;
            if ($sayfay->acid != $hesap->id && $hesap->id != $kid) {
                include THEME_DIR . "404.php";
                die();
            }
        }
    }
}

try {
    $surevar = $db->query("SELECT DISTINCT t1.id FROM sayfalar AS t1 LEFT JOIN dopingler_501 AS t2 ON t2.ilan_id=t1.id AND t2.durum=1 WHERE (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.id=" . (int)$sayfay->id . " AND (t1.durum=1 OR t2.id IS NOT NULL)");
    $surevar = $surevar->rowCount();
} catch (PDOException $e) {
    die($e->getMessage());
}

$linkcek = "https://www.turkiyeemlaksitesi.com.tr";
$fiyat_int = $gvn->para_int($sayfay->fiyat);
$fiyat = $gvn->para_str($fiyat_int);

$aidat_int = $gvn->para_int($sayfay->aidat);
$aidat = $gvn->para_str($aidat_int);

$ulke = $db->query("SELECT ulke_adi FROM ulkeler_501 WHERE id=" . (int)$sayfay->ulke_id)->fetch(PDO::FETCH_OBJ)->ulke_adi;
$il = $db->query("SELECT il_adi, slug FROM il WHERE id=" . (int)$sayfay->il_id)->fetch(PDO::FETCH_OBJ);
$il_slug = $il->slug;
$il = $il->il_adi;
$ilce = $db->query("SELECT ilce_adi, slug FROM ilce WHERE id=" . (int)$sayfay->ilce_id)->fetch(PDO::FETCH_OBJ);
$ilce_slug = $ilce->slug;
$ilce = $ilce->ilce_adi;
$mahalle = $db->query("SELECT mahalle_adi, slug FROM mahalle_koy WHERE id=" . (int)$sayfay->mahalle_id)->fetch(PDO::FETCH_OBJ);
$mahalle_slug = $mahalle->slug;
$mahalle = $mahalle->mahalle_adi;
$search = SITE_URL;
$search .= ($sayfay->emlak_durum != '') ? $gvn->PermaLink($sayfay->emlak_durum) . "/" : '';
$search .= ($sayfay->emlak_tipi != '') ? $gvn->PermaLink($sayfay->emlak_tipi) . "/" : '';
$search .= ($sayfay->konut_sekli != '') ? $gvn->PermaLink($sayfay->konut_sekli) . "/" : '';
$search .= ($sayfay->konut_tipi != '') ? $gvn->PermaLink($sayfay->konut_tipi) . "/" : '';
$tarihi = date("d-n-Y", strtotime($sayfay->tarih));
$tarihi = explode("-", $tarihi);

$db->query("UPDATE sayfalar SET hit=hit+1 WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . (int)$sayfay->id);

$arsa = $fonk->get_lang($sayfay->dil, "EMLK_TIPI");
$arsa = explode("<+>", $arsa);
$isyeri = $arsa[1];
$arsa = $arsa[2];
$ilan_linki = ($dayarlar->permalink == 'Evet') ? $sayfay->url . '.html' : 'index.php?p=sayfa&id=' . (int)$sayfay->id;

/*
Danışman için kontroller
 */
if ($sayfay->danisman_id != 0 && $sayfay->danisman_id != 1) {
    $danisman = $db->prepare("SELECT * FROM danismanlar_501 WHERE id=?");
    $danisman->execute([(int)$sayfay->danisman_id]);
    if ($danisman->rowCount() > 0) {
        $danisman = $danisman->fetch(PDO::FETCH_OBJ);

        $adsoyad = $danisman->adsoyad;

        $gsm = ($danisman->gsm != '') ? $danisman->gsm : '';
        $telefon = ($danisman->telefon != '') ? $danisman->telefon : '';
        $demail = ($danisman->email != '') ? $danisman->email : '';
        $davatar = ($danisman->resim != '') ? 'uploads/thumb/' . $danisman->resim : 'uploads/default-avatar.png';
        $profil_link = "javascript:void(0);";
    } else {
        $uye_id = $sayfay->danisman_id;
    }
} else {
    /*sitede ilanların, ilan sahibi bilgileri ile gözükmesi istenirse acid=XXX; de XXX yerine site sahibinin hesap kodu yazılacak. İlan sahibinin bilgileri ile gözükmesi istenirse acid; şeklinde bırakılacak*/
$uye_id = $sayfay->acid=501;
}

if ($uye_id != '') {
    $uyee = $db->prepare("SELECT *, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
    /*sitede ilanların, danışman adıyla bilgileri ile gözükmemesi için $uye_id=XXX de =XXX yerine site sahibinin hesap kodu yazılacak. Danışmanın bilgileri ile gözükmesi istenirse $uye_id şeklinde bırakılacak*/
    $uyee->execute([(int)$uye_id=501]);
    if ($uyee->rowCount() > 0) {
        $uyee = $uyee->fetch(PDO::FETCH_OBJ);

        $adsoyad = ($uyee->unvan == '') ? $uyee->adsoyad : $uyee->unvan;
        $gsm = ($uyee->telefon != '' && $uyee->telefond == 0) ? $uyee->telefon : '';
        $telefon = ($uyee->sabit_telefon != '' && $uyee->sabittelefond == 0) ? $uyee->sabit_telefon : '';
        $demail = ($uyee->email != '' && $uyee->epostad == 0) ? $uyee->email : '';
        $davatar = ($uyee->avatar != '' && $uyee->avatard == 0) ? 'uploads/thumb/' . $uyee->avatar : 'uploads/default-avatar.png';

        $profil_link = "profil/";
        $profil_link .= ($uyee->nick_adi == '') ? $uyee->id : $uyee->nick_adi;
    }
}

$hit = @$fonk->SayiDuzelt($sayfay->hit);

if ($gayarlar->doviz == 1) {
    $xml = @simplexml_load_file("https://www.tcmb.gov.tr/kurlar/today.xml");
    if (count($xml->Currency) > 0) {
        foreach ($xml->Currency as $Currency) {
            if ($Currency["Kod"] == "USD") {
                $usd_DS = $Currency->BanknoteSelling;
                $usd_DA = $Currency->BanknoteBuying;
            } elseif ($Currency["Kod"] == "EUR") {
                $eur_DS = $Currency->BanknoteSelling;
                $eur_DA = $Currency->BanknoteBuying;
            } elseif ($Currency["Kod"] == "GBP") {
                $gbp_DS = $Currency->BanknoteSelling;
                $gbp_DA = $Currency->BanknoteBuying;
            } elseif ($Currency["Kod"] == "CHF") {
                $chf_DS = $Currency->BanknoteSelling;
                $chf_DA = $Currency->BanknoteBuying;
            }
        }
    }
}
  
  
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= htmlspecialchars($sayfay->title == '' ? $sayfay->baslik : $sayfay->title, ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($sayfay->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($sayfay->description, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<meta property="og:image" content="<?php echo "http://" . $_SERVER['SERVER_NAME']; ?>/uploads/<?= htmlspecialchars($sayfay->resim, ENT_QUOTES, 'UTF-8'); ?>"/>
<meta property="og:title" content="<?= htmlspecialchars($sayfay->title == '' ? $sayfay->baslik : $sayfay->title, ENT_QUOTES, 'UTF-8'); ?>" />
<meta property="og:description" content="<?= htmlspecialchars($sayfay->description, ENT_QUOTES, 'UTF-8'); ?>" />
<!-- Meta Tags -->

<?php $category = true; include THEME_DIR . "inc/head.php"; ?>

<link rel="stylesheet" type="text/css" href="<?= THEME_DIR; ?>glib/pgwslideshow.min.css" />
<link rel="stylesheet" type="text/css" href="<?= THEME_DIR; ?>glib/pgwslideshow_light.css" />

</head>
<body>
<?php include THEME_DIR . "inc/header.php"; ?>

<div class="headerbg" <?= ($sayfay->resim2 != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($sayfay->resim2, ENT_QUOTES, 'UTF-8') . ');"' : 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->bayiler_resim, ENT_QUOTES, 'UTF-8') . ');"'; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX139"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <a href="index.html"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX136"), ENT_QUOTES, 'UTF-8'); ?></a>

                <?php if ($sayfay->emlak_durum != '') { ?>
                    <i class="fa fa-caret-right" aria-hidden="true"></i> <a href="<?= htmlspecialchars($gvn->PermaLink($sayfay->emlak_durum), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($sayfay->emlak_durum, ENT_QUOTES, 'UTF-8'); ?></a>
                <?php } ?>

                <?php if ($sayfay->emlak_tipi != '') { ?>
                    <i class="fa fa-caret-right" aria-hidden="true"></i> <a href="<?= htmlspecialchars($gvn->PermaLink($sayfay->emlak_tipi), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($sayfay->emlak_tipi, ENT_QUOTES, 'UTF-8'); ?></a>
                <?php } ?>

                <?php if ($sayfay->konut_sekli != '') { ?>
                    <i class="fa fa-caret-right" aria-hidden="true"></i> <a href="<?= htmlspecialchars($gvn->PermaLink($sayfay->konut_sekli), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($sayfay->konut_sekli, ENT_QUOTES, 'UTF-8'); ?></a>
                <?php } ?>

                <?php if ($sayfay->konut_tipi != '') { ?>
                    <i class="fa fa-caret-right" aria-hidden="true"></i> <a href="<?= htmlspecialchars($gvn->PermaLink($sayfay->konut_tipi), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($sayfay->konut_tipi, ENT_QUOTES, 'UTF-8'); ?></a>
                <?php } ?>

                <?php if ($il != '') { ?>
                    <i class="fa fa-caret-right" aria-hidden="true"></i> <a href="<?= htmlspecialchars($il_slug, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($il, ENT_QUOTES, 'UTF-8'); ?></a>

                    <?php if ($ilce != '') { ?>
                        <i class="fa fa-caret-right" aria-hidden="true"></i> <a href="<?= htmlspecialchars($il_slug, ENT_QUOTES, 'UTF-8') . '-' . htmlspecialchars($ilce_slug, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($ilce, ENT_QUOTES, 'UTF-8'); ?></a>

                        <?php if ($mahalle != '') { ?>
                            <i class="fa fa-caret-right" aria-hidden="true"></i> <a href="<?= htmlspecialchars($il_slug, ENT_QUOTES, 'UTF-8') . '-' . htmlspecialchars($ilce_slug, ENT_QUOTES, 'UTF-8') . '-' . htmlspecialchars($mahalle_slug, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($mahalle, ENT_QUOTES, 'UTF-8'); ?></a>
                        <?php } ?>

                    <?php } ?>

                <?php } ?>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>


<?php if ($surevar == 0) {
    include THEME_DIR . "ilan_detay_sure_doldu.php";
    die();
} ?>

<div id="wrapper">

    <div class="content" id="bigcontent">
        <?php include THEME_DIR . "inc/sosyal_butonlar.php"; ?>

        <div class="altbaslik">
            <h4><strong><?= htmlspecialchars($sayfay->baslik, ENT_QUOTES, 'UTF-8'); ?></strong></h4>
        </div>

        <style>
            .ilanyeniozellik {
                padding: 0px 5px;
                /* background: green; */
                color: green;
                font-size: 12px;
                border: 1px solid green;
                margin-left: 2px;
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 4px;
                margin-top: -10px;
                position: absolute;
            }
        </style>

        <?php if ($hesap->id == '') { ?>
            <div id="uyemsjgonder" class="modalDialog">
                <div>
                    <div style="padding:20px;">
                        <a href="#!" title="Close" class="close">X</a>
                        <h2><strong><?= htmlspecialchars($adsoyad, ENT_QUOTES, 'UTF-8'); ?></strong> / <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX413"), ENT_QUOTES, 'UTF-8'); ?></h2><br>
                        <center><strong><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX414"), ENT_QUOTES, 'UTF-8'); ?></strong>
                            <div class="clear"></div><br><br>
                            <a href="giris-yap" class="gonderbtn"><i class="fa fa-sign-in" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX356"), ENT_QUOTES, 'UTF-8'); ?></a>
                            <div class="clearmob"></div> <span><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX357"), ENT_QUOTES, 'UTF-8'); ?></span>
                            <br><br><br>
                        </center>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>

            <div id="HataliBildir" class="modalDialog">
                <div>
                    <div style="padding:20px;">
                        <a href="#!" title="Close" class="close">X</a>
                        <h2><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX428"), ENT_QUOTES, 'UTF-8'); ?></h2><br>
                        <center><strong><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX414"), ENT_QUOTES, 'UTF-8'); ?></strong>
                            <div class="clear"></div><br><br>
                            <a href="giris-yap" class="gonderbtn"><i class="fa fa-sign-in" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX356"), ENT_QUOTES, 'UTF-8'); ?></a>
                            <div class="clearmob"></div> <span><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX357"), ENT_QUOTES, 'UTF-8'); ?></span>
                            <br><br><br>
                        </center>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ($hesap->id != '') { ?>
            <div id="uyemsjgonder" class="modalDialog">
                <div>
                    <div style="padding:20px;">
                        <a href="#!" title="Close" class="close">X</a>
                        <h2><strong><?= htmlspecialchars($adsoyad, ENT_QUOTES, 'UTF-8'); ?></strong> / <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX413"), ENT_QUOTES, 'UTF-8'); ?></h2>
                        <form action="ajax.php?p=mesaj_gonder&uid=<?= $uyee->id; ?>&from=adv" method="POST" id="MesajGonderForm">
                        <textarea rows="3" name="mesaj" id="MesajYaz"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX412"), ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <input type="hidden" name="ilan_linki" value="<?= SITE_URL . $ilan_linki; ?>">
                            <a href="javascript:;" onclick="AjaxFormS('MesajGonderForm','MesajGonderSonuc');" style="float:right;" class="gonderbtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX420"), ENT_QUOTES, 'UTF-8'); ?></a>
                        </form>
                        <div id="TamamPnc" style="display:none"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX423"), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="clear"></div>
                        <div id="MesajGonderSonuc" style="display:none"></div>
                    </div>
                </div>
            </div>

            <div id="HataliBildir" class="modalDialog">
                <div>
                    <div style="padding:20px;">
                        <a href="#!" title="Close" class="close">X</a>
                        <h2><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX428"), ENT_QUOTES, 'UTF-8'); ?></h2>
                        <span style="font-size: 13px; margin-bottom: 15px; float: left;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX452"), ENT_QUOTES, 'UTF-8'); ?></span>
                        <form action="ajax.php?p=hatali_bildir&id=<?= $sayfay->id; ?>" method="POST" id="HataliBildirForm">
                            <textarea rows="3" name="mesaj" placeholder="<?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX450"), ENT_QUOTES, 'UTF-8'); ?>"></textarea>
                            <br />
                            <a href="javascript:;" onclick="AjaxFormS('HataliBildirForm','HataliBildirFormSonuc');" style="float:right;" class="gonderbtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX451"), ENT_QUOTES, 'UTF-8'); ?></a>
                        </form>
                        <div id="BiTamamPnc" style="display:none"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX449"), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="clear"></div>
                        <div id="HataliBildirFormSonuc" style="display:none"></div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <ul class="tab">
            <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'detaylar')" id="defaultOpen"><i class="fa fa-info" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, 'TX416'), ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'ilan_video')"><i class="fa fa-video-camera" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, 'TX417'), ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'ilan_harita')"><i class="fa fa-map-marker" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, 'TX418'), ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'sokak_gorunumu')"><i class="fa fa-street-view" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, 'TX419'), ENT_QUOTES, 'UTF-8'); ?></a></li>
        </ul>

        <div class="favyaz">
            <script type="text/javascript">
                function favEkle(id) {
                    <?php if ($hesap->id == '') { ?>
                    alert("<?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX434"), ENT_QUOTES, 'UTF-8'); ?>");
                    <?php } else { ?>
                    $("#favOn").slideUp(300, function () {
                        $("#favOff").slideDown(300);
                    });
                    $.get("ajax.php?p=favori", {'id': id}, function (data) {
                        if (data != undefined) {
                            if (data == 1) {
                                // ok
                            }
                        }
                    });
                    <?php } ?>
                }

                function favCikar(id) {
                    <?php if ($hesap->id == '') { ?>
                    alert("<?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX434"), ENT_QUOTES, 'UTF-8'); ?>");
                    <?php } else { ?>
                    $("#favOff").hide(1, function () {
                        $("#favOn").show(1);
                    });
                    $.get("ajax.php?p=favori", {'id': id}, function (data) {
                        if (data != undefined) {
                            if (data == 1) {
                                // ok
                            }
                        }
                    });
                    <?php } ?>
                }
				
				
</script>
<?php
if ($hesap->id != '') {
    $favKontrol = $db->query("SELECT id FROM favoriler_501 WHERE acid=" . (int)$hesap->id . " AND ilan_id=" . (int)$sayfay->id);
    $favKontrol = $favKontrol->rowCount();
    $favNe = ($favKontrol > 0) ? 1 : 0;
} else {
    $favNe = 0;
}
?>
<a href="javascript:void(0);" id="favOff" onclick="favCikar(<?= (int)$sayfay->id; ?>);" <?= ($favNe == false) ? 'style="display:none"' : ''; ?>><i id="favyazaktif" class="fa fa-heart" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX430"), ENT_QUOTES, 'UTF-8'); ?></a>
<a href="javascript:void(0);" id="favOn" onclick="favEkle(<?= (int)$sayfay->id; ?>);" <?= ($favNe == true) ? 'style="display:none"' : ''; ?>><i class="fa fa-heart-o" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX429"), ENT_QUOTES, 'UTF-8'); ?></a>
<div class="desktopclear"></div>
<a onclick="window.print();" href="#!"><i class="fa fa-print" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX433"), ENT_QUOTES, 'UTF-8'); ?></a>
</div>

<div id="detaylar" class="tabcontent"><!-- detaylar start div -->

    <div class="ilandetay">

        <div class="ilanfotolar">

            <div id="image-gallery" style="display:none">
                <?php
                $image_list = array();
                $sql = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . (int)$sayfay->id . " ORDER BY sira ASC");
                if ($sql->rowCount() > 0) {
                    $image_list = $sql->fetchAll();
                } else {
                    $qu = $db->prepare("SELECT id, ilan_no FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=? ORDER BY id ASC");
                    $qu->execute([$sayfay->ilan_no]);
                    if ($qu->rowCount() > 0) {
                        while ($qrow = $qu->fetch(PDO::FETCH_OBJ)) {
                            if (count($image_list) < 1) {
                                $varmi = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . (int)$qrow->id . " ORDER BY sira ASC");
                                if ($varmi->rowCount() > 0) {
                                    $image_list = $varmi->fetchAll();
                                }
                            }
                        }
                    }
                }

                if (!$image_list) {
                    $image_list[] = [
                        'id' => 0,
                        'resim' => $sayfay->resim,
                    ];
                }

                foreach ($image_list as $row) {
                    ?>
                    <a class="ilandetaybigfoto" data-exthumbimage="<?= $linkcek; ?>/uploads/<?= htmlspecialchars($row['resim'], ENT_QUOTES, 'UTF-8'); ?>" data-src="<?= $linkcek; ?>/uploads/<?= htmlspecialchars($row['resim'], ENT_QUOTES, 'UTF-8'); ?>" id="mega<?= (int)$row['id']; ?>">Mega Foto #<?= (int)$row['id']; ?></a>
                <?php } ?>
            </div>

            <div class="clearfix">
                <ul id="image-slider" class="gallery list-unstyled cS-hidden" style="width:100%;">
                    <?php
                    if ($image_list) {
                        foreach ($image_list as $row) {
                            ?>
                            <li data-thumb="<?= $linkcek; ?>/uploads/thumb/<?= htmlspecialchars($row['resim'], ENT_QUOTES, 'UTF-8'); ?>" onclick="$('#mega<?= (int)$row['id']; ?>').click();">
                                <img style="width:100%;cursor: crosshair;" src="<?= $linkcek; ?>/uploads/<?= htmlspecialchars($row['resim'], ENT_QUOTES, 'UTF-8'); ?>" />
                            </li>
                        <?php }
                    } ?>
                </ul>
            </div>

        </div>
      </div><!-- İlan detaylar div end -->

<style>
.ilanozellikler table tr td {padding:4px;}
</style>

<div class="ilanozellikler">
<table width="100%" border="0">
    <?php if ($sayfay->fiyat != '' && $sayfay->fiyat != 0) { ?>
        <tr>
            <td colspan="2"><h3><strong><?= htmlspecialchars($fiyat . ' ' . $sayfay->pbirim, ENT_QUOTES, 'UTF-8'); ?></strong></h3></td>
        </tr>
    <?php } ?>
    <tr>
        <td height="20" bgcolor="#eee" colspan="2"><h5>
            <?php if ($ulke != '' && $ulke != "Türkiye") { ?><a href="javascript:;"><?= htmlspecialchars($ulke, ENT_QUOTES, 'UTF-8'); ?></a> / <?php } ?>
            <?php if ($il != '') { ?><a href="<?= $search . htmlspecialchars($il_slug, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($il, ENT_QUOTES, 'UTF-8'); ?></a>
                <?php if ($ilce != '') { ?> / <a href="<?= $search . htmlspecialchars($il_slug, ENT_QUOTES, 'UTF-8') . "-" . htmlspecialchars($ilce_slug, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($ilce, ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php if ($mahalle != '') { ?> / <a href="<?= $search . htmlspecialchars($il_slug, ENT_QUOTES, 'UTF-8') . "-" . htmlspecialchars($ilce_slug, ENT_QUOTES, 'UTF-8') . "-" . htmlspecialchars($mahalle_slug, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($mahalle, ENT_QUOTES, 'UTF-8'); ?></a><?php } ?>
                    <?php if ($mahalle == '' && $sayfay->semt != '') { ?> / <a href="javascript:void(0);"><?= htmlspecialchars($sayfay->semt, ENT_QUOTES, 'UTF-8'); ?></a><?php } ?>
                <?php } ?>
            <?php } ?>
        </h5></td>
    </tr>
    <tr>
        <td width="52%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX140"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= htmlspecialchars($sayfay->ilan_no, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX141"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= htmlspecialchars($tarihi[0] . ' ' . $aylar[$tarihi[1]] . ' ' . $tarihi[2], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>

    <?php if ($sayfay->emlak_durum != '') { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX5311"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->emlak_durum, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->emlak_tipi != '') { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX5411"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->emlak_tipi, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->konut_sekli != '') { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX58"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->konut_sekli, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->konut_tipi != '') { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX142"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->konut_tipi, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->metrekare != '' || $sayfay->metrekare != 0) { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX143"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->metrekare, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->brut_metrekare != '' && $sayfay->brut_metrekare != 0) { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX1431"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->brut_metrekare, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->emlak_tipi == $arsa) { ?>
        <?php if ($sayfay->metrekare_fiyat != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX327"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->metrekare_fiyat, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->ada_no != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX328"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->ada_no, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->parsel_no != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX329"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->parsel_no, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->pafta_no != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX330"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->pafta_no, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->kaks_emsal != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX331"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->kaks_emsal, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->gabari != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX332"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->gabari, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->imar_durum != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX682"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->imar_durum, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->tapu_durumu != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX333"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->tapu_durumu, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->katk != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX334"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->katk, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>

        <?php if ($sayfay->takas != '') { ?>
            <tr>
                <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX336"), ENT_QUOTES, 'UTF-8'); ?></td>
                <td width="50%"><?= htmlspecialchars($sayfay->takas, ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php } ?>
    <?php } ?>

    <?php if ($sayfay->krediu != '') { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX335"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->krediu, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->oda_sayisi != '' || $sayfay->oda_sayisi != 0) { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX144"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->oda_sayisi, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->bina_yasi != '' || $sayfay->bina_yasi != 0) { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX145"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->bina_yasi, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->bulundugu_kat != '') { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX146"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->bulundugu_kat, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->bina_kat_sayisi != '' && $sayfay->bina_kat_sayisi != 0) { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX147"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->bina_kat_sayisi, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>

    <?php if ($sayfay->isitma != '' && $sayfay->emlak_tipi != $arsa) { ?>
        <tr>
            <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX148"), ENT_QUOTES, 'UTF-8'); ?></td>
            <td width="50%"><?= htmlspecialchars($sayfay->isitma, ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    <?php } ?>


<?php if ($sayfay->banyo_sayisi != '' && $sayfay->banyo_sayisi != 0) { ?>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX149"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= htmlspecialchars($sayfay->banyo_sayisi, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
<?php } ?>

<?php if ($sayfay->esyali != '' && $sayfay->emlak_tipi != $arsa) { ?>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX150"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= ($sayfay->esyali == 1) ? htmlspecialchars($fonk->get_lang($sayfay->dil, "TX167"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($fonk->get_lang($sayfay->dil, "TX168"), ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
<?php } ?>

<?php if ($sayfay->kullanim_durum != '') { ?>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX151"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= htmlspecialchars($sayfay->kullanim_durum, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
<?php } ?>

<?php if ($sayfay->site_ici != '') { ?>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX152"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= ($sayfay->site_ici == 1) ? htmlspecialchars($fonk->get_lang($sayfay->dil, "TX167"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($fonk->get_lang($sayfay->dil, "TX168"), ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
<?php } ?>

<?php if ($aidat_int != '' || $aidat_int != 0) { ?>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX153"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= htmlspecialchars($aidat . ' ' . $sayfay->pbirim, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
<?php } ?>

<?php if ($sayfay->kimden != '') { ?>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX460"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= htmlspecialchars($sayfay->kimden, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
<?php } ?>

<?php if ($sayfay->yetkis != '') { ?>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX624"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= htmlspecialchars($sayfay->yetkis, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
<?php } ?>

<?php if ($sayfay->yetki_bilgisi != '' || $sayfay->yetki_bilgisi != 0) { ?>
    <tr>
        <td width="50%" style="font-weight:bolder;"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX1451"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td width="50%"><?= htmlspecialchars($sayfay->yetki_bilgisi, ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
<?php } ?>
</table>
</div><!-- İlan Özellikler div end -->

<!-- danisman start -->

<div class="danisman">
    <h3 class="danismantitle"><?=($uyee->id == '' OR $uyee->turu==2 OR $uyee->turu==1) ? $fonk->get_lang($sayfay->dil,"TX155") : $fonk->get_lang($sayfay->dil,"TX154");?></h3>

    <a href="<?=$profil_link;?>"><img src="https://www.turkiyeemlaksitesi.com.tr/<?=$davatar;?>" width="200" height="150"></a>

    <h4><strong><a href="<?=$profil_link;?>"><?=$adsoyad;?></a></strong></h4>
    <?php
    $sql = $db->query("SELECT id, kid, adi, soyadi, avatar, avatard, nick_adi FROM hesaplar WHERE site_id_555=501 AND durum = 0 AND turu = 2 AND onecikar = 1 AND onecikar_btarih > NOW() ORDER BY RAND() LIMIT 0, 12");
    if($adsoyad != $uyee->unvan):
    ?>
        <span>
            <?=$uyee->dunvan;?>
        </span>
    <?php endif;?>            

    <div class="clear"></div>
    <div class="iletisim" style="display: flex; justify-content: space-evenly;">
        <a target="_blank" href="https://api.whatsapp.com/send?phone=<?= rawurlencode($uyee->telefon); ?>&text=<?= rawurlencode($link); ?>%20<?= rawurlencode('Bu ilan hakkında bilgi almak istiyorum');?>" class="whatsappbtn gonderbtn" style="width: 20px; height: 20px; margin-top: 15px; float: none; display: inline-block;">
            <img src="https://i.hizliresim.com/8wlrjtp.png" alt="WhatsApp" style="width: 100%; height: 100%;">
        </a>
        <a target="_blank" href="https://t.me/share/url?url=<?= rawurlencode($link); ?>&text=<?= rawurlencode($sayfay->baslik); ?>" class="telegrambtn gonderbtn" style="width: 20px; height: 20px; margin-top: 15px; float: none; display: inline-block;">
            <img src="https://i.hizliresim.com/cfgqfse.png" alt="Telegram" style="width: 100%; height: 100%;">
        </a>
    </div>

    <?php
    if($uyee->id != ''){
        $portfoyu = ($uyee->turu == 1 OR $uyee->turu == 2) ? '/portfoy' : '';
    ?>
        <a href="<?=$profil_link.$portfoyu;?>" class="gonderbtn" target="_blank" style="font-size:14px;padding: 7px 0px;width:140px;margin-top: 15px;float:none; display: inline-block;"><i class="fa fa-search" aria-hidden="true"></i> <?=$fonk->get_lang($sayfay->dil,"TX391");?></a>
        <div class="clear"></div>

        <? if($uyee->id != $hesap->id){?>
            <? if($gayarlar->anlik_sohbet==1){?>
                <a href="#uyemsjgonder" class="gonderbtn" style="font-size:14px;padding: 7px 0px;width:140px;margin-top: 5px;float:none; display: inline-block;"><i class="fa fa-envelope-o" aria-hidden="true"></i> <?=$fonk->get_lang($sayfay->dil,"TX392");?></a>
                <div class="clear"></div>
            <? } ?>
        <? } ?>

    <? } ?>

    <? if($gsm != ''){ ?>
        <h5 class="profilgsm"> <strong><a style="color:white;" href="tel:<?=$gsm;?>"><?=$gsm;?></a></strong> <span style="margin-left:5px;font-size:13px;"> <?=$fonk->get_lang($sayfay->dil,"TX156");?></span></h5>
    <? } ?>

    <? if($demail != ''){ ?>
        <h5><strong><?=$fonk->get_lang($sayfay->dil,"TX158");?></strong><br><a href="mailto:<?=$demail;?>" target="_blank" ><?=$demail;?></a></h5>
    <? } ?>

    <div class="clear" style="margin-top:15px;"></div>
    <a href="#HataliBildir" class="gonderbtn" style="font-size:13px;padding: 7px 0px;width:140px;margin-top: 5px;margin-bottom:10px;float:none; display: inline-block;"><i class="fa fa-bell-o" aria-hidden="true"></i> <?=$fonk->get_lang($sayfay->dil,"TX428");?></a>

    <div class="clear"></div>
</div>
<!-- danisman end -->

<div class="clear"></div>

<?php if ($fonk->bosluk_kontrol(strip_tags($sayfay->icerik)) == false): ?>
    <div class="ilanaciklamalar">
        <h3><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX161"), ENT_QUOTES, 'UTF-8'); ?></h3>
        <p><?= $sayfay->icerik; ?></p>
    </div>
<?php endif; ?>

<?= str_replace("[hit]", $hit, $fonk->get_lang($sayfay->dil, "TX459")); ?>

<?php
$delm1 = explode("<+>", $fonk->get_lang($sayfay->dil, "CEPHE"));
$delm2 = explode("<+>", $fonk->get_lang($sayfay->dil, "IC_OZELLIKLER"));
$delm3 = explode("<+>", $fonk->get_lang($sayfay->dil, "DIS_OZELLIKLER"));
$delm4 = explode("<+>", $fonk->get_lang($sayfay->dil, "ALTYAPI_OZELLIKLER"));
$delm5 = explode("<+>", $fonk->get_lang($sayfay->dil, "KONUM_OZELLIKLER"));
$delm6 = explode("<+>", $fonk->get_lang($sayfay->dil, "GENEL_OZELLIKLER"));
$delm7 = explode("<+>", $fonk->get_lang($sayfay->dil, "MANZARA_OZELLIKLER"));
$cdelm1 = count($delm1);
$cdelm2 = count($delm2);
$cdelm3 = count($delm3);
$cdelm4 = count($delm4);
$cdelm5 = count($delm5);
$cdelm6 = count($delm6);
$cdelm7 = count($delm7);

if ($sayfay->cephe_ozellikler != '' || $sayfay->ic_ozellikler != '' || $sayfay->dis_ozellikler != '' || $sayfay->altyapi_ozellikler != '' || $sayfay->konum_ozellikler != '' || $sayfay->genel_ozellikler != '' || $sayfay->manzara_ozellikler != '') {
    if ($cdelm1 > 1 || $cdelm2 > 1 || $cdelm3 > 1 || $cdelm4 > 0 || $cdelm5 > 0 || $cdelm6 > 0 || $cdelm7 > 0) {
        ?>
        <div class="ilanaciklamalar">
            <h3><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX162"), ENT_QUOTES, 'UTF-8'); ?></h3>

            <?php if ($sayfay->emlak_tipi == $arsa): ?>

                <?php if ($cdelm4 > 1): ?>
                    <div class="ilanozellik">
                        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX323"), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php
                        $ielm = explode("<+>", $sayfay->altyapi_ozellikler);
                        foreach ($delm4 as $val) {
                            if (in_array($val, $ielm)) {
                                ?>
                                <span id="ozellikaktif"><i class="fa fa-check" aria-hidden="true"></i><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?> </span>
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($cdelm5 > 1): ?>
                    <div class="ilanozellik">
                        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX324"), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php
                        $ielm = explode("<+>", $sayfay->konum_ozellikler);
                        foreach ($delm5 as $val) {
                            if (in_array($val, $ielm)) {
                                ?>
                                <span id="ozellikaktif"><i class="fa fa-check" aria-hidden="true"></i><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?> </span>
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($cdelm6 > 1): ?>
                    <div class="ilanozellik">
                        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX325"), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php
                        $ielm = explode("<+>", $sayfay->genel_ozellikler);
                        foreach ($delm6 as $val) {
                            if (in_array($val, $ielm)) {
                                ?>
                                <span id="ozellikaktif"><i class="fa fa-check" aria-hidden="true"></i><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?> </span>
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($cdelm7 > 1): ?>
                    <div class="ilanozellik">
                        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX326"), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php
                        $ielm = explode("<+>", $sayfay->manzara_ozellikler);
                        foreach ($delm7 as $val) {
                            if (in_array($val, $ielm)) {
                                ?>
                                <span id="ozellikaktif"><i class="fa fa-check" aria-hidden="true"></i><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?> </span>
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>

                <?php if ($cdelm1 > 0): ?>
                    <div class="ilanozellik">
                        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX163"), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php
                        $ielm = explode("<+>", $sayfay->cephe_ozellikler);
                        foreach ($delm1 as $val) {
                            if (in_array($val, $ielm)) {
                                ?>
                                <span id="ozellikaktif"><i class="fa fa-check" aria-hidden="true"></i><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?> </span>
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($cdelm2 > 1): ?>
                    <div class="ilanozellik">
                        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX164"), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php
                        $ielm = explode("<+>", $sayfay->ic_ozellikler);
                        foreach ($delm2 as $val) {
                            if (in_array($val, $ielm)) {
                                ?>
                                <span id="ozellikaktif"><i class="fa fa-check" aria-hidden="true"></i><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?> </span>
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($cdelm3 > 1): ?>
                    <div class="ilanozellik">
                        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX165"), ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php
                        $ielm = explode("<+>", $sayfay->dis_ozellikler);
                        foreach ($delm3 as $val) {
                            if (in_array($val, $ielm)) {
                                ?>
                                <span id="ozellikaktif"><i class="fa fa-check" aria-hidden="true"></i><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?> </span>
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    <?php } ?>
<?php } ?>


<div class="clear"></div>

<!-- Video ve Harita -->
<div id="ilan_video" class="tabcontent" style="text-align:center;">
    <?php if ($sayfay->video == ''): ?>
        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX420"), ENT_QUOTES, 'UTF-8'); ?></h4>
    <?php else: ?>
        <video width="70%" height="500" controls>
            <source src="https://www.turkiyeemlaksitesi.com.tr/uploads/videos/<?= htmlspecialchars($sayfay->video, ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "VIDEO_SUPPORT"), ENT_QUOTES, 'UTF-8'); ?></video>
    <?php endif; ?>
    <div class="clear"></div>
</div>

<div id="ilan_harita" class="tabcontent"><!-- maps harita start -->
    <?php if ($sayfay->maps != '' && strlen($sayfay->maps) < 50): ?>
        <?php
        $coords = htmlspecialchars($sayfay->maps, ENT_QUOTES, 'UTF-8');
        list($lat, $lng) = explode(",", $coords);
        ?>
        <div id="map" style="width: 100%; height: 500px"></div>
        <input type="hidden" value="<?= $lat; ?>" id="g_lat">
        <input type="hidden" value="<?= $lng; ?>" id="g_lng">

        <script type="text/javascript">
            function initMap() {
                var g_lat = parseFloat(document.getElementById("g_lat").value);
                var g_lng = parseFloat(document.getElementById("g_lng").value);
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 12,
                    center: {lat: g_lat, lng: g_lng}
                });
                var geocoder = new google.maps.Geocoder();

                var marker = new google.maps.Marker({
                    position: {
                        lat: g_lat,
                        lng: g_lng
                    },
                    map: map
                });
            }
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($gayarlar->google_api_key, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap"></script>

    <?php else: ?>
        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX421"), ENT_QUOTES, 'UTF-8'); ?></h4>
    <?php endif; ?>
    <div class="clear"></div>
</div><!-- maps harita end -->

<div id="sokak_gorunumu" class="tabcontent">
    <?php if ($sayfay->maps != '' && strlen($sayfay->maps) < 50): ?>
        <iframe width="100%" height="500" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/streetview?key=<?= htmlspecialchars($gayarlar->google_api_key, ENT_QUOTES, 'UTF-8'); ?>&location=<?= htmlspecialchars($sayfay->maps, ENT_QUOTES, 'UTF-8'); ?>"></iframe>
    <?php else: ?>
        <h4><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX421"), ENT_QUOTES, 'UTF-8'); ?></h4>
    <?php endif; ?>
    <div class="clear"></div>
</div>
<!-- Video ve Harita end -->

</div><!-- Clear div end -->

<?php
if ($gayarlar->reklamlar == 1) { // Eğer reklamlar aktif ise...
    $detect = (!isset($detect)) ? new Mobile_Detect : $detect;
    $rtipi = 5;
    $reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1)");
    $rcount = $reklamlar->rowCount();
    $order = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
    $reklam = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1) " . $order . " LIMIT 0,1")->fetch(PDO::FETCH_OBJ);
    if ($rcount > 0) {
?>
        <!-- 728 x 90 Reklam Alanı -->
        <div class="ad728home">
            <?= ($detect->isMobile() || $detect->isTablet()) ? htmlspecialchars($reklam->mobil_kodu, ENT_QUOTES, 'UTF-8') : htmlspecialchars($reklam->kodu, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <!-- 728 x 90 Reklam Alanı END-->
<?php
    }
} // Eğer reklamlar aktif ise...
?>

<?php if ($gayarlar->doviz == 1 || $gayarlar->kredih == 1) { ?>
    <div id="wrapper">
        <div class="dovizkredi">
            <?php if ($gayarlar->doviz == 1) { ?>
                <div class="dovizkurlari">
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td><h4><strong><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX461"), ENT_QUOTES, 'UTF-8'); ?></strong></h4></td>
                            <td align="center"><strong><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX462"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td align="center"><strong><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX463"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
                        </tr>
                        <tr>
                            <td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX464"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td align="center"><?= htmlspecialchars($usd_DA, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td align="center"><?= htmlspecialchars($usd_DS, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <tr>
                            <td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX465"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td align="center"><?= htmlspecialchars($eur_DA, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td align="center"><?= htmlspecialchars($eur_DS, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <tr>
                            <td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX466"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td align="center"><?= htmlspecialchars($gbp_DA, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td align="center"><?= htmlspecialchars($gbp_DS, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <tr>
                            <td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX467"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td align="center"><?= htmlspecialchars($chf_DA, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td align="center"><?= htmlspecialchars($chf_DS, ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    </table>
                </div>
            <?php } ?>

            <?php if ($gayarlar->kredih == 1) { ?>
                <div class="kredihesaplama">
                    <script type="text/javascript">
                        Number.prototype.formatMoney = function (c, d, t) {
                            var n = this,
                                c = isNaN(c = Math.abs(c)) ? 2 : c,
                                d = d == undefined ? "." : d,
                                t = t == undefined ? "," : t,
                                s = n < 0 ? "-" : "",
                                i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                                j = (j = i.length) > 3 ? j % 3 : 0;
                            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
                        };

                        function DovizKontrol(x, y) {
                            sonuc = true;
                            if (x == "vade") {
                                vade = y;
                                if (isNaN(vade)) {
                                    sonuc = false;
                                }
                            } else if (x == "tutar") {
                                if (isNaN(tutar)) {
                                    sonuc = false;
                                }
                            } else if (x == "faiz") {
                                fayiz = y;
                                if (isNaN(fayiz)) {
                                    sonuc = false;
                                }
                            } else {
                                if (isNaN(faiz)) {
                                    sonuc = false;
                                }
                            }
                            return sonuc;
                        }

                        function KrediHesapla() {
                            $("#error_sonuc,#sonuc").fadeOut(300);
                            var sonuchtml, odeme_plani, hata, banka;
                            vade = $("#vade").val();
                            tutar = $("#tutar").val();
                            faiz = $("#faiz").val();

                            if (faiz == 0) {
                                faiz = $("#faiz_diger").val();
                            }

                            faiz = faiz.replace(",", ".");

                            var cevir;
                            if (tutar.length == 9 || tutar.length == 10) {
                                cevir = true;
                                tutar = tutar.replace(".", "");
                                tutar = tutar.replace(".", "");
                                tutar = parseFloat(tutar);
                            } else {
                                cevir = false;
                            }

                            tur = $("input[name=turu]:checked").val();
                            vadeKont = DovizKontrol('vade', vade);
                            tutarKont = DovizKontrol('tutar', tutar);
                            faizKont = DovizKontrol('faiz', faiz);

                            if ((vade == '' || vade == undefined) || (tutar == '' || tutar == undefined) || (faiz == '' || faiz == undefined || faiz == 0)) {
                                hata = '<span class="error"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX468"), ENT_QUOTES, 'UTF-8'); ?></span>';
                            } else if (vadeKont == false || tutarKont == false || faizKont == false) {
                                hata = '<span class="error"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX469"), ENT_QUOTES, 'UTF-8'); ?></span>';
                            } else {
                                hata = '';
                                banka = $("#faiz option:selected").text();

                                z = (parseFloat(faiz) / 100);
                                tip = "Konut Kredisi";

                                taksit = (parseFloat(tutar) * z) / (1 - 1 / (Math.pow(1 + z, parseFloat(vade))));
                                toplam = taksit * parseFloat(vade);

                                if (cevir) {
                                    taksit2 = taksit.formatMoney(2, '.', ',');
                                    toplam2 = toplam.formatMoney(2, '.', ',');
                                } else {
                                    var taksit_str = taksit.toString();
                                    yakala = taksit_str.substr(0, 2);
                                    if (yakala == '0.') {
                                        taksit_str = taksit_str.substr(2, 5);
                                        taksit2 = taksit_str.substr(0, 3) + "." + taksit_str.substr(3, 2);
                                    } else {
                                        taksit2 = taksit.toFixed(3);
                                    }
                                    toplam2 = toplam.toFixed(3);
                                }

                                var banka_str = banka.replace(".", ",");
                                var faiz_str = faiz.replace(".", ",");
                                sonuchtml =
                                    '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">' +
                                    '<tr>' +
                                    '<td width="50%"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX508"), ENT_QUOTES, 'UTF-8'); ?></td>' +
                                    '<td><strong>' + tutar + ' TL</strong></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                    '<td width="50%"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX509"), ENT_QUOTES, 'UTF-8'); ?></td>' +
                                    '<td><strong>' + vade + ' Ay</strong></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                    '<td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX510"), ENT_QUOTES, 'UTF-8'); ?></td>' +
                                    '<td><strong>' + banka_str + '</strong></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                    '<td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX510"), ENT_QUOTES, 'UTF-8'); ?></td>' +
                                    '<td><strong>' + faiz_str + '</strong></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                    '<td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX511"), ENT_QUOTES, 'UTF-8'); ?></td>' +
                                    '<td><strong>' + taksit2 + ' TL</strong></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                    '<td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX512"), ENT_QUOTES, 'UTF-8'); ?></td>' +
                                    '<td><strong>' + toplam2 + ' TL</strong></td>' +
                                    '</tr>' +
                                    '<tr>' +
                                    '<td><a href="javascript:GeriDon();" class="gonderbtn"><i class="fa fa-chevron-left" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX515"), ENT_QUOTES, 'UTF-8'); ?></a></td>' +
                                    '<td></td>' +
                                    '</tr>' +
                                    '</table>';
                            }

                            if (hata == '') {
                                $("#odeme_plani").html(odeme_plani);
                                $("#sonuc").html(sonuchtml);
                                $("#HesaplamaForm,#error_sonuc").slideUp(300, function () {
                                    $("#sonuc").slideDown(300);
                                });
                            } else {
                                $("#error_sonuc").html(hata);
                                $("#error_sonuc").fadeIn(300);
                            }
                        }

                        function GeriDon() {
                            $("#error_sonuc,#sonuc").slideUp(300, function () {
                                $("#HesaplamaForm").slideDown(300);
                            });
                        }

                        function FaizSelected(durum) {
                            if (durum == 0) {
                                $("#faiz_diger").slideDown(500, function () {
                                    $("#faiz_diger").focus();
                                });
                            } else {
                                $("#faiz_diger").slideUp(500);
                            }
                        }
                    </script>
					

<!--div id="odeme_plan" class="modalDialog">
<div>
<div style="padding:20px;">
<a href="#!" title="Close" class="close">X</a>
<h2>Ödeme Planı</h2><br>

<div id="odeme_plani"></div>

<div class="clear"></div>
</div>
</div>
</div-->


<div id="HesaplamaForm">
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="2"><h4><strong><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX470"), ENT_QUOTES, 'UTF-8'); ?></strong></h4></td>
  </tr>
  <tr>
    <td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX471"), ENT_QUOTES, 'UTF-8'); ?></td>
    <td><input name="tutar" id="tutar" type="text" data-mask="#.##0" data-mask-reverse="true" data-mask-maxlength="false"></td>
  </tr>
  <tr>
    <td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX472"), ENT_QUOTES, 'UTF-8'); ?></td>
    <td>
    <input name="vade" id="vade" type="text">
    </td>
  </tr>
  <tr>
    <td><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX510"), ENT_QUOTES, 'UTF-8'); ?></td>
    <td>
  <select name="faiz" id="faiz" onchange="FaizSelected(this.options[this.selectedIndex].value);">
  <?php
  $bankalar = explode(",", $fonk->get_lang($sayfay->dil, "BANKA_FAIZLER"));
  foreach ($bankalar as $row) {
    $parc = explode("=", $row);
  ?><option value="<?= htmlspecialchars($parc[1], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($parc[0], ENT_QUOTES, 'UTF-8'); ?> (%<?= htmlspecialchars($parc[1], ENT_QUOTES, 'UTF-8'); ?>)</option>
    <?php
  }
  ?>
    <option value="0"><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX516"), ENT_QUOTES, 'UTF-8'); ?></option>
  </select>
  <div class="clear"></div>
  <input type="text" style="margin-top:8px; display:none" name="faiz_diger" id="faiz_diger">
  </td>
  </tr>
  
  <tr>
    <td colspan="2" align="right" style="border:none"><a style="float:right;margin-left:10px;" class="gonderbtn" href="javascript:KrediHesapla();"><i class="fa fa-calculator" aria-hidden="true"></i> <?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX474"), ENT_QUOTES, 'UTF-8'); ?></a>
    <div id="error_sonuc" style="display:none"></div>
    </td>
  </tr>
</table>

</div>
<div id="sonuc" style="display:none"></div>
</div>
<?php } ?>
</div>
<?php } ?>

</div>

</div>
<!-- content end -->

<?php
$benzeri = ($sayfay->il_id != 0) ? "t1.il_id=" . (int)$sayfay->il_id . " " : '';
$benzeri .= ($sayfay->ilce_id != 0) ? "OR t1.ilce_id=" . (int)$sayfay->ilce_id . " " : '';
$benzeri .= ($sayfay->mahalle_id != 0) ? "OR t1.mahalle_id=" . (int)$sayfay->mahalle_id . " " : '';

$sql = $db->query("SELECT DISTINCT t1.id, t1.url, t1.fiyat, t1.baslik, t1.resim, t1.il_id, t1.ilce_id, t1.emlak_durum, t1.pbirim, t1.emlak_tipi, t1.ilan_no FROM sayfalar AS t1 LEFT JOIN dopingler_501 AS t2 ON t1.id=t2.ilan_id AND t2.durum=1 WHERE (t1.btarih>NOW() OR t2.btarih>NOW()) AND (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.tipi=4 AND t1.durum=1 AND t1.id!=" . (int)$sayfay->id . " AND (" . $benzeri . ") GROUP BY t1.ilan_no ORDER BY RAND() LIMIT 0,10");
if ($sql->rowCount() > 0) {
?>
<!-- Benzer İlanlar -->
<div id="wrapper"> 
<div class="content" id="bigcontent">
<div class="altbaslik">
<div id="pager4" class="pager"></div>
<h4 id="sicakfirsatlar"><i class="fa fa-clock-o" aria-hidden="true"></i> <strong><a><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX475"), ENT_QUOTES, 'UTF-8'); ?></a></strong></h4>
</div>
<div class="list_carousel">
        <ul id="foo4">
        
    <?php
    while ($row = $sql->fetch(PDO::FETCH_OBJ)) {

      $id = $row->id;
      $row_lang = $db->query("SELECT t1.id, t1.url, t1.fiyat, t1.baslik, t1.resim, t1.il_id, t1.ilce_id, t1.emlak_durum, t1.pbirim, t1.emlak_tipi, t1.ilan_no FROM sayfalar AS t1 WHERE (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.ilan_no=" . (int)$row->ilan_no . " AND t1.dil='" . htmlspecialchars($dil, ENT_QUOTES, 'UTF-8') . "' ");
      if ($row_lang->rowCount() > 0) {
        $row = $row_lang->fetch(PDO::FETCH_OBJ);
        $row->id = $id;
      }


    $link = ($dayarlar->permalink == 'Evet') ? htmlspecialchars($row->url, ENT_QUOTES, 'UTF-8') . '.html' : 'index.php?p=sayfa&id=' . (int)$row->id;
    if ($row->fiyat != 0) {
    $fiyat_int = $gvn->para_int($row->fiyat);
    $fiyat = $gvn->para_str($fiyat_int);
    }
    $sc_il = $db->query("SELECT il_adi FROM il WHERE id=" . (int)$row->il_id)->fetch(PDO::FETCH_OBJ);
    $sc_ilce = $db->query("SELECT ilce_adi FROM ilce WHERE id=" . (int)$row->ilce_id)->fetch(PDO::FETCH_OBJ);
    ?>
    <li>
    <a href="<?= $link; ?>">
    <div class="kareilan">
   <span class="ilandurum" <?php echo ($row->emlak_durum == $emstlk) ? 'id="satilik"' : ''; echo ($row->emlak_durum == $emkrlk) ? 'id="kiralik"' : ''; ?>><?= htmlspecialchars($row->emlak_durum, ENT_QUOTES, 'UTF-8'); ?>
			/  <?= htmlspecialchars($row->emlak_tipi, ENT_QUOTES, 'UTF-8'); ?>
			</span>
    <img title="Sıcak Fırsat" alt="Sıcak Fırsat" src="<?= htmlspecialchars($linkcek, ENT_QUOTES, 'UTF-8'); ?>/uploads/thumb/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" width="234" height="201">
    <div class="fiyatlokasyon" <?php echo ($row->emlak_durum == $emkrlk) ? 'id="lokkiralik"' : ''; ?>>
    <?php if ($row->fiyat != '' OR $row->fiyat != 0) { ?><h3><?= htmlspecialchars($fiyat, ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($row->pbirim, ENT_QUOTES, 'UTF-8'); ?></h3><?php } ?> 
    <h4><?= htmlspecialchars($sc_il->il_adi, ENT_QUOTES, 'UTF-8'); ?> / <?= htmlspecialchars($sc_ilce->ilce_adi, ENT_QUOTES, 'UTF-8'); ?></h4>
    </div>
    <div class="kareilanbaslik">
    <h3><?= htmlspecialchars($fonk->kisalt($row->baslik, 0, 45), ENT_QUOTES, 'UTF-8'); ?><?=(strlen($row->baslik) > 45) ? '...' : '';?></h3>
    </div> 
    </div>
    </a>
    </li>
    <?php } ?>
</ul>
</div>
</div>
</div>
</div>
<div class="clear"></div>
<!-- Benzer İlanlar END-->
<?php } ?>
<?php include THEME_DIR . "inc/footer.php"; ?>