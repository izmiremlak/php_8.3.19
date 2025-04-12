<?php
if (!defined("THEME_DIR")) {
    die();
}

// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); echo ($on == 'hakkinda') ? ' ' . htmlspecialchars(dil("TX425"), ENT_QUOTES, 'UTF-8') : ''; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<meta name="keywords" content="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<base href="<?= htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8'); ?>" />

<?php include THEME_DIR . "inc/head.php"; ?>

</head>
<body>

<?php if ($hesap->id == '') { ?>
<div id="uyemsjgonder" class="modalDialog">
    <div>
        <div style="padding:20px;">
            <a href="<?= htmlspecialchars(REQUEST_URL, ENT_QUOTES, 'UTF-8'); ?>#!" title="Close" class="close">X</a>
            <h2><strong><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></strong> / <?= htmlspecialchars(dil("TX413"), ENT_QUOTES, 'UTF-8'); ?></h2><br>
            <center><strong><?= htmlspecialchars(dil("TX414"), ENT_QUOTES, 'UTF-8'); ?></strong>
            <div class="clear"></div><br><br>
            <a href="giris-yap" class="gonderbtn"><i class="fa fa-sign-in" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX356"), ENT_QUOTES, 'UTF-8'); ?></a><div class="clearmob"></div> <span><?= htmlspecialchars(dil("TX357"), ENT_QUOTES, 'UTF-8'); ?></span>
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
            <a href="<?= htmlspecialchars(REQUEST_URL, ENT_QUOTES, 'UTF-8'); ?>#!" title="Close" class="close">X</a>
            <h2><strong><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></strong> / <?= htmlspecialchars(dil("TX413"), ENT_QUOTES, 'UTF-8'); ?></h2>
            <form action="ajax.php?p=mesaj_gonder&uid=<?= htmlspecialchars($profil->id, ENT_QUOTES, 'UTF-8'); ?>&from=adv" method="POST" id="MesajGonderForm">
                <textarea rows="3" name="mesaj" id="MesajYaz"></textarea>
                <a href="javascript:;" onclick="AjaxFormS('MesajGonderForm','MesajGonderSonuc');" style="float:right;" class="gonderbtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX415"), ENT_QUOTES, 'UTF-8'); ?></a>
            </form>
            <div id="TamamPnc" style="display:none"><?= htmlspecialchars(dil("TX423"), ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="clear"></div>
            <div id="MesajGonderSonuc" style="display:none"></div>
        </div>
    </div>
</div>
<?php } ?>

<?php include THEME_DIR . "inc/header.php"; ?>

<div id="kfirmaprofili" class="headerbg" <?= ($gayarlar->foto_galeri_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->foto_galeri_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>
    </div>
</div>

<div class="clear"></div>

<div class="kurumsalbtns">
    <div id="wrapper">
        <a href="<?= htmlspecialchars($uyelink, ENT_QUOTES, 'UTF-8'); ?>" id="kurumsalbtnaktif"><?= htmlspecialchars(dil("TX626"), ENT_QUOTES, 'UTF-8'); ?></a>
        <?php if ($gayarlar->anlik_sohbet == 1) { ?><a href="<?= htmlspecialchars(REQUEST_URL, ENT_QUOTES, 'UTF-8'); ?>#uyemsjgonder" class="gonderbtn"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX627"), ENT_QUOTES, 'UTF-8'); ?></a><?php } ?>
    </div>
</div>

<div id="wrapper">

<?php
if ($gayarlar->reklamlar == 1) { // Eğer reklamlar aktif ise...
    $detect = (!isset($detect)) ? new Mobile_Detect : $detect;

    $rtipi = 10;
    $reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1)");
    $rcount = $reklamlar->rowCount();
    $order = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
    $reklam = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1) " . $order . " LIMIT 0,1")->fetch(PDO::FETCH_OBJ);
    if ($rcount > 0) {
?>
<!-- 728 x 90 Reklam Alanı -->
<div class="clear"></div>
<div class="ad728home">
    <?= ($detect->isMobile() || $detect->isTablet()) ? $reklam->mobil_kodu : $reklam->kodu; ?>
</div>
<!-- 728 x 90 Reklam Alanı END-->
<?php
    }
} // Eğer reklamlar aktif ise...
?>

<div class="content" style="float:left;">

<?php
$search_link = $uyelink . "?on=profile";
$search_linkx = $search_link;
$execute = array();

// Emlak Durumu için filtre...
if ($emlak_durum != '') {
    $dahili_query .= "AND emlak_durum=? ";
    $execute[] = $emlak_durum;

    $search_link .= "&emlak_durum=" . htmlspecialchars($emlak_durum, ENT_QUOTES, 'UTF-8');
}

// Order by için işlemler...
$orderi = $gvn->html_temizle($_REQUEST["order"]);
$search_linkx = $search_link;
if ($fonk->bosluk_kontrol($orderi) == true) {
    $dahili_order = "id DESC";
} else {
    $filtre_count += 1;
    $bgrs = '&';
    $search_link .= $bgrs . "order=" . htmlspecialchars($orderi, ENT_QUOTES, 'UTF-8');
    if ($orderi == 'fiyat_asc') {
        $dahili_order = "CAST(fiyat AS DECIMAL(10,2)) ASC";
    } elseif ($orderi == 'fiyat_desc') {
        $dahili_order = "CAST(fiyat AS DECIMAL(10,2)) DESC";
    } else {
        $dahili_order = "id DESC";
    }
}
$orbgrs = '&';

$git = $gvn->zrakam($_GET["git"]);
$qry = $pagent->sql_query("SELECT t1.ilan_no,t1.id,t1.url,t1.fiyat,t1.tarih,t1.il_id,t1.ilce_id,t1.emlak_durum,t1.emlak_tipi,t1.resim,t1.baslik,t1.pbirim,t1.metrekare FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND durum=0 {$dahili_query} ORDER BY {$dahili_order}", $execute);
$query = $db->prepare($qry['sql']);
$query->execute($execute);
$adet = $qry['toplam'];
?>

<?php if ($adet > 0) { ?>
<span style="float:left;margin-bottom:15px;"><strong><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></strong> <?= htmlspecialchars(dil("TX627"), ENT_QUOTES, 'UTF-8'); ?> <b><?= $adet; ?></b> <?= htmlspecialchars(dil("TX628"), ENT_QUOTES, 'UTF-8'); ?></span>
<div class="clear"></div>

<div class="list_carousel">
    <ul id="foo44">
        <?php
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
            $id = $row->id;
            $row_lang = $db->query("SELECT t1.ilan_no,t1.id,t1.url,t1.fiyat,t1.tarih,t1.il_id,t1.ilce_id,t1.emlak_durum,t1.emlak_tipi,t1.resim,t1.baslik,t1.pbirim,t1.metrekare FROM sayfalar AS t1 WHERE (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.durum=0 AND t1.id={$id}");
            if ($row_lang->rowCount() > 0) {
                $row = $row_lang->fetch(PDO::FETCH_OBJ);
                $row->id = $id;
            }
        ?>
        <li>
        <?php
        $link = ($dayarlar->permalink == 'Evet') ? htmlspecialchars($row->url, ENT_QUOTES, 'UTF-8') . '.html' : 'index.php?p=sayfa&id=' . (int)$row->id;
        if ($row->fiyat != 0) {
            $fiyat_int = $gvn->para_int($row->fiyat);
            $fiyat = $gvn->para_str($fiyat_int);
        }
        $sc_il = $db->query("SELECT il_adi FROM il WHERE id=" . (int)$row->il_id)->fetch(PDO::FETCH_OBJ);
        $sc_ilce = $db->query("SELECT ilce_adi FROM ilce WHERE id=" . (int)$row->ilce_id)->fetch(PDO::FETCH_OBJ);
        ?>
        <a href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="kareilan">
        <span class="ilandurum" <?= ($row->emlak_durum == $emstlk) ? 'id="satilik"' : ''; echo ($row->emlak_durum == $emkrlk) ? 'id="kiralik"' : ''; ?>><?= htmlspecialchars($row->emlak_durum, ENT_QUOTES, 'UTF-8'); ?></span>
        <img title="Sıcak Fırsat" alt="Sıcak Fırsat" src="https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" width="234" height="160">
        <div class="fiyatlokasyon" <?= ($row->emlak_durum == $emkrlk) ? 'id="lokkiralik"' : ''; ?>>
        <?php if ($row->fiyat != '' || $row->fiyat != 0) { ?><h3><?= htmlspecialchars($fiyat, ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($row->pbirim, ENT_QUOTES, 'UTF-8'); ?></h3><?php } ?> 
        <h4><?= htmlspecialchars($sc_il->il_adi, ENT_QUOTES, 'UTF-8'); ?> / <?= htmlspecialchars($sc_ilce->ilce_adi, ENT_QUOTES, 'UTF-8'); ?></h4>
        </div>
        <div class="kareilanbaslik">
        <h3><?= htmlspecialchars($fonk->kisalt($row->baslik, 0, 45), ENT_QUOTES, 'UTF-8'); ?><?= (strlen($row->baslik) > 45) ? '...' : ''; ?></h3>
        </div> 
        </div>
        </a>
        </li>
        <?php } ?>
    </ul>
</div>

<?php } else { ?>
<h4 style="text-align:center;margin-top:60px;"><?= htmlspecialchars(dil("TX385"), ENT_QUOTES, 'UTF-8'); ?></h4>
<?php } ?>

<?php if ($adet > 0) { ?>
<div class="clear"></div>
<div class="sayfalama">
<?php echo $pagent->listele(htmlspecialchars($uyelink, ENT_QUOTES, 'UTF-8') . '?git=', $git, $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
</div>
<?php } ?>

</div>
<!-- FİRMA DETAYI END-->

<div class="sidebar" style="float:right;">

<!-- profil start -->
<div class="danisman">
<h3 class="danismantitle"><?= htmlspecialchars($uturu[$profil->turu], ENT_QUOTES, 'UTF-8'); ?> <?= ($profil->turu == 3) ? '' : htmlspecialchars(dil("TX384"), ENT_QUOTES, 'UTF-8'); ?></h3>

<img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" width="200" height="150">

<h4><strong><a><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></a></strong></h4>
<div class="clear"></div>

<?php
$gsm = ($profil->telefon != '' && $profil->telefond == 0) ? htmlspecialchars($profil->telefon, ENT_QUOTES, 'UTF-8') : '';
$tel = ($profil->sabit_telefon != '' && $profil->sabittelefond == 0) ? htmlspecialchars($profil->sabit_telefon, ENT_QUOTES, 'UTF-8') : '';
?>

<?php if ($gsm != '') { ?><h5 class="profilgsm"><strong><a style="color:white;" href="tel:<?= $gsm; ?>"><?= $gsm; ?></a></strong><span style="margin-left:5px;font-size:13px;"><?= htmlspecialchars(dil("TX159"), ENT_QUOTES, 'UTF-8'); ?></span></h5><?php } ?>

<?php if ($tel != '') { ?><h5><strong><?= htmlspecialchars(dil("TX157"), ENT_QUOTES, 'UTF-8'); ?></strong><br><a href="tel:<?= $tel; ?>"><?= $tel; ?></a></h5><?php } ?>

<?php if ($profil->email != '' && $profil->epostad == 0) { ?><h5><strong><?= htmlspecialchars(dil("TX158"), ENT_QUOTES, 'UTF-8'); ?></strong><br><?= htmlspecialchars($profil->email, ENT_QUOTES, 'UTF-8'); ?></h5><?php } ?>

<div class="clear"></div>
<br>
</div>
<!-- profil end -->

</div>

</div>

<div class="clear"></div>
</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>