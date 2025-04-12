<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $adsoyad = $gvn->html_temizle($_POST['adsoyad']);
        $gsm = $gvn->html_temizle($_POST['gsm']);
        $telefon = $gvn->html_temizle($_POST['telefon']);
        $email = $gvn->html_temizle($_POST['email']);

        if (empty($adsoyad) || empty($gsm) || empty($telefon) || empty($email)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];

        if (!empty($resim1tmp)) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', $gorsel_boyutlari['danismanlar']['thumb_x'], $gorsel_boyutlari['danismanlar']['thumb_y']);
        } else {
            $resim = "default_danisman_resim.png";
        }

        // Veritabanı ekleme işlemi
        try {
            $stmt = $db->prepare("INSERT INTO danismanlar_501 (adsoyad, gsm, telefon, email, resim, tarih) VALUES (:adsoyad, :gsm, :telefon, :email, :resim, :tarih)");
            $stmt->execute([
                'adsoyad' => $adsoyad,
                'gsm' => $gsm,
                'telefon' => $telefon,
                'email' => $email,
                'resim' => $resim,
                'tarih' => $fonk->datetime()
            ]);

            // Başarılı mesajı göster
            $fonk->ajax_tamam("Başarıyla Eklendi.");
            $fonk->yonlendir("index.php?p=danismanlar", 3000);

        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}