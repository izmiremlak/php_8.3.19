<?php
if($_POST){
    if($hesap->id != ""){
        $danisman_limit = $hesap->danisman_limit;
        $paketi = $db->query("SELECT * FROM upaketler_501 WHERE acid=".$hesap->id." AND durum=1 AND btarih>NOW()");
        if($paketi->rowCount() > 0){
            $paketi = $paketi->fetch(PDO::FETCH_OBJ);
            $danisman_limit += ($paketi->danisman_limit == 0) ? 9999 : $paketi->danisman_limit;
            $danisman_limit -= $db->query("SELECT id FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=".$paketi->acid." AND pid=".$paketi->id)->rowCount();
        }

        if($danisman_limit < 1){
            die('<span class="error">'.dil("TX608").'</span>');
        }

        $pid = ($paketi->id == '') ? 0 : $paketi->id;
        $turu = 2;
        $adsoyad = $gvn->html_temizle($_POST["adsoyad"]);
        $ayr = @explode(" ", $adsoyad);
        $soyadi = end($ayr);
        array_pop($ayr);
        $adi = implode(" ", $ayr);
        $nick_adi = $gvn->PermaLink($adsoyad);
        $email = $gvn->html_temizle($_POST["email"]);
        $telefon = $gvn->rakam($_POST["telefon"]);
        $sabit_telefon = $gvn->rakam($_POST["sabit_telefon"]);
        $parola = $gvn->parola($_POST["parola"]);
        $parola_tekrar = $gvn->parola($_POST["parola_tekrar"]);

        /*
        $unvan = $gvn->html_temizle($_POST["unvan"]);
        $vergi_no = $gvn->html_temizle($_POST["vergi_no"]);
        $vergi_dairesi = $gvn->html_temizle($_POST["vergi_dairesi"]);
        $adres = $gvn->html_temizle($_POST["adres"]);
        $tcno = $gvn->rakam($_POST["tcno"]);
        $hakkinda = $gvn->mesaj(htmlspecialchars($_POST["hakkinda"], ENT_QUOTES));
        */

        $telefond = $gvn->zrakam($_POST["telefond"]);
        $sabittelefond = $gvn->zrakam($_POST["sabittelefond"]);
        $epostad = $gvn->zrakam($_POST["epostad"]);
        $avatard = $gvn->zrakam($_POST["avatard"]);
        $sms_izin = $gvn->zrakam($_POST["sms_izin"]);
        $mail_izin = $gvn->zrakam($_POST["mail_izin"]);
        $avatar = $_FILES["avatar"];
        $hakkinda = $gvn->filtre($_POST["hakkinda"]);

        if($fonk->bosluk_kontrol($adsoyad) == true){
            die('<span class="error">'.dil("TX14").'</span>');
        } elseif($gvn->eposta_kontrol($email) == false){
            die('<span class="error">'.dil("TX15").'</span>');
        }

        if($avatar["tmp_name"] != ''){
            $max_size = 2097152; // Yüklenecek her resim için max 2Mb boyut sınırı
            $allow_exten = array('.jpg','.jpeg','.png'); // İzin verilen uzantılar
            $file = $avatar;

            $tmp = $file["tmp_name"]; // Kaynak
            $xadi = $file["name"]; // Dosya adı
            $size = $file["size"]; // Boyutu
            $uzanti = $fonk->uzanti($xadi); // Uzantısı

            if($size <= $max_size){ // Boyutu max boyutu geçmiyorsa devam
                if(in_array($uzanti, $allow_exten)){ // İzin verilen uzantılarda ise devam
                    $exmd = strtolower(substr(md5(uniqid(rand())), 0, 18));
                    $randnm = $exmd.$uzanti;
                    $resim = $fonk->resim_yukle(true, $file, $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', $gorsel_boyutlari['avatar']['thumb_x'], $gorsel_boyutlari['avatar']['thumb_y'], true); // Küçük
                    $resim = $fonk->resim_yukle(false, $file, $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', $gorsel_boyutlari['avatar']['orjin_x'], $gorsel_boyutlari['avatar']['orjin_y'], true); // Büyük boy
                    if($resim == ''){ // Eğer resim yüklenmemişse
                        die('<span class="error">Image Upload is Failed!</span>');
                    }
                } else {
                    die('<span class="error">'.dil("TX355").'</span>');
                }
            } else {
                die('<span class="error">'.dil("TX354").'</span>');
            }
        }

        $kontrol2 = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND durum=1 AND (email=? OR ip=?) ");
        $kontrol2->execute(array($email, $ip));

        if($kontrol2->rowCount() > 0 ){
            die('<span class="error">'.dil("TX16").'</span>');
        }

        $kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND email=?");
        $kontrol->execute(array($email));

        if($kontrol->rowCount() > 0 ){
            die('<span class="error">'.dil("TX17").'</span>');
        }

        if($telefon != ''){
            $kontrol3 = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND telefon=?");
            $kontrol3->execute(array($telefon));
            if($kontrol3->rowCount() > 0 ){
                die('<span class="error">'.dil("TX18").'</span>');
            }
        }

        if($fonk->bosluk_kontrol($parola) == true){
            die('<span class="error">'.dil("TX34").'</span>');
        } elseif($fonk->bosluk_kontrol($parola_tekrar) == true){
            die('<span class="error">'.dil("TX35").'</span>');
        }

        if($parola_tekrar != $parola){
            die('<span class="error">'.dil("TX20").'</span>');
        }

        try{
            $sql = $db->prepare("INSERT INTO hesaplar SET site_id_888=100,site_id_777=501501,site_id_699=200,site_id_700=335501,site_id_701=501501,site_id_702=300,site_id_555=501,site_id_450=000,site_id_444=000,site_id_333=335,site_id_335=335,site_id_334=334,site_id_306=306,site_id_222=200,site_id_111=100, turu=?, adi=?, soyadi=?, telefon=?, email=?, sms_izin=?, mail_izin=?, sabit_telefon=?, telefond=?, sabittelefond=?, epostad=?, avatard=?, nick_adi=?, parola=?, avatar=?, kid=?, olusturma_tarih=?, hakkinda=?, pid=?");
            $sql->execute(array($turu, $adi, $soyadi, $telefon, $email, $sms_izin, $mail_izin, $sabit_telefon, $telefond, $sabittelefond, $epostad, $avatard, $nick_adi, $parola, $resim, $hesap->id, $fonk->datetime(), $hakkinda, $pid));

            $acid = $db->lastInsertId();
        } catch(PDOException $e){
            die($e->getMessage());
        }

        $baskasi = $db->prepare("SELECT nick_adi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND nick_adi=? AND id!=?");
        $baskasi->execute(array($nick_adi, $acid));
        if($baskasi->rowCount() > 0){
            $nick_adi .= "-".$acid;
            $nup = $db->prepare("UPDATE hesaplar SET nick_adi=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$acid);
            $nup->execute(array($nick_adi));
        }

        $fonk->yonlendir("eklenen-danismanlar", 2000);
        ?>
        <script>
            $("#DanismanEkleForm").slideUp(500, function(){
                $("#TamamDiv").slideDown(500);
            });
            $('html, body').animate({scrollTop: 250}, 500);
        </script>
        <?
    }
}
?>