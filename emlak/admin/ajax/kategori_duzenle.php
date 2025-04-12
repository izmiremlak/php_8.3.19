<?php
// Verilerin POST ile gelip gelmediğini kontrol et
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {

        // GET parametresinden ID'yi güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        
        // Kategori bilgilerini veritabanından çek
        $snc = $db->prepare("SELECT * FROM kategoriler_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // Verileri güvenli bir şekilde al
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $ustu = $gvn->zrakam($_POST["ustu"]);
        $icerik = $_POST["icerik"];
        $url = $gvn->PermaLink($baslik);
        $title = $gvn->html_temizle($_POST["title"]);
        $keywords = $gvn->html_temizle($_POST["keywords"]);
        $description = $gvn->html_temizle($_POST["description"]);

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik)) {
            die($fonk->ajax_hata("Lütfen başlık yazınız."));
        }

        // PermaLink Kontrolü
        $kcvr = $db->prepare("SELECT id FROM kategoriler_501 WHERE baslik=:baslik AND id!=:id AND url=:url AND dil=:dil");
        $kcvr->execute(['baslik' => $baslik, 'id' => $snc->id, 'url' => $url, 'dil' => $dil]);
        $kcvr = $kcvr->rowCount();
        if ($kcvr > 0) {
            $tpla = $kcvr + 1;
            $url .= '_' . $tpla;
        }

        // Resim işlemleri
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];

        if ($resim1tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['kategoriler']['resim1']['thumb_x'], $gorsel_boyutlari['kategoriler']['resim1']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['kategoriler']['resim1']['orjin_x'], $gorsel_boyutlari['kategoriler']['resim1']['orjin_y']);

            // Veritabanı güncellemesi
            $avgn = $db->prepare("UPDATE kategoriler_501 SET resim=:image WHERE id=:id");
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

        if ($resim2tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim2nm);
            $resim2 = $fonk->resim_yukle(true, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['kategoriler']['resim2']['thumb_x'], $gorsel_boyutlari['kategoriler']['resim2']['thumb_y']);
            $resim2 = $fonk->resim_yukle(false, 'resim2', $randnm, '../uploads', $gorsel_boyutlari['kategoriler']['resim2']['orjin_x'], $gorsel_boyutlari['kategoriler']['resim2']['orjin_y']);

            // Veritabanı güncellemesi
            $avgn = $db->prepare("UPDATE kategoriler_501 SET resim2=:image WHERE id=:id");
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

        // Kategori güncelleme sorgusu
        $sql = $db->prepare("UPDATE kategoriler_501 SET baslik=:baslik, url=:url, icerik=:icerik, title=:title, keywords=:keywords, description=:description, sira=:sira, ustu=:ustu WHERE id=:id");
        $sql->execute([
            'baslik' => $baslik,
            'url' => $url,
            'icerik' => $icerik,
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
            'sira' => $sira,
            'ustu' => $ustu,
            'id' => $snc->id
        ]);

        if ($sql) {
            $fonk->ajax_tamam("Kategori Güncellendi.");
        } else {
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}