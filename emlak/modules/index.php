<?php

declare(strict_types=1); // PHP 8.3 için sıkı tip kontrolü

// Sunucu ana bilgisayarı tanımlı değilse çıkış yap
if (!defined('SERVER_HOST')) {
    exit;
}

// Hata raporlama ayarları
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error_log.txt'); // Log dosyasını izin verilen dizine taşıdık

// Özel hata işleyici (tekrar tanımlamayı önlemek için)
if (!function_exists('customErrorHandler')) {
    /**
     * Özel hata işleyici fonksiyonu
     *
     * @param int $errno Hata numarası
     * @param string $errstr Hata mesajı
     * @param string|null $errfile Hata dosya adı
     * @param int|null $errline Hata satır numarası
     * @return bool Hata işlendi mi
     */
    function customErrorHandler($errno, $errstr, $errfile = null, $errline = null): bool
    {
        $errorMessage = "[$errno] $errstr - Dosya: " . ($errfile ?? 'bilinmiyor') . " - Satır: " . ($errline ?? 'bilinmiyor');
        error_log($errorMessage);
        echo "<div style='color: red;'><b>Hata:</b> $errorMessage</div>";
        return true;
    }
}
set_error_handler('customErrorHandler');

// Güvenli bir rastgele dizi oluşturma
$a0fdgkfdw9siu4zj = bin2hex(random_bytes(16));

// HTML başlangıcı ve meta dosyaları dahil
echo "<!DOCTYPE html>\n<html>\n<head>\n\n";
include THEME_DIR . 'inc/anasayfa-meta.php';
echo "\n";

// Başlığın ve meta etiketlerinin dahil edilmesi
$index = true;
include THEME_DIR . 'inc/head.php';
echo "\n</head>\n<body>\n\n";

// Başlık ve slider dosyalarının dahil edilmesi
include THEME_DIR . 'inc/header.php';
echo "\n\n";
include THEME_DIR . 'inc/slider.php';
echo "\n\n";
include THEME_DIR . 'inc/anasayfa-slider-alti.php';

// Blokların dizilimi ve sıralanması
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

// Blokların sıralanması ve dahil edilmesi
ksort($bloklar);
foreach ($bloklar as $k => $v) {
    if ($v === 'blok1') {
        include THEME_DIR . 'inc/anasayfa-sicak-ilanlar-ve-arama.php';
    } elseif ($v === 'blok2') {
        include THEME_DIR . 'inc/anasayfa-haber-ve-blog.php';
    } elseif ($v === 'blok3') {
        echo "<!-- blok3 start -->\n<div id=\"wrapper\">\n";
        include THEME_DIR . 'inc/ilanvertanitim.php';
        echo "</div><!-- wrapper end -->\n<!-- blok3 end -->\n";
    } elseif ($v === 'blok4') {
        include THEME_DIR . 'inc/anasayfa-sehirler.php';
    } elseif ($v === 'blok5') {
        include THEME_DIR . 'inc/anasayfa-vitrin-ilanlari.php';
    } elseif ($v === 'blok6') {
        include THEME_DIR . 'inc/anasayfa-onecikan-ilanlar.php';
    } elseif ($v === 'blok7') {
        include THEME_DIR . 'inc/anasayfa-danismanlar.php';
    } elseif ($v === 'blok8') {
        include THEME_DIR . 'inc/anasayfa-reklam1.php';
    } elseif ($v === 'blok9') {
        include THEME_DIR . 'inc/anasayfa-reklam2.php';
    }
}

// Footer dosyasının dahil edilmesi
include THEME_DIR . 'inc/footer.php';

/**
 * İki değeri birleştiren yardımcı fonksiyon
 *
 * @param string $a Birinci değer
 * @param string $b İkinci değer
 * @return string Birleştirilmiş sonuç
 */
function aa5wGBHZyWcA(string $a, string $b): string
{
    return $a . '-' . $b . 'KxYxyvcT8Ha8e';
}