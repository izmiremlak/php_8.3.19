<?php
// $dayarlar tanımlı değilse boş bir nesne oluştur
$dayarlar = $dayarlar ?? new stdClass();
// verification özelliği yoksa boş string varsay
$dayarlar->verification = $dayarlar->verification ?? '';

// Site doğrulama kodu var mı kontrol ediyoruz, varsa ekliyoruz
if ($dayarlar->verification !== '') {
    echo htmlspecialchars($dayarlar->verification, ENT_QUOTES, 'UTF-8');
}
?>

<!-- Canonical link ekliyoruz -->
<link rel="canonical" href="<?= htmlspecialchars(REQUEST_URL ?? '', ENT_QUOTES, 'UTF-8'); ?>" />

<!-- Css dosyalarını yüklüyoruz -->
<link rel="stylesheet" type='text/css' href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>css/stylex.css" />
<link rel="stylesheet" type="text/css" href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>css/extralayers.css" media="none" onload="if(media!='all')media='all'" />
<link rel="stylesheet" type="text/css" href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>rs-plugin/css/settings.css" media="none" onload="if(media!='all')media='all'" />
<link rel="stylesheet" type='text/css' href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>css/font-awesome.min.css" media="none" onload="if(media!='all')media='all'" />
<link rel="stylesheet" type='text/css' href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>css/ionicons.min.css" media="none" onload="if(media!='all')media='all'" />
<link rel="stylesheet" type='text/css' href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>css/animate.css" media="none" onload="if(media!='all')media='all'" />
<link rel='stylesheet' type='text/css' href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>css/prettyPhoto.css" media="none" onload="if(media!='all')media='all'" />

<link rel='stylesheet' type='text/css' href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800&subset=latin,latin-ext' media="none" onload="if(media!='all')media='all'" />
<link href="https://fonts.googleapis.com/css?family=Titillium+Web:200,400,700&subset=latin-ext" rel="stylesheet" media="none" onload="if(media!='all')media='all'" />

<!-- Sayfa tipi 4 veya 5 ise galeri css dosyalarını ekliyoruz -->
<?php if (($p === 'sayfa' && ($sayfay->tipi ?? 0) === 4) || ($p === 'sayfa' && ($sayfay->tipi ?? 0) === 5)) { ?>
    <link href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>lightgallery/css/lightgallery.css" rel="stylesheet">
    <link href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>lightslider/css/lightslider.css" rel="stylesheet" />
<?php } elseif ($p === 'uye_paneli' && (($_GET["rd"] ?? '') === 'ilan_duzenle' || ($_GET["rd"] ?? '') === 'ilan_olustur')) { ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>nestable/css/components/nestable.almost-flat.min.css" />
    <link rel="stylesheet" href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>nestable/css/components/nestable.min.css" />
    <link rel="stylesheet" href="<?= htmlspecialchars(THEME_DIR ?? ''); ?>nestable/css/components/nestable.gradient.min.css" />
<?php } ?>

<!-- Yükleniyor animasyonu -->
<style type="text/css">
    div.hbcne { position: fixed; z-index: 4000; }
    div.hgchd { top: 0px; left: 0px; }
    div.hbcne { _position: absolute; }
    div.hgchd { _bottom: auto; _top: expression(ie6 = (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + 'px'); }
</style>
<center>
    <a style="font-family:Arial;font-size:19px;color:#ff0000;"></a><br /><br />
    <img style="padding:0px;margin:0px;background-color:transparent;border:none;" src="<?= htmlspecialchars(THEME_DIR ?? ''); ?>images/loading.gif" alt="Loading..." title="yükleniyor" width="auto" height="auto" />
</center>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(() => document.getElementById("izmirtr")?.style.display = "none", 100);
    });
</script>

<!-- jQuery kütüphanesini yüklüyoruz -->
<script src="<?= htmlspecialchars(THEME_DIR ?? ''); ?>js/jquery-2.2.4.min.js"></script>