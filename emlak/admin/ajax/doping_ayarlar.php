<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $dzaman1a = filter_var($_POST['dzaman1a'], FILTER_VALIDATE_INT);
        $dzaman1b = $gvn->harf_rakam($_POST['dzaman1b']);
        $dzaman2a = filter_var($_POST['dzaman2a'], FILTER_VALIDATE_INT);
        $dzaman2b = $gvn->harf_rakam($_POST['dzaman2b']);
        $dzaman3a = filter_var($_POST['dzaman3a'], FILTER_VALIDATE_INT);
        $dzaman3b = $gvn->harf_rakam($_POST['dzaman3b']);
        $dzaman1 = $dzaman1a . "|" . $dzaman1b;
        $dzaman2 = $dzaman2a . "|" . $dzaman2b;
        $dzaman3 = $dzaman3a . "|" . $dzaman3b;

        $fiyat1 = $_POST['fiyat1'];
        $fiyat2 = $_POST['fiyat2'];
        $fiyat3 = $_POST['fiyat3'];

        foreach ($fiyat1 as $k => $v) {
            $dfiyat1 = $gvn->prakam($fiyat1[$k]);
            $dfiyat2 = $gvn->prakam($fiyat2[$k]);
            $dfiyat3 = $gvn->prakam($fiyat3[$k]);
            $dfiyat1 = $gvn->para_int($dfiyat1);
            $dfiyat2 = $gvn->para_int($dfiyat2);
            $dfiyat3 = $gvn->para_int($dfiyat3);

            $gunc = $db->prepare("UPDATE doping_ayarlar_501 SET fiyat1 = ?, fiyat2 = ?, fiyat3 = ? WHERE id = ?");
            $gunc->execute([$dfiyat1, $dfiyat2, $dfiyat3, $k]);
        }

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Veritabanı güncelleme işlemi
        try {
            $gunc = $db->prepare("UPDATE gayarlar_501 SET dzaman1 = ?, dzaman2 = ?, dzaman3 = ?");
            $gunc->execute([$dzaman1, $dzaman2, $dzaman3]);
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }

        $fonk->ajax_tamam("Ayarlar Güncellendi.");
    }
}