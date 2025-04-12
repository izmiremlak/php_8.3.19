<?php
// Kullanıcı kimliğini ve tipini kontrol et
if ($hesap->id != "" && $hesap->tipi != 0) {
    // POST verilerinin kontrolü
    if ($_POST) {
        // Verileri güvenli bir şekilde al
        $lokasyon = $gvn->html_temizle($_POST["lokasyon"]);
        $sira = $gvn->zrakam($_POST["sira"]);
        $adres = $gvn->html_temizle($_POST["adres"]);
        $telefon = $gvn->html_temizle($_POST["telefon"]);
        $gsm = $gvn->html_temizle($_POST["gsm"]);
        $email = $gvn->html_temizle($_POST["email"]);
        $google_maps = $gvn->html_temizle($_POST["google_maps"]);

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($lokasyon)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen tüm alanları eksiksiz doldurun."));
        }

        try {
            // Şube ekleme
            $ekle = $db->prepare("INSERT INTO subeler_bayiler_501 SET turu=?, lokasyon=?, sira=?, adres=?, telefon=?, gsm=?, email=?, google_maps=?, dil=?");
            $ekle->execute(['0', $lokasyon, $sira, $adres, $telefon, $gsm, $email, $google_maps, $dil]);

            $fonk->ajax_tamam("Şube Eklendi.");
            $fonk->yonlendir("index.php?p=subeler", 1500);
        } catch (PDOException $e) {
            error_log("Şube eklenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            $fonk->ajax_hata($e->getMessage());
        }
    }
}