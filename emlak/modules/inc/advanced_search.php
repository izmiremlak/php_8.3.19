<?php

declare(strict_types=1); // PHP 8.3 için sıkı tip kontrolü

// Hata raporlamasını etkinleştir ve hem log dosyasına hem ekrana yaz
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error_log.txt');
error_reporting(E_ALL);

// Özel hata işleyici: Hataları hem log’a hem ekrana yaz (tekrar tanımlamayı önle)
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

// Formdan gelen değişkenleri tanımlı hale getir (undefined variable hatasını önlemek için)
$q = $_POST['q'] ?? '';
$emlak_durum = $_POST['emlak_durum'] ?? '';
$emlak_tipi = $_POST['emlak_tipi'] ?? '';
$konut_tipi = $_POST['konut_tipi'] ?? '';
$konut_sekli = $_POST['konut_sekli'] ?? '';
$ulke_id = $_POST['ulke_id'] ?? '';
$il = $_POST['il'] ?? '';
$ilce = $_POST['ilce'] ?? '';
$mahalle = $_POST['mahalle'] ?? '';
$bulundugu_kat = $_POST['bulundugu_kat'] ?? '';
$min_fiyat = $_POST['min_fiyat'] ?? 0;
$max_fiyat = $_POST['max_fiyat'] ?? 0;
$min_metrekare = $_POST['min_metrekare'] ?? 0;
$max_metrekare = $_POST['max_metrekare'] ?? 0;
$min_bina_kat_sayisi = $_POST['min_bina_kat_sayisi'] ?? 0;
$max_bina_kat_sayisi = $_POST['max_bina_kat_sayisi'] ?? 0;
$yapi_durum = $_POST['yapi_durum'] ?? '';
$kaks_emsal = $_POST['kaks_emsal'] ?? '';
$gabari = $_POST['gabari'] ?? '';
$imar_durum = $_POST['imar_durum'] ?? '';
$tapu_durumu = $_POST['tapu_durumu'] ?? '';
$katk = $_POST['katk'] ?? '';
$krediu = $_POST['krediu'] ?? '';
$takas = $_POST['takas'] ?? '';
$ilan_tarih = $_POST['ilan_tarih'] ?? '';
$resimli = $_POST['resimli'] ?? ''; // Satır 407 için tanımlama
$videolu = $_POST['videolu'] ?? ''; // Satır 411 için tanımlama
$orderg = $_POST['order'] ?? '';
$sicak = $_POST['sicak'] ?? '';
$vitrin = $_POST['vitrin'] ?? '';
$onecikan = $_POST['onecikan'] ?? '';

?>

<div class="altbaslik">
    <h4 id="mobileMenuToggle">
        <i class="fa fa-search" aria-hidden="true"></i> <strong><?= htmlspecialchars(dil("TX51") ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
    </h4>
</div>

<div class="gelismisara">
    <form action="ajax.php?p=ilanlar" method="POST" id="IlanlarAramaForm">
        <!-- Arama metni girişi -->
        <input name="q" type="text" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?= htmlspecialchars(dil("TX52") ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <?php
        // Emlak Durumu dropdown
        $emlkdrm = htmlspecialchars(dil("EMLK_DRM") ?? '', ENT_QUOTES, 'UTF-8');
        if ($emlkdrm != '') {
        ?>
            <select name="emlak_durum">
                <option value=""><?= htmlspecialchars(dil("TX53") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $parc = explode("<+>", $emlkdrm);
                foreach ($parc as $val) {
                ?>
                    <option <?= ($val == $emlak_durum) ? 'selected' : ''; ?>><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <?php
        // Emlak Tipi dropdown ve dinamik JavaScript kontrolü
        $emlktp = htmlspecialchars(dil("EMLK_TIPI") ?? '', ENT_QUOTES, 'UTF-8');
        if ($emlktp != '') {
        ?>
            <select name="emlak_tipi" onchange="konut_getir(this.options[this.selectedIndex].value);">
                <option value=""><?= htmlspecialchars(dil("TX54") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $parc = explode("<+>", $emlktp);
                $isyeri = $parc[1] ?? '';
                $arsa = $parc[2] ?? '';
                foreach ($parc as $val) {
                ?>
                    <option <?= ($val == $emlak_tipi) ? 'selected' : ''; ?>><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>

            <script type="text/javascript">
                function emlak_tipi(v) {
                    if (v === "<?= htmlspecialchars($arsa, ENT_QUOTES, 'UTF-8'); ?>") {
                        $(".konut").slideUp(function() {
                            $(".arsa").slideDown();
                        });
                    } else {
                        $(".arsa").slideUp(function() {
                            $(".konut").slideDown();
                        });
                    }
                }

                $(document).ready(function() {
                    emlak_tipi($("select[name=emlak_tipi]").val());
                    $("select[name=emlak_tipi]").change(function() {
                        emlak_tipi($(this).val());
                        if ($(this).val() === "<?= htmlspecialchars($isyeri, ENT_QUOTES, 'UTF-8'); ?>") {
                            $("select[name=konut_tipi] option:eq(0)").text("<?= htmlspecialchars($isyeri . " " . dil("TX666"), ENT_QUOTES, 'UTF-8'); ?>");
                            $("select[name=konut_sekli] option:eq(0)").text("<?= htmlspecialchars($isyeri . " " . dil("TX667"), ENT_QUOTES, 'UTF-8'); ?>");
                        } else {
                            $("select[name=konut_tipi] option:eq(0)").text("<?= htmlspecialchars(dil("TX57") ?? '', ENT_QUOTES, 'UTF-8'); ?>");
                            $("select[name=konut_sekli] option:eq(0)").text("<?= htmlspecialchars(dil("TX58") ?? '', ENT_QUOTES, 'UTF-8'); ?>");
                        }
                    });
                });
            </script>
        <?php } ?>

        <?php
        // Konut Tipi dropdown
        if ($emlak_tipi == $isyeri) {
            $knttipi = htmlspecialchars(dil("KNT_TIPI2") ?? '', ENT_QUOTES, 'UTF-8');
        } else {
            $knttipi = htmlspecialchars(dil("KNT_TIPI") ?? '', ENT_QUOTES, 'UTF-8');
        }
        if ($knttipi != '') {
        ?>
            <select name="konut_tipi" class="konut">
                <option value=""><?= htmlspecialchars(dil("TX57") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $parc = explode("<+>", $knttipi);
                foreach ($parc as $val) {
                ?>
                    <option <?= ($val == $konut_tipi) ? 'selected' : ''; ?>><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <?php
        // Konut Şekli dropdown
        $kntsekli = htmlspecialchars(dil("KNT_SEKLI") ?? '', ENT_QUOTES, 'UTF-8');
        if ($kntsekli != '') {
        ?>
            <select name="konut_sekli" class="konut">
                <option value=""><?= htmlspecialchars(dil("TX58") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $parc = explode("<+>", $kntsekli);
                foreach ($parc as $val) {
                ?>
                    <option <?= ($val == $konut_sekli) ? 'selected' : ''; ?>><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <?php
        // Ülke Seçimi dropdown
        $ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
        $ulkelerc = $ulkeler->rowCount();
        if ($ulkelerc > 1) {
            if ($il != '' && $il != 0) {
                $yakalail = $db->prepare("SELECT ulke_id FROM il WHERE id = ?");
                $yakalail->execute([(int)$il]);
                $yakalail = $yakalail->rowCount() > 0 ? $yakalail->fetch(PDO::FETCH_OBJ) : null;
            }
        ?>
            <select name="ulke_id" onchange="ajaxHere('ajax.php?p=il_getir&ulke_id=' + this.options[this.selectedIndex].value, 'il');">
                <option value=""><?= htmlspecialchars(dil("TX348") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                while ($row = $ulkeler->fetch(PDO::FETCH_OBJ)) {
                ?>
                    <option value="<?= (int)$row->id; ?>" <?= (($yakalail->ulke_id ?? '') == $row->id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->ulke_adi, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <select class="yariminpt" name="il" id="il" onchange="ajaxHere('ajax.php?p=ilce_getir&varsa=1&il_id=' + this.options[this.selectedIndex].value, 'ilce');">
            <option value=""><?= htmlspecialchars(dil("TX55") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
            if ($ulkelerc < 2) {
                $ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
                $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id = " . (int)($ulke->id ?? 0) . " ORDER BY id ASC");
            } elseif ($yakalail != null) {
                $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id = " . (int)($yakalail->ulke_id ?? 0) . " ORDER BY id ASC");
            } else {
                $sql = null;
            }
            if ($sql != null) {
                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                    if ($row->id == $il) {
                        $il_adi = $row->il_adi;
                    }
            ?>
                    <option value="<?= (int)$row->id; ?>" <?= ($row->id == $il) ? 'selected' : ''; ?>><?= htmlspecialchars($row->il_adi, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
                }
            }
            ?>
        </select>

        <select name="ilce" id="ilce" onchange="ajaxHere('ajax.php?p=mahalle_getir&ilce_id=' + this.options[this.selectedIndex].value, 'mahalle');">
            <option value=""><?= htmlspecialchars(dil("TX56") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
            if ($il != '') {
                $sql = $db->prepare("SELECT id, ilce_adi FROM ilce WHERE il_id = ? ORDER BY id ASC");
                $sql->execute([(int)$il]);
                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
            ?>
                    <option value="<?= (int)$row->id; ?>" <?= ($row->id == $ilce) ? 'selected' : ''; ?>><?= htmlspecialchars($row->ilce_adi, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
                }
            }
            ?>
        </select>

        <select name="mahalle" id="mahalle">
            <option value=""><?= htmlspecialchars(dil("TX266") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
            if ($ilce != '') {
                $semtler = $db->query("SELECT * FROM semt WHERE ilce_id = " . (int)$ilce);
                if ($semtler->rowCount() > 0) {
                    while ($srow = $semtler->fetch(PDO::FETCH_OBJ)) {
                        $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE semt_id = " . (int)$srow->id . " AND ilce_id = " . (int)$ilce . " ORDER BY mahalle_adi ASC");
                        if ($mahalleler->rowCount() > 0) {
            ?>
                            <optgroup label="<?= htmlspecialchars($srow->semt_adi, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php
                                while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                                ?>
                                    <option value="<?= (int)$row->id; ?>" <?= ($mahalle == $row->id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->mahalle_adi, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php
                                }
                                ?>
                            </optgroup>
                        <?php
                        }
                    }
                } else {
                    $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE ilce_id = " . (int)$ilce . " ORDER BY mahalle_adi ASC");
                    while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                        ?>
                        <option value="<?= (int)$row->id; ?>" <?= ($mahalle == $row->id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->mahalle_adi, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
                    }
                }
            }
            ?>
        </select>

        <?php
        // Bulunduğu Kat dropdown
        $bulundkat = htmlspecialchars(dil("BULND_KAT") ?? '', ENT_QUOTES, 'UTF-8');
        if ($bulundkat != '') {
        ?>
            <select name="bulundugu_kat" class="konut">
                <option value=""><?= htmlspecialchars(dil("TX59") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $parc = explode("<+>", $bulundkat);
                foreach ($parc as $val) {
                ?>
                    <option <?= ($val == $bulundugu_kat) ? 'selected' : ''; ?>><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <!-- Fiyat ve metrekare girişleri -->
        <input name="min_fiyat" type="text" value="<?= ($min_fiyat != 0) ? htmlspecialchars($min_fiyat, ENT_QUOTES, 'UTF-8') : ''; ?>" id="yariminpt" placeholder="<?= htmlspecialchars(dil("TX60") ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <input name="max_fiyat" type="text" value="<?= ($max_fiyat != 0) ? htmlspecialchars($max_fiyat, ENT_QUOTES, 'UTF-8') : ''; ?>" id="yariminpt" placeholder="<?= htmlspecialchars(dil("TX61") ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <input name="min_metrekare" type="text" value="<?= ($min_metrekare != 0) ? htmlspecialchars($min_metrekare, ENT_QUOTES, 'UTF-8') : ''; ?>" id="yariminpt" placeholder="<?= htmlspecialchars(dil("TX62") ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <input name="max_metrekare" type="text" value="<?= ($max_metrekare != 0) ? htmlspecialchars($max_metrekare, ENT_QUOTES, 'UTF-8') : ''; ?>" id="yariminpt" placeholder="<?= htmlspecialchars(dil("TX63") ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <input name="min_bina_kat_sayisi" type="text" value="<?= ($min_bina_kat_sayisi != 0) ? htmlspecialchars($min_bina_kat_sayisi, ENT_QUOTES, 'UTF-8') : ''; ?>" id="yariminpt" placeholder="<?= htmlspecialchars(dil("TX64") ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <input name="max_bina_kat_sayisi" type="text" value="<?= ($max_bina_kat_sayisi != 0) ? htmlspecialchars($max_bina_kat_sayisi, ENT_QUOTES, 'UTF-8') : ''; ?>" id="yariminpt" placeholder="<?= htmlspecialchars(dil("TX65") ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <?php
        // Yapı Durumu dropdown
        $yapidrm = htmlspecialchars(dil("YAPI_DRM") ?? '', ENT_QUOTES, 'UTF-8');
        if ($yapidrm != '') {
        ?>
            <select name="yapi_durum" class="konut">
                <option value=""><?= htmlspecialchars(dil("TX66") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $parc = explode("<+>", $yapidrm);
                foreach ($parc as $val) {
                ?>
                    <option <?= ($val == $yapi_durum) ? 'selected' : ''; ?>><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <?php
        // Kaks Emsal dropdown
        $kaks_emsaller = htmlspecialchars(dil("KAKS_EMSAL") ?? '', ENT_QUOTES, 'UTF-8');
        if ($kaks_emsaller != '') {
        ?>
            <select name="kaks_emsal" class="arsa">
                <option value=""><?= htmlspecialchars(dil("TX331") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $exps = explode("<+>", $kaks_emsaller);
                foreach ($exps as $value) {
                ?>
                    <option <?= ($kaks_emsal == $value) ? 'selected' : ''; ?>><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <?php
        // Gabari dropdown
        $gabariler = htmlspecialchars(dil("GABARI") ?? '', ENT_QUOTES, 'UTF-8');
        if ($gabariler != '') {
        ?>
            <select name="gabari" class="arsa">
                <option value=""><?= htmlspecialchars(dil("TX332") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $exps = explode("<+>", $gabariler);
                foreach ($exps as $value) {
                ?>
                    <option <?= ($gabari == $value) ? 'selected' : ''; ?>><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <?php
        // İmar Durumu dropdown
        $imar_durumlari = htmlspecialchars(dil("IMAR_DURUM") ?? '', ENT_QUOTES, 'UTF-8');
        if ($imar_durumlari != '') {
        ?>
            <select name="imar_durum" class="arsa">
                <option value=""><?= htmlspecialchars(dil("TX682") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $exps = explode("<+>", $imar_durumlari);
                foreach ($exps as $value) {
                ?>
                    <option <?= ($imar_durum == $value) ? 'selected' : ''; ?>><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <?php
        // Tapu Durumu dropdown
        $tapu_durumlari = htmlspecialchars(dil("TAPU_DRM") ?? '', ENT_QUOTES, 'UTF-8');
        if ($tapu_durumlari != '') {
        ?>
            <select name="tapu_durumu" class="arsa">
                <option value=""><?= htmlspecialchars(dil("TX333") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                $exps = explode("<+>", $tapu_durumlari);
                foreach ($exps as $value) {
                ?>
                    <option <?= ($tapu_durumu == $value) ? 'selected' : ''; ?>><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <!-- Katkı ve takas seçenekleri -->
        <select name="katk" class="arsa">
            <option value=""><?= htmlspecialchars(dil("TX334") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option <?= ($katk == htmlspecialchars(dil("TX167") ?? '', ENT_QUOTES, 'UTF-8')) ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX167") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option <?= ($katk == htmlspecialchars(dil("TX168") ?? '', ENT_QUOTES, 'UTF-8')) ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX168") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
        </select>

        <?php
        if (htmlspecialchars(dil("TX335") ?? '', ENT_QUOTES, 'UTF-8') != '') {
            $exp = explode(",", htmlspecialchars(dil("TX653") ?? '', ENT_QUOTES, 'UTF-8'));
        ?>
            <select name="krediu" class="arsa">
                <option value=""><?= htmlspecialchars(dil("TX335") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                foreach ($exp as $row) {
                ?>
                    <option <?= ($krediu == $row) ? 'selected' : ''; ?>><?= htmlspecialchars($row, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php
                }
                ?>
            </select>
        <?php } ?>

        <select name="takas" class="arsa">
            <option value=""><?= htmlspecialchars(dil("TX336") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option <?= ($takas == htmlspecialchars(dil("TX167") ?? '', ENT_QUOTES, 'UTF-8')) ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX167") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option <?= ($takas == htmlspecialchars(dil("TX168") ?? '', ENT_QUOTES, 'UTF-8')) ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX168") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
        </select>

        <!-- İlan tarihi dropdown -->
        <select name="ilan_tarih">
            <option value=""><?= htmlspecialchars(dil("TX67") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="bugun" <?= ($ilan_tarih == "bugun") ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX68") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="son3" <?= ($ilan_tarih == "son3") ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX69") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="son7" <?= ($ilan_tarih == "son7") ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX70") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="son14" <?= ($ilan_tarih == "son14") ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX71") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="son21" <?= ($ilan_tarih == "son21") ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX72") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="son1ay" <?= ($ilan_tarih == "son1ay") ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX73") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="son2ay" <?= ($ilan_tarih == "son2ay") ? 'selected' : ''; ?>><?= htmlspecialchars(dil("TX74") ?? '', ENT_QUOTES, 'UTF-8'); ?></option>
        </select>

        <div class="clear"></div>

        <!-- Checkbox’lar -->
        <input style="width:30px;" id="resimli" class="checkbox-custom" name="resimli" value="true" type="checkbox" <?= ($resimli == "true") ? 'checked' : ''; ?>>
        <label for="resimli" class="checkbox-custom-label" style="margin-top:7px;"><span class="checktext"><?= htmlspecialchars(dil("TX613") ?? '', ENT_QUOTES, 'UTF-8'); ?></span></label>
        <div class="clear"></div>

        <input style="width:30px;" id="videolu" class="checkbox-custom" name="videolu" value="true" type="checkbox" <?= ($videolu == "true") ? 'checked' : ''; ?>>
        <label for="videolu" class="checkbox-custom-label"><span class="checktext"><?= htmlspecialchars(dil("TX614") ?? '', ENT_QUOTES, 'UTF-8'); ?></span></label>
        <div class="clear"></div>
        <br />

        <!-- Arama butonu -->
        <a href="javascript:;" onclick="AjaxFormS('IlanlarAramaForm','IlanlarAramaForm_sonuc');" class="gonderbtn"><i class="fa fa-search" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX75") ?? '', ENT_QUOTES, 'UTF-8'); ?></a>

        <!-- Gizli input’lar -->
        <input type="hidden" name="order" value="<?= htmlspecialchars($orderg, ENT_QUOTES, 'UTF-8'); ?>" />
        <?php if ($sicak == "true") { ?>
            <input type="hidden" name="sicak" value="true" />
        <?php } ?>
        <?php if ($vitrin == "true") { ?>
            <input type="hidden" name="vitrin" value="true" />
        <?php } ?>
        <?php if ($onecikan == "true") { ?>
            <input type="hidden" name="onecikan" value="true" />
        <?php } ?>
    </form>

    <!-- Konut tipi dinamik güncelleme -->
    <script type="text/javascript">
        function konut_getir(tipi) {
            if (tipi === "<?= htmlspecialchars($isyeri, ENT_QUOTES, 'UTF-8'); ?>") {
                $("select[name=konut_tipi]").html("<?
                                                    $knttipi = htmlspecialchars(dil("KNT_TIPI2") ?? '', ENT_QUOTES, 'UTF-8');
                                                    ?><option value=''><?= htmlspecialchars(dil("TX57") ?? '', ENT_QUOTES, 'UTF-8'); ?></option><?
                                                                                                    $parc = explode("<+>", $knttipi);
                                                                                                    foreach ($parc as $val) {
                                                                                                    ?><option><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></option><?
                                                                                                                                            }
                                                                                                                                                ?>");
            } else {
                $("select[name=konut_tipi]").html("<?
                                                    $knttipi = htmlspecialchars(dil("KNT_TIPI") ?? '', ENT_QUOTES, 'UTF-8');
                                                    ?><option value=''><?= htmlspecialchars(dil("TX57") ?? '', ENT_QUOTES, 'UTF-8'); ?></option><?
                                                                                                    $parc = explode("<+>", $knttipi);
                                                                                                    foreach ($parc as $val) {
                                                                                                    ?><option><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></option><?
                                                                                                                                            }
                                                                                                                                                ?>");
            }
        }
    </script>

    <div id="IlanlarAramaForm_sonuc" style="display:none"></div>
</div>