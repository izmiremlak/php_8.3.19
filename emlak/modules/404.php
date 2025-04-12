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
<title><?= htmlspecialchars(dil('TX1'), ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<meta name="keywords" content="<?= htmlspecialchars($sayfay->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($sayfay->description, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="robots" content="All" />  
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<base href="<?= SITE_URL; ?>" />

<?php include THEME_DIR . "inc/head.php"; ?>

</head>
<body>

<?php include THEME_DIR . "inc/header.php"; ?>

<div class="headerbg" style="background-image: url(uploads/404.jpg);">
    <div id="wrapper">
    </div>
</div>

<div id="wrapper">

    <div class="content" id="bigcontent">

        <div class="altbaslik">
            <h4><strong><?= htmlspecialchars(dil('TX1'), ENT_QUOTES, 'UTF-8'); ?></strong></h4>
        </div>

        <div class="clear"></div>

        <div class="sayfadetay" style="text-align:center;">

            <h2><strong><?= htmlspecialchars(dil('TX1'), ENT_QUOTES, 'UTF-8'); ?></strong></h2>
            <br><br>
            <h4><?= htmlspecialchars(dil('TX2'), ENT_QUOTES, 'UTF-8'); ?></h4>
            <br><br>
            <h4><a href="index.html"><strong><?= htmlspecialchars(dil('TX3'), ENT_QUOTES, 'UTF-8'); ?></strong></a></h4>

        </div>

    </div>

    <div class="clear"></div>
</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>