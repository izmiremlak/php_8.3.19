<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $tarih = $fonk->datetime();
        
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
        $kcvr = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik=:baslik AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $tpla = $kcvr + 1;
            $url .= '_' . $tpla;
        }

        // Veritabanı ekleme
        $sql = $db->prepare("INSERT INTO kategoriler_501 SET dil=:dil, baslik=:baslik, url=:url, tarih=:tarih, sira=:sira, tipi=:tipi");
        $sql->execute(['dil' => $dil, 'baslik' => $baslik, 'url' => $url, 'tarih' => $tarih, 'sira' => $sira, 'tipi' => 3]);

        if ($sql) {
            $fonk->ajax_tamam("Referans Kategori Eklendi.");
            $fonk->yonlendir("index.php?p=referans_kategoriler", 3000);
        } else {
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}