<?php
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
        $dili = $snc->kisa_adi;
    } else {
        die();
    }

    $baska_dil = $db->query("SELECT id, kisa_adi FROM diller_501 WHERE id != $id");

    if ($baska_dil->rowCount() < 1) {
        error_log("Sileceğiniz dil dışında başka dil olmadığından dil silinemez!", 3, '/var/log/php_errors.log');
        die($fonk->ajax_hata("Sileceğiniz dil dışında başka dil olmadığından dil silinemez!"));
    }

    $baska_dili = $baska_dil->fetch(PDO::FETCH_OBJ)->kisa_adi;

    // Veritabanı silme işlemi
    $deleteQueries = [
        "DELETE FROM ayarlar_501 WHERE dil = :dil",
        "DELETE FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND dil = :dil",
        "DELETE FROM kategoriler_501 WHERE dil = :dil",
        "DELETE FROM mail_sablonlar_501 WHERE dil = :dil",
        "DELETE FROM menuler_501 WHERE dil = :dil",
        "DELETE FROM referanslar_501 WHERE dil = :dil",
        "DELETE FROM sayfalar WHERE site_id_555=501 AND dil = :dil",
        "DELETE FROM slider_501 WHERE dil = :dil",
        "DELETE FROM subeler_bayiler_501 WHERE dil = :dil",
        "DELETE FROM sehirler_501 WHERE dil = :dil"
    ];

    try {
        foreach ($deleteQueries as $query) {
            $stmt = $db->prepare($query);
            $stmt->execute(['dil' => $dili]);
        }

        $stmt = $db->prepare("DELETE FROM diller_501 WHERE id = :id");
        $stmt->execute(['id' => $id]);

        unlink("../" . THEME_DIR . "diller/" . $dili . ".txt");

        $fonk->ajax_tamam("Dil Silindi.");
        setcookie("dil", $baska_dili, time() + 60 * 60 * 24 * 7);
        $fonk->yonlendir("index.php");

    } catch (PDOException $e) {
        // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
        error_log($e->getMessage(), 3, '/var/log/php_errors.log');
        die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
    }
}