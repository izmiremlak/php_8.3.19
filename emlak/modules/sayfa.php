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

$url = $gvn->html_temizle($_GET["url"]);
$id = $gvn->rakam($_GET["id"]);

if ($dayarlar->permalink == 'Evet') {
    $sayfay = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND url=:urls ");
    $sayfay->execute(['urls' => $url]);
    $sayfay = $sayfay->fetch(PDO::FETCH_OBJ);
} else {
    $sayfay = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:ids ");
    $sayfay->execute(['ids' => $id]);
    $sayfay = $sayfay->fetch(PDO::FETCH_OBJ);
}

$url = str_replace(["../", "./", "&", "?", "%"], "", $url);
if (file_exists($url . ".html")) {
    include $url . ".html";
    die();
} elseif ($sayfay->id == "") {
    include "404.php";
    die();
}

if ($sayfay->kategori_id != 0) {
    $kat1 = $db->query("SELECT * FROM kategoriler_501 WHERE id=" . $sayfay->kategori_id)->fetch(PDO::FETCH_OBJ);

    if ($kat1->ustu != 0) {
        $kat2 = $db->query("SELECT * FROM kategoriler_501 WHERE id=" . $kat1->ustu)->fetch(PDO::FETCH_OBJ);
        $kategori = clone $kat2;
        $alt_kategori = clone $kat1;
    } else {
        $kategori = $kat1;
    }

    if ($sayfay->tipi == 3) {
        $klink = ($dayarlar->permalink == 'Evet') ? 'hizmetler/' . $kategori->url : 'index.php?p=hizmetler&id=' . $kategori->id;
    } elseif ($sayfay->tipi == 4) {
        $klink = ($dayarlar->permalink == 'Evet') ? 'kategori/' . $kategori->url : 'index.php?p=kategori&id=' . $kategori->id;
        $aklink = ($dayarlar->permalink == 'Evet') ? 'kategori/' . $alt_kategori->url : 'index.php?p=kategori&id=' . $alt_kategori->id;
    }
}

if ($sayfay->tipi == 4) {
    include THEME_DIR . "ilan_detay.php";
    die();
} elseif ($sayfay->tipi == 5) {
    include THEME_DIR . "proje_detay.php";
    die();
}

if ($sayfay->tipi == 3) {
    $category = true;
} else {
    $sayfa = true;
}

$sayfaya_gore_headbg = ($sayfay->tipi == 0) ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->ekatalog_resim, ENT_QUOTES, 'UTF-8') . ');"' : '';
$sayfaya_gore_headbg = ($sayfay->tipi == 1) ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->yazilar_resim, ENT_QUOTES, 'UTF-8') . ');"' : $sayfaya_gore_headbg;
$sayfaya_gore_headbg = ($sayfay->tipi == 2) ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->haber_ve_duyurular_resim, ENT_QUOTES, 'UTF-8') . ');"' : $sayfaya_gore_headbg;
$sayfaya_gore_headbg = ($sayfay->tipi == 3) ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->hizmetler_resim, ENT_QUOTES, 'UTF-8') . ');"' : $sayfaya_gore_headbg;
?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= ($sayfay->title == '') ? htmlspecialchars($sayfay->baslik, ENT_QUOTES, 'UTF-8') : htmlspecialchars($sayfay->title, ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($sayfay->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($sayfay->description, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<?php include THEME_DIR . "inc/head.php"; ?>

</head>
<body>

<?php include THEME_DIR . "inc/header.php"; ?>

<div class="headerbg" <?= ($sayfay->resim2 != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($sayfay->resim2, ENT_QUOTES, 'UTF-8') . ');"' : $sayfaya_gore_headbg; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars($sayfay->baslik, ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <a href="index.html"><?= htmlspecialchars(dil("TX136"), ENT_QUOTES, 'UTF-8'); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php if ($sayfay->tipi == 1) { ?><a href="yazilar"><?= htmlspecialchars(dil("TX221"), ENT_QUOTES, 'UTF-8'); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i> <?php } ?>
                <?php if ($sayfay->tipi == 2) { ?><a href="haber-ve-duyurular"><?= htmlspecialchars(dil("TX222"), ENT_QUOTES, 'UTF-8'); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i> <?php } ?>
                <?php if ($sayfay->tipi == 3) { ?><a href="hizmetler"><?= htmlspecialchars(dil("TX223"), ENT_QUOTES, 'UTF-8'); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i> <?php } ?>
                <?php if ($kategori->id != '') { ?><a href="<?= htmlspecialchars($klink, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($kategori->baslik, ENT_QUOTES, 'UTF-8'); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i> <?php } ?>
                <?php if ($alt_kategori->id != '') { ?><a href="<?= htmlspecialchars($aklink, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($alt_kategori->baslik, ENT_QUOTES, 'UTF-8'); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i> <?php } ?>
                <span><?= htmlspecialchars($sayfay->baslik, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">

<div class="content" <?= (($gayarlar->blog_sidebar == 0 && $sayfay->tipi == 1) || ($gayarlar->haberler_sidebar == 0 && $sayfay->tipi == 2) || ($gayarlar->sayfa_sidebar == 0 && $sayfay->tipi == 0)) ? 'id="bigcontent"' : ''; ?>>

<?php include THEME_DIR . "inc/sosyal_butonlar.php"; ?>

<div class="altbaslik">

<h4><strong><?= htmlspecialchars($sayfay->baslik, ENT_QUOTES, 'UTF-8'); ?></strong></h4>

</div>

<div class="clear"></div>

<div class="sayfadetay">

<?php if ($gayarlar->reklamlar == 1) { // Eğer reklamlar aktif ise...
    $detect = (!isset($detect)) ? new Mobile_Detect : $detect;
    $rtipi = 4;
    $reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1)");
    $rcount = $reklamlar->rowCount();
    $order = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
    $reklam = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1) " . $order . " LIMIT 0,1")->fetch(PDO::FETCH_OBJ);
    if ($rcount > 0) {
?>
<!-- 336 x 280 Reklam Alanı -->
<div class="ad336x280">
    <?= ($detect->isMobile() || $detect->isTablet()) ? $reklam->mobil_kodu : $reklam->kodu; ?>
</div>
<!-- 336 x 280 Reklam Alanı END-->
<?php } } // Eğer reklamlar aktif ise... ?>

<p><?= htmlspecialchars($sayfay->icerik, ENT_QUOTES, 'UTF-8'); ?></p>

<div class="urungiderfotolar gallery">
    <?php
    $sql = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . (int)$sayfay->id . " AND dil='" . htmlspecialchars($dil, ENT_QUOTES, 'UTF-8') . "' ORDER BY id DESC");
    while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
    ?>
    <a href="uploads/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" rel="prettyPhoto[gallery1]"><img src="uploads/thumb/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" width="100" height="100"></a>
    <?php } ?>
</div>

</div>

</div>

<?php
if (
    ($gayarlar->blog_sidebar != 0 && $sayfay->tipi == 1) ||
    ($gayarlar->haberler_sidebar != 0 && $sayfay->tipi == 2) ||
    ($gayarlar->sayfa_sidebar != 0 && $sayfay->tipi == 0) ||
    ($gayarlar->hizmetler_sidebar != 0 && $sayfay->tipi == 3)
) {
?>
<div class="sidebar">
<?php
$sayfa = true;
include THEME_DIR . "inc/sayfa_sidebar.php";
?>
</div>
<?php } ?>

<div class="clear"></div>

<?php include THEME_DIR . "inc/ilanvertanitim.php"; ?>
</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>