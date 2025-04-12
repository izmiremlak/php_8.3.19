<?php
// Rastgele ilan numarası oluştur
$ilan_no = random_int(10000000, 99999999);

// GET parametresinden id değerini al ve doğrula
$id = $gvn->rakam($_GET["id"]);
if ($id != '') {
    // Veritabanından sayfa bilgilerini çek
    $snc = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND id = ? AND tipi = 4");
    $snc->execute([$id]);
    
    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ);

        // Hesap bilgilerini çek
        $acc = $db->query("SELECT id, kid FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = " . $snc->acid)->fetch(PDO::FETCH_OBJ);
        $kid = $acc->kid;

        // Kullanıcı yetkisini kontrol et
        if ($snc->acid != $hesap->id && $hesap->id != $kid) {
            header("Location: ilan-olustur");
            exit;
        }

    } else {
        header("Location: ilan-olustur");
        exit;
    }
}

// Dil bilgilerini çek
$dilx = $db->query("SELECT * FROM diller_501 WHERE kisa_adi = ?", [$dil])->fetch(PDO::FETCH_OBJ);

// Paket için gerekli kontroller start
if ($hesap->kid == 0 && $hesap->turu == 0) { // Bireysel
    $acids = $hesap->id;
    $pkacid = $acids;
} elseif ($hesap->kid == 0 && $hesap->turu == 1) { // Kurumsal
    $dids = $db->query("SELECT kid, id, GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid = " . $hesap->id)->fetch(PDO::FETCH_OBJ);
    $danismanlar = $dids->danismanlar;
    $acids = ($danismanlar == '') ? $hesap->id : $hesap->id . ',' . $danismanlar;
    $pkacid = $hesap->id;
} elseif ($hesap->kid != 0 && $hesap->turu == 2) { // Danışman
    $kurumsal = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = " . $hesap->kid);
    if ($kurumsal->rowCount() > 0) {
        $kurumsal = $kurumsal->fetch(PDO::FETCH_OBJ);
        $dids = $db->query("SELECT kid, id, GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid = " . $kurumsal->id)->fetch(PDO::FETCH_OBJ);
        $danismanlar = $dids->danismanlar;
        $acids = ($danismanlar == '') ? $kurumsal->id : $kurumsal->id . ',' . $danismanlar;
        $pkacid = $kurumsal->id;
    } else {
        $acids = $hesap->id;
        $pkacid = $acids;
    }
} else {
    $acids = $hesap->id;
    $pkacid = $acids;
}
// Paket için gerekli kontroller end

// Hesap bilgilerini paket bilgileri ile güncelle
$aylik_ilan_limit = $hesap->aylik_ilan_limit;
$ilan_resim_limit = $hesap->ilan_resim_limit;
$ilan_yayin_sure = $hesap->ilan_yayin_sure;
$ilan_yayin_periyod = $hesap->ilan_yayin_periyod;

if (isset($kurumsal) && $kurumsal) {
    $aylik_ilan_limit = $kurumsal->aylik_ilan_limit;
    $ilan_resim_limit = $kurumsal->ilan_resim_limit;
    $ilan_yayin_sure = $kurumsal->ilan_yayin_sure;
    $ilan_yayin_periyod = $kurumsal->ilan_yayin_periyod;
}

$paketi = $db->query("SELECT * FROM upaketler_501 WHERE acid = ? AND durum = 1 AND btarih > NOW()", [$pkacid]);
if ($paketi->rowCount() > 0) {
    $paketi = $paketi->fetch(PDO::FETCH_OBJ);
    $aylik_ilan_limit = ($paketi->aylik_ilan_limit == 0) ? 99999 : $paketi->aylik_ilan_limit;
    $ilan_resim_limit = ($paketi->ilan_resim_limit == 0) ? 99999 : $paketi->ilan_resim_limit;
    $ilan_yayin_sure = ($paketi->ilan_yayin_sure == 0) ? 120 : $paketi->ilan_yayin_sure;
    $ilan_yayin_periyod = ($paketi->ilan_yayin_sure == 0) ? "yillik" : $paketi->ilan_yayin_periyod;
    $paketegore = "AND pid = " . $paketi->id . " ";
}

$buay = date("Y-m");
$aylik_ilan_limit -= $db->query("SELECT tarih, id FROM sayfalar WHERE site_id_555=501 AND ekleme = 1 AND tipi = 4 " . $paketegore . "AND acid IN (" . $acids . ") AND tarih LIKE ?", ["%" . $buay . "%"])->rowCount();

?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . $gayarlar->belgeler_resim . ');"' : ''; ?>>
<div id="wrapper">
<div class="headtitle">
<h1><?= dil("TX297"); ?></h1>
<div class="sayfayolu">
<span><?= dil("TX298"); ?></span>
</div>
</div>
</div>
<div class="headerwhite"></div>
</div>

<style>
#doping_ekle input {width:auto;box-shadow:none;}
#doping_ekle label {cursor:pointer}
#doping_ekle table tr td {padding:10px;font-size:14px;}
#doping_ekle table tr td span {font-size:13px;}
</style>

<div id="wrapper">
<div class="uyepanel">
<div class="content" id="bigcontent">
<div class="uyedetay">
<div class="uyeolgirisyap">
<!-- İlan Aşamaları start -->
<div class="asamaline"></div>
<div class="ilanasamalar">
<div class="ilanasamax"><center><h3>1</h3><div class="clear"></div><?= dil("TX527"); ?></center></div>
<div class="ilanasamax"><center><h3>2</h3><div class="clear"></div><?= dil("TX528"); ?></center></div>
<div class="ilanasamax"><center><h3>3</h3><div class="clear"></div><?= dil("TX530"); ?></center></div>
<?php if ($gayarlar->dopingler_501 == 1) { ?>
<div class="ilanasamax"><center><h3>4</h3><div class="clear"></div><?= dil("TX531"); ?></center></div>
<?php } ?>
<div class="ilanasamax islem_tamam"><center><h3><?= ($gayarlar->dopingler_501 == 1) ? 5 : 4; ?></h3><div class="clear"></div><?= dil("TX532"); ?></center></div>
</div>
<!-- İlan Aşamaları END -->

<?php
if ($id != '') {
    $asama = $gvn->zrakam($_GET["asama"]);

    if ($asama == 0) { // aşama 0 foto galeri ayarı...
        $gurl = "ajax.php?p=ilan_galeri_resim_yukle&id=" . $id;

        $yfotolar = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id = " . $snc->id . " ORDER BY sira ASC");
        $yfotolarcnt = $yfotolar->rowCount();
        $resim_limit = $ilan_resim_limit;
        $ilan_resim_limit -= $yfotolarcnt;
        ?>
<script type="text/javascript">
$(document).ready(function(){
    $(".ilanasamax:eq(1)").attr("id","asamaaktif");
});
</script>

<div id="galeri_foto_ekle">

<script type="text/javascript">
function YuklemeBitti(){
    window.location.href = "ilan-olustur?id=<?=$id;?>";
}
</script>

<h4 style="font-weight:bold;margin-bottom:20px;color:<?=$gayarlar->renk2;?>;font-size:18px;"><?= dil("TX338"); ?></h4>
<div class="alert alert-info" role="alert"><?= str_replace("[ilan_resim_limit]", $ilan_resim_limit, dil("TX339")); ?></div>
<div class="clear"></div>
<br />
<div class="m-b-30">
    <form action="#" class="dropzone" id="dropzone">
        <div class="fallback">
            <input name="file" type="file" multiple="multiple">
        </div>
    </form>
</div>

<div class="clear"></div>
<br />

<h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= dil("TX340"); ?></h4>
<div class="alert alert-info" role="alert"><?= dil("TX341"); ?></div>

<?php if ($ilan_resim_limit < 1) { ?>
<div class="alert-error"><?= str_replace("[resim_limit]", $resim_limit, dil("TX612")); ?></div>
<div class="clear"></div>
<?php } ?>

<br>

<?php
$detect = new Mobile_Detect;
$isMobile = $detect->isMobile() || $detect->isTablet();
?>

<form action="ajax.php?p=galeri_foto_guncelle&ilan_id=<?=$snc->id;?>&from=insert<?php echo($yfotolarcnt > 0) ? '&photos=1' : '&photos=0'; ?>" method="POST" id="ilanGaleriFotolar">
    <div class="ilandigerfotolar" id="yuklenen_fotolar">
    <ul id="list" class="uk-nestable" data-uk-nestable="{maxDepth:1}">
    <?php
    if ($yfotolarcnt > 0) {
        $i = 0;
        while ($row = $yfotolar->fetch(PDO::FETCH_OBJ)) {
            $i += 1;
            ?>
    <li class="uk-nestable-item" id="xrow_<?=$row->id;?>" data-id="<?=$i;?>" data-idi="<?=$row->id;?>">
    <div class="ilandetayfotos gallery">
    <?php if (!$isMobile) { ?>
    <div class="ilanfototasi"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div>
    <?php } ?>
    <div class="clear"></div>
    <a rel="prettyPhoto[gallery1]" href="https://www.turkiyeemlaksitesi.com.tr/uploads/<?=$row->resim;?>" target="_blank"><img src="https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$row->resim;?>" width="100" height="75"></a>
    <a title="Sil" class="ilanfotosil" href="javascript:ajaxHere('ajax.php?p=resim_sil&ilan_id=<?=$snc->id;?>&resim_id=<?=$row->id;?>','ilanGaleriFotolar_output');" id="xrowd_<?=$row->id;?>"><i class="fa fa-trash"></i></a>
    <a style="z-index:33;" title="Döndür" id="ilandondur" class="ilanfotosil" href='javascript:window.open("<?=SITE_URL . "rotate/" . $row->id;?>", "mywindow","status=1,toolbar=0,resizable=1,width="+window.innerWidth+",height="+window.innerHeight+100+"").moveTo(0, 0);'><i class="fa fa-repeat"></i></a>
    <div style="position:absolute;font-size:13px;">
    <input type="radio" id="kapak-<?php echo $row->id; ?>" class="radio-custom" name="kapak" value="<?=$row->resim;?>" <?=($row->resim == $snc->resim) ? 'checked' : '';?> style="width:100px;">
    <label for="kapak-<?php echo $row->id; ?>" class="radio-custom-label"><span class="checktext"><?= dil("TX342"); ?></span></label>
    </div>
    <div class="clear"></div>
    </div>
    </li>
    <?php } ?>
    <?php } ?>
    </ul><!-- uk content nestable end -->
    </div>
    <div class="clear"></div>
    <hr style="border: 1px solid #eee;">
    <br>
    <div class="clear"></div>
    <div align="right">
    <div id="ilanGaleriFotolar_output" style="display:none"></div>
    </div>
</form>

<style>
.ilandetayfotos { float: left; margin: 5px; height: 115px; position: relative; }
.ilandetayfotos img { box-shadow: 0px 0px 5px #b1b1b1; }
.ilanfotosil { margin-top: 0px; left: 0px; position: absolute; background: rgba(0,0,0,0.3); color: white; padding: 5px; padding-left: 10px; padding-right: 10px; font-size: 16px; border-radius: 2px; cursor: pointer; }
.ilanfotosil:hover { background: red; color: white; }
#ilandondur { margin: 0px; right: 0px; left: auto; }
.ilanfototasi { filter: alpha(opacity=80); cursor: -webkit-grabbing; margin-top: 60px; right: 0px; position: absolute; background: rgba(0,0,0,0.3); color: white; padding: 5px; padding-left: 10px; padding-right: 10px; font-size: 16px; border-radius: 2px; cursor: pointer; }
</style>


<div align="right">
    <a class="btn" href="javascript:AjaxFormS('ilanGaleriFotolar','ilanGaleriFotolar_output');"><?=dil("TX344");?> <i class="fa fa-arrow-right"></i></a>
</div>

<link href="modules/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    var gurl = "<?=$gurl;?>";
    var max_dosya = <?=$ilan_resim_limit;?>;
</script>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="modules/dropzone/dist/dropzone_galeri.js"></script>

</div><!-- galeri foto ekle end -->

<?php
} // aşama 0 foto galeri ayarı...

if ($asama == 1) { // video sistemi start...
?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".ilanasamax:eq(2)").attr("id","asamaaktif");
    });
</script>
<div id="galeri_video_ekle">
    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX444");?></h4>
    <div class="alert alert-info" role="alert"><?=dil("TX4571");?></div>

    <div style="height:200px;float:left;width:100%;">
        <form action="ajax.php?p=galeri_video_guncelle&ilan_id=<?=$snc->id;?>&from=insert<?php echo($snc->video != '') ? '&video=1' : '&video=0';?>" method="POST" id="VideoForm" enctype="multipart/form-data">
            <center><input type="file" name="video" id="VideoSec" /></center>
            <div class="clear"></div>
        </form>

        <div class="yuklebar" id="YuklemeBar" style="display:none">
            <span id="percent">0%</span>
            <div class="yuklebarasama animated flash" id="YuklemeDurum"></div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <hr style="border: 1px solid #eee;">
    <br>
    <div align="right">
        <a style="margin-left: 15px;" class="btn" href="javascript:YuklemeBaslat();"><?=dil("TX442");?> <i class="fa fa-cloud-upload" aria-hidden="true"></i></a>  
        <a class="btn" href="javascript:atla();"><?=dil("TX443");?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
    </div>
    <div class="clear"></div>
    <div align="right"><br>
        <div id="VideoForm_output" style="display:none"></div>
    </div>
</div><!-- galeri video ekle end -->

<script type="text/javascript">
// Dosya boyutunu kontrol eden fonksiyon
function DosyaBoyutu(input_id) {
    var input, file;
    if (!window.FileReader) {
        console.log("File API'si bu tarayıcıda henüz desteklenmiyor.");
        return 0;
    } else {
        input = document.getElementById(input_id);
        if (!input) {
            console.log("Hm, ilgili input elementi yerinde yok :)");
            return 0;
        } else if (!input.files) {
            console.log("Bu tarayıcı dosya girdilerinin `files` özelliğini desteklemiyor gibi görünüyor.");
            return 0;
        } else if (!input.files[0]) {
            console.log("Dosya seçilmemiş görünüyor.");
            return 0;
        } else {
            file = input.files[0];
            return file.size;
        }
    }
}

// Dosya uzantısını kontrol eden fonksiyon
function DosyaUzantiKontrol(input_id, Uzantilar) {
    var oInput = document.getElementById(input_id);
    if (oInput.type == "file") {
        var sFileName = oInput.value;
        if (sFileName.length > 0) { // eğer dosya seçilmişse
            var blnValid = false;
            for (var j = 0; j < Uzantilar.length; j++) { // izin verilen uzantılar döndürüyoruz...
                var sCurExtension = Uzantilar[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            } // izin verilen uzantılar döndürüyoruz...
                
            if (blnValid) { // eğer uzantı geçerliyse
                return true;
            } else { // eğer uzantı geçersiz ise...
                console.log("Sorry, " + sFileName + " is invalid, allowed extensions are: " + Uzantilar.join(", "));
                return false;
            } // eğer uzantı geçersiz ise...
        } else { // Eğer dosya seçilmemiş ise
            return false;
        } // Eğer dosya seçilmemiş ise
    } else { // Tipi dosya değil ise
        return false;
    } // Tipi dosya değil ise
}

// Video yükleme işlemini başlatan fonksiyon
function YuklemeBaslat(){
    var video = $("#VideoSec")[0].files.length;

    if (video < 1) {
        $("#VideoForm_output").html("<span class='error'><?=dil("TX454");?></span>");
        $("#VideoForm_output").fadeIn(600);
    } else {
        var videoSize = DosyaBoyutu('VideoSec');
        var VideoValid = DosyaUzantiKontrol('VideoSec',['.mp4']);

        if (videoSize > <?=dil("VIDEO_MAX_BAYT");?>) {
            $("#VideoForm_output").html("<span class='error'><?=dil("TX455");?></span>");
            $("#VideoForm_output").fadeIn(600);
        } else if (!VideoValid) {
            $("#VideoForm_output").html("<span class='error'><?=dil("TX456");?></span>");
            $("#VideoForm_output").fadeIn(600);
        } else { // Eğer video boyutu çok değilse
            $("#VideoForm_output").fadeOut(400);

            $("#VideoForm").slideUp(400,function(){
                $("#YuklemeBar").slideDown(400);
            });

            var bar = $('#YuklemeDurum');
            var percent = $('#percent');
            $("#VideoForm").ajaxForm({
                target: '#VideoForm_output',
                beforeSend: function() {
                    percent.attr("style","");
                    var percentVal = '0%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percent_style = percent.attr("style");
                    if (percent_style == undefined) {
                        percent_style = '';
                    }
                    if (percentComplete >= 47 && percent_style == '') {
                        percent.attr("style","color:#FFF;z-index:3;position:relative;");
                    }
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                complete: function() {
                    $("#VideoForm_output").fadeIn(400);
                }
            }).submit();
        } // Video max boyutu aşmıyorsa...
    } // Video seçilmiş ise devam...
}

// Aşamayı atlayan fonksiyon
function atla(){
    <?php if ($gayarlar->dopingler_501 == 1) { ?>
    window.location.href = 'ilan-olustur?id=<?=$id;?>&asama=2';
    <?php } else { ?>
    $(".ilanasamax").removeAttr("id");
    $(".islem_tamam").attr("id","asamaaktif");
    $("#galeri_video_ekle").hide(1,function(){
        $("#TamamDiv").show(1);
        ajaxHere('ajax.php?p=ilan_son_asama&id=<?=$snc->id;?>','asama_result');
    });
    $('html, body').animate({scrollTop: 250}, 500);
    $("head").prepend('<meta http-equiv="refresh" content="5;url=aktif-ilanlar" />');
    <?php } ?>
}
</script>

<?php
} // video sistemi end


if ($asama == 2 && $gayarlar->dopingler_501 == 1) {
    list($dzaman1a, $dzaman1b) = explode("|", $gayarlar->dzaman1);
    list($dzaman2a, $dzaman2b) = explode("|", $gayarlar->dzaman2);
    list($dzaman3a, $dzaman3b) = explode("|", $gayarlar->dzaman3);
    $dzaman1b = $periyod[$dzaman1b];
    $dzaman2b = $periyod[$dzaman2b];
    $dzaman3b = $periyod[$dzaman3b];

    $odeme = $gvn->harf_rakam($_GET["odeme"]);
    $customs = $_SESSION["custom"];
    if ($fonk->bosluk_kontrol($customs) == false) {
        $custom = base64_decode($customs);
        $custom = json_decode($custom, true);
    }
    $from = "insert";
?>
<script type="text/javascript">
$(document).ready(function(){
    $(".ilanasamax:eq(3)").attr("id","asamaaktif");
});
</script>
<div id="doping_ekle"> <!-- doping ekle div start -->

<div class="clear"></div>

<?php if ($odeme == "true" && $_SESSION["custom"] != '') { ?>
<form action="ilan-olustur" method="GET" id="OdemeYontemiForm">
<input type="hidden" name="id" value="<?=$id;?>">
<input type="hidden" name="asama" value="2">

<br>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#eee"><h5><strong><?=dil("TX535");?></strong></h5></td>
    <td align="center" bgcolor="#eee"><strong><?=dil("TX536");?></strong></td>
  </tr>
  
  <?php
    $dopingler_501 = $custom["dopingler_501"];
    foreach ($dopingler_501 as $row) {
  ?>
  <tr>
    <td><?=dil("DOPING".$row["did"]);?></td>
    <td align="center">(<?=$row["sure"];?> <?=$periyod[$row["periyod"]];?>)<br><strong><?=$gvn->para_str($row["tutar"]);?> <?=dil("DOPING_PBIRIMI");?></strong></td>
  </tr>
  <?php } ?>
</table>

<h4 style="float:right;margin-top:25px;margin-bottom:25px;" id="ToplamOdenecek"><?=dil("TX523");?>: <strong><font id="toplam_tutar"><?=$gvn->para_str($custom["toplam_tutar"]);?></font> <?=dil("DOPING_PBIRIMI");?></strong></h4>

<div class="clear"></div>
<hr style="border: 1px solid #eee;">
<br>
<div style="width: 200px; margin: auto;">
<h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX537");?></h4>

<input id="odeme1" class="radio-custom" name="odeme" value="havale_eft" type="radio" style="width:100px;">
<label for="odeme1" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?=dil("TX538");?></span></label>
<div class="clear"></div>

<?php if ($gayarlar->paytr == 1) { ?>
<input id="odeme2" class="radio-custom" name="odeme" value="paytr" type="radio" style="width:100px;">
<label for="odeme2" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?=dil("TX539");?></span></label>
<div class="clear"></div>
<?php } ?>

<?php if ($gayarlar->iyzico == 1) { ?>
<input id="odeme3" class="radio-custom" name="odeme" value="iyzico" type="radio" style="width:100px;">
<label for="odeme3" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?=dil("TX539");?></span></label>
<div class="clear"></div>
<?php } ?>

<?php if ($gayarlar->paypal == 1) { ?>
<input id="odeme4" class="radio-custom" name="odeme" value="paypal" type="radio" style="width:100px;">
<label for="odeme4" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext">PayPal</span></label>
<div class="clear"></div>
<?php } ?>

</div>
<br><div class="clear"></div>
<hr style="border: 1px solid #eee;">

<div class="clear"></div>

<div class="clear"></div>
<br />

<div align="right">
<a style="margin-left: 15px;" class="btn" href="javascript:void(0);" onclick="OdemeYap();" id="DopingleButon"><i class="fa fa-check" aria-hidden="true"></i> <?=dil("TX540");?></a>
<a class="btn" href="ilan-olustur?id=<?=$id;?>&asama=2"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?=dil("TX541");?></a>
</div>

<div id="OdemeYontemiForm_output"></div>

</form>

<script type="text/javascript">
function OdemeYap() {
    var odeme_yontemi = $("input[name='odeme']:checked").val();
    if (odeme_yontemi == '' || odeme_yontemi == undefined) {
        $("#OdemeYontemiForm_output").html('<span class="error"><?=dil("TX542");?></span>');
    } else {
        $("#OdemeYontemiForm").submit();
    }
}
</script>

<?php } elseif ($odeme == "havale_eft" && $_SESSION["custom"] != '') { // Ödeme Banka Havale/EFT ile ise... ?>

<h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX543");?></h4>
<p>
<?=$dayarlar->hesap_numaralari;?>
</p>
<div style="text-align:center;margin-top:25px;">
<a class="btn" href="javascript:;" onclick="ajaxHere('ajax.php?p=ilan_doping_siparis&id=<?=$snc->id;?>&odeme=havale_eft&from=<?=$from;?>','SipSonuc');"><?=dil("TX544");?> <i class="fa fa-angle-double-right" style="margin-right:0px;margin-left:15px;" aria-hidden="true"></i></a></div>
<div class="clear"></div>
<div id="SipSonuc"></div>

<a class="btn" href="javascript:window.history.back();"><?=dil("TX515");?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

<?php } elseif ($odeme == "paytr" && $_SESSION["custom"] != '') { // Ödeme PayTR ile ise... ?>

<h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX543");?></h4>

<?php
    if ($_SESSION["advfrom"] == '') {
        $_SESSION["advfrom"] = $from;
    }
    $fiyat_int = $custom["toplam_tutar"];
    $urunadi = dil("PAY_NAME");
    $oid = time();
    $ftutar = ($fiyat_int * 100);
    $ftutar = (stristr($ftutar,".")) ? explode(".",$ftutar)[0] : $ftutar;

    $sipce = $db->prepare("INSERT INTO paytr_checks_501 SET acid=?, oid=?, status=?, custom=?, tarih=?, tutar=?");
    $sipce->execute([$hesap->id, $oid, 'waiting', $customs, $fonk->datetime(), $fiyat_int]);
?>
<!-- SanalPos frame kodu -->
<?php
    $fonk->paytr_frame($hesap->adi." ".$hesap->soyadi, $hesap->email, $hesap->adres, $hesap->telefon, $urunadi, $ftutar, $oid);
?>
<!-- SanalPos frame kodu end -->
<a class="btn" href="javascript:window.history.back();"><?=dil("TX515");?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

<?php } elseif ($odeme == "iyzico" && $_SESSION["custom"] != '') { // Ödeme Iyzico ile ise... ?>

<h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX543");?></h4>

<?php
    if ($_SESSION["advfrom"] == '') {
        $_SESSION["advfrom"] = $from;
    }

    $fonk->iyzico_cek();
	

<?php
class CheckoutFormSample
{
    public function should_initialize_checkout_form($tutar, $adi, $soyadi, $email, $site_url)
    {
        // İstek sınıfı oluştur
        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId("65465464646");
        $request->setPrice($tutar);
        $request->setPaidPrice($tutar);
        $request->setBasketId("BI101");
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($site_url . "odeme-sonuc");

        // Kullanıcı bilgilerini ayarla
        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId("BY789");
        $buyer->setName($adi);
        $buyer->setSurname($soyadi);
        $buyer->setEmail($email);
        $buyer->setIdentityNumber("74300864791");
        $buyer->setRegistrationAddress("Address");
        $buyer->setIp($_SERVER['REMOTE_ADDR']);
        $buyer->setCity("Istanbul");
        $buyer->setCountry("Turkey");
        $buyer->setZipCode("34732");
        $request->setBuyer($buyer);

        // Kargo adresini ayarla
        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName("Jane Doe");
        $shippingAddress->setCity("Istanbul");
        $shippingAddress->setCountry("Turkey");
        $shippingAddress->setAddress("Address");
        $shippingAddress->setZipCode("34742");
        $request->setShippingAddress($shippingAddress);

        // Fatura adresini ayarla
        $billingAddress = new \Iyzipay\Model\Address();
        $billingAddress->setContactName("Jane Doe");
        $billingAddress->setCity("Istanbul");
        $billingAddress->setCountry("Turkey");
        $billingAddress->setAddress("Address");
        $billingAddress->setZipCode("34742");
        $request->setbillingAddress($billingAddress);

        // Sepet öğelerini ayarla
        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId("BI101");
        $firstBasketItem->setName("Test");
        $firstBasketItem->setCategory1("Test1");
        $firstBasketItem->setCategory2("Test2");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice($tutar);
        $basketItems[0] = $firstBasketItem;
        $request->setBasketItems($basketItems);

        // İsteği gerçekleştir
        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, Sample::options());

        // Sonucu döndür
        return $checkoutFormInitialize;
    }
}

$fiyat_int = $custom["toplam_tutar"];
$sample = new CheckoutFormSample();
$sonuc = $sample->should_initialize_checkout_form($fiyat_int, $hesap->adi, $hesap->soyadi, $hesap->email, SITE_URL);
$stat = $sonuc->getStatus();

if ($stat == 'success') {
    echo $sonuc->getCheckoutFormContent();
?>
<div style="width: 80%;margin-top: 20px;">
    <div id="iyzipay-checkout-form" class="responsive"></div>
</div>
<?php
} else {
    echo '<span class="error">Hata Mesajı: ' . $sonuc->getErrorMessage() . '</span>';
}
?>

<a class="btn" href="javascript:window.history.back();"><?=dil("TX515");?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

<?php } elseif ($odeme == "paypal" && $_SESSION["custom"] != '') { // Ödeme PayPal ile ise... ?>

<h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX543");?></h4>

<?php
if ($_SESSION["advfrom"] == '') {
    $_SESSION["advfrom"] = $from;
}
?>
<center>
    <h4><?=dil("TX545");?></h4>
</center>

<div id="OdemeButon" style="text-align:center;margin-top:25px;"><a class="btn" href="javascript:;" onclick="ajaxHere('ajax.php?p=ilan_doping_siparis&id=<?=$snc->id;?>&odeme=paypal&from=<?=$from;?>','SipSonuc');"><i class="fa fa-check"></i> <?=dil("TX546");?></a></div>

<h4 style="color:green;margin-top:20px; display:none" id="SipGoster"><?=dil("TX547");?></h4>

<a class="btn" href="javascript:window.history.back();"><?=dil("TX515");?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

<div class="clear"></div>
<div id="SipSonuc"></div>

<?php } else { unset($_SESSION["custom"]); unset($_SESSION["advfrom"]); ?>
<form action="ajax.php?p=ilan_dopingle&id=<?=$id;?>&from=<?=$from;?>" method="POST" id="DopingleForm">

<h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX517");?></h4>

<div class="alert alert-info" role="alert"><?=dil("TX5181");?></div>
<br>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#eee"><h5><strong><?=dil("TX519");?></strong></h5></td>
    <td align="center" bgcolor="#eee"><strong><?=$dzaman1a . " " . $dzaman1b;?></strong></td>
    <td align="center" bgcolor="#eee"><strong><?=$dzaman2a . " " . $dzaman2b;?></strong></td>
    <td align="center" bgcolor="#eee"><strong><?=$dzaman3a . " " . $dzaman3b;?></strong></td>
  </tr>
  
  <?php
  $dopingler_501 = $db->query("SELECT * FROM doping_ayarlar_501 ORDER BY id ASC");
  while ($row = $dopingler_501->fetch(PDO::FETCH_OBJ)) {
    // Eğer aynı dopingten varsa ve süresi bitmemişse...
    $isdoping = $db->prepare("SELECT id, durum FROM dopingler_501 WHERE ilan_id=? AND did=? AND btarih > NOW()");
    $isdoping->execute([$snc->id, $row->id]);
  ?>
  <tr>
    <td><?=dil("DOPING" . $row->id);?></td>
    
    <?php if ($isdoping->rowCount() > 0) {
        $isdoping = $isdoping->fetch(PDO::FETCH_OBJ);
    ?>
    <td align="center" colspan="3">
    <?php if ($isdoping->durum == 0) { ?>
    <h5 style="color:orange;"><i class="fa fa-clock-o"></i> <?=dil("TX533");?></h5>
    <?php } elseif ($isdoping->durum == 1) { ?>
    <h5 style="color:green;"><i class="fa fa-check"></i> <?=dil("TX526");?></h5>
    <?php } ?>
    </td>
    <?php } else { ?>
    <td align="center"><label><input name="doping[<?=$row->id;?>]" class="checkbox_one" type="checkbox" value="1" data-money="<?=$row->fiyat1;?>"> <?=$gvn->para_str($row->fiyat1);?> <?=dil("DOPING_PBIRIMI");?></label></td>
    <td align="center"><label><input name="doping[<?=$row->id;?>]" class="checkbox_one" type="checkbox" value="2" data-money="<?=$row->fiyat2);?>"> <?=$gvn->para_str($row->fiyat2);?> <?=dil("DOPING_PBIRIMI");?></label></td>
    <td align="center"><label><input name="doping[<?=$row->id;?>]" class="checkbox_one" type="checkbox" value="3" data-money="<?=$row->fiyat3);?>"> <?=$gvn->para_str($row->fiyat3);?> <?=dil("DOPING_PBIRIMI");?></label></td>
    <?php } ?>
  </tr>
  <?php } ?>
</table>

<div class="clear"></div>

<h4 style="float:right;margin-top:25px;margin-bottom:25px;" id="ToplamOdenecek"><?=dil("TX523");?>: <strong><font id="toplam_tutar">0</font> <?=dil("DOPING_PBIRIMI");?></strong></h4>

<div class="clear"></div>

<hr style="border: 1px solid #eee;">
<br>
<div align="right">
<a style="margin-left: 15px;" class="btn" href="javascript:void(0);" onclick="AjaxFormS('DopingleForm','DopingleForm_output');" id="DopingleButon"><i class="fa fa-check" aria-hidden="true"></i> <?=dil("TX524");?></a>  
<a class="btn" href="javascript:atla();"><?=dil("TX443");?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
</div>

<div id="DopingleForm_output" style="display:none" align="left"></div>

<script type="text/javascript">
var formatter = new Intl.NumberFormat('tr-TR', {
  style: 'currency',
  currency: 'TRY',
  minimumFractionDigits: 2,
});

$(document).ready(function(){
    ToplamDegisti();
    $(".checkbox_one").change(function(){
        var elem = $(this);
        $(".checkbox_one[name='"+elem.attr("name")+"']").not(this).prop("checked",false);
        ToplamDegisti();
    });
});

function ToplamDegisti(){
    var toplam_tutar = 0, tutar = 0;
    $("table tr td input:checked").each(function(index){
        tutar = $(this).attr("data-money");
        tutar = parseFloat(tutar);
        toplam_tutar += tutar;
    });

    if (toplam_tutar > 0) {
        toplam_tutar = formatter.format(toplam_tutar);
        toplam_tutar = toplam_tutar.replace("₺", "");
        $("#toplam_tutar").html(toplam_tutar);
    } else {
        $("#toplam_tutar").html(toplam_tutar);
    }
}
</script>
</form>

<?php } ?>

</div><!-- doping ekle div end asama 2 end -->

<script type="text/javascript">
function atla(){
    $(".ilanasamax").removeAttr("id");
    $(".islem_tamam").attr("id","asamaaktif");
    $("#doping_ekle").hide(1,function(){
        $("#TamamDiv").show(1);
        ajaxHere('ajax.php?p=ilan_son_asama&id=<?=$snc->id;?>','asama_result');
    });
    $('html, body').animate({scrollTop: 250}, 500);
    $("head").prepend('<meta http-equiv="refresh" content="5;url=aktif-ilanlar" />');
}
</script>
<?php
}

if ($asama == 3) {
?>
<script type="text/javascript">
$(document).ready(function(){
    $(".islem_tamam").attr("id","asamaaktif");
    $("#TamamDiv").show(1);
    ajaxHere('ajax.php?p=ilan_son_asama&id=<?=$snc->id;?>','asama_result');
    $("head").prepend('<meta http-equiv="refresh" content="5;url=aktif-ilanlar" />');
});
</script>
<?php
}

} else { // ilan id gelmiyorsa // ?>
<script type="text/javascript">
$(document).ready(function(){
    $(".ilanasamax:eq(0)").attr("id","asamaaktif");
});
</script>
<?php } ?>


<?php

// Aylık ilan limitini kontrol et
if ($aylik_ilan_limit < 1) {
?>
<br><br><br>
<?=dil("TX611");?>
<?php
} else {
?>

<div class="clear"></div>
<?=str_replace(array("[aylik_ilan_limit]", "[ilan_resim_limit]", "[ilan_yayin_sure]"), array($aylik_ilan_limit, $ilan_resim_limit, $ilan_yayin_sure . " " . $periyod[$ilan_yayin_periyod]), dil("TX655"));?>
<div class="clear"></div>

<form action="ajax.php?p=ilan_olustur" method="POST" id="IlanOlusturForm" enctype="multipart/form-data">
<input type="hidden" name="ilan_no" value="<?=$ilan_no;?>" />

<ul class="etab">
    <li><a href="javascript:void(0)" class="etablinks" onclick="openTab(event, 'etab1')" id="edefaultOpen"><?=$dilx->gosterim_adi;?></a></li>
    <?php
    $dilop = array();
    $i = 1;
    $sql = $db->query("SELECT * FROM diller_501 WHERE kisa_adi != ? ORDER BY sira ASC", [$dil]);
    while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
        $i++;
        $dilop[$i] = $row;
    ?>
    <li><a href="javascript:void(0)" class="etablinks" onclick="openTab(event, 'etab<?=$i;?>')" id="edefaultOpen"><?=$row->gosterim_adi;?></a></li>
    <?php } ?>
</ul>

<?php
for ($o = 2; $o <= $i; $o++) {
    $op = $dilop[$o];
?>
<div id="etab<?=$o;?>" class="etabcontent">
<table width="100%" border="0">
<tr>
    <td><?=dil("TX258");?> <span style="color:red">*</span></td>
    <td><input name="tabs[<?=$op->kisa_adi;?>][baslik]" type="text"></td>
</tr>
<tr>
    <td colspan="2"><br>
	<div class="ilanaciklamalar">
	<h3><strong><?=dil("TX288");?></strong></h3>
	<div class="clear"></div>
    <textarea class="thetinymce" id="icerik<?=$o;?>" name="tabs[<?=$op->kisa_adi;?>][icerik]"></textarea>
	</div>
	</td>
</tr>
</table>
</div>
<?php } ?>

<div id="etab1" class="etabcontent">
<table width="100%" border="0">
<tr>
    <td><?=dil("TX258");?> <span style="color:red">*</span></td>
    <td><input name="baslik" type="text"></td>
</tr>
<tr>
    <td><?=dil("TX259");?> <span style="color:red">*</span></td>
    <td>
	<input name="fiyat" type="text" id="ilantutar" data-mask="#.##0" data-mask-reverse="true" data-mask-maxlength="false">
	<select name="pbirim" id="ilanpbirimi">
	<?php
	$pbirimler = explode(",", dil("PARA_BIRIMI"));
	foreach ($pbirimler as $birim) {
	?><option><?=$birim;?></option><?php } ?>
	</select>
	</td>
</tr>
<tr>
    <td><?=dil("TX260");?></td>
    <td><input disabled type="text" value="<?=$ilan_no;?>"></td>
</tr>

<?php
$emlkdrm = dil("EMLK_DRM");
if ($emlkdrm != '') {
?>
<tr>
    <td><?=dil("TX261");?> <span style="color:red">*</span></td>
    <td>
<select name="emlak_durum">
		<?php
		$parc = explode("<+>", $emlkdrm);
		foreach ($parc as $val) {
		?><option><?=$val;?></option><?php } ?>
</select>
    </td>
</tr>
<?php } ?>

<?php
$emlktp = dil("EMLK_TIPI");
if ($emlktp != '') {
?>
<tr>
    <td><?=dil("TX262");?> <span style="color:red">*</span></td>
    <td>
<select name="emlak_tipi" onchange="konut_getir(this.options[this.selectedIndex].value);">
		<?php
		$parc = explode("<+>", $emlktp);
		foreach ($parc as $val) {
		?><option><?=$val;?></option><?php } ?>
</select>
    </td>
</tr>
<?php } ?>

<?php
$kntsekli = dil("KNT_SEKLI");
if ($kntsekli != '') {
?>
<tr id="konut_sekli_con">
    <td><?=dil("TX268");?></td>
    <td>
<select name="konut_sekli">
        <option value=""><?=dil("TX268");?></option>
		<?php
		$parc = explode("<+>", $kntsekli);
		foreach ($parc as $val) {
		?><option><?=$val;?></option><?php } ?>
</select>
    </td>
</tr>
<?php } ?>

<?php
$knttipi = dil("KNT_TIPI");
if ($knttipi != '') {
?>
<tr id="konut_tipi_con">
    <td><?=dil("TX267");?></td>
    <td>
<select name="konut_tipi">
        <option value=""><?=dil("TX267");?></option>
		<?php
		$parc = explode("<+>", $knttipi);
		foreach ($parc as $val) {
		?><option><?=$val;?></option><?php } ?>
</select>
    </td>
</tr>
<?php } ?>

<?php
$ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
$ulkelerc = $ulkeler->rowCount();
if ($ulkelerc > 1) {
?>
<tr>
    <td><?=dil("TX348");?> <span style="color:red">*</span></td>
    <td>
<select id="ulke_id" name="ulke_id" onchange="ajaxHere('ajax.php?p=il_getir&ulke_id=' + this.options[this.selectedIndex].value, 'il'), yazdir();">
		<option value=""><?=dil("TX264");?></option>
		<?php
		while ($row = $ulkeler->fetch(PDO::FETCH_OBJ)) {
		?><option value="<?=$row->id;?>"><?=$row->ulke_adi;?></option><?php } ?>
</select>
    </td>
</tr>
<?php } ?>

<tr>
    <td><?=dil("TX263");?> <span style="color:red">*</span></td>
    <td>
<select id="il" name="il" onchange="ajaxHere('ajax.php?p=ilce_getir&il_id=' + this.options[this.selectedIndex].value, 'ilce'), $('#semt').html(''), yazdir();">
		<option value=""><?=dil("TX264");?></option>
		<?php
		if ($ulkelerc < 2) {
			$ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
			$sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id = ? ORDER BY id ASC", [$ulke->id]);
			while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
			?><option value="<?=$row->id;?>"><?=$row->il_adi;?></option><?php } ?>
		<?php } ?>
</select>
    </td>
</tr>

<tr>
    <td><?=dil("TX265");?></td>
    <td>
<select onchange="ajaxHere('ajax.php?p=mahalle_getir&ilce_id=' + this.options[this.selectedIndex].value, 'semt'), yazdir();" name="ilce" id="ilce">
        <option value=""><?=dil("TX264");?></option>
		<option value="0"><?=dil('TX349');?></option>
        <?php
		if ($ilan->il_id != '') {
			$sql = $db->query("SELECT id, ilce_adi FROM ilce WHERE il_id = ? ORDER BY id ASC", [$ilan->il_id]);
			while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
			?><option value="<?=$row->id;?>" <?=($row->id == $ilan->ilce_id) ? 'selected' : '';?>><?=$row->ilce_adi;?></option><?php } ?>
		<?php } ?>
</select>
    </td>
</tr>

<tr>
    <td><?=dil("TX266");?></td>
    <td>
	<select onchange="yazdir();" name="mahalle" id="semt">
        <option value=""><?=dil("TX264");?></option>
		<option value="0"><?=dil('TX349');?></option>
        <?php
		if ($ilan->ilce_id != '') {
			$sql = $db->query("SELECT id, mahalle_adi FROM mahalle_koy WHERE ilce_id = ? ORDER BY id ASC", [$ilan->ilce_id]);
			while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
			?><option value="<?=$row->id;?>" <?=($row->id == $ilan->mahalle_id) ? 'selected' : '';?>><?=$row->mahalle_adi;?></option><?php } ?>
		<?php } ?>
</select>
	</td>
</tr>

<tr>
    <td><?=dil("TX270");?> <span style="color:red">*</span></td>
    <td><input name="metrekare" type="text"></td>
</tr>

<tr id="brut_metrekare_con">
    <td><?=dil("TX2701");?></td>
    <td><input name="brut_metrekare" type="text"></td>
</tr>

<?php
$bulundkat = dil("BULND_KAT");
if ($bulundkat != '') {
?>
<tr id="bulundugu_kat_con">
    <td><?=dil("TX269");?></td>
    <td>
<select name="bulundugu_kat">
        <option value=""><?=dil("TX269");?></option>
		<?php
		$parc = explode("<+>", $bulundkat);
		foreach ($parc as $val) {
		?><option><?=$val;?></option><?php } ?>
</select>
    </td>
</tr>
<?php } ?>


<?php

// Yapı durumu kontrolü ve form alanı
$ypidrm = dil("YAPI_DURUM");
if ($ypidrm != '') {
?>
<tr id="yapi_durum_con">
    <td><?=dil("TX271");?></td>
    <td>
    <select name="yapi_durum">
    <option value=""><?=dil("TX271");?></option>
        <?php
        $parc = explode("<+>", $ypidrm);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>

// Oda sayısı kontrolü ve form alanı
<?php
$odasayisiy = dil("ODA_SAYISI");
if ($odasayisiy != '') {
?>
<tr id="oda_sayisi_con">
    <td><?=dil("TX272");?></td>
    <td>
     <select name="oda_sayisi">
     <option value=""><?=dil("TX272");?></option>
        <?php
        $parc = explode("<+>", $odasayisiy);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>

// Bina yaşı form alanı
<tr id="bina_yasi_con">
    <td><?=dil("TX273");?></td>
    <td><input name="bina_yasi" type="text"></td>
</tr>

// Bina kat sayısı form alanı
<tr id="bina_kat_sayisi_con">
    <td><?=dil("TX274");?></td>
    <td><input name="bina_kat_sayisi" type="text"></td>
</tr>

// Isıtma durumu kontrolü ve form alanı
<?php
$isitma = dil("ISITMA");
if ($isitma != '') {
?>
<tr id="isitma_con">
    <td><?=dil("TX275");?></td>
    <td>
     <select name="isitma">
     <option value=""><?=dil("TX275");?></option>
        <?php
        $parc = explode("<+>", $isitma);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>

// Banyo sayısı form alanı
<tr id="banyo_sayisi_con">
    <td><?=dil("TX276");?></td>
    <td><input name="banyo_sayisi" type="text"></td>
</tr>

// Eşyalı durumu form alanı
<tr id="esyali_con">
    <td><?=dil("TX277");?></td>
    <td>
    <select name="esyali">
        <option value=""><?=dil("TX277");?></option>
        <option value="1"><?=dil("TX167");?></option>
        <option value="0"><?=dil("TX168");?></option>
    </select>
    </td>
</tr>

// Kullanım durumu kontrolü ve form alanı
<?php
$kuldrm = dil("KUL_DURUM");
if ($kuldrm != '') {
?>
<tr id="kullanim_durum_con">
    <td><?=dil("TX278");?></td>
    <td>
    <select name="kullanim_durum">
    <option value=""><?=dil("TX278");?></option>
        <?php
        $parc = explode("<+>", $kuldrm);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>

// Site içi durumu form alanı
<tr id="site_ici_con">
    <td><?=dil("TX279");?></td>
    <td>
    <select name="site_ici">
        <option value=""><?=dil("TX279");?></option>
        <option value="1"><?=dil("TX167");?></option>
        <option value="0"><?=dil("TX168");?></option>
    </select>
    </td>
</tr>

// Aidat form alanı
<tr id="aidat_con">
    <td><?=dil("TX280");?></td>
    <td><input name="aidat" type="text"></td>
</tr>

// Arsa için metrekare fiyatı form alanı
<tr class="arsa_icin">
    <td><?=dil("TX327");?></td>
    <td><input name="metrekare_fiyat" type="text"></td>
</tr>

// Arsa için ada no form alanı
<tr class="arsa_icin">
    <td><?=dil("TX328");?></td>
    <td><input name="ada_no" type="text"></td>
</tr>

// Arsa için parsel no form alanı
<tr class="arsa_icin">
    <td><?=dil("TX329");?></td>
    <td><input name="parsel_no" type="text"></td>
</tr>

// Arsa için pafta no form alanı
<tr class="arsa_icin">
    <td><?=dil("TX330");?></td>
    <td><input name="pafta_no" type="text"></td>
</tr>

// Arsa için kaks/emsal form alanı
<tr class="arsa_icin">
    <td><?=dil("TX331");?></td>
    <td>
    <?php
    $kaks_emsl = dil("KAKS_EMSAL");
    if ($kaks_emsl != '') {
    ?>
    <select name="kaks_emsal">
    <option value=""><?=dil("TX264");?></option>
        <?php
        $parc = explode("<+>", $kaks_emsl);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php } ?>
    </select>
    <?php } else { ?>
    <input name="kaks_emsal" type="text">
    <?php } ?>
    </td>
</tr>

// Arsa için gabari form alanı
<tr class="arsa_icin">
    <td><?=dil("TX332");?></td>
    <td>
    <?php
    $gabari = dil("GABARI");
    if ($gabari != '') {
    ?>
    <select name="gabari">
    <option value=""><?=dil("TX264");?></option>
        <?php
        $parc = explode("<+>", $gabari);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php } ?>
    </select>
    <?php } else { ?>
    <input name="gabari" type="text">
    <?php } ?>
    </td>
</tr>

// Arsa için imar durumu kontrolü ve form alanı
<?php
$imar_drm = dil("IMAR_DURUM");
if ($imar_drm != '') {
?>
<tr class="arsa_icin">
    <td><?=dil("TX682");?></td>
    <td>
    <select name="imar_durum" class="form-control">
    <option value=""><?=dil("TX264");?></option>
        <?php
        $parc = explode("<+>", $imar_drm);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>

// Arsa için tapu durumu kontrolü ve form alanı
<?php
$tapu_drm = dil("TAPU_DRM");
if ($tapu_drm != '') {
?>
<tr class="arsa_icin">
    <td><?=dil("TX333");?></td>
    <td>
    <select name="tapu_durumu" class="form-control">
    <option value=""><?=dil("TX264");?></option>
        <?php
        $parc = explode("<+>", $tapu_drm);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>

// Kat karşılığı durumu form alanı
<tr class="arsa_icin">
    <td><?=dil("TX334");?></td>
    <td>
    <select name="katk" class="form-control">
    <option value=""><?=dil("TX264");?></option>
    <option><?=dil("TX167");?></option>
    <option><?=dil("TX168");?></option>
    </select>
    </td>
</tr>

// Kredi durumu kontrolü ve form alanı
<?php
if (dil("TX335") != '') {
$exp = explode(",", dil("TX653"));
?>
<tr>
    <td><?=dil("TX335");?></td>
    <td>
    <select name="krediu">
    <option value=""><?=dil("TX264");?></option>
    <?php
    foreach ($exp as $row) {
    ?><option><?=$row;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>


 <tr class="arsa_icin">
    <td><?=dil("TX336");?></td>
    <td>
    <select name="takas" class="form-control">
        <option value=""><?=dil("TX264");?></option>
        <option><?=dil("TX167");?></option>
        <option><?=dil("TX168");?></option>
    </select>
    </td>
</tr>

<?php
if (dil("KIMDEN") != '') {
    $exp = explode(",", dil("KIMDEN"));
?>
<tr>
    <td><?=dil("TX460");?></td>
    <td>
    <select name="kimden">
        <option value=""><?=dil("TX460");?></option>
        <?php
        foreach ($exp as $row) {
        ?><option><?=$row;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>

<?php
if (dil("TX624") != '') {
    $exp = explode(",", dil("TX625"));
?>
<tr>
    <td><?=dil("TX624");?></td>
    <td>
    <select name="yetkis">
        <option value=""><?=dil("TX624");?></option>
        <?php
        foreach ($exp as $row) {
        ?><option><?=$row;?></option><?php } ?>
    </select>
    </td>
</tr>
<?php } ?>

<tr id="yetki_bilgisi_con">
    <td><?=dil("TX2731");?><span style="color:red">*</span></td>
    <div class="form-group" style="text-align: center;">
    <td><strong><font color="green">Buraya <font color="red">Emlak Yetki Belgenizin <font color="green">Numarasını Giriniz.</font>
    <input name="yetki_bilgisi" type="text"></td>
</tr>

<table>
<br/>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> SÖZLEŞMELİ İLANLARINIZ </strong></font><br/><font color="black">Sözleşmeli İlanlarınızı, Daha Hızlı Pazarlamak İçin,</font><br/> <font color="black">İsterseniz, Üyelerimizle ve / veya Grup Oluşturarak, Grup Üyeleriniz ile Paylaşabilirsiniz.</font><br/> <font color="black">Paylaştığınız İlanda, <font color="blue">Size ait - Firma İsmi, Logo, Telefon vs.</font> Olmamalıdır.<br/> <font color="black">İlan Açıklamasının en altına <font color="blue">Bu İlan Yetkilisi Tarafından Paylaşıma Açılmıştır </font> Yazınız <br/> <font color="black">Sözleşmeli İlan Kutucuklarını Doldurduysanız, </font><br/><font color="red"><strong>KAPALI PORTFÖY ve TALEP KUTUCUKLARINI İŞARETLEMEYİNİZ. </strong> </font><br/><br/></p>
</div>
<br/>

<tr id="site_id_888_con">
    <td><?=dil("TX27340");?></td>
    <div class="form-group" style="text-align: center;">
    <td><font color="blue"> Sözleşmeli İlanınızı, Üye Emlakçılarımız ile Paylaşmak İçin,</font>&nbsp&nbsp&nbsp<font color="black">Kutunun İçine <font color="red"><strong> 100 </font></strong><font color="black">Yazınız.
    <input name="site_id_888" type="text"></td>
</tr>

<tr id="site_id_777_con">
    <td><?=dil("TX27341");?></td>
    <div class="form-group" style="text-align: center;">
    <td><font color="blue"> Sözleşmeli İlanınızı, Grubunuzdaki Emlakçılar ile Paylaşmak İçin </font>&nbsp&nbsp&nbsp<font color="black">Kutunun İçine <font color="red"><strong> GRUP KODUNU </font></strong><font color="black">Yazınız
    <input name="site_id_777" type="text"></td>
</tr>
</table>

<br/>
<table>
    <div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> KAPALI PORTFÖY İLANLARINIZ </strong></font><br/><font color="black"> Kapalı Portföy İlanlarınızı, Artık Sitenize, Girerek Saklayabilir</font><br/><font color="black">Linkini Müşterilerinize Yollayabilir, Ofisinizde Sunum Yapabilirsiniz.</font> <br/><font color="black">İsterseniz, Üyelerimizle ve / veya Grup Oluşturarak, Grup Üyeleriniz ile Paylaşabilirsiniz.<br/><font color="red"> <strong>KAPALI PORTFÖY İLANLARI, HİÇBİR SİTEDE YAYINLANMAZ.</strong></font><br/><font color="blue"> Sitenize Girdiğiniz İlanları, Admin Kısmından, Sadece Siz Görebilirsiniz.</font><br/><font color="blue"> Paylaştığınız İlanları, Paylaştığınız Üyeler, Kendi Sitelerinin Admin kısmına girerek Görebilirler. </font><br/><font color="black">Kapalı Portföy İlan Kutucuklarını Doldurduysanız, </font><br/><font color="red"><strong>SÖZLEŞMELİ İLAN ve TALEPLER KUTUCUKLARINI DOLDURMAYINIZ. </strong> </font><br/><br/>
    </div>
<br/>

<tr id="site_id_699_con">
    <td><?=dil("TX27342");?></td>
    <div class="form-group" style="text-align: center;">
    <td><font color="blue"> Kapalı Portföy İlanlarınızı, Üye Emlakçılarımız ile Paylaşmak için </font><br/> <font color="black"> Kutunun İçine <font color="red"><strong> 200 </font></strong><font color="black">Yazınız. ( İlan Başlık Yazısının Başına </font> <font color="red"><strong> KAPALI </font></strong><font color="black">Yazmayı Unutmayınız )</font>
    <input name="site_id_699" type="text"></td>
</tr>

<tr id="site_id_700_con">
    <td><?=dil("TX27343");?></td>
    <div class="form-group" style="text-align: center;">
    <td><font color="blue"> Kapalı Portföy İlanlarınızı, PAYLAŞMAK İSTEMEZSENİZ,</font><br/> <font color="black"> Kutucuğun İçine size verdiğimiz <font color="red"><strong> ŞİFREYİ </font></strong><font color="black">Yazınız.
    <input name="site_id_700" type="text"></td>
</tr>

<tr id="site_id_701_con">
    <td><?=dil("TX27344");?></td>
    <div class="form-group" style="text-align: center;">
    <td><font color="blue"> Kapalı Portföy İlanlarınızı, GRUBUNUZDAKİ EMLAKÇILARLA PAYLAŞMAK İçin</font><br/> <font color="black"> Kutucuğun İçine,<font color="red"><strong> GRUP KODUNU </font></strong><font color="black">Yazınız.
    <input name="site_id_701" type="text"></td>
</tr>
</table>

<table>
<br/>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> EMLAK TALEPLERİNİZİ PAYLAŞIN </strong></font><br/><font color="black"> Sıtenize girdiğiniz Müşteri Talepleriniz, PORTALLARIMIZDA, Sizin Bilgileriniz ile YAYINLANIR. </font><br/><font color="black"> Gireceğiniz Taleplerin Başlığının Başına </font><font color="red"><strong> TALEP </strong></font> <font color="black">Yazmayı unutmayınız.</font> <br/><font color="blue"> ( PAYLAŞILAN TALEPLERİ, Sizin Sitenizdede, Sizin Bilgileriniz ile Göstermemizi isterseniz, Bizi arayabilirsiniz.) </font> <br/><font color="black">Emlak Talep İlan Kutucuğunu Doldurduysanız, </font><br/><font color="red"><strong>SÖZLEŞMELİ İLAN ve KAPALI PORTFÖY KUTUCUKLARINI DOLDURMAYINIZ. </strong> </font><br/><br/>
</div>

<tr id="site_id_702_con">
    <td><?=dil("TX27345");?></td>
    <div class="form-group" style="text-align: center;">
    <td><font color="blue">Emlak Taleplerinizi Paylaşmak için,</font> <font color="black">Kutunun İçine <font color="red"><strong> 300 </font></strong><font color="black">Yazınız.
    <input name="site_id_702" type="text"></td>
</tr>
</table>

<table>
<br/>
<div class="form-group" style="text-align: center;">
    <p><strong><font color="blue"> İLANINIZI PORTALLARIMIZDA DA YAYINLAYABİLİRSİNİZ </font> <br/><font color="red">İSTEDİĞİNİZ ( Portalın / Portalların ) KUTUCUĞUNU İŞARETLEYİNİZ. </font></strong><br/><font color="black"> ( İŞARETLEMEDİĞİNİZ ( Portalımızda / Portallarımızda ) İLANINIZ YAYINLANMAZ.) </font><br/><font color="black">( İLANINIZ, Portallarımızda Sizin Bilgilerinizle Yayınlanır. )</font><br/><br/>

<tr id="site_id_335_con">
    <td><?=dil("TX2734");?></td>
    <td>
        <div style="margin-top: 25px;" style="text-align: left;">
            <input type="checkbox" id="site_id_335_checkbox" onclick="toggleSiteId(335)" />
            <label for="site_id_335_checkbox"></label>
            <input type="hidden" id="site_id_335" name="site_id_335" value="">
        </div>
    </td>
</tr>

<tr id="site_id_334_con">
    <td><?=dil("TX2735");?></td>
    <td>
        <div style="margin-top: 25px;">
            <input type="checkbox" id="site_id_334_checkbox" onclick="toggleSiteId(334)" />
            <label for="site_id_334_checkbox"></label>
            <input type="hidden" id="site_id_334" name="site_id_334" value="">
        </div>
    </td>
</tr>

<tr id="site_id_306_con">
    <td><?=dil("TX2736");?></td>
    <td>
        <div style="margin-top: 25px;">
            <input type="checkbox" id="site_id_306_checkbox" onclick="toggleSiteId(306)" />
            <label for="site_id_306_checkbox"></label>
            <input type="hidden" id="site_id_306" name="site_id_306" value="">
        </div>
    </td>
</tr>
</table>

<script>
function toggleSiteId(siteId) {
    var checkbox = document.getElementById('site_id_' + siteId + '_checkbox');
    var hiddenInput = document.getElementById('site_id_' + siteId);
    if (checkbox.checked) {
        hiddenInput.value = siteId; 
    } else {
        hiddenInput.value = ''; 
    }
}
</script>

<?php
$delm1 = explode("<+>", dil("CEPHE"));
$delm2 = explode("<+>", dil("IC_OZELLIKLER"));
$delm3 = explode("<+>", dil("DIS_OZELLIKLER"));
$delm4 = explode("<+>", dil("ALTYAPI_OZELLIKLER"));
$delm5 = explode("<+>", dil("KONUM_OZELLIKLER"));
$delm6 = explode("<+>", dil("GENEL_OZELLIKLER"));
$delm7 = explode("<+>", dil("MANZARA_OZELLIKLER"));
$cdelm1 = count($delm1);
$cdelm2 = count($delm2);
$cdelm3 = count($delm3);
$cdelm4 = count($delm4);
$cdelm5 = count($delm5);
$cdelm6 = count($delm6);
$cdelm7 = count($delm7);

if ($cdelm1 > 1 || $cdelm2 > 1 || $cdelm3 > 1 || $cdelm4 > 1 || $cdelm5 > 1 || $cdelm6 > 1 || $cdelm7 > 1) {
?>
<tr id="ozellikler_con">
    <td colspan="2">
    <div class="ilanaciklamalar">
    <h3><?=dil("TX284");?></h3>

    <?php
    $checkbox = 0;
    if ($cdelm1 > 1) {
        $ielm = explode("<+>", $ilan->cephe_ozellikler);
    ?>
    <div class="ilanozellik tipi_konut">
    <h4><?=dil("TX285");?></h4>
    <?php
    foreach ($delm1 as $val) {
    ?>
    <span><label><input name="cephe_ozellikler[]" value="<?=$val;?>" type="checkbox"> <?=$val;?></label></span>
<?php } ?>
    </div>
    <?php } ?>
    </div>
    </td>
</tr>
<?php } ?>


<?php
// İç özellikler kontrolü ve form alanı
if ($cdelm2 > 1) {
    $ielm = explode("<+>", $ilan->ic_ozellikler);
?>
<div class="ilanozellik tipi_konut">
<h4><?=dil("TX286");?></h4>
<?php
foreach ($delm2 as $val) {
    $checked = (in_array($val, $ielm)) ? 'checked' : '';
?>
<span><label><input name="ic_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
<?php
}
?>
</div>
<?php } ?>

// Dış özellikler kontrolü ve form alanı
<?php
if ($cdelm3 > 1) {
    $ielm = explode("<+>", $ilan->dis_ozellikler);
?>
<div class="ilanozellik tipi_konut">
<h4><?=dil("TX287");?></h4>
<?php
foreach ($delm3 as $val) {
    $checked = (in_array($val, $ielm)) ? 'checked' : '';
?>
<span><label><input name="dis_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
<?php
}
?>
</div>
<?php } ?>

// Altyapı özellikleri kontrolü ve form alanı
<?php
if ($cdelm4 > 1) {
    $ielm = explode("<+>", $ilan->altyapi_ozellikler);
?>
<div class="ilanozellik tipi_arsa">
<h4><?=dil("TX323");?></h4>
<?php
foreach ($delm4 as $val) {
    $checked = (in_array($val, $ielm)) ? 'checked' : '';
?>
<span><label><input name="altyapi_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
<?php
}
?>
</div>
<?php } ?>

// Konum özellikleri kontrolü ve form alanı
<?php
if ($cdelm5 > 1) {
    $ielm = explode("<+>", $ilan->konum_ozellikler);
?>
<div class="ilanozellik tipi_arsa">
<h4><?=dil("TX324");?></h4>
<?php
foreach ($delm5 as $val) {
    $checked = (in_array($val, $ielm)) ? 'checked' : '';
?>
<span><label><input name="konum_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
<?php
}
?>
</div>
<?php } ?>

// Genel özellikler kontrolü ve form alanı
<?php
if ($cdelm6 > 1) {
    $ielm = explode("<+>", $ilan->genel_ozellikler);
?>
<div class="ilanozellik tipi_arsa">
<h4><?=dil("TX325");?></h4>
<?php
foreach ($delm6 as $val) {
    $checked = (in_array($val, $ielm)) ? 'checked' : '';
?>
<span><label><input name="genel_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
<?php
}
?>
</div>
<?php } ?>

// Manzara özellikleri kontrolü ve form alanı
<?php
if ($cdelm7 > 1) {
    $ielm = explode("<+>", $ilan->manzara_ozellikler);
?>
<div class="ilanozellik tipi_arsa">
<h4><?=dil("TX326");?></h4>
<?php
foreach ($delm7 as $val) {
    $checked = (in_array($val, $ielm)) ? 'checked' : '';
?>
<span><label><input name="manzara_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
<?php
}
?>
</div>
<?php } ?>
</div>
</td>
</tr>
<?php } ?>

<tr>
    <td colspan="2">
    <strong><i class="fa fa-map-marker" aria-hidden="true"></i> <?=dil("TX289");?></strong>
    <br><span style="font-size:13px;"><?=dil("TX0");?><?=dil("TX290");?></span>
<br><br>
<div class="gmapsecenek">
<input disabled class="form-control" id="map_il" type="text" placeholder="Şehir">
<input disabled id="map_ilce" class="form-control" type="text" placeholder="İlçe">
<input disabled onchange="yazdir();" type="text" id="map_mahalle" class="form-control" value="" placeholder="Mahalle">
<input onchange="yazdir();" type="text" class="form-control" name="map_cadde" value="" placeholder="Cadde">
<input onchange="yazdir();" type="text" class="form-control" name="map_sokak" value="" placeholder="Sokak">
</div>

<input type="text" class="form-control" id="map_adres" name="map_adres" placeholder="Adres yaznz..." style="display: none;">
<input type="text" id="coords" name="maps" value="" style="display:none;" />

<div id="map" style="width: 100%; height: 300px"></div>

<?php
    $coords = "41.003917,28.967299";
    list($lat, $lng) = explode(",", $coords);
?>
<input type="hidden" value="<?php echo $lat; ?>" id="g_lat">
<input type="hidden" value="<?php echo $lng; ?>" id="g_lng">
<script type="text/javascript">
function initMap() {
    var g_lat = parseFloat(document.getElementById("g_lat").value);
    var g_lng = parseFloat(document.getElementById("g_lng").value);
    var map = new google.maps.Map(document.getElementById('map'), {
        dragable: true,
        zoom: 15,
        center: { lat: g_lat, lng: g_lng }
    });
    var geocoder = new google.maps.Geocoder();

    var marker = new google.maps.Marker({
        position: { lat: g_lat, lng: g_lng },
        map: map,
        draggable: true
    });

    jQuery('#map_adres').on('change', function () {
        var val = $(this).val();
        geocodeAddress(marker, geocoder, map, val);
    });

    google.maps.event.addListener(marker, 'dragend', function () {
        dragend(marker);
    });

}

function geocodeAddress(marker, geocoder, resultsMap, address) {
    if (address) {
        geocoder.geocode({ 'address': address }, function (results, status) {
            if (status === 'OK') {
                resultsMap.setCenter(results[0].geometry.location);
                marker.setMap(resultsMap);
                marker.setPosition(results[0].geometry.location);
                dragend(marker);
            } else {
                console.log('Geocode was not successful for the following reason: ' + status + " word: " + address);
            }
        });
    }
}

function dragend(marker) {
    var lat = marker.getPosition().lat();
    var lng = marker.getPosition().lng();
    $("#coords").val(lat + "," + lng);
}
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gayarlar->google_api_key; ?>&callback=initMap"></script>
</td>
</tr>

<tr>
    <td colspan="2"><br>
    <div class="ilanaciklamalar">
    <h3><strong><?=dil("TX288");?></strong></h3>
    <div class="clear"></div>
    <textarea class="thetinymce" id="icerik" name="icerik"></textarea>
    </div>
    </td>
</tr>
</table>
</div><!-- tab1 end -->

<div class="form-group">
    <label class="col-sm-3 control-label"><font color="red"><strong>İlan Notunuz</strong></font></label>
    <div class="col-sm-9">
    <textarea name="notu" class="form-control" placeholder="Bu notu sadece siz görebilirsiniz. ( İlanı Paylaşacaksanız, Buraya Özel Birşey Yazmayınız.) "><?=$snc->notu;?></textarea>
    </div>
</div>

<table width="100%" border="0">
<tr>
    <td style="border:none" colspan="2">
    <div id="IlanOlusturForm_output" style="display:none"></div>
    <a href="javascript:;" id="IlanSubmit" onclick="IlanSubmit();" class="btn"><i class="fa fa-camera"></i> <?=dil("TX299");?></a>
    </td>
</tr>
</table>
</form>

<script src="<?=THEME_DIR;?>tinymce/tinymce.min.js"></script>
<script type="application/x-javascript">
tinymce.init({
    selector: ".thetinymce",
    height: 300,
    language: 'tr',
    menubar: false,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern imagetools'
    ],
    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image',
    toolbar2: 'print preview media | forecolor backcolor emoticons',
});
</script>

<script type="text/javascript"> 
function IlanSubmit() {
    var stbutton = $("#IlanSubmit");
    var stonc = stbutton.attr("onclick");
    var stinn = stbutton.html();
    stbutton.removeAttr("onclick");
    stbutton.html('<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

    $(".thetinymce").each(function () {
        var idi = $(this).attr("id");
        $("#" + idi).html(tinymce.get(idi).getContent());
    });

    $("#IlanOlusturForm_output").fadeOut(400);
    $("#IlanOlusturForm").ajaxForm({
        target: '#IlanOlusturForm_output',
        complete: function () {
            $("#IlanOlusturForm_output").fadeIn(400);
            stbutton.attr("onclick", stonc);
            stbutton.html(stinn);
        }
    }).submit();
}

$(document).ready(function () {
    $(".tipi_arsa,.arsa_icin").hide(1);
    $("select[name='emlak_tipi']").change(function () {
        var val = $(this).val();

        var select = "#yetki_bilgisi_con,#brut_metrekare_con,#site_id_888_con,#site_id_777_con,#site_id_700_con,#site_id_701_con,#site_id_661_con,#site_id_662_con,#site_id_663_con,#site_id_664_con,#site_id_665_con,#site_id_666_con,#site_id_667_con,#site_id_668_con,#site_id_669_con,#site_id_335_con,#site_id_334_con,#site_id_306_con,#konut_sekli_con,#konut_tipi_con,#bulundugu_kat_con,#yapi_durum_con,#oda_sayisi_con,#bina_yasi_con,#bina_kat_sayisi_con,#isitma_con,#banyo_sayisi_con,#esyali_con,#notu_con,#kullanim_durum_con,#site_ici_con,#aidat_con";

        if (val == '<?=$arsa;?>') {
            $(select).fadeOut(500);
            $(".tipi_konut").slideUp(500);
            $(".tipi_arsa,.arsa_icin").slideDown(500);
        } else {
            $(select).fadeIn(500);
            $(".tipi_arsa,.arsa_icin").slideUp(500);
            $(".tipi_konut").slideDown(500);
        }
    });
});

function konut_getir(tipi) {
    if (tipi == "<?=$isyeri;?>") {
        $("select[name=konut_tipi]").html("<?php
        $knttipi = dil("KNT_TIPI2");
        ?><option value=''><?=dil("TX57");?></option><?php
        $parc = explode("<+>", $knttipi);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php
        }
        ?>");
    } else {
        $("select[name=konut_tipi]").html("<?php
        $knttipi = dil("KNT_TIPI");
        ?><option value=''><?=dil("TX57");?></option><?php
        $parc = explode("<+>", $knttipi);
        foreach ($parc as $val) {
        ?><option><?=$val;?></option><?php
        }
        ?>");
    }
}
</script>


<script type="text/javascript">
/* Map Ayarları */

function yazdir() {
    let ulke = $("#ulke_id").val();
    ulke = $("#ulke_id option[value='" + ulke + "']").text();
    let il = $("#il").val();
    il = $("#il option[value='" + il + "']").text();
    let ilce = $("#ilce").val();
    ilce = $("#ilce option[value='" + ilce + "']").text();
    let maha = $("#semt").val();
    maha = $("#semt option[value='" + maha + "']").text();
    let cadde = $("input[name='map_cadde']").val();
    let sokak = $("input[name='map_sokak']").val();
    let neler = "";

    if (il && il !== '<?=dil("TX264");?>') {
        if (ulke && ulke !== '<?=dil("TX264");?>') {
            neler += ", " + ulke;
        }
        neler += il;
        $("#map_il").val(il);
        if (ilce && ilce !== '<?=dil("TX264");?>') {
            neler += ", " + ilce;
            $("#map_ilce").val(ilce);
            if (maha && maha !== '<?=dil("TX264");?>') {
                neler += ", " + maha;
                $("#map_mahalle").val(maha);
            } else {
                $("#map_mahalle").val('');
            }

            if (cadde && cadde !== '<?=dil("TX264");?>') {
                neler += ", " + cadde;
            }

            if (sokak && sokak !== '<?=dil("TX264");?>') {
                neler += ", " + sokak;
            }
        } else {
            $("#map_ilce").val('');
        }
    } else {
        $("#map_il").val('');
    }
    $("input[name='map_adres']").val(neler);
    GetMap();
}

function GetMap() {
    $("#map_adres").trigger("change");
}
</script>

<script>
function openTab(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("etabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("etablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("edefaultOpen").click();
</script>

<style>
/* Style the list */
ul.etab {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Float the list items side by side */
ul.etab li { float: left; }

/* Style the links inside the list items */
ul.etab li a {
    display: inline-block;
    color: black;
    text-align: center;
    padding: 14px 13px;
    text-decoration: none;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of links on hover */
ul.etab li a:hover { background-color: #ddd; }

/* Create an active/current tablink class */
ul.etab li a:focus, .active { background-color: #ccc; }

/* Style the tab content */
.etabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}
.etabcontent h4 { margin-top: 10px; }
.etabcontent {
    -webkit-animation: fadeEffect 1s;
    animation: fadeEffect 1s; /* Fading effect takes 1 second */
}

@-webkit-keyframes fadeEffect {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeEffect {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<?php } ?>

<?php } // eğer galeri değil ise ?>

</div>

<div id="TamamDiv" style="display:none">

<!-- TAMAM MESAJ -->
<div style="margin-bottom: 70px; text-align: center;" id="BasvrTamam">
    <i style="font-size: 80px; color: green;" class="fa fa-check"></i>
    <h2 style="color: green; font-weight: bold;"><?=dil("TX300");?></h2>
    <br/>
    <h4><?=dil("TX301");?></h4>
</div>
<!-- TAMAM MESAJ -->
<div id="asama_result" style="display:none"></div>

</div>

</div>
</div>
</div>

<div class="clear"></div>

</div>

</div>