<?php

declare(strict_types=1); // PHP 8.3 için sıkı tip kontrolü

// Hata raporlama ve loglama ayarları
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error_log.txt');

// Özel hata işleyici (tekrar tanımlamayı önlemek için)
if (!function_exists('customErrorHandler')) {
    /**
     * Özel hata işleyici fonksiyonu
     *
     * @param int $errno Hata numarası
     * @param string $errstr Hata mesajı
     * @param string|null $errfile Hata dosya adı
     * @param int|null $errline Hata satır numarası
     * @return bool Hata işlendi mi
     */
    function customErrorHandler($errno, $errstr, $errfile = null, $errline = null): bool
    {
        $errorMessage = "[$errno] $errstr - Dosya: " . ($errfile ?? 'bilinmiyor') . " - Satır: " . ($errline ?? 'bilinmiyor');
        error_log($errorMessage);
        echo "<div style='color: red;'><b>Hata:</b> $errorMessage</div>";
        return true;
    }
}
set_error_handler('customErrorHandler');

// Dil değişkenini kontrol et, tanımlı değilse varsayılan 'tr' kullan
$dil = $dil ?? 'tr';

?>

<div class="clear"></div>
<div class="footinfo">
    <h1><?= str_replace('[telefon]', htmlspecialchars($dayarlar->telefon ?? '', ENT_QUOTES, 'UTF-8'), htmlspecialchars(dil('FOOTER_TEXT') ?? '', ENT_QUOTES, 'UTF-8')); ?></h1>
</div>

<div class="footseolinks">
    <div id="wrapper">
        <?php
        $sql = $db->prepare('SELECT * FROM referanslar_501 WHERE dil = ? ORDER BY sira ASC');
        $sql->execute([htmlspecialchars($dil, ENT_QUOTES, 'UTF-8')]);
        while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
        ?>
            <a href="<?= htmlspecialchars($row->website ?? '', ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($row->adi ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
        <?php
        }
        ?>
    </div>
</div>

<div class="footer">
    <div id="wrapper">
        <div class="footblok">
            <img title="logo" alt="logo" src="uploads/thumb/<?= htmlspecialchars($gayarlar->footer_logo ?? '', ENT_QUOTES, 'UTF-8'); ?>" width="auto" height="80">
            <?php if (!empty($dayarlar->slogan3 ?? '')) { ?>
                <p><?= htmlspecialchars($dayarlar->slogan3, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php } ?>
            <?php if (!empty($dayarlar->telefon ?? '')) { ?>
                <h4><span><?= htmlspecialchars(dil('TX76') ?? '', ENT_QUOTES, 'UTF-8'); ?> </span><strong><a style="color:white;" href="tel:<?= htmlspecialchars($dayarlar->telefon, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($dayarlar->telefon, ENT_QUOTES, 'UTF-8'); ?></a></strong></h4>
            <?php } ?>
            <?php if (!empty($dayarlar->faks ?? '')) { ?>
                <h4><span><?= htmlspecialchars(dil('TX77') ?? '', ENT_QUOTES, 'UTF-8'); ?> </span><strong><?= htmlspecialchars($dayarlar->faks, ENT_QUOTES, 'UTF-8'); ?></strong></h4>
            <?php } ?>
            <?php if (!empty($dayarlar->email ?? '')) { ?>
                <h4><span><?= htmlspecialchars(dil('TX78') ?? '', ENT_QUOTES, 'UTF-8'); ?> </span><strong><?= htmlspecialchars($dayarlar->email, ENT_QUOTES, 'UTF-8'); ?></strong></h4>
            <?php } ?>
            <?php if (!empty($dayarlar->adres ?? '')) { ?>
                <h5><span><?= htmlspecialchars(dil('TX79') ?? '', ENT_QUOTES, 'UTF-8'); ?> </span><?= htmlspecialchars($dayarlar->adres, ENT_QUOTES, 'UTF-8'); ?></h5>
            <?php } ?>
        </div>

        <?php
        // Sayfaları sorgula ve yalnızca geçerliyse tanımla
        $sayfalar = [
            1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null, 7 => null
        ];
        foreach ([1, 2, 3, 4, 5, 6, 7] as $i) {
            if (($dayarlar->{"foot_sayfa{$i}"} ?? 0) != 0) {
                $stmt = $db->prepare('SELECT id, url FROM sayfalar WHERE site_id_555 = 501 AND id = ?');
                $stmt->execute([(int)$dayarlar->{"foot_sayfa{$i}"}]);
                $sayfalar[$i] = $stmt->fetch(PDO::FETCH_OBJ) ?: null;
            }
        }

        // URL'leri yalnızca sayfa varsa oluştur
        $sayfa_urls = [];
        foreach ([1, 2, 3, 4, 5, 6, 7] as $i) {
            $sayfa_urls[$i] = ($sayfalar[$i] && ($dayarlar->permalink ?? 'Hayır') === 'Evet')
                ? htmlspecialchars($sayfalar[$i]->url ?? '', ENT_QUOTES, 'UTF-8') . '.html'
                : 'index.php?p=sayfa&id=' . ($sayfalar[$i] ? (int)$sayfalar[$i]->id : 0);
        }
        ?>

        <div class="footblok" id="footlinks">
            <h3><?= htmlspecialchars(dil('TX80') ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
            <a href="<?= ($dayarlar->foot_sayfa1 ?? 0) == 0 ? htmlspecialchars($dayarlar->foot_link1 ?? '', ENT_QUOTES, 'UTF-8') : $sayfa_urls[1]; ?>"><?= htmlspecialchars($dayarlar->foot_text1 ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
            <a href="<?= ($dayarlar->foot_sayfa2 ?? 0) == 0 ? htmlspecialchars($dayarlar->foot_link2 ?? '', ENT_QUOTES, 'UTF-8') : $sayfa_urls[2]; ?>"><?= htmlspecialchars($dayarlar->foot_text2 ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
            <a href="<?= ($dayarlar->foot_sayfa3 ?? 0) == 0 ? htmlspecialchars($dayarlar->foot_link3 ?? '', ENT_QUOTES, 'UTF-8') : $sayfa_urls[3]; ?>"><?= htmlspecialchars($dayarlar->foot_text3 ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
            <a href="<?= ($dayarlar->foot_sayfa4 ?? 0) == 0 ? htmlspecialchars($dayarlar->foot_link4 ?? '', ENT_QUOTES, 'UTF-8') : $sayfa_urls[4]; ?>"><?= htmlspecialchars($dayarlar->foot_text4 ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
            <a href="<?= ($dayarlar->foot_sayfa5 ?? 0) == 0 ? htmlspecialchars($dayarlar->foot_link5 ?? '', ENT_QUOTES, 'UTF-8') : $sayfa_urls[5]; ?>"><?= htmlspecialchars($dayarlar->foot_text5 ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
            <a href="<?= ($dayarlar->foot_sayfa6 ?? 0) == 0 ? htmlspecialchars($dayarlar->foot_link6 ?? '', ENT_QUOTES, 'UTF-8') : $sayfa_urls[6]; ?>"><?= htmlspecialchars($dayarlar->foot_text6 ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
            <a href="<?= ($dayarlar->foot_sayfa7 ?? 0) == 0 ? htmlspecialchars($dayarlar->foot_link7 ?? '', ENT_QUOTES, 'UTF-8') : $sayfa_urls[7]; ?>"><?= htmlspecialchars($dayarlar->foot_text7 ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
        </div>

        <div class="footblok" id="footebulten">
            <h3><?= htmlspecialchars(dil('TX81') ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
            <form action="ajax.php?p=bulten" method="POST" id="bulten_form">
                <p><?= htmlspecialchars(dil('TX82') ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                <input name="gsm" type="text" placeholder="<?= htmlspecialchars(dil('TX83') ?? '', ENT_QUOTES, 'UTF-8'); ?>" id="gsm" data-mask="(0500) 000 00 00">
                <input name="email" type="text" placeholder="<?= htmlspecialchars(dil('TX84') ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="clear"></div>
                <a class="btn" href="javascript:AjaxFormS('bulten_form','bsonuc');" style="margin-bottom:5px;"><?= htmlspecialchars(dil('TX85') ?? '', ENT_QUOTES, 'UTF-8'); ?></a>
                <div id="bsonuc"></div>
            </form>

            <div id="BultenTamam" style="display:none">
                <!-- TAMAM MESAJ -->
                <div style="margin-top:30px;margin-bottom:70px;text-align:center;" id="BasvrTamam">
                    <i style="font-size:80px;color:white;" class="fa fa-check"></i>
                    <h2 style="color:white;font-weight:bold;"><?= htmlspecialchars(dil('TX86') ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                    <br>
                    <h4><?= htmlspecialchars(dil('TX87') ?? '', ENT_QUOTES, 'UTF-8'); ?></h4>
                </div>
                <!-- TAMAM MESAJ -->
            </div>
        </div>
    </div>
</div>

<div class="altfooter">
    <div id="wrapper">
        <h5><?= htmlspecialchars(dil('TX88') ?? '', ENT_QUOTES, 'UTF-8'); ?></h5>

        <?php if (!empty($dayarlar->facebook ?? '') || !empty($dayarlar->instagram ?? '') || !empty($dayarlar->twitter ?? '')) { ?>
            <div class="headsosyal">
                <?php if (!empty($dayarlar->facebook ?? '')) { ?>
                    <a target="_blank" href="<?= htmlspecialchars($dayarlar->facebook, ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                <?php } ?>
                <?php if (!empty($dayarlar->twitter ?? '')) { ?>
                    <a target="_blank" href="<?= htmlspecialchars($dayarlar->twitter, ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                <?php } ?>
                <?php if (!empty($dayarlar->instagram ?? '')) { ?>
                    <a target="_blank" href="<?= htmlspecialchars($dayarlar->instagram, ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<div class="clear"></div>

<a href="#0" class="cd-top"></a>

<?php if (($p ?? '') === 'uye_paneli' && in_array($_GET['rd'] ?? '', ['aktif_ilanlar', 'pasif_ilanlar', 'favori_ilanlar'])) { ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>css/dataTables.responsive.min.css" />
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/dataTables.responsive.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                responsive: true,
                "language": {
                    "url": "<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/dtablelang.json"
                }
            });
        });
    </script>
    <style>
        #datatable {font-size:13px;}
        .dataTables_length {width:160px;margin-bottom:10px;font-size:13px;}
        .dttblegoster {float:left;margin-right:10px;}
        .datatbspan {line-height: 35px;}
        .dataTables_paginate, .dataTables_info {font-size:13px;}
    </style>
<?php } else { ?>
    <!-- Js -->
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/jquery.cookie.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>rs-plugin/js/jquery.plugins.min.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>rs-plugin/js/jquery.slider.min.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/viewportchecker.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/waypoints.min.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/jquery.counterup.min.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/modernizr.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/zjquery.mask.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/zinputmask.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/jquery.prettyPhoto.js" type="text/javascript" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/setting.js" defer></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/jquery.carouFredSel-6.2.1-packed.js" defer></script>
    <script>
        $(function() {
            $('#foo2').carouFredSel({
                auto: true,
                prev: '#prev2',
                next: '#next2',
                pagination: "#pager2",
                mousewheel: true,
                scroll: {
                    fx: "scroll",
                    items: 1,
                    easing: "quadratic",
                    pauseOnHover: true,
                    duration: 1000
                }
            });

            $('#foo3').carouFredSel({
                auto: true,
                pagination: "#pager3",
                prev: '#prev3',
                next: '#next3',
                mousewheel: true,
                scroll: {
                    fx: "scroll",
                    items: 1,
                    easing: "quadratic",
                    pauseOnHover: true,
                    duration: 1000
                }
            });

            $('#foo4').carouFredSel({
                auto: true,
                pagination: "#pager4",
                prev: '#prev4',
                next: '#next4',
                mousewheel: false,
                scroll: {
                    fx: "scroll",
                    items: 1,
                    easing: "quadratic",
                    pauseOnHover: true,
                    duration: 1000
                }
            });

            $('#foo5').carouFredSel({
                auto: true,
                pagination: "#pager5",
                prev: '#prev5',
                next: '#next5',
                mousewheel: true,
                scroll: {
                    fx: "scroll",
                    items: 1,
                    easing: "quadratic",
                    pauseOnHover: true,
                    duration: 1000
                }
            });
        });
    </script>
<?php } ?>

<!-- Ekstra -->
<?php $fonk->ekstra(false, false, true); ?>

<?php if (($p ?? '') === 'uye_paneli' && ($rd ?? '') === 'danismanlar') { ?>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>/remodal/dist/remodal.min.js"></script>
    <script type="text/javascript">
        var inst = $('[data-remodal-id=DanismanSil]').remodal();

        function SilDanisman(id) {
            $("#delete_id").val(id);
            inst.open();
        }

        function DanismanSil(ilan_sil) {
            var DanismanID = $("#delete_id").val();
            if (DanismanID != 0) {
                inst.close();
                ajaxHere('ajax.php?p=danisman_sil&id=' + DanismanID + '&ilan_sil=' + ilan_sil, 'hidden_result');
            }
        }
    </script>
<?php } ?>

<?php if (($p ?? '') === 'sayfa' && in_array($sayfay->tipi ?? 0, [4, 5])) { ?>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightslider/js/lightslider.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/picturefill.min.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/lightgallery.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/lg-fullscreen.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/lg-thumbnail.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/lg-video.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/lg-autoplay.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/lg-zoom.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/lg-hash.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/lg-pager.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>lightgallery/js/jquery.mousewheel.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var neDir = "#image-slider";
            var neDir2 = "#image-gallery";
            $(neDir).lightSlider({
                gallery: true,
                item: 1,
                thumbItem: 6,
                slideMargin: 0,
                speed: 900,
                auto: true,
                loop: true,
                enableTouch: true,
                onSliderLoad: function() {
                    $(neDir).removeClass('cS-hidden');
                }
            });
            $(neDir2).lightGallery({
                hash: false,
                actualSize: false,
                exThumbImage: 'data-exthumbimage',
                enableDrag: true,
                enableTouch: true,
            });
        });
    </script>

    <?php if (($sayfay->tipi ?? 0) == 4) { ?>
        <script>
            function openCity(evt, cityName) {
                var i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                document.getElementById(cityName).style.display = "block";
                evt.currentTarget.className += " active";
            }

            document.getElementById("defaultOpen").click();
        </script>
    <?php } ?>
<?php } elseif (($p ?? '') === 'uye_paneli' && ($rd ?? '') === 'mesajlar' && ($gayarlar->anlik_sohbet ?? 0) == 1) { ?>
    <script type="text/javascript">
        var KisiListesi = $("#ContactList");
        var MesjListesi = $("#MessagesList");
        var KaydirSure = 1;

        $(document).ready(function() {
            ArayuzYukle();

            $("#AramaKutusu").keyup(function(ne) {
                ArayuzYukle();
            });
        });

        setInterval("ArayuzYukle()", 1500);

        function objCount(obj) {
            var prop;
            var propCount = 0;
            for (prop in obj) {
                propCount++;
            }
            return propCount;
        }

        function ArayuzYukle() {
            var uid = $("#uid").val();
            var ak = $("#AramaKutusu").val();

            $.get("ajax.php?p=mesajlar_kisiler", {'uid': uid, 'arama': ak}, function(data) {
                var kisiler = data.kisiler;

                if (kisiler == undefined) {
                    KisiListesi.html('');
                }

                if (ak != '' && kisiler != undefined) {
                    var mevcut_kisiler = $("#ContactList a");
                    if (mevcut_kisiler.length > 0) {
                        mevcut_kisiler.each(function(index) {
                            var indis = $(this).attr("id");
                            var kisi = kisiler[indis];
                            if (kisi == undefined) {
                                $(this).remove();
                            } else {
                                var icerik = kisi.icerik;
                                var len1 = $(this).prop('outerHTML').length;
                                var len2 = icerik.length;
                                if (len1 != len2) { $(this).prop('outerHTML', icerik); }
                            }
                        });
                    }

                    $.each(kisiler, function(i, item) {
                        var isDiv = $("#" + i).has(".mesajkisi").length ? true : false;
                        if (!isDiv) {
                            KisiListesi.append(item.icerik);
                        }
                    });
                }

                var bildirim = data.bildirim;
                if (bildirim != undefined) {
                    var cebildirim = $.cookie("bildirim");
                    if (cebildirim == undefined) {
                        if (bildirim > 0) {
                            bildirim_play();
                            $.cookie("bildirim", bildirim, { expires: 7 });
                            $(".mbildirim").fadeIn(300);
                            $(".mbildirim").html(bildirim);
                        }
                    } else {
                        if (bildirim == 0) {
                            $.removeCookie("bildirim");
                            $(".mbildirim").fadeOut(300);
                            $(".mbildirim").html(bildirim);
                        } else {
                            if (cebildirim != bildirim) {
                                bildirim_play();
                                $.cookie("bildirim", bildirim, { expires: 7 });
                                $(".mbildirim").fadeIn(300);
                                $(".mbildirim").html(bildirim);
                            }
                        }
                    }

                    var mbildirim = $(".mbildirim").val();
                    if (mbildirim != bildirim) {
                        if (bildirim > 0) {
                            $(".mbildirim").fadeIn(300);
                            $(".mbildirim").html(bildirim);
                        } else {
                            $(".mbildirim").fadeOut(300);
                            $(".mbildirim").html(bildirim);
                        }
                    }
                }

                if (ak == '' && kisiler != undefined) {
                    var mevcut_kisiler = $("#ContactList a");
                    if (mevcut_kisiler.length > 0) {
                        mevcut_kisiler.each(function(index) {
                            var indis = $(this).attr("id");
                            var kisi = kisiler[indis];
                            if (kisi == undefined) {
                                $(this).remove();
                            } else {
                                var icerik = kisi.icerik;
                                var len1 = $(this).prop('outerHTML').length;
                                var len2 = icerik.length;
                                var yindex = kisi.sira;

                                if (index != yindex) { $(this).remove(); }
                                if (len1 != len2) { $(this).prop('outerHTML', icerik); }
                            }
                        });
                    }

                    $.each(kisiler, function(i, item) {
                        var isDiv = $("#" + i).has(".mesajkisi").length ? true : false;
                        if (!isDiv) {
                            KisiListesi.prepend(item.icerik);
                        }
                    });
                }

                if (uid != 0 && data.sohbet != undefined) {
                    var ae = $("#ae");

                    if (ae.val() != uid) {
                        $("#MesajlasmaContent").fadeOut(100, function() {
                            $("#UyeAvatar").attr("src", data.sohbet.avatar);
                            $("#UyeAdiSoyadi").html(data.sohbet.adsoyad);
                            $("#UyeTuru").html(data.sohbet.uyeturu);
                            $("#UyeProLink").attr("href", data.sohbet.uyeprolink);
                            if (data.sohbet.engelbutonu == 1) { $("#EngelButon").hide(10); } else { $("#EngelButon").show(10); }
                            if (data.sohbet.benEngel == 1) { $("#EngelButon").html('<i class="fa fa-ban" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX402") ?? '', ENT_QUOTES, 'UTF-8'); ?>'); } else { $("#EngelButon").html('<i class="fa fa-ban" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX403") ?? '', ENT_QUOTES, 'UTF-8'); ?>'); }
                            if (data.sohbet.grsmesilbuton == 1) { $("#GorusmeSilButon").hide(10); } else { $("#GorusmeSilButon").show(10); }
                            if (data.sohbet.engelbutonu == 1 || data.sohbet.benEngel == 1) {
                                $("#MesajlasAlani").hide(1);
                                $("#MesajEngeli").show(1);
                            } else {
                                $("#MesajEngeli").hide(1);
                                $("#MesajlasAlani").show(1);
                            }
                        });
                    } else {
                        $("#UyeAvatar").attr("src", data.sohbet.avatar);
                        $("#UyeAdiSoyadi").html(data.sohbet.adsoyad);
                        $("#UyeTuru").html(data.sohbet.uyeturu);
                        $("#UyeProLink").attr("href", data.sohbet.uyeprolink);
                        if (data.sohbet.engelbutonu == 1) { $("#EngelButon").hide(10); } else { $("#EngelButon").show(10); }
                        if (data.sohbet.benEngel == 1) { $("#EngelButon").html('<i class="fa fa-ban" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX402") ?? '', ENT_QUOTES, 'UTF-8'); ?>'); } else { $("#EngelButon").html('<i class="fa fa-ban" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX403") ?? '', ENT_QUOTES, 'UTF-8'); ?>'); }
                        if (data.sohbet.grsmesilbuton == 1) { $("#GorusmeSilButon").hide(10); } else { $("#GorusmeSilButon").show(10); }
                        if (data.sohbet.engelbutonu == 1 || data.sohbet.benEngel == 1) {
                            $("#MesajlasAlani").hide(1);
                            $("#MesajEngeli").show(1);
                        } else {
                            $("#MesajEngeli").hide(1);
                            $("#MesajlasAlani").show(1);
                        }
                    }

                    var iletiler = data.sohbet.iletiler;

                    if (iletiler == undefined) {
                        MesjListesi.html('');
                        $("#MesajlasmaContent").fadeIn(300);
                        ae.val(uid);
                    } else {
                        var mevcut_iletiler = $("#MessagesList .msjbaloncuk");
                        var mticount = mevcut_iletiler.length;
                        if (mticount > 0) {
                            mevcut_iletiler.each(function(index) {
                                var indis = $(this).attr("id");
                                if (iletiler[indis] == undefined) {
                                    $(this).remove();
                                }
                            });
                        }

                        var ticount1 = objCount(iletiler);
                        var ticount2 = 0;

                        if (ticount1 > 0) {
                            $.each(iletiler, function(i, item) {
                                ticount2 += 1;
                                var isDiv = $("#" + i).has("h5").length ? true : false;
                                if (isDiv) {
                                    var len1 = $("#" + i).prop('outerHTML').length;
                                    var len2 = item.length;
                                    if (len1 != len2) {
                                        $("#" + i).prop('outerHTML', item);
                                    }
                                } else {
                                    MesjListesi.append(item);
                                }
                            });
                        }

                        if (mticount != 0 && mticount < ticount1 && ticount1 == ticount2) {
                            $('#MessageBox').animate({scrollTop: $('#MessageBox')[0].scrollHeight}, KaydirSure);
                        }

                        if (ticount1 == ticount2 && ae.val() != uid) {
                            ae.val(uid);
                            $("#MesajlasmaContent").fadeIn(300, function() {
                                $('#MessageBox').animate({scrollTop: $('#MessageBox')[0].scrollHeight}, KaydirSure);
                            });
                        }

                        if (mticount == 0 && ticount1 == ticount2 && ae.val() == uid) {
                            $('#MessageBox').animate({scrollTop: $('#MessageBox')[0].scrollHeight}, KaydirSure);
                        }
                    }
                }
            });
        }

        function SohbetGoster(uid) {
            var neuid = $("#uid");
            if (uid != neuid.val()) {
                neuid.val(uid);
                $("#MesajGonderForm").attr("action", "ajax.php?p=mesaj_gonder&uid=" + uid);
                window.history.pushState("string", "", "mesajlar?uid=" + uid);
                $("#default_acilis").hide(1);
                ArayuzYukle();
            }
        }

        function GorusmeyiSil() {
            var uid = $("#uid");
            var neuid = uid.val();
            if (confirm("<?= htmlspecialchars(dil('TX407') ?? '', ENT_QUOTES, 'UTF-8'); ?>")) {
                $.get("ajax.php?p=mesaj_sil", {'uid': neuid}, function(sonuc) {
                    ArayuzYukle();
                    $("#MesajlasmaContent").hide(1, function() {
                        $("#default_acilis").show(1);
                        uid.val(0);
                    });
                });
            }
        }

        function EngelDurum() {
            var uid = $("#uid");
            var neuid = uid.val();
            if (confirm("<?= htmlspecialchars(dil('TX408') ?? '', ENT_QUOTES, 'UTF-8'); ?>")) {
                $.get("ajax.php?p=mesaj_engelle", {'uid': neuid}, function(sonuc) {
                    ArayuzYukle();
                });
            }
        }
    </script>
    <input type="hidden" id="uid" value="<?= htmlspecialchars($uid ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
    <input type="hidden" id="ae" value="<?= htmlspecialchars($uid ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
<?php } ?>

<script type="text/javascript">
<?php if (($rd ?? '') !== 'mesajlar' && !empty($hesap->id ?? '') && ($gayarlar->anlik_sohbet ?? 0) == 1) { ?>
    function bildirim_kontrol() {
        $.get("ajax.php?p=mesajlar_bildirim", function(data) {
            var bildirim = data.bildirim;
            var cebildirim = $.cookie("bildirim");
            if (bildirim != undefined) {
                if (cebildirim == undefined) {
                    if (bildirim > 0) {
                        bildirim_play();
                        $.cookie("bildirim", bildirim, { expires: 7 });
                        $(".mbildirim").fadeIn(300);
                        $(".mbildirim").html(bildirim);
                    }
                } else {
                    if (bildirim == 0) {
                        $.removeCookie("bildirim");
                        $(".mbildirim").fadeOut(300);
                        $(".mbildirim").html(bildirim);
                    } else {
                        if (cebildirim != bildirim) {
                            bildirim_play();
                            $.cookie("bildirim", bildirim, { expires: 7 });
                            $(".mbildirim").fadeIn(300);
                            $(".mbildirim").html(bildirim);
                        }
                    }
                }

                var mbildirim = $(".mbildirim").val();
                if (mbildirim != bildirim) {
                    if (bildirim > 0) {
                        $(".mbildirim").fadeIn(300);
                        $(".mbildirim").html(bildirim);
                    } else {
                        $(".mbildirim").fadeOut(300);
                        $(".mbildirim").html(bildirim);
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        bildirim_kontrol();
    });

    setInterval("bildirim_kontrol()", 3000);
<?php } ?>

var bildirim_ses = document.createElement('audio');
bildirim_ses.setAttribute('src', '<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>sound/notify.mp3');
bildirim_ses.addEventListener("load", function() {
    bildirim_ses.play();
}, true);

function bildirim_play() {
    bildirim_ses.play();
}

function bildirim_pause() {
    bildirim_ses.pause();
}
</script>

<script>
$(document).ready(function() {
    $("#mobileMenuToggle").click(function() {
        $("#mobileMenu").toggleClass("active");
    });
});
</script>

<script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>js/jquery-sticky.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.menu').scrollToFixed();
});
</script>

<?php if (($p ?? '') === 'uye_paneli' && in_array($rd ?? '', ['dopinglerim', 'paketlerim', ''])) { ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>css/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    $(function() {
        $("#accordion").accordion({ heightStyle: "content" });
    });
    </script>
<?php } ?>

<?php if (($p ?? '') === 'uye_paneli' && in_array($_GET['rd'] ?? '', ['ilan_duzenle', 'ilan_olustur'])) { ?>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>nestable/js/uikit.min.js"></script>
    <script src="<?= htmlspecialchars(THEME_DIR ?? '', ENT_QUOTES, 'UTF-8'); ?>nestable/js/components/nestable.min.js"></script>
    <script>
    $('.uk-nestable').on('change.uk.nestable', function(e) {
        var data = $("#list").data("nestable").serialize();
        $.post("ajax.php?p=galeri_foto_guncelle&ilan_id=<?= htmlspecialchars($snc->id ?? '', ENT_QUOTES, 'UTF-8'); ?>&from=nestable", {value: data}, function(a) {
            $("#ilanGaleriFotolar_output").html(a);
        });
    });
    </script>
<?php } ?>

<?= htmlspecialchars($dayarlar->analytics ?? '', ENT_QUOTES, 'UTF-8'); ?>
<?= htmlspecialchars($dayarlar->embed ?? '', ENT_QUOTES, 'UTF-8'); ?>

</body>
</html>