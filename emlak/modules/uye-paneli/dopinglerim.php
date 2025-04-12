<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX556"), ENT_QUOTES, 'UTF-8'); ?></h1>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">

<div class="uyepanel">

<div class="content">

<div class="uyedetay">
<div class="uyeolgirisyap">
    <h4 class="uyepaneltitle"><?= htmlspecialchars(dil("TX556"), ENT_QUOTES, 'UTF-8'); ?></h4>
    
    <?php
    $git = $gvn->zrakam($_GET["git"]);
    $qry = $pagent->sql_query("SELECT * FROM dopingler_group_501 WHERE acid=" . (int)$hesap->id . " ORDER BY id DESC", $git, 6);
    $query = $db->query($qry['sql']);
    $adet = $qry['toplam'];
    ?>
    <?php
    if ($adet > 0) {
    ?>
    <div id="hidden_result" style="display:none"></div>

    <div id="accordion">
    
    <?php
    $i = 0;
    $bugun = date("Y-m-d");
    while ($dop = $query->fetch(PDO::FETCH_OBJ)) {
        $i += 1;
        $ilani = $db->query("SELECT id, url, baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . (int)$dop->ilan_id)->fetch(PDO::FETCH_OBJ);
        $ilanilink = ($dayarlar->permalink == 'Evet') ? htmlspecialchars($ilani->url, ENT_QUOTES, 'UTF-8') . '.html' : 'index.php?p=sayfa&id=' . (int)$ilani->id;
    ?>
    <h3><?= htmlspecialchars($ilani->baslik, ENT_QUOTES, 'UTF-8'); ?></h3>
    <div>
        <table width="100%" border="0">
        <tr>
            <td bgcolor="#EFEFEF"><strong><?= htmlspecialchars(dil("TX557"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
            <td align="center" bgcolor="#EFEFEF"><strong><?= htmlspecialchars(dil("TX558"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
            <td align="center" bgcolor="#EFEFEF"><strong><?= htmlspecialchars(dil("TX559"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
        </tr>
        <?php
        $dopingleri = $db->query("SELECT * FROM dopingler_501 WHERE gid=" . (int)$dop->id);
        while ($row = $dopingleri->fetch(PDO::FETCH_OBJ)) {
            $kgun = $fonk->gun_farki($row->btarih, $bugun);
            $tarihi = date("d.m.Y H:i", strtotime($row->tarih));
        ?>
        <tr>
        <td><?= htmlspecialchars(dil("DOPING" . $row->did), ENT_QUOTES, 'UTF-8'); ?></td>
        <td align="center">(<?= (int)$row->sure; ?> <?= htmlspecialchars($periyod[$row->periyod], ENT_QUOTES, 'UTF-8'); ?>)<br><strong><?= htmlspecialchars($gvn->para_str($row->tutar), ENT_QUOTES, 'UTF-8'); ?></strong></td>
        <td align="center">
        <?php
        if ($row->durum == 0) {
        ?><span style="color:orange;font-weight:bold;"><?= htmlspecialchars(dil("TX560"), ENT_QUOTES, 'UTF-8'); ?></span><?php
        } elseif ($row->durum == 1) {
        ?>
        <span style="color:green;font-weight:bold;"><?= htmlspecialchars(dil("TX561"), ENT_QUOTES, 'UTF-8'); ?></span><br>
        <?php if ($kgun < 0) { ?>
        <strong style="color:red"><i class="fa fa-clock-o"></i> <?= htmlspecialchars(dil("TX562"), ENT_QUOTES, 'UTF-8'); ?></strong>
        <?php } else { ?>
        <strong><i class="fa fa-clock-o"></i> <?= ($kgun == 0) ? htmlspecialchars(dil("TX563"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($kgun, ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars(dil("TX564"), ENT_QUOTES, 'UTF-8'); ?></strong>
        <?php } ?>
        <?php
        } elseif ($row->durum == 2) {
        ?><span style="color:red;font-weight:bold;"><?= htmlspecialchars(dil("TX565"), ENT_QUOTES, 'UTF-8'); ?></span><?php
        }
        ?>
        </td>
        </tr>
        <?php } ?>
        <tr>
        <td colspan="3">
        <span style="float:left;"><?= htmlspecialchars(dil("TX566"), ENT_QUOTES, 'UTF-8'); ?>: <strong><?= date("d.m.Y H:i", strtotime($dop->tarih)); ?></strong></span>
        <span style="float:right; margin-left:20px;"><?= htmlspecialchars(dil("TX567"), ENT_QUOTES, 'UTF-8'); ?>: <strong><?= htmlspecialchars($gvn->para_str($dop->tutar), ENT_QUOTES, 'UTF-8'); ?></strong></span>
        <span style="float:right;"><?= htmlspecialchars(dil("TX568"), ENT_QUOTES, 'UTF-8'); ?>: <strong><?= htmlspecialchars($dop->odeme_yontemi, ENT_QUOTES, 'UTF-8'); ?></strong></span>
        </td>
        </tr>
        </table>
    </div>
    <?php } ?>
    
    </div><!-- tab end -->

    <div class="clear"></div>
    <div class="sayfalama">
    <?php echo $pagent->listele('dopinglerim?git=', $git, $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
    </div>

    <?php } else { ?> 
    <h4 style="text-align:center;margin-top:60px;"><?= htmlspecialchars(dil("TX569"), ENT_QUOTES, 'UTF-8'); ?></h4>
    <?php } ?>

</div>
</div>
</div>

<div class="sidebar">
<?php include THEME_DIR . "inc/uyepanel_sidebar.php"; ?>
</div>

<div class="clear"></div>

</div>

</div>