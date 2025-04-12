<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Form verilerini temizle ve doğrula
        $foot_link1 = $gvn->html_temizle($_POST['foot_link1']);
        $foot_link2 = $gvn->html_temizle($_POST['foot_link2']);
        $foot_link3 = $gvn->html_temizle($_POST['foot_link3']);
        $foot_link4 = $gvn->html_temizle($_POST['foot_link4']);
        $foot_link5 = $gvn->html_temizle($_POST['foot_link5']);
        $foot_link6 = $gvn->html_temizle($_POST['foot_link6']);
        $foot_link7 = $gvn->html_temizle($_POST['foot_link7']);

        $foot_text1 = $gvn->html_temizle($_POST['foot_text1']);
        $foot_text2 = $gvn->html_temizle($_POST['foot_text2']);
        $foot_text3 = $gvn->html_temizle($_POST['foot_text3']);
        $foot_text4 = $gvn->html_temizle($_POST['foot_text4']);
        $foot_text5 = $gvn->html_temizle($_POST['foot_text5']);
        $foot_text6 = $gvn->html_temizle($_POST['foot_text6']);
        $foot_text7 = $gvn->html_temizle($_POST['foot_text7']);

        $foot_sayfa1 = filter_var($_POST['foot_sayfa1'], FILTER_VALIDATE_INT);
        $foot_sayfa2 = filter_var($_POST['foot_sayfa2'], FILTER_VALIDATE_INT);
        $foot_sayfa3 = filter_var($_POST['foot_sayfa3'], FILTER_VALIDATE_INT);
        $foot_sayfa4 = filter_var($_POST['foot_sayfa4'], FILTER_VALIDATE_INT);
        $foot_sayfa5 = filter_var($_POST['foot_sayfa5'], FILTER_VALIDATE_INT);
        $foot_sayfa6 = filter_var($_POST['foot_sayfa6'], FILTER_VALIDATE_INT);
        $foot_sayfa7 = filter_var($_POST['foot_sayfa7'], FILTER_VALIDATE_INT);

        // Veritabanı güncelleme işlemi
        try {
            $stmt = $db->prepare("UPDATE ayarlar_501 SET foot_link1 = ?, foot_link2 = ?, foot_link3 = ?, foot_link4 = ?, foot_link5 = ?, foot_link6 = ?, foot_link7 = ?, foot_text1 = ?, foot_text2 = ?, foot_text3 = ?, foot_text4 = ?, foot_text5 = ?, foot_text6 = ?, foot_text7 = ?, foot_sayfa1 = ?, foot_sayfa2 = ?, foot_sayfa3 = ?, foot_sayfa4 = ?, foot_sayfa5 = ?, foot_sayfa6 = ?, foot_sayfa7 = ?");
            $stmt->execute([$foot_link1, $foot_link2, $foot_link3, $foot_link4, $foot_link5, $foot_link6, $foot_link7, $foot_text1, $foot_text2, $foot_text3, $foot_text4, $foot_text5, $foot_text6, $foot_text7, $foot_sayfa1, $foot_sayfa2, $foot_sayfa3, $foot_sayfa4, $foot_sayfa5, $foot_sayfa6, $foot_sayfa7]);

            $fonk->ajax_tamam("Bilgiler Güncellendi.");
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir Hata Oluştu! " . htmlspecialchars($e->getMessage()));
        }
    }
}