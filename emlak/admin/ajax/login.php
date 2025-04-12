<?php
if($_POST){
    if($hesap->id == ""){

        $email = $gvn->eposta($_POST["email"]);
        $parola = $gvn->html_temizle($_POST["parola"]);
        $otut = $gvn->rakam($_POST["otut"]);

        if($fonk->bosluk_kontrol($email) == true OR $fonk->bosluk_kontrol($parola) == true){
            die($fonk->hata("E-Posta ve parola bilgisi gereklidir!"));
        }

        $kontrol = $db->prepare("SELECT id, email, parola, tipi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND (email=:eposta AND parola=:sifre) AND (tipi=2 OR tipi=1) ");
        $kontrol->execute(array('eposta' => $email, 'sifre' => $parola));

        if($kontrol->rowCount() != 0){
            $hesap = $kontrol->fetch(PDO::FETCH_OBJ);
            $secret = $fonk->login_secret_key($hesap->id, $parola);
            $dt = $fonk->datetime();
            $ip_adres = $fonk->IpAdresi();

            $hup = $db->prepare("UPDATE hesaplar SET ip=:ip_adresi, son_giris_tarih=:tarih, login_secret=:secret WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:hesap_id");
            $hup->execute(array(
                'ip_adresi' => $ip_adres,
                'tarih' => $dt,
                'secret' => $secret,
                'hesap_id' => $hesap->id
            ));

            $_SESSION["acid"] = $hesap->id;
            $_SESSION["acpw"] = $hesap->parola;

            if($otut == 1){
                setcookie("acid", $hesap->id, time()+60*60*24*30);
                setcookie("acpw", $parola, time()+60*60*24*30);
                setcookie("acsecret", $secret, time()+60*60*24*30);
            }
            $fonk->tamam("Giriş yapılıyor...");
            $fonk->yonlendir("index.php", 0);

        }else{
            $fonk->hata("E-Posta veya parolanız hatalı!");
        }

    }
}