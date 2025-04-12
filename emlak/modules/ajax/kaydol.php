<?php
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($hesap->id == "" AND $_POST){

    if($gayarlar->uyelik == 0){
        die();
    }

    $crz1 = $_COOKIE["crz1"];
    $adsoyad = $gvn->html_temizle($_POST["adsoyad"]);
    $ayr = @explode(" ", $adsoyad);
    $soyadi = end($ayr);
    array_pop($ayr);
    $adi = implode(" ", $ayr);
    $telefon = $gvn->rakam($_POST["telefon"]);
    $email = $gvn->html_temizle($_POST["email"]);
    $parola = $gvn->parola($_POST["parola"]);
    $parola_tekrar = $gvn->parola($_POST["parola_tekrar"]);
    $olusturma_tarih = $fonk->datetime();
    $ip = $fonk->IpAdresi();
    $sozlesme = $gvn->rakam($_POST["sozlesme"]);
    $turu = $gvn->zrakam($_POST["turu"]);

    $unvan = $gvn->html_temizle($_POST["unvan"]);
    $vergi_no = $gvn->html_temizle($_POST["vergi_no"]);
    $vergi_dairesi = $gvn->html_temizle($_POST["vergi_dairesi"]);
    $adres = $gvn->html_temizle($_POST["adres"]);
    $tcno = $gvn->rakam($_POST["tcno"]);
    $tconay = 0;
    $smsonay = 0;
    $elonay = 0;
    $nick_adi = ($unvan == '') ? $gvn->PermaLink($adsoyad) : $gvn->PermaLink($unvan);
    $tarihi = date("d.m.Y H:i");

    if($fonk->bosluk_kontrol($turu) == true){
        die('<span class="error">'.dil("TX353").'</span>');
    }elseif($turu < 0 OR $turu > 1){
        die('<span class="error">'.dil("TX353").'</span>');
    }elseif($fonk->bosluk_kontrol($adsoyad) == true){
        die('<span class="error">'.dil("TX32").'</span>');
    }elseif($gvn->eposta_kontrol($email) == false){
        die('<span class="error">'.dil("TX33").'</span>');
    }elseif($fonk->bosluk_kontrol($parola) == true){
        die('<span class="error">'.dil("TX34").'</span>');
    }elseif($fonk->bosluk_kontrol($parola_tekrar) == true){
        die('<span class="error">'.dil("TX35").'</span>');
    }elseif($fonk->bosluk_kontrol($telefon) == true AND $gayarlar->sms_aktivasyon == 1){
        die('<span class="error">'.dil("TX36").'</span>');
    }elseif(strlen($telefon) < 10 AND $gayarlar->sms_aktivasyon == 1){
        die('<span class="error">'.dil("TX36").'</span>');
    }elseif($sozlesme != 1){
        die('<span class="error">'.dil("TX37").'</span>');
    }

    if($turu == 0 OR $turu == 2){
        if($fonk->bosluk_kontrol($tcno) == true AND $gayarlar->tcnod == 1){
            die('<span class="error">'.dil("TX369").'</span>');
        }elseif($gayarlar->tcnod == 1 AND $gvn->tcNoCheck($tcno) == false){
            die('<span class="error">'.dil("TX370").'</span>');
        }
    }

    if($turu == 1){
        if($fonk->bosluk_kontrol($unvan) == true){
            die('<span class="error">'.dil("TX372").'</span>');
        }
        if($fonk->bosluk_kontrol($vergi_dairesi) == true){
            die('<span class="error">'.dil("TX372").'</span>');
        }
        if($fonk->bosluk_kontrol($vergi_no) == true){
            die('<span class="error">'.dil("TX372").'</span>');
        }
    }

    if($fonk->bosluk_kontrol($adres) == true AND $gayarlar->adresd==1){
        die('<span class="error">'.dil("TX371").'</span>');
    }

    if($parola_tekrar != $parola){
        die('<span class="error">'.dil("TX38").'</span>');
    }

    $kontrol2 = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND durum=1 AND (email=? OR telefon=? OR ip=?) ");
    $kontrol2->execute(array($email, $telefon, $ip));

    if($kontrol2->rowCount() > 0 ){
        die('<span class="error">'.dil("TX39").'</span>');
    }

    $kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND email=? OR telefon=? ");
    $kontrol->execute(array($email, $telefon));
    if($kontrol->rowCount() > 0 ){
        die('<span class="error">'.dil("TX40").'</span>');
    }

    // Doğrulama Code...
    $sms_onay = $gayarlar->sms_aktivasyon;
    if($gayarlar->sms_username == ''){
        $sms_onay = 0;
    }
    if($sms_onay == 1){
        $gonay = $gvn->rakam($_GET["gonay"]);
        if($gonay == 1){
            $scode = $gvn->rakam($_POST["scode"]);

            if($scode == ''){
                die('<span class="error">'.dil("TX374").'</span>');
            }

            $sorgula = $db->prepare("SELECT id FROM sgonderiler_501 WHERE no=? AND scode=? AND tarih=?");
            $sorgula->execute(array($telefon, $scode, $fonk->this_date()));
            $hak = $_SESSION["hak"];

            if($hak >= 3){
                die('<span class="error">'.dil("TX375").'</span>');
            }elseif($sorgula->rowCount() > 0){
                $del = $db->prepare("DELETE FROM sgonderiler_501 WHERE scode=?");
                $del->execute(array($scode));
                unset($_SESSION["hak"]);
            }else{
                $hak = $hak+1;
                $_SESSION["hak"] = $hak;
                die('<span class="error">'.dil("TX376").'</span>');
            }
        }else{
            $cachesorg = $db->prepare("SELECT id FROM sgonderiler_501 WHERE ip=? AND tarih=?");
            $cachesorg->execute(array($ip, $fonk->this_date()));

            if($cachesorg->rowCount() >= 3){
                die('<span class="error">'.dil("TX377").'</span>');
            }

            $scode = rand(10000, 99999);
            $fonk->bildirim_gonder(array($scode, date("d.m.Y H:i")), "uyelik_sms_onay_kodu", $email, $telefon);

            $cacins = $db->prepare("INSERT INTO sgonderiler SET ip=?, tarih=?, scode=?, no=? ");
            $cacins->execute(array($ip, $fonk->this_date(), $scode, $telefon));
            $_SESSION["hak"] = 0;
            ?>
            <script>
                $('html, body').animate({scrollTop: 0}, 1000);
                $("#KaydolForm").attr("action","ajax.php?p=kaydol&gonay=1");
                $("#telout").html('<?=$telefon;?>');
                $("#uyelik").slideUp(500,function(){
                    $("#Gonay").slideDown(500);
                });
            </script>
            <?
            die();
        }
    } // Doğrulama Code... End

    if($gayarlar->tcnod==1){
        $tconay = 1;
    }
    if($gayarlar->sms_aktivasyon==1){
        $smsonay = 1;
    }

    if($turu == 0){
        $ua = $fonk->UyelikAyarlar();
        $uane = $ua["bireysel_uyelik"];
        $aylik_ilan_limit = $uane["aylik_ilan_limit"];
        $ilan_resim_limit = $uane["ilan_resim_limit"];
        $ilan_yayin_sure = $uane["ilan_yayin_sure"];
        $ilan_yayin_periyod = $uane["ilan_yayin_periyod"];
        $danisman_limit = 0;
    }elseif($turu == 1){
        $ua = $fonk->UyelikAyarlar();
        $uane = $ua["kurumsal_uyelik"];
        $danisman_limit = $uane["danisman_limit"];
        $aylik_ilan_limit = $uane["aylik_ilan_limit"];
        $ilan_resim_limit = $uane["ilan_resim_limit"];
        $ilan_yayin_sure = $uane["ilan_yayin_sure"];
        $ilan_yayin_periyod = $uane["ilan_yayin_periyod"];
    }else{
        $danisman_limit = 0;
        $aylik_ilan_limit = 0;
        $ilan_resim_limit = 0;
        $ilan_yayin_sure = 0;
        $ilan_yayin_periyod = "";
    }

    try {
        $olustur = $db->prepare("INSERT INTO hesaplar SET site_id_888=100,site_id_777=501501,site_id_699=200,site_id_700=335501,site_id_701=501501,site_id_702=300,site_id_555=501,site_id_450=000,site_id_444=000,site_id_333=335,site_id_335=335,site_id_334=334,site_id_306=306,site_id_222=200,site_id_111=100,adi=?,soyadi=?,email=?,parola=?,olusturma_tarih=?,telefon=?,ip=?,mail_izin=?,sms_izin=?,turu=?,nick_adi=?,unvan=?,vergi_no=?,vergi_dairesi=?,tcno=?,adres=?,aylik_ilan_limit=?,ilan_resim_limit=?,ilan_yayin_sure=?,ilan_yayin_periyod=?,danisman_limit=? ");
        $olustur->execute(array($adi, $soyadi, $email, $parola, $olusturma_tarih, $telefon, $ip, 1, 1, $turu, $nick_adi, $unvan, $vergi_no, $vergi_dairesi, $tcno, $adres, $aylik_ilan_limit, $ilan_resim_limit, $ilan_yayin_sure, $ilan_yayin_periyod, $danisman_limit));
    }catch(PDOException $e){
        die('<span class="error">'.dil("TX28").'</span>');
    }

    $lid = $db->lastInsertId();

    if($lid != "" OR $lid != 0){

        $secret = $fonk->login_secret_key($lid, $parola);

        $_SESSION["acid"] = $lid;
        $_SESSION["acpw"] = $parola;

        $baskasi = $db->prepare("SELECT nick_adi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND nick_adi=? AND id!=?");
        $baskasi->execute(array($nick_adi, $lid));
        if($baskasi->rowCount()>0){
            $nick_adi .= "-".$lid;
            $nup = $db->prepare("UPDATE hesaplar SET nick_adi=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$lid);
            $nup->execute(array($nick_adi));
        }

        $db->query("UPDATE hesaplar SET login_secret='".$secret."' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$lid);

        ?>
        <script>
            $("#KaydolForm").slideUp(500,function(){
                $("#TamamPnc").slideDown(500);
            });
            $('html, body').animate({scrollTop: 250}, 500);
        </script>
        <?

        $fonk->bildirim_gonder(array($adsoyad, $email, $parola, SITE_URL."hesabim"), "kaydol", $email, $telefon);

        $gt = $gvn->html_temizle($_SERVER["HTTP_REFERER"]);
        $fonk->yonlendir($gt, 5000); 
    }
}