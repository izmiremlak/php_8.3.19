<?php
// POST verilerinin kontrolü
if ($_POST) {
    // Kullanıcı kimliğini ve tipini kontrol et
    if ($hesap->id != "" && $hesap->tipi != 0) {
        // Verileri güvenli bir şekilde al
        $id = $gvn->rakam($_GET["id"]);
        $snc = $db->prepare("SELECT * FROM mail_sablonlar_501 WHERE id=:ids");
        $snc->execute(['ids' => $id]);

        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            error_log("Geçersiz şablon ID: $id. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Geçersiz şablon ID.');
        }

        // Verileri güvenli bir şekilde al
        $adi = $gvn->html_temizle($_POST["adi"]);
        $konu = $gvn->html_temizle($_POST["konu"]);
        $konu2 = $gvn->html_temizle($_POST["konu2"]);
        $degiskenler = $gvn->html_temizle($_POST["degiskenler"]);
        $yemails = $gvn->html_temizle($_POST["yemails"]);
        $yphones = $gvn->html_temizle($_POST["yphones"]);
        $icerik = $_POST["icerik"];
        $icerik2 = $_POST["icerik2"];
        $icerik3 = $_POST["icerik3"];
        $icerik4 = $_POST["icerik4"];
        $ubildirim = $gvn->zrakam($_POST["ubildirim"]);
        $abildirim = $gvn->zrakam($_POST["abildirim"]);
        $sbildirim = $gvn->zrakam($_POST["sbildirim"]);
        $ysbildirim = $gvn->zrakam($_POST["ysbildirim"]);

        // Şablon güncelleme sorgusu
        $updt = $db->prepare("UPDATE mail_sablonlar_501 SET adi=?, konu=?, konu2=?, icerik=?, icerik2=?, icerik3=?, icerik4=?, ubildirim=?, abildirim=?, sbildirim=?, ysbildirim=?, degiskenler=?, yemails=?, yphones=? WHERE id=?");
        $updt->execute([$adi, $konu, $konu2, $icerik, $icerik2, $icerik3, $icerik4, $ubildirim, $abildirim, $sbildirim, $ysbildirim, $degiskenler, $yemails, $yphones, $id]);

        if ($updt) {
            $fonk->ajax_tamam("İşlem Tamamlandı.");
        } else {
            error_log("Şablon güncellenemedi. Şablon ID: $id. Tarih: " . date("Y-m-d H:i:s"));
            die('Hata: Şablon güncellenemedi.');
        }
    }
}