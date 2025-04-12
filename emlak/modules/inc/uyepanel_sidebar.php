<?php
// Tema dizini tanımlı değilse çıkış yap
if (!defined("THEME_DIR")) {
    die();
}

// Hata yönetimi için ayarları yapılandır
ini_set('log_errors', 1);
ini_set('error_log', THEME_DIR . 'logs/error_log.txt'); // Hata log dosyasının yolu
error_reporting(E_ALL); // Tüm hataları raporla

// Hataları sitede göster
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='color: red;'><b>Hata:</b> [$errno] $errstr - $errfile:$errline</div>";
    return true;
}
set_error_handler("customErrorHandler");
?>
<div class="uyepanellinks">
    <h5><?= htmlspecialchars(dil("TX107"), ENT_QUOTES, 'UTF-8'); ?><br>
    <strong>Sn. <?= htmlspecialchars($hesap->adi, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($hesap->soyadi, ENT_QUOTES, 'UTF-8'); ?></strong>
    </h5>

    <!-- id="uyeaktifbtn" -->
    <a id="uyepanelyeniilan" class="btn" href="ilan-olustur"><i class="fa fa-plus" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX108"), ENT_QUOTES, 'UTF-8'); ?></a>
    <?php if ($hesap->turu == 1 || $hesap->turu == 0) { ?>
        <?php
        $upaketleri = $db->query("SELECT id FROM upaketler_501 WHERE acid=" . intval($hesap->id));
        $upaketleri = $upaketleri->rowCount();
        ?>
        <a <?= ($upaketleri < 1) ? 'id="uyepaketlink" ' : ''; ?>class="btn<?= ($rd == "paketlerim" || $rd == "uyelik_paketi_satinal") ? ' uyeaktifbtn2' : ''; ?>" href="<?= ($upaketleri > 0) ? 'paketlerim' : 'uyelik-paketi-satinal'; ?>"><i class="fa fa-gift" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX109"), ENT_QUOTES, 'UTF-8'); ?></a>
    <?php } ?>
    <a class="btn" href="dopinglerim" <?= ($rd == "dopinglerim") ? 'id="uyeaktifbtn"' : ''; ?>><i class="fa fa-rocket" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX534"), ENT_QUOTES, 'UTF-8'); ?></a>
    <a class="btn" href="aktif-ilanlar" <?= ($rd == "aktif_ilanlar") ? 'id="uyeaktifbtn"' : ''; ?>><i class="fa fa-thumbs-up" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX110"), ENT_QUOTES, 'UTF-8'); ?></a>
    <a class="btn" href="pasif-ilanlar" <?= ($rd == "pasif_ilanlar") ? 'id="uyeaktifbtn"' : ''; ?>><i class="fa fa-power-off" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX111"), ENT_QUOTES, 'UTF-8'); ?></a>
    <?php if ($hesap->turu == 1) { ?> 
        <a class="btn" href="eklenen-danismanlar" <?= ($rd == "danismanlar" || $rd == "danisman_ekle" || $rd == "danisman_duzenle") ? 'id="uyeaktifbtn"' : ''; ?>><i class="fa fa-briefcase" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX112"), ENT_QUOTES, 'UTF-8'); ?></a>
    <?php } ?>
    <a class="btn" href="favori-ilanlar" <?= ($rd == "favori_ilanlar") ? 'id="uyeaktifbtn"' : ''; ?>><i class="fa fa-heart" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX113"), ENT_QUOTES, 'UTF-8'); ?></a>
    <?php if ($gayarlar->anlik_sohbet == 1) { ?>
        <a class="btn" href="mesajlar" <?= ($rd == "mesajlar") ? 'id="uyeaktifbtn"' : ''; ?>><span style="margin-left: -29px; position: absolute; margin-top: -15px; display: none;" class="msjvar mbildirim">0</span><i class="fa fa-envelope" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX114"), ENT_QUOTES, 'UTF-8'); ?></a>
    <?php } ?>
    <a class="btn" href="uye-paneli" <?= ($rd == '') ? 'id="uyeaktifbtn"' : ''; ?>><i class="fa fa-user" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX115"), ENT_QUOTES, 'UTF-8'); ?></a>
</div>