<?php
// Hata ayıklama modunu etkinleştir
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Eğer POST isteği yoksa işlem yapılmaz
if (!$_POST) {
    die();
}

// Kullanıcı hesap kontrolü
if ($hesap->id != "" && $hesap->tipi != 0) {

    // Güvenli veri işleme
    $id = $gvn->rakam($_GET["ilan_id"]);
    $from = $gvn->harf_rakam($_GET["from"]);

    // Veritabanından sayfa bilgilerini al
    $snc = $db->prepare("SELECT id, resim, ilan_no FROM sayfalar WHERE site_id_555=501 AND id=:ids");
    $snc->execute(['ids' => $id]);

    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ); // Veriyi nesne olarak al
    } else {
        die(); // Kayıt bulunamazsa işlem sonlandırılır
    }

    // Eğer "nestable" işlemi ise
    if ($from == "nestable") {
        foreach ($_POST['value'] as $key => $row) {
            $keys = $key + 1;
            $idi = $row['idi'];
            $sira = $keys;

            try {
                $updt = $db->prepare("UPDATE galeri_foto SET sira=? WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND sayfa_id=?");
                $updt->execute([$sira, $idi, $snc->id]);
            } catch (PDOException $e) {
                die($e->getMessage()); // Hata mesajını göster
            }
        }
        die();
    }

    // Kapak görseli güncelleme
    $kapak = $gvn->html_temizle($_POST["kapak"]);
    $siralar = $_POST["sira"];
    $cnt = count($siralar);

    if ($kapak != '' && $kapak != $snc->resim) {
        try {
            $gunc = $db->prepare("UPDATE sayfalar SET resim=? WHERE site_id_555=501 AND ilan_no=?");
            $gunc->execute([$kapak, $snc->ilan_no]);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

/*foreach($siralar as $id=>$sira){
$sira		= $gvn->rakam($sira);
$id			= $gvn->rakam($id);
$db->query("UPDATE galeri_foto SET sira='".$sira."' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$id);

}*/

    // Eğer işlem "insert" ise yönlendirme yapılır
    if ($from == "insert") {
        $fonk->yonlendir("index.php?p=ilan_ekle&id=" . $snc->id . "&asama=1", 1);
    } else {
        // Güncelleme işlemi başarılı mesajı ve yönlendirme
        $fonk->ajax_tamam("Galeri Güncellendi!");
        $fonk->yonlendir("index.php?p=ilan_duzenle&id=" . $snc->id . "&goto=photos#tab2", 1000);
    }
}