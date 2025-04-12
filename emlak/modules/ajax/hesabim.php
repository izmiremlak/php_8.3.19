<?php
if ($_POST) {
    if ($hesap->id != "") {

        // Kullanıcıdan gelen verileri temizle ve filtrele
        $turu = $hesap->turu;
        $adsoyad = $gvn->html_temizle($_POST["adsoyad"]);
        $ayr = explode(" ", $adsoyad);
        $soyadi = end($ayr);
        array_pop($ayr);
        $adi = implode(" ", $ayr);
        $email = $gvn->html_temizle($_POST["email"]);
        $telefon = $gvn->rakam($_POST["telefon"]);
        $sabit_telefon = $gvn->rakam($_POST["sabit_telefon"]);
        $parola = $gvn->parola($_POST["parola"]);
        $parola_tekrar = $gvn->parola($_POST["parola_tekrar"]);
        $unvan = $gvn->html_temizle($_POST["unvan"]);
        $vergi_no = $gvn->html_temizle($_POST["vergi_no"]);
        $vergi_dairesi = $gvn->html_temizle($_POST["vergi_dairesi"]);
        $adres = ($turu == 2) ? $hesap->adres : $gvn->html_temizle($_POST["adres"]);
        $tcno = ($turu == 2) ? $hesap->tcno : $gvn->rakam($_POST["tcno"]);
        $telefond = $gvn->zrakam($_POST["telefond"]);
        $sabittelefond = $gvn->zrakam($_POST["sabittelefond"]);
        $epostad = $gvn->zrakam($_POST["epostad"]);
        $avatard = $gvn->zrakam($_POST["avatard"]);
        $sms_izin = $gvn->zrakam($_POST["sms_izin"]);
        $mail_izin = $gvn->zrakam($_POST["mail_izin"]);
        $il = $gvn->zrakam($_POST["il"]);
        $ilce = $gvn->zrakam($_POST["ilce"]);
        $mahalle = $gvn->zrakam($_POST["mahalle"]);
        $maps = $gvn->html_temizle($_POST["maps"]);
        $avatar = $_FILES["avatar"];
        $hakkinda = $gvn->filtre($_POST["hakkinda"]);
        $nick_adi = ($unvan != '' && $hesap->turu == 1) ? $gvn->PermaLink($unvan) : $hesap->nick_adi;

        // E-posta kontrolü
        if ($gvn->eposta_kontrol($email) == false) {
            die('<span class="error">' . dil("TX15") . '</span>');
        }

        // Kullanıcı türüne göre ek kontroller
        if ($turu == 0) {
            $unvan = ($unvan != '') ? '' : $unvan;
            $tcno = ($tcno == '' && $hesap->tcno != '') ? $hesap->tcno : $tcno;

            if ($fonk->bosluk_kontrol($tcno) == true && $gayarlar->tcnod == 1 && $hesap->tcno == '') {
                die('<span class="error">' . dil("TX369") . '</span>');
            } elseif ($gayarlar->tcnod == 1 && $gvn->tcNoCheck($tcno) == false && $hesap->tcno == '') {
                die('<span class="error">' . dil("TX370") . '</span>');
            }
        }

        if ($turu == 1) {
            if ($fonk->bosluk_kontrol($unvan) == true) {
                die('<span class="error">' . dil("TX372") . '</span>');
            }
            if ($fonk->bosluk_kontrol($vergi_dairesi) == true) {
                die('<span class="error">' . dil("TX372") . '</span>');
            }
            if ($fonk->bosluk_kontrol($vergi_no) == true) {
                die('<span class="error">' . dil("TX372") . '</span>');
            }
        }

        if ($turu == 0 || $turu == 1) {
            if ($fonk->bosluk_kontrol($adres) == true && $gayarlar->adresd == 1) {
                die('<span class="error">' . dil("TX371") . '</span>');
            }
        }

        if ($hesap->turu == 1) {
            // İl kontrolü
            if ($il != 0) {
                $ilkontrol = $db->prepare("SELECT * FROM il WHERE id=?");
                $ilkontrol->execute([$il]);
                if ($ilkontrol->rowCount() < 1) {
                    die("<span class='error'>" . dil("TX24") . "</span>");
                }
            }

            // İlçe kontrolü
            if ($ilce != 0) {
                $ilcekontrol = $db->prepare("SELECT * FROM ilce WHERE id=?");
                $ilcekontrol->execute([$ilce]);
                if ($ilcekontrol->rowCount() < 1) {
                    die("<span class='error'>" . dil("TX25") . "</span>");
                }
            }

            // Mahalle kontrolü
            if ($mahalle != 0) {
                $mahakontrol = $db->prepare("SELECT * FROM mahalle_koy WHERE id=?");
                $mahakontrol->execute([$mahalle]);
                if ($mahakontrol->rowCount() < 1) {
                    die("<span class='error'>" . dil("TX25") . "</span>");
                }
            }

            if (strlen($maps) >= 40) {
                die("<span class='error'>We have the problem!</span>");
            }

            // Adres güncelleme
            $adres_update = $db->prepare("UPDATE hesaplar SET il_id=?, ilce_id=?, mahalle_id=?, maps=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
            $adres_update->execute([$il, $ilce, $mahalle, $maps, $hesap->id]);
        }

        // Eğer avatar yüklenmişse işlemleri yap
        if ($avatar["tmp_name"] != '') {
            $max_size = 2097152; // Max 2Mb
            $allow_exten = ['.jpg', '.jpeg', '.png']; // İzin verilen uzantılar
            $file = $avatar;

            $tmp = $file["tmp_name"];
            $xadi = $file["name"];
            $size = $file["size"];
            $uzanti = $fonk->uzanti($xadi);

            if ($size <= $max_size) {
                if (in_array($uzanti, $allow_exten)) {
                    $exmd = strtolower(substr(md5(uniqid(rand())), 0, 18));
                    $randnm = $exmd . $uzanti;
                    $resim = $fonk->resim_yukle(true, $file, $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', $gorsel_boyutlari['avatar']['thumb_x'], $gorsel_boyutlari['avatar']['thumb_y'], true);
                    $resim = $fonk->resim_yukle(false, $file, $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', $gorsel_boyutlari['avatar']['orjin_x'], $gorsel_boyutlari['avatar']['orjin_y'], true);
                    if ($resim != '') {
                        $db->query("UPDATE hesaplar SET avatar='$randnm' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $hesap->id);
?>
<script type="text/javascript">
$(document).ready(function(){
    $("#avatar_image").attr("src","/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$resim;?>");
});
</script>
<?php
                    } else {
                        die('<span class="error">Resim Yükleme Başarısız!</span>');
                    }
                } else {
                    die('<span class="error">' . dil("TX355") . '</span>');
                }
            } else {
                die('<span class="error">' . dil("TX354") . '</span>');
            }
        }

        // Benzersiz e-posta kontrolü
        $kontrol2 = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND durum=1 AND id!=? AND (email=? OR ip=?)");
        $kontrol2->execute([$hesap->id, $email, $ip]);
        if ($kontrol2->rowCount() > 0) {
            die('<span class="error">' . dil("TX16") . '</span>');
        }

        // E-posta kontrolü
        $kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id!=? AND email=?");
        $kontrol->execute([$hesap->id, $email]);
        if ($kontrol->rowCount() > 0) {
            die('<span class="error">' . dil("TX17") . '</span>');
        }

        // Telefon kontrolü
        if ($telefon != '') {
            $kontrol3 = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id!=? AND telefon=?");
            $kontrol3->execute([$hesap->id, $telefon]);
            if ($kontrol3->rowCount() > 0) {
                die('<span class="error">' . dil("TX18") . '</span>');
            }
        }

        // Parola kontrolü
        if ($parola != "") {
            if ($fonk->bosluk_kontrol($parola_tekrar) == true) {
                die('<span class="error">' . dil("TX19") . '');
            }
            if ($parola_tekrar != $parola) {
                die('<span class="error">' . dil("TX20") . '');
            }
            $gnc = $db->prepare("UPDATE hesaplar SET parola=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $hesap->id);
            $gnc->execute([$parola]);
            $_SESSION["acpw"] = $parola;
            if ($ck_acpw != "") {
                $login_secret = $fonk->login_secret_key($hesap->id, $parola);
                setcookie("acid", $hesap->id, time() + 60 * 60 * 24 * 30);
                setcookie("acpw", $parola, time() + 60 * 60 * 24 * 30);
                setcookie("acsecret", $login_secret, time() + 60 * 60 * 24 * 30);
                $db->query("UPDATE hesaplar SET login_secret='$login_secret' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $hesap->id);
            }
        }

        // TC onay durumu kontrolü
        if ($gayarlar->tcnod == 1) {
            $tconay = 1;
        } else {
            $tconay = $hesap->tconay;
        }

        // Benzersiz kullanıcı adı kontrolü
        if ($fonk->bosluk_kontrol($nick_adi) == false) {
            $baskasi = $db->prepare("SELECT nick_adi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND nick_adi=? AND id!=?");
            $baskasi->execute([$nick_adi, $hesap->id]);
            if ($baskasi->rowCount() > 0) {
                $nick_adi .= "-" . $hesap->id;
            }
        }

        // Kullanıcı bilgilerini güncelle
        $sql = $db->prepare("UPDATE hesaplar SET telefon=?, email=?, sms_izin=?, mail_izin=?, sabit_telefon=?, telefond=?, sabittelefond=?, epostad=?, avatard=?, tcno=?, unvan=?, vergi_no=?, vergi_dairesi=?, adres=?, tconay=?, nick_adi=?, hakkinda=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
        $sql->execute([$telefon, $email, $sms_izin, $mail_izin, $sabit_telefon, $telefond, $sabittelefond, $epostad, $avatard, $tcno, $unvan, $vergi_no, $vergi_dairesi, $adres, $tconay, $nick_adi, $hakkinda, $hesap->id]);

        echo '<span class="complete">' . dil("TX21") . '</span>';
    }
}