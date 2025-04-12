<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $tarih = $fonk->datetime();
        // Verileri güvenli bir şekilde al
        $baslik = htmlspecialchars($_POST["baslik"], ENT_QUOTES, 'UTF-8');
        $sira = $gvn->zrakam($_POST["sira"]);
        $url = $gvn->PermaLink($baslik);

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik)) {
            error_log("Lütfen başlık yazınız. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // Permalink kontrolü
        $kcvr = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik=:baslik AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $url .= '_' . ($kcvr + 1);
        }

        // Veritabanı bağlantısını hata modunda ayarla
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Kategori bilgilerini ekle
            $sql = $db->prepare("INSERT INTO kategoriler_501 SET dil=:dil, baslik=:baslik, url=:url, tarih=:tarih, sira=:sira, tipi=:tipi");
            $sql->execute([
                'dil' => $dil,
                'baslik' => $baslik,
                'url' => $url,
                'tarih' => $tarih,
                'sira' => $sira,
                'tipi' => 2
            ]);

            if ($sql) {
                $fonk->ajax_tamam("Video Kategori Eklendi.");
                $fonk->yonlendir("index.php?p=video_kategoriler", 3000);
            } else {
                $fonk->ajax_hata("Bir hata oluştu.");
            }
        } catch (PDOException $e) {
            // Hataları log dosyasına kaydet ve sitede göster
            error_log("Kategori eklenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }
    }
}