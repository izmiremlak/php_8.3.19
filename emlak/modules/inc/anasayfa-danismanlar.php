<?php
// Veritabanından öne çıkan danışmanları çekme sorgusu
$sql = $db->query("SELECT id, kid, adi, soyadi, avatar, avatard, nick_adi 
                   FROM hesaplar 
                   WHERE site_id_555=501 AND durum=0 AND turu=2 AND onecikar=1 AND onecikar_btarih>NOW() 
                   ORDER BY RAND() 
                   LIMIT 0,12");

// Sonuç olup olmadığını kontrol et
if ($sql->rowCount() > 0) {
?>
<!-- Öne Çıkan Danışmanlar -->
<div id="wrapper">
    <div class="content" id="bigcontent" style="margin-bottom:10px;margin-top:10px;">
        <div class="altbaslik">
            <div class="nextprevbtns">
                <span id="slider-next"><a id="prev5" class="bx-prev" href="" style="display: inline;"><i id="prevnextbtn" class="fa fa-angle-left"></i></a></span>
                <span id="slider-prev"><a id="next5" class="bx-next" href="" style="display: inline;"><i id="prevnextbtn" class="fa fa-angle-right"></i></a></span>
            </div>
            <h4 id="sicakfirsatlar"><strong><a href="danismanlar"><?= htmlspecialchars(dil("TX477")); ?></a></strong></h4>
        </div>

        <div class="list_carousel" id="anadanismanlar">
            <ul id="foo5">
                <?php
                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                    $plink = "profil/";
                    $plink .= ($row->nick_adi == '') ? $row->id : $row->nick_adi;
                    $kid = $row->kid;

                    // Kurumsal detayları çekme
                    $kurumsal = $db->prepare("SELECT adi, soyadi, unvan FROM hesaplar WHERE site_id_555=501 AND id=?");
                    $kurumsal->execute([$kid]);
                    if ($kurumsal->rowCount() > 0) {
                        $kurumsal = $kurumsal->fetch(PDO::FETCH_OBJ);
                        $kurumsal_name = ($kurumsal->unvan != '') ? $kurumsal->unvan : $kurumsal->adi . " " . $kurumsal->soyadi;
                    } else {
                        $kurumsal_name = "";
                    }

                    // Avatar URL'sini belirleme
                    $avatar = ($row->avatar == '' || $row->avatard == 1) ? 'https://www.turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : 'https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/' . $row->avatar;
                ?>
                    <li><a href="<?= htmlspecialchars($plink); ?>">
                            <div class="anadanisman">
                                <div class="danismanfotoana" style="background-image: url(<?= htmlspecialchars($avatar); ?>);"></div>
                                <div class="danismanbilgisi">
                                    <h4><?= htmlspecialchars($row->adi . " " . $row->soyadi); ?></h4>
                                    <?php if ($kurumsal_name != '') { ?><h5><?= htmlspecialchars($kurumsal_name); ?></h5><?php } ?>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>
<div class="clear"></div>
<!-- Öne Çıkan Danışmanlar END -->
<?php
} else {
    // Danışman bulunamazsa hata kaydı oluştur
    error_log("No featured advisors found.", 0);
    echo "<div class='error'>No featured advisors found.</div>";
}