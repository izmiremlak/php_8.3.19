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

// POST verilerinin varlığını kontrol et
if ($_POST) {
    $adsoyad = $gvn->html_temizle($_POST["adsoyad"]);
    $telefon = $gvn->html_temizle($_POST["telefon"]);
    $email = $gvn->html_temizle($_POST["email"]);
    $cevap = strtolower($gvn->html_temizle($_POST["cevap"]));
    $mesaj = $gvn->mesaj($_POST["mesaj"]);
    $emlak_tipi = $gvn->html_temizle($_POST["emlak_tipi"]);
    $ulke_id = $gvn->zrakam($_POST["ulke_id"]);
    $il = $gvn->zrakam($_POST["il"]);
    $ilce = $gvn->zrakam($_POST["ilce"]);
    $mahalle = $gvn->zrakam($_POST["mahalle"]);
    $talep = $gvn->html_temizle($_POST["talep"]);
    $customs = array();

    // Son 2 dakika içinde aynı IP'den yapılan talepleri kontrol et
    $varmi = $db->prepare("SELECT * FROM mail_501 WHERE tipi=2 AND ip=? AND tarih BETWEEN DATE_SUB(NOW(), INTERVAL 2 MINUTE) AND NOW()");
    $varmi->execute(array($fonk->IpAdresi()));

    // Boş alan kontrolü
    if ($fonk->bosluk_kontrol($adsoyad) == true OR $fonk->bosluk_kontrol($email) == true OR $fonk->bosluk_kontrol($mesaj) == true) {
        die('<span class="error">' . dil("MS1") . '</span>');
    }

    // Email doğrulama
    if ($gvn->eposta_kontrol($email) == false) {
        die('<span class="error">' . dil("MS2") . '</span>');
    }

    // Aynı IP'den yapılan talepleri engelle
    if ($varmi->rowCount() > 0) {
        die('<span class="error">' . dil("MS8") . '</span>');
    }

    $ulke_id = ($ulke_id == 0) ? 1 : $ulke_id;

    // Ülkeyi kontrol et
    $ulkekontrol = $db->prepare("SELECT * FROM ulkeler_501 WHERE id=?");
    $ulkekontrol->execute(array($ulke_id));
    if ($ulkekontrol->rowCount() < 1) {
        die("<span class=''>Geçersiz ülke girdiniz!</span>");
    }
    $ulke = $ulkekontrol->fetch(PDO::FETCH_OBJ);

    // İli kontrol et
    $ilkontrol = $db->prepare("SELECT * FROM il WHERE id=?");
    $ilkontrol->execute(array($il));
    if ($ilkontrol->rowCount() < 1) {
        die("<span class=''>" . dil("TX24") . "</span>");
    }
    $il = $ilkontrol->fetch(PDO::FETCH_OBJ);

    // İlçeyi kontrol et
    if ($ilce != 0) {
        $ilcekontrol = $db->prepare("SELECT * FROM ilce WHERE id=?");
        $ilcekontrol->execute(array($ilce));
        if ($ilcekontrol->rowCount() < 1) {
            die("<span class=''>" . dil("TX25") . "</span>");
        }
        $ilce = $ilcekontrol->fetch(PDO::FETCH_OBJ);
    }

    // Mahalleyi kontrol et
    if ($mahalle != 0) {
        $mahakontrol = $db->prepare("SELECT * FROM mahalle_koy WHERE id=?");
        $mahakontrol->execute(array($mahalle));
        if ($mahakontrol->rowCount() < 1) {
            die("<span class=''>" . dil("TX25") . "</span>");
        }
        $mahalle = $mahakontrol->fetch(PDO::FETCH_OBJ);
    }

    $deg2 = array();
    $customs["acid"] = $hesap->id;

    if ($emlak_tipi != '') {
        $customs["Emlak Tipi"] = $emlak_tipi;
        $deg2[] = $emlak_tipi;
    } else {
        $deg2[] = "Seçilmedi";
    }

    if ($ulke->ulke_adi != '' AND $ulke->ulke_adi != "Türkiye") {
        $customs["Ülke"] = $ulke->ulke_adi;
        $deg2[] = $ulke->ulke_adi;
    } else {
        $deg2[] = "Türkiye";
    }

    if ($il->il_adi != '') {
        $customs["İl"] = $il->il_adi;
        $deg2[] = $il->il_adi;
    } else {
        $deg2[] = "Seçilmedi";
    }

    if ($ilce->ilce_adi != '') {
        $customs["İlçe"] = $ilce->ilce_adi;
        $deg2[] = $ilce->ilce_adi;
    } else {
        $deg2[] = "Seçilmedi";
    }

    if ($mahalle->mahalle_adi != '') {
        $customs["Mahalle"] = $mahalle->mahalle_adi;
        $deg2[] = $mahalle->mahalle_adi;
    } else {
        $deg2[] = "Seçilmedi";
    }

    if ($talep != '') {
        $customs["Talebi"] = $talep;
        $deg2[] = $talep;
    } else {
        $deg2[] = "Boş";
    }

    $deg1 = array($adsoyad, $email, $telefon, $mesaj, date("d.m.Y H:i", strtotime($tarih)), $ip_adres);
    $degiskenler = array_merge($deg1, $deg2);

    $gonder = $fonk->bildirim_gonder($degiskenler, "emlak_talep_formu", $email, $telefon);

    if ($gonder) {
?>
<script type="text/javascript">
$("#EmlakTalepForm").slideUp(500, function(){
    $("#EmlakTalepForm_SUCCESS").slideDown(500);
});
$('html, body').animate({scrollTop: 250}, 500);
</script>
<?php
        $customs = $fonk->json_encode_tr($customs);

        $ekle = $db->prepare("INSERT INTO mail_501 SET tipi=:tipine, adsoyad=:adsoyad, email=:email, telefon=:telefon, tarih=:tarih, mesaj=:mesaj, ip=:ip, customs=:custom_code");
        $ekle->execute(array(
            'tipine' => 2,
            'adsoyad' => $adsoyad,
            'email' => $email,
            'telefon' => $telefon,
            'tarih' => $fonk->datetime(),
            'mesaj' => $mesaj,
            'ip' => $fonk->IpAdresi(),
            'custom_code' => $customs,
        ));
    } else {
        die('<span class="hata">' . dil("MS5") . '</span>');
    }
}