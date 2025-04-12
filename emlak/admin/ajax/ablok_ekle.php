<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {
        
        // Girişleri temizle ve doğrula
        $sira = filter_input(INPUT_POST, 'sira', FILTER_VALIDATE_INT);
        $icon = filter_input(INPUT_POST, 'icon', FILTER_SANITIZE_STRING);
        $aciklama = filter_input(INPUT_POST, 'aciklama', FILTER_SANITIZE_STRING);
        $baslik = $gvn->html_temizle($_POST['baslik']);
        $url = $gvn->html_temizle($_POST['url']);

        if (empty($baslik) || empty($aciklama) || empty($sira)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Dosya yükleme işlemi
        $resim1tmp = $_FILES['resim']['tmp_name'] ?? '';
        $resim1nm = $_FILES['resim']['name'] ?? '';

        if ($resim1tmp !== '') {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['abloklar']['orjin_x'], $gorsel_boyutlari['abloklar']['orjin_y']);
        }

        // Blok verilerini veritabanına ekle
        $ekle = $db->prepare("INSERT INTO abloklar (dil, sira, icon, resim, baslik, aciklama, url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($ekle->execute([$dil, $sira, $icon, $resim, $baslik, $aciklama, $url])) {
            $fonk->ajax_tamam("Anasayfa Blok Eklendi.");
            $fonk->yonlendir("index.php?p=abloklar", 3000);
        } else {
            error_log("Bir hata oluştu.", 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}