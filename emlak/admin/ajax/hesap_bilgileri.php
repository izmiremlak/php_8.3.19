<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcının kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $adi = htmlspecialchars($_POST["adi"], ENT_QUOTES, 'UTF-8');
        $soyadi = htmlspecialchars($_POST["soyadi"], ENT_QUOTES, 'UTF-8');
        $email = $gvn->eposta($_POST["email"]);
        $avatartmp = $_FILES['avatar']["tmp_name"];
        $avatarnm = $_FILES['avatar']["name"];

        $mparola = htmlspecialchars($_POST["mparola"], ENT_QUOTES, 'UTF-8');
        $yparola = htmlspecialchars($_POST["yparola"], ENT_QUOTES, 'UTF-8');
        $ytparola = htmlspecialchars($_POST["ytparola"], ENT_QUOTES, 'UTF-8');

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($adi) || $fonk->bosluk_kontrol($email)) {
            error_log("Ad veya e-posta boş bırakılamaz. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen adınızı veya e-posta adresinizi boş bırakmayınız."));
        }

        if ($fonk->bosluk_kontrol($mparola)) {
            error_log("Mevcut parola boş bırakılamaz. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen mevcut parolanızı giriniz."));
        }

        // Mevcut parolanın doğruluğunu kontrol et
        if ($mparola != $hesap->parola) {
            error_log("Mevcut parola yanlış. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Mevcut Parolanızı Yanlış Yazdınız!"));
        }

        // Yeni parola işlemleri
        if ($yparola != '') {
            if ($fonk->bosluk_kontrol($ytparola)) {
                error_log("Yeni parola tekrarı boş bırakılamaz. Tarih: " . date("Y-m-d H:i:s"));
                die($fonk->ajax_hata("Yeni Parola Tekrarı Giriniz!"));
            }

            if ($ytparola != $yparola) {
                error_log("Yeni parola tekrarı hatalı. Tarih: " . date("Y-m-d H:i:s"));
                die($fonk->ajax_hata("Yeni Parola Tekrarı Hatalı Yazdınız!"));
            }

            try {
                $guncelle = $db->prepare("UPDATE hesaplar SET parola=:yparola WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:acid");
                $guncelle->execute(['yparola' => $yparola, 'acid' => $hesap->id]);

                if ($guncelle) {
                    $fonk->ajax_tamam("Hesap Parolanız Güncellendi.");
                    $_SESSION["acpw"] = $yparola;

                    if ($ck_acpw != "") {
                        $login_secret = $fonk->login_secret_key($hesap->id, $yparola);
                        setcookie("acid", $hesap->id, time() + 60 * 60 * 24 * 30);
                        setcookie("acpw", $yparola, time() + 60 * 60 * 24 * 30);
                        setcookie("acsecret", $login_secret, time() + 60 * 60 * 24 * 30);
                        $db->query("UPDATE hesaplar SET login_secret='" . $login_secret . "' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $hesap->id);
                    }
                }
            } catch (PDOException $e) {
                error_log("Parola güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
                die($e->getMessage());
            }
        }

        // E-posta adresi kontrolü
        if ($gvn->eposta_kontrol($email) === false) {
            error_log("Geçersiz e-posta adresi. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("E-posta adresiniz geçersiz!"));
        }

        if ($email != $hesap->email) {
            $kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND email=:email");
            $kontrol->execute(['email' => $email]);
            if ($kontrol->rowCount() > 0) {
                error_log("E-posta başka hesap tarafından kullanılıyor. Tarih: " . date("Y-m-d H:i:s"));
                die($fonk->ajax_uyari("E-posta başka hesap tarafından kullanılıyor."));
            } else {
                $guncelle = $db->prepare("UPDATE hesaplar SET email=:email WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:id");
                $guncelle->execute(['email' => $email, 'id' => $hesap->id]);
                $fonk->ajax_tamam("E-posta adresiniz güncellendi.");
            }
        }

        // Avatar yükleme işlemleri
        if ($avatartmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($avatarnm);
            $avatar = $fonk->resim_yukle(true, 'avatar', $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', 128, 128);
            if ($avatar) {
                try {
                    $avgn = $db->prepare("UPDATE hesaplar SET avatar=:avatar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:id");
                    $avgn->execute(['avatar' => $avatar, 'id' => $hesap->id]);

                    if ($avgn) {
                        $fonk->ajax_tamam('Avatar Resimi Güncellendi');
                        ?><script type="text/javascript">
                        $(document).ready(function () {
                            $('.img-circle').attr("src", "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$avatar;?>");
                        });
                        </script><?php
                    }
                } catch (PDOException $e) {
                    error_log("Avatar güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
                    die($e->getMessage());
                }
            } else {
                error_log("Avatar güncellenemedi. Tarih: " . date("Y-m-d H:i:s"));
                $fonk->ajax_hata('Avatar Güncellenemedi. Bir hata oluştu!');
            }
        }

        // Kullanıcı bilgilerini güncelle
        try {
            $yguncelle = $db->prepare("UPDATE hesaplar SET adi=:adi, soyadi=:soyadi WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:id");
            $yguncelle->execute(['adi' => $adi, 'soyadi' => $soyadi, 'id' => $hesap->id]);

            if ($yguncelle) {
                $fonk->ajax_tamam("Hesap bilgileri güncellendi.");
            } else {
                $fonk->ajax_hata("Hesap bilgileri güncellenemiyor.");
            }
        } catch (PDOException $e) {
            error_log("Hesap bilgileri güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }
    }
}