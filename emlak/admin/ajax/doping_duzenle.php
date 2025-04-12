<?php 
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if (!$id) {
            error_log("Geçersiz ID", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Geçersiz ID"));
        }

        $stmt = $db->prepare("SELECT * FROM dopingler_group_501 WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        $durum = filter_var($_POST['durum'], FILTER_VALIDATE_INT);
        $odeme_yontemi = $gvn->html_temizle($_POST['odeme_yontemi']);
        $xtutar = filter_var($gvn->prakam($_POST['xtutar']), FILTER_SANITIZE_NUMBER_INT);
        $ids = $_POST['ids'];
        $sure = $_POST['sure'];
        $periyod = $_POST['periyod'];
        $tutar = $_POST['tutar'];
        $btarih = $_POST['btarih'];

        foreach ($ids as $d) {
            $dis = $db->prepare("SELECT id, sure, periyod, tarih, btarih FROM dopingler_501 WHERE id = ?");
            $dis->execute([$d]);

            if ($dis->rowCount() > 0) {
                $dis = $dis->fetch(PDO::FETCH_OBJ);
                $dsure = filter_var($sure[$d], FILTER_VALIDATE_INT);
                $dperiyod = $gvn->harf_rakam($periyod[$d]);
                $dtutar = filter_var($gvn->prakam($tutar[$d]), FILTER_SANITIZE_NUMBER_INT);
                $dbtarih = $gvn->html_temizle($btarih[$d]);
                $dbtarih = ($dbtarih == '') ? date("Y-m-d") . " 23:59:59" : date("Y-m-d", strtotime($dbtarih)) . " 23:59:59";

                if ($dbtarih == $dis->btarih && ($dsure != $dis->sure || $dperiyod != $dis->periyod)) {
                    $expiry = $dis->tarih . " +" . $dsure;
                    $expiry .= ($dperiyod == "gunluk") ? ' day' : '';
                    $expiry .= ($dperiyod == "aylik") ? ' month' : '';
                    $expiry .= ($dperiyod == "yillik") ? ' year' : '';
                    $dbtarih = date("Y-m-d", strtotime($expiry)) . " 23:59:59";
                }

                // Veritabanı güncelleme işlemi
                try {
                    $query = $db->prepare("UPDATE dopingler_501 SET sure = ?, periyod = ?, tutar = ?, btarih = ?, durum = ? WHERE id = ?");
                    $query->execute([$dsure, $dperiyod, $dtutar, $dbtarih, $durum, $d]);
                } catch (PDOException $e) {
                    // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
                    error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                    die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
                }
            }
        }

        if ($durum == 1 && $snc->durum != 1) {
            $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = " . $snc->acid)->fetch(PDO::FETCH_OBJ);
            $sayfay = $db->query("SELECT id, baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = " . $snc->ilan_id)->fetch(PDO::FETCH_OBJ);

            $adsoyad = $hesapp->adi;
            $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
            $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
            $baslik = $sayfay->baslik . " " . dil("PAY_NAME");
            $fiyat = $gvn->para_str($tutar) . " " . dil("DOPING_PBIRIMI");
            $neresi = "dopinglerim";

            $fonk->bildirim_gonder([
                $adsoyad,
                $hesapp->email,
                $hesapp->parola,
                $baslik,
                $fiyat,
                date("d.m.Y H:i", strtotime($fonk->datetime())),
                SITE_URL . $neresi
            ], "siparis_onaylandi", $hesapp->email, $hesapp->tel);
        }

        // Veritabanı güncelleme işlemi
        try {
            $query = $db->prepare("UPDATE dopingler_group_501 SET durum = ?, odeme_yontemi = ?, tutar = ? WHERE id = ?");
            $query->execute([$durum, $odeme_yontemi, $xtutar, $id]);
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }

        $fonk->ajax_tamam("Güncelleme başarıyla gerçekleşti.");
    }
}