<?php
// POST isteği olup olmadığını kontrol et
if ($_POST) {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id != "" AND $hesap->tipi != 0) {
        $tipi = 2;
        $tarih = $fonk->datetime();
        $ekleyen = $hesap->id;

        // POST verilerini güvenli bir şekilde al ve temizle
        $baslik = $gvn->html_temizle($_POST["baslik"]);
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
        $kcvr = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND baslik=:baslik AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $tpla = $kcvr + 1;
            $url .= '_' . $tpla;
        }

        // Resim yükleme işlemi
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];

        if ($resim1tmp != "") {
            $randnm = $url . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['haber_ve_duyurular']['resim1']['thumb_x'], $gorsel_boyutlari['haber_ve_duyurular']['resim1']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['haber_ve_duyurular']['resim1']['orjin_x'], $gorsel_boyutlari['haber_ve_duyurular']['resim1']['orjin_y']);
        }

        // İkinci resim yükleme işlemi
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];

        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['haber_ve_duyurular']['resim2']['thumb_x'], $gorsel_boyutlari['haber_ve_duyurular']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['haber_ve_duyurular']['resim2']['orjin_x'], $gorsel_boyutlari['haber_ve_duyurular']['resim2']['orjin_y']);
        }

        // Veritabanına ekleme işlemi
        try {
            $sql = $db->prepare("INSERT INTO sayfalar SET site_id_555=501, dil=:dil, tipi=:tipi, baslik=:baslik, url=:url, resim=:resim, resim2=:resim2, icerik=:icerik, tarih=:tarih, title=:title, keywords=:keywords, description=:description, ekleyen=:ekleyen");
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
                'ekleyen' => $ekleyen
            ]);

            $fonk->ajax_tamam("Haberler Eklendi.");
            $fonk->yonlendir("index.php?p=haber_ve_duyurular", 3000);
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
        }
    }
}