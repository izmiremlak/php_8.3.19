<?php

declare(strict_types=1); // PHP 8.3 için sıkı tip kontrolü

// Hata raporlama ve log dosyası ayarları
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error_log.txt'); // İzin verilen yol içinde log dosyası

// Özel hata işleyici (tekrar tanımlamayı önlemek için)
if (!function_exists('customErrorHandler')) {
    /**
     * Özel hata işleyici fonksiyonu
     *
     * @param int $errno Hata numarası
     * @param string $errstr Hata mesajı
     * @param string|null $errfile Hata dosya adı
     * @param int|null $errline Hata satır numarası
     * @return bool Hata işlendi mi
     */
    function customErrorHandler($errno, $errstr, $errfile = null, $errline = null): bool
    {
        $errorMessage = "[$errno] $errstr - Dosya: " . ($errfile ?? 'bilinmiyor') . " - Satır: " . ($errline ?? 'bilinmiyor');
        error_log($errorMessage);
        echo "<div style='color: red;'><b>Hata:</b> $errorMessage</div>";
        return true;
    }
}
set_error_handler('customErrorHandler');

// Dil değişkenini kontrol et, tanımlı değilse varsayılan 'tr' kullan
$dil = $dil ?? 'tr';

// Şehirler veritabanından çekiliyor (SQL injection önlemek için prepared statement)
$stmt = $db->prepare("SELECT * FROM sehirler_501 WHERE dil = ? ORDER BY sira ASC");
$stmt->execute([htmlspecialchars($dil, ENT_QUOTES, 'UTF-8')]);
if ($stmt->rowCount() > 0) {
?>
<!-- blok4 start -->
<div class="sehirbutonlar">
    <div id="sehirbutonlar-container">
        <?php
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $eksq = "";
            $linki = "";
            $smod_adi = "";

            // İl kontrolü
            if (($row["il"] ?? 0) != 0) {
                $ilKontrol = $db->prepare("SELECT id, il_adi, slug FROM il WHERE id = ?");
                $ilKontrol->execute([(int)$row["il"]]);
                if ($ilKontrol->rowCount() > 0) {
                    $ilKontrol = $ilKontrol->fetch(PDO::FETCH_OBJ);
                    $eksq .= "t1.il_id=" . (int)$ilKontrol->id . " AND ";
                    $linki .= "/" . htmlspecialchars($ilKontrol->slug ?? '', ENT_QUOTES, 'UTF-8');
                    $smod_adi = htmlspecialchars($ilKontrol->il_adi ?? '', ENT_QUOTES, 'UTF-8');
                }
            }

            // İlçe kontrolü
            if (($row["ilce"] ?? 0) != 0) {
                $ilceKontrol = $db->prepare("SELECT id, ilce_adi, slug FROM ilce WHERE id = ?");
                $ilceKontrol->execute([(int)$row["ilce"]]);
                if ($ilceKontrol->rowCount() > 0) {
                    $ilceKontrol = $ilceKontrol->fetch(PDO::FETCH_OBJ);
                    $eksq .= "t1.ilce_id=" . (int)$ilceKontrol->id . " AND ";
                    $linki .= "-" . htmlspecialchars($ilceKontrol->slug ?? '', ENT_QUOTES, 'UTF-8');
                    $smod_adi = htmlspecialchars($ilceKontrol->ilce_adi ?? '', ENT_QUOTES, 'UTF-8');
                }
            }

            // Mahalle kontrolü
            if (($row["mahalle"] ?? 0) != 0) {
                $mahaKontrol = $db->prepare("SELECT id, mahalle_adi, slug FROM mahalle_koy WHERE id = ?");
                $mahaKontrol->execute([(int)$row["mahalle"]]);
                if ($mahaKontrol->rowCount() > 0) {
                    $mahaKontrol = $mahaKontrol->fetch(PDO::FETCH_OBJ);
                    $eksq .= "t1.mahalle_id=" . (int)$mahaKontrol->id . " AND ";
                    $linki .= "-" . htmlspecialchars($mahaKontrol->slug ?? '', ENT_QUOTES, 'UTF-8');
                    $smod_adi = htmlspecialchars($mahaKontrol->mahalle_adi ?? '', ENT_QUOTES, 'UTF-8');
                }
            }

            if (!empty($smod_adi) && !empty($linki)) {
                $xemlak_durum = "";
                switch ($row['emlak_durum'] ?? '') {
                    case "satilik":
                        $xemlak_durum = $emstlk ?? ''; // Tanımsızsa boş string
                        break;
                    case "kiralik":
                        $xemlak_durum = $emkrlk ?? ''; // Tanımsızsa boş string
                        break;
                    case "gunluk_kiralik":
                        $xemlak_durum = $emgkrlk ?? ''; // Tanımsızsa boş string
                        break;
                }
                $slug_emlkdrm = $gvn->PermaLink($xemlak_durum ?? ''); // Tanımsızsa boş string
                $adet = $db->query("SELECT COUNT(t1.id) as adet FROM sayfalar AS t1 LEFT JOIN dopingler_501 AS t2 ON t2.ilan_id=t1.id AND t2.did=4 AND t2.durum=1 AND t2.btarih>NOW() WHERE (t1.btarih>NOW() OR t2.btarih>NOW() OR EXISTS (SELECT btarih FROM dopingler_501 WHERE ilan_id=t1.id AND durum=1 AND btarih>NOW())) AND t1.durum=1 AND t1.ekleme=1 AND " . $eksq . "t1.emlak_durum='" . htmlspecialchars($xemlak_durum, ENT_QUOTES, 'UTF-8') . "' AND ((t1.site_id_555=501 AND t1.durum=1 AND t1.site_id_699=0 AND t1.site_id_700=0 AND t1.site_id_701=0 AND t1.site_id_702=0) OR (t1.site_id_888=100 AND t1.durum=1 AND t1.il_id=35) OR (t1.site_id_777=501501 AND t1.durum=1) OR (t1.site_id_702=300 AND t1.durum=1)) AND t1.tipi=4")->fetch(PDO::FETCH_OBJ)->adet ?? 0;
                $linki = htmlspecialchars($slug_emlkdrm, ENT_QUOTES, 'UTF-8') . $linki;
        ?>
                <a href="<?= $linki; ?>">
                    <div class="sehirbtn fadeup">
                        <img src="uploads/<?= htmlspecialchars($row['resim'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" title="<?= $smod_adi; ?>" alt="<?= $smod_adi; ?>" width="167" height="280">
                        <div class="sehiristatistk">
                            <h1><?= $smod_adi; ?></h1>
                            <h2><?= htmlspecialchars($xemlak_durum, ENT_QUOTES, 'UTF-8'); ?></h2>
                            <h3><strong><?= htmlspecialchars((string)$adet, ENT_QUOTES, 'UTF-8'); ?></strong> <?= htmlspecialchars(dil("TX209") ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                        </div>
                    </div>
                </a>
        <?php
            }
        }
        ?>
    </div>
</div>
<!-- blok4 end -->
<?php
} // Eğer bir şeyler varsa göster
?>