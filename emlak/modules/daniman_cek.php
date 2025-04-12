<?php
declare(strict_types=1);

error_reporting(E_ALL); // Tüm hata raporlamalarını aç
ini_set('display_errors', '1'); // Hataları ekranda göster
ini_set('log_errors', '1'); // Hataları log dosyasına yaz
ini_set('error_log', __DIR__ . '/error_log.log'); // Log dosyasının yolu

?>
<div class="danisman">
<h3 class="danismantitle"><?= ($uyee->id == '' || $uyee->turu == 2 || $uyee->turu == 1) ? htmlspecialchars($fonk->get_lang($sayfay->dil, "TX155"), ENT_QUOTES, 'UTF-8') : htmlspecialchars($fonk->get_lang($sayfay->dil, "TX156"), ENT_QUOTES, 'UTF-8'); ?></h3>

<a href="<?= htmlspecialchars($profil_link, ENT_QUOTES, 'UTF-8'); ?>"><img src="https://www.turkiyeemlaksitesi.com.tr/<?= htmlspecialchars($davatar, ENT_QUOTES, 'UTF-8'); ?>" width="200" height="200" alt="<?= htmlspecialchars($adsoyad, ENT_QUOTES, 'UTF-8'); ?>"></a>

<h4><strong><a href="<?= htmlspecialchars($profil_link, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($adsoyad, ENT_QUOTES, 'UTF-8'); ?></a></strong></h4>
<?php
if ($adsoyad != $uyee->unvan):
?>
    <span>
        <?= htmlspecialchars($uyee->unvan, ENT_QUOTES, 'UTF-8'); ?>
    </span>
<?php endif; ?>       
<div class="clear"></div>
    <div class="iletisim" style="display: flex; justify-content: space-evenly;"> 
    <a target="_blank" href="https://api.whatsapp.com/send?phone=<?= rawurlencode($uyee->telefon); ?>&text=<?= rawurlencode($link); ?>%20<?= rawurlencode('Bu ilan hakkında bilgi almak istiyorum'); ?>"><i class="fa fa-whatsapp"></i></a>
    <a target="_blank" href="https://t.me/share/url?url=<?= rawurlencode($link); ?>&text=<?= rawurlencode($sayfay->baslik); ?>" class="telegrambtn gonderbtn" style="font-size:14px;padding: 5px 25px;"><i class="fa fa-telegram"></i></a>
    </div>
<?php
if ($uyee->id != '') {
    $portfoyu = ($uyee->turu == 1 || $uyee->turu == 2) ? '/portfoy' : '';
?>
<a href="<?= htmlspecialchars($profil_link . $portfoyu, ENT_QUOTES, 'UTF-8'); ?>" class="gonderbtn" target="_blank" style="font-size:14px;padding: 7px 0px;width:140px;margin-top: 15px;float:none; display: inline-block;"><i class="fa fa-list-alt"></i> Portföy</a>
<?php if ($uyee->id != $hesap->id) { ?>
<?php if ($gayarlar->anlik_sohbet == 1) { ?>
<a href="#uyemsjgonder" class="gonderbtn" style="font-size:14px;padding: 7px 0px;width:140px;margin-top: 5px;float:none; display: inline-block;"><i class="fa fa-envelope-o" aria-hidden="true"></i> Mesaj Gönder</a>
<div class="clear"></div>
<?php } ?>
<?php } ?>
<?php } ?>
<?php if ($gsm != '') { ?><h5 class="profilgsm"><strong><a style="color:white;" href="tel:<?= htmlspecialchars($gsm, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($gsm, ENT_QUOTES, 'UTF-8'); ?></a></strong></h5><?php } ?>
<?php if ($demail != '') { ?><h5><strong><?= htmlspecialchars($fonk->get_lang($sayfay->dil, "TX158"), ENT_QUOTES, 'UTF-8'); ?></strong><br><a href="mailto:<?= htmlspecialchars($demail, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($demail, ENT_QUOTES, 'UTF-8'); ?></a></h5><?php } ?>
<div class="clear" style="margin-top:15px;"></div>
<a href="#HataliBildir" class="gonderbtn" style="font-size:13px;padding: 7px 0px;width:140px;margin-top: 5px;margin-bottom:10px;float:none; display: inline-block;"><i class="fa fa-bell-o" aria-hidden="true"></i> Hatalı Bildir</a>
<div class="clear"></div>
</div>