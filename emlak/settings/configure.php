<?php
// Türkçe: Sıkı tip denetimlerini etkinleştir
declare(strict_types=1);

// Türkçe: Veritabanı ayarlarını dahil et
require_once __DIR__ . '/DB.php';

// Türkçe: PHP sürüm kontrolü ve minimum gereksinim
$php_ver = substr(phpversion(), 0, 3);
if ($php_ver < '5.4') {
    die('Yazılım en düşük PHP 5.4 kadar desteklemektedir. Lütfen PHP versiyonunuzu yükseltin.');
}

// Türkçe: Domain adı kontrolü ve ayarlaması
$stadrs = str_replace('www.', '', strtolower($_SERVER['SERVER_NAME']));
if (str_contains($stadrs, 'izmiremlakbirligi.com.tr')) {
    $domain2 = 'izmiremlakbirligi.com.tr';
} else {
    $domain_file = __DIR__ . '/domain.txt';
    if (!is_file($domain_file)) {
        touch($domain_file);
        file_put_contents($domain_file, $stadrs);
        $domain2 = $stadrs;
    } else {
        $domain2 = file_get_contents($domain_file);
        if ($domain2 === '') {
            file_put_contents($domain_file, $stadrs);
            $domain2 = $stadrs;
        }
    }
}

// Türkçe: Veritabanı bağlantısını dahil et
require_once __DIR__ . '/dbconnect.php';