<div class="clear"></div>
<?php
try {
    if ($gayarlar->reklamlar == 1) { // Eğer reklamlar aktif ise...
        $detect = $detect ?? new Mobile_Detect;

        $rtipi = 1;
        // Reklamları veritabanından çekme sorgusu
        $reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1)");
        $rcount = $reklamlar->rowCount();
        $order = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
        $reklam = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1) " . $order . " LIMIT 0,1")->fetch(PDO::FETCH_OBJ);

        if ($rcount > 0) {
?>
            <!-- 728 x 90 Reklam Alanı -->
            <div class="clear"></div>
            <div class="ad728home">
                <?= ($detect->isMobile() || $detect->isTablet()) ? htmlspecialchars($reklam->mobil_kodu) : htmlspecialchars($reklam->kodu); ?>
            </div>
            <!-- 728 x 90 Reklam Alanı END-->
<?php
        }
    }
} catch (PDOException $e) {
    // Hataları log dosyasına yazma
    error_log("Reklamlar çekilirken bir hata oluştu: " . $e->getMessage(), 0);
    echo "<div class='error'>Reklamlar çekilirken bir hata oluştu.</div>";
}
?>
<div class="clear"></div>