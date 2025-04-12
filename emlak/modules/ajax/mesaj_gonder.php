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

// Anlık sohbet kapalıysa işlem durdurulur
if ($gayarlar->anlik_sohbet == 0) {
    die();
}

// Kullanıcı bilgileri alınır
$bid = $hesap->id;
$basd = $hesap->adi . ' ' . $hesap->soyadi;
$uid = intval($_GET["uid"]); // Kullanıcı ID'si güvenli hale getirilir
$mesaj = $_POST["mesaj"];
$ilink = $gvn->mesaj(htmlspecialchars($_POST["ilan_linki"], ENT_QUOTES));
$mesaj .= "\n\n" . dil("ILAN_LINKI") . ": " . $ilink;
$mesaj = $gvn->mesaj(htmlspecialchars($mesaj, ENT_QUOTES));
$tarih = $fonk->datetime();
$ipi = $fonk->IpAdresi();
$from = $gvn->harf($_GET["from"]);

// Gerekli dosya dahil edilir
include "methods/chat.lib.php";

// Kullanıcı ID'si kontrol edilir
if ($uid == 0) {
    die('<span class="error">' . dil("TX409") . '</span>');
}

// Kullanıcı engelleme durumu kontrol edilir
if ($KarsiEngel == 1 || $BenEngel == 1) {
    die('<span class="error">' . dil("TX410") . '</span>');
}

// Mesajın boş olup olmadığı kontrol edilir
if ($fonk->bosluk_kontrol($mesaj) == true) {
    die();
}

// İlk sohbet durumu kontrol edilir ve gerekli işlemler yapılır
if ($ilkSohbet == 1) {
    try {
        $MesajLineOlustur = $db->prepare("INSERT INTO mesajlar_501 SET kimden=?, kime=?, tarih=?, starih=?");
        $MesajLineOlustur->execute([$bid, $uid, $tarih, $tarih]);
    } catch (PDOException $e) {
        // Hata mesajını log dosyasına yaz
        error_log($e->getMessage(), 3, __DIR__ . '/logs/error_log.txt');
        die($e->getMessage());
    }
    $mid = $db->lastInsertId();
    $MesajLine = $db->prepare("SELECT * FROM mesajlar_501 WHERE id=?");
    $MesajLine->execute([$mid]);
    if ($MesajLine->rowCount() > 0) {
        $MesajLine = $MesajLine->fetch(PDO::FETCH_OBJ);
        $ilkSohbet = 0;
    }
}

// Mesaj gönderme işlemi yapılır
try {
    $iletiGonder = $db->prepare("INSERT INTO mesaj_iletiler_501 SET mid=?, gid=?, ileti=?, tarih=?, ip=?");
    $iletiGonder->execute([$MesajLine->id, $bid, $mesaj, $tarih, $ipi]);
} catch (PDOException $e) {
    // Hata mesajını log dosyasına yaz
    error_log($e->getMessage(), 3, __DIR__ . '/logs/error_log.txt');
    die($e->getMessage());
}
$db->query("UPDATE mesajlar_501 SET starih='$tarih' WHERE id=" . $MesajLine->id);

// Kullanıcı arayüzü güncelleme
if ($from == '') {
    echo '<script type="text/javascript">$("#MesajYaz").val(""); ArayuzYukle();</script>';
} elseif ($from == "adv") {
    $adsoyad = $hesap->adi;
    $adsoyad .= ($hesap->soyadi != '') ? ' ' . $hesap->soyadi : '';
    $adsoyad = ($hesap->unvan != '') ? $hesap->unvan : $adsoyad;
    $uyebm = intval($_COOKIE["uyebm"]);

    if ($uyebm == '' || $uyebm != $uid) {
        // admin eposta text
        $aetxt = '
        <table width="100%" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td width="100%" style="border-bottom-width: 1px; border-bottom-style: dotted; border-bottom-color: #CCC; padding:3px;" scope="col">
                    <h3 style="font-size:20px; font-family:Calibri, Helvetica, sans-serif; color:#00599D; font-weight:bold;">Bilgilendirme</h3>
                </td>
            </tr>
            <tr>
                <td style="border-bottom-width: 1px; border-bottom-style: dotted; border-bottom-color: #CCC; padding:3px;" scope="col">
                    <p><span style="font-size:14px; font-family:Calibri, Helvetica, sans-serif;">Sn. ' . $uyadsoyad . ',</span><br><br>
                    <span style="font-size:14px; font-family:Calibri, Helvetica, sans-serif;">Bir kullanıcımız size mesaj gönderdi. Detaylı bilgi ve mesajı yanıtlamak için üye panelinizi ziyaret edebilirsiniz.</span></p>
                    <p><strong><span style="font-family: Calibri, Helvetica, sans-serif; font-size: 14px; color: #D4701A">Detaylar;<br>----------------------------</span></strong><span style="font-size:14px; font-family:Calibri, Helvetica, sans-serif;">www.' . str_replace("www.", "", $_SERVER["SERVER_NAME"]) . '<br></span></p>
                    <p><span style="font-size:14px; font-family:Calibri, Helvetica, sans-serif;">İyi Çalışmalar,<br>www.' . str_replace("www.", "", $_SERVER["SERVER_NAME"]) . '<br></span></p>
                </td>
            </tr>
        </table>';

        $aegonder = $fonk->mail_gonder('Mesajınız var.', $uye->email, $aetxt);
        if ($aegonder) {
            setcookie("uyebm", $uid, time() + 60 * 5);
        }
    }

    echo '<script>
        $("#MesajGonderForm").slideUp(500, function() {
            $("#TamamPnc").slideDown(500);
        });
    </script>';
}