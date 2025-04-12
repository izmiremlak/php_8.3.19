<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM upaketler_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz paket ID. Tarih: " . date("Y-m-d H:i:s"));
            die();
        }

        $adi = $gvn->html_temizle($_POST["adi"]);
        $durum = $gvn->zrakam($_POST["durum"]);
        $odeme_yontemi = $gvn->html_temizle($_POST["odeme_yontemi"]);
        $tutar = $gvn->prakam($_POST["tutar"]);
        $tutar = $gvn->para_int($tutar);
        $sure = $gvn->zrakam($_POST["sure"]);
        $periyod = $gvn->harf_rakam($_POST["periyod"]);
        $aylik_ilan_limit = $gvn->zrakam($_POST["aylik_ilan_limit"]);
        $ilan_resim_limit = $gvn->zrakam($_POST["ilan_resim_limit"]);
        $danisman_limit = $gvn->zrakam($_POST["danisman_limit"]);
        $ilan_yayin_sure = $gvn->zrakam($_POST["ilan_yayin_sure"]);
        $ilan_yayin_periyod = $gvn->harf_rakam($_POST["ilan_yayin_periyod"]);
        $danisman_onecikar = $gvn->zrakam($_POST["danisman_onecikar"]);
        $danisman_onecikar_sure = $gvn->zrakam($_POST["danisman_onecikar_sure"]);
        $danisman_onecikar_periyod = $gvn->harf_rakam($_POST["danisman_onecikar_periyod"]);
        $btarih = $gvn->html_temizle($_POST["btarih"]);
        $btarih = ($btarih == '') ? date("Y-m-d") . " 23:59:59" : date("Y-m-d", strtotime($btarih)) . " 23:59:59";

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($adi)) {
            error_log("Lütfen paket adı belirtiniz. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen paket adı belirtiniz."));
        }

        if ($btarih == $snc->btarih && ($sure != $snc->sure || $periyod != $snc->periyod)) {
            $expiry = $snc->tarih . " +" . $sure;
            $expiry .= ($periyod == "gunluk") ? ' day' : '';
            $expiry .= ($periyod == "aylik") ? ' month' : '';
            $expiry .= ($periyod == "yillik") ? ' year' : '';
            $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";
        }

        if ($durum == 1 && $snc->durum != 1) {
            $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);

            $adsoyad = $hesapp->adi;
            $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
            $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
            $baslik = $adi . " " . dil("PAY_NAME2");

            $fiyat = $gvn->para_str($tutar) . " " . dil("UYELIKP_PBIRIMI");
            $neresi = "paketlerim";

            $fonk->bildirim_gonder([$adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi], "siparis_onaylandi", $hesapp->email);
        }

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            // Paket güncelleme
            $query = $db->prepare("UPDATE upaketler_501 SET adi=?, durum=?, odeme_yontemi=?, tutar=?, sure=?, periyod=?, btarih=?, aylik_ilan_limit=?, ilan_resim_limit=?, ilan_yayin_sure=?, ilan_yayin_periyod=?, danisman_limit=?, danisman_onecikar=?, danisman_onecikar_sure=?, danisman_onecikar_periyod=? WHERE id=?");
            $query->execute([$adi, $durum, $odeme_yontemi, $tutar, $sure, $periyod, $btarih, $aylik_ilan_limit, $ilan_resim_limit, $ilan_yayin_sure, $ilan_yayin_periyod, $danisman_limit, $danisman_onecikar, $danisman_onecikar_sure, $danisman_onecikar_periyod, $id]);
        } catch (PDOException $e) {
            error_log("Paket güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }

        $fonk->ajax_tamam("Paket Başarıyla Güncellendi.");
        $fonk->yonlendir("index.php?p=upaketler", 1000);
    }
}