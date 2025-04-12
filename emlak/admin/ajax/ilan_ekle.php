<?php
// POST isteği olup olmadığını ve kullanıcının kimlik doğrulamasını kontrol et
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($hesap->id)) {
    die();
}

// POST verilerini güvenli bir şekilde al ve temizle
$ilan_no             = $gvn->zrakam($_POST["ilan_no"]);
$accid               = $gvn->zrakam($_POST["accid"]);
$baslik              = $gvn->html_temizle($_POST["baslik"]);
$title               = $gvn->html_temizle($_POST["title"]);
$keywords            = $gvn->html_temizle($_POST["keywords"]);
$description         = $gvn->html_temizle($_POST["description"]);
$fiyat               = $gvn->prakam($_POST["fiyat"]);
$pbirim              = $gvn->harf_rakam($_POST["pbirim"]);
$emlak_durum         = $gvn->html_temizle($_POST["emlak_durum"]);
$emlak_tipi          = $gvn->html_temizle($_POST["emlak_tipi"]);
$ulke_id             = $gvn->zrakam($_POST["ulke_id"]);
$il                  = $gvn->zrakam($_POST["il"]);
$il_id               = $il;
$ilce                = $gvn->zrakam($_POST["ilce"]);
$ilce_id             = $ilce;
$mahalle             = $gvn->zrakam($_POST["mahalle"]);
$mahalle_id          = $mahalle;
$konut_tipi          = $gvn->html_temizle($_POST["konut_tipi"]);
$konut_sekli         = $gvn->html_temizle($_POST["konut_sekli"]);
$bulundugu_kat       = $gvn->html_temizle($_POST["bulundugu_kat"]);
$metrekare           = $gvn->zrakam($_POST["metrekare"]);
$brut_metrekare      = $gvn->zrakam($_POST["brut_metrekare"]);
$yapi_durum          = $gvn->html_temizle($_POST["yapi_durum"]);
$oda_sayisi          = $gvn->html_temizle($_POST["oda_sayisi"]);
$bina_yasi           = $gvn->html_temizle($_POST["bina_yasi"]);
$bina_kat_sayisi     = $gvn->zrakam($_POST["bina_kat_sayisi"]);
$isitma              = $gvn->html_temizle($_POST["isitma"]);
$banyo_sayisi        = $gvn->html_temizle($_POST["banyo_sayisi"]);
$esyali              = $gvn->rakam($_POST["esyali"]);
$site_ici            = $gvn->rakam($_POST["site_ici"]);
$kimden              = $gvn->html_temizle($_POST["kimden"]);
$yetkis              = $gvn->html_temizle($_POST["yetkis"]);
$yetki_bilgisi       = $gvn->html_temizle($_POST["yetki_bilgisi"]);
$site_id_888         = $gvn->zrakam($_POST["site_id_888"]);
$site_id_777         = $gvn->zrakam($_POST["site_id_777"]);
$site_id_699         = $gvn->zrakam($_POST["site_id_699"]);
$site_id_700         = $gvn->zrakam($_POST["site_id_700"]);
$site_id_701         = $gvn->zrakam($_POST["site_id_701"]);
$site_id_702         = $gvn->zrakam($_POST["site_id_702"]);
$site_id_661         = $gvn->zrakam($_POST["site_id_661"]);
$site_id_662         = $gvn->zrakam($_POST["site_id_662"]);
$site_id_663         = $gvn->zrakam($_POST["site_id_663"]);
$site_id_664         = $gvn->zrakam($_POST["site_id_664"]);
$site_id_665         = $gvn->zrakam($_POST["site_id_665"]);
$site_id_666         = $gvn->zrakam($_POST["site_id_666"]);
$site_id_667         = $gvn->zrakam($_POST["site_id_667"]);
$site_id_668         = $gvn->zrakam($_POST["site_id_668"]);
$site_id_669         = $gvn->zrakam($_POST["site_id_669"]);
$site_id_335         = $gvn->zrakam($_POST["site_id_335"]);
$site_id_334         = $gvn->zrakam($_POST["site_id_334"]);
$site_id_306         = $gvn->zrakam($_POST["site_id_306"]);
$aidat               = $gvn->prakam($_POST["aidat"]);
$metrekare_fiyat     = $gvn->prakam($_POST["metrekare_fiyat"]);
$ada_no              = $gvn->html_temizle($_POST["ada_no"]);
$parsel_no           = $gvn->html_temizle($_POST["parsel_no"]);
$pafta_no            = $gvn->html_temizle($_POST["pafta_no"]);
$kaks_emsal          = $gvn->html_temizle($_POST["kaks_emsal"]);
$gabari              = $gvn->html_temizle($_POST["gabari"]);
$imar_durum          = $gvn->html_temizle($_POST["imar_durum"]);
$tapu_durumu         = $gvn->html_temizle($_POST["tapu_durumu"]);
$katk                = $gvn->html_temizle($_POST["katk"]);
$krediu              = $gvn->html_temizle($_POST["krediu"]);
$takas               = $gvn->html_temizle($_POST["takas"]);
$kullanim_durum      = $gvn->html_temizle($_POST["kullanim_durum"]);
$maps                = $gvn->html_temizle($_POST["maps"]);
$danisman_id         = $gvn->zrakam($_POST["danisman_id"]);
$onecikan            = $gvn->zrakam($_POST["onecikan"]);
$icerik              = $_POST["icerik"];
$notu                = $_POST["notu"];
$cephe_ozellikler    = $_POST["cephe_ozellikler"];
$ic_ozellikler       = $_POST["ic_ozellikler"];
$dis_ozellikler      = $_POST["dis_ozellikler"];
$altyapi_ozellikler  = $_POST["altyapi_ozellikler"];
$konum_ozellikler    = $_POST["konum_ozellikler"];
$genel_ozellikler    = $_POST["genel_ozellikler"];
$manzara_ozellikler  = $_POST["manzara_ozellikler"];
$btarih              = date("Y-m-d", strtotime("+100 year"));

// Zorunlu alanların boş olup olmadığını kontrol et
if ($fonk->bosluk_kontrol($ilan_no) || $fonk->bosluk_kontrol($baslik) || $il == 0) {
    die($fonk->ajax_hata("Lütfen zorunlu alanları doldurunuz."));
}

// Ülke ID'sini belirle
$ulke_id = ($ulke_id == 0) ? $db->query("SELECT id FROM ulkeler_501 ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_OBJ)->id : $ulke_id;

// İlan numarasının benzersiz olup olmadığını kontrol et
$noKontrol = $db->prepare("SELECT ilan_no FROM sayfalar WHERE site_id_555=501 AND ilan_no = ?");
$noKontrol->execute([$ilan_no]);
if ($noKontrol->rowCount() > 0 || $ilan_no < 1 || strlen($ilan_no) < 5) {
    $ilan_no = rand(10000000, 99999999);
}

// Fiyatları integer'a dönüştür
$fiyat_int = $gvn->para_int($fiyat);
$aidat_int = $gvn->para_int($aidat);
$fiyat_str = $gvn->para_str($fiyat_int);

// Ülke doğrulaması yap
$ulkekontrol = $db->prepare("SELECT * FROM ulkeler_501 WHERE id = ?");
$ulkekontrol->execute([$ulke_id]);
if ($ulkekontrol->rowCount() < 1) {
    die("<span class=''>Geçersiz ülke girdiniz!</span>");
}
$ulke = $ulkekontrol->fetch(PDO::FETCH_OBJ);

// İl doğrulaması yap
if ($il != 0) {
    $ilkontrol = $db->prepare("SELECT * FROM il WHERE id = ?");
    $ilkontrol->execute([$il]);
    if ($ilkontrol->rowCount() < 1) {
        die("<span class=''>Geçersiz il girdiniz!</span>");
    }
    $il = $ilkontrol->fetch(PDO::FETCH_OBJ);
}

// İlçe doğrulaması yap
if ($ilce != 0) {
    $ilcekontrol = $db->prepare("SELECT * FROM ilce WHERE id = ?");
    $ilcekontrol->execute([$ilce]);
    if ($ilcekontrol->rowCount() < 1) {
        die("<span class=''>Geçersiz ilçe girdiniz!</span>");
    }
    $ilce = $ilcekontrol->fetch(PDO::FETCH_OBJ);
}

// Mahalle doğrulaması yap
if ($mahalle != 0) {
    $mahakontrol = $db->prepare("SELECT * FROM mahalle_koy WHERE id = ?");
    $mahakontrol->execute([$mahalle]);
    if ($mahakontrol->rowCount() < 1) {
        die("<span class=''>".dil("TX25")."</span>");
    }
    $mahalle = $mahakontrol->fetch(PDO::FETCH_OBJ);
}

// Özellikleri birleştir
if (count($cephe_ozellikler) > 0) {
    $cephe_ozellikler = implode("<+>", $cephe_ozellikler);
}
if (count($ic_ozellikler) > 0) {
    $ic_ozellikler = implode("<+>", $ic_ozellikler);
}
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

// Özel SEO Ayarları
if ($fonk->bosluk_kontrol($keywords) && $fonk->bosluk_kontrol($description)) {
    $keywords = $emlak_durum;
    $xkeywords = ($konut_tipi != '') ? " ".$konut_tipi : '';
    $xkeywords .= ($xkeywords == '' && $konut_sekli != '') ? " ".$konut_sekli : '';
    $xkeywords .= ($xkeywords == '' && $emlak_tipi != '') ? " ".$emlak_tipi : '';
    $keywords .= $xkeywords;
    $keywords1 = ($ilce->ilce_adi != '') ? $keywords." ".$ilce->ilce_adi : '';
    $keywords2 =  ($ilce->ilce_adi != '') ? " , ".$keywords." ".$il->il_adi : $keywords." ".$il->il_adi;
    $keywords3 = ($mahalle->mahalle_adi != '') ? " , ".$keywords." ".$mahalle->mahalle_adi : '';
    $keywords4 = ($fiyat != '' && $fiyat != 0) ? " , ".$keywords." ".$fiyat_str." ".$pbirim : '';
    $keywords = ($keywords1.$keywords2.$keywords3.$keywords4);
    $description = $baslik;
    $description .= ($keywords != '') ? " | ".$keywords : '';
}

// Eğer ilana resim yüklenmemişse kapak resimi default adını veriyoruz...
$resim = 'default_ilan_resim.jpg';

// PermaLink Kontrolü
$url = $gvn->PermaLink($baslik)."-".$ilan_no;
if ($dayarlar->permalink != 'Evet') {
    $url = strtolower(substr(md5(uniqid(rand())), 0, 10));
}

// Para Birimi Kontrolü
$pbirimler = explode(",", dil("PARA_BIRIMI"));
if (!in_array($pbirim, $pbirimler)) {
    $pbirim = $pbirimler[0];
}

// Danışman ID Kontrolü
if ($danisman_id != 0 && $accid == 0) {
    $danisman = $db->query("SELECT id FROM danismanlar_501 WHERE id = ".$danisman_id);
    if ($danisman->rowCount() > 0) {
        $acid = $danisman_id;
    } else {
        $acid = $danisman_id;
    }
} else {
    $acid = ($accid == 0) ? $hesap->id : $accid;
}

// İlanı Veritabanına Ekle
$prepare = "INSERT INTO sayfalar SET site_id_555=501,site_id_450=000,site_id_444=000,site_id_333=335,site_id_222=200,site_id_111=100, ekleme = ?, baslik = ?, fiyat = ?, ilan_no = ?, emlak_durum = ?, emlak_tipi = ?, ulke_id = ?, il_id = ?, ilce_id = ?, mahalle_id = ?, konut_tipi = ?, konut_sekli = ?, bulundugu_kat = ?, metrekare = ?, brut_metrekare = ?, yapi_durum = ?, oda_sayisi = ?, bina_yasi = ?, bina_kat_sayisi = ?, isitma = ?, banyo_sayisi = ?, esyali = ?, kullanim_durum = ?, site_ici = ?, kimden = ?, yetkis = ?, yetki_bilgisi = ?, site_id_888 = ?, site_id_777 = ?, site_id_699 = ?, site_id_700 = ?, site_id_701 = ?, site_id_702 = ?, site_id_661 = ?, site_id_662 = ?, site_id_663 = ?, site_id_664 = ?, site_id_665 = ?, site_id_666 = ?, site_id_667 = ?, site_id_668 = ?, site_id_669 = ?, site_id_335 = ?, site_id_334 = ?, site_id_306 = ?, aidat_int = ?, cephe_ozellikler = ?, ic_ozellikler = ?, dis_ozellikler = ?, maps = ?, resim = ?, acid = ?, 4, dil = ?, url = ?, ekleme_tarihi = ?, duzenleme_tarihi = ?, btarih = ?, icerik = ?, title = ?, keywords = ?, description = ?, resim2 = ?, 1, pbirim = ?, metrekare_fiyat = ?, ada_no = ?, parsel_no = ?, pafta_no = ?, kaks_emsal = ?, gabari = ?, imar_durum = ?, tapu_durumu = ?, katk = ?, krediu = ?, takas = ?, altyapi_ozellikler = ?, konum_ozellikler = ?, genel_ozellikler = ?, manzara_ozellikler = ?, notu = ?, danisman_id = ?";

try {
    $sql = $db->prepare($prepare);
    $sql->execute([
        1, $baslik, $fiyat_int, $ilan_no, $emlak_durum, $emlak_tipi, $ulke_id, $il_id, $ilce_id, $mahalle_id, $konut_tipi, $konut_sekli, $bulundugu_kat, $metrekare, $brut_metrekare, $yapi_durum, $oda_sayisi, $bina_yasi, $bina_kat_sayisi, $isitma, $banyo_sayisi, $esyali, $kullanim_durum, $site_ici, $kimden, $yetkis, $yetki_bilgisi, $site_id_888, $site_id_777, $site_id_699, $site_id_700, $site_id_701, $site_id_702, $site_id_661, $site_id_662, $site_id_663, $site_id_664, $site_id_665, $site_id_666, $site_id_667, $site_id_668, $site_id_669, $site_id_335, $site_id_334, $site_id_306, $aidat_int, $cephe_ozellikler, $ic_ozellikler, $dis_ozellikler, $maps, $resim, $acid, 4, $dil, $url, $fonk->datetime(), $fonk->datetime(), $btarih, $icerik, $title, $keywords, $description, $resim2, 1, $pbirim, $metrekare_fiyat, $ada_no, $parsel_no, $pafta_no, $kaks_emsal, $gabari, $imar_durum, $tapu_durumu, $katk, $krediu, $takas, $altyapi_ozellikler, $konum_ozellikler, $genel_ozellikler, $manzara_ozellikler, $notu, $danisman_id
    ]);
    $ilan_id = $db->lastInsertId();
} catch (PDOException $e) {
    echo $e->getMessage();
    die($fonk->ajax_hata("Teknik bir problem oluştu, Lütfen yetkili kişilere bildiriniz! #1 "));
}

if (empty($ilan_id)) {
    die($fonk->ajax_hata("Teknik bir problem oluştu, Lütfen yetkili kişilere bildiriniz! #2"));
}

// Dil sekmeleri kontrolü
$tabs = $_POST["tabs"];
if (!empty($tabs)) {
    foreach ($tabs as $key => $val) {
        $dilop = $db->prepare("SELECT * FROM diller_501 WHERE kisa_adi = ?");
        $dilop->execute([$key]);
        if ($dilop->rowCount() > 0 && !$fonk->bosluk_kontrol($val['baslik'])) {
            $dilop = $dilop->fetch(PDO::FETCH_OBJ);
            
            // Geçerli dil değişkenleri
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

            // Dil değişkenlerini al
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

            // Pozisyonlar
            $pos0 = array_search($emlak_durum, $d_emlk_drm);
            $pos1 = array_search($emlak_tipi, $d_emlk_tipi);
            $pos2 = array_search($konut_sekli, $d_knt_sekli);
            $pos3 = array_search($konut_tipi, $d_knt_tipi);
            if ($pos3 === false) {
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

            // Değişkenler
            $l_baslik = $gvn->html_temizle($val['baslik']);
            $l_icerik = $val['icerik'];
            $l_title = ($val['title'] == '') ? $title : $gvn->html_temizle($val['title']);
            $l_keywords = ($val['keywords'] == '') ? $keywords : $gvn->html_temizle($val['keywords']);
            $l_description = ($val['description'] == '') ? $description : $gvn->html_temizle($val['description']);
            $l_url = $gvn->PermaLink($l_baslik)."-".$ilan_no;
            $l_emlak_durum = $l_emlk_drm[$pos0];
            $l_emlak_tipi = $l_emlk_tipi[$pos1];
            $l_konut_sekli = $l_knt_sekli[$pos2];
            $l_konut_tipi = ($pos3 === false) ? $l_knt_tipi2[$pos3_1] : $l_knt_tipi[$pos3];
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
            
// Genel Özellikleri Düzelt
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
        1, $l_baslik, $fiyat_int, $ilan_no, $l_emlak_durum, $l_emlak_tipi, $ulke_id, $il_id, $ilce_id, $mahalle_id, $l_konut_tipi, $l_konut_sekli, $l_bulundugu_kat, $metrekare, $brut_metrekare, $l_yapi_durum, $l_oda_sayisi, $bina_yasi, $bina_kat_sayisi, $l_isitma, $banyo_sayisi, $esyali, $l_kullanim_durum, $site_ici, $l_kimden, $l_yetkis, $yetki_bilgisi, $site_id_888, $site_id_777, $site_id_699, $site_id_700, $site_id_701, $site_id_702, $site_id_661, $site_id_662, $site_id_663, $site_id_664, $site_id_665, $site_id_666, $site_id_667, $site_id_668, $site_id_669, $site_id_335, $site_id_334, $site_id_306, $aidat_int, $l_cephe_ozellikler, $l_ic_ozellikler, $l_dis_ozellikler, $maps, $resim, $acid, 4, $key, $l_url, $fonk->datetime(), $fonk->datetime(), $btarih, $l_icerik, $l_title, $l_keywords, $l_description, $resim2, 1, $pbirim, $metrekare_fiyat, $ada_no, $parsel_no, $pafta_no, $l_kaks_emsal, $l_gabari, $l_imar_durum, $l_tapu_durumu, $l_katk, $l_krediu, $l_takas, $l_altyapi_ozellikler, $l_konum_ozellikler, $l_genel_ozellikler, $l_manzara_ozellikler, $notu, $danisman_id
    ]);
} catch (PDOException $e) {
    echo $e->getMessage();
    die($fonk->ajax_hata("Transfer is problem ".$dil." => ".$key." --> Message: ".$e->getMessage()));
}

$fonk->yonlendir("index.php?p=ilan_ekle&id=".$ilan_id, 0);