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

// POST verisinin varlığını kontrol et
if (!isset($_POST)) {
    die(); exit;
}

// POST verilerini güvenli hale getir
$email = $gvn->html_temizle($_POST["email"]);
$gsmx = $gvn->rakam($_POST["gsm"]);

// Email ve GSM bilgisi boşsa hata mesajı göster
if ($email == '' AND $gsmx == '') {
    die('<span class="error">' . htmlspecialchars(dil("TX4"), ENT_QUOTES, 'UTF-8') . '</span>');
}

// GSM numarası girilmişse işlemleri yap
if ($gsmx != '') {
    $numaralar = str_replace(",", "<br />", $gayarlar->bulten_gsm);
    $phones = explode("<br />", $numaralar);
    $numaralarx = array();
    foreach ($phones as $gsm) {
        $gsm = $gvn->rakam($gsm);
        $gsm = trim($gsm);
        if ($gsm != "" AND is_numeric($gsm)) {
            $gsm = (substr($gsm, 0, 3) == '+90') ? '0' . substr($gsm, 3, 20) : $gsm;
            $gsm = (substr($gsm, 0, 2) == '90') ? '0' . substr($gsm, 2, 20) : $gsm;
            $gsm = (substr($gsm, 0, 1) != 0) ? '0' . $gsm : $gsm;
            if (strlen($gsm) == 11) {
                if (!in_array($gsm, $numaralarx)) {
                    $numaralarx[] = $gsm;
                }
            }
        }
    }

    $gsmx = trim($gsmx);
    if ($gsmx != "" AND is_numeric($gsmx)) {
        $gsmx = (substr($gsmx, 0, 3) == '+90') ? '0' . substr($gsmx, 3, 20) : $gsmx;
        $gsmx = (substr($gsmx, 0, 2) == '90') ? '0' . substr($gsmx, 2, 20) : $gsmx;
        $gsmx = (substr($gsmx, 0, 1) != 0) ? '0' . $gsmx : $gsmx;
        if (strlen($gsmx) == 11) {
            if (in_array($gsmx, $numaralarx)) {
                die('<span class="error">' . htmlspecialchars(dil("TX5"), ENT_QUOTES, 'UTF-8') . '</span>');
            } else {
                $numaralarx[] = $gsmx;
            }
        } else {
            die('<span class="error">' . htmlspecialchars(dil("TX6"), ENT_QUOTES, 'UTF-8') . '</span>');
        }
    } else {
        die('<span class="error">' . htmlspecialchars(dil("TX6"), ENT_QUOTES, 'UTF-8') . '</span>');
    }

    $bulten_gsm = @implode(",", $numaralarx);
    $update = $db->prepare("UPDATE gayarlar_501 SET bulten_gsm=? ");
    $update->execute(array($bulten_gsm));
} // eğer gsm yazmışsa
?>

<!-- Email adresi girilmişse işlemleri yap -->
<?php
if ($email != '') {
    $epostalar = str_replace(",", "<br />", $gayarlar->bulten_email);
    $emails = explode("<br />", $epostalar);
    $emaillerx = array();
    foreach ($emails as $eml) {
        $eml = $gvn->html_temizle($eml);
        $eml = trim($eml);
        if ($eml != "") {
            if (!in_array($eml, $emaillerx)) {
                $emaillerx[] = $eml; 
            }
        }
    }

    $email = trim($email);
    if ($email != "") {
        if (!$gvn->eposta_kontrol($email)) {
            die('<span class="error">' . htmlspecialchars(dil("TX8"), ENT_QUOTES, 'UTF-8') . '</span>');
        } else {
            if (in_array($email, $emaillerx)) {
                die('<span class="error">' . htmlspecialchars(dil("TX9"), ENT_QUOTES, 'UTF-8') . '</span>');
            } else {
                $emaillerx[] = $email;
            }
        }
    }

    $bulten_email = @implode(",", $emaillerx);
    $update = $db->prepare("UPDATE gayarlar_501 SET bulten_email=? ");
    $update->execute(array($bulten_email));
} // Eğer email yazmışsa 
?>
<script type="text/javascript">
$("#bulten_form").slideUp(700, function(){
    $("#BultenTamam").slideDown(800);
});
</script>