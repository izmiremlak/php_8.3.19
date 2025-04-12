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
<title><?= htmlspecialchars(dil("TX649"), ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

<div class="headerbg" style="background-image: url(uploads/911da78222.jpg);">
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX649"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <span><?= htmlspecialchars(dil("TX650"), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div class="clear"></div>

<div id="wrapper">
    <div class="content" id="bigcontent">
        <div class="clearmob" style="margin-top:20px;"></div>

        <?php
        $qry = $pagent->sql_query("SELECT id,kid,adi,soyadi,avatar,avatard,nick_adi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND durum=0 AND turu=2 ORDER BY CASE WHEN onecikar_btarih>NOW() THEN 1 ELSE 0 END, adi ASC");
        $query = $db->query($qry['sql']);

        if ($query->rowCount() > 0) {
        ?>
        <div class="list_carousel" id="anadanismanlar">
            <ul id="foo55">
            <?php
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $plink = "profil/" . (($row->nick_adi == '') ? $row->id : $row->nick_adi);
                $kid = $row->kid;
                $kurumsal = $db->prepare("SELECT adi,soyadi,unvan FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                $kurumsal->execute([$kid]);
                if ($kurumsal->rowCount() > 0) {
                    $kurumsal = $kurumsal->fetch(PDO::FETCH_OBJ);
                    $kurumsal = ($kurumsal->unvan != '') ? $kurumsal->unvan : $kurumsal->adi . " " . $kurumsal->soyadi;
                }
                $avatar = ($row->avatar == '' || $row->avatard == 1) ? '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : htmlspecialchars($row->avatar, ENT_QUOTES, 'UTF-8');
            ?>
            <li><a href="<?= htmlspecialchars($plink, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="anadanisman">
                    <div class="danismanfotoana" style="background-image: url(<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>);"></div>
                    <div class="danismanbilgisi">
                        <h4><?= htmlspecialchars($row->adi . " " . $row->soyadi, ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php if ($fonk->bosluk_kontrol($kurumsal) == false) { ?><h5><?= htmlspecialchars($kurumsal, ENT_QUOTES, 'UTF-8'); ?></h5><?php } ?>
                    </div>
                </div></a></li>
            <?php } ?>
            </ul>
        </div>

        <div class="clear"></div>
        <div class="sayfalama">
            <?php echo $pagent->listele('danismanlar?git=', $gvn->zrakam($_GET["git"]), $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
        </div>

        <?php } else { ?>
        <h4 style="color:red"><?= htmlspecialchars(dil("TX623"), ENT_QUOTES, 'UTF-8'); ?></h4>
        <?php } ?>
    </div>

    <div class="clear"></div>
</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>