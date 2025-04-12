<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM reklamlar_199 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz reklam ID: $id. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Geçersiz reklam ID.');
        }

        // POST verilerini güvenli bir şekilde al
        $baslik = $gvn->html_temizle($_POST["baslik"]);
        $tipi = $gvn->zrakam($_POST["tipi"]);
        $kodu = $_POST["kodu"];
        $mobil_kodu = $_POST["mobil_kodu"];
        $durum = $gvn->zrakam($_POST["durum"]);
        $suresiz = $gvn->zrakam($_POST["suresiz"]);
        $btarih = $gvn->html_temizle($_POST["btarih"]);
        $btarih = ($btarih == '') ? date("Y-m-d") : date("Y-m-d", strtotime($btarih)) . " 23:59:59";
        $tarih = $fonk->datetime();

        // Veritabanı güncelleme
        try {
            $query = $db->prepare("UPDATE reklamlar_199 SET baslik=?, tipi=?, kodu=?, mobil_kodu=?, durum=?, suresiz=?, btarih=? WHERE id=?");
            $query->execute([$baslik, $tipi, $kodu, $mobil_kodu, $durum, $suresiz, $btarih, $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die('Hata: ' . htmlspecialchars($e->getMessage()));
        }

        // Başarılı mesajı ve yönlendirme
        $fonk->ajax_tamam("Reklam güncellendi.");
        $fonk->yonlendir("index.php?p=reklamlar", 1000);
    }
}