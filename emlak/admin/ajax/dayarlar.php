<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if (!empty($hesap->id) && $hesap->tipi !== 0) {
        // Girişleri temizle ve doğrula
        $title = $gvn->html_temizle($_POST['title'] ?? '');
        $keywords = $gvn->html_temizle($_POST['keywords'] ?? '');
        $description = $gvn->html_temizle($_POST['description'] ?? '');
        $facebook = $gvn->html_temizle($_POST['facebook'] ?? '');
        $twitter = $gvn->html_temizle($_POST['twitter'] ?? '');
        $instagram = $gvn->html_temizle($_POST['instagram'] ?? '');
        $google = $gvn->html_temizle($_POST['google'] ?? '');
        $google_maps = $gvn->html_temizle($_POST['google_maps'] ?? '');
        $slogan1 = $gvn->html_temizle($_POST['slogan1'] ?? '');
        $slogan2 = $gvn->html_temizle($_POST['slogan2'] ?? '');
        $slogan3 = $gvn->html_temizle($_POST['slogan3'] ?? '');
        $telefon = $gvn->html_temizle($_POST['telefon'] ?? '');
        $faks = $gvn->html_temizle($_POST['faks'] ?? '');
        $gsm = $gvn->html_temizle($_POST['gsm'] ?? '');
        $email = $gvn->html_temizle($_POST['email'] ?? '');
        $adres = $_POST['adres'] ?? '';
        $analytics = $_POST['analytics'] ?? '';
        $verification = $_POST['verification'] ?? '';
        $embed = $_POST['embed'] ?? '';
        $google_api_key = $_POST['google_api_key'] ?? '';

        // Veritabanı güncelleme işlemi
        try {
            $stmt = $db->prepare("UPDATE gayarlar_501 SET google_api_key = :google_api_key");
            $stmt->execute(['google_api_key' => $google_api_key]);

            $stmt = $db->prepare("UPDATE ayarlar_501 SET 
                title = :title,
                keywords = :keywords,
                description = :description,
                facebook = :facebook,
                twitter = :twitter,
                instagram = :instagram,
                google = :google,
                slogan1 = :slogan1,
                slogan2 = :slogan2,
                slogan3 = :slogan3,
                telefon = :telefon,
                faks = :faks,
                gsm = :gsm,
                email = :email,
                adres = :adres,
                analytics = :analytics,
                google_maps = :google_maps,
                embed = :embed,
                verification = :verification
            ");
            $stmt->execute([
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'instagram' => $instagram,
                'google' => $google,
                'slogan1' => $slogan1,
                'slogan2' => $slogan2,
                'slogan3' => $slogan3,
                'telefon' => $telefon,
                'faks' => $faks,
                'gsm' => $gsm,
                'email' => $email,
                'adres' => $adres,
                'analytics' => $analytics,
                'google_maps' => $google_maps,
                'embed' => $embed,
                'verification' => $verification,
            ]);

            $fonk->ajax_tamam("Site Bilgileri Güncellendi.");

        } catch (PDOException $e) {
            error_log("Hata: " . $e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            exit;
        }
    } else {
        $fonk->ajax_hata("Yetkisiz erişim!");
        exit;
    }
} else {
    $fonk->ajax_hata("Geçersiz istek!");
    exit;
}