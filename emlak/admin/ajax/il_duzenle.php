<?php
// POST isteği olup olmadığını kontrol et
if ($_POST) {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id != "" AND $hesap->tipi != 0) {
        // GET verisini güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM il WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        // Sonuçları kontrol et
        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // POST verilerini güvenli bir şekilde al ve temizle
        $il_adi = $gvn->html_temizle($_POST["il_adi"]);
        $anasayfa = $gvn->zrakam($_POST["anasayfa"]);

        // İl adı kontrolü
        if ($fonk->bosluk_kontrol($il_adi) == true) {
            die($fonk->ajax_hata("Lütfen il adı yazınız."));
        }

        // Resim yükleme işlemi
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];
        $resim2tmp = $_FILES['resim2']["tmp_name"];
        $resim2nm = $_FILES['resim2']["name"];

        if ($resim1tmp != "") {
            $randnm = $snc->slug . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['sehirler']['orjin_x'], $gorsel_boyutlari['sehirler']['orjin_y']);

            // Veritabanını güncelle
            try {
                $avgn = $db->prepare("UPDATE il SET resim=:image WHERE id=:id");
                $avgn->execute(['image' => $resim, 'id' => $snc->id]);
                $fonk->ajax_tamam('Resim Güncellendi');
                echo "<script type=\"text/javascript\">
                $(document).ready(function(){
                    $('#resim_src').attr(\"src\",\"../uploads/{$resim}\");
                });
                </script>";
            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                $fonk->ajax_hata("Resim güncellenemedi: " . htmlspecialchars($e->getMessage()));
            }
        }

        // İl bilgilerini güncelle
        try {
            $sql = $db->prepare("UPDATE il SET il_adi=:il_adi, anasayfa=:anasayfa WHERE id=:id");
            $sql->execute(['il_adi' => $il_adi, 'anasayfa' => $anasayfa, 'id' => $snc->id]);

            $fonk->ajax_tamam("Şehir Güncellendi.");
        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
        }
    }
}