<?php
// Türkçe: Sıkı tip denetimlerini etkinleştir
declare(strict_types=1);

// Türkçe: Güvenlik kontrolü
if (!defined('SERVER_HOST')) {
    die();
}

try {
    // Türkçe: PDO ile veritabanı bağlantısı
    $db = new PDO(
        'mysql:host=' . SERVER_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USERNAME,
        DB_PASSWORD
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Hata modunu etkinleştir
} catch (PDOException $e) {
    // Türkçe: Hata mesajını log dosyasına yaz ve ekrana göster
    handle_error('Bir hata oluştu: ' . $e->getMessage());
}

// Türkçe: Charset ayarları
try {
    $db->exec('SET NAMES utf8');
    $db->exec("SET NAMES 'UTF8'");
    $db->exec("SET character_set_connection = 'UTF8'");
    $db->exec("SET character_set_client = 'UTF8'");
    $db->exec("SET character_set_results = 'UTF8'");
} catch (PDOException $e) {
    // Türkçe: Hata mesajını log dosyasına yaz ve ekrana göster
    handle_error('Charset ayarlarında bir hata oluştu: ' . $e->getMessage());
}