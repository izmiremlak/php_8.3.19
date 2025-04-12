<?php
// Kullanıcı kimliğini ve tipini kontrol et
if ($hesap->id != "" && $hesap->tipi != 0) {
    // Verileri güvenli bir şekilde al
    $id = $gvn->rakam($_GET["id"]);
    $snc = $db->prepare("SELECT * FROM kategoriler_501 WHERE id=:ids");
    $snc->execute(['ids' => $id]);

    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ);
    } else {
        error_log("Geçersiz kategori ID: $id. Tarih: " . date("Y-m-d H:i:s"));
        die('Hata: Geçersiz kategori ID.');
    }

    if ($_POST) {
        // Verileri güvenli bir şekilde al
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $url = $gvn->PermaLink($baslik);

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik)) {
            error_log("Lütfen başlık yazınız. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // PermaLink kontrolü
        $kcvr = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik=:baslik AND id!=:id AND url=:urls AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'urls' => $url, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $tpla = $kcvr + 1;
            $url .= '_' . $tpla;
        }

        // Veritabanı güncelleme
        $sql = $db->prepare("UPDATE kategoriler_501 SET baslik=:baslik, url=:url, sira=:sira WHERE id=:id");
        $sql->execute(['baslik' => $baslik, 'url' => $url, 'sira' => $sira, 'id' => $snc->id]);

        if ($sql) {
            $fonk->ajax_tamam("Referans Kategori Güncellendi.");
        } else {
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}