<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id !== "" && $hesap->tipi !== 0) {

        // Girişleri temizle ve doğrula
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            error_log("Geçersiz ID.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Geçersiz ID."));
        }

        $stmt = $db->prepare("SELECT * FROM danismanlar_501 WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // Girişleri temizle ve doğrula
        $adsoyad = $gvn->html_temizle($_POST['adsoyad']);
        $gsm = $gvn->html_temizle($_POST['gsm']);
        $telefon = $gvn->html_temizle($_POST['telefon']);
        $email = $gvn->html_temizle($_POST['email']);

        if (empty($adsoyad) || empty($gsm) || empty($telefon) || empty($email)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];

        if (!empty($resim1tmp)) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads', $gorsel_boyutlari['danismanlar']['thumb_x'], $gorsel_boyutlari['danismanlar']['thumb_y']);

            try {
                $stmt = $db->prepare("UPDATE danismanlar_501 SET resim = :image WHERE id = :id");
                $stmt->execute(['image' => $resim, 'id' => $snc->id]);

                $fonk->ajax_tamam('Resim Güncellendi');

                ?><script type="text/javascript">
                $(document).ready(function(){
                    $('#resim_src').attr("src", "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$resim;?>");
                });
                </script><?php

            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
            }
        }

        try {
            $stmt = $db->prepare("UPDATE danismanlar_501 SET adsoyad = :adsoyad, gsm = :gsm, telefon = :telefon, email = :email WHERE id = :id");
            $stmt->execute([
                'adsoyad' => $adsoyad,
                'gsm' => $gsm,
                'telefon' => $telefon,
                'email' => $email,
                'id' => $snc->id
            ]);

            $fonk->ajax_tamam("Başarıyla Güncellendi.");

        } catch (PDOException $e) {
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}