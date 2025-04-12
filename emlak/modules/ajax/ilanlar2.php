<?php
// POST kontrolü
if (!$_POST) {
    die();
}

// Filtreleme ve arama için gerekli deðiþkenler tanýmlandý
$filtre = '';
$search_linkx = SITE_URL;
$search_link = SITE_URL;
$filtre_count = 0;
$bgrsyok = 0;

// Kullanýcýdan gelen veriler güvenli hale getirildi
$how = $gvn->harf_rakam($_POST["how"]);
$sicak = $gvn->harf_rakam($_POST["sicak"]);
$vitrin = $gvn->harf_rakam($_POST["vitrin"]);
$onecikan = $gvn->harf_rakam($_POST["onecikan"]);
$resimli = $gvn->harf_rakam($_POST["resimli"]);
$videolu = $gvn->harf_rakam($_POST["videolu"]);
$order = $gvn->harf_rakam($_POST["order"]);
$q = $gvn->html_temizle($_POST["q"]);
$emlak_durum = $gvn->html_temizle($_POST["emlak_durum"]);
$emlak_tipi = $gvn->html_temizle($_POST["emlak_tipi"]);
$il = $gvn->rakam($_POST["il"]);
$ilce = $gvn->rakam($_POST["ilce"]);
$mahalle = $gvn->rakam($_POST["mahalle"]);
$konut_tipi = $gvn->html_temizle($_POST["konut_tipi"]);
$konut_sekli = $gvn->html_temizle($_POST["konut_sekli"]);
$bulundugu_kat = $gvn->html_temizle($_POST["bulundugu_kat"]);
$min_fiyat = $gvn->prakam($_POST["min_fiyat"]);
$max_fiyat = $gvn->prakam($_POST["max_fiyat"]);
$min_metrekare = $gvn->rakam($_POST["min_metrekare"]);
$max_metrekare = $gvn->rakam($_POST["max_metrekare"]);
$min_bina_kat_sayisi = $gvn->rakam($_POST["min_bina_kat_sayisi"]);
$max_bina_kat_sayisi = $gvn->rakam($_POST["max_bina_kat_sayisi"]);
$yapi_durum = $gvn->html_temizle($_POST["yapi_durum"]);
$ilan_tarih = $gvn->html_temizle($_POST["ilan_tarih"]);

// Eðer tüm filtreleme kriterleri boþsa iþlemi durdur
if (
    $fonk->bosluk_kontrol($q) == true &&
    $fonk->bosluk_kontrol($emlak_durum) == true &&
    $fonk->bosluk_kontrol($emlak_tipi) == true &&
    $fonk->bosluk_kontrol($il) == true &&
    $fonk->bosluk_kontrol($ilce) == true &&
    $fonk->bosluk_kontrol($mahalle) == true &&
    $fonk->bosluk_kontrol($konut_tipi) == true &&
    $fonk->bosluk_kontrol($konut_sekli) == true &&
    $fonk->bosluk_kontrol($bulundugu_kat) == true &&
    $fonk->bosluk_kontrol($min_fiyat) == true &&
    $fonk->bosluk_kontrol($max_fiyat) == true &&
    $fonk->bosluk_kontrol($min_metrekare) == true &&
    $fonk->bosluk_kontrol($max_metrekare) == true &&
    $fonk->bosluk_kontrol($min_bina_kat_sayisi) == true &&
    $fonk->bosluk_kontrol($max_bina_kat_sayisi) == true &&
    $fonk->bosluk_kontrol($yapi_durum) == true &&
    $fonk->bosluk_kontrol($ilan_tarih) == true &&
    $fonk->bosluk_kontrol($sicak) == true &&
    $fonk->bosluk_kontrol($vitrin) == true &&
    $fonk->bosluk_kontrol($onecikan) == true &&
    $fonk->bosluk_kontrol($resimli) == true &&
    $fonk->bosluk_kontrol($videolu) == true &&
    $fonk->bosluk_kontrol($how) == true
) {
    die();
}

// Profil URL'si oluþturuluyor
$search_link .= "profil/" . $how . "/";

// Emlak durumu filtresi
if ($emlak_durum != '') {
    $bgrsyok += 1;
    $getemlkdrm = $gvn->permaLink($emlak_durum);
    $search_link .= $getemlkdrm . "/";
}

// Emlak tipi filtresi
if ($emlak_tipi != '') {
    $bgrsyok += 1;
    $getemlktipi = $gvn->PermaLink($emlak_tipi);
    $search_link .= $getemlktipi . "/";
}

// Konut þekli filtresi
if ($konut_sekli != '') {
    $bgrsyok += 1;
    $getkonutskli = $gvn->PermaLink($konut_sekli);
    $search_link .= $getkonutskli . "/";
}

// Konut tipi filtresi
if ($konut_tipi != '') {
    $bgrsyok += 1;
    $getkonuttpi = $gvn->PermaLink($konut_tipi);
    $search_link .= $getkonuttpi . "/";
}

// Ýl filtresi
if ($il != '') {
    $ilkontrol = $db->prepare("SELECT id, il_adi, slug FROM il WHERE id=? ORDER BY id ASC");
    $ilkontrol->execute([$il]);
    if ($ilkontrol->rowCount() > 0) {
        $ilim = $ilkontrol->fetch(PDO::FETCH_OBJ);
        $bgrsyok += 1;
        $search_link .= $ilim->slug . "-";
    }
}

// Ýlçe filtresi
if ($ilce != '' && isset($ilim->id)) {
    $ilcekontrol = $db->prepare("SELECT id, ilce_adi, slug FROM ilce WHERE id=? ORDER BY id ASC");
    $ilcekontrol->execute([$ilce]);
    if ($ilcekontrol->rowCount() > 0) {
        $ilcem = $ilcekontrol->fetch(PDO::FETCH_OBJ);
        $bgrsyok += 1;
        $search_link .= $ilcem->slug . "-";
    }
}

// Mahalle filtresi
if ($mahalle != '' && isset($ilcem->id) && isset($ilim->id)) {
    $mahkontrol = $db->prepare("SELECT id, slug FROM mahalle_koy WHERE id=? ORDER BY id ASC");
    $mahkontrol->execute([$mahalle]);
    if ($mahkontrol->rowCount() > 0) {
        $mahallem = $mahkontrol->fetch(PDO::FETCH_OBJ);
        $bgrsyok += 1;
        $search_link .= $mahallem->slug . "-";
    }
}

// Son URL düzenlemesi
$search_link = ($bgrsyok > 0) ? rtrim($search_link, "-") : $search_link;
$search_link = ($bgrsyok > 0) ? rtrim($search_link, "/") : $search_link;
$search_link = ($bgrsyok > 0) ? $search_link : $search_linkx . "profil/" . $how . "/portfoy";

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

// Kelime veya ilan no ile arama
if ($q != '') {
$varmikontrol	= $db->prepare("SELECT id,url FROM sayfalar WHERE (site_id_555=501 OR (site_id_888=100 AND durum=1 AND il_id=35) OR (site_id_777=501501 AND durum=1) OR (site_id_699=200 AND durum=1 AND il_id=35) OR (site_id_701=501501 AND durum=1) OR (site_id_702=300 AND durum=1)) AND ilan_no=? AND tipi=4 AND durum=1");
    $varmikontrol->execute([$q]);
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

// Min fiyat filtresi
if ($min_fiyat != '' && strlen($min_fiyat) < 24) {
    $min_fiyat_int = $gvn->para_int($min_fiyat);
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND fiyat >= " . $min_fiyat_int . " ";
    $search_link .= $bgrs . "min_fiyat=" . $min_fiyat;
}

// Max fiyat filtresi
if ($max_fiyat != '' && strlen($max_fiyat) < 24) {
    $max_fiyat_int = $gvn->para_int($max_fiyat);
    $filtre_count += 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND fiyat <= " . $max_fiyat_int . " ";
    $search_link .= $bgrs . "max_fiyat=" . $max_fiyat;
}

// Ýlan tarihi için filtre
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

// Order için
if ($order != '') {
    $search_link .= "&order=" . $order;
}

// Son URL düzenlemesi ve yönlendirme
$search_link = str_replace("/?", "?", $search_link);
$fonk->yonlendir($search_link, 1);