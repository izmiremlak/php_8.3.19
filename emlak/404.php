<?php
// THEME_DIR sabitinin tanımlı olup olmadığını kontrol eder. Tanımlı değilse "Problem 404" mesajı ile sonlandırır.
if (!defined("THEME_DIR")) {
    die("Problem 404");
}

// SCRIPT_NAME sunucu değişkenini kullanarak dizin adını alır.
$dirs = dirname($_SERVER["SCRIPT_NAME"]);

// Dizin adının son karakteri '/' değilse, '/' ekler.
if (substr($dirs, -1) !== '/') {
    $dirs .= '/';
}

// 404 hata sayfasını içeri aktarır.
include "modules/404.php";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Sayfa Bulunamadı</title>
    <meta name="description" content="Aradığınız sayfa bulunamadı. Lütfen ana sayfaya dönün.">
    <link rel="stylesheet" href="assets/css/admin.min.css">
</head>
<body>
    <div class="content">
        <div class="container">
            <div class="wrapper-page">
                <div class="ex-page-content text-center">
                    <!-- Başlık -->
                    <h1>404!</h1>
                    <!-- Alt Başlık -->
                    <h2>Üzgünüz, Sayfa Bulunamadı.</h2>
                    <br>
                </div>
                <br>
            </div>
        </div>
    </div>
    <script>
        var resizefunc = [];
    </script>
    <script src="assets/js/admin.min.js"></script>
</body>
</html>