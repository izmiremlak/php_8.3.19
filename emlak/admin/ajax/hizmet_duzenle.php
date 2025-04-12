<?php
// POST isteği olup olmadığını kontrol et
if ($_POST) {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id != "" AND $hesap->tipi != 0) {
        // GET verisini güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND id=:ids");
        $snc->execute(['ids' => $id]);

        // Sonuçları kontrol et
        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // POST verilerini güvenli bir şekilde al ve temizle
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $icerik = $_POST["icerik"];
        $url = $gvn->PermaLink($baslik);
        $title = $gvn->html_temizle($_POST["title"]);
        $keywords = $gvn->html_temizle($_POST["keywords"]);
        $description = $gvn->html_temizle($_POST["description"]);
        $link = $gvn->html_temizle($_POST["link"]);
        $kategori_id = $gvn->zrakam($_POST["kategori_id"]);
        $kisa_icerik = $_POST["kisa_icerik"];
        $icon = $_POST["icon"];
        $anasayfa = $gvn->zrakam($_POST["anasayfa"]);

        // Başlık kontrolü
        if ($fonk->bosluk_kontrol($baslik) == true) {
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // PermaLink kontrolü
        $kcvr = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND baslik=:baslik AND id!=:id AND url=:urls AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'urls' => $url, 'dil' => $dil]);
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
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['hizmetler']['resim1']['thumb_x'], $gorsel_boyutlari['hizmetler']['resim1']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['hizmetler']['resim1']['orjin_x'], $gorsel_boyutlari['hizmetler']['resim1']['orjin_y']);

            // Veritabanını güncelle
            try {
                $avgn = $db->prepare("UPDATE sayfalar SET resim=:image WHERE site_id_555=501 AND id=:id");
                $avgn->execute(['image' => $resim, 'id' => $snc->id]);
                $fonk->ajax_tamam('Resim Güncellendi');
                echo "<script type=\"text/javascript\">
                $(document).ready(function(){
                    $('#resim_src').attr(\"src\",\"../uploads/thumb/{$resim}\");
                });
                </script>";
            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                $fonk->ajax_hata("Resim Güncellenemedi. Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
            }
        }

        // İkinci resim yükleme işlemi
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];

        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['hizmetler']['resim2']['thumb_x'], $gorsel_boyutlari['hizmetler']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['hizmetler']['resim2']['orjin_x'], $gorsel_boyutlari['hizmetler']['resim2']['orjin_y']);

            // Veritabanını güncelle
            try {
                $avgn = $db->prepare("UPDATE sayfalar SET resim2=:image WHERE site_id_555=501 AND id=:id");
                $avgn->execute(['image' => $resim2, 'id' => $snc->id]);
                $fonk->ajax_tamam('Resim Güncellendi');
                echo "<script type=\"text/javascript\">
                $(document).ready(function(){
                    $('#resim2_src').attr(\"src\",\"../uploads/thumb/{$resim2}\");
                });
                </script>";
            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                $fonk->ajax_hata("Resim Güncellenemedi. Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
            }
        }

        // Hizmetleri güncelle
        try {
            $sql = $db->prepare("UPDATE sayfalar SET baslik=:baslik, url=:url, icerik=:icerik, title=:title, keywords=:keywords, description=:description, anasayfa=:anasayfa, icon=:icon, kisa_icerik=:kisa_icerik, link=:link, kategori_id=:kategori_id WHERE site_id_555=501 AND id=:id");
            $sql->execute([
                'baslik' => $baslik,
                'url' => $url,
                'icerik' => $icerik,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'anasayfa' => $anasayfa,
                'icon' => $icon,
                'kisa_icerik' => $kisa_icerik,
                'link' => $link,
                'kategori_id' => $kategori_id,
                'id' => $snc->id
            ]);

            $fonk->ajax_tamam("Hizmet Güncellendi.");
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
        }
    }
}