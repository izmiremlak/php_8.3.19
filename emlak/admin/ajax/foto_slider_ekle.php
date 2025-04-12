<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($_POST) {
    if ($hesap->id != "" AND $hesap->tipi != 0) {
        // POST verilerini temizle
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $aciklama = $gvn->html_temizle($_POST["aciklama"]);
        $link = $gvn->html_temizle($_POST["link"]);

        // Resim yükleme işlemi
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];

        if ($resim1nm == '') {
            die($fonk->ajax_uyari("Lütfen görsel seçiniz!"));
        }

        if ($resim1tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['slider']['thumb_x'], $gorsel_boyutlari['slider']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['slider']['orjin_x'], $gorsel_boyutlari['slider']['orjin_y']);
        }

        // Veritabanına kayıt ekleme
        try {
            $ekle = $db->prepare("INSERT INTO slider_501 SET 
                baslik=:baslik, sira=:sira, resim=:resim, tarih=:bugun, dil=:dil, aciklama=:aciklamax, link=:linkx ");
            $ekle->execute([
                'baslik' => $baslik,
                'sira' => $sira,
                'resim' => $resim,
                'bugun' => $fonk->datetime(),
                'dil' => $dil,
                'aciklamax' => $aciklama,
                'linkx' => $link,
            ]);

            if ($ekle) {
                $fonk->ajax_tamam("Slayt Eklendi.");
                $fonk->yonlendir("index.php?p=foto_slider", 3000);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}