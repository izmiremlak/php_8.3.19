<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // POST verilerini güvenli bir şekilde al
        $baslik = $_POST["baslik"];
        $link = $_POST["link"];
        $sira = $_POST["sira"];
        $count = count($baslik);

        // Mevcut referansları sil
        $db->query("DELETE FROM referanslar_501 WHERE dil=:dil", ['dil' => $dil]);

        for ($i = 0; $i < $count; $i++) {
            $adi = $gvn->html_temizle($baslik[$i]);
            $website = $gvn->html_temizle($link[$i]);
            $siraa = $gvn->zrakam($sira[$i]);

            if ($adi != '') {
                // Yeni referansları ekle
                $ekle = $db->prepare("INSERT INTO referanslar_501 SET adi=?, sira=?, website=?, dil=?");
                $ekle->execute([$adi, $siraa, $website, $dil]);
            }
        }

        // Başarı mesajı
        $fonk->ajax_tamam("Seo linkler başarıyla güncellendi.");
    }
}