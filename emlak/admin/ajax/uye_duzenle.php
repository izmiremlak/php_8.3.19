<?php
if ($_POST) {
    if ($hesap->id != "" AND $hesap->tipi != 0) {

        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM hesaplar WHERE site_id_555=501 AND id=:ids");
        $snc->execute(array('ids' => $id));

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        $adsoyad = $gvn->html_temizle($_POST["adsoyad"]);
        $webadres = $gvn->html_temizle($_POST["webadres"]);
        $ayr = @explode(" ", $adsoyad);
        $soyadi = end($ayr);
        array_pop($ayr);
        $adi = implode(" ", $ayr);
        $email = ($snc->tipi == 1) ? $snc->email : $gvn->html_temizle($_POST["email"]);
        $dunvan = ($snc->tipi == 1) ? $snc->dunvan : $gvn->html_temizle($_POST["dunvan"]);
        $telefon = $gvn->rakam($_POST["telefon"]);
        $sabit_telefon = $gvn->rakam($_POST["sabit_telefon"]);
        $parola = ($snc->tipi == 1) ? $snc->parola : $gvn->html_temizle($_POST["parola"]);
        $durum = $gvn->zrakam($_POST["durum"]);
        $sms_bildirim = $gvn->zrakam($_POST["sms_izin"]);
        $email_bildirim = $gvn->zrakam($_POST["mail_izin"]);
        $telefond = $gvn->zrakam($_POST["telefond"]);
        $sabittelefond = $gvn->zrakam($_POST["sabittelefond"]);
        $epostad = $gvn->zrakam($_POST["epostad"]);
        $avatard = $gvn->zrakam($_POST["avatard"]);
        $turu = ($snc->tipi == 1) ? $snc->turu : $gvn->zrakam($_POST["turu"]);
        $ilan_aktifet = $gvn->zrakam($_POST["ilan_aktifet"]);
        $ulke_id = $gvn->zrakam($_POST["ulke_id"]);
        $il = $gvn->zrakam($_POST["il"]);
        $ilce = $gvn->zrakam($_POST["ilce"]);
        $mahalle = $gvn->zrakam($_POST["mahalle"]);
        $maps = $gvn->html_temizle($_POST["maps"]);
        $adres = $gvn->html_temizle($_POST["adres"]);
        $tcno = $gvn->rakam($_POST["tcno"]);
        $unvan = $gvn->html_temizle($_POST["unvan"]);
        $vergi_no = $gvn->html_temizle($_POST["vergi_no"]);
        $vergi_dairesi = $gvn->html_temizle($_POST["vergi_dairesi"]);
        $nick_adi = $gvn->html_temizle($_POST["nick_adi"]);
        $hakkinda = $_POST["hakkinda"];
        $danisman_limit = $gvn->zrakam($_POST["danisman_limit"]);
        $aylik_ilan_limit = $gvn->zrakam($_POST["aylik_ilan_limit"]);
        $ilan_resim_limit = $gvn->zrakam($_POST["ilan_resim_limit"]);
        $ilan_yayin_sure = $gvn->zrakam($_POST["ilan_yayin_sure"]);
        $ilan_yayin_periyod = $gvn->html_temizle($_POST["ilan_yayin_periyod"]);
        $onecikar = $gvn->zrakam($_POST["onecikar"]);
        $onecikar_btarih = $gvn->html_temizle($_POST["onecikar_btarih"]);
        $onecikar_btarih = ($onecikar_btarih == '') ? '' : date("Y-m-d", strtotime($onecikar_btarih)) . " 23:59:59";
        $kid = ($turu == 2 && $snc->turu != 2) ? $hesap->id : $snc->kid;

        if ($fonk->bosluk_kontrol($adsoyad) == true OR $fonk->bosluk_kontrol($email) == true OR $fonk->bosluk_kontrol($parola) == true) {
            die($fonk->ajax_uyari("Lütfen adı soyadı, email ve parola alanlarını boş bırakmayınız."));
        }

        if ($ulke_id == 0) {
            $ulke_id = $snc->ulke_id;
        }

        if ($nick_adi == $snc->nick_adi) {
            if (($turu == 0 || $turu == 2) && ($adi != $snc->adi || $soyadi != $snc->soyadi)) {
                $nick_adi = $adi;
                $nick_adi .= ($soyadi != '') ? $soyadi : '';
                $nick_adi = $gvn->PermaLink($nick_adi);
            } elseif ($turu == 1 && $unvan != $snc->unvan) {
                $nick_adi = $gvn->PermaLink($unvan);
            } elseif ($turu == 1 && $unvan == '') {
                $nick_adi = $adi;
                $nick_adi .= ($soyadi != '') ? $soyadi : '';
                $nick_adi = $gvn->PermaLink($nick_adi);
            }
        }

        $resim1tmp = $_FILES['avatar']["tmp_name"];
        $resim1nm = $_FILES['avatar']["name"];
        $linkcek = "https://www.turkiyeemlaksitesi.com.tr";

        if ($resim1tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 14)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'avatar', $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', $gorsel_boyutlari['avatar']['thumb_x'], $gorsel_boyutlari['avatar']['thumb_y']);
            $resim = $fonk->resim_yukle(false, 'avatar', $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', $gorsel_boyutlari['avatar']['orjin_x'], $gorsel_boyutlari['avatar']['orjin_y']);

            ## veritabanı işlevi
            $avgn = $db->query("UPDATE hesaplar SET avatar='" . $resim . "' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $id);

            $fonk->ajax_tamam('Avatar Güncellendi');
            ?><script type="text/javascript">
            $(document).ready(function() {
                $('#avatar_src').attr("src", "<?= $linkcek; ?>/uploads/thumb/<?= $resim; ?>");
            });
            </script><?php
        }

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $gunc = $db->prepare("UPDATE hesaplar SET adi=?,soyadi=?,webadres=?,email=?,dunvan=?,telefon=?,parola=?,durum=?,sms_izin=?,mail_izin=?,turu=?,nick_adi=?,ulke_id=?,il_id=?,ilce_id=?,mahalle_id=?,maps=?,sabit_telefon=?,unvan=?,vergi_no=?,vergi_dairesi=?,adres=?,tcno=?,telefond=?,sabittelefond=?,epostad=?,avatard=?,hakkinda=?,aylik_ilan_limit=?,ilan_resim_limit=?,ilan_yayin_sure=?,ilan_yayin_periyod=?,danisman_limit=?,onecikar=?,onecikar_btarih=?,kid=?,ilan_aktifet=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $id);
            $gunc->execute(array($adi, $soyadi, $webadres, $email, $dunvan, $telefon, $parola, $durum, $sms_bildirim, $email_bildirim, $turu, $nick_adi, $ulke_id, $il, $ilce, $mahalle, $maps, $sabit_telefon, $unvan, $vergi_no, $vergi_dairesi, $adres, $tcno, $telefond, $sabittelefond, $epostad, $avatard, $hakkinda, $aylik_ilan_limit, $ilan_resim_limit, $ilan_yayin_sure, $ilan_yayin_periyod, $danisman_limit, $onecikar, $onecikar_btarih, $kid, $ilan_aktifet));
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        $fonk->ajax_tamam("Üye Bilgileri Güncellendi.");
    }
}