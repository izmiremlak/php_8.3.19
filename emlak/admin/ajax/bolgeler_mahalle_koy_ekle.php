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

        $semt_id = filter_var($_POST['semt'], FILTER_VALIDATE_INT);

        $stmt = $db->prepare("SELECT * FROM ilce WHERE id = :id");
        $stmt->execute(['id' => $ilce_id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        $slug = $gvn->PermaLink($adi);
        $il_slug = $db->query("SELECT slug FROM il WHERE id=" . $snc->il_id)->fetch(PDO::FETCH_OBJ)->slug;
        $ilce_slug = $snc->slug;
        $slug2 = $il_slug . "-" . $ilce_slug . "-" . $slug;

        // Veritabanı ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO mahalle_koy (il_id, ilce_id, semt_id, ulke_id, mahalle_adi, slug, slug2) VALUES (:il_id, :ilce_id, :semt_id, :ulke_id, :mahalle_adi, :slug, :slug2)");
            $stmt->execute([
                'il_id' => $snc->il_id,
                'ilce_id' => $snc->id,
                'semt_id' => $semt_id,
                'ulke_id' => $snc->ulke_id,
                'mahalle_adi' => $adi,
                'slug' => $slug,
                'slug2' => $slug2
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