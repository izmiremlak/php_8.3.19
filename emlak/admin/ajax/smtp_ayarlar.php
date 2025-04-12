<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $smtp_fromname = $gvn->html_temizle($_POST["smtp_fromname"]);
        $smtp_host = $gvn->html_temizle($_POST["smtp_host"]);
        $smtp_port = $gvn->zrakam($_POST["smtp_port"]);
        $smtp_protokol = $gvn->html_temizle($_POST["smtp_protokol"]);
        $smtp_username = $gvn->html_temizle($_POST["smtp_username"]);
        $smtp_password = $gvn->html_temizle($_POST["smtp_password"]);

        // SMTP ayarlarını güncelleme
        $guncelle = $db->prepare("UPDATE gayarlar_501 SET smtp_host=:host, smtp_port=:port, smtp_protokol=:protokol, smtp_username=:username, smtp_password=:password, smtp_fromname=:fromname");
        $guncelle->execute(['host' => $smtp_host, 'port' => $smtp_port, 'protokol' => $smtp_protokol, 'username' => $smtp_username, 'password' => $smtp_password, 'fromname' => $smtp_fromname]);

        if ($guncelle) {
            $fonk->ajax_tamam("SMTP Ayarları Güncellendi.");
        } else {
            error_log("SMTP ayarları güncellenemedi. Tarih: " . date("Y-m-d H:i:s"));
            $fonk->ajax_hata("SMTP ayarları güncellenemedi. Lütfen tekrar deneyin.");
        }
    } else {
        error_log("Geçersiz kullanıcı veya yetki. Tarih: " . date("Y-m-d H:i:s"));
        $fonk->ajax_hata("Geçersiz kullanıcı veya yetki.");
    }
}