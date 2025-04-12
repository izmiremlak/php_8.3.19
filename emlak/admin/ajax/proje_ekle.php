<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $tipi = 5;
        $tarih = $fonk->datetime();
        $ekleyen = $hesap->id;

        // Verileri güvenli bir şekilde al
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $kisa_icerik = $gvn->html_temizle($_POST["kisa_icerik"]);
        $turu = $gvn->zrakam($_POST["turu"]);
        $onecikan = $gvn->zrakam($_POST["onecikan"]);
        $kategori_id = $gvn->zrakam($_POST["kategori_id"]);
        $danisman_id = $gvn->zrakam($_POST["danisman_id"]);
        $icerik = $_POST["icerik"];
        $url = $gvn->PermaLink($baslik);
        $title = $gvn->html_temizle($_POST["title"]);
        $keywords = $gvn->html_temizle($_POST["keywords"]);
        $description = $gvn->html_temizle($_POST["description"]);
        $maps = $gvn->html_temizle($_POST["maps"]);

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik)) {
            error_log("Lütfen başlık yazınız. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // PermaLink kontrolü
        $kcvr = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND baslik=:baslik AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $tpla = $kcvr + 1;
            $url .= '_' . $tpla;
        }

        // Resim1 yükleme işlemi
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];
        if ($resim1tmp != "") {
            $randnm = $url . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['projeler']['resim1']['thumb_x'], $gorsel_boyutlari['projeler']['resim1']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['projeler']['resim1']['orjin_x'], $gorsel_boyutlari['projeler']['resim1']['orjin_y']);
        }

        // Resim2 yükleme işlemi
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];
        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['projeler']['resim2']['thumb_x'], $gorsel_boyutlari['projeler']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['projeler']['resim2']['orjin_x'], $gorsel_boyutlari['projeler']['resim2']['orjin_y']);
        }

        // Veritabanı güncelleme
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $db->prepare("INSERT INTO sayfalar SET site_id_555=501, dil=:dil, tipi=:tipi, baslik=:baslik, url=:url, resim=:resim, resim2=:resim2, icerik=:icerik, tarih=:tarih, title=:title, keywords=:keywords, description=:description, ekleyen=:ekleyen, onecikan=:onecikan, kategori=:kategori, kisa_icerik=:kisa_icerik, turu=:turu, danisman=:danisman, maps=:maps");
            $sql->execute([
                'dil' => $dil,
                'tipi' => $tipi,
                'baslik' => $baslik,
                'url' => $url,
                'resim' => $resim,
                'resim2' => $resim2,
                'icerik' => $icerik,
                'tarih' => $tarih,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'ekleyen' => $ekleyen,
                'onecikan' => $onecikan,
                'kategori' => $kategori_id,
                'kisa_icerik' => $kisa_icerik,
                'turu' => $turu,
                'danisman' => $danisman_id,
                'maps' => $maps
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo("Bir hata oluştu: " . $e->getMessage());
            die();
        }

        if ($sql) {
            $fonk->ajax_tamam("Proje Eklendi.");
            $fonk->yonlendir("index.php?p=projeler", 3000);
        } else {
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}