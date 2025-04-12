<?php
// POST iste�i olup olmad���n� kontrol et
if ($_POST) {
    // Kullan�c�n�n giri� yap�p yapmad���n� ve do�ru tipte olup olmad���n� kontrol et
    if ($hesap->id != "" AND $hesap->tipi != 0) {
        // GET verisini g�venli bir �ekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM kategoriler_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        // Sonu�lar� kontrol et
        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // POST verilerini g�venli bir �ekilde al ve temizle
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $icerik = $_POST["icerik"];
        $url = $gvn->PermaLink($baslik);
        $title = $gvn->html_temizle($_POST["title"]);
        $keywords = $gvn->html_temizle($_POST["keywords"]);
        $description = $gvn->html_temizle($_POST["description"]);

        // Ba�l�k kontrol�
        if ($fonk->bosluk_kontrol($baslik) == true) {
            die($fonk->ajax_hata("L�tfen ba�l�k yaz�n�z."));
        }

        // PermaLink kontrol�
        $kcvr = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik=:baslik AND id!=:id AND url=:urls AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'urls' => $url, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $tpla = $kcvr + 1;
            $url .= '_' . $tpla;
        }

        // Kategori g�ncelleme
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

            $fonk->ajax_tamam("Hizmet Kategori G�ncellendi.");
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata olu�tu: " . htmlspecialchars($e->getMessage()));
        }
    }
}