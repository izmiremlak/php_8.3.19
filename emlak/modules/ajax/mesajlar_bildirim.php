<?php
// Hata yönetimi için ayarları yapılandır
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error_log.txt'); // Hata log dosyasının yolu
error_reporting(E_ALL); // Tüm hataları raporla

// Hataları sitede göster
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='color: red;'><b>Hata:</b> [$errno] $errstr - $errfile:$errline</div>";
    return true;
}
set_error_handler("customErrorHandler");

// PDO hata modunu ayarla
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// JSON içerik tipi belirle
header("Content-Type: application/json; Charset=utf-8");

// Kullanıcı ID'si alınır ve bildirimler hesaplanır
if ($hesap->id != '') {
    $bid = $hesap->id;
    $bildirim = 0;
    $data = array('bildirim' => $bildirim);

    // Anlık sohbet etkinse bildirimler alınır
    if ($gayarlar->anlik_sohbet == 1) {
        try {
            $kisilerListe = $db->prepare("SELECT DISTINCT mr.id FROM mesajlar_501 AS mr INNER JOIN mesaj_iletiler_501 AS mi ON mi.mid = mr.id WHERE (mr.kimden=:idim OR mr.kime=:idim) AND ( (mr.kimden=:idim AND mr.durum=1) OR (mr.kime=:idim AND mr.durum=1) )");
            $kisilerListe->execute(array('idim' => $bid));
        } catch (PDOException $e) {
            // Hata mesajını log dosyasına yaz
            error_log($e->getMessage(), 3, __DIR__ . '/logs/error_log.txt');
            die($e->getMessage());
        }

        while ($row = $kisilerListe->fetch(PDO::FETCH_OBJ)) {
            $bsayi = $db->query("SELECT COUNT(id) AS kac FROM mesaj_iletiler_501 WHERE mid=" . $row->id . " AND gid!=" . $bid . " AND durum=0")->fetch(PDO::FETCH_OBJ)->kac;
            $bildirim += $bsayi;
        }

        $data['bildirim'] = $bildirim;
    }

    echo json_encode($data);
}