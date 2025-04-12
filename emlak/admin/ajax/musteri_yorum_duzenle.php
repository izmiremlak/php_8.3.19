<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM musteri_yorumlar WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz yorum ID: $id. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Geçersiz yorum ID.');
        }

        // Verileri güvenli bir şekilde al
        $adsoyad = $gvn->html_temizle($_POST["adsoyad"]);
        $firma = $gvn->html_temizle($_POST["firma"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $mesaj = $_POST["mesaj"];

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($adsoyad) || $mesaj == '') {
            error_log("Lütfen tüm alanları doldurun. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Resim yükleme işlemi
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];

        if ($resim1tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['musteri_yorumlar']['thumb_x'], $gorsel_boyutlari['musteri_yorumlar']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['musteri_yorumlar']['orjin_x'], $gorsel_boyutlari['musteri_yorumlar']['orjin_y']);

            // Veritabanı güncelleme
            $avgn = $db->prepare("UPDATE musteri_yorumlar SET resim=:image WHERE id=:id");
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

        // Yorum güncelleme
        $updt = $db->prepare("UPDATE musteri_yorumlar SET adsoyad=?, mesaj=?, sira=?, firma=? WHERE id=?");
        $updt->execute([$adsoyad, $mesaj, $sira, $firma, $id]);

        if ($updt) {
            $fonk->ajax_tamam("İşlem Tamamlandı.");
        } else {
            error_log("Yorum güncellenemedi. Yorum ID: $id. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Yorum güncellenemedi.');
        }
    }
}