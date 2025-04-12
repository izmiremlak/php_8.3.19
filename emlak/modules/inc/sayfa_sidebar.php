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
?>
<h2><i class="fa fa-bullhorn" aria-hidden="true"></i> <a href="haber-ve-duyurular"><?= htmlspecialchars(dil("TX104"), ENT_QUOTES, 'UTF-8'); ?></a></h2>
<div class="sidelinks">
<?php
$sqlx = $db->query("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=2 AND dil='" . htmlspecialchars($dil, ENT_QUOTES, 'UTF-8') . "' ORDER BY id DESC LIMIT 0,5");
while ($row = $sqlx->fetch(PDO::FETCH_OBJ)) {
    $link = ($dayarlar->permalink == 'Evet') ? htmlspecialchars($row->url, ENT_QUOTES, 'UTF-8') . '.html' : 'index.php?p=sayfa&id=' . intval($row->id);
?>
    <a href="<?= $link; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i><?= htmlspecialchars($row->baslik, ENT_QUOTES, 'UTF-8'); ?></a>
<?php } ?>
</div>

<div class="clear"></div>

<h2><i class="fa fa-rss" aria-hidden="true"></i> <a href="yazilar"><?= htmlspecialchars(dil("TX105"), ENT_QUOTES, 'UTF-8'); ?></a></h2>
<div class="sidelinks">
<?php
$sqlx = $db->query("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=1 AND dil='tr' ORDER BY id DESC LIMIT 0,5");
while ($row = $sqlx->fetch(PDO::FETCH_OBJ)) {
    $link = ($dayarlar->permalink == 'Evet') ? htmlspecialchars($row->url, ENT_QUOTES, 'UTF-8') . '.html' : 'index.php?p=sayfa&id=' . intval($row->id);
?>
    <a href="<?= $link; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i><?= htmlspecialchars($row->baslik, ENT_QUOTES, 'UTF-8'); ?></a>
<?php } ?>
</div>