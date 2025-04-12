<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Form verilerini temizle ve doğrula
        $baslik = $gvn->html_temizle($_POST['baslik']);
        $sira = filter_var($_POST['sira'], FILTER_VALIDATE_INT);

        if (empty($baslik)) {
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        $resim1tmp = $_FILES['resim']['tmp_name'] ?? '';
        $resim1nm = $_FILES['resim']['name'] ?? '';
        $dosya1tmp = $_FILES['dosya']['tmp_name'] ?? '';
        $dosya1nm = $_FILES['dosya']['name'] ?? '';

        // Resim yükleme işlemi
        if (!empty($resim1tmp)) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['ekatalog']['thumb_x'], $gorsel_boyutlari['ekatalog']['thumb_y']);
        }

        // Dosya yükleme işlemi
        if (!empty($dosya1tmp)) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($dosya1nm);
            if (!move_uploaded_file($dosya1tmp, "../uploads/kataloglar/" . $randnm)) {
                // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
                $errorMessage = 'Dosya yüklenirken bir hata oluştu.';
                error_log($errorMessage, 3, '/var/log/php_errors.log');
                die($fonk->ajax_hata($errorMessage));
            }
            $dosya = $randnm;
        }

        // Veritabanına ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO ekatalog (baslik, sira, link, resim, dil) VALUES (:baslik, :sira, :link, :resim, :dil)");
            $stmt->execute([
                'baslik' => $baslik,
                'sira' => $sira,
                'link' => "uploads/kataloglar/" . $dosya,
                'resim' => $resim,
                'dil' => $dil
            ]);

            $fonk->ajax_tamam("Katalog Eklendi.");
            $fonk->yonlendir("index.php?p=ekatalog", 3000);
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}