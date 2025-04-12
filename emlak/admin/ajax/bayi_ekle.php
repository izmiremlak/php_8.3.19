<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id !== "" && $hesap->tipi !== 0) {
    // POST isteği olup olmadığını kontrol et
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Girişleri temizle ve doğrula
        $lokasyon = $gvn->html_temizle($_POST['lokasyon']);
        $sira = $gvn->zrakam($_POST['sira']);
        $adres = $gvn->html_temizle($_POST['adres']);
        $telefon = $gvn->html_temizle($_POST['telefon']);
        $gsm = $gvn->html_temizle($_POST['gsm']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $google_maps = $gvn->html_temizle($_POST['google_maps']);

        // Boş alan kontrolü
        if (empty($lokasyon) || empty($sira) || empty($adres) || empty($telefon) || empty($gsm) || empty($email) || empty($google_maps)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Veritabanı ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO subeler_bayiler_501 (turu, lokasyon, sira, adres, telefon, gsm, email, google_maps, dil) VALUES (:turu, :lokasyon, :sira, :adres, :telefon, :gsm, :email, :google_maps, :dil)");
            $stmt->execute([
                'turu' => '1',
                'lokasyon' => $lokasyon,
                'sira' => $sira,
                'adres' => $adres,
                'telefon' => $telefon,
                'gsm' => $gsm,
                'email' => $email,
                'google_maps' => $google_maps,
                'dil' => $dil
            ]);

            // Başarılı mesajı göster
            $fonk->ajax_tamam("Bayi Eklendi.");
            $fonk->yonlendir("index.php?p=bayiler", 1500);

        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}