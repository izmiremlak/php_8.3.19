<?php
declare(strict_types=1);

if (!defined("THEME_DIR")) {
    die();
}

error_reporting(E_ALL); // Tüm hata raporlamalarını aç
ini_set('display_errors', '1'); // Hataları ekranda göster
ini_set('log_errors', '1'); // Hataları log dosyasına yaz
ini_set('error_log', __DIR__ . '/error_log.log'); // Log dosyasının yolu
?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= htmlspecialchars(dil("TX135"), ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($dayarlar->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($dayarlar->description, ENT_QUOTES, 'UTF-8'); ?>" /> 
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<?php include THEME_DIR . "inc/head.php"; ?>
</head>
<body>

<?php include THEME_DIR . "inc/header.php"; ?>

<div class="headerbg" <?= ($gayarlar->haber_ve_duyurular_resim  != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->haber_ve_duyurular_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX135"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <a href="index.html"><?= htmlspecialchars(dil("TX136"), ENT_QUOTES, 'UTF-8'); ?></a> / <span><strong><?= htmlspecialchars(dil("TX135"), ENT_QUOTES, 'UTF-8'); ?></strong></span>
            </div>
        </div>
    </div>
</div>

<div id="wrapper">
    <div class="content" <?= ($gayarlar->haberler_sidebar == 0) ? 'id="bigcontent"' : '';?>>
        <?php include THEME_DIR . "inc/sosyal_butonlar.php"; ?>

        <div class="altbaslik">
            <h4><strong><?= htmlspecialchars(dil("TX135"), ENT_QUOTES, 'UTF-8'); ?></strong></h4>
        </div>

        <div class="clear"></div>

        <?php
        $qry = $pagent->sql_query("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=2 AND dil='" . htmlspecialchars($dil, ENT_QUOTES, 'UTF-8') . "' ORDER BY id DESC", $gvn->rakam($_GET["git"]));
        $query = $db->query($qry['sql']);

        if ($query->rowCount() > 0) {
            while ($ft = $query->fetch(PDO::FETCH_OBJ)) {
                $linkx = ($dayarlar->permalink == 'Evet') ? htmlspecialchars($ft->url, ENT_QUOTES, 'UTF-8') . '.html' : 'index.php?p=sayfa&id=' . htmlspecialchars($ft->id, ENT_QUOTES, 'UTF-8');
                $icerik = strip_tags($ft->icerik);
        ?>
        <div class="listeleme"> 
            <div class="listefoto">
                <img src="uploads/thumb/<?= htmlspecialchars($ft->resim, ENT_QUOTES, 'UTF-8'); ?>" width="210" height="190">
            </div>
            <div class="listeicerik">
                <h3><a href="<?= htmlspecialchars($linkx, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($ft->baslik, ENT_QUOTES, 'UTF-8'); ?></a></h3>
                <p><?= htmlspecialchars($fonk->kisalt($icerik, 0, 335), ENT_QUOTES, 'UTF-8'); ?><?php if (strlen($icerik) > 335) { echo '...'; } ?> <strong><a class="detaylink" href="<?= htmlspecialchars($linkx, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars(dil("TX137"), ENT_QUOTES, 'UTF-8'); ?></a></strong></p>
            </div>
        </div>
        <?php 
            }
        ?>

        <div class="clear"></div>
        <div class="sayfalama">
            <?php echo $pagent->listele('haber-ve-duyurular?git=', $gvn->rakam($_GET["git"]), $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
        </div>

        <?php 
        } else { 
        ?>
        <b style="color:#aa1818;"><?= htmlspecialchars(dil("TX138"), ENT_QUOTES, 'UTF-8'); ?></b>
        <?php 
        } 
        ?>
    </div>

    <?php
    if ($gayarlar->haberler_sidebar != 0) {
    ?>
    <div class="sidebar">
        <?php include THEME_DIR . "inc/sayfa_sidebar.php"; ?>
    </div>
    <?php 
    }
    ?>

    <div class="clear"></div>

    <?php include THEME_DIR . "inc/ilanvertanitim.php"; ?>
</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>