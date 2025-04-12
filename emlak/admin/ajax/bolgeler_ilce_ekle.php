<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $il_id = filter_var($_GET['il_id'], FILTER_VALIDATE_INT);
        if (!$il_id) {
            error_log("Lütfen il seçiniz.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen il seçiniz."));
        }

        $adi = $gvn->html_temizle($_POST['adi']);
        if (empty($adi)) {
            error_log("Lütfen bir isim belirleyin.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen bir isim belirleyin."));
        }

        $stmt = $db->prepare("SELECT * FROM il WHERE id = :id");
        $stmt->execute(['id' => $il_id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        $slug = $gvn->PermaLink($adi);
        $il_slug = $snc->slug;
        $slug2 = $il_slug . "-" . $slug;

        // Veritabanı ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO ilce (il_id, ulke_id, ilce_adi, slug, slug2) VALUES (:il_id, :ulke_id, :ilce_adi, :slug, :slug2)");
            $stmt->execute([
                'il_id' => $il_id,
                'ulke_id' => $snc->ulke_id,
                'ilce_adi' => $adi,
                'slug' => $slug,
                'slug2' => $slug2
            ]);

            // Başarılı mesajı göster
            $fonk->ajax_tamam("Başarıyla Eklendi.");
            $fonk->yonlendir("index.php?p=bolgeler_il&id=" . $il_id, 1000);

        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}