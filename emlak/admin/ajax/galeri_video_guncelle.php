<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id != "" AND $hesap->tipi != 0) {

    // GET verilerini güvenli bir şekilde al
    $id = $gvn->rakam($_GET["ilan_id"]);
    $from = $gvn->harf_rakam($_GET["from"]);
    $video = $gvn->zrakam($_GET["video"]);

    // İlgili sayfa ve kullanıcı bilgilerini veritabanından al
    $kontrol = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND id=?");
    $kontrol->execute([$id]);
    if ($kontrol->rowCount() < 1) {
        die();
    }
    $snc = $kontrol->fetch(PDO::FETCH_OBJ);

    // Video dosyası geçici konumunu kontrol et
    $video_tmp = $_FILES["video"]["tmp_name"];
    if ($video_tmp == '') {
        die('<span class="error">' . dil("TX454") . '</span>');
    }

    // Video dosyası bilgilerini al ve kontrol et
    $video_name = $_FILES["video"]["name"];
    $video_size = $_FILES["video"]["size"];
    $video_exte = $fonk->uzanti($video_name);
    $uzantilar = [".mp4"];

    if ($video_size > dil("VIDEO_MAX_BAYT")) {
        die('<span class="error">' . dil("TX455") . '</span>');
    }

    if (!in_array($video_exte, $uzantilar)) {
        die('<span class="error">' . dil("TX456") . '</span>');
    }

    // Rastgele video adı oluştur
    $video_adi = strtolower(substr(md5(uniqid(rand())), 0, 12)) . ".mp4";

    // Video dosyasını yükle
    $yukle = @move_uploaded_file($video_tmp, "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $video_adi);
    if (!$yukle) {
        ?>
        <script type="text/javascript">
        var xbar = $('#YuklemeDurum');
        var xpercent = $('#percent');
        var xpercentVal = "0%";

        $("#YuklemeBar").slideUp(400, function() {
            $("#VideoForm").slideDown(400);
            xbar.width(xpercentVal);
            xpercent.html(xpercentVal);
        });
        </script>
        <?php
        die('<span class="error">' . dil("TX456") . '</span>');
    }

    // Eski video dosyasını sil
    if ($snc->video != '') {
        $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $snc->video;
        if (file_exists($nirde)) {
            unlink($nirde);
        }
    }

    // Veritabanında video bilgilerini güncelle
    try {
        $ilan_update = $db->prepare("UPDATE sayfalar SET video=? WHERE site_id_555=501 AND ilan_no=?");
        $ilan_update->execute([$video_adi, $snc->ilan_no]);
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, '/var/log/php_errors.log');
        die($fonk->ajax_hata("Bir hata oluştu: " . htmlspecialchars($e->getMessage())));
    }

    // Yönlendirme ve başarı mesajları
    if ($from == "insert") {
        if ($gayarlar->dopingler_501 == 1) {
            $fonk->ajax_tamam("Video Başarıyla Yüklendi.");
            $fonk->yonlendir("index.php?p=ilan_ekle&id=" . $snc->id . "&asama=2", 1);
        } else {
            $fonk->yonlendir("index.php?p=ilanlar", 3000);
            ?>
            <script type="text/javascript">
            $("#galeri_video_ekle").hide(1, function() {
                $("#TamamDiv").show(1);
            });
            $('html, body').animate({scrollTop: 0}, 500);
            </script>
            <?php
        }
    } else {
        $fonk->yonlendir("index.php?p=ilan_duzenle&id=" . $snc->id . "&goto=video#tab3", 100);
        $fonk->ajax_tamam("Video Başarıyla Yüklendi.");
    }
}