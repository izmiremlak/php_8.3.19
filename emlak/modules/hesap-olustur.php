<?php
declare(strict_types=1);

if (!defined("THEME_DIR")) {
    die();
}

if ($hesap->id != '') {
    header("Location:uye-paneli");
    exit();
}

if ($gayarlar->uyelik == 0) {
    header("Location:giris-yap");
    exit();
}

error_reporting(E_ALL); // Tüm hata raporlamalarını aç
ini_set('display_errors', '1'); // Hataları ekranda göster
ini_set('log_errors', '1'); // Hataları log dosyasına yaz
ini_set('error_log', __DIR__ . '/error_log.log'); // Log dosyasının yolu
?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= htmlspecialchars(dil("TX125"), ENT_QUOTES, 'UTF-8'); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?= htmlspecialchars($dayarlar->keywords, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="description" content="<?= htmlspecialchars($dayarlar->description, ENT_QUOTES, 'UTF-8'); ?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<?php include THEME_DIR . "inc/head.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var vid = document.getElementById("bgvid");
    var pauseButton = document.querySelector("#polina button");

    if (window.matchMedia('(prefers-reduced-motion)').matches) {
        vid.removeAttribute("autoplay");
        vid.pause();
        pauseButton.innerHTML = "Paused";
    }

    function vidFade() {
        vid.classList.add("stopfade");
    }

    vid.addEventListener('ended', function() {
        vid.pause();
        vidFade();
    });

    pauseButton.addEventListener("click", function() {
        vid.classList.toggle("stopfade");
        if (vid.paused) {
            vid.play();
            pauseButton.innerHTML = "Pause";
        } else {
            vid.pause();
            pauseButton.innerHTML = "Paused";
        }
    });
});
</script>
</head>
<body id="uyegirispage">

<video poster="modules/images/uyeolgirisbg.jpg" id="bgvid" playsinline autoplay muted loop>
<source src="modules/images/uyeolgirisbg.mp4" type="video/mp4">
</video>

<div id="wrapper">

<div class="uyeolgirislogo">
    <a href="index.html"><img title="logo" alt="logo" src="uploads/thumb/<?= htmlspecialchars($gayarlar->footer_logo, ENT_QUOTES, 'UTF-8'); ?>" width="auto" height="80" class=""></a>
    <h1><?= htmlspecialchars(dil("TX125"), ENT_QUOTES, 'UTF-8'); ?></h1>
</div>

<div class="uyeolgirisyap">
    <div class="uyeolgirisslogan"><h4><?= htmlspecialchars(dil("TX358"), ENT_QUOTES, 'UTF-8'); ?></strong></h4><br><br>
        <a href="girisyap" class="gonderbtn"><?= htmlspecialchars(dil("TX359"), ENT_QUOTES, 'UTF-8'); ?></a>
    </div>

    <div class="uyeol">
        <div style="padding:20px;">
            <form action="ajax.php?p=kaydol" method="POST" id="KaydolForm">
                <style>
                /* Custom CSS styles can be added here */
                </style>

                <!-- SMS ONAY -->
                <div id="Gonay" style="display:none;">
                    <table width="100%" border="0">
                        <tr>
                            <td align="center" style="border:none;">
                                <h3><strong><?= htmlspecialchars(dil('TX378'), ENT_QUOTES, 'UTF-8'); ?></strong></h3><br>
                                <h4 style="font-size: 18px; font-weight: 100;"><?= htmlspecialchars(dil('TX379'), ENT_QUOTES, 'UTF-8'); ?></h4><br>
                                <input name="scode" type="text" value="" style="width:208px;margin-bottom:25px;padding:11px;" placeholder="<?= htmlspecialchars(dil('TX383'), ENT_QUOTES, 'UTF-8'); ?>">
                                <br>
                                <a href="javascript:;" onClick="AjaxFormS('KaydolForm','Gonay_snc');" class="mobilonaybtn"><i style="margin-right:5px;" class="fa fa-check"></i> <?= htmlspecialchars(dil('TX384'), ENT_QUOTES, 'UTF-8'); ?></a>
                                <div class="clear"></div><br>
                                <div id="Gonay_snc" style="display:none"></div>
                                <br>
                                <a href="iletisim" target="_blank"><i class="fa fa-caret-right" aria-hidden="true"></i> <?= htmlspecialchars(dil('TX381'), ENT_QUOTES, 'UTF-8'); ?></a> <br><br>
                                <strong><a href="javascript:;" onclick="GeriDon();"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?= htmlspecialchars(dil('TX382'), ENT_QUOTES, 'UTF-8'); ?></a></strong>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- SMS ONAY -->

                <!-- Uyelik start -->
                <div id="uyelik">
                    <table width="100%" border="0">
                        <tr>
                            <td colspan="2"><h4><i class="fa fa-user-plus" aria-hidden="true"></i> <strong><?= htmlspecialchars(dil("TX125"), ENT_QUOTES, 'UTF-8'); ?></strong></h4></td>
                        </tr>
                        
                        <tr>
                            <td colspan="2" height="50">
                                <span style="margin-right:20px;font-weight:bold;margin-bottom:7px;float:left;"><?= htmlspecialchars(dil("TX350"), ENT_QUOTES, 'UTF-8'); ?></span>
                                <input id="turu_0" class="radio-custom" name="turu" value="0" type="radio" style="width:25px;">
                                <label for="turu_0" class="radio-custom-label" style="margin-right: 28px;" ><span class="checktext"><?= htmlspecialchars(dil("TX351"), ENT_QUOTES, 'UTF-8'); ?></span></label>
                                <input id="turu_1" class="radio-custom" name="turu" value="1" type="radio" style="width:25px;">
                                <label for="turu_1" class="radio-custom-label" style="margin-right: 28px;" ><span class="checktext"><?= htmlspecialchars(dil("TX352"), ENT_QUOTES, 'UTF-8'); ?></span></label>
                            </td>
                        </tr>
                        
                        <tr>
                            <td><?= htmlspecialchars(dil("TX126"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="adsoyad" type="text"></td>
                        </tr>
                        <tr>
                            <td><?= htmlspecialchars(dil("TX127"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="email" type="text"></td>
                        </tr>
                        
                        <tr>
                            <td><?= htmlspecialchars(dil("TX128"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="telefon" type="text" id="gsm" placeholder="<?= ($gayarlar->sms_aktivasyon == 1) ? htmlspecialchars(dil("TX373"), ENT_QUOTES, 'UTF-8') : ''; ?>"></td>
                        </tr>
                        
                        <?php if ($gayarlar->tcnod == 1) { ?>
                        <tr class="turu_0">
                            <td><?= htmlspecialchars(dil("TX364"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="tcno" type="text" id="tcno" maxlength="11" placeholder=""></td>
                        </tr>
                        <?php } ?>
                        
                        <tr class="turu_1" style="display:none">
                            <td><?= htmlspecialchars(dil("TX366"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="unvan" type="text" id="unvan" placeholder=""></td>
                        </tr>
                        
                        <tr class="turu_1" style="display:none">
                            <td><?= htmlspecialchars(dil("TX367"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="vergi_no" type="text" id="vergi_no" placeholder=""></td>
                        </tr>
                        
                        <tr class="turu_1" style="display:none">
                            <td><?= htmlspecialchars(dil("TX368"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="vergi_dairesi" type="text" id="vergi_dairesi" placeholder=""></td>
                        </tr>
                        
                        <?php if ($gayarlar->adresd == 1) { ?>
                        <tr>
                            <td><?= htmlspecialchars(dil("TX365"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="adres" type="text" id="adres" placeholder=""></td>
                        </tr>
                        <?php } ?>
                        
                        <tr>
                            <td><?= htmlspecialchars(dil("TX129"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="parola" type="password"></td>
                        </tr>
                        <tr>
                            <td><?= htmlspecialchars(dil("TX130"), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><input name="parola_tekrar" type="password"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input id="checkbox-5" class="checkbox-custom" name="sozlesme" value="1" type="checkbox" style="width:100px;">
                                <label for="checkbox-5" class="checkbox-custom-label"><span class="checktext"><a target="_blank" href="<?= htmlspecialchars(dil("TX131HF"), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars(dil("TX131"), ENT_QUOTES, 'UTF-8'); ?></a></span></label>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none" colspan="2">
                                <div class="clear" style="margin-bottom: 15px;"></div>
                                <center>
                                    <button type="button" style="float:none;" onclick="AjaxFormS('KaydolForm','KaydolForm_Snc');" class="btn"><i class="fa fa-user-plus" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX132"), ENT_QUOTES, 'UTF-8'); ?></button>
                                    <div class="clear"></div>
                                    <div id="KaydolForm_Snc" align="center" style="display:none;font-weight:bold;"></div>
                                </center>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- Uyelik End -->
            </form>
            <div id="TamamPnc" style="display:none">
                <!-- TAMAM MESAJ -->
                <div style="margin-top:30px;margin-bottom:70px;text-align:center;" id="BasvrTamam">
                    <i style="font-size:80px;color:green;" class="fa fa-check"></i>
                    <h4 style="color:green;font-weight:bold;"><?= htmlspecialchars(dil("TX133"), ENT_QUOTES, 'UTF-8'); ?></h4>
                    <br/>
                    <h5><?= htmlspecialchars(dil("TX134"), ENT_QUOTES, 'UTF-8'); ?></h5>
                </div>
                <!-- TAMAM MESAJ -->
            </div>

            <script type="text/javascript">
            function GeriDon() {
                document.getElementById("KaydolForm").setAttribute("action", "ajax.php?p=kaydol");
                document.getElementById("Gonay").style.display = 'none';
                document.getElementById("uyelik").style.display = 'block';
            }

            document.addEventListener('DOMContentLoaded', function() {
                var turuElements = document.getElementsByName("turu");
                turuElements.forEach(function(elem) {
                    elem.addEventListener('change', function() {
                        var turu = this.value;
                        if (turu == 0 || turu == 2) {
                            document.querySelectorAll(".turu_1").forEach(function(el) {
                                el.style.display = 'none';
                            });
                            document.querySelectorAll(".turu_0").forEach(function(el) {
                                el.style.display = 'block';
                            });
                        } else if (turu == 1) {
                            document.querySelectorAll(".turu_1").forEach(function(el) {
                                el.style.display = 'block';
                            });
                            document.querySelectorAll(".turu_0").forEach(function(el) {
                                el.style.display = 'none';
                            });
                        }
                    });
                });
            });
            </script>
        </div>
    </div>
</div>

</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>