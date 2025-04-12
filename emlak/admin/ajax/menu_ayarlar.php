<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $otoug = $gvn->zrakam($_POST["otoug"]);
        $otoh = $gvn->zrakam($_POST["otoh"]);
        $otoug_sira = $gvn->zrakam($_POST["otoug_sira"]);
        $otoh_sira = $gvn->zrakam($_POST["otoh_sira"]);

        // SMS firma dosyasını güncelle
        if ($sms_firma != '') {
            if (@file_put_contents("../sms_firma.txt", $sms_firma) === false) {
                error_log("SMS firma dosyası yazılamadı. Tarih: " . date("Y-m-d H:i:s"));
            }
        }

        // Menü ayarlarını güncelleme sorgusu
        $guncelle = $db->prepare("UPDATE gayarlar_501 SET otoug=?, otoh=?, otoug_sira=?, otoh_sira=?");
        $guncelle->execute([$otoug, $otoh, $otoug_sira, $otoh_sira]);

        if ($guncelle) {
            $fonk->ajax_tamam("Menü Ayarları Güncellendi.");
        } else {
            error_log("Menü ayarları güncellenemedi. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Menü ayarları güncellenemedi.');
        }
    }
}