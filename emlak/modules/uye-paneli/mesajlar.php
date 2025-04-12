<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Anlık sohbet kapalıysa yönlendirme
if ($gayarlar->anlik_sohbet == 0) {
    header("Location:hesabim");
    die();
}

$uturu = explode(",", dil("UYELIK_TURLERI"));
$bid = $hesap->id;
$uid = $gvn->zrakam($_GET["uid"]);

// Chat kütüphanesi dahil etme
include "methods/chat.lib.php";
?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX426"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <span><?= htmlspecialchars(dil("TX427"), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">
    <div class="clear"></div>
    <div class="uyepanel">
        <div class="sidebar" id="msjkisiler">
            <i class="fa fa-search" aria-hidden="true" id="msjaraicon"></i>
            <input name="msjara" type="text" id="AramaKutusu" placeholder="<?= htmlspecialchars(dil("TX400"), ENT_QUOTES, 'UTF-8'); ?>" value="">

            <div id="ContactMessagesBox" class="showscroll">
                <div id="ContactList"></div>
            </div><!-- sol tarafa scroll için bir div end -->
        </div>

        <div class="content" id="uyemesajlari">
            <div class="uyedetay">
                <div class="mesajlasmalar">

                    <!-- DEFAULT AÇILIŞ -->
                    <div id="default_acilis" <?= ($uid != 0) ? 'style="display:none"' : ''; ?>>
                        <?= htmlspecialchars(dil("TX397"), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <!-- DEFAULT AÇILIŞ -->

                    <!-- MesajlasmaContent Start -->
                    <div id="MesajlasmaContent" <?= ($uid == 0) ? 'style="display:none"' : ''; ?>>
                        <div class="uyemsjprofili">
                            <img src="<?= htmlspecialchars($uyavatar, ENT_QUOTES, 'UTF-8'); ?>" id="UyeAvatar" width="100" height="100" alt="">
                            <h4><strong id="UyeAdiSoyadi"><?= htmlspecialchars($uyadsoyad, ENT_QUOTES, 'UTF-8'); ?></strong><br><span id="UyeTuru">(<?= htmlspecialchars($uyturu, ENT_QUOTES, 'UTF-8'); ?>)</span></h4>
                            <a href="<?= htmlspecialchars($uyeProLink, ENT_QUOTES, 'UTF-8'); ?>" id="UyeProLink" class="gonderbtn"><i class="fa fa-eye" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX433"), ENT_QUOTES, 'UTF-8'); ?></a>
                            <a href="javascript:;" class="gonderbtn" id="EngelButon" onclick="EngelDurum();" <?= ($KarsiEngel == 1) ? 'style="display:none"' : ''; ?>><i class="fa fa-ban" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX434"), ENT_QUOTES, 'UTF-8'); ?></a>
                            <a href="javascript:;" class="gonderbtn" id="GorusmeSilButon" onclick="GorusmeyiSil();" <?= ($isileti == 0) ? 'style="display:none"' : ''; ?>><i class="fa fa-trash"></i> <?= htmlspecialchars(dil("TX435"), ENT_QUOTES, 'UTF-8'); ?></a>
                        </div>

                        <div id="MessageBox" class="showscroll">
                            <div id="MessagesList"></div>
                            <div id="scrollbottom"></div>
                        </div>

                        <div class="uyemsjarea" id="MesajlasAlani" <?= ($KarsiEngel == 1 || $BenEngel == 1) ? 'style="display:none"' : ''; ?>>
                            <form action="ajax.php?p=mesaj_gonder&uid=<?= (int)$uid; ?>" method="POST" id="MesajGonderForm">
                                <textarea rows="3" name="mesaj" id="MesajYaz"></textarea>
                                <a href="javascript:;" onclick="AjaxFormS('MesajGonderForm','MesajGonderSonuc');" style="float:right;" class="gonderbtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX436"), ENT_QUOTES, 'UTF-8'); ?></a>
                                <div id="MesajGonderSonuc" style="display:none"></div>
                            </form>
                        </div>

                        <div id="MesajEngeli" <?= ($KarsiEngel == 1 || $BenEngel == 1) ? 'style="display:block"' : 'style="display:none"'; ?>>
                            <?= htmlspecialchars(dil("TX406"), ENT_QUOTES, 'UTF-8'); ?>
                        </div>

                        <style>
                            #MesajEngeli {
                                text-align: center;
                                margin-top: 35px;
                                font-weight: bold;
                            }
                        </style>

                    </div><!-- MesajlasmaContent End -->

                </div><!-- sağ mesajlaşma tarafı end -->
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
</div>