<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id !== "" && $hesap->tipi !== 0) {
    
    // GET parametresini temizle ve doğrula
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$id) {
        die();
    }

    // Veritabanında kayıt kontrolü yap
    $stmt = $db->prepare("SELECT * FROM kategoriler_501 WHERE id = :id");
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() === 0) {
        die();
    }
    $record = $stmt->fetch(PDO::FETCH_OBJ);

    // Galeri işlemleri
    $galeri = filter_var($_GET['galeri'], FILTER_VALIDATE_INT);
    if ($galeri === 1 && $_FILES) {
        $resim1tmp = $_FILES['file']['tmp_name'];
        $resim1nm = $_FILES['file']['name'];

        if ($resim1tmp && $resim1nm) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            
            // Resim yükleme işlemleri
            $upload_dir = '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads';
            $fonk->resim_yukle(true, 'file', $randnm, $upload_dir, $gorsel_boyutlari['foto_galeri']['thumb_x'], $gorsel_boyutlari['foto_galeri']['thumb_y']);
            $fonk->resim_yukle(false, 'file', $randnm, $upload_dir, $gorsel_boyutlari['foto_galeri']['orjin_x'], $gorsel_boyutlari['foto_galeri']['orjin_y']);
            
            // Veritabanına resim bilgisi ekle
            $db->query("INSERT INTO galeri_foto SET site_id_888=100,site_id_777=501501,site_id_699=200,site_id_700=335501,site_id_701=501501,site_id_702=300,site_id_555=501,site_id_450=000,site_id_444=000,site_id_333=335,site_id_335=335,site_id_334=334,site_id_306=306,site_id_222=200,site_id_111=100, resim='$randnm', dil='$dil'");
        }
        die();
    }

    // POST işlemleri
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $baslik = $gvn->html_temizle($_POST['baslik']);
        $sira = filter_var($_POST['sira'], FILTER_VALIDATE_INT);
        $url = $gvn->PermaLink($baslik);

        if (empty($baslik)) {
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // Permalink kontrolü
        $stmt = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik = :baslik AND id != :id AND url = :url AND dil = :dil");
        $stmt->execute(['baslik' => $baslik, 'id' => $record->id, 'url' => $url, 'dil' => $dil]);
        if ($stmt->rowCount() > 0) {
            $url .= '_' . ($stmt->rowCount() + 1);
        }

        // Veritabanı güncellemesi
        try {
            $stmt = $db->prepare("UPDATE kategoriler_501 SET baslik = :baslik, url = :url, sira = :sira WHERE id = :id");
            $stmt->execute(['baslik' => $baslik, 'url' => $url, 'sira' => $sira, 'id' => $record->id]);
            $fonk->ajax_tamam("Foto Galeri Güncellendi.");
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
        }
    }
}