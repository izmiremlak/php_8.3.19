<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {
        // Tarih, başlık ve sıra verilerini temizle ve doğrula
        $tarih = $fonk->datetime();
        $baslik = $gvn->html_temizle($_POST['baslik']);
        $sira = filter_var($_POST['sira'], FILTER_VALIDATE_INT);
        $url = $gvn->PermaLink($baslik);

        if (empty($baslik)) {
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // Permalink kontrolü
        $stmt = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik = :baslik AND dil = :dil");
        $stmt->execute(['baslik' => $baslik, 'dil' => $dil]);
        if ($stmt->rowCount() > 0) {
            $url .= '_' . ($stmt->rowCount() + 1);
        }

        // Veritabanına ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO kategoriler_501 (dil, baslik, url, tarih, sira, tipi) VALUES (:dil, :baslik, :url, :tarih, :sira, :tipi)");
            $stmt->execute(['dil' => $dil, 'baslik' => $baslik, 'url' => $url, 'tarih' => $tarih, 'sira' => $sira, 'tipi' => 1]);

            $fonk->ajax_tamam("Foto Galeri Eklendi.");
            $fonk->yonlendir("index.php?p=foto_galeri", 3000);
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
        }
    }
}