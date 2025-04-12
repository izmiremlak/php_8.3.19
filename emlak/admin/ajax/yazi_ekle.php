<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $tipi = 1;
        $tarih = $fonk->datetime();
        $ekleyen = $hesap->id;
        // Verileri güvenli bir şekilde al
        $baslik = htmlspecialchars($_POST["baslik"], ENT_QUOTES, 'UTF-8');
        $icerik = $_POST["icerik"];
        $url = $gvn->PermaLink($baslik);
        $title = htmlspecialchars($_POST["title"], ENT_QUOTES, 'UTF-8');
        $keywords = htmlspecialchars($_POST["keywords"], ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($_POST["description"], ENT_QUOTES, 'UTF-8');

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik)) {
            error_log("Lütfen başlık yazınız. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // Permalink kontrolü
        $kcvr = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND baslik=:baslik AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $url .= '_' . ($kcvr + 1);
        }

        // Resim yükleme işlemleri
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];

        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];

        if ($resim1tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['yazilar']['resim1']['thumb_x'], $gorsel_boyutlari['yazilar']['resim1']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['yazilar']['resim1']['orjin_x'], $gorsel_boyutlari['yazilar']['resim1']['orjin_y']);
        }

        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['yazilar']['resim2']['thumb_x'], $gorsel_boyutlari['yazilar']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['yazilar']['resim2']['orjin_x'], $gorsel_boyutlari['yazilar']['resim2']['orjin_y']);
        }

        // Veritabanı işlevi
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

            if ($sql) {
                $fonk->ajax_tamam("Yazı Eklendi.");
                $fonk->yonlendir("index.php?p=yazilar", 3000);
            } else {
                $fonk->ajax_hata("Bir hata oluştu.");
            }
        } catch (PDOException $e) {
            error_log("Yazı eklenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }
    }
}