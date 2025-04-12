<?php
// Kullanıcı kimliğini kontrol et
if ($hesap->id == '') {
    error_log("Geçersiz kullanıcı kimliği. Tarih: " . date("Y-m-d H:i:s"));
    die();
}

$id = $gvn->rakam($_GET["ilan_id"]);

// Video varlığını kontrol et
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND id=?");
$kontrol->execute([$id]);
if ($kontrol->rowCount() < 1) {
    error_log("Geçersiz video ID. Tarih: " . date("Y-m-d H:i:s"));
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

// Video dosyasını sil
if ($snc->video != '') {
    $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $snc->video;
    if (file_exists($nirde)) {
        unlink($nirde);
    }
}

// Video bilgisini güncelle
try {
    $db->query("UPDATE sayfalar SET video='' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $id);
} catch (PDOException $e) {
    error_log("Video bilgisi güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
    die($e->getMessage());
}
?>
<script type="text/javascript">
$(document).ready(function() {
    $("#VideoVarContent").slideUp(300, function() {
        $("#galeri_video_ekle").slideDown(300);
    });
    $('html, body').animate({scrollTop: 0}, 500);
});
</script>