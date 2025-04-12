<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Üyelik ayarlarını al
        $ua = $fonk->UyelikAyarlar();

        // Verileri güvenli bir şekilde al
        $sureler = $_POST["sure"];
        $periyodlar = $_POST["periyod"];
        $tutarlar = $_POST["tutar"];
        $ucretler = [];

        // Ücretleri düzenle
        for ($i = 0; $i < count($sureler); $i++) {
            if ($periyodlar[$i] != '') {
                $sure = $gvn->zrakam($sureler[$i]);
                $periyodu = $gvn->harf_rakam($periyodlar[$i]);
                $tutar = $gvn->prakam($tutarlar[$i]);
                $tutar = $gvn->para_int($tutar);
                $ucretler[] = ['sure' => $sure, 'periyod' => $periyodu, 'tutar' => $tutar];
            }
        }
        $ua["danisman_onecikar_ucretler"] = $ucretler;

        // JSON encode
        $jso = $fonk->json_encode_tr($ua);

        // Veritabanı güncelleme
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $gunc = $db->prepare("UPDATE gayarlar_501 SET uyelik_ayarlar=?");
            $gunc->execute([$jso]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die($e->getMessage());
        }

        $fonk->ajax_tamam("Ayarlar Güncellendi.");
    }
}