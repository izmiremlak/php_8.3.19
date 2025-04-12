<?php
// Tema dizini tanımlı değilse çıkış yap
if (!defined("THEME_DIR")) {
    die();
}

// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Girdi doğrulama fonksiyonları
$how = $gvn->harf_rakam($_GET["how"]);
$on = $gvn->harf($_GET["on"]);

// Profil kontrolü
if (is_numeric($how)) {
    $kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND durum=0");
    $kontrol->execute([$how]);
} else {
    $kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND nick_adi=? AND durum=0");
    $kontrol->execute([$how]);
}

// Profil bulundu mu kontrolü
if ($kontrol->rowCount() > 0) {
    $profil = $kontrol->fetch(PDO::FETCH_OBJ);
} else {
    include "404.php";
    die();
}

// Profil bilgileri
$name = $profil->adi . " " . $profil->soyadi;
$avatar = ($profil->avatar == '' || $profil->avatard == 1) ? 'https://www.turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : 'https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/' . htmlspecialchars($profil->avatar, ENT_QUOTES, 'UTF-8');
$uturu = explode(",", dil("UYELIK_TURLERI"));

// Üye linki oluşturma
$uyelink = SITE_URL . "profil/";
$uyelink .= ($profil->nick_adi == '') ? $profil->id : htmlspecialchars($profil->nick_adi, ENT_QUOTES, 'UTF-8');

// Ad soyad bilgisi
$name = ($profil->unvan != '') ? htmlspecialchars($profil->unvan, ENT_QUOTES, 'UTF-8') : $profil->adi . " " . $profil->soyadi;

// Profil türüne göre işlem yapma
if ($profil->turu == 1) {
    $danismanlari = $db->query("SELECT id FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND turu=2 AND durum=0 AND kid=" . (int)$profil->id)->rowCount();
    $maps = htmlspecialchars($profil->maps, ENT_QUOTES, 'UTF-8');
    $adres = htmlspecialchars($profil->adres, ENT_QUOTES, 'UTF-8');

    if ($profil->il_id != 0) {
        $ili = $db->prepare("SELECT il_adi FROM il WHERE id=?");
        $ili->execute([(int)$profil->il_id]);
        if ($ili->rowCount() > 0) {
            $il_adi = $ili->fetch(PDO::FETCH_OBJ)->il_adi;
        }
    }

    if ($profil->ilce_id != 0) {
        $ilcei = $db->prepare("SELECT ilce_adi FROM ilce WHERE id=?");
        $ilcei->execute([(int)$profil->ilce_id]);
        if ($ilcei->rowCount() > 0) {
            $ilce_adi = $ilcei->fetch(PDO::FETCH_OBJ)->ilce_adi;
        }
    }

    if ($profil->mahalle_id != 0) {
        $mahallei = $db->prepare("SELECT mahalle_adi FROM mahalle_koy WHERE id=?");
        $mahallei->execute([(int)$profil->mahalle_id]);
        if ($mahallei->rowCount() > 0) {
            $mahalle_adi = $mahallei->fetch(PDO::FETCH_OBJ)->mahalle_adi;
        }
    }

} elseif ($profil->turu == 2) {
    $kurumsal = $db->prepare("SELECT id,maps,il_id,ilce_id,mahalle_id,adres,unvan,nick_adi FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
    $kurumsal->execute([(int)$profil->kid]);
    if ($kurumsal->rowCount() > 0) {
        $kurumsal = $kurumsal->fetch(PDO::FETCH_OBJ);

        $maps = htmlspecialchars($kurumsal->maps, ENT_QUOTES, 'UTF-8');
        $adres = htmlspecialchars($kurumsal->adres, ENT_QUOTES, 'UTF-8');

        if ($kurumsal->il_id != 0) {
            $ili = $db->prepare("SELECT il_adi FROM il WHERE id=?");
            $ili->execute([(int)$kurumsal->il_id]);
            if ($ili->rowCount() > 0) {
                $il_adi = $ili->fetch(PDO::FETCH_OBJ)->il_adi;
            }
        }

        if ($kurumsal->ilce_id != 0) {
            $ilcei = $db->prepare("SELECT ilce_adi FROM ilce WHERE id=?");
            $ilcei->execute([(int)$kurumsal->ilce_id]);
            if ($ilcei->rowCount() > 0) {
                $ilce_adi = $ilcei->fetch(PDO::FETCH_OBJ)->ilce_adi;
            }
        }

        if ($kurumsal->mahalle_id != 0) {
            $mahallei = $db->prepare("SELECT mahalle_adi FROM mahalle_koy WHERE id=?");
            $mahallei->execute([(int)$kurumsal->mahalle_id]);
            if ($mahallei->rowCount() > 0) {
                $mahalle_adi = $mahallei->fetch(PDO::FETCH_OBJ)->mahalle_adi;
            }
        }
    }
}

// Profil türüne göre dosya dahil etme
if ($profil->turu == 1 || $profil->turu == 2) {
    include THEME_DIR . "profil_kurumsal.php";
} else {
    include THEME_DIR . "profil_bireysel.php";
}