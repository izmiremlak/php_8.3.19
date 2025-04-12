<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id !== "" && $hesap->tipi !== 0) {

    try {
        // sgonderiler tablosunu temizlemek için SQL sorgusu
        $gunc = $db->query("DELETE FROM sgonderiler_501");

        // Başarılı mesajı göster
        $fonk->ajax_tamam("Başarılı bir şekilde temizlendi.");

    } catch (PDOException $e) {
        // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
        error_log($e->getMessage(), 3, '/var/log/php_errors.log');
        die("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
    }
}