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

// Kullanıcı giriş kontrolü
if ($hesap->id == "") {
    include THEME_DIR . "giris-yap.php";
    die();
}

$rd = $gvn->harf_rakam($_GET["rd"]);
?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= htmlspecialchars(dil("TX227"), ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($dayarlar->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($dayarlar->description, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<?php include THEME_DIR . "inc/head.php"; ?>

<?php if ($rd == "danismanlar") { ?>
<link rel="stylesheet" href="<?= THEME_DIR; ?>/remodal/dist/remodal.css">
<link rel="stylesheet" href="<?= THEME_DIR; ?>/remodal/dist/remodal-default-theme.css">
<?php } ?>

</head>
<body>
<?php include THEME_DIR . "inc/header.php"; ?>

<?php
if ($rd != "") {
    if (file_exists(THEME_DIR . 'uye-paneli/' . htmlspecialchars($rd, ENT_QUOTES, 'UTF-8') . ".php")) {
        include THEME_DIR . "uye-paneli/" . htmlspecialchars($rd, ENT_QUOTES, 'UTF-8') . ".php";
    } else {
        include THEME_DIR . "uye-paneli/hesabim.php";
    }
} else {
    include THEME_DIR . "uye-paneli/hesabim.php";
}
?>

<?php include THEME_DIR . "inc/footer.php"; ?>
</body>
</html>