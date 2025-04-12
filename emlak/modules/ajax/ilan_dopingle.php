<?php
// Hata yönetimi için ayarları yapılandır
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error_log.txt'); // Hata log dosyasının yolu
error_reporting(E_ALL); // Tüm hataları raporla

// Hataları sitede göster
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='color: red;'><b>Hata:</b> [$errno] $errstr - $errfile:$errline</div>";
    return true;
}
set_error_handler("customErrorHandler");

// Kullanıcı giriş kontrolü
if ($hesap->id == '') {
    die();
}

// Veritabanı hata modunu ayarla
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $gvn->rakam($_GET["id"]);
$from = $gvn->harf_rakam($_GET["from"]);
$doping = $_POST["doping"];

// İlan kontrolü
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi=4 AND id=?");
$kontrol->execute(array($id));
if ($kontrol->rowCount() < 1) {
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

// Hesap kontrolü
$acc = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . intval($snc->acid))->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($snc->acid != $hesap->id && $hesap->id != $kid) {
    die();
}

// Doping kontrolü
$cdoping = count($doping);
if ($doping == '' || $cdoping == 0) {
    die('<span class="error">' . dil("TX525") . '</span>');
}

// Zaman ayarlarını al
list($dzaman1a, $dzaman1b) = explode("|", $gayarlar->dzaman1);
list($dzaman2a, $dzaman2b) = explode("|", $gayarlar->dzaman2);
list($dzaman3a, $dzaman3b) = explode("|", $gayarlar->dzaman3);

$tpt = 0;
$customs = array();
$customs["satis"] = 'doping_ekle';
$customs["ilan_id"] = $id;
$customs["acid"] = $hesap->id;
foreach ($doping as $k => $v) {
    if (is_numeric($k) && is_numeric($v) && strlen($v) == 1 && $v > 0 && $v < 4) {
        $sga = $db->prepare("SELECT id, fiyat1, fiyat2, fiyat3, adi FROM doping_ayarlar_501 WHERE id=?");
        $sga->execute(array($k));
        if ($sga->rowCount() == 0) {
            die('<span class="error">Your select option is invalid.</span>');
        } else {
            $di = $sga->fetch(PDO::FETCH_ASSOC);

            // Eğer aynı dopingten varsa ve süresi bitmemişse
            $isdoping = $db->prepare("SELECT id FROM dopingler_501 WHERE ilan_id=? AND did=? AND btarih > NOW();");
            $isdoping->execute(array($snc->id, $k));
            if ($isdoping->rowCount() > 0) {
                die('<span class="error">"' . htmlspecialchars($di["adi"], ENT_QUOTES) . '" dopingi zaten ilanınızda aktif kullanılıyor.</span>');
            }

            $tutar = $di["fiyat" . $v];
            $tpt += $tutar;
            $customs["dopingler_501"][] = array(
                'did' => $k,
                'adi' => htmlspecialchars($di["adi"], ENT_QUOTES),
                'sure' => ${"dzaman" . $v . "a"},
                'periyod' => ${"dzaman" . $v . "b"},
                'tutar' => $tutar,
            );
        }
    }
}

if ($tpt > 0) {
    $customs["toplam_tutar"] = $tpt;
    $jncustoms = $fonk->json_encode_tr($customs);
    $becustoms = base64_encode($jncustoms);
    $_SESSION["custom"] = $becustoms;

    if ($from == "insert") {
        $fonk->yonlendir("ilan-olustur?id=" . $snc->id . "&asama=2&odeme=true", 1);
    } else {
        $fonk->yonlendir("uye-paneli?rd=ilan_duzenle&id=" . $snc->id . "&goto=doping&odeme=true", 1);
    }
} else { // Eğer toplam tutar sıfırsa
    $custom = $customs;
    $odeme_yontemi = "Yok";
    $tarih = $fonk->datetime();
    $durum = 1;
    $hesap_id = $hesap->id;

    try {
        $group = $db->prepare("INSERT INTO dopingler_group_501 SET acid=?, ilan_id=?, tutar=?, tarih=?, odeme_yontemi=?, durum=?");
        $group->execute(array($hesap_id, $custom["ilan_id"], $custom["toplam_tutar"], $tarih, $odeme_yontemi, $durum));
        $gid = $db->lastInsertId();
    } catch (PDOException $e) {
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
            $olustur->execute(array($hesap_id, $custom["ilan_id"], $dop["did"], $dop["tutar"], $dop["adi"], $dop["sure"], $dop["periyod"], $tarih, $btarih, $durum, $gid));
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    $fonk->yonlendir("aktif-ilanlar", 5000);
    ?>
    <script type="text/javascript">
    $(document).ready(function(){
        $(".ilanasamax").removeAttr("id");
        $(".islem_tamam").attr("id","asamaaktif");
    });
    $("#doping_ekle").hide(1,function(){
        $("#TamamDiv").show(1);
        ajaxHere('ajax.php?p=ilan_son_asama&id=<?= $snc->id; ?>','asama_result');
    });
    $('html, body').animate({scrollTop: 250}, 500);
    </script>
    <?php
} // Eğer toplam tutar sıfırsa end