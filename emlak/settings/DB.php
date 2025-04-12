<?php
// Türkçe: Sıkı tip denetimlerini etkinleştir
declare(strict_types=1);

// Türkçe: Veritabanı bilgilerinizi tanımlar
const SERVER_HOST = 'localhost';
const DB_NAME = 'turkiyeemlaksitesitrdb';
const DB_USERNAME = 'turkiyeemlaksitesitrdb';
const DB_PASSWORD = '!turkiyeTR1234turkiyeTR?';
const DB_CHARSET = 'utf8';

// Türkçe: PayPal tahsilat ayarları
$pay_secret = 'a1b2c3d4e5f6&123+&654321'; // !ÖNEMLİ! :: PayPal ödeme için gereklidir. Rastgele bir değer girin. Güvenlik için gereklidir.

// Türkçe: SMTP Debugger
const SMTP_DEBUG = false; // Aktif: true, Pasif: false

// Türkçe: Tahsilat sonuç sayfa adresleri
$domain = str_replace('www.', '', strtolower($_SERVER['SERVER_NAME']));

// Türkçe: HTTP veya HTTPS kontrolü
$http_scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') ? 'http' : 'https';
$www = stristr($_SERVER['HTTP_HOST'], 'www') ? 'www.' : '';

define('PAYTR_OK_URL', $http_scheme . '://' . $www . $domain . '/odeme-tamamlandi');
define('PAYTR_FAIL_URL', $http_scheme . '://' . $www . $domain . '/odeme-basarisiz');

// Türkçe: PayPal ödeme sırrı
define('PAY_SECRET', md5($pay_secret));

// Türkçe: Ödeme yöntemleri
$oyontemleri = ['Banka Havale/EFT', 'Kredi Kartı', 'Kredi Kartı (PAYPAL)'];

// Türkçe: Zaman dilimi ayarı (Türkiye için yaz saati uygulaması nedeniyle Asia/Riyadh kullanılır)
date_default_timezone_set('Asia/Riyadh');

// Türkçe: PHP teknik ayarları
error_reporting(E_ALL ^ E_NOTICE);

// Türkçe: Log dizinini kontrol et ve oluştur
$logDir = __DIR__ . '/logs';
$logFile = $logDir . '/error.log';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
if (!is_writable($logDir)) {
    chmod($logDir, 0755);
}

/**
 * Türkçe: Hataları log dosyasına yazma fonksiyonu
 * @param string $message Hata mesajı
 */
function log_error(string $message): void
{
    global $logFile;
    error_log($message . PHP_EOL, 3, $logFile); // Hata mesajını log dosyasına yaz
}

/**
 * Türkçe: Hata mesajlarını hem log dosyasına yaz hem de ekrana göster
 * @param string $error_message Hata mesajı
 */
function handle_error(string $error_message): void
{
    log_error($error_message); // Log dosyasına yaz
    if (ini_get('display_errors')) { // Yalnızca display_errors açıkken ekrana yaz
        echo '<div style="color: red; border: 1px solid red; padding: 10px; margin: 10px;">' .
             htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . '</div>';
    }
}