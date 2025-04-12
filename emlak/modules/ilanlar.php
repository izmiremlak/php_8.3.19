<?php
// Hata raporlama seviyesini ayarla ve hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt'); // Hataları bu dosyaya yaz

if (!defined("THEME_DIR")) {
    die();
}

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Filtreleme parametresini temizle
$filtre = $gvn->html_temizle($_GET["filtre"]);

if ($filtre != '') {
    $search_link = SITE_URL;
    $filtresc = (stristr($filtre, "/"));
    $filtreb = array_diff(explode("/", $filtre), ["", " "]);
    $filtreby = $filtreb;

    $emlkdrm1 = explode("<+>", dil("EMLK_DRM"));
    $emlktp1 = explode("<+>", dil("EMLK_TIPI"));
    $konutskli1 = explode("<+>", dil("KNT_SEKLI"));
    $knttipi1 = array_merge(explode("<+>", dil("KNT_TIPI")), explode("<+>", dil("KNT_TIPI2")));

    $emlkdrm2 = $gvn->PermaLinkArray($emlkdrm1);
    $emlktp2 = $gvn->PermaLinkArray($emlktp1);
    $konutskli2 = $gvn->PermaLinkArray($konutskli1);
    $knttipi2 = $gvn->PermaLinkArray($knttipi1);

    $tanimlar = [
        'emlak_durum' => $emlkdrm1,
        'emlak_tipi' => $emlktp1,
        'konut_sekli' => $konutskli1,
        'konut_tipi' => $knttipi1
    ];
    $kontroller = [
        'emlak_durum' => $emlkdrm2,
        'emlak_tipi' => $emlktp2,
        'konut_sekli' => $konutskli2,
        'konut_tipi' => $knttipi2
    ];

    foreach ($filtreb as $k => $ney) {
        foreach ($kontroller as $varadi => $dizi) {
            if (false !== $key = array_search($ney, $dizi)) {
                if (!isset(${$varadi})) {
                    ${$varadi} = $tanimlar[$varadi][$key];
                    unset($filtreb[$k]);
                }
            }
        }
    }

    $cfiltreb = count($filtreb);
    if ($cfiltreb > 0 && $cfiltreb <= 1) { // Son kalan 1 veya 2 parametreyi alıyoruz...
        $il = 0;
        $ilce = 0;
        $mahalle = 0;
        foreach ($filtreb as $fi) {
            $kontrol = $db->prepare("SELECT id FROM il WHERE slug = ?");
            $kontrol->execute([$fi]);
            if ($kontrol->rowCount() > 0) {
                $res = $kontrol->fetch(PDO::FETCH_OBJ);
                $il = $res->id;
            } else {
                $kontrol = $db->prepare("SELECT id, il_id FROM ilce WHERE slug2 = ?");
                $kontrol->execute([$fi]);
                if ($kontrol->rowCount() > 0) {
                    $res = $kontrol->fetch(PDO::FETCH_OBJ);
                    $il = $res->il_id;
                    $ilce = $res->id;
                } else {
                    $kontrol = $db->prepare("SELECT id, il_id, ilce_id FROM mahalle_koy WHERE slug2 = ?");
                    $kontrol->execute([$fi]);
                    if ($kontrol->rowCount() > 0) {
                        $res = $kontrol->fetch(PDO::FETCH_OBJ);
                        $il = $res->il_id;
                        $ilce = $res->ilce_id;
                        $mahalle = $res->id;
                    }
                }
            }
        }
    }

} else {
    $search_link = "ilanlar";
    $emlak_durum = $gvn->html_temizle($_GET["emlak_durum"]);
    $emlak_tipi = $gvn->html_temizle($_GET["emlak_tipi"]);
    $konut_sekli = $gvn->html_temizle($_GET["konut_sekli"]);
    $konut_tipi = $gvn->html_temizle($_GET["konut_tipi"]);
    $il = $gvn->rakam($_GET["il"]);
    $ilce = $gvn->rakam($_GET["ilce"]);
    $mahalle = $gvn->rakam($_GET["mahalle"]);
}

$filtre_count = 0;
$aradigi_sey = [];
$git = $gvn->rakam($_GET["git"]);
$sicak = $gvn->harf_rakam($_GET["sicak"]);
$vitrin = $gvn->harf_rakam($_GET["vitrin"]);
$onecikan = $gvn->harf_rakam($_GET["onecikan"]);
$resimli = $gvn->harf_rakam($_GET["resimli"]);
$videolu = $gvn->harf_rakam($_GET["videolu"]);
$q = $gvn->html_temizle($_GET["q"]);
$bulundugu_kat = $gvn->html_temizle($_GET["bulundugu_kat"]);
$kaks_emsal = $gvn->html_temizle($_GET["kaks_emsal"]);
$gabari = $gvn->html_temizle($_GET["gabari"]);
$imar_durum = $gvn->html_temizle($_GET["imar_durum"]);
$tapu_durumu = $gvn->html_temizle($_GET["tapu_durumu"]);
$katk = $gvn->html_temizle($_GET["katk"]);
$krediu = $gvn->html_temizle($_GET["krediu"]);
$takas = $gvn->html_temizle($_GET["takas"]);
$min_fiyat = $gvn->prakam($_GET["min_fiyat"]);
$max_fiyat = $gvn->prakam($_GET["max_fiyat"]);
$min_metrekare = $gvn->rakam($_GET["min_metrekare"]);
$max_metrekare = $gvn->rakam($_GET["max_metrekare"]);
$min_bina_kat_sayisi = $gvn->rakam($_GET["min_bina_kat_sayisi"]);
$max_bina_kat_sayisi = $gvn->rakam($_GET["max_bina_kat_sayisi"]);
$yapi_durum = $gvn->html_temizle($_GET["yapi_durum"]);
$ilan_tarih = $gvn->html_temizle($_GET["ilan_tarih"]);

// Gelen filtreleme isteklerinin hepsi boşsa indexe yönlendiriyoruz...
if (
    $fonk->bosluk_kontrol($q) === true &&
    $fonk->bosluk_kontrol($emlak_durum) === true &&
    $fonk->bosluk_kontrol($emlak_tipi) === true &&
    $fonk->bosluk_kontrol($il) === true &&
    $fonk->bosluk_kontrol($ilce) === true &&
    $fonk->bosluk_kontrol($mahalle) === true &&
    $fonk->bosluk_kontrol($konut_tipi) === true &&
    $fonk->bosluk_kontrol($konut_sekli) === true &&
    $fonk->bosluk_kontrol($bulundugu_kat) === true &&
    $fonk->bosluk_kontrol($kaks_emsal) === true &&
    $fonk->bosluk_kontrol($gabari) === true &&
    $fonk->bosluk_kontrol($imar_durum) === true &&
    $fonk->bosluk_kontrol($tapu_durumu) === true &&
    $fonk->bosluk_kontrol($katk) === true &&
    $fonk->bosluk_kontrol($krediu) === true &&
    $fonk->bosluk_kontrol($takas) === true &&
    $fonk->bosluk_kontrol($min_fiyat) === true &&
    $fonk->bosluk_kontrol($max_fiyat) === true &&
    $fonk->bosluk_kontrol($min_metrekare) === true &&
    $fonk->bosluk_kontrol($max_metrekare) === true &&
    $fonk->bosluk_kontrol($min_bina_kat_sayisi) === true &&
    $fonk->bosluk_kontrol($max_bina_kat_sayisi) === true &&
    $fonk->bosluk_kontrol($yapi_durum) === true &&
    $fonk->bosluk_kontrol($ilan_tarih) === true &&
    $fonk->bosluk_kontrol($sicak) === true &&
    $fonk->bosluk_kontrol($vitrin) === true &&
    $fonk->bosluk_kontrol($onecikan) === true &&
    $fonk->bosluk_kontrol($resimli) === true &&
    $fonk->bosluk_kontrol($videolu) === true
) {
    header("Location: index.html");
    die();
}

$dahili_query = "";
$execute = [];

// Emlak Durumu için filtre...
if ($emlak_durum != '') {
    $aradigi_sey[] = $emlak_durum;
    $dahili_query .= "AND t1.emlak_durum = ? ";
    $execute[] = $emlak_durum;
    $search_link .= $gvn->PermaLink($emlak_durum) . "/";
}

// Emlak Tipi için filtre...
if ($emlak_tipi != '') {
    $aradigi_sey[] = $emlak_tipi;
    $dahili_query .= "AND t1.emlak_tipi = ? ";
    $execute[] = $emlak_tipi;
    $ekslsh = (substr($search_link, -1) == '/') ? '' : '/';
    $search_link .= $ekslsh . $gvn->PermaLink($emlak_tipi);
}

// Konut Şekli için filtre...
if ($konut_sekli != '') {
    $aradigi_sey[] = $konut_sekli;
    $dahili_query .= "AND t1.konut_sekli = ? ";
    $execute[] = $konut_sekli;
    $ekslsh = (substr($search_link, -1) == '/') ? '' : '/';
    $search_link .= $ekslsh . $gvn->PermaLink($konut_sekli);
}

// Konut Tipi için filtre...
if ($konut_tipi != '') {
    $aradigi_sey[] = $konut_tipi;
    $dahili_query .= "AND t1.konut_tipi = ? ";
    $execute[] = $konut_tipi;
    $ekslsh = (substr($search_link, -1) == '/') ? '' : '/';
    $search_link .= $ekslsh . $gvn->PermaLink($konut_tipi);
}

// İl için filtre...
if ($il != '') {
    $ilkontrol = $db->prepare("SELECT id, il_adi, slug FROM il WHERE id = ? ORDER BY id ASC");
    $ilkontrol->execute([$il]);
    if ($ilkontrol->rowCount() > 0) {
        $ilim = $ilkontrol->fetch(PDO::FETCH_OBJ);
        $aradigi_sey[] = $ilim->il_adi;
        $dahili_query .= "AND t1.il_id = ? ";
        $execute[] = $il;
        $ekslsh = (substr($search_link, -1) == '/') ? '' : '/';
        $search_link .= $ekslsh . $ilim->slug;
    }
}

// İlçe için filtre...
if ($ilce != '') {
    $ilcekontrol = $db->prepare("SELECT id, ilce_adi, slug FROM ilce WHERE id = ? ORDER BY id ASC");
    $ilcekontrol->execute([$ilce]);
    if ($ilcekontrol->rowCount() > 0) {
        $ilcem = $ilcekontrol->fetch(PDO::FETCH_OBJ);
        $aradigi_sey[] = $ilcem->ilce_adi;
        $dahili_query .= "AND t1.ilce_id = ? ";
        $execute[] = $ilce;
        $ekslsh = (substr($search_link, -1) == '-') ? '' : '-';
        $search_link .= $ekslsh . $ilcem->slug;
    }
}

<?php
// Hata raporlama seviyesini ayarla ve hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt'); // Hataları bu dosyaya yaz

// Mahalle için filtre...
if ($ilce != '' && $mahalle != '') {
    $mahkontrol = $db->prepare("SELECT * FROM mahalle_koy WHERE id=? ORDER BY id ASC");
    $mahkontrol->execute([$mahalle]);
    if ($mahkontrol->rowCount() > 0) {
        $mahallem = $mahkontrol->fetch(PDO::FETCH_OBJ);

        $aradigi_sey[] = $mahallem->mahalle_adi;

        $dahili_query .= "AND t1.mahalle_id=? ";
        $execute[] = $mahalle;
        $ekslsh = (substr($search_link, -1) == '-') ? '' : '-';
        $search_link .= $ekslsh . $mahallem->slug;
    }
}

$search_link = rtrim($search_link, "-");
$search_link = rtrim($search_link, "/");

// Sıcak fırsatlar için filtre
if ($sicak == "true") {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX316");
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND EXISTS (SELECT btarih FROM dopingler_501 WHERE ilan_id=t1.id AND durum=1 AND did=1 AND btarih>NOW()) ";
    $search_link .= $bgrs . "sicak=true";
}

// Vitrin için filtre
if ($vitrin == "true") {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX478");
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND EXISTS (SELECT btarih FROM dopingler_501 WHERE ilan_id=t1.id AND durum=1 AND did=2 AND btarih>NOW())";
    $search_link .= $bgrs . "vitrin=true";
}

// Öne Çıkan için filtre
if ($onecikan == "true") {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX479");
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND EXISTS (SELECT btarih FROM dopingler_501 WHERE ilan_id=t1.id AND durum=1 AND did=3 AND btarih>NOW()) ";
    $search_link .= $bgrs . "onecikan=true";
}

// Resimli ilanlar için filtre
if ($resimli == "true") {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX613");
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND (SELECT COUNT(id) FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=t1.id)>0 ";
    $search_link .= $bgrs . "resimli=true";
}

// Videolu ilanlar için filtre
if ($videolu == "true") {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX614");
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.video != '' ";
    $search_link .= $bgrs . "videolu=true";
}

// Kelime veya İlan No ile arama için filtre
if ($q != '' && strlen($q) < 255) {
    $filtre_count += 1;
    $aradigi_sey[] = $q;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND (t1.baslik LIKE ? OR t1.ilan_no LIKE ?) ";
    $execute[] = "%" . $q . "%";
    $execute[] = "%" . $q . "%";
    $search_link .= $bgrs . "q=" . $q;
}

// Bulunduğu Kat için filtre...
if ($bulundugu_kat != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX59") . " " . $bulundugu_kat;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.bulundugu_kat=? ";
    $execute[] = $bulundugu_kat;
    $search_link .= $bgrs . "bulundugu_kat=" . $bulundugu_kat;
}

// Kaks emsal için filtre...
if ($kaks_emsal != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX331") . ": " . $kaks_emsal;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.kaks_emsal=? ";
    $execute[] = $kaks_emsal;
    $search_link .= $bgrs . "kaks_emsal=" . $kaks_emsal;
}

// Gabari için filtre...
if ($gabari != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX332") . ": " . $gabari;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.gabari=? ";
    $execute[] = $gabari;
    $search_link .= $bgrs . "gabari=" . $gabari;
}

// İmar Durumu için filtre...
if ($imar_durum != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX682") . ": " . $imar_durum;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.imar_durum=? ";
    $execute[] = $imar_durum;
    $search_link .= $bgrs . "imar_durum=" . $imar_durum;
}

// Tapu Durumu için filtre...
if ($tapu_durumu != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX333") . ": " . $tapu_durumu;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.tapu_durumu=? ";
    $execute[] = $tapu_durumu;
    $search_link .= $bgrs . "tapu_durumu=" . $tapu_durumu;
}

// Kat Karşılığı için filtre...
if ($katk != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX334") . ": " . $katk;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.katk=? ";
    $execute[] = $katk;
    $search_link .= $bgrs . "katk=" . $katk;
}

// Kredi Uygunluk için filtre...
if ($krediu != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX335") . ": " . $krediu;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.krediu=? ";
    $execute[] = $krediu;
    $search_link .= $bgrs . "krediu=" . $krediu;
}

// Takas için filtre...
if ($takas != '') {
    $filtre_count += 1;
    $aradigi_sey[] = dil("TX336") . ": " . $takas;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $dahili_query .= "AND t1.takas=? ";
    $execute[] = $takas;
    $search_link .= $bgrs . "takas=" . $takas;
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
        $dahili_query .= "AND t1." . $islem . " ";
        $search_link .= $bgrs . "ilan_tarih=" . $ilan_tarih;
    }
}

// Order by için işlemler...
$orderi = $gvn->html_temizle($_REQUEST["order"]);
$search_linkx = $search_link;
if ($fonk->bosluk_kontrol($orderi) === true) {
    $dahili_order = "ORDER BY CASE WHEN t2_id IS NULL THEN 2 ELSE 1 END, t2_id ASC, t1.id DESC";
} else {
    $orderivr = 1;
    $bgrs = ($filtre_count < 2) ? '?' : '&';
    $search_link .= $bgrs . "order=" . $orderi;
    if ($orderi == 'fiyat_asc') {
        $dahili_order = "ORDER BY CAST(t1.fiyat AS DECIMAL(10,2)) ASC";
    } elseif ($orderi == 'fiyat_desc') {
        $dahili_order = "ORDER BY CAST(t1.fiyat AS DECIMAL(10,2)) DESC";
    } else {
        $dahili_order = "ORDER BY t2.id ASC";
    }
}

$baslik = dil("TX648");

// Özel Seo Ayarları
$keywords = $emlak_durum;
$xkeywords = ($konut_tipi != '') ? " " . $konut_tipi : '';
$xkeywords .= ($xkeywords == '' && $konut_sekli != '') ? " " . $konut_sekli : '';
$xkeywords .= ($xkeywords == '' && $emlak_tipi != '') ? " " . $emlak_tipi : '';
$keywords .= $xkeywords;
$keywords1 = ($ilcem->ilce_adi != '') ? $keywords . " " . $ilcem->ilce_adi : '';
$keywords2 = ($ilcem->ilce_adi != '') ? "," . $keywords . " " . $ilim->il_adi : $keywords . " " . $ilim->il_adi;
$keywords3 = ($mahallem->mahalle_adi != '') ? "," . $keywords . " " . $mahallem->mahalle_adi : '';
$keywords = ($keywords1 . $keywords2 . $keywords3 . $keywords4);
$description = $baslik;
$description .= ($keywords != '') ? " | " . $keywords : '';
$ne_ariyor = implode(" + ", $aradigi_sey);
?>
<!DOCTYPE html>
<html>
<head>


<!-- Meta Tags -->
<title><?= htmlspecialchars($ne_ariyor, ENT_QUOTES, 'UTF-8') ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($keywords != '' ? $keywords : $dayarlar->keywords, ENT_QUOTES, 'UTF-8') ?>" />
<meta name="description" content="<?= htmlspecialchars($description != '' ? $description : $dayarlar->description, ENT_QUOTES, 'UTF-8') ?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<base href="<?= SITE_URL ?>" />

<?php include THEME_DIR . "inc/head.php"; ?>

</head>
<body>

<?php include THEME_DIR . "inc/header.php"; ?>


<div class="headerbg" <?= $gayarlar->bayiler_resim != '' ? 'style="background-image: url(uploads/' . $gayarlar->bayiler_resim . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="sayfayolu">
                <a href="index.html"><?= dil("TX136"); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php if ($emlak_durum != '') { ?>
                    <a href="<?= $gvn->PermaLink($emlak_durum); ?>"><?= htmlspecialchars($emlak_durum, ENT_QUOTES, 'UTF-8') ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php } ?>

                <?php if ($emlak_tipi != '') { ?>
                    <a href="<?= $gvn->PermaLink($emlak_tipi); ?>"><?= htmlspecialchars($emlak_tipi, ENT_QUOTES, 'UTF-8') ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php } ?>

                <?php if ($konut_sekli != '') { ?>
                    <a href="<?= $gvn->PermaLink($konut_sekli); ?>"><?= htmlspecialchars($konut_sekli, ENT_QUOTES, 'UTF-8') ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php } ?>

                <?php if ($konut_tipi != '') { ?>
                    <a href="<?= $gvn->PermaLink($konut_tipi); ?>"><?= htmlspecialchars($konut_tipi, ENT_QUOTES, 'UTF-8') ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php } ?>

                <?php if ($ilim->il_adi != '') { ?>
                    <a href="<?= htmlspecialchars($ilim->slug, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($ilim->il_adi, ENT_QUOTES, 'UTF-8') ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>

                    <?php if ($ilcem->ilce_adi != '') { ?>
                        <a href="<?= htmlspecialchars($ilim->slug, ENT_QUOTES, 'UTF-8') ?>-<?= htmlspecialchars($ilcem->slug, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($ilcem->ilce_adi, ENT_QUOTES, 'UTF-8') ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>

                        <?php if ($mahallem->mahalle_adi != '') { ?>
                            <a href="<?= htmlspecialchars($ilim->slug, ENT_QUOTES, 'UTF-8') ?>-<?= htmlspecialchars($ilcem->slug, ENT_QUOTES, 'UTF-8') ?>-<?= htmlspecialchars($mahallem->slug, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($mahallem->mahalle_adi, ENT_QUOTES, 'UTF-8') ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                        <?php } ?>

                    <?php } ?>

                <?php } ?>

                <?php if ($sicak == "true") { ?>
                    <a href="ilanlar?sicak=true"><?= dil("TX316"); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php } ?>

                <?php if ($vitrin == "true") { ?>
                    <a href="ilanlar?vitrin=true"><?= dil("TX478"); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php } ?>

                <?php if ($onecikan == "true") { ?>
                    <a href="ilanlar?onecikan=true"><?= dil("TX479"); ?></a> <i class="fa fa-caret-right" aria-hidden="true"></i>
                <?php } ?>

                <span><?= dil("TX170"); ?></span>

            </div>
        </div>

    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">


<div class="content" <?= $gayarlar->hizmetler_sidebar == 0 ? 'id="bigcontent"' : ''; ?>>

<?php
try {
    $qry = $pagent->sql_query("SELECT t1.ilan_no, t1.id, t1.url, t1.fiyat, t1.tarih, t1.il_id, t1.ilce_id, t1.emlak_durum, t1.resim, t1.baslik, t1.pbirim, t1.metrekare, t2.id AS t2_id 
                               FROM sayfalar AS t1 
                               LEFT JOIN dopingler_501 AS t2 ON t2.ilan_id = t1.id AND t2.did = 4 AND t2.durum = 1 AND t2.btarih > NOW() 
                               WHERE (t1.btarih > NOW() OR t2.btarih > NOW() OR EXISTS (SELECT btarih FROM dopingler_501 WHERE ilan_id = t1.id AND durum = 1 AND btarih > NOW())) 
                               AND t1.durum = 1 AND t1.ekleme = 1 AND ((t1.site_id_555=501 AND t1.durum=1 AND t1.site_id_699=0 AND t1.site_id_700=0 AND t1.site_id_701=0 AND t1.site_id_702=0) OR (t1.site_id_888=100 AND t1.durum=1 AND t1.il_id=35) OR (t1.site_id_777=501501 AND t1.durum=1) OR (t1.site_id_702=300 AND t1.durum=1)) AND t1.tipi = 4 " . $dahili_query . " " . $dahili_order, $git, 12, $execute);

    $query = $qry['sql'];
    $query = $db->prepare($query);
    $query->execute($execute);
} catch (PDOException $e) {
    echo "Veritabanı hatası: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
$adet = $qry['toplam'];
$orbgrs = ($filtre_count < 1) ? '?' : '&';
?>

<div class="altbaslik">
    <h4 id="sicakfirsatlar" <?= $fonk->strlentr($ne_ariyor) > 65 ? ' class="titlekisalt"' : ''; ?>><strong>"<?= htmlspecialchars($fonk->kisalt2($ne_ariyor, 0, 76), ENT_QUOTES, 'UTF-8') ?>"</strong></h4>

    <div class="tablolistx">
        <a class="tooltip-top" data-tooltip="<?= dil("TX646"); ?>" href="javascript:Listele('table_list');"><i class="fa fa-list" aria-hidden="true"></i></a>
        <a class="tooltip-top" data-tooltip="<?= dil("TX647"); ?>" href="javascript:Listele('grid_list');"><i class="fa fa-th-large" aria-hidden="true"></i></a>
    </div>

    <select name="order" class="gelismissirala" onchange="location = this.options[this.selectedIndex].value;">
        <option value="<?= $search_linkx; ?>"><?= dil("TX171"); ?></option>
        <option value="<?= $search_linkx . $orbgrs . "order=fiyat_asc"; ?>" <?= $orderi == 'fiyat_asc' ? "selected" : ''; ?>><?= dil("TX172"); ?></option>
        <option value="<?= $search_linkx . $orbgrs . "order=fiyat_desc"; ?>" <?= $orderi == 'fiyat_desc' ? "selected" : ''; ?>><?= dil("TX173"); ?></option>
        <option value="<?= $search_linkx . $orbgrs . "order=order_last"; ?>" <?= $orderi == 'order_last' ? "selected" : ''; ?>><?= dil("TX174"); ?></option>
    </select>
</div>

<div class="clear"></div>

<?php
if ($gayarlar->reklamlar == 1) { // Eğer reklamlar aktif ise...
    $detect = (!isset($detect)) ? new Mobile_Detect : $detect;
    $rtipi = 3;
    $reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1)");
    $rcount = $reklamlar->rowCount();
    $order = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
    $reklam = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1) " . $order . " LIMIT 0,1")->fetch(PDO::FETCH_OBJ);
    if ($rcount > 0) {
        ?><!-- 728 x 90 Reklam Alanı -->
        <div class="ad728home">
            <?= ($detect->isMobile() || $detect->isTablet()) ? $reklam->mobil_kodu : $reklam->kodu; ?>
        </div>
        <!-- 728 x 90 Reklam Alanı END-->
    <?php }
} // Eğer reklamlar aktif ise...
?>

<div class="ilanlistesi">

    <?php
    if ($adet > 0) {
        $needs = [];
        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
            $t2_id = $row->t2_id;
            $id = $row->id;
            $row_lang = $db->query("SELECT t1.id, t1.url, t1.fiyat, t1.tarih, t1.il_id, t1.ilce_id, t1.emlak_durum, t1.emlak_tipi, t1.resim, t1.baslik, t1.pbirim, t1.metrekare 
                                    FROM sayfalar AS t1 
                                    WHERE (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.ilan_no = {$row->ilan_no} AND t1.dil = '{$dil}'");
            if ($row_lang->rowCount() > 0) {
                $row = $row_lang->fetch(PDO::FETCH_OBJ);
                $row->t2_id = $t2_id;
                $row->id = $id;
            }

            $ilink = ($dayarlar->permalink == 'Evet') ? $row->url . '.html' : 'index.php?p=sayfa&id=' . $row->id;
            if ($row->fiyat != 0 || $row->fiyat != '') {
                $fiyat_int = $gvn->para_int($row->fiyat);
                $fiyat = $gvn->para_str($fiyat_int);
            }
            $tarihi = date("d-n-Y", strtotime($row->tarih));
            $tarihi = explode("-", $tarihi);
            $ili = $db->query("SELECT il_adi FROM il WHERE id={$row->il_id}")->fetch(PDO::FETCH_OBJ)->il_adi;
            $ilcesi = $db->query("SELECT ilce_adi FROM ilce WHERE id={$row->ilce_id}")->fetch(PDO::FETCH_OBJ)->ilce_adi;
            $adres = $ili;
            $adres .= ($ilcesi != '') ? " / " . $ilcesi : '';
            $emlkdrm = ($row->emlak_durum == $emstlk) ? 'id="satilik"' : '';
            $emlkdrm .= ($row->emlak_durum == $emkrlk) ? 'id="kiralik"' : '';
            $isdoping5 = $db->query("SELECT id FROM dopingler_501 WHERE ilan_id={$row->id} AND durum=1 AND did=5 AND btarih>NOW()")->fetch(PDO::FETCH_OBJ);
            $rebold = ($isdoping5->id == '') ? 0 : 1;

            $needs[] = [
                'rebold' => $rebold,
                'ilink' => $ilink,
                'resim' => "uploads/thumb/" . $row->resim,
                'baslik' => $row->baslik,
                'fiyat' => ($row->fiyat != '' || $row->fiyat != 0) ? $fiyat . " " . $row->pbirim : dil("TX186"),
                'metrekare' => $row->metrekare,
                'tarih' => $tarihi[0] . ' ' . $aylar[$tarihi[1]] . ' ' . $tarihi[2],
                'adres' => $adres,
                'emlkdrm' => $emlkdrm,
                'emlak_durum' => $row->emlak_durum,
            ];
        }
        ?>

        <div id="table_list" style="display:none">
            <table width="100%" border="0">
                <tr>
                    <td colspan="2" bgcolor="#EFEFEF"><strong><?= dil("TX177"); ?></strong></td>
                    <td width="15%" id="mobtd" align="center" bgcolor="#EFEFEF"><strong><?= dil("TX178"); ?></strong></td>
                    <td id="mobtd" align="center" bgcolor="#EFEFEF"><strong><?= dil("TX179"); ?></strong></td>
                    <td width="15%" id="mobtd" align="center" bgcolor="#EFEFEF"><strong><?= dil("TX180"); ?></strong></td>
                    <td width="15%" id="mobtd" align="center" bgcolor="#EFEFEF"><strong><?= dil("TX181"); ?></strong></td>
                </tr>

                <?php
                foreach ($needs as $row) {
                    $ilink = $row["ilink"];
                    $resim = $row["resim"];
                    $baslik = $row["baslik"];
                    $fiyat = $row["fiyat"];
                    $metrekare = $row["metrekare"];
                    $tarih = $row["tarih"];
                    $adres = $row["adres"];
                    $rebold = $row["rebold"];
                    ?>
                    <tr<?= ($rebold == 1) ? ' class="doping5"' : ''; ?>>
                        <td width="100"><a href="<?= $ilink; ?>"><img src="https://www.turkiyeemlaksitesi.com.tr/<?= $resim; ?>" width="120" height="90" /></a></td>
                        <td>
                            <a href="<?= $ilink; ?>"><?= htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8') ?></a><div class="clearmob"></div>
                            <span class="mobilanbilgi"><strong><?= dil("TX184"); ?></strong> <?= htmlspecialchars($tarih, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="mobilanbilgi"><strong><?= dil("TX185"); ?></strong> <?= htmlspecialchars($adres, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="mobilanbilgi"><strong><?= dil("TX183"); ?></strong> <?= htmlspecialchars($metrekare, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="mobilanbilgi"><strong><?= dil("TX182"); ?></strong> <strong style="color:red;"><?= htmlspecialchars($fiyat, ENT_QUOTES, 'UTF-8'); ?><strong></span>
                        </td>
                        <td width="15%" id="mobtd" align="center"><?= htmlspecialchars($fiyat, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td id="mobtd" align="center"><?= htmlspecialchars($metrekare, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td width="15%" id="mobtd" align="center"><?= htmlspecialchars($tarih, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td width="15%" id="mobtd" align="center"><?= htmlspecialchars($adres, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <div id="grid_list" style="display:none">
            <?php
            foreach ($needs as $row) {
                $ilink = $row["ilink"];
                $resim = $row["resim"];
                $baslik = $fonk->kisalt2($row["baslik"], 0, 50);
                $fiyat = $row["fiyat"];
                $metrekare = $row["metrekare"];
                $tarih = $row["tarih"];
                $adres = $row["adres"];
                $emlkdrm = $row["emlkdrm"];
                $emlkdrm2 = $row["emlak_durum"];
                $rebold = $row["rebold"];
                ?>
                <a href="<?= $ilink; ?>">
                    <div class="<?= ($rebold == 1) ? 'doping5_grid ' : ''; ?>kareilan fadein hidden visible animated fadeIn">
                        <span class="ilandurum" <?= $emlkdrm; ?>><?= htmlspecialchars($emlkdrm2, ENT_QUOTES, 'UTF-8'); ?></span>
                        <img title="<?= htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8'); ?>" src="https://www.turkiyeemlaksitesi.com.tr/<?= $resim; ?>" width="234" height="201">
                        <div class="fiyatlokasyon">
                            <h3><?= htmlspecialchars($fiyat, ENT_QUOTES, 'UTF-8'); ?></h3>
                            <h4><?= htmlspecialchars($adres, ENT_QUOTES, 'UTF-8'); ?></h4>
                        </div>
                        <div class="kareilanbaslik">
                            <h3><?= htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8'); ?></h3>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
		
<?php } else { ?>
<h4 style="text-align:center;margin-top:60px;"><?= dil("TX187"); ?></h4>
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

<?php if ($gayarlar->hizmetler_sidebar == 1) { ?>
    <div class="sidebar">
        <?php include THEME_DIR . "inc/advanced_search.php"; ?>
    </div>
<?php } ?>

<div class="clear"></div>

<?php include THEME_DIR . "inc/ilanvertanitim.php"; ?>
</div>

<?php include THEME_DIR . "inc/footer.php"; ?>
<script type="text/javascript">
    $(document).ready(function () {
        var list = $.cookie("list");
        if (list == '' || list == undefined) {
            list = "table_list";
        }

        Listele(list);
    });

    function Listele(turu) {
        if (turu == "table_list") {
            $("#grid_list").fadeOut(5, function () {
                $("#table_list").fadeIn(5);
                $(".tablolistx a:eq(0)").attr("id", "tablolistx-aktif");
                $(".tablolistx a:eq(1)").removeAttr("id");
            });
        } else if (turu == "grid_list") {
            $("#table_list").fadeOut(5, function () {
                $("#grid_list").fadeIn(5);
                $(".tablolistx a:eq(1)").attr("id", "tablolistx-aktif");
                $(".tablolistx a:eq(0)").removeAttr("id");
            });
        }
        $.cookie("list", turu);
    }
</script>
