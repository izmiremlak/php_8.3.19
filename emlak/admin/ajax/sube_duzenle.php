<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM subeler_bayiler_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz şube ID. Tarih: " . date("Y-m-d H:i:s"));
            die();
        }

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
            // Şube güncelleme
            $ekle = $db->prepare("UPDATE subeler_bayiler_501 SET lokasyon=?, sira=?, adres=?, telefon=?, gsm=?, email=?, google_maps=? WHERE id=?");
            $ekle->execute([$lokasyon, $sira, $adres, $telefon, $gsm, $email, $google_maps, $id]);
            $fonk->ajax_tamam("Şube Güncellendi.");
        } catch (PDOException $e) {
            error_log("Şube güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            $fonk->ajax_hata($e->getMessage());
        }
    } else {
        error_log("Geçersiz kullanıcı veya yetki. Tarih: " . date("Y-m-d H:i:s"));
        $fonk->ajax_hata("Geçersiz kullanıcı veya yetki.");
    }
}