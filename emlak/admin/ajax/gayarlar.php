<?php

$azj3mfs9q7p = "267f8098c2b1f0d7902eee4e7288a0f0";

// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($_POST && $hesap->id != "" && $hesap->tipi != 0) {
    // Dosya yükleme işlemleri
    $resim1tmp = $_FILES["logo"]["tmp_name"];
    $resim1nm = $_FILES["logo"]["name"];
    $resim2tmp = $_FILES["footer_logo"]["tmp_name"];
    $resim2nm = $_FILES["footer_logo"]["name"];
    $favtmp = $_FILES["ficon"]["tmp_name"];
    $favnm = $_FILES["ficon"]["name"];
    $fav_error = $_FILES["ficon"]["error"];
    $wattmp = $_FILES["watermark"]["tmp_name"];
    $watnm = $_FILES["watermark"]["name"];
    $wat_error = $_FILES["watermark"]["error"];

    // POST verilerini güvenli bir şekilde al ve temizle
    $default_dil = $gvn->html_temizle($_POST["default_dil"]);
    $permalink = $gvn->html_temizle($_POST["permalink"]);
    $renk1 = $gvn->html_temizle($_POST["renk1"]);
    $renk2 = $gvn->html_temizle($_POST["renk2"]);
    $urun_siparis = $gvn->zrakam($_POST["urun_siparis"]);
    $urun_brosur_link = $gvn->html_temizle($_POST["urun_brosur_link"]);
    $hizmetler_sidebar = $gvn->zrakam($_POST["hizmetler_sidebar"]);
    $urunler_sidebar = $gvn->zrakam($_POST["urunler_sidebar"]);
    $sayfa_sidebar = $gvn->zrakam($_POST["sayfa_sidebar"]);
    $haberler_sidebar = $gvn->zrakam($_POST["haberler_sidebar"]);
    $blog_sidebar = $gvn->zrakam($_POST["blog_sidebar"]);
    $projeler_sidebar = $gvn->zrakam($_POST["projeler_sidebar"]);
    $stok = $gvn->zrakam($_POST["stok"]);
    $uyelik = $gvn->zrakam($_POST["uyelik"]);
    $tcnod = $gvn->zrakam($_POST["tcnod"]);
    $adresd = $gvn->zrakam($_POST["adresd"]);
    $sms_aktivasyon = $gvn->zrakam($_POST["sms_aktivasyon"]);
    $dopingler_501 = $gvn->zrakam($_POST["dopingler_501"]);
    $anlik_sohbet = $gvn->zrakam($_POST["anlik_sohbet"]);
    $reklamlar = $gvn->zrakam($_POST["reklamlar"]);
    $site_ssl = $gvn->zrakam($_POST["site_ssl"]);
    $site_www = $gvn->zrakam($_POST["site_www"]);
    $doviz = $gvn->zrakam($_POST["doviz"]);
    $kredih = $gvn->zrakam($_POST["kredih"]);
    $yemails = $gvn->html_temizle($_POST["yemails"]);
    $yphones = $gvn->html_temizle($_POST["yphones"]);
    $blok1 = $gvn->zrakam($_POST["blok1"]);
    $blok2 = $gvn->zrakam($_POST["blok2"]);
    $blok3 = $gvn->zrakam($_POST["blok3"]);
    $blok4 = $gvn->zrakam($_POST["blok4"]);
    $blok5 = $gvn->zrakam($_POST["blok5"]);
    $blok6 = $gvn->zrakam($_POST["blok6"]);
    $blok7 = $gvn->zrakam($_POST["blok7"]);
    $blok8 = $gvn->zrakam($_POST["blok8"]);
    $blok9 = $gvn->zrakam($_POST["blok9"]);

    // Dil kontrolü
    if ($fonk->bosluk_kontrol($default_dil) == true) {
        exit($fonk->ajax_uyari("Lütfen Dil Seçin!"));
    }

    // Güvenlik kontrolü
    if ($xsd006 != "527cbb94538f6768970d0fc630ee0acf") {
        exit;
    }

    // Logo yükleme işlemi
    if ($resim1tmp != "") {
        $randnm = strtolower($resim1nm);
        $logo = $fonk->resim_yukle(true, "logo", $randnm, "../uploads", $gorsel_boyutlari["logo"]["thumb_x"], $gorsel_boyutlari["logo"]["thumb_y"], false);
        $logo = $fonk->resim_yukle(false, "logo", $randnm, "../uploads", $gorsel_boyutlari["logo"]["orjin_x"], $gorsel_boyutlari["logo"]["orjin_y"], false);
        if ($logo) {
            try {
                $avgn = $db->prepare("UPDATE gayarlar_501 SET logo=:logos");
                $avgn->execute(["logos" => $logo]);
                $fonk->ajax_tamam("Logo Resimi Güncellendi");
                echo "<script type=\"text/javascript\">
                \$(document).ready(function(){
                    \$('#logo_src').attr(\"src\",\"../uploads/thumb/" . $logo . "\");
                });
                </script>";
            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                $fonk->ajax_hata("Logo Güncellenemedi. Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
            }
        } else {
            $fonk->ajax_hata("Logo Güncellenemedi. Bir hata oluştu!");
        }
    }

    // Footer logo yükleme işlemi
    if ($resim2tmp != "") {
        $randnm = strtolower($resim2nm);
        $footer_logo = $fonk->resim_yukle(true, "footer_logo", $randnm, "../uploads", $gorsel_boyutlari["logo"]["thumb_x"], $gorsel_boyutlari["logo"]["thumb_y"], false);
        $footer_logo = $fonk->resim_yukle(false, "footer_logo", $randnm, "../uploads", $gorsel_boyutlari["logo"]["orjin_x"], $gorsel_boyutlari["logo"]["orjin_y"], false);
        if ($footer_logo) {
            try {
                $avgn = $db->prepare("UPDATE gayarlar_501 SET footer_logo=?");
                $avgn->execute([$footer_logo]);
                $fonk->ajax_tamam("Footer Logo Resimi Güncellendi");
                echo "<script type=\"text/javascript\">
                \$(document).ready(function(){
                    \$('#footer_logo_src').attr(\"src\",\"../uploads/thumb/" . $footer_logo . "\");
                });
                </script>";
            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                $fonk->ajax_hata("Footer Logo Güncellenemedi. Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
            }
        } else {
            $fonk->ajax_hata("Footer Logo Güncellenemedi. Bir hata oluştu!");
        }
    }

    // Favicon yükleme işlemi
    if ($favtmp != "") {
        $randnm = "favicon.ico";
        $ficon = @move_uploaded_file($favtmp, "../" . $randnm);
        if ($ficon) {
            $fonk->ajax_tamam("Favicon Resimi Güncellendi");
            echo "<script type=\"text/javascript\">
            \$(document).ready(function(){
                \$('#ficon_src').attr(\"src\",\"../" . $randnm . "?time=" . time() . "\");
            });
            </script>";
        } else {
            $fonk->ajax_hata("Favicon Güncellenemedi. Bir hata oluştu!");
        }
    }

    // Watermark yükleme işlemi
    if ($wattmp != "") {
        $randnm = "watermark.png";
        $watermark = @move_uploaded_file($wattmp, "../" . $randnm);
        if ($watermark) {
            $fonk->ajax_tamam("Watermark Resimi Güncellendi");
            echo "<script type=\"text/javascript\">
            \$(document).ready(function(){
                \$('#watermark_src').attr(\"src\",\"../" . $randnm . "?time=" . time() . "\");
            });
            </script>";
        } else {
            $fonk->ajax_hata("Watermark Güncellenemedi. Bir hata oluştu!");
        }
    }

    // Genel ayarları güncelleme
    try {
        $yeni = $db->prepare("UPDATE ayarlar_501 SET blok1=?,blok2=?,blok3=?,blok4=?,blok5=?,blok6=?,blok7=?,blok8=?,blok9=?,yemails=?,yphones=? WHERE dil=?");
        $yeni->execute([$blok1, $blok2, $blok3, $blok4, $blok5, $blok6, $blok7, $blok8, $blok9, $yemails, $yphones, $dil]);

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $prmup = $db->prepare("UPDATE ayarlar_501 SET permalink=? WHERE dil=?");
        $prmup->execute([$permalink, $dil]);

        $guncelle = $db->prepare("UPDATE gayarlar_501 SET default_dil=?, renk1=?, renk2=?, urun_siparis=?, hizmetler_sidebar=?, urunler_sidebar=?, sayfa_sidebar=?, haberler_sidebar=?, blog_sidebar=?, projeler_sidebar=?, stok=?, uyelik=?, tcnod=?, adresd=?, sms_aktivasyon=?, dopingler_501=?, anlik_sohbet=?, reklamlar=?, site_ssl=?, site_www=?, doviz=?, kredih=? WHERE site_id_555=000");
        $guncelle->execute([$default_dil, $renk1, $renk2, $urun_siparis, $hizmetler_sidebar, $urunler_sidebar, $sayfa_sidebar, $haberler_sidebar, $blog_sidebar, $projeler_sidebar, $stok, $uyelik, $tcnod, $adresd, $sms_aktivasyon, $dopingler_501, $anlik_sohbet, $reklamlar, $site_ssl, $site_www, $doviz, $kredih]);

        $fonk->ajax_tamam("Genel ayarlar güncellendi.");
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, '/var/log/php_errors.log');
        exit("Hata : " . $e->getMessage());
    }
}

// Rastgele string oluşturma fonksiyonu
function aHtwd2xWtgS(string $first, string $second): string {
    $phrase = ucfirst($first) . " " . ucfirst($second) . " WeDkv6Sf60btGZikJIqG";
    return $phrase;
}