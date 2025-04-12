<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id !== "" && $hesap->tipi !== 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Girişleri temizle ve doğrula
        $adi = $gvn->html_temizle($_POST['adi']);
        $kisa_adi = strtolower($gvn->html_temizle($_POST['kisa_adi']));
        $gosterim_adi = $gvn->html_temizle($_POST['gosterim_adi']);
        $sira = filter_var($_POST['sira'], FILTER_VALIDATE_INT);
        $degiskenler = stripslashes($_POST['degiskenler']);
        $durum = filter_var($_POST['durum'], FILTER_VALIDATE_INT);
        $kopyala = filter_var($_POST['kopyala'], FILTER_VALIDATE_INT);

        // Dilin daha önce eklenip eklenmediğini kontrol et
        $kontrolet = $db->prepare("SELECT id FROM diller_501 WHERE adi = :adi OR kisa_adi = :kisa_adi");
        $kontrolet->execute(['adi' => $adi, 'kisa_adi' => $kisa_adi]);

        if ($kontrolet->rowCount() > 0) {
            error_log("Böyle bir dil zaten mevcut!", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Böyle bir dil zaten mevcut!"));
        }

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($adi) || $fonk->bosluk_kontrol($kisa_adi) || $fonk->bosluk_kontrol($degiskenler)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Resim yükleme işlemi
        $resim1tmp = $_FILES['resim']['tmp_name'];
        $resim1nm = $_FILES['resim']['name'];
        $resim = '';

        if (!empty($resim1tmp)) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', false, false);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', false, false);
        }

        // Veritabanı güncelleme işlemi
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO diller_501 (adi, kisa_adi, resim, gosterim_adi, sira, durum) VALUES (:adi, :kisa_adi, :resim, :gosterim_adi, :sira, :durum)");
            $stmt->execute([
                'adi' => $adi,
                'kisa_adi' => $kisa_adi,
                'resim' => $resim,
                'gosterim_adi' => $gosterim_adi,
                'sira' => $sira,
                'durum' => $durum
            ]);

            // Dosya oluşturma ve içerik yazma
            $filePath = "../" . THEME_DIR . "diller/" . $kisa_adi . ".txt";
            touch($filePath);
            file_put_contents($filePath, $degiskenler);

            // Çerez ayarlama
            setcookie("dil", $kisa_adi, time() + 60 * 60 * 24 * 30);

            // Kopyalama işlemleri
            if ($kopyala == 1) {
                $dili = $db->query("SELECT kisa_adi FROM diller_501 WHERE kisa_adi = '$dil' ORDER BY id DESC")->fetch(PDO::FETCH_OBJ)->kisa_adi;

                $fonk->dil_aktar("ayarlar", $dili, $kisa_adi);
                $fonk->dil_aktar("mail_sablonlar_501 SET", $dili, $kisa_adi);
                $fonk->dil_aktar("kategoriler", $dili, $kisa_adi);
                $fonk->dil_aktar("sayfalar", $dili, $kisa_adi);
                $fonk->dil_aktar("menuler", $dili, $kisa_adi);
                $fonk->dil_aktar("galeri_foto", $dili, $kisa_adi);
                $fonk->dil_aktar("slider", $dili, $kisa_adi);
                $fonk->dil_aktar("subeler_bayiler", $dili, $kisa_adi);
                $fonk->dil_aktar("referanslar", $dili, $kisa_adi);
                $fonk->dil_aktar("sehirler", $dili, $kisa_adi);
            }

            $db->commit();

            $fonk->ajax_tamam("Dil eklendi.");
            $fonk->yonlendir("index.php", 1500);

        } catch (PDOException $e) {
            $db->rollBack();
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}