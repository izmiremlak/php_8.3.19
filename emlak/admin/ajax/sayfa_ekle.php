<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $tipi = 0;
        $tarih = $fonk->datetime();
        $ekleyen = $hesap->id;

        // Verileri güvenli bir şekilde al
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $icerik = $_POST["icerik"];
        $url = $gvn->PermaLink($baslik);
        $title = $gvn->html_temizle($_POST["title"]);
        $keywords = $gvn->html_temizle($_POST["keywords"]);
        $description = $gvn->html_temizle($_POST["description"]);

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

        // Resim yükleme işlemleri
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];

        if ($resim1tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['sayfalar']['resim1']['thumb_x'], $gorsel_boyutlari['sayfalar']['resim1']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['sayfalar']['resim1']['orjin_x'], $gorsel_boyutlari['sayfalar']['resim1']['orjin_y']);
        }

        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['sayfalar']['resim2']['thumb_x'], $gorsel_boyutlari['sayfalar']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['sayfalar']['resim2']['orjin_x'], $gorsel_boyutlari['sayfalar']['resim2']['orjin_y']);
        }

        // Veritabanına ekleme
        $sql = $db->prepare("INSERT INTO sayfalar SET site_id_555=501, dil=:dil, tipi=:tipi, baslik=:baslik, url=:url, resim=:resim, resim2=:resim2, icerik=:icerik, tarih=:tarih, title=:title, keywords=:keywords, description=:description, ekleyen=:ekleyen");
        $sql->execute(['dil' => $dil, 'tipi' => $tipi, 'baslik' => $baslik, 'url' => $url, 'resim' => $resim, 'resim2' => $resim2, 'icerik' => $icerik, 'tarih' => $tarih, 'title' => $title, 'keywords' => $keywords, 'description' => $description, 'ekleyen' => $ekleyen]);

        if ($sql) {
            $fonk->ajax_tamam("Sayfa Eklendi.");
            $fonk->yonlendir("index.php?p=sayfalar", 3000);
        } else {
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}