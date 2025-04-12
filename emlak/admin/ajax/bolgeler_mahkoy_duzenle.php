<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            error_log("Geçersiz ID.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Geçersiz ID."));
        }

        $stmt = $db->prepare("SELECT * FROM mahalle_koy WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // Girişleri temizle ve doğrula
        $adi = $gvn->html_temizle($_POST['adi']);
        if (empty($adi)) {
            error_log("Lütfen bir isim belirleyin.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen bir isim belirleyin."));
        }

        $slug = $gvn->PermaLink($adi);
        $il_slug = $db->query("SELECT slug FROM il WHERE id=" . $snc->il_id)->fetch(PDO::FETCH_OBJ)->slug;
        $ilce_slug = $db->query("SELECT slug FROM ilce WHERE id=" . $snc->ilce_id)->fetch(PDO::FETCH_OBJ)->slug;
        $slug2 = $il_slug . "-" . $ilce_slug . "-" . $slug;

        // Veritabanı güncelleme işlemi
        try {
            $stmt = $db->prepare("UPDATE mahalle_koy SET mahalle_adi = :adi, slug = :slug, slug2 = :slug2 WHERE id = :id");
            $stmt->execute([
                'adi' => $adi,
                'slug' => $slug,
                'slug2' => $slug2,
                'id' => $snc->id
            ]);

            // Başarılı mesajı göster
            $fonk->ajax_tamam("Başarıyla Güncellendi.");
            $fonk->yonlendir("index.php?p=bolgeler_ilce&id=" . $snc->ilce_id, 500);

        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}