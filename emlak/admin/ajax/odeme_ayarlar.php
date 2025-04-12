<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $paypal = $gvn->zrakam($_POST["paypal"]);
        $iyzico = $gvn->zrakam($_POST["iyzico"]);
        $paytr = $gvn->zrakam($_POST["paytr"]);
        $paypal_odeme_email = $gvn->html_temizle($_POST["paypal_odeme_email"]);
        $iyzico_key = $gvn->html_temizle($_POST["iyzico_key"]);
        $iyzico_secret_key = $gvn->html_temizle($_POST["iyzico_secret_key"]);
        $paytr_magaza_no = $gvn->html_temizle($_POST["paytr_magaza_no"]);
        $paytr_magaza_key = $gvn->html_temizle($_POST["paytr_magaza_key"]);
        $paytr_magaza_salt = $gvn->html_temizle($_POST["paytr_magaza_salt"]);
        $hesap_numaralari = $_POST["hesap_numaralari"];

        // İki ödeme sistemi kontrolü
        if ($iyzico == 1 && $paytr == 1) {
            error_log("İki ödeme sistemi aynı anda kullanılamaz. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("İki ödeme sistemi aynı anda kullanılamaz."));
        }

        // Hesap numaraları güncelleme
        $guncelle0 = $db->prepare("UPDATE ayarlar_501 SET hesap_numaralari=? WHERE dil=?");
        $guncelle0->execute([$hesap_numaralari, $dil]);

        // Ödeme ayarları güncelleme
        $guncelle = $db->prepare("UPDATE gayarlar_501 SET paytr=?, paypal=?, iyzico=?, iyzico_key=?, iyzico_secret_key=?, paytr_magaza_no=?, paytr_magaza_key=?, paytr_magaza_salt=?, paypal_odeme_email=?");
        $guncelle->execute([$paytr, $paypal, $iyzico, $iyzico_key, $iyzico_secret_key, $paytr_magaza_no, $paytr_magaza_key, $paytr_magaza_salt, $paypal_odeme_email]);

        if ($guncelle) {
            $fonk->ajax_tamam("Tahsilat Ayarları Güncellendi.");
        } else {
            error_log("Ödeme ayarları güncellenemedi. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Ödeme ayarları güncellenemedi.');
        }
    }
}