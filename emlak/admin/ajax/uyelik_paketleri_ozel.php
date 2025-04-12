<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $paketler_icerik = htmlspecialchars($_POST["paketler_icerik"], ENT_QUOTES, 'UTF-8');

        // Veritabanı bağlantısını hata modunda ayarla
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Üyelik paketleri içeriğini güncelle
            $gunc = $db->prepare("UPDATE ayarlar_501 SET paketler_icerik=? WHERE dil='" . $dil . "' ");
            $gunc->execute([$paketler_icerik]);
        } catch (PDOException $e) {
            // Hataları log dosyasına kaydet ve sitede göster
            error_log("Üyelik paketleri içeriği güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }

        // Başarı mesajı
        $fonk->ajax_tamam("Ayarlar Güncellendi.");
    }
}