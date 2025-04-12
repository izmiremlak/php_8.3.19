<?php

declare(strict_types=1); // PHP 8.3 için sıkı tip kontrolü

// Hata raporlama ayarları: Tüm hataları göster
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Hata loglama ayarları: Hataları dosyaya kaydet
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error_log.txt');

// Gerekli fonksiyon dosyasını yükle
$functionsPath = __DIR__ . '/functions.php';
if (file_exists($functionsPath)) {
    require_once $functionsPath;
} else {
    $errorMsg = "Hata: $functionsPath bulunamadı!";
    error_log($errorMsg);
    die("<div style='color: red;'><b>Hata:</b> $errorMsg</div>");
}

// Tema dizinini yalnızca bir kez tanımla
if (!defined('THEME_DIR')) {
    $possibleThemePaths = [
        __DIR__ . '/themes/default/', // Varsayılan tema yolu
        __DIR__ . '/templates/',      // Alternatif tema klasörü
        __DIR__ . '/',                // Kök dizin (son çare)
    ];

    $themePath = '';
    foreach ($possibleThemePaths as $path) {
        if (!empty($path) && @is_dir($path)) {
            $themePath = $path;
            break;
        }
    }

    if ($themePath !== '') {
        define('THEME_DIR', $themePath);
    } else {
        $errorMsg = "Hata: Geçerli bir tema dizini bulunamadı! Denenen yollar: " . implode(', ', $possibleThemePaths);
        error_log($errorMsg);
        echo "<div style='color: red;'><b>Hata:</b> $errorMsg</div>";
        define('THEME_DIR', __DIR__ . '/'); // Varsayılan olarak kök dizin
    }
}

// REQUEST_URL sabitini tanımla (çift tanımlamayı önle)
if (!defined('REQUEST_URL')) {
    define('REQUEST_URL', $_SERVER['REQUEST_URI'] ?? '/');
}

// Özel hata işleyici fonksiyonu
if (!function_exists('customErrorHandler')) {
    /**
     * Hataları hem loga yazar hem ekranda gösterir
     *
     * @param int $errno Hata numarası
     * @param string $errstr Hata mesajı
     * @param string|null $errfile Hata dosyası
     * @param int|null $errline Hata satırı
     * @return bool Hata işlendi mi
     */
    function customErrorHandler(int $errno, string $errstr, ?string $errfile = null, ?int $errline = null): bool
    {
        $errorMessage = "[$errno] $errstr - Dosya: " . ($errfile ?? 'bilinmiyor') . " - Satır: " . ($errline ?? 'bilinmiyor');
        error_log($errorMessage);
        echo "<div style='color: red;'><b>Hata:</b> $errorMessage</div>";
        return true;
    }
}
set_error_handler('customErrorHandler');

// İstatistik fonksiyonunu çalıştır
if (function_exists('istatistik_fonksiyonu')) {
    istatistik_fonksiyonu();
} else {
    $errorMsg = "Hata: istatistik_fonksiyonu tanımlı değil!";
    error_log($errorMsg);
    echo "<div style='color: red;'><b>Hata:</b> $errorMsg</div>";
}

// WWW yönlendirme
if (!str_contains($_SERVER['SERVER_NAME'] ?? '', 'www.') && ($gayarlar->site_www ?? 0) === 1) {
    header('Location: http://www.' . ($_SERVER['SERVER_NAME'] ?? '') . REQUEST_URL);
    exit;
}

// HTTPS yönlendirme
if (empty($_SERVER['HTTPS']) && ($gayarlar->site_ssl ?? 0) === 1) {
    header('Location: https://' . ($_SERVER['SERVER_NAME'] ?? '') . REQUEST_URL);
    exit;
}

// GET parametresini al ve temizle
$p = isset($gvn) && method_exists($gvn, 'harf_rakam')
    ? $gvn->harf_rakam($_GET['p'] ?? '')
    : preg_replace('/[^a-zA-Z0-9]/', '', $_GET['p'] ?? '');

// Sayfayı yönlendir
$pageFile = $p === '' ? THEME_DIR . 'index.php' : THEME_DIR . $p . '.php';
$notFoundPath = __DIR__ . '/404.php';

if (file_exists($pageFile)) {
    include $pageFile;
} elseif (file_exists($notFoundPath)) {
    include $notFoundPath;
} else {
    $errorMsg = "Hata: 404 dosyası ($notFoundPath) bulunamadı!";
    error_log($errorMsg);
    echo "<div style='color: red;'><b>Hata:</b> $errorMsg</div>";
    echo "<h1>404 - Sayfa Bulunamadı</h1>";
}