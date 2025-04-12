<?php
// Kullanıcı kimliğini ve tipini kontrol et
if ($hesap->id != "" && $hesap->tipi != 0) {
    $id = $gvn->rakam($_GET["id"]);
    $snc = $db->prepare("SELECT * FROM kategoriler_501 WHERE id=:ids");
    $snc->execute(['ids' => $id]);

    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ);
    } else {
        error_log("Geçersiz kategori ID. Tarih: " . date("Y-m-d H:i:s"));
        die();
    }

    if ($_POST) {
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
        $kcvr = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik=:baslik AND id!=:id AND url=:urls AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'urls' => $url, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $url .= '_' . ($kcvr + 1);
        }

        // Veritabanı bağlantısını hata modunda ayarla
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Kategori bilgilerini güncelle
            $sql = $db->prepare("UPDATE kategoriler_501 SET baslik=:baslik, url=:url, sira=:sira WHERE id=:id");
            $sql->execute(['baslik' => $baslik, 'url' => $url, 'sira' => $sira, 'id' => $snc->id]);

            if ($sql) {
                $fonk->ajax_tamam("Video Kategori Güncellendi.");
            } else {
                $fonk->ajax_hata("Bir hata oluştu.");
            }
        } catch (PDOException $e) {
            // Hataları log dosyasına kaydet ve sitede göster
            error_log("Kategori güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }
    }
}