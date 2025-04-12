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
if ($hesap->id == '') {
    die();
}

// Kullanıcı türü kontrolü
if ($hesap->turu != 1) {
    die();
}

$id = $gvn->rakam($_GET["id"]);

// Danışman bilgilerini kontrol et
$kontrol = $db->prepare("SELECT id, avatar, onecikar, onecikar_btarih FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND kid=?");
$kontrol->execute(array($id, $hesap->id));

if ($kontrol->rowCount() == 0) {
    die();
}

$danisman = $kontrol->fetch(PDO::FETCH_OBJ);

// Eğer danışman öne çıkarılmışsa
if ($danisman->onecikar == 1) {
    $bugun = date("Y-m-d");
    $dkgun = $fonk->gun_farki($danisman->onecikar_btarih, $bugun);
    if ($dkgun < 0) {
        $fonk->yonlendir("danisman-one-cikar?id=" . intval($id));
        die();
    } elseif ($dkgun >= 0) {
        die();
    }
} else {
    $oncpaket = $db->query("SELECT id, danisman_onecikar, danisman_onecikar_sure, danisman_onecikar_periyod FROM upaketler_501 WHERE acid=" . intval($hesap->id) . " AND durum=1 AND danisman_o[...]");
    if ($oncpaket->rowCount() > 0) {
        $fonk->yonlendir("danisman-one-cikar?id=" . intval($id));
        die();
    } else {
        $paketegore = $db->query("SELECT id, danisman_onecikar, danisman_onecikar_sure, danisman_onecikar_periyod FROM upaketler_501 WHERE acid=" . intval($hesap->id) . " AND durum=1 AND dani[...]");
        if ($paketegore->rowCount() > 0) {
            $paket = $paketegore->fetch(PDO::FETCH_OBJ);
            $danisman_onecikar_sure = ($paket->danisman_onecikar_sure == 0) ? 120 : $paket->danisman_onecikar_sure;
            $danisman_onecikar_periyod = ($paket->danisman_onecikar_sure == 0) ? "yillik" : $paket->danisman_onecikar_periyod;

            $expiry = "+" . $danisman_onecikar_sure;
            $expiry .= ($danisman_onecikar_periyod == "gunluk") ? ' day' : '';
            $expiry .= ($danisman_onecikar_periyod == "aylik") ? ' month' : '';
            $expiry .= ($danisman_onecikar_periyod == "yillik") ? ' year' : '';
            $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";

            $daUpdate = $db->query("UPDATE hesaplar SET onecikar=1, onecikar_btarih='" . $btarih . "' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . intval($id));
            $pakUpdate = $db->query("UPDATE upaketler_501 SET danisman_onecikar_use='1' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . intval($paket->id));
        } else {
            $fonk->yonlendir("danisman-one-cikar?id=" . intval($id));
            die();
        }
    }
}

// Yönlendirme ve ekran güncelleme
$fonk->yonlendir("eklenen-danismanlar", 1);
?>
<script type="text/javascript">
$(function(){
    $("#RoketButon<?= $id; ?>").hide(500);
});
</script>