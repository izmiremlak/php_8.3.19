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

// Favori bilgilerini kontrol et
$kontrol = $db->prepare("SELECT id FROM favoriler_501 WHERE id=? AND acid=?");
$kontrol->execute(array($id, $hesap->id));

if ($kontrol->rowCount() == 0) {
    die();
}

$favori = $kontrol->fetch(PDO::FETCH_OBJ);

// Favoriyi sil
try {
    $db->query("DELETE FROM favoriler_501 WHERE id=" . intval($id));
?>
<script type="text/javascript">
$(function(){
    $("#row_<?= intval($id); ?>").css({"background-color" : '#EFCFCF'});
    $("#row_<?= intval($id); ?>").animate({opacity : 0.1}, 1000, function(){
        $("#row_<?= intval($id); ?>").fadeOut(100);
    });
});
</script>
<?php
} catch (PDOException $e) {
    // Hata durumunda log dosyasına yaz
    error_log($e->getMessage(), 3, __DIR__ . '/logs/error_log.txt');
    echo '<div style="color: red;"><b>Hata:</b> Favori silinirken bir hata oluştu.</div>';
}