<?php
if ($_POST) {
    if ($hesap->id != "") {

        // id parametresini temizle
        $id = $gvn->rakam($_GET["id"]);
        
        // Veritabanından kullanıcı kontrolü
        $kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND kid=?");
        $kontrol->execute([$id, $hesap->id]);
        if ($kontrol->rowCount() < 1) {
            die();
        }
        $snc = $kontrol->fetch(PDO::FETCH_OBJ);

        // Kullanıcıdan gelen verileri temizle ve filtrele
        $adsoyad = $gvn->html_temizle($_POST["adsoyad"]);
        $ayr = explode(" ", $adsoyad);
        $soyadi = end($ayr);
        array_pop($ayr);
        $adi = implode(" ", $ayr);
        $nick_adi = $gvn->PermaLink($adsoyad);
        $email = $gvn->html_temizle($_POST["email"]);
        $telefon = $gvn->rakam($_POST["telefon"]);
        $sabit_telefon = $gvn->rakam($_POST["sabit_telefon"]);
        $parola = $gvn->parola($_POST["parola"]);
        $parola_tekrar = $gvn->parola($_POST["parola_tekrar"]);
        $telefond = $gvn->zrakam($_POST["telefond"]);
        $sabittelefond = $gvn->zrakam($_POST["sabittelefond"]);
        $epostad = $gvn->zrakam($_POST["epostad"]);
        $avatard = $gvn->zrakam($_POST["avatard"]);
        $sms_izin = $gvn->zrakam($_POST["sms_izin"]);
        $mail_izin = $gvn->zrakam($_POST["mail_izin"]);
        $durum = $gvn->zrakam($_POST["durum"]);
        $avatar = $_FILES["avatar"];
        $hakkinda = $gvn->filtre($_POST["hakkinda"]);

        // Durum değerini kontrol et
        if ($durum < 0 || $durum > 1) {
            $durum = $snc->durum;
        }

        // E-posta kontrolü
        if ($gvn->eposta_kontrol($email) == false) {
            die('<span class="error">' . dil("TX15") . '</span>');
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
                        $db->query("UPDATE hesaplar SET avatar='$resim' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->id);
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
        $kontrol2->execute([$snc->id, $email, $ip]);
        if ($kontrol2->rowCount() > 0) {
            die('<span class="error">' . dil("TX16") . '</span>');
        }

        // E-posta kontrolü
        $kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id!=? AND email=?");
        $kontrol->execute([$snc->id, $email]);
        if ($kontrol->rowCount() > 0) {
            die('<span class="error">' . dil("TX17") . '</span>');
        }

        // Telefon kontrolü
        if ($telefon != '') {
            $kontrol3 = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id!=? AND telefon=?");
            $kontrol3->execute([$snc->id, $telefon]);
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
            $gnc = $db->prepare("UPDATE hesaplar SET parola=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->id);
            $gnc->execute([$parola]);
        }

        // Benzersiz kullanıcı adı kontrolü
        $baskasi = $db->prepare("SELECT nick_adi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND nick_adi=? AND id!=?");
        $baskasi->execute([$nick_adi, $snc->id]);
        if ($baskasi->rowCount() > 0) {
            $nick_adi .= "-" . $snc->id;
        }

        // Kullanıcı bilgilerini güncelle
        $sql = $db->prepare("UPDATE hesaplar SET telefon=?, email=?, sms_izin=?, mail_izin=?, sabit_telefon=?, telefond=?, sabittelefond=?, epostad=?, avatard=?, nick_adi=?, durum=?, hakkinda=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
        $sql->execute([$telefon, $email, $sms_izin, $mail_izin, $sabit_telefon, $telefond, $sabittelefond, $epostad, $avatard, $nick_adi, $durum, $hakkinda, $snc->id]);

        echo '<span class="complete">' . dil("TX21") . '</span>';
    }
}