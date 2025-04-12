<?php
// Hata yönetimi için ayarları yapılandır
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error_log.txt'); // Hata log dosyasının yolu
error_reporting(E_ALL); // Tüm hataları raporla

// Hataları sitede göster
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='color: red;'><b>Hata:</b> [$errno] $errstr - $errfile:$errline</div>";
    return true;
}
set_error_handler("customErrorHandler");

// Kullanıcı giriş kontrolü
if (!$_POST || $hesap->id == '') {
    die();
}

$aylik_ilan_limit = $hesap->aylik_ilan_limit;
$ilan_resim_limit = $hesap->ilan_resim_limit;
$ilan_yayin_sure = $hesap->ilan_yayin_sure;
$ilan_yayin_periyod = $hesap->ilan_yayin_periyod;

// Paket için gerekli kontroller
if ($hesap->kid == 0 && $hesap->turu == 0) { // Bireysel
    $acids = $hesap->id;
    $pkacid = $acids;
    $ilan_aktifet = $hesap->ilan_aktifet;
} elseif ($hesap->kid == 0 && $hesap->turu == 1) { // Kurumsal
    $dids = $db->query("SELECT kid, id, GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=" . $hesap->id)->fetch(PDO::FETCH_OBJ);
    $danismanlar = $dids->danismanlar;
    $acids = ($danismanlar == '') ? $hesap->id : $hesap->id . ',' . $danismanlar;
    $pkacid = $hesap->id;
    $ilan_aktifet = $hesap->ilan_aktifet;
} elseif ($hesap->kid != 0 && $hesap->turu == 2) { // Danışman
    $kurumsal = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $hesap->kid);
    if ($kurumsal->rowCount() > 0) {
        $kurumsal = $kurumsal->fetch(PDO::FETCH_OBJ);
        $dids = $db->query("SELECT kid, id, GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=" . $kurumsal->id)->fetch(PDO::FETCH_OBJ);
        $danismanlar = $dids->danismanlar;
        $acids = ($danismanlar == '') ? $kurumsal->id : $kurumsal->id . ',' . $danismanlar;
        $pkacid = $kurumsal->id;
        $ilan_aktifet = ($kurumsal->ilan_aktifet == 0) ? $hesap->ilan_aktifet : $kurumsal->ilan_aktifet;
        $aylik_ilan_limit += $kurumsal->aylik_ilan_limit;
        $ilan_resim_limit += $kurumsal->ilan_resim_limit;
        $ilan_yayin_sure += $kurumsal->ilan_yayin_sure;
        $ilan_yayin_periyod = $kurumsal->ilan_yayin_periyod;
    } else {
        $acids = $hesap->id;
        $pkacid = $acids;
        $ilan_aktifet = $hesap->ilan_aktifet;
    }
} else {
    $acids = $hesap->id;
    $pkacid = $acids;
    $ilan_aktifet = $hesap->ilan_aktifet;
}

// Paket bilgilerini kontrol et
$paketi = $db->query("SELECT * FROM upaketler_501 WHERE acid=" . $pkacid . " AND durum=1 AND btarih>NOW()");
if ($paketi->rowCount() > 0) {
    $paketi = $paketi->fetch(PDO::FETCH_OBJ);
    $aylik_ilan_limit = ($paketi->aylik_ilan_limit == 0) ? 99999 : $paketi->aylik_ilan_limit;
    $ilan_resim_limit = ($paketi->ilan_resim_limit == 0) ? 99999 : $paketi->ilan_resim_limit;
    $ilan_yayin_sure = ($paketi->ilan_yayin_sure == 0) ? 120 : $paketi->ilan_yayin_sure;
    $ilan_yayin_periyod = ($paketi->ilan_yayin_sure == 0) ? "yillik" : $paketi->ilan_yayin_periyod;
    $paketegore = "AND pid=" . $paketi->id . " ";
}

$buay = date("Y-m");
$aylik_ilan_limit -= $db->query("SELECT tarih, id FROM sayfalar WHERE site_id_555=501 AND ekleme=1 AND tipi=4 " . $paketegore . "AND acid IN(" . $acids . ") AND tarih LIKE '%" . $buay . "%' ")->rowCount();

if ($aylik_ilan_limit < 1) {
    die('<span class="error">' . dil("TX611") . '</span>');
}

$expiry = "+" . $ilan_yayin_sure;
$expiry .= ($ilan_yayin_periyod == "gunluk") ? ' day' : '';
$expiry .= ($ilan_yayin_periyod == "aylik") ? ' month' : '';
$expiry .= ($ilan_yayin_periyod == "yillik") ? ' year' : '';
$btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";
$pid = ($paketi->id != '') ? $paketi->id : 0;
$durum = ($hesap->tipi == 1) ? 1 : $ilan_aktifet;
$ilan_no = intval($_POST["ilan_no"]);
$baslik = htmlspecialchars($_POST["baslik"]);
$fiyat = $gvn->prakam($_POST["fiyat"]);
$pbirim = $gvn->harf_rakam($_POST["pbirim"]);
$emlak_durum = htmlspecialchars($_POST["emlak_durum"]);
$emlak_tipi = htmlspecialchars($_POST["emlak_tipi"]);
$ulke_id = intval($_POST["ulke_id"]);
$il = intval($_POST["il"]);
$il_id = $il;
$ilce = intval($_POST["ilce"]);
$ilce_id = $ilce;
$mahalle = intval($_POST["mahalle"]);
$mahalle_id = $mahalle;
$semt = htmlspecialchars($_POST["semt"]);
$konut_tipi = htmlspecialchars($_POST["konut_tipi"]);
$konut_sekli = htmlspecialchars($_POST["konut_sekli"]);
$bulundugu_kat = htmlspecialchars($_POST["bulundugu_kat"]);
$metrekare = intval($_POST["metrekare"]);
$brut_metrekare = intval($_POST["brut_metrekare"]);
$metrekare_fiyat = $gvn->prakam($_POST["metrekare_fiyat"]);
$ada_no = htmlspecialchars($_POST["ada_no"]);
$parsel_no = htmlspecialchars($_POST["parsel_no"]);
$pafta_no = htmlspecialchars($_POST["pafta_no"]);
$kaks_emsal = htmlspecialchars($_POST["kaks_emsal"]);
$gabari = htmlspecialchars($_POST["gabari"]);
$imar_durum = htmlspecialchars($_POST["imar_durum"]);
$tapu_durumu = htmlspecialchars($_POST["tapu_durumu"]);
$katk = htmlspecialchars($_POST["katk"]);
$krediu = htmlspecialchars($_POST["krediu"]);
$takas = htmlspecialchars($_POST["takas"]);
$yapi_durum = htmlspecialchars($_POST["yapi_durum"]);
$oda_sayisi = htmlspecialchars($_POST["oda_sayisi"]);
$bina_yasi = htmlspecialchars($_POST["bina_yasi"]);
$bina_kat_sayisi = intval($_POST["bina_kat_sayisi"]);
$isitma = htmlspecialchars($_POST["isitma"]);
$banyo_sayisi = htmlspecialchars($_POST["banyo_sayisi"]);
$esyali = htmlspecialchars($_POST["esyali"]);
$site_ici = htmlspecialchars($_POST["site_ici"]);
$kimden = htmlspecialchars($_POST["kimden"]);
$yetkis = htmlspecialchars($_POST["yetkis"]);
$yetki_bilgisi = htmlspecialchars($_POST["yetki_bilgisi"]);
$site_id_888 = intval($_POST["site_id_888"]);
$site_id_777 = intval($_POST["site_id_777"]);
$site_id_699 = intval($_POST["site_id_699"]);
$site_id_700 = intval($_POST["site_id_700"]);
$site_id_701 = intval($_POST["site_id_701"]);
$site_id_702 = intval($_POST["site_id_702"]);
$site_id_661 = intval($_POST["site_id_661"]);
$site_id_662 = intval($_POST["site_id_662"]);
$site_id_663 = intval($_POST["site_id_663"]);
$site_id_664 = intval($_POST["site_id_664"]);
$site_id_665 = intval($_POST["site_id_665"]);
$site_id_666 = intval($_POST["site_id_666"]);
$site_id_667 = intval($_POST["site_id_667"]);
$site_id_668 = intval($_POST["site_id_668"]);
$site_id_669 = intval($_POST["site_id_669"]);
$site_id_335 = intval($_POST["site_id_335"]);
$site_id_334 = intval($_POST["site_id_334"]);
$site_id_306 = intval($_POST["site_id_306"]);
$aidat = $gvn->prakam($_POST["aidat"]);
$kullanim_durum = htmlspecialchars($_POST["kullanim_durum"]);
$maps = htmlspecialchars($_POST["maps"]);
$icerik = $gvn->filtre($_POST["icerik"]);
$cephe_ozellikler = $_POST["cephe_ozellikler"];
$ic_ozellikler = $_POST["ic_ozellikler"];
$dis_ozellikler = $_POST["dis_ozellikler"];
$altyapi_ozellikler = $_POST["altyapi_ozellikler"];
$konum_ozellikler = $_POST["konum_ozellikler"];
$genel_ozellikler = $_POST["genel_ozellikler"];
$manzara_ozellikler = $_POST["manzara_ozellikler"];
$notu = $_POST["notu"];

// Boş alan kontrol ediyorum...
if ($fonk->bosluk_kontrol($ilan_no) || $fonk->bosluk_kontrol($baslik) || $il == 0) {
    die("<span class='error'>" . dil("TX22") . "</span>");
}

// Ilan benzerlik kontrolü
$noKontrol = $db->prepare("SELECT ilan_no FROM sayfalar WHERE site_id_555=501 AND ilan_no=?");
$noKontrol->execute([$ilan_no]);
if ($noKontrol->rowCount() > 0 || $ilan_no < 1 || $ilan_no == 0 || strlen((string)$ilan_no) < 5) {
    $ilan_no = rand(10000000, 99999999);
}

// Fiyatlar için int değişken
$fiyat_int = $gvn->para_int($fiyat);
$aidat_int = $gvn->para_int($aidat);

$fiyat_str = $gvn->para_str($fiyat_int);

$ulke_id = ($ulke_id == 0) ? $db->query("SELECT id FROM ulkeler_501 ORDER BY id DESC LIMIT 0,1")->fetch(PDO::FETCH_OBJ)->id : $ulke_id;

// Ülkeyi kontrol ediyorum....
$ulkekontrol = $db->prepare("SELECT * FROM ulkeler_501 WHERE id=?");
$ulkekontrol->execute([$ulke_id]);
if ($ulkekontrol->rowCount() < 1) {
    die("<span class=''>Geçersiz ülke girdiniz!</span>");
}
$ulke = $ulkekontrol->fetch(PDO::FETCH_OBJ);

// İli kontrol ediyorum....
if ($il != 0) {
    $ilkontrol = $db->prepare("SELECT * FROM il WHERE id=?");
    $ilkontrol->execute([$il]);
    if ($ilkontrol->rowCount() < 1) {
        die("<span class=''>" . dil("TX24") . "</span>");
    }
    $il = $ilkontrol->fetch(PDO::FETCH_OBJ);
}

// İlçeyi kontrol ediyorum....
if ($ilce != 0) {
    $ilcekontrol = $db->prepare("SELECT * FROM ilce WHERE id=?");
    $ilcekontrol->execute([$ilce]);
    if ($ilcekontrol->rowCount() < 1) {
        die("<span class=''>" . dil("TX25") . "</span>");
    }
    $ilce = $ilcekontrol->fetch(PDO::FETCH_OBJ);
}

// Mahalleyi kontrol ediyorum....
if ($mahalle != 0) {
    $mahakontrol = $db->prepare("SELECT * FROM mahalle_koy WHERE id=?");
    $mahakontrol->execute([$mahalle]);
    if ($mahakontrol->rowCount() < 1) {
        die("<span class=''>" . dil("TX25") . "</span>");
    }
    $mahalle = $mahakontrol->fetch(PDO::FETCH_OBJ);
}


// Özellikleri toparlıyorum
// Cephe Özellikler
if (count($cephe_ozellikler) > 0) {
    $cephe_ozellikler = implode("<+>", $cephe_ozellikler);
}
// İç Özellikler
if (count($ic_ozellikler) > 0) {
    $ic_ozellikler = implode("<+>", $ic_ozellikler);
}
// Dış Özellikler
if (count($dis_ozellikler) > 0) {
    $dis_ozellikler = implode("<+>", $dis_ozellikler);
}

if (count($altyapi_ozellikler) > 0) {
    $altyapi_ozellikler = implode("<+>", $altyapi_ozellikler);
}

if (count($konum_ozellikler) > 0) {
    $konum_ozellikler = implode("<+>", $konum_ozellikler);
}

if (count($genel_ozellikler) > 0) {
    $genel_ozellikler = implode("<+>", $genel_ozellikler);
}

if (count($manzara_ozellikler) > 0) {
    $manzara_ozellikler = implode("<+>", $manzara_ozellikler);
}

// Özel Seo Ayarları
$keywords = $emlak_durum;
$xkeywords = ($konut_tipi != '') ? " " . $konut_tipi : '';
$xkeywords .= ($xkeywords == '' && $konut_sekli != '') ? " " . $konut_sekli : '';
$xkeywords .= ($xkeywords == '' && $emlak_tipi != '') ? " " . $emlak_tipi : '';
$keywords .= $xkeywords;
$keywords1 = ($ilce->ilce_adi != '') ? $keywords . " " . $ilce->ilce_adi : '';
$keywords2 = ($ilce->ilce_adi != '') ? " , " . $keywords . " " . $il->il_adi : $keywords . " " . $il->il_adi;
$keywords3 = ($mahalle->mahalle_adi != '') ? " , " . $keywords . " " . $mahalle->mahalle_adi : '';
$keywords4 = ($fiyat != '' && $fiyat != 0) ? " , " . $keywords . " " . $fiyat_str . " " . $pbirim : '';
$keywords = ($keywords1 . $keywords2 . $keywords3 . $keywords4);
$description = $baslik;
$description .= ($keywords != '') ? " | " . $keywords : '';

// Eğer ilana resim yüklenmemişse kapak resimi default adını veriyoruz...
$resim = 'default_ilan_resim.jpg';

// PermaLink Kontrolü
$url = $gvn->PermaLink($baslik) . "-" . $ilan_no;
if ($dayarlar->permalink != 'Evet') {
    $url = '';
}

// Para Birimi Kontrolü
$pbirimler = explode(",", dil("PARA_BIRIMI"));
if (!in_array($pbirim, $pbirimler)) {
    $pbirim = $pbirimler[0];
}

$prepare = "INSERT INTO sayfalar SET site_id_555=501,site_id_450=000,site_id_444=000,site_id_333=335,site_id_222=200,site_id_111=100,baslik=?,fiyat=?,ilan_no=?,emlak_durum=?,emlak_tipi=?,ulke_id=?,il_id=?,ilce_id=?,mahalle_id=?,konut_tipi=?,konut_sekli=?,bulundugu_kat=?,metrekare=?,brut_metrekare=?,yapi_durum=?,oda_sayisi=?,bina_yasi=?,bina_kat_sayisi=?,isitma=?,banyo_sayisi=?,esyali=?,kullanim_durum=?,site_ici=?,aidat=?,cephe_ozellikler=?,ic_ozellikler=?,dis_ozellikler=?,maps=?,resim=?,acid=?,tipi=4,dil=?,url=?,tarih=?,tarih_guncel=?,pbirim=?,icerik=?,metrekare_fiyat=?,ada_no=?,parsel_no=?,pafta_no=?,kaks_emsal=?,gabari=?,imar_durum=?,tapu_durumu=?,katk=?,krediu=?,takas=?,kimden=?,yetkis=?,yetki_bilgisi=?,site_id_888=?,site_id_777=?,site_id_699=?,site_id_700=?,site_id_701=?,site_id_702=?,site_id_661=?,site_id_662=?,site_id_663=?,site_id_664=?,site_id_665=?,site_id_666=?,site_id_667=?,site_id_668=?,site_id_669=?,site_id_335=?,site_id_334=?,site_id_306=?,altyapi_ozellikler=?,konum_ozellikler=?,genel_ozellikler=?,manzara_ozellikler=?,notu=?,durum=?,keywords=?,description=?,btarih=?,pid=?";

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $sql = $db->prepare($prepare);
    $sql->execute([
        $baslik,
        $fiyat_int,
        $ilan_no,
        $emlak_durum,
        $emlak_tipi,
        $ulke->id,
        $il_id,
        $ilce_id,
        $mahalle_id,
        $konut_tipi,
        $konut_sekli,
        $bulundugu_kat,
        $metrekare,
        $brut_metrekare,
        $yapi_durum,
        $oda_sayisi,
        $bina_yasi,
        $bina_kat_sayisi,
        $isitma,
        $banyo_sayisi,
        $esyali,
        $kullanim_durum,
        $site_ici,
        $aidat_int,
        $cephe_ozellikler,
        $ic_ozellikler,
        $dis_ozellikler,
        $maps,
        $resim,
        $hesap->id,
        4,
        $dil,
        $url,
        $fonk->datetime(),
        $fonk->datetime(),
        $pbirim,
        $icerik,
        $metrekare_fiyat,
        $ada_no,
        $parsel_no,
        $pafta_no,
        $kaks_emsal,
        $gabari,
        $imar_durum,
        $tapu_durumu,
        $katk,
        $krediu,
        $takas,
        $kimden,
        $yetkis,
        $yetki_bilgisi,
        $site_id_888,
        $site_id_777,
        $site_id_699,
        $site_id_700,
        $site_id_701,
        $site_id_702,
        $site_id_661,
        $site_id_662,
        $site_id_663,
        $site_id_664,
        $site_id_665,
        $site_id_666,
        $site_id_667,
        $site_id_668,
        $site_id_669,
        $site_id_335,
        $site_id_334,
        $site_id_306,
        $altyapi_ozellikler,
        $konum_ozellikler,
        $genel_ozellikler,
        $manzara_ozellikler,
        $notu,
        $durum,
        $keywords,
        $description,
        $btarih,
        $pid,
    ]);
    $ilan_id = $db->lastInsertId();
} catch (PDOException $e) {
    die("<span class='error'>" . dil("TX28") . "</span>");
}

if ($ilan_id == '' || $ilan_id == 0) {
    die("<span class='error'>" . dil("TX28") . "</span>");
}

// Çok dilli destek için POST verilerini işleme
$tabs = $_POST["tabs"];
if (!empty($tabs)) {
    foreach ($tabs as $key => $val) {
        $dilop = $db->prepare("SELECT * FROM diller_501 WHERE kisa_adi=?");
        $dilop->execute([$key]);
        if ($dilop->rowCount() > 0 && !$fonk->bosluk_kontrol($val['baslik'])) {
            $dilop = $dilop->fetch(PDO::FETCH_OBJ);

            // Mevcut dildeki verileri al
            $d_ortk1 = [dil("TX167"), dil("TX168")];
            $d_emlk_drm = explode("<+>", dil("EMLK_DRM"));
            $d_emlk_tipi = explode("<+>", dil("EMLK_TIPI"));
            $d_knt_sekli = explode("<+>", dil("KNT_SEKLI"));
            $d_knt_tipi = explode("<+>", dil("KNT_TIPI"));
            $d_knt_tipi2 = explode("<+>", dil("KNT_TIPI2"));
            $d_bulnd_kat = explode("<+>", dil("BULND_KAT"));
            $d_yapi_drm = explode("<+>", dil("YAPI_DURUM"));
            $d_oda_sayis = explode("<+>", dil("ODA_SAYISI"));
            $d_isitm = explode("<+>", dil("ISITMA"));
            $d_kul_durum = explode("<+>", dil("KUL_DURUM"));
            $d_kaks_emsl = explode("<+>", dil("KAKS_EMSAL"));
            $d_gabari = explode("<+>", dil("GABARI"));
            $d_imr_durum = explode("<+>", dil("IMAR_DURUM"));
            $d_tapu_drm = explode("<+>", dil("TAPU_DRM"));
            $d_kimdn = explode(",", dil("KIMDEN"));
            $d_yetkiso = explode(",", dil("TX625"));
            $d_delm1 = explode("<+>", dil("CEPHE"));
            $d_delm2 = explode("<+>", dil("IC_OZELLIKLER"));
            $d_delm3 = explode("<+>", dil("DIS_OZELLIKLER"));
            $d_delm4 = explode("<+>", dil("ALTYAPI_OZELLIKLER"));
            $d_delm5 = explode("<+>", dil("KONUM_OZELLIKLER"));
            $d_delm6 = explode("<+>", dil("GENEL_OZELLIKLER"));
            $d_delm7 = explode("<+>", dil("MANZARA_OZELLIKLER"));

            // Yeni dildeki verileri al
            $l_ortk1 = [$fonk->get_lang($key, "TX167"), $fonk->get_lang($key, "TX168")];
            $l_emlk_drm = explode("<+>", $fonk->get_lang($key, "EMLK_DRM"));
            $l_emlk_tipi = explode("<+>", $fonk->get_lang($key, "EMLK_TIPI"));
            $l_knt_sekli = explode("<+>", $fonk->get_lang($key, "KNT_SEKLI"));
            $l_knt_tipi = explode("<+>", $fonk->get_lang($key, "KNT_TIPI"));
            $l_knt_tipi2 = explode("<+>", $fonk->get_lang($key, "KNT_TIPI2"));
            $l_bulnd_kat = explode("<+>", $fonk->get_lang($key, "BULND_KAT"));
            $l_yapi_drm = explode("<+>", $fonk->get_lang($key, "YAPI_DURUM"));
            $l_oda_sayis = explode("<+>", $fonk->get_lang($key, "ODA_SAYISI"));
            $l_isitm = explode("<+>", $fonk->get_lang($key, "ISITMA"));
            $l_kul_durum = explode("<+>", $fonk->get_lang($key, "KUL_DURUM"));
            $l_kaks_emsl = explode("<+>", $fonk->get_lang($key, "KAKS_EMSAL"));
            $l_gabari = explode("<+>", $fonk->get_lang($key, "GABARI"));
            $l_imar_drm = explode("<+>", $fonk->get_lang($key, "IMAR_DURUM"));
            $l_tapu_drm = explode("<+>", $fonk->get_lang($key, "TAPU_DRM"));
            $l_kimdn = explode(",", $fonk->get_lang($key, "KIMDEN"));
            $l_yetkiso = explode(",", $fonk->get_lang($key, "TX625"));
            $l_delm1 = explode("<+>", $fonk->get_lang($key, "CEPHE"));
            $l_delm2 = explode("<+>", $fonk->get_lang($key, "IC_OZELLIKLER"));
            $l_delm3 = explode("<+>", $fonk->get_lang($key, "DIS_OZELLIKLER"));
            $l_delm4 = explode("<+>", $fonk->get_lang($key, "ALTYAPI_OZELLIKLER"));
            $l_delm5 = explode("<+>", $fonk->get_lang($key, "KONUM_OZELLIKLER"));
            $l_delm6 = explode("<+>", $fonk->get_lang($key, "GENEL_OZELLIKLER"));
            $l_delm7 = explode("<+>", $fonk->get_lang($key, "MANZARA_OZELLIKLER"));


// Mevcut dildeki verileri yeni dile göre eşleştir
$pos0 = array_search($emlak_durum, $d_emlk_drm);
$pos1 = array_search($emlak_tipi, $d_emlk_tipi);
$pos2 = array_search($konut_sekli, $d_knt_sekli);
$pos3 = array_search($konut_tipi, $d_knt_tipi);
if ($pos3 == false) {
    $pos3_1 = array_search($konut_tipi, $d_knt_tipi2);
}
$pos4 = array_search($bulundugu_kat, $d_bulnd_kat);
$pos5 = array_search($yapi_durum, $d_yapi_drm);
$pos6 = array_search($oda_sayisi, $d_oda_sayis);
$pos7 = array_search($isitma, $d_isitm);
$post15 = array_search($kaks_emsal, $d_kaks_emsl);
$post16 = array_search($gabari, $d_gabari);
$post17 = array_search($imar_durum, $d_imr_durum);
$pos8 = array_search($tapu_durumu, $d_tapu_drm);
$pos9 = array_search($katk, $d_ortk1);
$pos10 = array_search($krediu, $d_ortk1);
$pos11 = array_search($takas, $d_ortk1);
$pos12 = array_search($kimden, $d_kimdn);
$pos13 = array_search($yetkis, $d_yetkiso);
$pos14 = array_search($kullanim_durum, $d_kul_durum);

// Yeni dildeki değişkenler
$l_baslik = $gvn->html_temizle($val['baslik']);
$l_icerik = $val['icerik'];
$l_title = ($val['title'] == '') ? $title : $gvn->html_temizle($val['title']);
$l_keywords = ($val['keywords'] == '') ? $keywords : $gvn->html_temizle($val['keywords']);
$l_description = ($val['description'] == '') ? $description : $gvn->html_temizle($val['description']);
$l_url = $gvn->PermaLink($l_baslik) . "-" . $ilan_no;
$l_emlak_durum = $l_emlk_drm[$pos0];
$l_emlak_tipi = $l_emlk_tipi[$pos1];
$l_konut_sekli = $l_knt_sekli[$pos2];
$l_konut_tipi = ($pos3 == false) ? $l_knt_tipi2[$pos3_1] : $l_knt_tipi[$pos3];
$l_bulundugu_kat = $l_bulnd_kat[$pos4];
$l_yapi_durum = $l_yapi_drm[$pos5];
$l_oda_sayisi = $l_oda_sayis[$pos6];
$l_isitma = $l_isitm[$pos7];
$l_kullanim_durum = $l_kul_durum[$pos14];
$l_kaks_emsal = $l_kaks_emsl[$post15];
$l_gabari = $l_gabari[$post16];
$l_imar_durum = $l_imar_drm[$post17];
$l_tapu_durumu = $l_tapu_drm[$pos8];
$l_katk = $l_ortk1[$pos9];
$l_krediu = $l_ortk1[$pos10];
$l_takas = $l_ortk1[$pos11];
$l_kimden = $l_kimdn[$pos12];
$l_yetkis = $l_yetkiso[$pos13];
$l_cephe_ozellikler = $_POST["cephe_ozellikler"];
$l_ic_ozellikler = $_POST["ic_ozellikler"];
$l_dis_ozellikler = $_POST["dis_ozellikler"];
$l_altyapi_ozellikler = $_POST["altyapi_ozellikler"];
$l_konum_ozellikler = $_POST["konum_ozellikler"];
$l_genel_ozellikler = $_POST["genel_ozellikler"];
$l_manzara_ozellikler = $_POST["manzara_ozellikler"];

if (count($l_cephe_ozellikler) > 0) {
    $l_cephe_ozelliklerl = [];
    foreach ($l_cephe_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm1);
        $l_cephe_ozelliklerl[] = $l_delm1[$pos];
    }
    $l_cephe_ozellikler = $l_cephe_ozelliklerl;
}

if (count($l_ic_ozellikler) > 0) {
    $l_ic_ozelliklerl = [];
    foreach ($l_ic_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm2);
        $l_ic_ozelliklerl[] = $l_delm2[$pos];
    }
    $l_ic_ozellikler = $l_ic_ozelliklerl;
}

if (count($l_dis_ozellikler) > 0) {
    $l_dis_ozelliklerl = [];
    foreach ($l_dis_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm3);
        $l_dis_ozelliklerl[] = $l_delm3[$pos];
    }
    $l_dis_ozellikler = $l_dis_ozelliklerl;
}

if (count($l_altyapi_ozellikler) > 0) {
    $l_altyapi_ozelliklerl = [];
    foreach ($l_altyapi_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm4);
        $l_altyapi_ozelliklerl[] = $l_delm4[$pos];
    }
    $l_altyapi_ozellikler = $l_altyapi_ozelliklerl;
}

if (count($l_konum_ozellikler) > 0) {
    $l_konum_ozelliklerl = [];
    foreach ($l_konum_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm5);
        $l_konum_ozelliklerl[] = $l_delm5[$pos];
    }
    $l_konum_ozellikler = $l_konum_ozelliklerl;
}

if (count($l_genel_ozellikler) > 0) {
    $l_genel_ozelliklerl = [];
    foreach ($l_genel_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm6);
        $l_genel_ozelliklerl[] = $l_delm6[$pos];
    }
    $l_genel_ozellikler = $l_genel_ozelliklerl;
}

if (count($l_manzara_ozellikler) > 0) {
    $l_manzara_ozelliklerl = [];
    foreach ($l_manzara_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm7);
        $l_manzara_ozelliklerl[] = $l_delm7[$pos];
    }
    $l_manzara_ozellikler = $l_manzara_ozelliklerl;
}

$l_cephe_ozellikler = (empty($l_cephe_ozellikler)) ? '' : implode("<+>", $l_cephe_ozellikler);
$l_ic_ozellikler = (empty($l_ic_ozellikler)) ? '' : implode("<+>", $l_ic_ozellikler);
$l_dis_ozellikler = (empty($l_dis_ozellikler)) ? '' : implode("<+>", $l_dis_ozellikler);
$l_altyapi_ozellikler = (empty($l_altyapi_ozellikler)) ? '' : implode("<+>", $l_altyapi_ozellikler);
$l_konum_ozellikler = (empty($l_konum_ozellikler)) ? '' : implode("<+>", $l_konum_ozellikler);
$l_genel_ozellikler = (empty($l_genel_ozellikler)) ? '' : implode("<+>", $l_genel_ozellikler);
$l_manzara_ozellikler = (empty($l_manzara_ozellikler)) ? '' : implode("<+>", $l_manzara_ozellikler);

try {
    $sql = $db->prepare($prepare);
    $sql->execute([
        $l_baslik,
        $fiyat_int,
        $ilan_no,
        $l_emlak_durum,
        $l_emlak_tipi,
        $ulke->id,
        $il_id,
        $ilce_id,
        $mahalle_id,
        $l_konut_tipi,
        $l_konut_sekli,
        $l_bulundugu_kat,
        $metrekare,
        $brut_metrekare,
        $l_yapi_durum,
        $l_oda_sayisi,
        $bina_yasi,
        $bina_kat_sayisi,
        $l_isitma,
        $banyo_sayisi,
        $esyali,
        $l_kullanim_durum,
        $site_ici,
        $aidat_int,
        $l_cephe_ozellikler,
        $l_ic_ozellikler,
        $l_dis_ozellikler,
        $maps,
        $resim,
        $hesap->id,
        4,
        $key,
        $l_url,
        $fonk->datetime(),
        $fonk->datetime(),
        $pbirim,
        $l_icerik,
        $metrekare_fiyat,
        $ada_no,
        $parsel_no,
        $pafta_no,
        $l_kaks_emsal,
        $l_gabari,
        $l_imar_durum,
        $l_tapu_durumu,
        $l_katk,
        $l_krediu,
        $l_takas,
        $l_kimden,
        $l_yetkis,
        $yetki_bilgisi,
        $site_id_888,
        $site_id_777,
        $site_id_699,
        $site_id_700,
        $site_id_701,
        $site_id_702,
        $site_id_661,
        $site_id_662,
        $site_id_663,
        $site_id_664,
        $site_id_665,
        $site_id_666,
        $site_id_667,
        $site_id_668,
        $site_id_669,
        $site_id_335,
        $site_id_334,
        $site_id_306,
        $l_altyapi_ozellikler,
        $l_konum_ozellikler,
        $l_genel_ozellikler,
        $l_manzara_ozellikler,
        $notu,
        $durum,
        $l_keywords,
        $l_description,
        $btarih,
        $pid,
    ]);
} catch (PDOException $e) {
    echo $e->getMessage();
    die($fonk->ajax_hata("Transfer is problem " . $dil . " => " . $key . " --> Message: " . $e->getMessage()));
}
}

$fonk->yonlendir("ilan-olustur?id=" . $ilan_id, 1);

/*?><script type="text/javascript">
$("#IlanOlusturForm").slideUp(700,function(){
$("#TamamDiv").slideDown(800);
});
$('html, body').animate({scrollTop: 250}, 500);
</script><? */