<div class="altbaslik">

<h4><i class="fa fa-search" aria-hidden="true"></i> <strong><?=dil("TX620");?></strong></h4>

</div>



<div class="gelismisara">

<form action="ajax.php?p=ilanlar2" method="POST" id="IlanlarAramaForm">

<input name="how" type="hidden" value="<?=$how;?>">

<input name="q" type="text" value="<?=$q;?>" placeholder="<?=dil("TX52");?>">



<?php

$emlkdrm	= dil("EMLK_DRM");

if($emlkdrm != ''){

?>

<select name="emlak_durum">

        <option value=""><?=dil("TX53");?></option>

		<?php

		$parc		= explode("<+>",$emlkdrm);

		foreach($parc as $val){

		?><option <?=($val == $emlak_durum) ? 'selected' : '';?>><?=$val;?></option><?

		}

		?>

</select>

<? } ?>



<?php

$emlktp		= dil("EMLK_TIPI");

if($emlktp != ''){

?>

<select name="emlak_tipi" onchange="konut_getir(this.options[this.selectedIndex].value);">

        <option value=""><?=dil("TX54");?></option>

		<?php

		$parc		= explode("<+>",$emlktp);

    	$isyeri 	= $parc[1];

    	$arsa   	= $parc[2];

		foreach($parc as $val){

		?><option <?=($val == $emlak_tipi) ? 'selected' : '';?>><?=$val;?></option><?

		}

		?>

</select>

<script type="text/javascript">

$(document).ready(function(){

	$("select[name=emlak_tipi]").change(function(){

		if($(this).val() == "<?=$isyeri;?>"){

			$("select[name=konut_tipi] option:eq(0)").text("<?=$isyeri." ".dil("TX666");?>");
			$("select[name=konut_sekli] option:eq(0)").text("<?=$isyeri." ".dil("TX667");?>");

		}else{

			$("select[name=konut_tipi] option:eq(0)").text("<?=dil("TX57");?>");
			$("select[name=konut_sekli] option:eq(0)").text("<?=dil("TX58");?>");

		}

	});

});

</script>

<? } ?>



<?php

if($emlak_tipi == $isyeri){

$knttipi		= dil("KNT_TIPI2");

}else {

$knttipi		= dil("KNT_TIPI");

}

if($knttipi != ''){

?>

<select name="konut_tipi">

        <option value=""><?=dil("TX57");?></option>

		<?php

		$parc		= explode("<+>",$knttipi);

		foreach($parc as $val){

		?><option <?=($val == $konut_tipi) ? 'selected' : '';?>><?=$val;?></option><?

		}

		?>

</select>

<? } ?>



<?php

$kntsekli		= dil("KNT_SEKLI");

if($kntsekli != ''){

?>

<select name="konut_sekli">

        <option value=""><?=dil("TX58");?></option>

		<?php

		$parc		= explode("<+>",$kntsekli);

		foreach($parc as $val){

		?><option <?=($val == $konut_sekli) ? 'selected' : '';?>><?=$val;?></option><?

		}

		?>

</select>

<? } ?>





<?php

$ulkeler	= $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");

$ulkelerc	= $ulkeler->rowCount();

if($ulkelerc>1){

if($il != '' && $il !=0){

$yakalail	= $db->prepare("SELECT ulke_id FROM il WHERE id=?");

$yakalail->execute(array($il));

if($yakalail->rowCount()>0){

$yakalail	= $yakalail->fetch(PDO::FETCH_OBJ);

}

}

?>

<select name="ulke_id" onchange="ajaxHere('ajax.php?p=il_getir&ulke_id='+this.options[this.selectedIndex].value,'il');">

        <option value=""><?=dil("TX348");?></option>

        <?php

		while($row	= $ulkeler->fetch(PDO::FETCH_OBJ)){

		?><option value="<?=$row->id;?>" <?=($yakalail->ulke_id == $row->id) ? 'selected' :'';?>><?=$row->ulke_adi;?></option><?

		}

		?>

</select>

<?}?>





<select name="il" id="il" onchange="ajaxHere('ajax.php?p=ilce_getir&varsa=1&il_id='+this.options[this.selectedIndex].value,'ilce');">

        <option value=""><?=dil("TX55");?></option>

        <?php

		if($ulkelerc<2){

		$ulke		= $ulkeler->fetch(PDO::FETCH_OBJ);

		$sql		= $db->query("SELECT id,il_adi FROM il WHERE ulke_id=".$ulke->id." ORDER BY id ASC");

		}elseif($yakalail!=false){

		$sql		= $db->query("SELECT id,il_adi FROM il WHERE ulke_id=".$yakalail->ulke_id." ORDER BY id ASC");

		}else{

		$sql		= NULL;

		}

		if($sql != NULL){

		while($row	= $sql->fetch(PDO::FETCH_OBJ)){

		?><option value="<?=$row->id;?>" <?=($row->id == $il) ? 'selected' : '';?>><?=$row->il_adi;?></option><?

		}

		}

		?>

</select>



<select name="ilce" id="ilce" onchange="ajaxHere('ajax.php?p=mahalle_getir&ilce_id='+this.options[this.selectedIndex].value,'mahalle');">

        <option value=""><?=dil("TX56");?></option>

        <?php

		if($il != ''){

		$sql		= $db->prepare("SELECT id,ilce_adi FROM ilce WHERE il_id=? ORDER BY id ASC");

		$sql->execute(array($il));

		}else{

		$sql		= '';

		}



		if($sql != ''){

		while($row	= $sql->fetch(PDO::FETCH_OBJ)){

		?><option value="<?=$row->id;?>" <?=($row->id == $ilce) ? 'selected' : '';?>><?=$row->ilce_adi;?></option><?

		}

		}

		?>

</select>



<select name="mahalle" id="mahalle">

        <option value=""><?=dil("TX266");?></option>

        <?php

		if($ilce != ''){

			$semtler	= $db->query("SELECT * FROM semt WHERE ilce_id=".$ilcem->id);

			if($semtler->rowCount()>0){

			while($srow	= $semtler->fetch(PDO::FETCH_OBJ)){

			$mahalleler	= $db->query("SELECT * FROM mahalle_koy WHERE semt_id=".$srow->id." AND ilce_id=".$ilcem->id." ORDER BY mahalle_adi ASC");

			if($mahalleler->rowCount()>0){

			?><optgroup label="<?=$srow->semt_adi;?>"><?

			while($row	= $mahalleler->fetch(PDO::FETCH_OBJ)){

			?><option value="<?=$row->id;?>" <?=($mahalle == $row->id) ? 'selected' : '';?>><?=$row->mahalle_adi;?></option><?

			}

			}

			?></optgroup><?

			}

			}else{

			$mahalleler	= $db->query("SELECT * FROM mahalle_koy WHERE ilce_id=".$ilcem->id." ORDER BY mahalle_adi ASC");

			while($row	= $mahalleler->fetch(PDO::FETCH_OBJ)){

			?><option value="<?=$row->id;?>" <?=($mahalle == $row->id) ? 'selected' : '';?>><?=$row->mahalle_adi;?></option><?

			}

			}

		}

		?>

</select>





<?php

$bulundkat		= dil("BULND_KAT");

if($bulundkat != ''){

?>

<select name="bulundugu_kat">

        <option value=""><?=dil("TX59");?></option>

		<?php

		$parc		= explode("<+>",$bulundkat);

		foreach($parc as $val){

		?><option <?=($val == $bulundugu_kat) ? 'selected' : '';?>><?=$val;?></option><?

		}

		?>

</select>

<? } ?>



<input name="min_fiyat" type="text" value="<?=($min_fiyat != 0) ? $min_fiyat : '';?>" id="yariminpt" placeholder="<?=dil("TX60");?>">

<input name="max_fiyat" type="text" value="<?=($max_fiyat != 0) ? $max_fiyat : '';?>" id="yariminpt" placeholder="<?=dil("TX61");?>">



<input name="min_metrekare" type="text" value="<?=($min_metrekare != 0) ? $min_metrekare : '';?>" id="yariminpt" placeholder="<?=dil("TX62");?>">

<input name="max_metrekare" type="text" value="<?=($max_metrekare != 0) ? $max_metrekare : '';?>" id="yariminpt" placeholder="<?=dil("TX63");?>">



<input name="min_bina_kat_sayisi" type="text" value="<?=($min_bina_kat_sayisi != 0) ? $min_bina_kat_sayisi : '';?>" id="yariminpt" placeholder="<?=dil("TX64");?>">

<input name="max_bina_kat_sayisi" type="text" value="<?=($max_bina_kat_sayisi != 0) ? $max_bina_kat_sayisi : '';?>" id="yariminpt" placeholder="<?=dil("TX65");?>">



<?php

$yapidrm		= dil("YAPI_DRM");

if($yapidrm != ''){

?>

<select name="yapi_durum">

        <option value=""><?=dil("TX66");?></option>

		<?php

		$parc		= explode("<+>",$yapidrm);

		foreach($parc as $val){

		?><option <?=($val == $yapi_durum) ? 'selected' : '';?>><?=$val;?></option><?

		}

		?>

</select>

<? } ?>



<select name="ilan_tarih">

        <option value=""><?=dil("TX67");?></option>

        <option value="bugun" <?=($ilan_tarih == "bugun") ? "selected" : '';?>><?=dil("TX68");?></option>

		<option value="son3" <?=($ilan_tarih == "son3") ? "selected" : '';?>><?=dil("TX69");?></option>

		<option value="son7" <?=($ilan_tarih == "son7") ? "selected" : '';?>><?=dil("TX70");?></option>

		<option value="son14" <?=($ilan_tarih == "son14") ? "selected" : '';?>><?=dil("TX71");?></option>

		<option value="son21" <?=($ilan_tarih == "son21") ? "selected" : '';?>><?=dil("TX72");?></option>

		<option value="son1ay" <?=($ilan_tarih == "son1ay") ? "selected" : '';?>><?=dil("TX73");?></option>

		<option value="son2ay" <?=($ilan_tarih == "son2ay") ? "selected" : '';?>><?=dil("TX74");?></option>

</select>



<div class="clear"></div>

<br />

<input id="resimli" class="checkbox-custom" name="resimli" value="true" type="checkbox" <?=($resimli=="true")? 'checked' : '';?> style="width:100px;">

<label for="resimli" class="checkbox-custom-label" style="margin-bottom:5px;"><span class="checktext"><?=dil("TX613");?></span></label>

<div class="clear"></div>



<input id="videolu" class="checkbox-custom" name="videolu" value="true" type="checkbox" <?=($videolu=="true")? 'checked' : '';?> style="width:100px;">

<label for="videolu" class="checkbox-custom-label"><span class="checktext"><?=dil("TX614");?></span></label>

<div class="clear"></div>

<br />



<a href="javascript:;" onclick="AjaxFormS('IlanlarAramaForm','IlanlarAramaForm_sonuc');" class="gonderbtn"><i class="fa fa-search" aria-hidden="true"></i> <?=dil("TX75");?></a>

<input type="hidden" name="order" value="<?=$orderg;?>" />

<?if($sicak == "true"){?>

<input type="hidden" name="sicak" value="true" />

<?}?>



<?if($vitrin == "true"){?>

<input type="hidden" name="vitrin" value="true" />

<?}?>



<?if($onecikan == "true"){?>

<input type="hidden" name="onecikan" value="true" />

<?}?>

</form>

<script type="text/javascript">

function konut_getir(tipi){

if(tipi == "<?=$isyeri;?>"){



  $("select[name=konut_tipi]").html("<?php

  $knttipi		= dil("KNT_TIPI2");



  ?><option value=''><?=dil("TX57");?></option><?

  $parc		= explode("<+>",$knttipi);

  foreach($parc as $val){

  ?><option><?=$val;?></option><?

  }

  ?>");



}else{

  $("select[name=konut_tipi]").html("<?php

  $knttipi		= dil("KNT_TIPI");



  ?><option value=''><?=dil("TX57");?></option><?

  $parc		= explode("<+>",$knttipi);

  foreach($parc as $val){

  ?><option><?=$val;?></option><?

  }

  ?>");

}

}

</script>

<div id="IlanlarAramaForm_sonuc" style="display:none"></div>

</div>