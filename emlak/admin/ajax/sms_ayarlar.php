<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $sms_baslik = $gvn->html_temizle($_POST["sms_baslik"]);
        $sms_username = $gvn->html_temizle($_POST["sms_username"]);
        $sms_password = $gvn->html_temizle($_POST["sms_password"]);
        $rez_tel = $gvn->html_temizle($_POST["rez_tel"]);
        $sms_firma = $gvn->zrakam($_POST["sms_firma"]);

        // SMS ayarlarını güncelleme
        $guncelle = $db->prepare("UPDATE gayarlar_501 SET sms_firma=?, sms_baslik=?, sms_username=?, sms_password=?, rez_tel=?");
        $guncelle->execute([$sms_firma, $sms_baslik, $sms_username, $sms_password, $rez_tel]);

        if ($guncelle) {
            $fonk->ajax_tamam("SMS Ayarları Güncellendi.");
        } else {
            error_log("SMS ayarları güncellenemedi. Tarih: " . date("Y-m-d H:i:s"));
            $fonk->ajax_hata("SMS ayarları güncellenemedi. Lütfen tekrar deneyin.");
        }
    } else {
        error_log("Geçersiz kullanıcı veya yetki. Tarih: " . date("Y-m-d H:i:s"));
        $fonk->ajax_hata("Geçersiz kullanıcı veya yetki.");
    }
}