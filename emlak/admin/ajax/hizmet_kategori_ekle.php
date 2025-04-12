<?php
// POST isteği olup olmadığını kontrol et
if ($_POST) {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id != "" AND $hesap->tipi != 0) {
        $tarih = $fonk->datetime();

        // POST verilerini güvenli bir şekilde al ve temizle
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $icerik = $_POST["icerik"];
        $url = $gvn->PermaLink($baslik);
        $title = $gvn->html_temizle($_POST["title"]);
        $keywords = $gvn->html_temizle($_POST["keywords"]);
        $description = $gvn->html_temizle($_POST["description"]);

        // Başlık kontrolü
        if ($fonk->bosluk_kontrol($baslik) == true) {
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

        // Kategori ekleme
        try {
            $sql = $db->prepare("INSERT INTO kategoriler_501 SET dil=:dil, baslik=:baslik, url=:url, tarih=:tarih, title=:title, keywords=:keywords, description=:description, sira=:sira, tipi=4");
            $sql->execute([
                'dil' => $dil,
                'baslik' => $baslik,
                'url' => $url,
                'tarih' => $tarih,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'sira' => $sira
            ]);

            $fonk->ajax_tamam("Hizmet Kategori Eklendi.");
            $fonk->yonlendir("index.php?p=hizmet_kategoriler", 3000);
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
        }
    }
}