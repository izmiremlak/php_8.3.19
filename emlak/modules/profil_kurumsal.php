<?php if(!defined("THEME_DIR")){die();}?><!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?php echo htmlspecialchars($name); echo ($on == 'hakkinda') ? ' ' . dil("TX425") : ''; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
<meta name="keywords" content="<?php echo htmlspecialchars($name); ?>" />
<meta name="description" content="<?php echo htmlspecialchars($name); ?>" />
<meta name="robots" content="All" />  
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<base href="<?=SITE_URL;?>" />

<?php include THEME_DIR."inc/head.php"; ?>

</head>
<body>

<?php if($hesap->id == ''){ ?>
<div id="uyemsjgonder" class="modalDialog">
<div>
<div style="padding:20px;">
<a href="<?=REQUEST_URL;?>#!" title="Close" class="close">X</a>
<h2><strong><?=$name;?></strong> / <?=dil("TX413");?></h2><br>
<center><strong><?=dil("TX414");?></strong>
<div class="clear"></div><br><br>
<a href="giris-yap" class="gonderbtn"><i class="fa fa-sign-in" aria-hidden="true"></i> <?=dil("TX356");?></a><div class="clearmob"></div> <span><?=dil("TX415");?></span> <a href="hesap-olustur" class="gonderbtn"><i class="fa fa-user-plus" aria-hidden="true"></i> <?=dil("TX125");?></a>
<br><br><br>
</center>
<div class="clear"></div>
</div>
</div>
</div>
<?php } ?>

<?php if($hesap->id != '' && $profil->tipi == 0){ ?>
<div id="uyemsjgonder" class="modalDialog">
<div>
<div style="padding:20px;">
<a href="<?=REQUEST_URL;?>#!" title="Close" class="close">X</a>
<h2><strong><?=$name;?></strong> / <?=dil("TX413");?></h2>
<form action="ajax.php?p=mesaj_gonder&uid=<?=$profil->id;?>&from=adv" method="POST" id="MesajGonderForm">
<textarea rows="3" name="mesaj" id="MesajYaz"></textarea>
<a href="javascript:;" onclick="AjaxFormS('MesajGonderForm','MesajGonderSonuc');" style="float:right;" class="gonderbtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?=dil("TX405");?></a>
</form>
<div id="TamamPnc" style="display:none"><?=dil("TX423");?></div>
<div class="clear"></div>
<div id="MesajGonderSonuc" style="display:none"></div>
</div>
</div>
</div>
<?php } ?>


<?php include THEME_DIR."inc/header.php"; ?>


<div id="kfirmaprofili" class="headerbg" <?=($gayarlar->video_galeri_resim  != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->video_galeri_resim) . ');"' : ''; ?>>
<div id="wrapper">
<div class="headtitle">
<h1><?=$name;?></h1>
<?php if($kurumsal->unvan != ''){
$kurumsal_link = ($kurumsal->nick_adi == '') ? $kurumsal->id : $kurumsal->nick_adi;
?>
<div class="sayfayolu">
<span><a id="kurumsalprofillink" href="profil/<?=$kurumsal_link;?>"><?=$kurumsal->unvan;?></a></span>
</div>
<?php } ?>
</div>
</div>
</div>


<div class="clear"></div>

<div class="kurumsalbtns">
<div id="wrapper">
<a href="<?=$uyelink;?>" <?=($on == '') ? 'id="kurumsalbtnaktif"' : '';?>><?=($profil->turu == 2) ? dil("TX159") : dil("TX631");?></a>
<?php if($profil->hakkinda != ''){?><a href="<?=$uyelink;?>/hakkinda" <?=($on == "hakkinda") ? 'id="kurumsalbtnaktif"' : '';?>><?=dil("TX425");?></a><?php } ?>
<?php if($profil->turu == 1 && $danismanlari > 0){?><a href="<?=$uyelink;?>/danismanlar" <?=($on == "danismanlar") ? 'id="kurumsalbtnaktif"' : '';?>><?=dil("TX629");?></a><?php } ?>
<a href="<?=$uyelink;?>/portfoy" <?=($on == "portfoy") ? 'id="kurumsalbtnaktif"' : '';?>><?=dil("TX630");?></a>
<?php if($gayarlar->anlik_sohbet == 1 && $profil->tipi == 0){?><a href="<?=REQUEST_URL;?>#uyemsjgonder" class="gonderbtn"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> <?=dil("TX392");?></a><?php } ?>
</div>
</div> 


<div id="wrapper">

<?php
if($gayarlar->reklamlar == 1){ // Eğer reklamlar aktif ise...
$detect = (!isset($detect)) ? new Mobile_Detect : $detect;

$rtipi = 10;
$reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1)");
$rcount = $reklamlar->rowCount();
$order = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
$reklam = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1) " . $order . " LIMIT 0,1")->fetch(PDO::FETCH_OBJ);
if($rcount > 0){
?>
<!-- 728 x 90 Reklam Alanı -->
<div class="clear"></div>
<div class="ad728home">
<?=($detect->isMobile() || $detect->isTablet()) ? $reklam->mobil_kodu : $reklam->kodu;?>
</div>
<!-- 728 x 90 Reklam Alanı END-->
<?php }} // Eğer reklamlar aktif ise... ?>


<div class="content" <?php if($on == "portfoy"){ ?>style="float:left;" <?php }else{ ?>id="bigcontent"<?php } ?>>

<?php if($on == ""){ ?>
<!-- FİRMA DETAYI -->
<div class="profilfirmadetay">
<div class="firmaprofililetisim">

<div style="padding:17px;">
<div class="pfirmalogo">
<img src="<?=$avatar;?>" alt="<?=htmlspecialchars($name);?>" title="<?=htmlspecialchars($name);?>" width="100%" height="auto">
</div> 


<div class="fprofilinfos">
<h4><strong><?=$name;?></strong></h4><h5><?php echo ($il_adi != '') ? htmlspecialchars($il_adi) : ''; echo ($ilce_adi != '') ? ' / ' . htmlspecialchars($ilce_adi) : ''; echo ($mahalle_adi != '') ? ' / ' . htmlspecialchars($mahalle_adi) : ''; ?></h5>

<?php if(($profil->sabit_telefon != '' && $profil->sabittelefond == 0) || ($profil->telefon != '' && $profil->telefond == 0)){?>
<span>
    <i class="fa fa-phone"></i>
    <?php if ($profil->sabit_telefon != '' && $profil->sabittelefond == 0) { ?>
        <a href="tel:<?=$profil->sabit_telefon;?>"><?=$profil->sabit_telefon;?></a><br>
    <?php } ?>
    <?php if ($profil->telefon != '' && $profil->telefond == 0) { ?>
        <a href="tel:<?=$profil->telefon;?>"><?=$profil->telefon;?> <?=dil("TX156");?></a>
    <?php } ?>
</span>

<div class="clear"></div>
<?php } ?>
<span>
<a href="mailto:<?=htmlspecialchars($profil->email);?>">
    <i class="fa fa-envelope"></i> <?=htmlspecialchars($profil->email);?>
</a>
</span>
<span>
<a href="https://<?=htmlspecialchars($profil->webadres);?>" target="_blank" rel="noopener noreferrer">
    <i class="fa fa-globe"></i> <?=htmlspecialchars($profil->webadres);?>
</a></span>
<div class="clear"></div>
<?php if($adres != ''){ ?>
<span>
<i class="fa fa-map-marker" style="margin-bottom:25px;"></i>
<?=htmlspecialchars($adres);?></span>
<?php } ?>

</div>

</div>
</div>


<?php if($maps != ''){ ?>
<div class="fprofilmap">
<?php
    $coords = ($maps == '') ? "40.9729012,28.6386138" : $maps;
    list($lat, $lng) = explode(",", $coords);
?>

    <div id="map" style="width: 100%; height: 300px"></div>
    <input type="hidden" value="<?php echo htmlspecialchars($lat); ?>" id="g_lat">
    <input type="hidden" value="<?php echo htmlspecialchars($lng); ?>" id="g_lng">

<script type="text/javascript">
      function initMap() {
        var g_lat = parseFloat(document.getElementById("g_lat").value);
        var g_lng = parseFloat(document.getElementById("g_lng").value);
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 12,
          center: {lat: g_lat, lng: g_lng}
        });
        var geocoder = new google.maps.Geocoder();
        
        var marker = new google.maps.Marker({
            position:{
              lat: g_lat,
              lng: g_lng
            },
            map: map
          });
       
      }     
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gayarlar->google_api_key; ?>&callback=initMap"></script>
</div>
<?php } ?>

</div>
<!-- FİRMA DETAYI END-->
<?php } ?>

<?php if($on == "hakkinda" && $profil->hakkinda != ''){ ?>
<!-- FİRMA HAKKINDA -->
<div class="profilfirmahakkinda">
<img style="float:right;margin:10px;" src="<?=$avatar;?>" alt="<?=htmlspecialchars($name);?>" title="<?=htmlspecialchars($name);?>" width="200" height="auto">

<p><?=htmlspecialchars($profil->hakkinda);?></p>

</div>
<!-- FİRMA HAKKINDA END -->
<?php } ?>


<?php if($on == "danismanlar" && $profil->turu == 1 && $danismanlari > 0){ ?>
<!-- FİRMA DANIŞMANLAR -->
<div class="profildanismanlar">
    <div class="list_carousel" id="anadanismanlar">
        <ul id="foo55">
            <?php
            // Danışmanları veritabanından çek ve listele
            $sql = $db->query("SELECT id, kid, adi, soyadi, avatar, avatard, nick_adi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND durum=0 AND turu=2 AND kid=" . $profil->id . " ORDER BY id DESC");
            while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                $plink = "profil/";
                $plink .= ($row->nick_adi == '') ? $row->id : $row->nick_adi;
                $avatar = ($row->avatar == '' || $row->avatard == 1) ? 'https://www.turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : 'https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/' . $row->avatar;
            ?>
                <li>
                    <a href="<?= $plink; ?>">
                        <div class="anadanisman">
                            <div class="danismanfotoana" style="background-image: url(<?= $avatar; ?>);"></div>
                            <div class="danismanbilgisi">
                                <h4><?= htmlspecialchars($row->adi . " " . $row->soyadi); ?></h4>
                            </div>
                        </div>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<!-- FİRMA DANIŞMANLAR END-->
<?php } ?>

<?php if($on == "portfoy"){

$filtre = $gvn->html_temizle($_GET["filtre"]);

if($filtre != ''){
$search_link = $uyelink."/";
$filtresc = (stristr($filtre,"/"));
$filtreb = @explode("/",$filtre);
$filtreb = array_diff($filtreb,array(""," "));
$filtreby = $filtreb;

$emlkdrm1 = explode("<+>",dil("EMLK_DRM"));
$emlktp1 = explode("<+>",dil("EMLK_TIPI"));
$konutskli1 = explode("<+>",dil("KNT_SEKLI"));
$knttipi1 = explode("<+>",dil("KNT_TIPI"));
$knttipi1x = explode("<+>",dil("KNT_TIPI2"));
$knttipi1 = array_merge($knttipi1,$knttipi1x);

$emlkdrm2 = $gvn->PermaLinkArray($emlkdrm1);
$emlktp2 = $gvn->PermaLinkArray($emlktp1);
$konutskli2 = $gvn->PermaLinkArray($konutskli1);
$knttipi2 = $gvn->PermaLinkArray($knttipi1);

$tanimlar = array(
'emlak_durum' => $emlkdrm1,
'emlak_tipi' => $emlktp1,
'konut_sekli' => $konutskli1,
'konut_tipi' => $knttipi1
);
$kontroller = array(
'emlak_durum' => $emlkdrm2,
'emlak_tipi' => $emlktp2,
'konut_sekli' => $konutskli2,
'konut_tipi' => $knttipi2
);

foreach($filtreb as $k=>$ney){
	foreach($kontroller as $varadi=>$dizi){
		if (false !== $key = array_search($ney, $dizi)){
		if(!isset(${$varadi})){
		${$varadi} = $tanimlar[$varadi][$key];
		unset($filtreb[$k]);
		}
		}
	}
}

$cfiltreb = count($filtreb);
if($cfiltreb > 0 AND $cfiltreb <= 1){ // Son kalan 1 veya 2 parametreyi alıyoruz...
$il = 0;
$ilce = 0;
$mahalle = 0;
foreach($filtreb as $fi){

$kontrol = $db->prepare("SELECT id FROM il WHERE slug=?");
$kontrol->execute(array($fi));
if($kontrol->rowCount() > 0){ // il için kontrol start
$res = $kontrol->fetch(PDO::FETCH_OBJ);
$il = $res->id;
}else{ // ilçe için kontrol start
$kontrol = $db->prepare("SELECT id, il_id FROM ilce WHERE slug2=?");
$kontrol->execute(array($fi));
if($kontrol->rowCount() > 0){
$res = $kontrol->fetch(PDO::FETCH_OBJ);
$il = $res->il_id;
$ilce = $res->id;
}else{ // Mahalle için kontrol start
$kontrol = $db->prepare("SELECT id, il_id, ilce_id FROM mahalle_koy WHERE slug2=?");
$kontrol->execute(array($fi));
if($kontrol->rowCount() > 0){
$res = $kontrol->fetch(PDO::FETCH_OBJ);
$il = $res->il_id;
$ilce = $res->ilce_id;
$mahalle = $res->id;
}
} // Mahalle için kontrol end
} // ilçe için kontrol end

} // foreach end
} // Son kalan filtreyi taratıyoruz...

}else{
$search_link = $uyelink."/"."portfoy";
$emlak_durum = $gvn->html_temizle($_GET["emlak_durum"]);
$emlak_tipi = $gvn->html_temizle($_GET["emlak_tipi"]);
$konut_sekli = $gvn->html_temizle($_GET["konut_sekli"]);
$konut_tipi = $gvn->html_temizle($_GET["konut_tipi"]);
$il = $gvn->rakam($_GET["il"]);
$ilce = $gvn->rakam($_GET["ilce"]);
$mahalle = $gvn->rakam($_GET["mahalle"]);
}

$filtre_count = 0;
$aradigi_sey = array();
$git = $gvn->rakam($_GET["git"]);
$sicak = $gvn->harf_rakam($_GET["sicak"]);
$vitrin = $gvn->harf_rakam($_GET["vitrin"]);
$onecikan = $gvn->harf_rakam($_GET["onecikan"]);
$resimli = $gvn->harf_rakam($_GET["resimli"]);
$videolu = $gvn->harf_rakam($_GET["videolu"]);
$q = $gvn->html_temizle($_GET["q"]);
$bulundugu_kat = $gvn->html_temizle($_GET["bulundugu_kat"]);
$min_fiyat = $gvn->prakam($_GET["min_fiyat"]);
$max_fiyat = $gvn->prakam($_GET["max_fiyat"]);
$min_metrekare = $gvn->rakam($_GET["min_metrekare"]);
$max_metrekare = $gvn->rakam($_GET["max_metrekare"]);
$min_bina_kat_sayisi = $gvn->rakam($_GET["min_bina_kat_sayisi"]);
$max_bina_kat_sayisi = $gvn->rakam($_GET["max_bina_kat_sayisi"]);
$yapi_durum = $gvn->html_temizle($_GET["yapi_durum"]);
$ilan_tarih = $gvn->html_temizle($_GET["ilan_tarih"]);

// Gelen filtreleme isteklerinin hepsi boşsa indexe yönlendiriyoruz...
if(
$fonk->bosluk_kontrol($q) == true AND
$fonk->bosluk_kontrol($emlak_durum) == true AND
$fonk->bosluk_kontrol($emlak_tipi) == true AND
$fonk->bosluk_kontrol($il) == true AND
$fonk->bosluk_kontrol($ilce) == true AND
$fonk->bosluk_kontrol($mahalle) == true AND
$fonk->bosluk_kontrol($konut_tipi) == true AND
$fonk->bosluk_kontrol($konut_sekli) == true AND
$fonk->bosluk_kontrol($bulundugu_kat) == true AND
$fonk->bosluk_kontrol($min_fiyat) == true AND
$fonk->bosluk_kontrol($max_fiyat) == true AND
$fonk->bosluk_kontrol($min_metrekare) == true AND
$fonk->bosluk_kontrol($max_metrekare) == true AND
$fonk->bosluk_kontrol($min_bina_kat_sayisi) == true AND
$fonk->bosluk_kontrol($max_bina_kat_sayisi) == true AND
$fonk->bosluk_kontrol($yapi_durum) == true AND
$fonk->bosluk_kontrol($ilan_tarih) == true AND
$fonk->bosluk_kontrol($sicak) == true AND
$fonk->bosluk_kontrol($vitrin) == true AND
$fonk->bosluk_kontrol($onecikan) == true AND
$fonk->bosluk_kontrol($resimli) == true AND
$fonk->bosluk_kontrol($videolu) == true
){
#header("Location: index.html");
#die();
}

// Emlak Durumu için filtre...
if($emlak_durum != ''){

$aradigi_sey[] = $emlak_durum;

$dahili_query .= "AND t1.emlak_durum=? ";
$execute[] = $emlak_durum;

$search_link .= $gvn->PermaLink($emlak_durum)."/";

}

// Emlak Tipi için filtre...
if($emlak_tipi != ''){

$aradigi_sey[] = $emlak_tipi;

$dahili_query .= "AND t1.emlak_tipi=? ";
$execute[] = $emlak_tipi;

$ekslsh = (substr($search_link,-1)=='/') ? '' : '/';
$search_link .= $ekslsh.$gvn->PermaLink($emlak_tipi);

}

// Konut Şekli için filtre...
if($konut_sekli != ''){

$aradigi_sey[] = $konut_sekli;

$dahili_query .= "AND t1.konut_sekli=? ";
$execute[] = $konut_sekli;
$ekslsh = (substr($search_link,-1)=='/') ? '' : '/';
$search_link .= $ekslsh.$gvn->PermaLink($konut_sekli);

}

// Konut Tipi için filtre...
if($konut_tipi != ''){

$aradigi_sey[] = $konut_tipi;

$dahili_query .= "AND t1.konut_tipi=? ";
$execute[] = $konut_tipi;
$ekslsh = (substr($search_link,-1)=='/') ? '' : '/';
$search_link .= $ekslsh.$gvn->PermaLink($konut_tipi);
}

// İl için filtre...
if($il != ''){
$ilkontrol = $db->prepare("SELECT id, il_adi, slug FROM il WHERE id=? ORDER BY id ASC");
$ilkontrol->execute(array($il));
if($ilkontrol->rowCount() > 0){
$ilim = $ilkontrol->fetch(PDO::FETCH_OBJ);

$aradigi_sey[] = $ilim->il_adi;

$dahili_query .= "AND t1.il_id=? ";
$execute[] = $il;
$ekslsh = (substr($search_link,-1)=='-') ? '' : '-';
$search_link .= $ekslsh.$ilim->slug;

}
}

// İlçe için filtre...
if($ilce != ''){
$ilcekontrol = $db->prepare("SELECT id, ilce_adi, slug FROM ilce WHERE id=? ORDER BY id ASC");
$ilcekontrol->execute(array($ilce));
if($ilcekontrol->rowCount() > 0){
$ilcem = $ilcekontrol->fetch(PDO::FETCH_OBJ);

$aradigi_sey[] = $ilcem->ilce_adi;

$dahili_query .= "AND t1.ilce_id=? ";
$execute[] = $ilce;
$ekslsh = (substr($search_link,-1)=='-') ? '' : '-';
$search_link .= $ekslsh.$ilcem->slug;

}
}

// Mahalle için filtre...
if($ilce != '' AND $mahalle != ''){
$mahkontrol = $db->prepare("SELECT * FROM mahalle_koy WHERE id=? ORDER BY id ASC");
$mahkontrol->execute(array($mahalle));
if($mahkontrol->rowCount() > 0){
$mahallem = $mahkontrol->fetch(PDO::FETCH_OBJ);

$aradigi_sey[] = $mahallem->mahalle_adi;

$dahili_query .= "AND t1.mahalle_id=? ";
$execute[] = $mahalle;
$ekslsh = (substr($search_link,-1)=='-') ? '' : '-';
$search_link .= $ekslsh.$mahallem->slug;

}
}

$search_link = rtrim($search_link,"-");
$search_link = rtrim($search_link,"/");

// Resimli ilanlar için filtre
if($resimli == "true"){
$filtre_count += 1;
$aradigi_sey[] = dil("TX613");
$bgrs = ($filtre_count < 2) ? '?' : '&';
$dahili_query .= "AND (SELECT COUNT(id) FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=t1.id)>0 ";
$search_link .= $bgrs."resimli=true";
}

// Videolu ilanlar için filtre
if($videolu == "true"){
$filtre_count += 1;
$aradigi_sey[] = dil("TX614");
$bgrs = ($filtre_count < 2) ? '?' : '&';
$dahili_query .= "AND t1.video!='' ";
$search_link .= $bgrs."videolu=true";
}

// Kelime veya İlan No ile arama için filtre
if($q != '' AND strlen($q) < 255){
$filtre_count += 1;
$aradigi_sey[] = $q;
$bgrs = ($filtre_count < 2) ? '?' : '&';
$dahili_query .= "AND (t1.baslik LIKE ? OR t1.ilan_no LIKE ?) ";
$execute[] = "%".$q."%";
$execute[] = "%".$q."%";
$search_link .= $bgrs."q=".$q;
}

<?php
// Bulunduğu Kat için filtre...
if ($bulundugu_kat != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX59") . " " . $bulundugu_kat;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.bulundugu_kat=? ";
    $execute[] = $bulundugu_kat;
    $search_link .= $bgrs . "bulundugu_kat=" . $bulundugu_kat;
}

// Min Fiyat için filtre...
if ($min_fiyat != '' && strlen($min_fiyat) < 24 && $min_fiyat != 0) {
    $min_fiyat_int = $gvn->para_int($min_fiyat);
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX317") . " " . $gvn->para_str($min_fiyat_int);
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.fiyat >=? ";
    $execute[] = $min_fiyat_int;
    $search_link .= $bgrs . "min_fiyat=" . $min_fiyat;
}

// Max Fiyat için filtre...
if ($max_fiyat != '' && strlen($max_fiyat) < 24 && $max_fiyat != 0) {
    $max_fiyat_int = $gvn->para_int($max_fiyat);
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX318") . " " . $gvn->para_str($max_fiyat_int);
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.fiyat <=? ";
    $execute[] = $max_fiyat_int;
    $search_link .= $bgrs . "max_fiyat=" . $max_fiyat;
}

// Min Metrekare için filtre...
if ($min_metrekare != '' && strlen($min_metrekare) < 11 && !stristr($min_metrekare, '.')) {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX319") . " " . $min_metrekare;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.metrekare >=? ";
    $execute[] = $min_metrekare;
    $search_link .= $bgrs . "min_metrekare=" . $min_metrekare;
}

// Max Metrekare için filtre...
if ($max_metrekare != '' && strlen($max_metrekare) < 11 && !stristr($max_metrekare, '.')) {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX320") . " " . $max_metrekare;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.metrekare <=? ";
    $execute[] = $max_metrekare;
    $search_link .= $bgrs . "max_metrekare=" . $max_metrekare;
}

// Min Bina Kat Sayısı için filtre...
if ($min_bina_kat_sayisi != '' && strlen($min_bina_kat_sayisi) < 11 && !stristr($min_bina_kat_sayisi, '.')) {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX321") . " " . $min_bina_kat_sayisi;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.bina_kat_sayisi >=? ";
    $execute[] = $min_bina_kat_sayisi;
    $search_link .= $bgrs . "min_bina_kat_sayisi=" . $min_bina_kat_sayisi;
}

// Max Bina Kat Sayısı için filtre...
if ($max_bina_kat_sayisi != '' && strlen($max_bina_kat_sayisi) < 11 && !stristr($max_bina_kat_sayisi, '.')) {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX322") . " " . $max_bina_kat_sayisi;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.bina_kat_sayisi <=? ";
    $execute[] = $max_bina_kat_sayisi;
    $search_link .= $bgrs . "max_bina_kat_sayisi=" . $max_bina_kat_sayisi;
}

// İlan Tarihi için filtre...
if ($ilan_tarih != '') {
    $islem = '';
    switch ($ilan_tarih) {
        case "bugun":
            $islem = "tarih LIKE '%" . date("Y-m-d") . "%'";
            break;
        case "son3":
            $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 3 DAY)";
            break;
        case "son7":
            $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            break;
        case "son14":
            $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 2 WEEK)";
            break;
        case "son21":
            $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 3 WEEK)";
            break;
        case "son1ay":
            $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            break;
        case "son2ay":
            $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 2 MONTH)";
            break;
    }
    if ($islem != '') {
        $filtre_count += 1;
        $bgrs = ($filtre_count < 2) ? '?' : '&';
        $dahili_query .= "AND t1." . $islem . " ";
        $search_link .= $bgrs . "ilan_tarih=" . $ilan_tarih;
    }
}

// Order by için işlemler...
$orderi = $gvn->html_temizle($_REQUEST["order"]);
$search_linkx = $search_link;
if ($fonk->bosluk_kontrol($orderi) == true) {
    $dahili_order = "ORDER BY t1.id DESC";
} else {
    $orderivr = 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "order=" . $orderi;
    switch ($orderi) {
        case 'fiyat_asc':
            $dahili_order = "ORDER BY CAST(t1.fiyat AS DECIMAL(10,2)) ASC";
            break;
        case 'fiyat_desc':
            $dahili_order = "ORDER BY CAST(t1.fiyat AS DECIMAL(10,2)) DESC";
            break;
        default:
            $dahili_order = "ORDER BY t1.id ASC";
            break;
    }
}

// Profil türüne göre danışmanlar belirleme
if ($profil->turu == 1) {
    $dids = $db->query("SELECT kid, id, GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=" . $profil->id)->fetch(PDO::FETCH_OBJ);
    $danismanlar = $dids->danismanlar;
    $acids = ($danismanlar == '') ? $profil->id : $profil->id . ',' . $danismanlar;
} else {
    $acids = $profil->id;
}

// İlan sorgusu
$qry = $pagent->sql_query("SELECT t1.ilan_no, t1.id, t1.url, t1.fiyat, t1.tarih, t1.il_id, t1.ilce_id, t1.emlak_durum, t1.emlak_tipi, t1.resim, t1.baslik, t1.pbirim, t1.metrekare, t2.id AS t2_id FROM sayfalar AS t1 LEFT JOIN dopingler_501 AS t2 ON t2.ilan_id=t1.id AND t2.did=4 AND t2.durum=1 AND t2.btarih>NOW() WHERE (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND (t1.btarih>NOW() OR t2.btarih>NOW() OR EXISTS (SELECT btarih FROM dopingler_501 WHERE ilan_id=t1.id AND durum=1 AND btarih>NOW())) AND t1.acid IN(" . $acids . ") AND t1.durum=1 AND t1.tipi=4 AND t1.ekleme=1 " . $dahili_query . " GROUP BY t1.ilan_no " . $dahili_order, $git, 12, $execute);

try {
    $query = $qry['sql'];
    $query = $db->prepare($query);
    $query->execute($execute);
} catch (PDOException $e) {
    echo $e->getMessage();
}

$adet = $qry['toplam'];
$ne_ariyor = implode(" + ", $aradigi_sey);

$orbgrs = ($filtre_count < 1) ? '?' : '&';

?>
<!-- FİRMA İLANLARI -->
<div class="pfirmailanlari">

<?php if ($adet > 0) { ?>
    <span style="float:left;margin-bottom:15px;"><b><?=$name;?></b> <?=dil("TX627");?> <b><?=$adet;?></b> <?=dil("TX628");?></span>
    <div class="clear"></div>

    <div class="list_carousel">
        <ul id="foo44">
            <?php
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $id = $row->id;
                $row_lang = $db->query("SELECT t1.ilan_no, t1.id, t1.url, t1.fiyat, t1.tarih, t1.il_id, t1.ilce_id, t1.emlak_durum, t1.emlak_tipi, t1.resim, t1.baslik, t1.pbirim, t1.metrekare FROM sayfalar AS t1 WHERE (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.ilan_no=" . $row->ilan_no . " AND t1.dil='" . $dil . "' ");
                if ($row_lang->rowCount() > 0) {
                    $row = $row_lang->fetch(PDO::FETCH_OBJ);
                    $row->id = $id;
                }
                
                $link = ($dayarlar->permalink == 'Evet') ? $row->url . '.html' : 'index.php?p=sayfa&id=' . $row->id;
                if ($row->fiyat != 0) {
                    $fiyat_int = $gvn->para_int($row->fiyat);
                    $fiyat = $gvn->para_str($fiyat_int);
                }
                $sc_il = $db->query("SELECT il_adi FROM il WHERE id=" . $row->il_id)->fetch(PDO::FETCH_OBJ);
                $sc_ilce = $db->query("SELECT ilce_adi FROM ilce WHERE id=" . $row->ilce_id)->fetch(PDO::FETCH_OBJ);
            ?>
                <li>
                    <a href="<?=$link;?>">
                        <div class="kareilan">
                            <span class="ilandurum" <?php echo ($row->emlak_durum == $emstlk) ? 'id="satilik"' : ''; echo ($row->emlak_durum == $emkrlk) ? 'id="kiralik"' : ''; ?>><?=$row->emlak_durum;?> / <?=$row->emlak_tipi;?></span>
                            <img title="<?=$row->baslik;?>" alt="<?=$row->baslik;?>" src="https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$row->resim;?>" width="234" height="201">
                            <div class="fiyatlokasyon" <?php echo ($row->emlak_durum == $emkrlk) ? 'id="lokkiralik"' : ''; ?>>
                                <?php if ($row->fiyat != '' || $row->fiyat != 0) { ?><h3><?=$fiyat;?> <?=$row->pbirim;?></h3><?php } ?> 
                                <h4><?=$sc_il->il_adi;?> / <?=$sc_ilce->ilce_adi;?></h4>
                            </div>
                            <div class="kareilanbaslik">
                                <h3><?=$fonk->kisalt($row->baslik, 0, 45);?><?=(strlen($row->baslik) > 45) ? '...' : '';?></h3>
                            </div> 
                        </div>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } else { ?>
    <h4 style="text-align:center;margin-top:60px;"><?=dil("TX187");?></h4>
<?php } ?>

<?php if ($adet > 0) {
    $bgrs = ($filtre_count < 1 && $orderivr != 1) ? '?' : '&';
?>
<div class="clear"></div>
<div class="sayfalama">
    <?php echo $pagent->listele($search_link . $bgrs . 'git=', $git, $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
</div>
<?php } ?>

</div>

</div>

<!-- FİRMA İLANLARI END -->
<?php } ?>

<?php if ($on == "portfoy") { ?>
<div class="sidebar" style="float:right;">
    <?php include THEME_DIR . "inc/advanced_search2.php"; ?>
</div>
<?php } ?>

<div class="clear"></div>
</div>

<?php include THEME_DIR . "inc/footer.php"; ?>


