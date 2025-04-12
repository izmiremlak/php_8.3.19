<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM video_galeri WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz video galeri ID. Tarih: " . date("Y-m-d H:i:s"));
            die();
        }

        // Verileri güvenli bir şekilde al
        $baslik = htmlspecialchars($_POST["baslik"], ENT_QUOTES, 'UTF-8');
        $sira = $gvn->zrakam($_POST["sira"]);
        $kategori_id = $gvn->zrakam($_POST["kategori_id"]);
        $youtube = htmlspecialchars($_POST["youtube"], ENT_QUOTES, 'UTF-8');
        $kesyou = substr($youtube, 32, 100);
        $resim = 'http://i1.ytimg.com/vi/' . $kesyou . '/hqdefault.jpg';

        // Boşluk kontrolü
        if ($fonk->bosluk_kontrol($baslik) || $fonk->bosluk_kontrol($youtube)) {
            error_log("Lütfen tüm alanları eksiksiz doldurun. Tarih: " . date("Y-m-d H:i:s"));
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        if ($youtube != $snc->youtube) {
            ?><script type="text/javascript">
            $(document).ready(function () {
                $('#resim_src').attr("src", "<?=$resim;?>");
            });
            </script><?php
        }

        // Veritabanı bağlantısını hata modunda ayarla
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Video galeri bilgilerini güncelle
            $updt = $db->prepare("UPDATE video_galeri SET sira=?, baslik=?, youtube=?, resim=?, kategori_id=? WHERE id=?");
            $updt->execute([$sira, $baslik, $youtube, $resim, $kategori_id, $snc->id]);

            if ($updt) {
                $fonk->ajax_tamam("Video Galeri Güncellendi.");
            }
        } catch (PDOException $e) {
            // Hataları log dosyasına kaydet ve sitede göster
            error_log("Video galeri güncellenemedi: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
            die($e->getMessage());
        }
    }
}