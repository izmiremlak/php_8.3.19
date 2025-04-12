<?php 
// Gerekli dosyaları dahil et
include "../functions.php";

// Gelen parametreyi güvenli bir şekilde al ve filtrele
$p = $gvn->harf_rakam($_GET["p"] ?? '');
$pdir = 'ajax/' . $p . '.php';

// Kullanıcı tipi kontrolleri
if (!in_array($p, ['login', 'forget_password']) && $hesap->tipi === 0) {
    // Hata mesajı döndür ve işlemi sonlandır
    exit($fonk->ajax_hata("Hata!"));
}

if (!in_array($p, ['login', 'forget_password', 'ilce_getir', 'ilce_getir_string']) && $hesap->tipi === 2) {
    // Demo versiyon kullanıcıları için işlemi sonlandır
    exit($fonk->ajax_hata("Demo versiyonda işlem yapamazsınız."));
}

// Yüklenen dosyaları kontrol et
if (!empty($_FILES)) {
    foreach ($_FILES as $key => $value) {
        $fi = $value;
        $uzanti = $fonk->uzanti($fi["name"] ?? '');
        $tehlikeliUzantilar = [".php", ".html", ".htaccess", ".ini", ".conf"];

        // Tehlikeli uzantıları engelle
        if (in_array($uzanti, $tehlikeliUzantilar, true)) {
            $fonk->ajax_hata("Bu dosya sitenize zarar verebildiği için yüklenemedi.", 4000);
            exit();
        }
    }
}

// Login işlemi için reCAPTCHA doğrulaması
if ($p === 'login') {
    $recaptchaSecret = '6LeOm_8qAAAAANXJTLdtXL30lBPAQzBEnApAtuYW';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // reCAPTCHA doğrulama URL ve verileri
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $recaptchaSecret,
        'response' => $recaptchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];

    // HTTP isteği için ayarlar
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    // reCAPTCHA doğrulama isteğini gönder
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resultJson = json_decode($result, false);

    // Doğrulama başarısızsa hata döndür
    if (!$resultJson || $resultJson->success !== true) {
        exit($fonk->ajax_hata("reCAPTCHA doğrulaması başarısız! Lütfen tekrar deneyin."));
    }
}

// Dosyanın mevcut olup olmadığını kontrol et ve dahil et
if (file_exists($pdir)) {
    require $pdir;
} else {
    // Hata mesajını hem log dosyasına yaz hem de kullanıcıya göster
    error_log("Dosya bulunamadı: $pdir", 0);
    echo 'File Not Found';
}