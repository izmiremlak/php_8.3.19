<?php 
// Gerekli fonksiyonları içe aktarır.
include "functions.php";

// GET isteğinden gelen 'p' parametresini harf ve rakamlarla sınırlar.
$p = $gvn->harf_rakam($_GET["p"]);

// İlgili AJAX dosyasının yolunu belirler.
$pdir = 'ajax/' . $p . '.php';

// Sunucu adresini alır.
$stadrs = $_SERVER["SERVER_NAME"];

// Sunucu adresi 'izmirtr.com' içeriyorsa belirli işlemleri yapar.
if (str_contains($stadrs, "izmirtr.com")) {
    if (
        $p === 'giris' || $p === 'sube_bayi_getir' || $p === 'ilce_getir' || $p === 'il_getir' ||
        $p === 'mahalle_getir' || $p === 'ilanlar' || $p === 'ilanlar2' || $p === 'mesajlar_bildirim' ||
        $p === 'mesajlar_gonder' || $p === 'mesajlar_sil' || $p === 'mesajlar_okundu' || $p === 'mesaj_detay'
    ) {
        // İşlem yapmaya izin veriliyor.
    } else {
        // İşlem yapılmasına izin verilmiyor.
        die('<span class="error">Demo versiyonda işlem yapamazsınız.</span>');
    }
} else {
    // Demo versiyonda işlem yapılamaz mesajı.
    die('<span class="error">Demo versiyonda işlem yapamazsınız.</span>');
}

// Belirtilen AJAX dosyasının varlığını kontrol eder ve içe aktarır.
if (file_exists($pdir)) {
    require $pdir;
} else {
    // Hata mesajını hem log dosyasına yazar hem de ekrana gösterir.
    error_log("File Not Found: " . $pdir, 3, "/var/log/php_errors.log");
    echo '<span class="error">File Not Found</span>';
}