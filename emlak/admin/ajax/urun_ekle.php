<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $tipi = 4;
        $tarih = $fonk->datetime();
        $ekleyen = $hesap->id;
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $urun_kodu = $gvn->html_temizle($_POST["urun_kodu"]);
        $fiyat = $gvn->html_temizle($_POST["fiyat"]);
        $kdv = $gvn->html_temizle($_POST["kdv"]);
        $stok = $gvn->zrakam($_POST["stok"]);
        $onecikan = $gvn->zrakam($_POST["onecikan"]);
        $kategori_id = $gvn->zrakam($_POST["kategori_id"]);
        $icerik = $_POST["icerik"];
        $url = $gvn->PermaLink($baslik);
        $title = $gvn->html_temizle($_POST["title"]);
        $keywords = $gvn->html_temizle($_POST["keywords"]);
        $description = $gvn->html_temizle($_POST["description"]);
        $kisa_icerik = $gvn->html_temizle($_POST["kisa_icerik"]);

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik)) {
            error_log("Lütfen başlık yazınız. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // PermaLink kontrolü
        $kcvr = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND baslik=:baslik AND dil='" . $dil . "'");
        $kcvr->execute(['baslik' => $baslik]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $tpla = $kcvr + 1;
            $url .= '_' . $tpla;
        }

        // Resim yükleme
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];

        if ($resim1tmp != "") {
            $randnm = $url . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['urunler']['resim1']['thumb_x'], $gorsel_boyutlari['urunler']['resim1']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['urunler']['resim1']['orjin_x'], $gorsel_boyutlari['urunler']['resim1']['orjin_y']);
        }

        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['urunler']['resim2']['thumb_x'], $gorsel_boyutlari['urunler']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['urunler']['resim2']['orjin_x'], $gorsel_boyutlari['urunler']['resim2']['orjin_y']);
        }

        // Ürün ekleme
        $sql = $db->prepare("INSERT INTO sayfalar SET site_id_555=501, dil=:dil, tipi=:tipi, baslik=:baslik, url=:url, resim=:resim, resim2=:resim2, icerik=:icerik, tarih=:tarih, title=:title, keywords=:keywords, description=:description, kisa_icerik=:kisa_icerik, urun_kodu=:urun_kodu, fiyat=:fiyat, kdv=:kdv, stok=:stok, onecikan=:onecikan, kategori_id=:kategori_id, ekleyen=:ekleyen");
        $sql->execute(['dil' => $dil, 'tipi' => $tipi, 'baslik' => $baslik, 'url' => $url, 'resim' => $resim, 'resim2' => $resim2, 'icerik' => $icerik, 'tarih' => $tarih, 'title' => $title, 'keywords' => $keywords, 'description' => $description, 'kisa_icerik' => $kisa_icerik, 'urun_kodu' => $urun_kodu, 'fiyat' => $fiyat, 'kdv' => $kdv, 'stok' => $stok, 'onecikan' => $onecikan, 'kategori_id' => $kategori_id, 'ekleyen' => $ekleyen]);

        if ($sql) {
            $fonk->ajax_tamam("Ürün Eklendi.");
            $fonk->yonlendir("index.php?p=urunler", 1500);
        } else {
            error_log("Ürün eklenemedi. Tarih: " . date("Y-m-d H:i:s"));
            $fonk->ajax_hata("Ürün eklenemedi.");
        }
    }
}