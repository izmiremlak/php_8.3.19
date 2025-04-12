<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $tipi = $gvn->zrakam($_POST["tipi"]);
        $kodu = $_POST["kodu"];
        $mobil_kodu = $_POST["mobil_kodu"];
        $durum = $gvn->zrakam($_POST["durum"]);
        $suresiz = $gvn->zrakam($_POST["suresiz"]);
        $btarih = $gvn->html_temizle($_POST["btarih"]);
        $btarih = ($btarih == '') ? date("Y-m-d") : date("Y-m-d", strtotime($btarih)) . " 23:59:59";
        $tarih = $fonk->datetime();

        // Veritabanına ekleme
        try {
            $query = $db->prepare("INSERT INTO reklamlar_199 SET baslik=?, tipi=?, kodu=?, mobil_kodu=?, durum=?, suresiz=?, btarih=?, tarih=?");
            $query->execute([$baslik, $tipi, $kodu, $mobil_kodu, $durum, $suresiz, $btarih, $tarih]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die('Hata: ' . htmlspecialchars($e->getMessage()));
        }

        // Başarılı mesajı ve yönlendirme
        $fonk->ajax_tamam("Reklam eklendi.");
        $fonk->yonlendir("index.php?p=reklamlar", 1000);
    }
}