<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Form verilerini temizle ve doğrula
        $data = trim($_POST['gonderilenler']);
        $emails = explode("\n", $data);
        $emaillerx = [];

        foreach ($emails as $eml) {
            $eml = $gvn->html_temizle(trim($eml));
            if ($eml !== "" && !in_array($eml, $emaillerx) && $gvn->eposta_kontrol($eml)) {
                $emaillerx[] = $eml;
            }
        }

        $bulten = implode(",", $emaillerx);

        // Veritabanı güncelleme işlemi
        try {
            $stmt = $db->prepare("UPDATE gayarlar_501 SET bulten_email = :bulten");
            $stmt->execute(['bulten' => $bulten]);

            $fonk->ajax_tamam("Data başarıyla güncellendi.");
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}