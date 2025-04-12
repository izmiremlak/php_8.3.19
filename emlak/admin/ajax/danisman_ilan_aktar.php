<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $danisman_id = filter_var($_POST['danisman_id'], FILTER_VALIDATE_INT);
        $danisman_yeni_id = filter_var($_POST['danisman_yeni_id'], FILTER_VALIDATE_INT);

        if (!$danisman_id || !$danisman_yeni_id) {
            error_log("Lütfen seçim yapınız...", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Lütfen seçim yapınız..."));
        }

        $stmt = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND tipi = 4 AND danisman_id = :danisman_id");
        $stmt->execute(['danisman_id' => $danisman_id]);
        $kac = $stmt->rowCount();

        if ($kac < 1) {
            error_log("Danışmanın ilanı yok.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Danışmanın ilanı yok."));
        }

        // Veritabanı güncelleme işlemi
        try {
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $query = $db->prepare("UPDATE sayfalar SET danisman_id = :danisman_yeni_id WHERE site_id_555=501 AND danisman_id = :danisman_id");
            $query->execute([
                'danisman_yeni_id' => $danisman_yeni_id,
                'danisman_id' => $danisman_id
            ]);

            echo $kac . " adet ilan başarıyla aktarıldı.";

        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}