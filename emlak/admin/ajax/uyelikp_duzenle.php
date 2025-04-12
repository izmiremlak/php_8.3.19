<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM uyelik_paketleri_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz paket ID. Tarih: " . date("Y-m-d H:i:s"));
            die();
        }

        // Verileri güvenli bir şekilde al
        $baslik = htmlspecialchars($_POST["baslik"], ENT_QUOTES, 'UTF-8');
        $sira = $gvn->zrakam($_POST["sira"]);
        $renk = htmlspecialchars($_POST["renk"], ENT_QUOTES, 'UTF-8');
        $gizle = $gvn->zrakam($_POST["gizle"]);
        $aylik_ilan_limit = $gvn->zrakam($_POST["aylik_ilan_limit"]);
        $ilan_resim_limit = $gvn->zrakam($_POST["ilan_resim_limit"]);
        $danisman_limit = $gvn->zrakam($_POST["danisman_limit"]);
        $ilan_yayın_sure = $gvn->zrakam($_POST["ilan_yayın_sure"]);
        $ilan_yayın_periyod = $gvn->harf_rakam($_POST["ilan_yayın_periyod"]);
        $danisman_onecikar = $gvn->zrakam($_POST["danisman_onecikar"]);
        $danisman_onecikar_sure = $gvn->zrakam($_POST["danisman_onecikar_sure"]);
        $danisman_onecikar_periyod = $gvn->harf_rakam($_POST["danisman_onecikar_periyod"]);
        $sureler = $_POST["sure"];
        $periyodlar = $_POST["periyod"];
        $tutarlar = $_POST["tutar"];
        $ucretler = [];

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik)) {
            error_log("Lütfen başlık belirtiniz. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen başlık belirtiniz."));
        }

        if ($fonk->bosluk_kontrol($renk)) {
            error_log("Lütfen renk seçiniz. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_hata("Lütfen renk seçiniz."));
        }

        // Ücret bilgilerini al ve güvenli hale getir
        for ($i = 0; $i < count($sureler); $i++) {
            if ($periyodlar[$i] != '') {
                $sure = $gvn->zrakam($sureler[$i]);
                $periyodu = $gvn->harf_rakam($periyodlar[$i]);
                $tutar = $gvn->prakam($tutarlar[$i]);
                $tutar = $gvn->para_int($tutar);
                $ucretler[] = ['sure' => $sure, 'periyod' => $periyodu, 'tutar' => $tutar];
            }
        }
        $ucretler = $fonk->json_encode_tr($ucretler);

        // Veritabanı bağlantısını hata modunda ayarla
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Üyelik paketi bilgilerini güncelle
            $query = $db->prepare("UPDATE uyelik_paketleri_501 SET baslik=?, sira=?, renk=?, gizle=?, aylik_ilan_limit=?, ilan_resim_limit=?, ilan_yayın_sure=?, ilan_yayın_periyod=?, danisman_limit=?, danisman_onecikar=?, danisman_onecikar_sure=?, danisman_onecikar_periyod=?, ucretler=? WHERE id=?");
            $query->execute([$baslik, $sira, $renk, $gizle, $aylik_ilan_limit, $ilan_resim_limit, $ilan_yayın_sure, $ilan_yayın_periyod, $danisman_limit, $danisman_onecikar, $danisman_onecikar_sure, $danisman_onecikar_periyod, $ucretler, $id]);
        } catch (PDOException $e) {
            // Hataları log dosyasına kaydet ve sitede göster
            error_log("Üyelik paketi güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }

        // Başarı mesajı
        $fonk->ajax_tamam("Paket Başarıyla Güncellendi.");
        $fonk->yonlendir("index.php?p=uyelik_paketleri", 1000);
    }
}