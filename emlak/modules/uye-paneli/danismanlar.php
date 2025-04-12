<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

if ($hesap->turu != 1) {
    header("Location:uye-paneli");
    die();
}
?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX485"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <span><?= htmlspecialchars(dil("TX486"), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">

<div class="uyepanel">

<div class="content">

<?php
if ($gayarlar->reklamlar == 1) { // Eğer reklamlar aktif ise...
    $detect = (!isset($detect)) ? new Mobile_Detect : $detect;
    $rtipi = 11;
    $reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1)");
    $rcount = $reklamlar->rowCount();
    $order = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
    $reklam = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1) " . $order . " LIMIT 0,1")->fetch(PDO::FETCH_OBJ);
    if ($rcount > 0) {
?>
<!-- 728 x 90 Reklam Alanı -->
<div class="ad728home">
    <?= ($detect->isMobile() || $detect->isTablet()) ? $reklam->mobil_kodu : $reklam->kodu; ?>
</div>
<!-- 728 x 90 Reklam Alanı END-->
<?php
    }
} // Eğer reklamlar aktif ise...
?>

<div id="ucretler" class="modalDialog">
    <div>
        <div style="padding:20px;">
            <a href="<?= REQUEST_URL; ?>#!" title="Close" class="close">X</a>
            <h2><strong><?= htmlspecialchars(dil("TX635"), ENT_QUOTES, 'UTF-8'); ?></strong></h2>
            <span>Danışmanlarınızı öne çıkarak web site anasayfasında yayınlanabilir ve daha fazla kitleye ulaşarak tanıtım yapabilirsiniz.</span><br><br>
            <table width="100%">
                <thead>
                    <tr>
                        <th align="left"><?= htmlspecialchars(dil("TX636"), ENT_QUOTES, 'UTF-8'); ?></th>
                        <th align="left"><?= htmlspecialchars(dil("TX536"), ENT_QUOTES, 'UTF-8'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $ua = $fonk->UyelikAyarlar();
                $ucretler = $ua["danisman_onecikar_ucretler"];
                foreach ($ucretler as $ucret) {
                ?>
                    <tr>
                        <td><?= htmlspecialchars($ucret["sure"], ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($periyod[$ucret["periyod"]], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($gvn->para_str($ucret["tutar"]) . " " . dil("DONECIKAR_PBIRIMI"), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>

            <div class="clear"></div>
        </div>
    </div>
</div>

<div class="uyedetay">
<div class="uyeolgirisyap">
    <h4 class="uyepaneltitle"><?= htmlspecialchars(dil("TX485"), ENT_QUOTES, 'UTF-8'); ?> <a class="gonderbtn" href="uye-paneli?rd=danisman_ekle"><i style="margin-right:10px;" class="fa fa-plus"></i> <?= htmlspecialchars(dil("TX487"), ENT_QUOTES, 'UTF-8'); ?></a></h4>
    <a style="margin-right:10px;" class="gonderbtn" href="#ucretler"><i style="margin-right:10px;" class="fa fa-rocket" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX635"), ENT_QUOTES, 'UTF-8'); ?></a>

    <div class="clear"></div>

    <?php
    $git = $gvn->zrakam($_GET["git"]);
    $qry = $pagent->sql_query("SELECT id,concat_ws(' ',adi,soyadi) AS adsoyad,durum,olusturma_tarih,son_giris_tarih,avatar,nick_adi,onecikar,onecikar_btarih FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid={$hesap->id} ORDER BY id DESC", $git, 10);
    $query = $db->query($qry['sql']);
    $adet = $qry['toplam'];
    ?>

    <?php
    if ($adet > 0) {
    ?>
    <div id="hidden_result" style="display:none"></div>
    <table width="100%" border="0" id="uyepanelilantable">
        <tr>
            <td id="mobtd" bgcolor="#EFEFEF"><strong><?= htmlspecialchars(dil("TX487"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
            <td bgcolor="#EFEFEF"><strong><?= htmlspecialchars(dil("TX488"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
            <td align="center" bgcolor="#EFEFEF"><strong><?= htmlspecialchars(dil("TX234"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
            <td width="15%" align="center" bgcolor="#EFEFEF"><strong><?= htmlspecialchars(dil("TX235"), ENT_QUOTES, 'UTF-8'); ?></strong></td>
        </tr>

    <?php
        $onchediye = $db->query("SELECT id FROM upaketler_501 WHERE acid=" . $hesap->id . " AND durum=1 AND danisman_onecikar=1 AND danisman_onecikar_use=1");
        if ($onchediye->rowCount() < 1) {
            $hediye = $db->query("SELECT id, danisman_onecikar, danisman_onecikar_sure, danisman_onecikar_periyod FROM upaketler_501 WHERE acid=" . $hesap->id . " AND durum=1 AND danisman_onecikar=1");
            if ($hediye->rowCount() > 0) {
                $hediye = $hediye->fetch(PDO::FETCH_OBJ);
            }
        }

        $bugun = date("Y-m-d");
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
            $prolink = ($row->nick_adi == '') ? 'profil/' . $row->id : 'profil/' . $row->nick_adi;
            $otarih = date("d.m.Y H:i", strtotime($row->olusturma_tarih));
            $avatar = ($row->avatar == '') ? 'https://www.turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : 'https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/' . $row->avatar;
            $adsoyad = htmlspecialchars($row->adsoyad, ENT_QUOTES, 'UTF-8');

            if ($row->onecikar == 1) {
                $onecikarma = true;
                $siparis = $db->query("SELECT id, tarih, btarih, durum FROM onecikan_danismanlar_501 WHERE did=" . $row->id . " AND durum=1 AND btarih>NOW() ORDER BY id DESC");
                if ($siparis->rowCount() > 0) {
                    $siparis = $siparis->fetch(PDO::FETCH_OBJ);
                }

                $dkgun = $fonk->gun_farki($row->onecikar_btarih, $bugun);
                if ($dkgun < 0) {
                    $durumne = dil("TX640");
                } elseif ($dkgun == 0) {
                    $durumne = dil("TX641");
                } elseif ($dkgun > 0) {
                    $durumne = dil("TX642") . $dkgun;
                }

                $aciklama_cikarma = ($dkgun < 0) ? dil("TX637") : dil("TX638");
                $baslangic_cikarma = ($siparis->id != '') ? dil("TX639") . date("d.m.Y", strtotime($siparis->tarih)) . " /" : '';
                $bitis_cikarma = date("d.m.Y", strtotime($row->onecikar_btarih));
            } else {
                $onecikarma = false;
            }

            $bcikarma = $db->query("SELECT id FROM onecikan_danismanlar_501 WHERE did=" . $row->id . " AND durum=0 ORDER BY id DESC");
            $bcikarma = $bcikarma->rowCount();
    ?>
            <tr id="row_<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>">
                <td width="75" id="mobtd"><img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" width="75" height="75"/></td>
                <td><a target="_blank" href="<?= htmlspecialchars($prolink, ENT_QUOTES, 'UTF-8'); ?>"><h5 style="font-size:16px;margin-bottom:5px;"><strong><?= $adsoyad; ?></strong></h5></a> 
                <?php if ($bcikarma > 0) { ?>
                    <span style="font-weight:bold;color:orange">(<?= htmlspecialchars(dil("TX661"), ENT_QUOTES, 'UTF-8'); ?>)</span><br>
                <?php } elseif ($onecikarma) { ?>
                    <span style="font-weight:bold;color:orange">(<?= $aciklama_cikarma; ?>)</span><br><strong><?= $durumne; ?></strong> - <?= ($baslangic_cikarma != '' ) ? $baslangic_cikarma : ''; ?> <?= $bitis_cikarma; ?><br>
                <?php } ?>
                    <span class="ilantarih"><?= htmlspecialchars(dil("TX489"), ENT_QUOTES, 'UTF-8'); ?> <?= $otarih; ?></span>
                </td>
                <td align="center">
                <?php
                if ($row->durum == 0) {
                ?><span style="color:green;font-weight:bold;"><?= htmlspecialchars(dil("TX491"), ENT_QUOTES, 'UTF-8'); ?></span><?php
                } elseif ($row->durum == 1) {
                ?><span style="color:orange;font-weight:bold;"><?= htmlspecialchars(dil("TX490"), ENT_QUOTES, 'UTF-8'); ?></span><?php
                }
                ?>
                </td>
                <td width="15%" align="center">

                <a title="Düzenle" class="uyeilankontrolbtn" href="uye-paneli?rd=danisman_duzenle&id=<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                <div class="clearmob"></div>
                <?php if ($onecikarma == false && $bcikarma < 1) { ?>
                <a title="Öne Çıkarın" id="RoketButon<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>" class="uyeilankontrolbtn" href="javascript:void(0);" onclick="<?= ($hediye->id != '') ? "HediyeRoket(" . htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8') . "," . $hediye->danisman_onecikar_sure . ",'" . $hediye->danisman_onecikar_periyod . "');" : "RoketButon(" . htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8') . ");"; ?>"><i class="fa fa-rocket" aria-hidden="true"></i></a>
                <?php } ?>
                <div class="clearmob"></div>
                <a title="Sil" class="uyeilankontrolbtn" href="javascript:;" onclick="SilDanisman(<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>);"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </td>
            </tr>
    <?php
        }
    ?>

    </table>

    <div class="clear"></div>
    <div class="sayfalama">
    <?php echo $pagent->listele('eklenen-danismanlar?git=', $git, $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
    </div>

    <?php } else { ?> 
    <h4 style="text-align:center;margin-top:60px;"><?= htmlspecialchars(dil("TX492"), ENT_QUOTES, 'UTF-8'); ?></h4>
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

<input type="hidden" name="delete_id" id="delete_id" value="0">
<div class="remodal" data-remodal-id="DanismanSil"
  data-remodal-options="hashTracking:false,closeOnOutsideClick:false">

  <button data-remodal-action="close" class="remodal-close"></button>
  <h1><?= htmlspecialchars(dil("TX498"), ENT_QUOTES, 'UTF-8'); ?></h1>
  <p>
    <?= htmlspecialchars(dil("TX499"), ENT_QUOTES, 'UTF-8'); ?>
  </p>
  <br>
  <?php
  $secenek = explode("|", dil("TX500"));
  ?>
  <button class="remodal-confirm" onclick="DanismanSil(1);"><i class="fa fa-check" aria-hidden="true"></i> <?= htmlspecialchars($secenek[0], ENT_QUOTES, 'UTF-8'); ?></button>
  <button class="remodal-cancel" onclick="DanismanSil(0);"><i class="fa fa-times" aria-hidden="true"></i> <?= htmlspecialchars($secenek[1], ENT_QUOTES, 'UTF-8'); ?></button>
  <button data-remodal-action="close" class="remodal-cancel"><?= htmlspecialchars($secenek[2], ENT_QUOTES, 'UTF-8'); ?></button>
</div>