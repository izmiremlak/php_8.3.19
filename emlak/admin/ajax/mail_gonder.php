<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $konu = $gvn->html_temizle($_POST["konu"]);
        $kime = $gvn->html_temizle($_POST["kime"]);
        $mesaj = $_POST["mesaj"];

        // Boşluk ve e-posta kontrolü
        if ($konu == '' || $kime == '' || $mesaj == '') {
            error_log("Tüm alanlar doldurulmadı. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Tüm alanları doldurunuz."));
        } elseif ($gvn->eposta_kontrol($kime) == false) {
            error_log("Geçersiz e-posta adresi: $kime. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Geçersiz bir e-posta adresi girdiniz."));
        }

        $message = $mesaj;

        // Mail gönderimi
        $gonder = $fonk->mail_gonder($konu, $kime, $message);

        if ($gonder) {
            ?><script>$('#MailGonder').modal('hide');</script><?php
            $fonk->ajax_tamam("Mail Başarıyla Gönderildi.");
        } else {
            error_log("Mail gönderilemedi. Konu: $konu, Kime: $kime. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Mail Gönderilemedi bir hata oluştu."));
        }
    }
}