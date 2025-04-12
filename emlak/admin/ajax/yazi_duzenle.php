<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz sayfa ID. Tarih: " . date("Y-m-d H:i:s"));
            die();
        }

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
        $kcvr = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND baslik=:baslik AND id!=:id AND url=:urls AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'urls' => $url, 'dil' => $dil]);
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

            // Veritabanı işlevi
            try {
                $avgn = $db->prepare("UPDATE sayfalar SET resim=:image WHERE site_id_555=501 AND id=:id");
                $avgn->execute(['image' => $resim, 'id' => $snc->id]);

                if ($avgn) {
                    $fonk->ajax_tamam('Resim Güncellendi');
                    ?><script type="text/javascript">
                    $(document).ready(function () {
                        $('#resim_src').attr("src", "../uploads/thumb/<?=$resim;?>");
                    });
                    </script><?php
                }
            } catch (PDOException $e) {
                error_log("Resim güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
                die($e->getMessage());
            }
        }

        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['yazilar']['resim2']['thumb_x'], $gorsel_boyutlari['yazilar']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['yazilar']['resim2']['orjin_x'], $gorsel_boyutlari['yazilar']['resim2']['orjin_y']);

            // Veritabanı işlevi
            try {
                $avgn = $db->prepare("UPDATE sayfalar SET resim2=:image WHERE site_id_555=501 AND id=:id");
                $avgn->execute(['image' => $resim2, 'id' => $snc->id]);

                if ($avgn) {
                    $fonk->ajax_tamam('Resim Güncellendi');
                    ?><script type="text/javascript">
                    $(document).ready(function () {
                        $('#resim2_src').attr("src", "../uploads/thumb/<?=$resim2;?>");
                    });
                    </script><?php
                }
            } catch (PDOException $e) {
                error_log("Resim güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
                die($e->getMessage());
            }
        }

        // Sayfa bilgilerini güncelle
        try {
            $sql = $db->prepare("UPDATE sayfalar SET baslik=:baslik, url=:url, icerik=:icerik, title=:title, keywords=:keywords, description=:description WHERE site_id_555=501 AND id=:id");
            $sql->execute([
                'baslik' => $baslik,
                'url' => $url,
                'icerik' => $icerik,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'id' => $snc->id
            ]);

            if ($sql) {
                $fonk->ajax_tamam("Yazı Güncellendi.");
            } else {
                $fonk->ajax_hata("Bir hata oluştu.");
            }
        } catch (PDOException $e) {
            error_log("Sayfa bilgisi güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }
    }
}