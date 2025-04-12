<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id !== "" && $hesap->tipi !== 0) {
    // PDO hata modunu ayarla
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // POST isteği olup olmadığını kontrol et
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($_POST['value'] as $key => $row) {
            $keys = $key + 1;
            $id = $row['id'] + 1;
            $blok = $row['name'];
            $sira = $keys;

            try {
                // Ayarları güncelle
                $stmt = $db->prepare("UPDATE ayarlar_501 SET " . $blok . "_sira = :sira WHERE dil = :dil");
                $stmt->execute([
                    'sira' => $sira,
                    'dil' => $dil
                ]);
            } catch (PDOException $e) {
                // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                die("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
            }
        }

        // Başarılı mesajı göster
        $fonk->ajax_tamam("Ayarlar Kaydedildi.");
    }
}