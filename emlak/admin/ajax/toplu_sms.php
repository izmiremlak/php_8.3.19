<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $gonderilenler = $_POST["gonderilenler"];
        $mesaj = $_POST["mesaj"];

        // Boşluk kontrolü
        if (empty($gonderilenler) || empty($mesaj)) {
            error_log("Lütfen boş bırakma! Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata('Lütfen boş bırakma!'));
        }

        $gidecekler = explode("\n", $gonderilenler);
        $gsmler = [];

        $i = 0;
        foreach ($gidecekler as $gsm) {
            $gsm = trim($gsm);
            if (!empty($gsm) && is_numeric($gsm)) {
                $gsm = (substr($gsm, 0, 3) == '+90') ? '0' . substr($gsm, 3, 20) : $gsm;
                $gsm = (substr($gsm, 0, 2) == '90') ? '0' . substr($gsm, 2, 20) : $gsm;
                $gsm = (substr($gsm, 0, 1) != 0) ? '0' . $gsm : $gsm;
                if (strlen($gsm) == 11) {
                    $gsmler[] = $gsm;
                }
            }
        }

        // SMS gönderme
        $gonder = $fonk->sms_gonder($gsmler, $mesaj);

        if ($gonder) {
            $fonk->ajax_tamam("Toplu SMS Başarılı bir şekilde gönderildi.", 3000);
        } else {
            error_log("SMS gönderilemiyor. Tarih: " . date("Y-m-d H:i:s"));
            $fonk->ajax_hata("İşlem Gerçekleşemiyor. Sms Gönderilemiyor.", 3000);
        }
    } else {
        error_log("Geçersiz kullanıcı veya yetki. Tarih: " . date("Y-m-d H:i:s"));
        $fonk->ajax_hata("Geçersiz kullanıcı veya yetki.");
    }
}