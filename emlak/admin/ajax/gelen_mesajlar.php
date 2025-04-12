<?php
// JSON türünde veri döndürüleceğini belirten başlık
header("Content-Type:application/json; Charset=utf8");

// Kullanıcının giriş yapıp yapmadığını kontrol et
if ($hesap->id != "") {
    $data = array();

    // Veritabanından mesajları al ve durum ve tarihe göre sırala
    try {
        $sorgu = $db->query("SELECT * FROM mail_501 ORDER BY durum ASC, tarih DESC");

        // Mesajları çek ve JSON formatında geri döndür
        while ($msg = $sorgu->fetch(PDO::FETCH_OBJ)) {
            $data['data'][] = array(
                '<div class="checkbox checkbox-primary"><input name="id[]" id="check' . $msg->id . '" type="checkbox"><label for="check' . $msg->id . '"></label></div>',
                '<a href="#"><i class="fa fa-circle text-info m-r-15"></i>' . htmlspecialchars($msg->adsoyad) . '</a>',
                htmlspecialchars($msg->telefon),
                htmlspecialchars($msg->email),
                htmlspecialchars($msg->tarih),
                'Kontrol Code',
            );
        }
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, '/var/log/php_errors.log');
        die(json_encode(['error' => 'Veritabanı hatası: ' . htmlspecialchars($e->getMessage())]));
    }

    echo json_encode($data);
}