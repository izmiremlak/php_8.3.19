<?php
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

        $stmt = $db->prepare("SELECT * FROM diller_501 WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $snc = $stmt->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        $adi = $gvn->html_temizle($_POST['adi']);
        $kisa_adi = $gvn->html_temizle($_POST['kisa_adi']);
        $gosterim_adi = $gvn->html_temizle($_POST['gosterim_adi']);
        $sira = filter_var($_POST['sira'], FILTER_VALIDATE_INT);
        $degiskenler = stripslashes($_POST['degiskenler']);
        $durum = filter_var($_POST['durum'], FILTER_VALIDATE_INT);

        if (empty($adi)) {
            error_log("Lütfen dil adını boş yazmayınız.", 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Lütfen dil adını boş yazmayınız."));
        }

        $resim1tmp = $_FILES['resim']['tmp_name'];
        $resim1nm = $_FILES['resim']['name'];

        if (!empty($resim1tmp)) {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(true, 'resim', $randnm, '../uploads', false, false);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', false, false);

            // Veritabanı güncelleme işlemi
            try {
                $stmt = $db->prepare("UPDATE diller_501 SET resim = :resim WHERE id = :id");
                $stmt->execute(['resim' => $resim, 'id' => $snc->id]);

                if ($stmt) {
                    $fonk->ajax_tamam('Simge Güncellendi');
                    ?>
                    <script type="text/javascript">
                    $(document).ready(function(){
                        $('#resim_src').attr("src", "../uploads/thumb/<?=$resim;?>");
                    });
                    </script>
                    <?php
                }
            } catch (PDOException $e) {
                // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
                error_log($e->getMessage(), 3, '/var/log/php_errors.log');
                die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
            }
        }

        file_put_contents("../".THEME_DIR."diller/".$snc->kisa_adi.".txt", $degiskenler);

        // Veritabanı güncelleme işlemi
        try {
            $stmt = $db->prepare("UPDATE diller_501 SET 
                adi = :adi,
                gosterim_adi = :gosterim_adi,
                sira = :sira,
                durum = :durum
                WHERE id = :id
            ");
            $stmt->execute([
                'adi' => $adi,
                'gosterim_adi' => $gosterim_adi,
                'sira' => $sira,
                'durum' => $durum,
                'id' => $snc->id
            ]);

            if ($stmt) {
                $fonk->ajax_tamam("Dil Güncellendi.");
            } else {
                $fonk->ajax_hata("Bir hata oluştu.");
            }
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}