<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $adsoyad = $gvn->html_temizle($_POST["adsoyad"]);
        $firma = $gvn->html_temizle($_POST["firma"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $mesaj = $_POST["mesaj"];
        $tarih = $fonk->datetime();

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
        }

        // Yorum ekleme
        $ekle = $db->prepare("INSERT INTO musteri_yorumlar SET adsoyad=?, mesaj=?, sira=?, dil=?, tarih=?, resim=?, firma=?");
        $ekle->execute([$adsoyad, $mesaj, $sira, $dil, $tarih, $resim, $firma]);

        if ($ekle) {
            $fonk->ajax_tamam("İşlem Tamamlandı.");
            $fonk->yonlendir("index.php?p=musteri_yorumlar", 3000);
        } else {
            error_log("Yorum eklenemedi. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Yorum eklenemedi.');
        }
    }
}