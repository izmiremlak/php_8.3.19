<?php
if(!$_POST){
die();
}
$filtre					= '';
$search_link			= SITE_URL;
$filtre_count			= 0;
$bgrsyok				= 0;
$sicak					= $gvn->harf_rakam($_POST["sicak"]);
$vitrin					= $gvn->harf_rakam($_POST["vitrin"]);
$onecikan				= $gvn->harf_rakam($_POST["onecikan"]);
$resimli				= $gvn->harf_rakam($_POST["resimli"]);
$videolu				= $gvn->harf_rakam($_POST["videolu"]);
$order					= $gvn->harf_rakam($_POST["order"]);
$q						= $gvn->html_temizle($_POST["q"]);
$emlak_durum			= $gvn->html_temizle($_POST["emlak_durum"]);
$emlak_tipi				= $gvn->html_temizle($_POST["emlak_tipi"]);
$il						= $gvn->rakam($_POST["il"]);
$ilce					= $gvn->rakam($_POST["ilce"]);
$mahalle				= $gvn->rakam($_POST["mahalle"]);
$konut_tipi				= $gvn->html_temizle($_POST["konut_tipi"]);
$konut_sekli			= $gvn->html_temizle($_POST["konut_sekli"]);
$bulundugu_kat			= $gvn->html_temizle($_POST["bulundugu_kat"]);
$kaks_emsal 			= $gvn->html_temizle($_POST["kaks_emsal"]);
$gabari 				= $gvn->html_temizle($_POST["gabari"]);
$imar_durum 			= $gvn->html_temizle($_POST["imar_durum"]);
$tapu_durumu 			= $gvn->html_temizle($_POST["tapu_durumu"]);
$katk			        = $gvn->html_temizle($_POST["katk"]);
$krediu			        = $gvn->html_temizle($_POST["krediu"]);
$takas			        = $gvn->html_temizle($_POST["takas"]);
$min_fiyat				= $gvn->prakam($_POST["min_fiyat"]);
$max_fiyat				= $gvn->prakam($_POST["max_fiyat"]);
$min_metrekare			= $gvn->rakam($_POST["min_metrekare"]);
$max_metrekare			= $gvn->rakam($_POST["max_metrekare"]);
$min_bina_kat_sayisi	= $gvn->rakam($_POST["min_bina_kat_sayisi"]);
$max_bina_kat_sayisi	= $gvn->rakam($_POST["max_bina_kat_sayisi"]);
$yapi_durum				= $gvn->html_temizle($_POST["yapi_durum"]);
$ilan_tarih				= $gvn->html_temizle($_POST["ilan_tarih"]);


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
$fonk->bosluk_kontrol($kaks_emsal) == true AND
$fonk->bosluk_kontrol($gabari) == true AND
$fonk->bosluk_kontrol($imar_durum) == true AND
$fonk->bosluk_kontrol($tapu_durumu) == true AND
$fonk->bosluk_kontrol($katk) == true AND
$fonk->bosluk_kontrol($krediu) == true AND
$fonk->bosluk_kontrol($takas) == true AND
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
#$fonk->yonlendir("index.html",1);
die();
}


if($q != ''){
$adresy = 0;
$parserle = explode(" ",$q);
foreach($parserle AS $parse){
if($fonk->bosluk_kontrol($parse)==false){
if($il == ''){ // adres il kontrolü
$kontrol = $db->prepare("SELECT * FROM il WHERE il_adi LIKE ?");
$kontrol->execute(array("%".$parse."%"));
if($kontrol->rowCount()>0){
  $sonuc = $kontrol->fetch(PDO::FETCH_OBJ);
  $il    = $sonuc->id;
  echo $sonuc->il_adi."<br>";
  $adresy +=1;
}
} // Adres il kontrolü

if($ilce == '' && $il != ''){ // adres ilçe kontrolü
  $kontrol = $db->prepare("SELECT * FROM ilce WHERE ilce_adi LIKE ? AND il_id=?");
  $kontrol->execute(array("%".$parse."%",$il));
  if($kontrol->rowCount()>0){
    $sonuc = $kontrol->fetch(PDO::FETCH_OBJ);
    $ilce  = $sonuc->id;
    echo $sonuc->ilce_adi."<br>";
    $adresy +=1;
  }
  } // Adres ilçe kontrolü
/*
  if($mahalle == '' && $ilce != ''){ // adres mahalle kontrolü
    $kontrol = $db->prepare("SELECT * FROM mahalle_koy WHERE mahalle_adi LIKE ? AND ilce_id=?");
    $kontrol->execute(array("%".$parse."%",$ilce));
    if($kontrol->rowCount()>0){
      $sonuc = $kontrol->fetch(PDO::FETCH_OBJ);
      $mahalle  = $sonuc->id;
      echo $sonuc->mahalle_adi."<br>";
      $adresy +=1;
    }
    } // Adres mahalle kontrolü */
}
}
if($adresy>0){
  $q = '';
}
}

// Emlak Durumu için filtre...
if($emlak_durum != ''){
$bgrsyok		+=1;
$getemlkdrm		= $gvn->permaLink($emlak_durum);
$search_link	.= $getemlkdrm."/";
}

// Emlak Tipi için filtre...
if($emlak_tipi != ''){
$bgrsyok		+=1;
$getemlktipi	= $gvn->PermaLink($emlak_tipi);
$search_link	.= $getemlktipi."/";
}

// Konut Şekli için filtre...
if($konut_sekli != ''){
$bgrsyok		+=1;
$getkonutskli	= $gvn->PermaLink($konut_sekli);
$search_link	.= $getkonutskli."/";
}

// Konut Tipi için filtre...
if($konut_tipi != ''){
$bgrsyok		+=1;
$getkonuttpi	= $gvn->PermaLink($konut_tipi);
$search_link	.= $getkonuttpi."/";
}


// İl için filtre...
if($il != ''){
$ilkontrol		= $db->prepare("SELECT id,il_adi,slug FROM il WHERE id=? ORDER BY id ASC");
$ilkontrol->execute(array($il));
if($ilkontrol->rowCount() > 0){
$ilim			= $ilkontrol->fetch(PDO::FETCH_OBJ);
$bgrsyok		+=1;
$search_link	.= $ilim->slug."-";
}
}

// İlçe için filtre...
if($ilce != '' AND $ilim->id != ''){
$ilcekontrol	= $db->prepare("SELECT id,ilce_adi,slug FROM ilce WHERE id=? ORDER BY id ASC");
$ilcekontrol->execute(array($ilce));
if($ilcekontrol->rowCount() > 0){
$ilcem			= $ilcekontrol->fetch(PDO::FETCH_OBJ);
$bgrsyok		+=1;
$search_link	.= $ilcem->slug."-";
}
}

// Mahalle için filtre...
if($mahalle != '' AND $ilcem->id != '' AND $ilim->id != ''){
$mahkontrol		= $db->prepare("SELECT id,slug FROM mahalle_koy WHERE id=? ORDER BY id ASC");
$mahkontrol->execute(array($mahalle));
if($mahkontrol->rowCount() > 0){
$mahallem		= $mahkontrol->fetch(PDO::FETCH_OBJ);
$bgrsyok		+=1;
$search_link	.= $mahallem->slug."-";
}
}

$search_link	= rtrim($search_link,"-");
$search_link	= ($bgrsyok>0) ? rtrim($search_link,"/") : $search_link;
$search_link	.= ($bgrsyok>0) ? '' : "ilanlar";


// sıcak fırsatlar için filtre
if($sicak == "true"){
$filtre_count	+=1;
$bgrs			= ($filtre_count < 2) ? '?' : '&';
$search_link	.= $bgrs."sicak=true";
}

<?php
// Vitrin ilanları için filtre
if ($vitrin == "true") {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "vitrin=true";
}

// Öne çıkan ilanlar için filtre
if ($onecikan == "true") {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "onecikan=true";
}

// Resimli ilanlar için filtre
if ($resimli == "true") {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "resimli=true";
}

// Videolu ilanlar için filtre
if ($videolu == "true") {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "videolu=true";
}

// Kelime veya İlan No ile arama için filtre
if ($q != '') {
$varmikontrol	= $db->prepare("SELECT id,url FROM sayfalar WHERE (site_id_555=501 OR (site_id_888=100 AND durum=1 AND il_id=35) OR (site_id_777=501501 AND durum=1) OR (site_id_699=200 AND durum=1 AND il_id=35) OR (site_id_701=501501 AND durum=1) OR (site_id_702=300 AND durum=1)) AND ilan_no=? AND tipi=4 AND durum=1 AND dil='".$dil."' ");
    $varmikontrol->execute([$q, $dil]);
    if ($varmikontrol->rowCount() > 0) {
        $ilani = $varmikontrol->fetch(PDO::FETCH_OBJ);
        $linki = ($dayarlar->permalink == 'Evet') ? $ilani->url . '.html' : 'index.php?p=sayfa&id=' . $ilani->id;
        $fonk->yonlendir($linki, 1);
        die();
    }

    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND (baslik LIKE '%" . $q . "%' OR ilan_no LIKE '%" . $q . "%') ";
    $search_link .= $bgrs . "q=" . $q;
}

// Bulunduğu Kat için filtre
if ($bulundugu_kat != '') {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND bulundugu_kat='" . $bulundugu_kat . "' ";
    $search_link .= $bgrs . "bulundugu_kat=" . $bulundugu_kat;
}

// Kaks emsal için filtre
if ($kaks_emsal != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX331") . ": " . $kaks_emsal;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "kaks_emsal=" . $kaks_emsal;
}

// Gabari için filtre
if ($gabari != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX332") . ": " . $gabari;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "gabari=" . $gabari;
}

// İmar durumu için filtre
if ($imar_durum != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX682") . ": " . $imar_durum;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "imar_durum=" . $imar_durum;
}

// Tapu durumu için filtre
if ($tapu_durumu != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX333") . ": " . $tapu_durumu;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "tapu_durumu=" . $tapu_durumu;
}

// Kat karşılığı için filtre
if ($katk != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX334") . ": " . $katk;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "katk=" . $katk;
}

// Kredi uygunluk için filtre
if ($krediu != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX335") . ": " . $krediu;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "krediu=" . $krediu;
}

// Takas için filtre
if ($takas != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX336") . ": " . $takas;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "takas=" . $takas;
}

// Min fiyat için filtre
if ($min_fiyat != '' && strlen($min_fiyat) < 24) {
    $min_fiyat_int = $gvn->para_int($min_fiyat);
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND fiyat >= " . $min_fiyat_int . " ";
    $search_link .= $bgrs . "min_fiyat=" . $min_fiyat;
}

// Max fiyat için filtre
if ($max_fiyat != '' && strlen($max_fiyat) < 24) {
    $max_fiyat_int = $gvn->para_int($max_fiyat);
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND fiyat <= " . $max_fiyat_int . " ";
    $search_link .= $bgrs . "max_fiyat=" . $max_fiyat;
}

// Min metrekare için filtre
if ($min_metrekare != '' && strlen($min_metrekare) < 24 && !stristr($min_metrekare, '.')) {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND metrekare >= " . $min_metrekare . " ";
    $search_link .= $bgrs . "min_metrekare=" . $min_metrekare;
}

// Max metrekare için filtre
if ($max_metrekare != '' && strlen($max_metrekare) < 24 && !stristr($max_metrekare, '.')) {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND metrekare <= " . $max_metrekare . " ";
    $search_link .= $bgrs . "max_metrekare=" . $max_metrekare;
}

// Min bina kat sayısı için filtre
if ($min_bina_kat_sayisi != '' && strlen($min_bina_kat_sayisi) < 24 && !stristr($min_bina_kat_sayisi, '.')) {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND bina_kat_sayisi >= " . $min_bina_kat_sayisi . " ";
    $search_link .= $bgrs . "min_bina_kat_sayisi=" . $min_bina_kat_sayisi;
}

// Max bina kat sayısı için filtre
if ($max_bina_kat_sayisi != '' && strlen($max_bina_kat_sayisi) < 24 && !stristr($max_bina_kat_sayisi, '.')) {
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND bina_kat_sayisi <= " . $max_bina_kat_sayisi . " ";
    $search_link .= $bgrs . "max_bina_kat_sayisi=" . $max_bina_kat_sayisi;
}

// İlan tarihi için filtre
if ($ilan_tarih != '') {
    $islem = '';
    if ($ilan_tarih == "bugun") {
        $islem = "tarih LIKE '%" . date("Y-m-d") . "%'";
    } elseif ($ilan_tarih == "son3") {
        $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 3 DAY)";
    } elseif ($ilan_tarih == "son7") {
        $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
    } elseif ($ilan_tarih == "son14") {
        $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 2 WEEK)";
    } elseif ($ilan_tarih == "son21") {
        $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 3 WEEK)";
    } elseif ($ilan_tarih == "son1ay") {
        $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
    } elseif ($ilan_tarih == "son2ay") {
        $islem = "tarih > DATE_SUB(CURDATE(), INTERVAL 2 MONTH)";
    }
    if ($islem != '') {
        $filtre_count += 1;
        $bgrs = ($filtre_count < 2) ? '?' : '&';
        $dahili_query .= "AND " . $islem . " ";
        $search_link .= $bgrs . "ilan_tarih=" . $ilan_tarih;
    }
}

// Sıralama için
if ($order != '') {
    $search_link .= "&order=" . $order;
}

$search_link = str_replace("/?", "?", $search_link);

// Yönlendirme
$fonk->yonlendir($search_link, 1);