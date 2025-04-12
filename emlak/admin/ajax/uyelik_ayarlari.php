<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Üyelik ayarlarını al
        $ua = $fonk->UyelikAyarlar();
        $bireysel_uyelik = htmlspecialchars($_POST["bireysel_uyelik"], ENT_QUOTES, 'UTF-8');
        $kurumsal_uyelik = htmlspecialchars($_POST["kurumsal_uyelik"], ENT_QUOTES, 'UTF-8');

        // Üyelik ayarlarını güncelle
        $ua["bireysel_uyelik"] = $bireysel_uyelik;
        $ua["kurumsal_uyelik"] = $kurumsal_uyelik;

        // JSON encode with Turkish characters support
        $jso = $fonk->json_encode_tr($ua);

        // Veritabanı bağlantısını hata modunda ayarla
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Üyelik ayarlarını güncelle
            $gunc = $db->prepare("UPDATE gayarlar_501 SET uyelik_ayarlar=? ");
            $gunc->execute([$jso]);
        } catch (PDOException $e) {
            // Hataları log dosyasına kaydet ve sitede göster
            error_log("Üyelik ayarları güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }

        // Başarı mesajı
        $fonk->ajax_tamam("Ayarlar Güncellendi.");
    }
}