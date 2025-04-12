<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if (!$id) {
            die();
        }

        $stmt = $db->prepare("SELECT * FROM ekatalog_501 WHERE site_id_555=501 AND id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // Form verilerini temizle ve doğrula
        $baslik = $gvn->html_temizle($_POST['baslik']);
        $sira = filter_var($_POST['sira'], FILTER_VALIDATE_INT);

        if (empty($baslik)) {
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Resim yükleme işlemi
        if (!empty($_FILES['resim']['tmp_name'])) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($_FILES['resim']['name']);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['ekatalog']['thumb_x'], $gorsel_boyutlari['ekatalog']['thumb_y']);
            
            try {
                $stmt = $db->prepare("UPDATE ekatalog SET resim = :resim WHERE id = :id");
                $stmt->execute(['resim' => $resim, 'id' => $snc->id]);
                
                echo '<script type="text/javascript">
                    $(document).ready(function(){
                        $("#resim_src").attr("src", "/../uploads/thumb/' . $resim . '");
                    });
                    </script>';
                
                $fonk->ajax_tamam('Resim Güncellendi');
            } catch (PDOException $e) {
                // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
            }
        }

        // Dosya yükleme işlemi
        if (!empty($_FILES['dosya']['tmp_name'])) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($_FILES['dosya']['name']);
            move_uploaded_file($_FILES['dosya']['tmp_name'], "../uploads/kataloglar/" . $randnm);
            
            try {
                $stmt = $db->prepare("UPDATE ekatalog SET link = :link WHERE id = :id");
                $stmt->execute(['link' => 'uploads/kataloglar/' . $randnm, 'id' => $snc->id]);
            } catch (PDOException $e) {
                // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
            }
        }

        // Veritabanı güncelleme işlemi
        try {
            $stmt = $db->prepare("UPDATE ekatalog SET baslik = :baslik, sira = :sira WHERE id = :id");
            $stmt->execute(['baslik' => $baslik, 'sira' => $sira, 'id' => $snc->id]);
            
            $fonk->ajax_tamam("Katalog Güncellendi.");
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}