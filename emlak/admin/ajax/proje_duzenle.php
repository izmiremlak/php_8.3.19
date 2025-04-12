<?php
// Kullanıcı kimliğini ve tipini kontrol et
if ($hesap->id != "" && $hesap->tipi != 0) {
    // Verileri güvenli bir şekilde al
    $id = $gvn->rakam($_GET["id"]);
    $snc = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND id=:ids");
    $snc->execute(['ids' => $id]);

    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ);
    } else {
        error_log("Geçersiz proje ID: $id. Tarih: " . date("Y-m-d H:i:s"));
        die('Hata: Geçersiz proje ID.');
    }

    $galeri = $_GET["galeri"];
    if ($galeri == 1) {
        if ($_FILES) {
            $resim1tmp = $_FILES['file']["tmp_name"];
            $resim1nm = $_FILES['file']["name"];
            $exmd = strtolower(substr(md5(uniqid(rand())), 0, 5));
            $randnm = $snc->url . "-" . $exmd . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'file', $randnm, '../uploads', $gorsel_boyutlari['projeler']['resim1']['thumb_x'], $gorsel_boyutlari['projeler']['resim1']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'file', $randnm, '../uploads', $gorsel_boyutlari['projeler']['resim1']['orjin_x'], $gorsel_boyutlari['projeler']['resim1']['orjin_y']);
            $db->query("INSERT INTO galeri_foto SET site_id_555=501, sayfa_id='" . $snc->id . "', resim='" . $resim . "', dil='" . $dil . "' ");
        }
        die();
    }

    if ($_POST) {
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
        $kcvr = $db->prepare("SELECT id FROM sayfalar WHERE site_id_555=501 AND baslik=:baslik AND id!=:id AND url=:urls AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'urls' => $url, 'dil' => $dil]);
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
            $avgn = $db->prepare("UPDATE sayfalar SET resim=:image WHERE site_id_555=501 AND id=:id");
            $avgn->execute(['image' => $resim, 'id' => $snc->id]);
            if ($avgn) {
                $fonk->ajax_tamam('Resim Güncellendi');
                ?><script type="text/javascript">
                $(document).ready(function(){
                    $('#resim_src').attr("src","../uploads/thumb/<?= htmlspecialchars($resim); ?>");
                });
                </script><?php
            }
        }

        // Resim2 yükleme işlemi
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];
        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['projeler']['resim2']['thumb_x'], $gorsel_boyutlari['projeler']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['projeler']['resim2']['orjin_x'], $gorsel_boyutlari['projeler']['resim2']['orjin_y']);
            $avgn = $db->prepare("UPDATE sayfalar SET resim2=:image WHERE site_id_555=501 AND id=:id");
            $avgn->execute(['image' => $resim2, 'id' => $snc->id]);
            if ($avgn) {
                $fonk->ajax_tamam('Resim Güncellendi');
                ?><script type="text/javascript">
                $(document).ready(function(){
                    $('#resim2_src').attr("src","../uploads/thumb/<?= htmlspecialchars($resim2); ?>");
                });
                </script><?php
            }
        }

        // Veritabanı güncelleme
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $sql = $db->prepare("UPDATE sayfalar SET baslik=:baslik, url=:url, icerik=:icerik, title=:title, keywords=:keywords, description=:description, onecikan=:onecikan, kategori_id=:kategori, kisa_icerik=:kisa_icerik, turu=:turu, danisman_id=:danisman, maps=:maps WHERE site_id_555=501 AND id=".$snc->id);
            $sql->execute([
                'baslik' => $baslik,
                'url' => $url,
                'icerik' => $icerik,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'onecikan' => $onecikan,
                'kategori' => $kategori_id,
                'kisa_icerik' => $kisa_icerik,
                'turu' => $turu,
                'danisman' => $danisman_id,
                'maps' => $maps,
                'id' => $snc->id
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo("Bir hata oluştu: " . $e->getMessage());
            die();
        }

        if ($sql) {
            $fonk->ajax_tamam("Proje Güncellendi.");
        } else {
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}