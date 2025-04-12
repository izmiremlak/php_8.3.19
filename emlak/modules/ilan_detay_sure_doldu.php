<?php
// Tema dizini tanımlı değilse çıkış yap
if (!defined("THEME_DIR")) {
    die();
}

// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Wrapper divi başlangıcı
?>
<div id="wrapper">
    <div class="content" id="bigcontent">
        <?php
        // Dil dosyasından TX621 anahtarını getir ve ekrana yazdır
        echo htmlspecialchars(dil("TX621"), ENT_QUOTES, 'UTF-8');
        ?>
        <br><br><br>
    </div>
</div>

<?php
// Footer dosyasını dahil et
include THEME_DIR . "inc/footer.php";
?>