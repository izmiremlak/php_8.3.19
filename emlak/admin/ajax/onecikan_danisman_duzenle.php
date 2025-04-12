<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM onecikan_danismanlar_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz danışman ID: $id. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Geçersiz danışman ID.');
        }

        // Verileri güvenli bir şekilde al
        $durum = $gvn->zrakam($_POST["durum"]);
        $odeme_yontemi = $gvn->html_temizle($_POST["odeme_yontemi"]);
        $tutar = $gvn->prakam($_POST["tutar"]);
        $tutar = $gvn->para_int($tutar);
        $sure = $gvn->zrakam($_POST["sure"]);
        $periyod = $gvn->harf_rakam($_POST["periyod"]);
        $btarih = $gvn->html_temizle($_POST["btarih"]);
        $btarih = ($btarih == '') ? date("Y-m-d") . " 23:59:59" : date("Y-m-d", strtotime($btarih)) . " 23:59:59";

        // Tarih güncelleme
        if ($btarih == $snc->btarih && ($sure != $snc->sure || $periyod != $snc->periyod)) {
            $expiry = $snc->tarih . " +" . $sure;
            $expiry .= ($periyod == "gunluk") ? ' day' : '';
            $expiry .= ($periyod == "aylik") ? ' month' : '';
            $expiry += ($periyod == "yillik") ? ' year' : '';
            $btarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";
        }

        // Durum güncelleme
        if ($durum == 1 && $snc->durum != 1) {
            $db->query("UPDATE hesaplar SET onecikar='1', onecikar_btarih='" . $btarih . "' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->did);
        } elseif ($durum == 2 && $snc->durum != 2) {
            $db->query("UPDATE hesaplar SET onecikar='0', onecikar_btarih='' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->did);
        }

        if ($durum == 1 && $snc->durum != 1) {
            $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
            $danisman = $db->query("SELECT id, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->did)->fetch(PDO::FETCH_OBJ);

            $adsoyad = $hesapp->adi;
            $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
            $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
            $baslik = $danisman->adsoyad . " " . dil("PAY_NAME3");

            $fiyat = $gvn->para_str($tutar) . " " . dil("DONECIKAR_PBIRIMI");
            $neresi = "eklenen-danismanlar";

            $fonk->bildirim_gonder([$adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi], "siparis_onaylandi", $hesapp->bildirim);
        }

        // Veritabanı güncelleme
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $query = $db->prepare("UPDATE onecikan_danismanlar_501 SET durum=?, odeme_yontemi=?, tutar=?, sure=?, periyod=?, btarih=? WHERE id=?");
            $query->execute([$durum, $odeme_yontemi, $tutar, $sure, $periyod, $btarih, $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die($e->getMessage());
        }

        $fonk->ajax_tamam("Sipariş Başarıyla Güncellendi.");
    }
}