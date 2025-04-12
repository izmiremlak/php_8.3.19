<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $mparola = $gvn->html_temizle($_POST["mparola"]);
        $yparola = $gvn->html_temizle($_POST["yparola"]);
        $ytparola = $gvn->html_temizle($_POST["ytparola"]);

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($mparola) || $fonk->bosluk_kontrol($yparola) || $fonk->bosluk_kontrol($ytparola)) {
            error_log("Lütfen Boş Alan Bırakmayın! Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_uyari("Lütfen Boş Alan Bırakmayın!"));
        }

        // Mevcut parola kontrolü
        if ($mparola != $hesap->parola) {
            error_log("Mevcut Parolanızı Yanlış Yazdınız! Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Mevcut Parolanızı Yanlış Yazdınız!"));
        }

        // Yeni parola tekrar kontrolü
        if ($ytparola != $yparola) {
            error_log("Yeni Parola Tekrarı Hatalı Yazdınız! Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Yeni Parola Tekrarı Hatalı Yazdınız!"));
        }

        // Parola güncelleme
        $guncelle = $db->prepare("UPDATE hesaplar SET parola=:yparola WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:acid");
        $guncelle->execute(['yparola' => $yparola, 'acid' => $hesap->id]);

        if ($guncelle) {
            $fonk->ajax_tamam("Hesap Parolanız Güncellendi.");
            $_SESSION["acpw"] = $yparola;

            if ($ck_acpw != "") {
                $login_secret = $fonk->login_secret_key($hesap->id, $yparola);
                setcookie("acid", $hesap->id, time() + 60 * 60 * 24 * 30);
                setcookie("acpw", $yparola, time() + 60 * 60 * 24 * 30);
                setcookie("acsecret", $login_secret, time() + 60 * 60 * 24 * 30);
                $db->query("UPDATE hesaplar SET login_secret='$login_secret' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $hesap->id);
            }
        } else {
            error_log("Bir hata oluştu. Şifre Değiştirilemiyor. Tarih: " . date("Y-m-d H:i:s"));
            $fonk->ajax_hata("Bir hata oluştu. Şifre Değiştirilemiyor.");
        }
    }
}