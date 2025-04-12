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

// Kullanıcı giriş kontrolü
if ($hesap->id == "" && $_POST) {
    $email = $gvn->eposta($_POST["email"]);
    $parola = $gvn->html_temizle($_POST["parola"]);
    $otut = $gvn->rakam($_POST["otut"]);

    // Boşluk kontrolü
    if ($fonk->bosluk_kontrol($email) == true || $fonk->bosluk_kontrol($parola) == true) {
        die('<span class="error">' . dil("TX10") . '</span>');
    }

    // Kullanıcı bilgilerini kontrol et
    $kontrol = $db->prepare("SELECT durum, id, email, parola, tipi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND email=:eposta AND parola=:sifre");
    $kontrol->execute(array('eposta' => $email, 'sifre' => $parola));

    if ($kontrol->rowCount() != 0) {
        $hesap = $kontrol->fetch(PDO::FETCH_OBJ);
        $secret = $fonk->login_secret_key($hesap->id, $parola);
        $dt = $fonk->datetime();
        $ip_adres = $fonk->IpAdresi();

        // Kullanıcı bilgilerini güncelle
        $hup = $db->prepare("UPDATE hesaplar SET ip=:ip_adresi, son_giris_tarih=:tarih, login_secret=:secret WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:hesap_id");
        $hup->execute(array(
            'ip_adresi' => $ip_adres,
            'tarih' => $dt,
            'secret' => $secret,
            'hesap_id' => $hesap->id
        ));

        // Kullanıcı hesabı engellenmiş mi?
        if ($hesap->durum == 1) {
            die('<span class="error">' . dil("TX11") . '</span>');
        } else {
            // Giriş başarılı ise oturum aç
            $_SESSION["acid"] = $hesap->id;
            $_SESSION["acpw"] = $hesap->parola;

            // Oturumu hatırla
            if ($otut == 1) {
                setcookie("acid", $hesap->id, time() + 60 * 60 * 24 * 30);
                setcookie("acpw", $parola, time() + 60 * 60 * 24 * 30);
                setcookie("acsecret", $secret, time() + 60 * 60 * 24 * 30);
            }
            echo('<span class="complete">' . dil("TX12") . '</span>');

            $referer = $gvn->html_temizle($_COOKIE["login_redirect"]);
            if ($referer != '' && stristr($referer, $domain)) {
                $yonlendir = (stristr($referer, "index.html") || stristr($referer, "index.php")) ? "uye-paneli" : $referer;
            } else {
                $yonlendir = "uye-paneli";
            }

            $fonk->yonlendir($yonlendir, 0);
            setcookie("login_redirect", "", time() - 1);
        }
    } else {
        die('<span class="error">' . dil("TX13") . '<span>');
    }
}