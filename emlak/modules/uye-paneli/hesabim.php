<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

$avatar = ($hesap->avatar == '') ? 'https://www.turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : 'https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/' . htmlspecialchars($hesap->avatar, ENT_QUOTES, 'UTF-8');
$uturu = explode(",", dil("UYELIK_TURLERI"));

if ($hesap->turu == 1) {
    $paketi = $db->query("SELECT adi FROM upaketler_501 WHERE acid=" . (int)$hesap->id . " AND durum=1 AND btarih>NOW()");
    if ($paketi->rowCount() > 0) {
        $paketi = $paketi->fetch(PDO::FETCH_OBJ);
        $paketine = " (" . htmlspecialchars($paketi->adi, ENT_QUOTES, 'UTF-8') . ")";
    }
}

if ($hesap->turu == 2) {
    $kurumsal = $db->prepare("SELECT id, concat_ws(' ', adi, soyadi) AS adsoyad, unvan FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
    $kurumsal->execute(array((int)$hesap->kid));
    if ($kurumsal->rowCount() > 0) {
        $kurumsal = $kurumsal->fetch(PDO::FETCH_OBJ);
    }
}
?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX446"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <span><?= htmlspecialchars(dil("TX447"), ENT_QUOTES, 'UTF-8'); ?></span>
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
    $rtipi = 6;
    $reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1)");
    $rcount = $reklamlar->rowCount();
    $order = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
    $reklam = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1) " . $order . " LIMIT 0,1")->fetch(PDO::FETCH_OBJ);
    if ($rcount > 0) {
?>
<!-- 728 x 90 Reklam Alanı -->
<div class="ad728home">
    <?= ($detect->isMobile() || $detect->isTablet()) ? htmlspecialchars($reklam->mobil_kodu, ENT_QUOTES, 'UTF-8') : htmlspecialchars($reklam->kodu, ENT_QUOTES, 'UTF-8'); ?>
</div>
<!-- 728 x 90 Reklam Alanı END-->
<?php
    }
} // Eğer reklamlar aktif ise...
?>

<div class="uyedetay">
<div class="uyeolgirisyap">
    <h4 class="uyepaneltitle"><?= htmlspecialchars(dil("TX245"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <form action="ajax.php?p=hesabim" method="POST" id="HesapForm">
    <div id="accordion">

    <h3><?= htmlspecialchars(dil("TX615"), ENT_QUOTES, 'UTF-8'); ?></h3>
    <div><!-- üyelik bilgisi -->
    <table width="100%" border="0">
    <tr>
        <td><?= htmlspecialchars(dil("TX362"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td>
        <strong><?= ($hesap->turu == 2) ? htmlspecialchars($uturu[$hesap->turu], ENT_QUOTES, 'UTF-8') . " (" . htmlspecialchars($kurumsal->unvan, ENT_QUOTES, 'UTF-8') . ")" : htmlspecialchars($uturu[$hesap->turu], ENT_QUOTES, 'UTF-8'); ?></strong>
        </td>
    </tr>

    <tr>
    <td><?= ($hesap->turu == 1) ? htmlspecialchars(dil("TX619"), ENT_QUOTES, 'UTF-8') : htmlspecialchars(dil("TX363"), ENT_QUOTES, 'UTF-8'); ?></td>
    <td>

    <script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#avatar_image')
                    .attr('src', e.target.result)
                    .width(130)
                    .height(128);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
    <input type="file" name="avatar" id="avatar" style="display:none;" onchange="readURL(this)" />
    <div class="uyeavatar">
    <a title="Foto Yükle" class="avatarguncelle" href="javascript:void(0);" onclick="$('#avatar').click();" ><i class="fa fa-camera" aria-hidden="true"></i></a>
    <img src="<?= $avatar; ?>" id="avatar_image" /><br>
    <span style="font-size: 13px; color: #777;"><?= htmlspecialchars(dil("TX654"), ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
    </td>
    </tr>
    <tr>
        <td><?= ($hesap->turu == 1) ? htmlspecialchars(dil("TX665"), ENT_QUOTES, 'UTF-8') : htmlspecialchars(dil("TX396"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td>
        <div class="prufilurllink">
        <a target="_blank" href="profil/<?= ($hesap->nick_adi == '') ? (int)$hesap->id : htmlspecialchars($hesap->nick_adi, ENT_QUOTES, 'UTF-8'); ?>"><?= SITE_URL; ?>profil/<?= ($hesap->nick_adi == '') ? (int)$hesap->id : htmlspecialchars($hesap->nick_adi, ENT_QUOTES, 'UTF-8'); ?></a>
        </div>
        </td>
    </tr>
    <tr>
        <td><?= htmlspecialchars(dil("TX246"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="adsoyad" type="text" disabled value="<?= htmlspecialchars($hesap->adi, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($hesap->soyadi, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>
    <tr>
        <td><?= htmlspecialchars(dil("TX247"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="email" type="text" value="<?= htmlspecialchars($hesap->email, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX248"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="telefon" type="text" id="gsm" value="<?= htmlspecialchars($hesap->telefon, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX390"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="sabit_telefon" type="text" id="telefon" value="<?= htmlspecialchars($hesap->sabit_telefon, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>
    <!-- devamı için file2.php'yi inceleyin -->
    </table>
    </div><!-- üyelik bilgisi end-->
 

    <?php if ($hesap->turu == 1) { ?>
    <h3><?= htmlspecialchars(dil("TX652"), ENT_QUOTES, 'UTF-8'); ?></h3>
    <div><!-- hakkında-->
    <table width="100%" border="0">

    <tr class="turu_1">
        <td><?= htmlspecialchars(dil("TX366"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="unvan" type="text" id="unvan" placeholder="" value="<?= htmlspecialchars($hesap->unvan, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>

    <tr class="turu_1">
        <td><?= htmlspecialchars(dil("TX367"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="vergi_no" type="text" id="vergi_no" placeholder="" value="<?= htmlspecialchars($hesap->vergi_no, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>

    <tr class="turu_1">
        <td><?= htmlspecialchars(dil("TX368"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="vergi_dairesi" type="text" id="vergi_dairesi" placeholder="" value="<?= htmlspecialchars($hesap->vergi_dairesi, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX365"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="adres" type="text" id="adres" placeholder="" value="<?= htmlspecialchars($hesap->adres, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>

    <tr>
        <td colspan="2">
        <span style="margin-bottom:10px; float: left;"><strong><?= htmlspecialchars(dil("TX644"), ENT_QUOTES, 'UTF-8'); ?></strong></span><div class="clear"></div>
        <textarea style="width:100%;" name="hakkinda" class="thetinymce" id="hakkinda" placeholder="<?= htmlspecialchars(dil("TX429"), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($hesap->hakkinda, ENT_QUOTES, 'UTF-8'); ?></textarea>
        </td>
    </tr>
    </table>
    </div><!-- hakkında end-->


    <h3><?= htmlspecialchars(dil("TX616"), ENT_QUOTES, 'UTF-8'); ?></h3>
    <div><!-- Adres ve Konum Bilgileri-->
    <table width="100%" border="0">
    <?php
    $ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
    $ulkelerc = $ulkeler->rowCount();
    if ($ulkelerc > 1) {
    ?>
    <tr>
        <td><?= htmlspecialchars(dil("TX348"), ENT_QUOTES, 'UTF-8'); ?> <span style="color:red">*</span></td>
        <td>
    <select id="ulke_id" name="ulke_id" onchange="yazdir();ajaxHere('ajax.php?p=il_getir&ulke_id='+this.options[this.selectedIndex].value,'il');">
            <option value=""><?= htmlspecialchars(dil("TX264"), ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
            while ($row = $ulkeler->fetch(PDO::FETCH_OBJ)) {
            ?><option value="<?= (int)$row->id; ?>" <?= ($hesap->ulke_id == $row->id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->ulke_adi, ENT_QUOTES, 'UTF-8'); ?></option><?php
            }
            ?>
    </select>
        </td>
    </tr>
    <?php } ?>


    <tr>
        <td><?= htmlspecialchars(dil("TX263"), ENT_QUOTES, 'UTF-8'); ?> <span style="color:red">*</span></td>
        <td>
    <select id="il" name="il" onchange="ajaxHere('ajax.php?p=ilce_getir&il_id='+this.options[this.selectedIndex].value,'ilce'),yazdir(),$('#semt').html('');">
            <option value=""><?= htmlspecialchars(dil("TX264"), ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
            if ($ulkelerc < 2) {
            $ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
            $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id=" . (int)$ulke->id . " ORDER BY id ASC");
            } else {
            $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id=" . (int)$hesap->ulke_id . " ORDER BY id ASC");
            }
            while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
            if ($row->id == $hesap->il_id) {
            $il_adi = $row->il_adi;
            }
            ?><option value="<?= (int)$row->id; ?>" <?= ($row->id == $hesap->il_id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->il_adi, ENT_QUOTES, 'UTF-8'); ?></option><?php
            }
            ?>
    </select>
        </td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX265"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td>
    <select name="ilce" id="ilce" onchange="ajaxHere('ajax.php?p=mahalle_getir&ilce_id='+this.options[this.selectedIndex].value,'semt'),yazdir();">
            <option value=""><?= htmlspecialchars(dil("TX264"), ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="0"><?= htmlspecialchars(dil('TX349'), ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
            if ($hesap->il_id != '') {
            $sql = $db->query("SELECT id, ilce_adi FROM ilce WHERE il_id=" . (int)$hesap->il_id . " ORDER BY id ASC");
            while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
            if ($row->id == $hesap->ilce_id) {
            $ilce_adi = $row->ilce_adi;
            }
            ?><option value="<?= (int)$row->id; ?>" <?= ($row->id == $hesap->ilce_id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->ilce_adi, ENT_QUOTES, 'UTF-8'); ?></option><?php
            }
            }
            ?>
    </select>
        </td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX266"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td>
        <select onchange="yazdir();" name="mahalle" id="semt">
            <option value=""><?= htmlspecialchars(dil("TX264"), ENT_QUOTES, 'UTF-8'); ?></option>
            <option value="0"><?= htmlspecialchars(dil('TX349'), ENT_QUOTES, 'UTF-8'); ?></option>
            <?php
            if ($hesap->ilce_id != 0) {
                $semtler = $db->query("SELECT * FROM semt WHERE ilce_id=" . (int)$hesap->ilce_id);
                if ($semtler->rowCount() > 0) {
                while ($srow = $semtler->fetch(PDO::FETCH_OBJ)) {
                $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE semt_id=" . (int)$srow->id . " AND ilce_id=" . (int)$hesap->ilce_id . " ORDER BY mahalle_adi ASC");
                if ($mahalleler->rowCount() > 0) {
                ?><optgroup label="<?= htmlspecialchars($srow->semt_adi, ENT_QUOTES, 'UTF-8'); ?>"><?php
                while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                if ($hesap->mahalle_id == $row->id) {
                $mahalle_adi = $row->mahalle_adi;
                }
                ?><option value="<?= (int)$row->id; ?>" <?= ($hesap->mahalle_id == $row->id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->mahalle_adi, ENT_QUOTES, 'UTF-8'); ?></option><?php
                }
                }
                ?></optgroup><?php
                }
                } else {
                $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE ilce_id=" . (int)$hesap->ilce_id . " ORDER BY mahalle_adi ASC");
                while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                if ($hesap->mahalle_id == $row->id) {
                $mahalle_adi = $row->mahalle_adi;
                }
                ?><option value="<?= (int)$row->id; ?>" <?= ($hesap->mahalle_id == $row->id) ? 'selected' : ''; ?>><?= htmlspecialchars($row->mahalle_adi, ENT_QUOTES, 'UTF-8'); ?></option><?php
                }
                }
            }
            ?>
        </select>
        </td>
    </tr>
	
<tr>
    <td colspan="2">
        <strong><i class="fa fa-map-marker" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX289"), ENT_QUOTES, 'UTF-8'); ?></strong>
        <br><span style="font-size:13px;"><?= htmlspecialchars(dil("TX0"), ENT_QUOTES, 'UTF-8'); ?><?= htmlspecialchars(dil("TX290"), ENT_QUOTES, 'UTF-8'); ?></span>
        <br><br>
        <div class="gmapsecenek">
            <input disabled class="form-control" id="map_il" value="<?= htmlspecialchars($il_adi, ENT_QUOTES, 'UTF-8'); ?>" type="text" placeholder="Şehir">
            <input disabled id="map_ilce" class="form-control" value="<?= htmlspecialchars($ilce_adi, ENT_QUOTES, 'UTF-8'); ?>" type="text" placeholder="İlçe">
            <input disabled onchange="yazdir();" type="text" id="map_mahalle" class="form-control" value="<?= htmlspecialchars($mahalle_adi, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Mahalle">
            <input onchange="yazdir();" type="text" class="form-control" name="map_cadde" value="" placeholder="Cadde">
            <input onchange="yazdir();" type="text" class="form-control" name="map_sokak" value="" placeholder="Sokak">
        </div>

        <input type="text" class="form-control" id="map_adres" name="map_adres" placeholder="Adres yaziniz..." style="display: none;">
        <input type="text" id="coords" name="maps" value="<?= htmlspecialchars($hesap->maps, ENT_QUOTES, 'UTF-8'); ?>" style="display:none;" />

        <div id="map" style="width: 100%; height: 300px"></div>

        <?php
        $coords = ($hesap->maps == '') ? '41.003917,28.967299' : htmlspecialchars($hesap->maps, ENT_QUOTES, 'UTF-8');
        list($lat, $lng) = explode(",", $coords);
        ?>
        <input type="hidden" value="<?= htmlspecialchars($lat, ENT_QUOTES, 'UTF-8'); ?>" id="g_lat">
        <input type="hidden" value="<?= htmlspecialchars($lng, ENT_QUOTES, 'UTF-8'); ?>" id="g_lng">
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
                    position: {
                        lat: g_lat,
                        lng: g_lng
                    },
                    map: map,
                    draggable: true
                });

                jQuery('#map_adres').on('change', function() {
                    var val = $(this).val();
                    geocodeAddress(marker, geocoder, map, val);
                });

                google.maps.event.addListener(marker, 'dragend', function() {
                    dragend(marker);
                });

            }

            function geocodeAddress(marker, geocoder, resultsMap, address) {
                if (address) {
                    geocoder.geocode({ 'address': address }, function(results, status) {
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
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($gayarlar->google_api_key, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap"></script>
    </td>
</tr>


  </table>
</div><!-- Adres ve Konum Bilgileri end-->
<? } ?>

<h3><?=($hesap->turu == 1 || $hesap->turu == 2) ? dil("TX617") : dil("TX618");?></h3>
<div><!-- Profil ve Bildirim Ayarları-->
<table width="100%" border="0">

<tr style="font-size:13px;">
       <td colspan="2">
	   
	   <? if($hesap->turu == 0){ ?>
       <h5 style="margin-bottom:7px;"><strong><?=dil("TX398");?></strong></h5>
       <input id="telefond_check" class="checkbox-custom" name="telefond" value="1" type="checkbox" <?php echo ($hesap->telefond == 1) ? 'checked' : '';?> style="width:100px;">
	   <label for="telefond_check" class="checkbox-custom-label"><span class="checktext"><?=dil("TX386");?></span></label>
       <div class="clear" style="margin-bottom:5px;"></div>
	   
	   <input id="sabittelefond_check" class="checkbox-custom" name="sabittelefond" value="1" type="checkbox" <?php echo ($hesap->sabittelefond == 1) ? 'checked' : '';?> style="width:100px;">
	   <label for="sabittelefond_check" class="checkbox-custom-label"><span class="checktext"><?=dil("TX387");?></span></label>
       <div class="clear" style="margin-bottom:5px;"></div>
	   
	   <input id="epostad_check" class="checkbox-custom" name="epostad" value="1" type="checkbox" <?php echo ($hesap->epostad == 1) ? 'checked' : '';?> style="width:100px;">
	   <label for="epostad_check" class="checkbox-custom-label"><span class="checktext"><?=dil("TX388");?></span></label>
       <div class="clear" style="margin-bottom:5px;"></div>
	   
	   <input id="avatard_check" class="checkbox-custom" name="avatard" value="1" type="checkbox" <?php echo ($hesap->avatard == 1) ? 'checked' : '';?> style="width:100px;">
	   <label for="avatard_check" class="checkbox-custom-label"><span class="checktext"><?=dil("TX389");?></span></label>
       <div class="clear" style="margin-bottom:5px;"></div>
	   
	   <h5 style="margin-bottom:7px;margin-top:10px;"><strong><?=dil("TX399");?></strong></h5>
        <? } ?>
		
		<input id="checkbox-6" class="checkbox-custom" name="mail_izin" value="1" type="checkbox" <?php echo ($hesap->mail_izin == 1) ? 'checked' : '';?> style="width:100px;">
	   <label for="checkbox-6" class="checkbox-custom-label"><span class="checktext"><?=dil("TX251");?></span></label>
       <div class="clear" style="margin-bottom:5px;"></div>
       <input id="checkbox-7" class="checkbox-custom" name="sms_izin" value="1" type="checkbox" <?php echo ($hesap->sms_izin == 1) ? 'checked' : '';?> style="width:100px;">
	   <label for="checkbox-7" class="checkbox-custom-label"><span class="checktext"><?=dil("TX252");?></span></label>
	   
       </td>
     </tr>
</table>
</div><!-- Profil ve Bildirim Ayarları end-->
  
</div><!-- tab end -->

<div class="clear"></div>
<br />
<div id="HesapForm_Snc"></div>

<a href="javascript:" id="SubmitBtn" onclick="HesabimSubmit();" class="btn"><i class="fa fa-refresh"></i> <?=dil("TX253");?></a>


  </form>
  
  <style>
  #accordion table tr td {
    padding: 5px;
}
  </style>

<script src="<?=THEME_DIR;?>tinymce/tinymce.min.js"></script>
<script type="application/x-javascript">
tinymce.init({
	selector:"#hakkinda",
    height: 200,
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
function HesabimSubmit(){
var stbutton = $("#SubmitBtn");
var stonc 	 = stbutton.attr("onclick");
var stinn  	 = stbutton.html();
stbutton.removeAttr("onclick");
stbutton.html('<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
<?if($hesap->turu==1){?>$('#hakkinda').html(tinymce.get('hakkinda').getContent());<?}?>
$("#HesapForm_Snc").fadeOut(400);
$("#HesapForm").ajaxForm({
target: '#HesapForm_Snc',
complete:function(){
$("#HesapForm_Snc").fadeIn(400);
stbutton.attr("onclick",stonc);
stbutton.html(stinn);
}
}).submit();
}

  /* Map Ayarları */

	function yazdir(type){
	var ulke 	= $("#ulke_id").val();
	ulke 		= $("#ulke_id option[value='"+ulke+"']").text();
	var il 		= $("#il").val();
	il 			= $("#il option[value='"+il+"']").text();
	var ilce 	= $("#ilce").val();
	ilce		= $("#ilce option[value='"+ilce+"']").text();
	var maha 	= $("#semt").val();
	maha		= $("#semt option[value='"+maha+"']").text();
	var cadde 	= $("input[name='map_cadde']").val();
	var sokak 	= $("input[name='map_sokak']").val();
	var neler 	= "";

	if(il != undefined && il != '' && il != '<?=dil("TX264");?>'){
	if(ulke != undefined && ulke!='' && ulke != '<?=dil("TX264");?>'){
	neler		+=", "+ulke;
	}
	neler 		+=il;
	$("#map_il").val(il);
	if(ilce != undefined && ilce != '' && ilce != '<?=dil("TX264");?>' && ilce != '<?=dil("TX56");?>'){
	neler +=", "+ilce;
	$("#map_ilce").val(ilce);
		if(maha != undefined && maha != '' && maha != '<?=dil("TX264");?>'){
		neler += ", "+maha;
		$("#map_mahalle").val(maha);
		
		}else{
		$("#map_mahalle").val('');
		}
		
		if(cadde != undefined && cadde != '' && cadde != '<?=dil("TX264");?>'){
		neler += ", "+cadde;
		
		}
		
		if(sokak != undefined && sokak != '' && sokak != '<?=dil("TX264");?>'){
		neler += ", "+sokak;
		}
	}else{
	$("#map_ilce").val('');
	}
	}else{
	$("#map_il").val('');
	}
	$("input[name='map_adres']").val(neler);
	GetMap();
	}

	function GetMap(){
		$("#map_adres").trigger("change");
	}
  </script>
</div>

</div>
</div><div class="sidebar">
<? include THEME_DIR."inc/uyepanel_sidebar.php"; ?>
</div>
</div>
<div class="clear"></div>


</div>