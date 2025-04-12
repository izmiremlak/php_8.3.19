<?php
// Kullanıcının giriş yapıp yapmadığını kontrol et
if ($hesap->id == '') {
    die();
}

// Veritabanı hata ayıklama modunu etkinleştir
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// GET ve POST verilerini güvenli bir şekilde al
$id = $gvn->rakam($_GET["id"]);
$from = $gvn->harf_rakam($_GET["from"]);
$doping = $_POST["doping"];

// İlanın varlığını kontrol et
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi=4 AND id=?");
$kontrol->execute([$id]);
if ($kontrol->rowCount() < 1) {
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

// Doping seçiminin geçerli olup olmadığını kontrol et
$cdoping = count($doping);
if ($doping == '' || $cdoping == 0) {
    die('<span class="error">' . dil("TX525") . '</span>');
}

// Doping zamanları belirle
list($dzaman1a, $dzaman1b) = explode("|", $gayarlar->dzaman1);
list($dzaman2a, $dzaman2b) = explode("|", $gayarlar->dzaman2);
list($dzaman3a, $dzaman3b) = explode("|", $gayarlar->dzaman3);
list($dzaman4a, $dzaman4b) = array("100", "yillik");

// Doping işlemleri için hazırlık
$customs = [
    "ilan_id" => $id
];
foreach ($doping as $k => $v) {
    if (is_numeric($k) && is_numeric($v) && strlen($v) == 1 && $v > 0 && $v < 5) {
        $sga = $db->prepare("SELECT id, fiyat1, fiyat2, fiyat3, adi FROM doping_ayarlar_501 WHERE id=?");
        $sga->execute([$k]);
        if ($sga->rowCount() == 0) {
            die('<span class="error">Your select option is invalid.</span>');
        } else {
            $di = $sga->fetch(PDO::FETCH_ASSOC);

            // Aynı dopingten varsa ve süresi bitmemişse kontrol et
            $isdoping = $db->prepare("SELECT id FROM dopingler_501 WHERE ilan_id=? AND did=? AND btarih > NOW();");
            $isdoping->execute([$snc->id, $k]);
            if ($isdoping->rowCount() > 0) {
                die('<span class="error">"' . htmlspecialchars($di["adi"], ENT_QUOTES, 'UTF-8') . '" dopingi zaten ilanınızda aktif kullanılıyor.</span>');
            }

            $customs["dopingler_501"][] = [
                'did' => $k,
                'adi' => htmlspecialchars($di["adi"], ENT_QUOTES, 'UTF-8'),
                'sure' => ${"dzaman" . $v . "a"},
                'periyod' => ${"dzaman" . $v . "b"}
            ];
        }
    }
}

// Doping ekleme işlemleri
$custom = $customs;
$odeme_yontemi = "Yok";
$tarih = $fonk->datetime();
$durum = 1;
$hesap_id = $hesap->id;

try {
    $group = $db->prepare("INSERT INTO dopingler_group_501 SET acid=?, ilan_id=?, tutar=?, tarih=?, odeme_yontemi=?, durum=?");
    $group->execute([$hesap_id, $custom["ilan_id"], $custom["toplam_tutar"], $tarih, $odeme_yontemi, $durum]);
    $gid = $db->lastInsertId();
} catch (PDOException $e) {
    error_log($e->getMessage(), 3, '/var/log/php_errors.log');
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
        $olustur->execute([$hesap_id, $custom["ilan_id"], $dop["did"], 0, $dop["adi"], $dop["sure"], $dop["periyod"], $tarih, $btarih, $durum, $gid]);
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, '/var/log/php_errors.log');
        die($e->getMessage());
    }
}

// Yönlendirme işlemleri
if ($from == "insert") {
    $fonk->yonlendir("index.php?p=ilanlar", 5000);
    echo "<script type=\"text/javascript\">
    $(document).ready(function(){
        $('.ilanasamax').removeAttr('id');
        $('.islem_tamam').attr('id', 'asamaaktif');
    });
    $('#doping_ekle').hide(1, function(){
        $('#TamamDiv').show(1);
    });
    $('html, body').animate({scrollTop: 0}, 500);
    </script>";
} else {
    $fonk->yonlendir("index.php?p=ilan_duzenle&id=" . $id . "&goto=doping#tab4", 1000);
    $fonk->ajax_tamam("Doping Ayarlandı.");
}