<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id !== "" && $hesap->tipi !== 0) {

    // Girişleri temizle ve doğrula
    $sil = filter_var($_GET['sil'], FILTER_VALIDATE_INT);
    $onayla = filter_var($_GET['onayla'], FILTER_VALIDATE_INT);

    if ($sil !== false) {
        // Silme işlemi
        try {
            $stmt = $db->prepare("DELETE FROM dopingler_group_501 WHERE id = :id");
            $stmt->execute(['id' => $sil]);

            $stmt = $db->prepare("DELETE FROM dopingler_501 WHERE gid = :gid");
            $stmt->execute(['gid' => $sil]);

            ?>
            <script type="text/javascript">
            $(document).ready(function(){
                $("#doping<?=$sil;?>").fadeOut(500, function(){
                    $("#doping<?=$sil;?>").remove();
                });
            });
            </script>
            <?php
            $fonk->ajax_tamam("Paket Silindi");
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    } elseif ($onayla !== false) {
        // Onaylama işlemi
        try {
            $sip = $db->query("SELECT * FROM dopingler_group_501 WHERE id = {$onayla}")->fetch(PDO::FETCH_OBJ);
            $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = {$sip->acid}")->fetch(PDO::FETCH_OBJ);
            $snc = $db->query("SELECT id, baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = {$sip->ilan_id}")->fetch(PDO::FETCH_OBJ);

            $adsoyad = $hesapp->adi;
            $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
            $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
            $baslik = $snc->baslik . " " . dil("PAY_NAME");

            $fiyat = $gvn->para_str($sip->tutar) . " " . dil("DOPING_PBIRIMI");
            $neresi = "dopinglerim";

            $fonk->bildirim_gonder([
                $adsoyad,
                $hesapp->email,
                $hesapp->parola,
                $baslik,
                $fiyat,
                date("d.m.Y H:i", strtotime($fonk->datetime())),
                SITE_URL . $neresi
            ], "siparis_onaylandi", $hesapp->email, $hesapp->tel);

            $stmt = $db->prepare("UPDATE dopingler_group_501 SET durum = 1 WHERE id = :id");
            $stmt->execute(['id' => $onayla]);

            $stmt = $db->prepare("UPDATE dopingler_501 SET durum = 1 WHERE gid = :gid");
            $stmt->execute(['gid' => $onayla]);

            ?>
            <script type="text/javascript">
            $(document).ready(function(){
                $("#doping<?=$onayla;?>_durum").html('<strong style="color:green">Onaylandı</strong>');
            });
            </script>
            <?php
        } catch (PDOException $e) {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            error_log($e->getMessage(), 3, '/var/log/php_errors.log');
            die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
        }
    }
}