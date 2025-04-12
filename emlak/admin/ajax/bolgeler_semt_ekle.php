<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $ilce_id = filter_var($_GET['ilce_id'], FILTER_VALIDATE_INT);
        if (!$ilce_id) {
            error_log("Lütfen ilçe seçiniz.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen ilçe seçiniz."));
        }

        $adi = $gvn->html_temizle($_POST['adi']);
        if (empty($adi)) {
            error_log("Lütfen bir isim belirleyin.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen bir isim belirleyin."));
        }

        $stmt = $db->prepare("SELECT * FROM ilce WHERE id = :id");
        $stmt->execute(['id' => $ilce_id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        $slug = $gvn->PermaLink($adi);

        // Veritabanı ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO semt (il_id, ilce_id, ulke_id, semt_adi, slug) VALUES (:il_id, :ilce_id, :ulke_id, :semt_adi, :slug)");
            $stmt->execute([
                'il_id' => $snc->il_id,
                'ilce_id' => $snc->id,
                'ulke_id' => $snc->ulke_id,
                'semt_adi' => $adi,
                'slug' => $slug
            ]);

            // Başarılı mesajı göster
            $fonk->ajax_tamam("Başarıyla Eklendi.");
            $fonk->yonlendir("index.php?p=bolgeler_ilce&id=" . $ilce_id, 1000);

        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}