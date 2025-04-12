<?php

// POST isteği olup olmadığını kontrol et
if ($_POST) {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id != "" AND $hesap->tipi != 0) {
        // Gönderilen veriyi al
        $data = $_POST["gonderilenler"];

        // Veriyi satırlara böl
        $numaralar = str_replace("\n", "<br />", $data);
        $phones = explode("<br />", $numaralar);
        $numaralarx = array();

        // Her bir numarayı işle
        foreach ($phones as $gsm) {
            $gsm = $gvn->rakam($gsm);
            $gsm = trim($gsm);
            if ($gsm != "" AND is_numeric($gsm)) {
                $gsm = (substr($gsm, 0, 3) == '+90') ? '0' . substr($gsm, 3, 20) : $gsm;
                $gsm = (substr($gsm, 0, 2) == '90') ? '0' . substr($gsm, 2, 20) : $gsm;
                $gsm = (substr($gsm, 0, 1) != 0) ? '0' . $gsm : $gsm;
                if (strlen($gsm) == 11) {
                    if (!in_array($gsm, $numaralarx)) {
                        $numaralarx[] = $gsm;
                    }
                }
            }
        }

        // Numara listesini birleştir
        $bulten = @implode(",", $numaralarx);

        // Veritabanında güncelle
        try {
            $guncel = $db->prepare("UPDATE gayarlar_501 SET bulten_gsm=?");
            $guncel->execute([$bulten]);
            $fonk->ajax_tamam("Data başarıyla güncellendi.");
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
        }
    }
}