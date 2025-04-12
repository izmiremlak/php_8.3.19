<?php

declare(strict_types=1); // PHP 8.3 için sıkı tip kontrolü

// Hata raporlamasını etkinleştir ve hem log dosyasına hem ekrana yaz
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');
error_reporting(E_ALL);

// Özel hata işleyici: Hataları hem log’a hem ekrana yaz
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    $errorMessage = "[Hata: $errno] $errstr - Dosya: $errfile - Satır: $errline";
    error_log($errorMessage);
    echo "<div style='color: red;'><b>Hata:</b> $errorMessage</div>";
    return true;
});

// Server host tanımlı değilse çıkış yap
if (!defined('SERVER_HOST')) {
    exit;
}

// Rastgele bir anahtar tanımla (mevcut mantık korundu)
$a0fdgkfdw9siu4zj = 'Tn@c+@Pp54&67pm(W<LSAd)+~x=w@nsga_<M1)3`c`(';

// HTML başlangıcını yazdır
echo "<!DOCTYPE html>\n<html>\n<head>\n\n";

// Meta bilgilerini dahil et (SEO için başlık ve meta denetimi)
include THEME_DIR . 'inc/anasayfa-meta.php';
echo "\n";

// Anasayfa başlık bilgisini dahil et
$index = true;
include THEME_DIR . 'inc/head.php';
echo "\n</head>\n<body>\n\n";

// Header dosyasını dahil et
include THEME_DIR . 'inc/header.php';
echo "\n\n";

// Slider dosyasını dahil et
include THEME_DIR . 'inc/slider.php';
echo "\n\n";

// Slider altı içeriğini kontrol ederek dahil et
$sliderAltiDosya = THEME_DIR . 'inc/anasayfa-slider-alti.php';
if (file_exists($sliderAltiDosya)) {
    include $sliderAltiDosya;
} else {
    $errorMessage = "Dosya bulunamadı: $sliderAltiDosya";
    error_log($errorMessage);
    echo "<div style='color: red;'><b>Hata:</b> $errorMessage</div>";
}

// Blokları bir diziye ekle (mevcut mantık korundu)
$bloklar = [];
if (($dayarlar->blok1 ?? 0) == 1) {
    $bloklar[$dayarlar->blok1_sira ?? 0] = 'blok1';
}
if (($dayarlar->blok2 ?? 0) == 1) {
    $bloklar[$dayarlar->blok2_sira ?? 0] = 'blok2';
}
if (($dayarlar->blok3 ?? 0) == 1) {
    $bloklar[$dayarlar->blok3_sira ?? 0] = 'blok3';
}
if (($dayarlar->blok4 ?? 0) == 1) {
    $bloklar[$dayarlar->blok4_sira ?? 0] = 'blok4';
}
if (($dayarlar->blok5 ?? 0) == 1) {
    $bloklar[$dayarlar->blok5_sira ?? 0] = 'blok5';
}
if (($dayarlar->blok6 ?? 0) == 1) {
    $bloklar[$dayarlar->blok6_sira ?? 0] = 'blok6';
}
if (($dayarlar->blok7 ?? 0) == 1) {
    $bloklar[$dayarlar->blok7_sira ?? 0] = 'blok7';
}
if (($dayarlar->blok8 ?? 0) == 1) {
    $bloklar[$dayarlar->blok8_sira ?? 0] = 'blok8';
}
if (($dayarlar->blok9 ?? 0) == 1) {
    $bloklar[$dayarlar->blok9_sira ?? 0] = 'blok9';
}

// Blokları sıraya göre düzenle
ksort($bloklar);

// Blokları sırayla dahil et
foreach ($bloklar as $k => $v) {
    if ($v == 'blok1') {
        include THEME_DIR . 'inc/anasayfa-sicak-ilanlar-ve-arama.php';
    }
    if ($v == 'blok2') {
        include THEME_DIR . 'inc/anasayfa-haber-ve-blog.php';
    }
    if ($v == 'blok3') {
        echo "<!-- blok3 start -->\n<div id=\"wrapper\">\n";
        include THEME_DIR . 'inc/ilanvertanitim.php';
        echo "</div><!-- wrapper end -->\n<!-- blok3 end -->\n";
    }
    if ($v == 'blok4') {
        include THEME_DIR . 'inc/anasayfa-sehirler.php';
    }
    if ($v == 'blok5') {
        include THEME_DIR . 'inc/anasayfa-vitrin-ilanlari.php';
    }
    if ($v == 'blok6') {
        include THEME_DIR . 'inc/anasayfa-onecikan-ilanlar.php';
    }
    if ($v == 'blok7') {
        include THEME_DIR . 'inc/anasayfa-danismanlar.php';
    }
    if ($v == 'blok8') {
        include THEME_DIR . 'inc/anasayfa-reklam1.php';
    }
    if ($v == 'blok9') {
        include THEME_DIR . 'inc/anasayfa-reklam2.php';
    }
}

// Footer dosyasını dahil et
include THEME_DIR . 'inc/footer.php';

// Rastgele bir fonksiyon (mevcut mantık korundu, PSR-12 için tip eklendi)
/**
 * İki string parametreyi birleştirip sabit bir ekleme yapar
 *
 * @param mixed $a İlk parametre
 * @param mixed $b İkinci parametre
 * @return string Birleştirilmiş sonuç
 */
function aa5wGBHZyWcA($a, $b): string
{
    return htmlspecialchars((string)$a . '-' . (string)$b . 'KxYxyvcT8Ha8e', ENT_QUOTES, 'UTF-8');
}

?>