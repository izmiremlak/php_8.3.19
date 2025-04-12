<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $ulke_id = $gvn->zrakam($_POST["ulke_id"]);
        $il = $gvn->zrakam($_POST["il"]);
        $ilce = $gvn->zrakam($_POST["ilce"]);
        $mahalle = $gvn->zrakam($_POST["mahalle"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $emlak_durum = $gvn->html_temizle($_POST["emlak_durum"]);

        // Boşluk kontrolü
        if ($il == 0 || $fonk->bosluk_kontrol($emlak_durum)) {
            error_log("Lütfen il ve Emlak durum alanlarını seçiniz. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_uyari("Lütfen il ve Emlak durum alanlarını seçiniz"));
        }

        // Slug oluşturma
        $full_slug = "";
        if ($il != 0) {
            $ilim = $db->query("SELECT slug FROM il WHERE id=$il")->fetch(PDO::FETCH_OBJ);
            $slug = $ilim->slug;
            $full_slug .= "-$slug";
        }
        if ($ilce != 0) {
            $ilcem = $db->query("SELECT slug FROM ilce WHERE id=$ilce")->fetch(PDO::FETCH_OBJ);
            $slug = $ilcem->slug;
            $full_slug .= "-$slug";
        }
        if ($mahalle != 0) {
            $mahallem = $db->query("SELECT slug FROM mahalle_koy WHERE id=$mahalle")->fetch(PDO::FETCH_OBJ);
            $slug = $mahallem->slug;
            $full_slug .= "-$slug";
        }

        $full_slug = trim($full_slug, "-");
        if ($full_slug == '') {
            $full_slug = md5(time());
        } else {
            $full_slug .= "-" . time();
        }

        // Resim yükleme işlemleri
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];

        if ($resim1tmp != "") {
            $randnm = $full_slug . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', $gorsel_boyutlari['referanslar']['thumb_x'], $gorsel_boyutlari['referanslar']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['referanslar']['orjin_x'], $gorsel_boyutlari['referanslar']['orjin_y']);
        }

        // Veritabanına ekleme
        $ekle = $db->prepare("INSERT INTO sehirler_501 SET ulke_id=?, il=?, ilce=?, mahalle=?, sira=?, resim=?, dil=?, emlak_durum=?");
        $ekle->execute([$ulke_id, $il, $ilce, $mahalle, $sira, $resim, $dil, $emlak_durum]);

        if ($ekle) {
            $fonk->ajax_tamam("Başarıyla Eklendi.");
            $fonk->yonlendir("index.php?p=sehirler", 3000);
        }
    }
}