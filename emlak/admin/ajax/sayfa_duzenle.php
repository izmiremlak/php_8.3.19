<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz sayfa ID: $id. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Geçersiz sayfa ID.');
        }

        // POST verilerini güvenli bir şekilde al
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
        $kcvr = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND baslik=:baslik AND id!=:id AND url=:urls AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'urls' => $url, 'dil' => $dil]);
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

            // Veritabanına resim güncelleme
            $avgn = $db->prepare("UPDATE sayfalar SET resim=:image WHERE site_id_555=501 AND id=:id");
            $avgn->execute(['image' => $resim, 'id' => $snc->id]);
            if ($avgn) {
                $fonk->ajax_tamam('Resim Güncellendi');
                ?><script type="text/javascript">
                $(document).ready(function(){
                    $('#resim_src').attr("src","../uploads/thumb/<?=$resim;?>");
                });
                </script><?php
            }
        }

        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['sayfalar']['resim2']['thumb_x'], $gorsel_boyutlari['sayfalar']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['sayfalar']['resim2']['orjin_x'], $gorsel_boyutlari['sayfalar']['resim2']['orjin_y']);

            // Veritabanına resim güncelleme
            $avgn = $db->prepare("UPDATE sayfalar SET resim2=:image WHERE site_id_555=501 AND id=:id");
            $avgn->execute(['image' => $resim2, 'id' => $snc->id]);
            if ($avgn) {
                $fonk->ajax_tamam('Resim Güncellendi');
                ?><script type="text/javascript">
                $(document).ready(function(){
                    $('#resim2_src').attr("src","../uploads/thumb/<?=$resim2;?>");
                });
                </script><?php
            }
        }

        // Sayfa güncelleme
        $sql = $db->prepare("UPDATE sayfalar SET baslik=:baslik, url=:url, icerik=:icerik, title=:title, keywords=:keywords, description=:description WHERE site_id_555=501 AND id=:id");
        $sql->execute(['baslik' => $baslik, 'url' => $url, 'icerik' => $icerik, 'title' => $title, 'keywords' => $keywords, 'description' => $description, 'id' => $snc->id]);

        if ($sql) {
            $fonk->ajax_tamam("Sayfa Güncellendi.");
        } else {
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}