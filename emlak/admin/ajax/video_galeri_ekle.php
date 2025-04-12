<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $baslik = htmlspecialchars($_POST["baslik"], ENT_QUOTES, 'UTF-8');
        $sira = $gvn->zrakam($_POST["sira"]);
        $kategori_id = $gvn->zrakam($_POST["kategori_id"]);
        $youtube = htmlspecialchars($_POST["youtube"], ENT_QUOTES, 'UTF-8');
        $kesyou = substr($youtube, 32, 100);
        $resim = 'http://i1.ytimg.com/vi/' . $kesyou . '/hqdefault.jpg';

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik) || $fonk->bosluk_kontrol($youtube)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Veritabanı bağlantısını hata modunda ayarla
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Video galeri bilgilerini ekle
            $ekle = $db->prepare("INSERT INTO video_galeri SET sira=?, baslik=?, youtube=?, resim=?, dil=?, kategori_id=?");
            $ekle->execute([$sira, $baslik, $youtube, $resim, $dil, $kategori_id]);

            if ($ekle) {
                $fonk->ajax_tamam("Video Galeri Eklendi.");
                $fonk->yonlendir("index.php?p=video_galeri", 3000);
            }
        } catch (PDOException $e) {
            // Hataları log dosyasına kaydet ve sitede göster
            error_log("Video galeri eklenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }
    }
}