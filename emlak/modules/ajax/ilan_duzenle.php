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

$id = $gvn->rakam($_GET["id"]);

// İlan kontrolü
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND id=?");
$kontrol->execute(array($id));
if ($kontrol->rowCount() < 1) {
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

$ilan_aktifet = ($hesap->tipi == 1) ? 1 : $hesap->ilan_aktifet;
$acc = $db->query("SELECT id, kid, ilan_aktifet FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($snc->acid != $hesap->id && $hesap->id != $kid) {
    die();
}
$kurumsal = $db->prepare("SELECT ilan_aktifet FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
$kurumsal->execute(array($kid));
if ($kurumsal->rowCount() > 0) {
    $ilan_aktifet = ($kurumsal->ilan_aktifet == 0) ? $ilan_aktifet : $kurumsal->ilan_aktifet;
}

$yfotolar = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . $snc->id . " AND dil='" . $dil . "' ORDER BY id DESC");
$yfotolarcnt = $yfotolar->rowCount();
$max_limit = 999;
$resim_limit = $max_limit - $yfotolarcnt;

$baslik = $gvn->html_temizle($_POST["baslik"]);
$fiyat = $gvn->prakam($_POST["fiyat"]);
$pbirim = $gvn->harf_rakam($_POST["pbirim"]);
$emlak_durum = $gvn->html_temizle($_POST["emlak_durum"]);
$emlak_tipi = $gvn->html_temizle($_POST["emlak_tipi"]);
$ulke_id = $gvn->zrakam($_POST["ulke_id"]);
$il = $gvn->zrakam($_POST["il"]);
$il_id = $il;
$ilce = $gvn->zrakam($_POST["ilce"]);
$ilce_id = $ilce;
$mahalle = $gvn->zrakam($_POST["mahalle"]);
$mahalle_id = $mahalle;
$semt = $gvn->html_temizle($_POST["semt"]);
$konut_tipi = $gvn->html_temizle($_POST["konut_tipi"]);
$konut_sekli = $gvn->html_temizle($_POST["konut_sekli"]);
$bulundugu_kat = $gvn->html_temizle($_POST["bulundugu_kat"]);
$metrekare = $gvn->zrakam($_POST["metrekare"]);
$brut_metrekare = $gvn->zrakam($_POST["brut_metrekare"]);
$metrekare_fiyat = $gvn->prakam($_POST["metrekare_fiyat"]);
$ada_no = $gvn->html_temizle($_POST["ada_no"]);
$parsel_no = $gvn->html_temizle($_POST["parsel_no"]);
$pafta_no = $gvn->html_temizle($_POST["pafta_no"]);
$kaks_emsal = $gvn->html_temizle($_POST["kaks_emsal"]);
$gabari = $gvn->html_temizle($_POST["gabari"]);
$imar_durum = $gvn->html_temizle($_POST["imar_durum"]);
$tapu_durumu = $gvn->html_temizle($_POST["tapu_durumu"]);
$katk = $gvn->html_temizle($_POST["katk"]);
$krediu = $gvn->html_temizle($_POST["krediu"]);
$takas = $gvn->html_temizle($_POST["takas"]);
$yapi_durum = $gvn->html_temizle($_POST["yapi_durum"]);
$oda_sayisi = $gvn->html_temizle($_POST["oda_sayisi"]);
$bina_yasi = $gvn->html_temizle($_POST["bina_yasi"]);
$bina_kat_sayisi = $gvn->rakam($_POST["bina_kat_sayisi"]);
$isitma = $gvn->html_temizle($_POST["isitma"]);
$banyo_sayisi = $gvn->html_temizle($_POST["banyo_sayisi"]);
$esyali = $gvn->html_temizle($_POST["esyali"]);
$site_ici = $gvn->html_temizle($_POST["site_ici"]);
$kimden = $gvn->html_temizle($_POST["kimden"]);
$yetkis = $gvn->html_temizle($_POST["yetkis"]);
$yetki_bilgisi = $gvn->html_temizle($_POST["yetki_bilgisi"]);
$site_id_888 = $gvn->zrakam($_POST["site_id_888"]);
$site_id_777 = $gvn->zrakam($_POST["site_id_777"]);
$site_id_699 = $gvn->zrakam($_POST["site_id_699"]);
$site_id_700 = $gvn->zrakam($_POST["site_id_700"]);
$site_id_701 = $gvn->zrakam($_POST["site_id_701"]);
$site_id_702 = $gvn->zrakam($_POST["site_id_702"]);
$site_id_661 = $gvn->zrakam($_POST["site_id_661"]);
$site_id_662 = $gvn->zrakam($_POST["site_id_662"]);
$site_id_663 = $gvn->zrakam($_POST["site_id_663"]);
$site_id_664 = $gvn->zrakam($_POST["site_id_664"]);
$site_id_665 = $gvn->zrakam($_POST["site_id_665"]);
$site_id_666 = $gvn->zrakam($_POST["site_id_666"]);
$site_id_667 = $gvn->zrakam($_POST["site_id_667"]);
$site_id_668 = $gvn->zrakam($_POST["site_id_668"]);
$site_id_669 = $gvn->zrakam($_POST["site_id_669"]);
$site_id_335 = $gvn->zrakam($_POST["site_id_335"]);
$site_id_334 = $gvn->zrakam($_POST["site_id_334"]);
$site_id_306 = $gvn->zrakam($_POST["site_id_306"]);
$aidat = $gvn->prakam($_POST["aidat"]);
$kullanim_durum = $gvn->html_temizle($_POST["kullanim_durum"]);
$maps = $gvn->html_temizle($_POST["maps"]);
$icerik = $gvn->filtre($_POST["icerik"]);
$cephe_ozellikler = $_POST["cephe_ozellikler"];
$ic_ozellikler = $_POST["ic_ozellikler"];
$dis_ozellikler = $_POST["dis_ozellikler"];
$altyapi_ozellikler = $_POST["altyapi_ozellikler"];
$konum_ozellikler = $_POST["konum_ozellikler"];
$genel_ozellikler = $_POST["genel_ozellikler"];
$manzara_ozellikler = $_POST["manzara_ozellikler"];
$notu = $_POST["notu"];
$resimler = $_FILES["resimler"];
$resimler_adet = count($resimler);

// Boş alan kontrol ediyorum...
if ($fonk->bosluk_kontrol($baslik) == true || $il == 0) {
    die("<span class='error'>" . dil("TX22") . "</span>");
}

// Fiyatlar için int değişken
$fiyat_int = $gvn->para_int($fiyat);
$aidat_int = $gvn->para_int($aidat);

$fiyat_str = $gvn->para_str($fiyat_int);

$ulke_id = ($ulke_id == 0) ? $db->query("SELECT id FROM ulkeler_501 ORDER BY id DESC LIMIT 0,1")->fetch(PDO::FETCH_OBJ)->id : $ulke_id;

// Ülkeyi kontrol ediyorum....
$ulkekontrol = $db->prepare("SELECT * FROM ulkeler_501 WHERE id=?");
$ulkekontrol->execute(array($ulke_id));
if ($ulkekontrol->rowCount() < 1) {
    die("<span class=''>Geçersiz ülke girdiniz!</span>");
}
$ulke = $ulkekontrol->fetch(PDO::FETCH_OBJ);

// İli kontrol ediyorum....
if ($il != 0) {
    $ilkontrol = $db->prepare("SELECT * FROM il WHERE id=?");
    $ilkontrol->execute(array($il));
    if ($ilkontrol->rowCount() < 1) {
        die("<span class=''>" . dil("TX24") . "</span>");
    }
    $il = $ilkontrol->fetch(PDO::FETCH_OBJ);
}

// İlçeyi kontrol ediyorum....
if ($ilce != 0) {
    $ilcekontrol = $db->prepare("SELECT * FROM ilce WHERE id=?");
    $ilcekontrol->execute(array($ilce));
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

if ($resimler_adet > $resim_limit) {
    die("<span class='error'>" . dil("TX26") . " " . $resim_limit . " " . dil("TX27") . "</span>");
}

// PermaLink Kontrolü
$url = $gvn->PermaLink($baslik) . "-" . $snc->ilan_no;
if ($dayarlar->permalink != 'Evet') {
    $url = $snc->url;
}


// Para Birimi Kontrolü
$pbirimler = explode(",", dil("PARA_BIRIMI"));
if (!in_array($pbirim, $pbirimler)) {
    $pbirim = $pbirimler[0];
}

if ($ilan_aktifet == 0 && $snc->durum != 0) {
    $hesapp = $hesap;
    $adsoyad = $hesapp->adi;
    $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
    $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
    $fonk->bildirim_gonder([$adsoyad, $snc->id, $snc->baslik, date("d.m.Y H:i")], "onay_bekleyen_ilan", $hesapp->email, $hesapp->telefon);
}

$prepare = "UPDATE sayfalar SET baslik=?,fiyat=?,emlak_durum=?,emlak_tipi=?,ulke_id=?,il_id=?,ilce_id=?,mahalle_id=?,konut_tipi=?,konut_sekli=?,bulundugu_kat=?,metrekare=?,brut_metrekare=?,yapi_durum=?,oda_sayisi=?,bina_yasi=?,bina_kat_sayisi=?,isitma=?,banyo_sayisi=?,esyali=?,kullanim_durum=?,site_ici=?,aidat=?,cephe_ozellikler=?,ic_ozellikler=?,dis_ozellikler=?,maps=?,url=?,tarih=?,ilan_aktifet=?,pbirim=?,icerik=?,metrekare_fiyat=?,ada_no=?,parsel_no=?,pafta_no=?,kaks_emsal=?,gabari=?,imar_durum=?,tapu_durumu=?,katk=?,krediu=?,takas=?,kimden=?,yetkis=?,yetki_bilgisi=?,site_id_888=?,site_id_777=?,site_id_699=?,site_id_700=?,site_id_701=?,site_id_702=?,site_id_661=?,site_id_662=?,site_id_663=?,site_id_664=?,site_id_665=?,site_id_666=?,site_id_667=?,site_id_668=?,site_id_669=?,site_id_335=?,site_id_334=?,site_id_306=?,altyapi_ozellikler=?,konum_ozellikler=?,genel_ozellikler=?,manzara_ozellikler=?,notu=?,keywords=?,description=? WHERE site_id_555=501 AND id=?";
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $sql = $db->prepare($prepare);
    $sql->execute([
        $baslik,
        $fiyat_int,
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
        $url,
        $fonk->datetime(),
        $ilan_aktifet,
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
        $keywords,
        $description,
        $snc->id
    ]);
} catch (PDOException $e) {
    die("<span class='error'>" . dil("TX28") . "</span>");
}

$tabs = $_POST["tabs"];
if (!empty($tabs)) {
    foreach ($tabs as $key => $val) {
        $dilop = $db->prepare("SELECT * FROM diller_501 WHERE kisa_adi=?");
        $dilop->execute(array($key));
        if ($dilop->rowCount() > 0 && $fonk->bosluk_kontrol($val['baslik']) == false && ($multi->rowCount() > 0 && $snc->id == $multif->id)) {
            $dilop = $dilop->fetch(PDO::FETCH_OBJ);

            // Current Lang
            $d_ortk1 = array($fonk->get_lang($snc->dil, "TX167"), $fonk->get_lang($snc->dil, "TX168"));
            $d_emlk_drm = explode("<+>", $fonk->get_lang($snc->dil, "EMLK_DRM"));
            $d_emlk_tipi = explode("<+>", $fonk->get_lang($snc->dil, "EMLK_TIPI"));
            $d_knt_sekli = explode("<+>", $fonk->get_lang($snc->dil, "KNT_SEKLI"));
            $d_knt_tipi = explode("<+>", $fonk->get_lang($snc->dil, "KNT_TIPI"));
            $d_knt_tipi2 = explode("<+>", $fonk->get_lang($snc->dil, "KNT_TIPI2"));
            $d_bulnd_kat = explode("<+>", $fonk->get_lang($snc->dil, "BULND_KAT"));
            $d_yapi_drm = explode("<+>", $fonk->get_lang($snc->dil, "YAPI_DURUM"));
            $d_oda_sayis = explode("<+>", $fonk->get_lang($snc->dil, "ODA_SAYISI"));
            $d_isitm = explode("<+>", $fonk->get_lang($snc->dil, "ISITMA"));
            $d_kul_durum = explode("<+>", $fonk->get_lang($snc->dil, "KUL_DURUM"));
            $d_kaks_emsl = explode("<+>", $fonk->get_lang($snc->dil, "KAKS_EMSAL"));
            $d_gabari = explode("<+>", $fonk->get_lang($snc->dil, "GABARI"));
            $d_imr_durum = explode("<+>", $fonk->get_lang($snc->dil, "IMAR_DURUM"));
            $d_tapu_drm = explode("<+>", $fonk->get_lang($snc->dil, "TAPU_DRM"));
            $d_kimdn = explode(",", $fonk->get_lang($snc->dil, "KIMDEN"));
            $d_yetkiso = explode(",", $fonk->get_lang($snc->dil, "TX625"));
            $d_delm1 = explode("<+>", $fonk->get_lang($snc->dil, "CEPHE"));
            $d_delm2 = explode("<+>", $fonk->get_lang($snc->dil, "IC_OZELLIKLER"));
            $d_delm3 = explode("<+>", $fonk->get_lang($snc->dil, "DIS_OZELLIKLER"));
            $d_delm4 = explode("<+>", $fonk->get_lang($snc->dil, "ALTYAPI_OZELLIKLER"));
            $d_delm5 = explode("<+>", $fonk->get_lang($snc->dil, "KONUM_OZELLIKLER"));
            $d_delm6 = explode("<+>", $fonk->get_lang($snc->dil, "GENEL_OZELLIKLER"));
            $d_delm7 = explode("<+>", $fonk->get_lang($snc->dil, "MANZARA_OZELLIKLER"));

            // Get Lang
            $l_ortk1 = array($fonk->get_lang($key, "TX167"), $fonk->get_lang($key, "TX168"));
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

            // Position
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

            // Variable
            $l_baslik = $gvn->html_temizle($val['baslik']);
            $l_icerik = $val['icerik'];
            $l_title = ($val['title'] == '') ? $title : $gvn->html_temizle($val['title']);
            $l_keywords = ($val['keywords'] == '') ? $keywords : $gvn->html_temizle($val['keywords']);
            $l_description = ($val['description'] == '') ? $description : $gvn->html_temizle($val['description']);
            $l_url = $gvn->PermaLink($l_baslik) . "-" . $snc->ilan_no;
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
            $l_imar_durum = $l_imr_durum[$post17];
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
                $l_cephe_ozelliklerl = array();
                foreach ($l_cephe_ozellikler as $elem) {
                    $pos = array_search($elem, $d_delm1);
                    $l_cephe_ozelliklerl[] = $l_delm1[$pos];
                }
                $l_cephe_ozellikler = $l_cephe_ozelliklerl;
            }
			

if (count($l_ic_ozellikler) > 0) {
    $l_ic_ozelliklerl = array();
    foreach ($l_ic_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm2);
        $l_ic_ozelliklerl[] = $l_delm2[$pos];
    }
    $l_ic_ozellikler = $l_ic_ozelliklerl;
}

if (count($l_dis_ozellikler) > 0) {
    $l_dis_ozelliklerl = array();
    foreach ($l_dis_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm3);
        $l_dis_ozelliklerl[] = $l_delm3[$pos];
    }
    $l_dis_ozellikler = $l_dis_ozelliklerl;
}

if (count($l_altyapi_ozellikler) > 0) {
    $l_altyapi_ozelliklerl = array();
    foreach ($l_altyapi_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm4);
        $l_altyapi_ozelliklerl[] = $l_delm4[$pos];
    }
    $l_altyapi_ozellikler = $l_altyapi_ozelliklerl;
}

if (count($l_konum_ozellikler) > 0) {
    $l_konum_ozelliklerl = array();
    foreach ($l_konum_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm5);
        $l_konum_ozelliklerl[] = $l_delm5[$pos];
    }
    $l_konum_ozellikler = $l_konum_ozelliklerl;
}

if (count($l_genel_ozellikler) > 0) {
    $l_genel_ozelliklerl = array();
    foreach ($l_genel_ozellikler as $elem) {
        $pos = array_search($elem, $d_delm6);
        $l_genel_ozelliklerl[] = $l_delm6[$pos];
    }
    $l_genel_ozellikler = $l_genel_ozelliklerl;
}

if (count($l_manzara_ozellikler) > 0) {
    $l_manzara_ozelliklerl = array();
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

$kontrol = $db->prepare("SELECT id, ilan_no FROM sayfalar WHERE site_id_555=501 AND ilan_no=? AND dil=?");
$kontrol->execute(array($snc->ilan_no, $key));

if ($kontrol->rowCount() > 0) { // var ise
    $vari = $kontrol->fetch(PDO::FETCH_OBJ);

    try {
        $sql = $db->prepare($prepare);
        $sql->execute(array(
            $l_baslik,
            $fiyat_int,
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
            $l_url,
            $fonk->datetime(),
            $ilan_aktifet,
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
            $l_notu,
            $l_keywords,
            $l_description,
            $vari->id
        ));
    } catch (PDOException $e) {
        echo $e->getMessage();
        die($fonk->ajax_hata("Transfer is problem " . $snc->dil . " => " . $key . " --> Message: " . $e->getMessage()));
    }
} else { // yok ise
    $prepare = "INSERT INTO sayfalar SET site_id_555=501,site_id_450=000,site_id_444=000,site_id_333=335,site_id_222=200,site_id_111=100,baslik=?,fiyat=?,ilan_no=?,emlak_durum=?,emlak_tipi=?,ulke_id=?,il_id=?,ilce_id=?,mahalle_id=?,konut_tipi=?,konut_sekli=?,bulundugu_kat=?,metrekare=?,brut_metrekare=?,yapi_durum=?,oda_sayisi=?,bina_yasi=?,bina_kat_sayisi=?,isitma=?,banyo_sayisi=?,esyali=?,kullanim_durum=?,site_ici=?,aidat=?,cephe_ozellikler=?,ic_ozellikler=?,dis_ozellikler=?,maps=?,resim=?,acid=?,tipi=4,dil=?,url=?,tarih=?,tarih_guncel=?,pbirim=?,icerik=?,metrekare_fiyat=?,ada_no=?,parsel_no=?,pafta_no=?,kaks_emsal=?,gabari=?,imar_durum=?,tapu_durumu=?,katk=?,krediu=?,takas=?,kimden=?,yetkis=?,yetki_bilgisi=?,site_id_888=?,site_id_777=?,site_id_699=?,site_id_700=?,site_id_701=?,site_id_702=?,site_id_661=?,site_id_662=?,site_id_663=?,site_id_664=?,site_id_665=?,site_id_666=?,site_id_667=?,site_id_668=?,site_id_669=?,site_id_335=?,site_id_334=?,site_id_306=?,altyapi_ozellikler=?,konum_ozellikler=?,genel_ozellikler=?,manzara_ozellikler=?,notu=?,durum=?,keywords=?,description=?,btarih=?,gosterim=0";
    try {
        $sql = $db->prepare($prepare);
        $sql->execute([
            $l_baslik,
            $fiyat_int,
            $snc->ilan_no,
            $l_emlak_durum,
            $l_emlak_tipi,
            $snc->ulke_id,
            $snc->il_id,
            $snc->ilce_id,
            $snc->mahalle_id,
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
            $snc->resim,
            $snc->acid,
            $key,
            $l_url,
            $snc->tarih,
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
            $l_notu,
            $durum,
            $l_keywords,
            $l_description,
            $btarih
        ]);
    } catch (PDOException $e) {
        echo $e->getMessage();
        die($fonk->ajax_hata("Transfer is problem " . $snc->dil . " => " . $key . " --> Message: " . $e->getMessage()));
    }
}

// Tarihleri al
$otarih = date("d.m.Y", strtotime($snc->tarih));
$gtarih = date("d.m.Y", strtotime($fonk->datetime()));

// Yönlendirme
if ($hesap->id == $kid) {
    $fonk->yonlendir("uye-paneli?rd=danisman_duzenle&id=" . $snc->acid . "&goto=ilanlari", 1500);
} else {
    $fonk->yonlendir("aktif-ilanlar", 1500);
}
?>
<script type="text/javascript">
    $("#IlanOlusturForm").slideUp(700, function() {
        $("#TamamDiv").slideDown(800);
    });
    $('html, body').animate({ scrollTop: 250 }, 500);
</script>