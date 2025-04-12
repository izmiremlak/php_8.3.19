<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $baslik = $gvn->html_temizle($_POST['baslik']);
        $sira = $gvn->zrakam($_POST['sira']);
        $resim1tmp = $_FILES['dosya']['tmp_name'] ?? '';
        $resim1nm = $_FILES['dosya']['name'] ?? '';

        // Boş alan kontrolü
        if (empty($baslik) || empty($sira)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Dosya yükleme işlemi
        if ($resim1tmp !== "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            if (!move_uploaded_file($resim1tmp, "../uploads/belgeler/" . $randnm)) {
                error_log("Dosya yüklenemedi: " . $resim1nm, 3, '/var/log/php_errors.log');
                die($fonk->ajax_hata("Dosya yüklenemedi."));
            }
        } else {
            $randnm = ''; // Dosya yüklenmemişse boş bırak
        }

        // Veritabanı ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO belgeler (baslik, sira, link, dil) VALUES (:baslik, :sira, :link, :dil)");
            $stmt->execute([
                'baslik' => $baslik,
                'sira' => $sira,
                'link' => "uploads/belgeler/" . $randnm,
                'dil' => $dil
            ]);

            // Başarılı mesajı göster
            $fonk->ajax_tamam("Belge Eklendi.");
            $fonk->yonlendir("index.php?p=belgeler", 3000);

        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}