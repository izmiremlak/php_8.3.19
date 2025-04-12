<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $ulke_id = filter_var($_GET['ulke_id'], FILTER_VALIDATE_INT);
        if (!$ulke_id) {
            error_log("Lütfen ülke seçiniz.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen ülke seçiniz."));
        }

        $adi = $gvn->html_temizle($_POST['adi']);
        if (empty($adi)) {
            error_log("Lütfen bir isim belirleyin.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen bir isim belirleyin."));
        }

        $slug = $gvn->PermaLink($adi);

        // Veritabanı ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO il (ulke_id, il_adi, slug) VALUES (:ulke_id, :il_adi, :slug)");
            $stmt->execute([
                'ulke_id' => $ulke_id,
                'il_adi' => $adi,
                'slug' => $slug
            ]);

            // Başarılı mesajı göster
            $fonk->ajax_tamam("Başarıyla Eklendi.");
            $fonk->yonlendir("index.php?p=bolgeler_ulke&id=" . $ulke_id, 1000);

        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}