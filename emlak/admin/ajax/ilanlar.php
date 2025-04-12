<?php
if ($hesap->id != "" && $hesap->tipi != 0) {

    // Güvenli bir şekilde 'id' parametresini al
    $id = $gvn->rakam($_GET["id"]);

    // Veritabanından ilanı çek
    $snc = $db->prepare("SELECT id, video, ilan_no FROM sayfalar WHERE (site_id_555=501 OR (site_id_888=100 AND durum=1 AND il_id=35) OR (site_id_777=501501 AND durum=1) OR (site_id_699=200 AND durum=1 AND il_id=35) OR (site_id_701=501501 AND durum=1) OR (site_id_702=300 AND durum=1)) AND tipi=4");
    $snc->execute(['ids' => $id]);

    // İlan bulunamazsa işlemi sonlandır
    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ);
    } else {
        die();
    }

    // Aynı ilan numarasına sahip çoklu kayıtları bul
    $multi = $db->query("SELECT id, ilan_no FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . $snc->ilan_no . " ORDER BY id ASC");
    $multif = $multi->fetch(PDO::FETCH_OBJ);
    $multidids = $db->query("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . $snc->ilan_no)->fetch(PDO::FETCH_OBJ)->ids;
    $mulid = ($multi->rowCount() > 1 && $snc->id == $multif->id) ? " IN(" . $multidids . ")" : "=" . $snc->id;

    // İlanı veritabanından sil
    $db->query("DELETE FROM sayfalar WHERE site_id_555=501 AND id" . $mulid);

    // Video varsa dosya sisteminden sil
    if ($snc->video != '') {
        $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $snc->video;
        if (file_exists($nirde)) {
            @unlink($nirde);
        }
    }

    // Galeri fotoğraflarını veritabanından al ve dosya sisteminden sil
    $quu = $db->query("SELECT id, resim FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id" . $mulid);
    while ($row = $quu->fetch(PDO::FETCH_OBJ)) {
        $pinfo = pathinfo("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/" . $row->resim);
        $folder = $pinfo["dirname"] . "/";
        $ext = $pinfo["extension"];
        $fname = $pinfo["filename"];
        $bname = $pinfo["basename"];

        // Fotoğrafın farklı boyutlarını sil
        @unlink($folder . "thumb/" . $bname);
        @unlink($folder . $bname);
        @unlink($folder . $fname . "_original." . $ext);
    }

    // Galeri fotoğraf kayıtlarını veritabanından sil
    $db->query("DELETE FROM galeri_foto WHERE site_id_555=501 AND sayfa_id" . $mulid);
?>
<script type="text/javascript">
$(document).ready(function(){
    $("#row_<?=$id;?>").fadeOut(500);
});
</script>
<?php
    // İşlem tamamlandığında başarı mesajı gönder
    $fonk->ajax_tamam("Veri Silindi");
}