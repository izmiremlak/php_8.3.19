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
<title><?= htmlspecialchars(dil("TX219"), ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($dayarlar->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($dayarlar->description, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<?php $category = true; include THEME_DIR . "inc/head.php"; ?>

</head>
<body>

<?php include THEME_DIR . "inc/header.php"; ?>

<div class="headerbg" <?= ($gayarlar->projeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->projeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX219"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <a href="index.html"><?= htmlspecialchars(dil("TX136"), ENT_QUOTES, 'UTF-8'); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i> <span><strong><?= htmlspecialchars(dil("TX219"), ENT_QUOTES, 'UTF-8'); ?></strong></span>
            </div>
        </div>
    </div>
</div>

<div id="wrapper">

<div class="content" id="bigcontent">

<?php include THEME_DIR . "inc/sosyal_butonlar.php"; ?>

<div class="altbaslik">

<h4><strong><?= htmlspecialchars(dil("TX219"), ENT_QUOTES, 'UTF-8'); ?></strong></h4>

</div>

<div class="clear"></div>

<div class="sehirbutonlar" id="projeler">
    <div id="sehirbutonlar-container">

    <?php
    $qry = $pagent->sql_query("SELECT id, url, baslik, resim FROM sayfalar WHERE site_id_555=501 AND tipi=5 AND dil='" . htmlspecialchars($dil, ENT_QUOTES, 'UTF-8') . "' ORDER BY id DESC", (int)$gvn->rakam($_GET["git"]));
    $query = $db->query($qry['sql']);

    if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
            $linkx = ($dayarlar->permalink == 'Evet') ? htmlspecialchars($row->url, ENT_QUOTES, 'UTF-8') . '.html' : 'index.php?p=sayfa&id=' . (int)$row->id;
    ?>
    <a href="<?= htmlspecialchars($linkx, ENT_QUOTES, 'UTF-8'); ?>"><div class="sehirbtn fadeup">
        <img src="uploads/thumb/<?= htmlspecialchars($row->resim, ENT_QUOTES, 'UTF-8'); ?>" width="320" height="290">
        <div class="sehiristatistk">
            <h2><strong><?= htmlspecialchars($fonk->kisalt($row->baslik, 0, 28), ENT_QUOTES, 'UTF-8'); ?><?= (strlen($row->baslik) > 28) ? '...' : ''; ?></strong></h2>
        </div>
    </div></a>
    <?php
        }
    } else {
    ?>
    <h4 style="color:red"><?= htmlspecialchars(dil("TX220"), ENT_QUOTES, 'UTF-8'); ?></h4>
    <?php
    }
    ?>

    <?php if ($query->rowCount() > 0) { ?>
    <div class="clear"></div>
    <div class="sayfalama">
        <?php echo $pagent->listele('projeler?git=', (int)$gvn->rakam($_GET["git"]), $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
    </div>
    <?php } ?>

    </div>
</div>

</div>

<div class="clear"></div>

<?php include THEME_DIR . "inc/ilanvertanitim.php"; ?>

</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>