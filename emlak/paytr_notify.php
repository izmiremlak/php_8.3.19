<?php
// Gerekli fonksiyonları içe aktarır.
include "functions.php";

// POST verilerini alır.
$post = $_POST;
$merchant_key = MAGAZA_KEY;
$merchant_salt = MAGAZA_SALT;

// POST değerleri ile hash oluşturur.
$hash = base64_encode(hash_hmac('sha256', $post['merchant_oid'] . $merchant_salt . $post['status'] . $post['total_amount'], $merchant_key, true));

// Oluşturulan hash'i, paytr'dan gelen POST içindeki hash ile karşılaştırır (isteğin paytr'dan geldiğine ve değişmediğine emin olmak için).
if ($hash != $post['hash']) {
    error_log("PAYTR notification failed: bad hash", 3, "/var/log/php_errors.log");
    die('PAYTR notification failed: bad hash');
}

// Sipariş numarasını alır ve veritabanında sorgular.
$oid = $post['merchant_oid'];
$sorgula = $db->prepare("SELECT * FROM paytr_checks_501 WHERE oid=?");
$sorgula->execute([$oid]);

if ($sorgula->rowCount() < 1) {
    error_log("Sipariş numarası geçersiz.", 3, "/var/log/php_errors.log");
    die("Sipariş numarası geçersiz.");
}

$siparis = $sorgula->fetch(PDO::FETCH_OBJ);

// Sipariş durumu kontrolü.
if ($siparis->status != 'success') {
    if ($post['status'] == 'success') {
        $siparisdrm = $db->query("UPDATE paytr_checks_501 SET status='success' WHERE id=" . $siparis->id);
        $customs = strip_tags($siparis->custom);
    } else {
        $siparisdrm = $db->prepare("UPDATE paytr_checks_501 SET status='failed', failed_mesaj=? WHERE id=" . $siparis->id);
        $siparisdrm->execute([$post['failed_reason_msg']]);
        error_log("Sipariş başarısız döndü: " . $post['failed_reason_msg'], 3, "/var/log/php_errors.log");
        die("Sipariş başarısız döndü.");
    }
} else {
    die("OK");
}

// Sipariş başarılı ise "OK" döndürür.
echo "OK";

// Customs verisinin boş olup olmadığını kontrol eder.
if ($fonk->bosluk_kontrol($customs) == true) {
    error_log("ERR CSTMERR: Customs verisi boş.", 3, "/var/log/php_errors.log");
    die("ERR CSTMERR");
}

// Customs verisini çözümler.
$customs = json_decode(base64_decode($customs), true);
$custom = $customs;

// Satış türüne göre işlemler.
$satis = $custom['satis'];

if ($custom['acid'] != '') {
    $hesapp = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
    $hesapp->execute([$custom['acid']]);
    $hesapp = $hesapp->fetch(PDO::FETCH_OBJ);
}

if ($satis == "doping_ekle") {
    $kontrol = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND id=?");
    $kontrol->execute([$custom["ilan_id"]]);
    if ($kontrol->rowCount() < 1) {
        die();
    }
    $snc = $kontrol->fetch(PDO::FETCH_OBJ);

    $odeme_yontemi = "Kredi Kartı";
    $tarih = $fonk->datetime();
    $durum = 1;
    $hesap_id = $hesapp->id;

    $adsoyad = $hesapp->adi;
    $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
    $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
    $baslik = $snc->baslik . " " . dil("PAY_NAME");

    $fiyat = $gvn->para_str($custom["toplam_tutar"]) . " " . dil("DOPING_PBIRIMI");
    $neresi = "dopinglerim";

    $fonk->bildirim_gonder(
        [$adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi],
        "siparis_onaylandi",
        $hesapp->email,
        $hesapp->tel
    );

    try {
        $group = $db->prepare("INSERT INTO dopingler_group_501 SET acid=?, ilan_id=?, tutar=?, tarih=?, odeme_yontemi=?, durum=?");
        $group->execute([$hesap_id, $custom["ilan_id"], $custom["toplam_tutar"], $tarih, $odeme_yontemi, $durum]);
        $gid = $db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage(), 3, "/var/log/php_errors.log");
        die($e->getMessage());
    }

    $dopingler_501 = $custom["dopingler_501"];
    foreach ($dopingler_501 as $dop) {
        $expiry = "+" . $dop["sure"];
        $expiry .= ($dop["periyod"] == "gunluk") ? ' day' : '';
        $expiry .= ($dop["periyod"] == "aylik") ? ' month' : '';
        $expiry .= ($dop["periyod"] == "yillik") ? ' year' : '';
        $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";

        try {
            $olustur = $db->prepare("INSERT INTO dopingler_501 SET acid=?, ilan_id=?, did=?, tutar=?, adi=?, sure=?, periyod=?, tarih=?, btarih=?, durum=?, gid=?");
            $olustur->execute([$hesap_id, $custom["ilan_id"], $dop["did"], $dop["tutar"], $dop["adi"], $dop["sure"], $dop["periyod"], $tarih, $btarih, $durum, $gid]);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage(), 3, "/var/log/php_errors.log");
            die($e->getMessage());
        }
    }
} elseif ($satis == "uyelik_paketi") {
    $id = $custom["paket"];
    $periyodu = $custom["periyod"];

    if ($id == 0 || strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
        die("Çok fazla bekleme yaptınız.");
    }

    $sorgula = $db->prepare("SELECT * FROM uyelik_paketleri_501 WHERE id=?");
    $sorgula->execute([$id]);
    if ($sorgula->rowCount() < 1) {
        die();
    }

    $paket = $sorgula->fetch(PDO::FETCH_OBJ);
    $ucretler = json_decode($paket->ucretler, true);
    $secilen = $ucretler[$periyodu];

    if ($secilen["periyod"] == '') {
        die();
    }

    $odeme_yontemi = "Kredi Kartı";
    $tarih = $fonk->datetime();
    $durum = 1;
    $hesap_id = $hesapp->id;

    $adsoyad = $hesapp->adi;
    $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
    $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
    $baslik = $paket->baslik . " " . dil("PAY_NAME2");

    $fiyat = $gvn->para_str($secilen["tutar"]) . " " . dil("UYELIKP_PBIRIMI");
    $neresi = "paketlerim";

    $fonk->bildirim_gonder(
        [$adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi],
        "siparis_onaylandi",
        $hesapp->email,
        $hesapp->tel
    );

    $expiry = "+" . $secilen["sure"];
    $expiry .= ($secilen["periyod"] == "gunluk") ? ' day' : '';
    $expiry .= ($secilen["periyod"] == "aylik") ? ' month' : '';
    $expiry .= ($secilen["periyod"] == "yillik") ? ' year' : '';
    $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";

    try {
        $query = $db->prepare("INSERT INTO upaketler_501 SET acid=?, pid=?, adi=?, tutar=?, durum=?, odeme_yontemi=?, tarih=?, btarih=?, sure=?, periyod=?, aylik_ilan_limit=?, ilan_resim_limit=?");
        $query->execute([$hesap_id, $paket->id, $paket->baslik, $secilen["tutar"], $durum, $odeme_yontemi, $tarih, $btarih, $secilen["sure"], $secilen["periyod"], $paket->aylik_ilan_limit, $paket->ilan_resim_limit]);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage(), 3, "/var/log/php_errors.log");
        die($e->getMessage());
    }
} elseif ($satis == "danisman_onecikar") {
    $id = $custom["danisman"];
    $periyodu = $custom["periyod"];

    if ($id == 0 || strlen($periyodu) > 3 || strlen($periyodu) < 1 || !is_numeric($periyodu)) {
        die("Çok fazla bekleme yaptınız.");
    }

    $kontrol = $db->prepare("SELECT id, adi, soyadi, avatar, onecikar, onecikar_btarih FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
    $kontrol->execute([$id]);

    if ($kontrol->rowCount() == 0) {
        die();
    }

    $danisman = $kontrol->fetch(PDO::FETCH_OBJ);

    $ua = $fonk->UyelikAyarlar();
    $secilen = $ua["danisman_onecikar_ucretler"][$periyodu];

    if ($secilen["periyod"] == '') {
        die();
    }

    $odeme_yontemi = "Kredi Kartı";
    $tarih = $fonk->datetime();
    $durum = 1;
    $hesap_id = $hesapp->id;

    $adsoyad = $hesapp->adi;
    $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
    $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
    $baslik = $danisman->adsoyad . " " . dil("PAY_NAME3");

    $fiyat = $gvn->para_str($secilen["tutar"]) . " " . dil("DONECIKAR_PBIRIMI");
    $neresi = "eklenen-danismanlar";

    $fonk->bildirim_gonder(
        [$adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi],
        "siparis_onaylandi",
        $hesapp->email,
        $hesapp->tel
    );

    $expiry = "+" . $secilen["sure"];
    $expiry .= ($secilen["periyod"] == "gunluk") ? ' day' : '';
    $expiry .= ($secilen["periyod"] == "aylik") ? ' month' : '';
    $expiry .= ($secilen["periyod"] == "yillik") ? ' year' : '';
    $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";

    try {
        $query = $db->prepare("INSERT INTO onecikan_danismanlar_501 SET acid=?, did=?, durum=?, sure=?, periyod=?, tarih=?, btarih=?, odeme_yontemi=?, tutar=?");
        $query->execute([$hesap_id, $danisman->id, $durum, $secilen["sure"], $secilen["periyod"], $fonk->datetime(), $btarih, $odeme_yontemi, $secilen["tutar"]]);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage(), 3, "/var/log/php_errors.log");
        die($e->getMessage());
    }

    $daUpdate = $db->query("UPDATE hesaplar SET onecikar=1, onecikar_btarih='" . $btarih . "' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $danisman->id);
} else {
    error_log("Geçersiz bir CUSTOM geldi.", 3, "/var/log/php_errors.log");
    die("Geçersiz bir CUSTOM geldi.");
}