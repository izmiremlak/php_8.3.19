<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $konu = $gvn->html_temizle($_POST["konu"]);
        $gonderilenler = $_POST["gonderilenler"];
        $mesaj = $_POST["mesaj"];

        // Boşluk kontrolü
        if (empty($konu) || empty($gonderilenler) || empty($mesaj)) {
            error_log("Lütfen boş bırakma! Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata('Lütfen boş bırakma!'));
        }

        $gidecekler = explode("\n", $gonderilenler);
        $i = 0;

        foreach ($gidecekler as $eposta) {
            if (!empty($eposta)) {
                $xgndr = $fonk->mail_gonder($konu, $eposta, $mesaj);
                if ($xgndr) {
                    $i++;
                } else {
                    error_log("Mail gönderilemedi: $eposta. Tarih: " . date("Y-m-d H:i:s"));
                }
            }
        }

        $gonder = true;

        if ($gonder) {
            $fonk->ajax_tamam("İşlem Başarıyla Gerçekleşti.");
            echo 'Toplam ' . $i . ' adet kişiye mail gönderildi.';
        } else {
            error_log("Mail gönderilemiyor. Tarih: " . date("Y-m-d H:i:s"));
            $fonk->ajax_hata("Mail gönderilemiyor.");
        }
    } else {
        error_log("Geçersiz kullanıcı veya yetki. Tarih: " . date("Y-m-d H:i:s"));
        $fonk->ajax_hata("Geçersiz kullanıcı veya yetki.");
    }
}