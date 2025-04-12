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

// POST kontrolü
if ($_POST) {
    // Kullanıcı oturum kontrolü
    if ($hesap->id == "") {
        $cerez = $_COOKIE["parola_hatirlat"];

        // Çerez kontrolü
        if ($fonk->bosluk_kontrol($cerez) == false) {
            die("<span class='error'>" . dil("TX41") . "</span>");
        }

        // Email güvenli hale getirme
        $email = $gvn->eposta($_POST["email"]);

        // Email boşluk kontrolü
        if ($fonk->bosluk_kontrol($email) == true) {
            die("<span class='error'>" . dil("TX42") . "</span>");
        }

        // Email geçerlilik kontrolü
        if ($gvn->eposta_kontrol($email) == false) {
            die("<span class='error'>" . dil("TX43") . "</span>");
        }

        // Kullanıcı bilgilerini kontrol et
        $kontrol = $db->prepare("SELECT email, parola, id, CONCAT_WS(' ', adi, soyadi) AS adsoyad, unvan, telefon FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND email=:eposta");
        $kontrol->execute(array('eposta' => $email));

        // Kullanıcı bulunduysa
        if ($kontrol->rowCount() > 0) {
            $acc = $kontrol->fetch(PDO::FETCH_OBJ);
            $adsoyad = ($acc->unvan != '') ? $acc->unvan : $acc->adsoyad;

            // Bildirim gönder
            $gonder = $fonk->bildirim_gonder(array($adsoyad, $acc->email, $acc->parola, SITE_URL . "hesabim"), "sifre_unuttum", $acc->email, $acc->telefon);

            // Bildirim gönderildiyse
            if ($gonder) {
                echo("<span class='complete'>" . dil("TX44") . "</span>");
                setcookie("parola_hatirlat", $email, time() + 60 * 15);
            } else {
                die("<span class='error'>" . dil("TX45") . "</span>");
            }
        } else {
            die("<span class='error'>" . dil("TX46") . "</span>");
        }
    }
}