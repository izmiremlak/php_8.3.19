<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id !== "" && $hesap->tipi !== 0) {

    // Girişleri temizle ve doğrula
    $sil = filter_var($_GET['sil'], FILTER_VALIDATE_INT);

    if ($sil) {
        // Danışmanı sil
        $stmt = $db->prepare("DELETE FROM danismanlar_501 WHERE id = :id");
        $stmt->execute(['id' => $sil]);

        if ($stmt->rowCount() > 0) {
            $id = $sil;

            ?>
            <script type="text/javascript">
            $(document).ready(function(){
                $("#danisman<?=$id;?>").fadeOut(500,function(){
                    $("#danisman<?=$id;?>").remove();
                });
            });
            </script>
            <?php
            $fonk->ajax_tamam("Danışman Silindi");
        } else {
            error_log("Danışman silinemedi.", 3, '/var/log/php_errors.log');
            $fonk->ajax_hata("Danışman silinemedi.");
        }
    } else {
        error_log("Geçersiz Danışman ID.", 3, '/var/log/php_errors.log');
        $fonk->ajax_hata("Geçersiz Danışman ID.");
    }
}