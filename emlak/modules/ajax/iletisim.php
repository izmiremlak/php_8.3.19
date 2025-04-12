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

if ($_POST) {
    $adsoyad = htmlspecialchars($_POST["adsoyad"], ENT_QUOTES, 'UTF-8');
    $telefon = htmlspecialchars($_POST["telefon"], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST["email"], ENT_QUOTES, 'UTF-8');
    $cevap = strtolower(htmlspecialchars($_POST["cevap"], ENT_QUOTES, 'UTF-8'));
    $mesaj = htmlspecialchars($_POST["mesaj"], ENT_QUOTES, 'UTF-8');
    $ip_adres = $fonk->IpAdresi();
    $tarih = $fonk->datetime();

    // 15 dakika içinde aynı IP adresinden gelen mailleri kontrol et
    $varmi = $db->prepare("SELECT * FROM mail_501 WHERE tipi=0 AND ip=? AND tarih BETWEEN DATE_SUB(NOW(), INTERVAL 15 MINUTE) AND NOW()");
    $varmi->execute([$ip_adres]);

    // Boşluk kontrolü
    if ($fonk->bosluk_kontrol($adsoyad) == true || $fonk->bosluk_kontrol($email) == true || $fonk->bosluk_kontrol($mesaj) == true) {
        die('<span class="error">' . htmlspecialchars(dil("MS1"), ENT_QUOTES, 'UTF-8') . '</span>');
    }

    // E-posta geçerlilik kontrolü
    if ($gvn->eposta_kontrol($email) == false) {
        die('<span class="error">' . htmlspecialchars(dil("MS2"), ENT_QUOTES, 'UTF-8') . '</span>');
    }

    // Aynı IP adresinden gelen mail kontrolü
    if ($varmi->rowCount() > 0) {
        die('<span class="error">' . htmlspecialchars(dil("MS3"), ENT_QUOTES, 'UTF-8') . '</span>');
    }

    // Güvenlik sorusu kontrolü
    if ($cevap != strtolower(dil('CEVP'))) {
        die('<span class="error">' . htmlspecialchars(dil("MS6"), ENT_QUOTES, 'UTF-8') . '</span>');
    }

    // Mail gönderimi
    $gonder = $fonk->bildirim_gonder([$adsoyad, $email, $telefon, $mesaj, date("d.m.Y H:i", strtotime($tarih)), $ip_adres], "iletisim", $email, $telefon);

    if ($gonder) {
        ?>
        <script type="text/javascript">
            $("#iletisim_form").slideUp(500, function () {
                $("#IletisimTamam").slideDown(500);
            });
            $('html, body').animate({scrollTop: 250}, 500);
        </script>
        <?php

        // Mail veritabanına eklenir
        $ekle = $db->prepare("INSERT INTO mail_501 SET adsoyad=:adsoyad, email=:email, telefon=:telefon, tarih=:tarih, mesaj=:mesaj, ip=:ip");
        $ekle->execute([
            'adsoyad' => $adsoyad,
            'email' => $email,
            'telefon' => $telefon,
            'tarih' => $tarih,
            'mesaj' => $mesaj,
            'ip' => $ip_adres
        ]);
    } else {
        die('<span class="hata">' . htmlspecialchars(dil("MS5"), ENT_QUOTES, 'UTF-8') . '</span>');
    }
}