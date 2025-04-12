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

$id = $gvn->rakam($_GET["id"]);
$ilan_sil = $gvn->rakam($_GET["ilan_sil"]);

// Danışman bilgilerini kontrol et
$kontrol = $db->prepare("SELECT id, avatar FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND kid=?");
$kontrol->execute(array($id, $hesap->id));

if ($kontrol->rowCount() == 0) {
    die();
}

$danisman = $kontrol->fetch(PDO::FETCH_OBJ);
$db->query("DELETE FROM hesaplar WHERE site_id_555=501 AND id=" . intval($id));

if ($ilan_sil == 1) {
    $query = $db->query("SELECT id, resim FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND acid=" . intval($id));
    while ($ilan = $query->fetch(PDO::FETCH_OBJ)) {
        $db->query("DELETE FROM sayfalar WHERE site_id_555=501 AND id=" . intval($ilan->id));
        if ($ilan->video != '') {
            $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $ilan->video;
            if (file_exists($nirde)) {
                @unlink($nirde);
            }
        }

        $quu = $db->query("SELECT resim FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . intval($ilan->id));
        while ($row = $quu->fetch(PDO::FETCH_OBJ)) {
            $pinfo = pathinfo("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/" . $row->resim);
            $folder = $pinfo["dirname"] . "/";
            $ext = $pinfo["extension"];
            $fname = $pinfo["filename"];
            $bname = $pinfo["basename"];

            @unlink($folder . "thumb/" . $bname);
            @unlink($folder . $bname);
            @unlink($folder . $fname . "_original." . $ext);
        }
        $db->query("DELETE FROM galeri_foto WHERE site_id_555=501 AND sayfa_id=" . intval($ilan->id));
    }
} else {
    $db->query("UPDATE sayfalar SET acid='" . intval($hesap->id) . "' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND acid=" . intval($id));
}
?>
<script type="text/javascript">
$(function(){
    $("#row_<?= intval($id); ?>").css({"background-color" : '#EFCFCF'});
    $("#row_<?= intval($id); ?>").animate({opacity : 0.1}, 1000, function(){
        $("#row_<?= intval($id); ?>").fadeOut(100);
    });
});
</script>