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

        $stmt = $db->prepare("SELECT * FROM belgeler WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // Girişleri temizle ve doğrula
        $baslik = $gvn->html_temizle($_POST['baslik']);
        $sira = $gvn->zrakam($_POST['sira']);
        $resim1tmp = $_FILES['dosya']['tmp_name'];
        $resim1nm = $_FILES['dosya']['name'];

        // Boş alan kontrolü
        if (empty($baslik) || empty($sira)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Dosya yükleme işlemi
        if ($resim1tmp !== "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            if (move_uploaded_file($resim1tmp, "../uploads/belgeler/" . $randnm)) {
                $stmt = $db->prepare("UPDATE belgeler SET link = :link WHERE id = :id");
                $stmt->execute([
                    'link' => 'uploads/belgeler/' . $randnm,
                    'id' => $snc->id
                ]);
            } else {
                error_log("Dosya yüklenemedi: " . $resim1nm, 3, '/var/log/php_errors.log');
                die($fonk->ajax_hata("Dosya yüklenemedi."));
            }
        }

        // Veritabanı güncelleme işlemi
        try {
            $stmt = $db->prepare("UPDATE belgeler SET baslik = :baslik, sira = :sira WHERE id = :id");
            $stmt->execute([
                'baslik' => $baslik,
                'sira' => $sira,
                'id' => $snc->id
            ]);
            $fonk->ajax_tamam("Belge Güncellendi.");
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}