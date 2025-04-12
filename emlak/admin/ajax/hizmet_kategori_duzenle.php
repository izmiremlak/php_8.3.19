<?php
// POST isteği olup olmadığını kontrol et
if ($_POST) {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id != "" AND $hesap->tipi != 0) {
        // GET verisini güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM kategoriler_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        // Sonuçları kontrol et
        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

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
        $kcvr = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik=:baslik AND id!=:id AND url=:urls AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'urls' => $url, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $tpla = $kcvr + 1;
            $url .= '_' . $tpla;
        }

        // Kategori güncelleme
        try {
            $sql = $db->prepare("UPDATE kategoriler_501 SET baslik=:baslik, url=:url, title=:title, keywords=:keywords, description=:description, sira=:sira WHERE id=:id");
            $sql->execute([
                'baslik' => $baslik,
                'url' => $url,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'sira' => $sira,
                'id' => $snc->id
            ]);

            $fonk->ajax_tamam("Hizmet Kategori Güncellendi.");
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
        }
    }
}