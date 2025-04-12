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

$id = $gvn->rakam($_GET["ilan_id"]);
$from = $gvn->harf_rakam($_GET["from"]);
$video = $gvn->zrakam($_GET["video"]);

// Sayfa bilgilerini kontrol et
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND id=?");
$kontrol->execute(array($id));
if ($kontrol->rowCount() < 1) {
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

// Kullanıcı yetkisi kontrolü
$ilan_aktifet = ($hesap->tipi == 1) ? 1 : $hesap->ilan_aktifet;
$acc = $db->query("SELECT id, kid, ilan_aktifet FROM hesaplar WHERE site_id_555=501 AND id=" . intval($snc->acid))->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($snc->acid != $hesap->id && $hesap->id != $kid) {
    die();
}
$kurumsal = $db->prepare("SELECT ilan_aktifet FROM hesaplar WHERE site_id_555=501 AND id=?");
$kurumsal->execute(array($kid));
if ($kurumsal->rowCount() > 0) {
    $ilan_aktifet = ($kurumsal->fetch(PDO::FETCH_OBJ)->ilan_aktifet == 0) ? $ilan_aktifet : $kurumsal->fetch(PDO::FETCH_OBJ)->ilan_aktifet;
}

// Çoklu ilan kontrolü
$multi = $db->query("SELECT id, ilan_no FROM sayfalar WHERE site_id_555=501 AND ilan_no=" . intval($snc->ilan_no) . " ORDER BY id ASC");
$multict = $multi->rowCount();
$multif = $multi->fetch(PDO::FETCH_OBJ);
$multidids = $db->query("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM sayfalar WHERE site_id_555=501 AND ilan_no=" . intval($snc->ilan_no))->fetch(PDO::FETCH_OBJ)->ids;
$mulid = ($multict > 1 && $snc->id == $multif->id) ? " IN(" . $multidids . ")" : "=" . intval($snc->id);
$mulidx = ($multict > 1) ? " IN(" . $multidids . ")" : "=" . intval($snc->id);

// Video dosyası kontrolü
$video_tmp = $_FILES["video"]["tmp_name"];
if ($video_tmp == '') {
    die('<span class="error">' . dil("TX454") . '</span>');
}

// Dosya bilgileri
$video_name = $_FILES["video"]["name"];
$video_size = $_FILES["video"]["size"];
$video_exte = $fonk->uzanti($video_name);
$uzantilar = array(".mp4");

// Dosya boyutu ve uzantı kontrolü
if ($video_size > dil("VIDEO_MAX_BAYT")) {
    die('<span class="error">' . dil("TX455") . '</span>');
}
if (!in_array($video_exte, $uzantilar)) {
    die('<span class="error">' . dil("TX456") . '</span>');
}

// Dosya adı oluşturma
$video_adi = strtolower(substr(md5(uniqid(rand())), 0, 12)) . ".mp4";

// Dosya yükleme
$yukle = @move_uploaded_file($video_tmp, "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $video_adi);
if (!$yukle) {
?>
<script type="text/javascript">
var xbar = $('#YuklemeDurum');
var xpercent = $('#percent');
var xpercentVal = "0%";
$("#YuklemeBar").slideUp(400, function(){
    $("#VideoForm").slideDown(400);
    xbar.width(xpercentVal);
    xpercent.html(xpercentVal);
});
</script>
<?php
    die('<span class="error">' . dil("TX456") . '</span>');
}

// Eski video dosyasını sil
if ($snc->video != '') {
    $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $snc->video;
    if (file_exists($nirde)) {
        unlink($nirde);
    }
}

// İlan güncelleme
try {
    $ilan_update = $db->prepare("UPDATE sayfalar SET video=?, durum=?, gtarih=? WHERE site_id_555=501 AND id" . $mulidx);
    $ilan_update->execute(array($video_adi, $ilan_aktifet, $fonk->datetime()));
} catch (PDOException $e) {
    die($e->getMessage());
}

// Yönlendirme işlemleri
if ($from == "insert") {
    if ($gayarlar->dopingler_501 == 1) {
        $fonk->yonlendir("ilan-olustur?id=" . intval($id) . "&asama=2");
    } else {
        $fonk->yonlendir("aktif-ilanlar", 5000);
?>
<script type="text/javascript">
$("#galeri_video_ekle").hide(1, function(){
    $("#TamamDiv").show(1);
    ajaxHere('ajax.php?p=ilan_son_asama&id=<?= intval($snc->id); ?>','asama_result');
});
$('html, body').animate({scrollTop: 250}, 500);
</script>
<?php
    }
} else {
    $fonk->yonlendir("uye-paneli?rd=ilan_duzenle&id=" . intval($snc->id) . "&goto=videos", 100);
?>
<span class="complete"><?= dil("TX453"); ?></span>
<?php
}