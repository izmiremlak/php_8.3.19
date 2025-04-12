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

// Başlık belirleme
$bslk = ($sayfay->turu == 0) ? "Devam Eden Projeler" : "Projelerimiz";
$bslk = ($sayfay->turu == 1) ? "Tamamlanan Projeler" : $bslk;
$bslk_link = ($sayfay->turu == 0) ? 'projeler?turu=0' : 'projeler';
$bslk_link = ($sayfay->turu == 1) ? 'projeler?turu=1' : $bslk_link;

// Sayfaya göre arka plan resmi ayarlama
$sayfaya_gore_headbg = ($sayfay->tipi == 5) ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->projeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : $sayfaya_gore_headbg;

// Danışman için kontroller
if ($sayfay->danisman_id != 0 && $sayfay->danisman_id != 1) {
    $danisman = $db->prepare("SELECT * FROM danismanlar_501 WHERE id=?");
    $danisman->execute([$sayfay->danisman_id]);
    if ($danisman->rowCount() > 0) {
        $danisman = $danisman->fetch(PDO::FETCH_OBJ);

        $adsoyad = htmlspecialchars($danisman->adsoyad, ENT_QUOTES, 'UTF-8');
        $gsm = ($danisman->gsm != '') ? htmlspecialchars($danisman->gsm, ENT_QUOTES, 'UTF-8') : '';
        $telefon = ($danisman->telefon != '') ? htmlspecialchars($danisman->telefon, ENT_QUOTES, 'UTF-8') : '';
        $demail = ($danisman->email != '') ? htmlspecialchars($danisman->email, ENT_QUOTES, 'UTF-8') : '';
        $davatar = ($danisman->resim != '') ? 'uploads/thumb/' . htmlspecialchars($danisman->resim, ENT_QUOTES, 'UTF-8') : 'uploads/default-avatar.png';
        $profil_link = "javascript:void(0);";
    } else {
        $uye_id = $sayfay->danisman_id;
    }
} else {
    $uye_id = $sayfay->acid;
}

// Üye bilgilerini kontrol etme
if ($uye_id != '') {
    $uyee = $db->prepare("SELECT *, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
    $uyee->execute([$uye_id]);
    if ($uyee->rowCount() > 0) {
        $uyee = $uyee->fetch(PDO::FETCH_OBJ);

        $adsoyad = ($uyee->unvan == '') ? htmlspecialchars($uyee->adsoyad, ENT_QUOTES, 'UTF-8') : htmlspecialchars($uyee->unvan, ENT_QUOTES, 'UTF-8');
        $gsm = ($uyee->telefon != '' && $uyee->telefond == 0) ? htmlspecialchars($uyee->telefon, ENT_QUOTES, 'UTF-8') : '';
        $telefon = ($uyee->sabit_telefon != '' && $uyee->sabittelefond == 0) ? htmlspecialchars($uyee->sabit_telefon, ENT_QUOTES, 'UTF-8') : '';
        $demail = ($uyee->email != '' && $uyee->epostad == 0) ? htmlspecialchars($uyee->email, ENT_QUOTES, 'UTF-8') : '';
        $davatar = ($uyee->avatar != '' && $uyee->avatard == 0) ? 'uploads/thumb/' . htmlspecialchars($uyee->avatar, ENT_QUOTES, 'UTF-8') : 'uploads/default-avatar.png';

        $profil_link = "profil/";
        $profil_link .= ($uyee->nick_adi == '') ? $uyee->id : htmlspecialchars($uyee->nick_adi, ENT_QUOTES, 'UTF-8');
    }
}
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

<?php $category = true; include THEME_DIR . "inc/head.php"; ?>

</head>
<body>

<?php include THEME_DIR . "inc/header.php"; ?>

<div class="headerbg" <?= ($sayfay->resim2 != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($sayfay->resim2, ENT_QUOTES, 'UTF-8') . ');"' : $sayfaya_gore_headbg; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars($sayfay->baslik, ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <a href="index.html"><strong><?= htmlspecialchars(dil("TX136"), ENT_QUOTES, 'UTF-8'); ?></strong></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <a href="projeler"><strong><?= htmlspecialchars(dil("TX210"), ENT_QUOTES, 'UTF-8'); ?></strong></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php /* <a href="<?= $bslk_link; ?>"><strong><?= $bslk; ?></strong></a> <i class="fa fa-caret-right" aria-hidden="true"></i> */ ?>
                <span><strong><?= htmlspecialchars($sayfay->baslik, ENT_QUOTES, 'UTF-8'); ?></strong></span>
            </div>
        </div>
    </div>
</div>

<div id="wrapper">

<div class="content" id="bigcontent">
<?php include THEME_DIR . "inc/sosyal_butonlar.php"; ?>

<div class="ilandetay">

<div class="altbaslik">
<h4><strong><?= htmlspecialchars($sayfay->baslik, ENT_QUOTES, 'UTF-8'); ?></strong></h4>
</div>

<div class="ilanfotolar" id="projefotolar">

<div id="image-gallery" style="display:none">
<?php
$query = "SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . (int)$sayfay->id . " ORDER BY id DESC";
$sql = $db->query($query);
while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
?>
<a class="ilandetaybigfoto" data-exthumbimage="uploads/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" data-src="uploads/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" id="mega<?= (int)$row->id; ?>"><img src="uploads/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" width="100%" height="auto" /></a>
<?php } ?>
</div>

<div class="clearfix">
    <ul id="image-slider" class="gallery list-unstyled cS-hidden" style="width:100%;">
        <?php
        $sql = $db->query($query);
        while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
        ?>
        <li data-thumb="uploads/thumb/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" onclick="$('#mega<?= (int)$row->id; ?>').click();">
            <img style="width:100%;cursor: crosshair;" src="uploads/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" />
        </li>
        <?php } ?>
    </ul>
</div>

</div>

<?php if ($adsoyad != '') { ?>
<!-- Danışman Bilgileri -->
<div class="danisman">
<h3 class="danismantitle"><?= ($uyee->id == '' || $uyee->turu == 2 || $uyee->turu == 1) ? htmlspecialchars(dil("TX155"), ENT_QUOTES, 'UTF-8') : htmlspecialchars(dil("TX154"), ENT_QUOTES, 'UTF-8'); ?></h3>

<a href="<?= htmlspecialchars($profil_link, ENT_QUOTES, 'UTF-8'); ?>"><img src="<?= htmlspecialchars($davatar, ENT_QUOTES, 'UTF-8'); ?>" width="200" height="150"></a>

<h4><strong><a href="<?= htmlspecialchars($profil_link, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($adsoyad, ENT_QUOTES, 'UTF-8'); ?></a></strong></h4>
<div class="clear"></div>

<?php if ($gsm != '') { ?><h5 class="profilgsm"><strong><a style="color:white;" href="tel:<?= htmlspecialchars($gsm, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($gsm, ENT_QUOTES, 'UTF-8'); ?></a></strong><span style="margin-left:5px;font-size:13px;"><?= htmlspecialchars(dil("TX159"), ENT_QUOTES, 'UTF-8'); ?></span></h5><?php } ?>

<?php if ($telefon != '') { ?><h5><strong><?= htmlspecialchars(dil("TX157"), ENT_QUOTES, 'UTF-8'); ?></strong><br><a href="tel:<?= htmlspecialchars($telefon, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($telefon, ENT_QUOTES, 'UTF-8'); ?></a></h5><?php } ?>

<?php if ($demail != '') { ?><h5><strong><?= htmlspecialchars(dil("TX158"), ENT_QUOTES, 'UTF-8'); ?></strong><br><?= htmlspecialchars($demail, ENT_QUOTES, 'UTF-8'); ?></h5><?php } ?>

<div class="clear"></div>
</div>
<!-- Danışman Bilgileri End -->
<?php } ?>

<div class="clear"></div>

<div class="ilanaciklamalar">
<h3><?= htmlspecialchars(dil("TX217"), ENT_QUOTES, 'UTF-8'); ?></h3>

<p><?= htmlspecialchars($sayfay->icerik, ENT_QUOTES, 'UTF-8'); ?></p>

</div>

<div class="clear"></div>
<?php if ($sayfay->maps != '') { ?>
<div class="ilanaciklamalar">
<h3><?= htmlspecialchars(dil("TX218"), ENT_QUOTES, 'UTF-8'); ?></h3>

<iframe src="<?= htmlspecialchars($sayfay->maps, ENT_QUOTES, 'UTF-8'); ?>" width="100%" height="350" frameborder="0" style="border:0" allowfullscreen></iframe>

</div>
<?php } ?>

</div>

</div>

<div class="clear"></div>

</div>

<?php include THEME_DIR . "inc/footer.php"; ?>
</body>
</html>