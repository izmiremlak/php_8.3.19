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

        $stmt = $db->prepare("SELECT * FROM subeler_bayiler_501 WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

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

        // Veritabanı güncelleme işlemi
        try {
            $stmt = $db->prepare("UPDATE subeler_bayiler_501 SET lokasyon = :lokasyon, sira = :sira, adres = :adres, telefon = :telefon, gsm = :gsm, email = :email, google_maps = :google_maps WHERE id = :id");
            $stmt->execute([
                'lokasyon' => $lokasyon,
                'sira' => $sira,
                'adres' => $adres,
                'telefon' => $telefon,
                'gsm' => $gsm,
                'email' => $email,
                'google_maps' => $google_maps,
                'id' => $id
            ]);
            $fonk->ajax_tamam("Bayi Güncellendi.");
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}