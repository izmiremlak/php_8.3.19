<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM referanslar_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz referans ID: $id. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Geçersiz referans ID.');
        }

        // POST verilerini güvenli bir şekilde al
        $adi = $gvn->html_temizle($_POST["adi"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $website = $gvn->html_temizle($_POST["website"]);
        $kategori_id = $gvn->zrakam($_POST["kategori_id"]);

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($adi)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Resim yükleme işlemleri
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];

        if ($resim1tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['referanslar']['thumb_x'], $gorsel_boyutlari['referanslar']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['referanslar']['orjin_x'], $gorsel_boyutlari['referanslar']['orjin_y']);

            // Veritabanına resim güncelleme
            $avgn = $db->prepare("UPDATE referanslar_501 SET resim=:image WHERE id=:id");
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

        // Referans güncelleme
        $updt = $db->prepare("UPDATE referanslar_501 SET website=:web, adi=:baslik, sira=:sira, kategori_id=:kategori_id WHERE id=:ids");
        $updt->execute(['web' => $website, 'baslik' => $adi, 'sira' => $sira, 'kategori_id' => $kategori_id, 'ids' => $snc->id]);

        if ($updt) {
            $fonk->ajax_tamam("Başarıyla Güncellendi.");
        }
    }
}