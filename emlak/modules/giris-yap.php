<?php
declare(strict_types=1);

if (!defined("THEME_DIR")) {
    die();
}

if ($hesap->id != '') {
    header("Location:uye-paneli");
    exit();
}

error_reporting(E_ALL); // Tüm hata raporlamalarını aç
ini_set('display_errors', '1'); // Hataları ekranda göster
ini_set('log_errors', '1'); // Hataları log dosyasına yaz
ini_set('error_log', __DIR__ . '/error_log.log'); // Log dosyasının yolu

$referer = $gvn->html_temizle($_SERVER["HTTP_REFERER"]);
$referer = ($referer == '') ? ORGIN_URL . ltrim($gvn->html_temizle($_SERVER["REQUEST_URI"]), "/") : $referer;
if (stristr($referer, $domain) && $referer != '') {
    setcookie("login_redirect", $referer, time() + 60 * 60);
}
?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?= htmlspecialchars(dil("TX356"), ENT_QUOTES, 'UTF-8'); ?></title>
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
    <h1><?= htmlspecialchars(dil("TX3562"), ENT_QUOTES, 'UTF-8'); ?></h1>
</div>

<div class="uyeolgirisyap">
    <div class="uyeolgirisslogan"><h4><?= htmlspecialchars(dil("TX360"), ENT_QUOTES, 'UTF-8'); ?><?= ($gayarlar->uyelik == 1) ? htmlspecialchars(dil("TX651"), ENT_QUOTES, 'UTF-8') : ''; ?></h4>
        <?php if ($gayarlar->uyelik == 1) { ?><br><br>
        <a href="hesap-olustur" class="gonderbtn"><?= htmlspecialchars(dil("TX361"), ENT_QUOTES, 'UTF-8'); ?></a><?php } ?>
    </div>

    <div class="uyeol" style="margin-top:50px;margin-bottom:50px;">
        <div style="padding:20px;">
            <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById("GirisForm").addEventListener("keypress", function(e) {
                    if (e.keyCode === 13) {
                        e.preventDefault(); // Enter tuşuna basıldığında formu gönderme
                        document.querySelector("#GirisForm .btn").click();
                    }
                });
            });
            </script>
            <form action="ajax.php?p=giris" method="POST" id="GirisForm">
                <table width="100%" border="0">
                    <tr>
                        <td colspan="2"><h4><i class="fa fa-sign-in" aria-hidden="true"></i> <strong><?= htmlspecialchars(dil("TX114"), ENT_QUOTES, 'UTF-8'); ?></strong></h4></td>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars(dil("TX116"), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><input name="email" type="text"></td>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars(dil("TX117"), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><input name="parola" type="password"></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input id="checkbox-4" class="checkbox-custom" name="otut" value="1" type="checkbox" checked style="width:100px;">
                            <label for="checkbox-4" class="checkbox-custom-label"><span class="checktext"><?= htmlspecialchars(dil("TX118"), ENT_QUOTES, 'UTF-8'); ?></span></label>
                            <a class="sifreunuttulink" href="javascript:;" onclick="sifre_unuttu();"><?= htmlspecialchars(dil("TX119"), ENT_QUOTES, 'UTF-8'); ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none" colspan="2">
                            <div style="width: 55%; font-size: 14px; float: left; display:none" id="GirisForm_Snc"></div>
                            <button type="button" onclick="AjaxFormS('GirisForm','GirisForm_Snc');" class="btn"><i class="fa fa-sign-in" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX120"), ENT_QUOTES, 'UTF-8'); ?></button>
                        </td>
                    </tr>
                </table>
            </form>

            <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById("SifreUnuttum").addEventListener("keypress", function(e) {
                    if (e.keyCode === 13) {
                        e.preventDefault(); // Enter tuşuna basıldığında formu gönderme
                        document.querySelector("#SifreUnuttum .btn").click();
                    }
                });
            });
            </script>
            <form action="ajax.php?p=sfunuttum" method="POST" id="SifreUnuttum" style="display:none">
                <table width="100%" border="0">
                    <tr>
                        <td colspan="2"><h4><i class="fa fa-sign-in" aria-hidden="true"></i> <strong><?= htmlspecialchars(dil("TX122"), ENT_QUOTES, 'UTF-8'); ?></strong></h4></td>
                    </tr>
                    <tr>
                        <td><?= htmlspecialchars(dil("TX116"), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><input name="email" type="text"></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <a class="sifreunuttulink" href="javascript:;" onclick="giris_yap();"><?= htmlspecialchars(dil("TX123"), ENT_QUOTES, 'UTF-8'); ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="border:none" colspan="2">
                            <div id="SifreUnuttum_Snc" style="width: 55%; font-size: 14px; float: left; display:none"></div>
                            <button type="button" onclick="AjaxFormS('SifreUnuttum','SifreUnuttum_Snc');" class="btn"><i class="fa fa-sign-in" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX124"), ENT_QUOTES, 'UTF-8'); ?></button>
                        </td>
                    </tr>
                </table>
            </form>

            <script type="text/javascript">
            function sifre_unuttu() {
                document.getElementById('GirisForm').style.display = 'none';
                document.getElementById('SifreUnuttum').style.display = 'block';
            }

            function giris_yap() {
                document.getElementById('SifreUnuttum').style.display = 'none';
                document.getElementById('GirisForm').style.display = 'block';
            }
            </script>
        </div>
    </div>
</div>

</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>