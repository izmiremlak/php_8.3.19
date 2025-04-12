<?php
// POST verilerinin varlığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($hesap->id) && $hesap->tipi != 0) {

        // ID'yi al ve kontrol et
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM abloklar WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            die();
        }

        // Gelen verileri temizle ve işle
        $sira = $gvn->zrakam($_POST["sira"]);
        $icon = $_POST["icon"];
        $aciklama = $_POST["aciklama"];
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $url = $gvn->html_temizle($_POST["url"]);

        // Boş alan kontrolü
        if ($fonk->bosluk_kontrol($baslik) || $fonk->bosluk_kontrol($aciklama) || $fonk->bosluk_kontrol($sira)) {
            die($fonk->ajax_uyari("Lütfen tüm alanları eksiksiz doldurun."));
        }

        // Resim yükleme işlemi
        $resim1tmp = $_FILES['resim']["tmp_name"];
        $resim1nm = $_FILES['resim']["name"];

        if ($resim1tmp != "") {
            $randnm = strtolower(substr(md5(uniqid(rand())), 0,10)) . $fonk->uzanti($resim1nm);
            $resim = $fonk->resim_yukle(false, 'resim', $randnm, '../uploads', $gorsel_boyutlari['abloklar']['orjin_x'], $gorsel_boyutlari['abloklar']['orjin_y']);

            // Veritabanı güncelleme işlemi
            $avgn = $db->prepare("UPDATE abloklar SET resim=:image WHERE id=:id");
            $avgn->execute(['image' => $resim, 'id' => $snc->id]);
            if ($avgn) {
                $fonk->ajax_tamam('Resim Güncellendi');
                echo "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#resim_src').attr('src','../uploads/thumb/{$resim}');
                        });
                      </script>";
            }
        }

        // Diğer alanları güncelleme işlemi
        $dzn = $db->prepare("UPDATE abloklar SET sira=?, icon=?, baslik=?, aciklama=?, url=? WHERE id=?");
        $dzn->execute([$sira, $icon, $baslik, $aciklama, $url, $snc->id]);

        if ($dzn) {
            $fonk->ajax_tamam("Anasayfa Blok Güncellendi.");
        } else {
            $fonk->ajax_hata("Bir hata oluştu.");
        }
    }
}