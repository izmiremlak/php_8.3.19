<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id != "" AND $hesap->tipi != 0) {
    // Resim ID'sini güvenli bir şekilde al
    $id = $gvn->rakam($_GET["id"]);

    // Sadece site sahibi resim silebilir
    $snc = $db->prepare("SELECT * FROM galeri_foto WHERE site_id_555=501 AND id=:ids");
    $snc->execute(['ids' => $id]);
    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ);
    } else {
        die();
    }

    // Resim dosya bilgilerini al ve sil
    $pinfo = pathinfo("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/" . $snc->resim);
    $folder = $pinfo["dirname"] . "/";
    $ext = $pinfo["extension"];
    $fname = $pinfo["filename"];
    $bname = $pinfo["basename"];

    // Dosyaları sil
    @unlink($folder . "thumb/" . $bname);
    @unlink($folder . $bname);
    @unlink($folder . $fname . "_original." . $ext);

    // Veritabanından kaydı sil
    try {
        $db->query("DELETE FROM galeri_foto WHERE site_id_555=501 AND id=" . $id);
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, '/var/log/php_errors.log');
        die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
    }

    // Başarı mesajı ve JavaScript ile ekrandan resmi kaldır
    ?>
    <script type="text/javascript">
        document.getElementById('foto_<?=$id;?>').style.display='none';
    </script>
    <?php
    $fonk->ajax_tamam("Fotoğraf Silindi.");
}