<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Session'dan custom verisini al
$customs = $_SESSION["custom"];

// Custom verisinin boşluk kontrolü
if ($fonk->bosluk_kontrol($customs) == false) {
    $custom = base64_decode($customs);
    $custom = json_decode($custom, true);
}

?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX549"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <!--span>...</span-->
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">
    <div class="uyepanel">
        <div class="content">
            <div class="uyedetay">
                <div class="uyeolgirisyap">
                    <h4 class="uyepaneltitle"><?= htmlspecialchars(dil("TX549"), ENT_QUOTES, 'UTF-8'); ?></h4>

                    <div style="margin-top:60px;margin-bottom:60px;text-align:center;" id="BasvrTamam">
                        <i style="font-size:80px;color:green;" class="fa fa-check"></i>
                        <h2 style="color:green;font-weight:bold;"><?= htmlspecialchars(dil("TX550"), ENT_QUOTES, 'UTF-8'); ?></h2>
                        <br/>
                        <h4><?= htmlspecialchars(dil("TX555"), ENT_QUOTES, 'UTF-8'); ?><br></h4><br><br>
                        <?php
                        if ($customs != '') {
                            if ($custom["satis"] == "doping_ekle") {
                                if ($_SESSION["advfrom"] == "insert") {
                                    header("Refresh:3; url=ilan-olustur?id=" . (int)$custom["ilan_id"] . "&asama=3");
                                    echo htmlspecialchars(dil("TX552"), ENT_QUOTES, 'UTF-8');
                                } elseif ($_SESSION["advfrom"] == "adv") {
                                    header("Refresh:2; url=uye-paneli?rd=ilan_duzenle&id=" . (int)$custom["ilan_id"] . "&goto=doping");
                                    echo htmlspecialchars(dil("TX552"), ENT_QUOTES, 'UTF-8');
                                }
                            } elseif ($custom["satis"] == "uyelik_paketi") {
                                header("Refresh:2; url=paketlerim");
                                echo htmlspecialchars(dil("TX552"), ENT_QUOTES, 'UTF-8');
                            } elseif ($custom["satis"] == "danisman_onecikar") {
                                header("Refresh:2; url=eklenen-danismanlar");
                                echo htmlspecialchars(dil("TX552"), ENT_QUOTES, 'UTF-8');
                            }
                            unset($_SESSION["custom"]);
                            unset($_SESSION["advfrom"]);
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>
        <div class="sidebar">
            <?php include THEME_DIR . "inc/uyepanel_sidebar.php"; ?>
        </div>
    </div>
    <div class="clear"></div>
// </div>fgjgh<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// Session'dan custom verisini al
$customs = $_SESSION["custom"];

// Custom verisinin boşluk kontrolü
if ($fonk->bosluk_kontrol($customs) == false) {
    $custom = base64_decode($customs);
    $custom = json_decode($custom, true);
}

?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX549"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <!--span>...</span-->
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">
    <div class="uyepanel">
        <div class="content">
            <div class="uyedetay">
                <div class="uyeolgirisyap">
                    <h4 class="uyepaneltitle"><?= htmlspecialchars(dil("TX549"), ENT_QUOTES, 'UTF-8'); ?></h4>

                    <div style="margin-top:60px;margin-bottom:60px;text-align:center;" id="BasvrTamam">
                        <i style="font-size:80px;color:green;" class="fa fa-check"></i>
                        <h2 style="color:green;font-weight:bold;"><?= htmlspecialchars(dil("TX550"), ENT_QUOTES, 'UTF-8'); ?></h2>
                        <br/>
                        <h4><?= htmlspecialchars(dil("TX555"), ENT_QUOTES, 'UTF-8'); ?><br></h4><br><br>
                        <?php
                        if ($customs != '') {
                            if ($custom["satis"] == "doping_ekle") {
                                if ($_SESSION["advfrom"] == "insert") {
                                    header("Refresh:3; url=ilan-olustur?id=" . (int)$custom["ilan_id"] . "&asama=3");
                                    echo htmlspecialchars(dil("TX552"), ENT_QUOTES, 'UTF-8');
                                } elseif ($_SESSION["advfrom"] == "adv") {
                                    header("Refresh:2; url=uye-paneli?rd=ilan_duzenle&id=" . (int)$custom["ilan_id"] . "&goto=doping");
                                    echo htmlspecialchars(dil("TX552"), ENT_QUOTES, 'UTF-8');
                                }
                            } elseif ($custom["satis"] == "uyelik_paketi") {
                                header("Refresh:2; url=paketlerim");
                                echo htmlspecialchars(dil("TX552"), ENT_QUOTES, 'UTF-8');
                            } elseif ($custom["satis"] == "danisman_onecikar") {
                                header("Refresh:2; url=eklenen-danismanlar");
                                echo htmlspecialchars(dil("TX552"), ENT_QUOTES, 'UTF-8');
                            }
                            unset($_SESSION["custom"]);
                            unset($_SESSION["advfrom"]);
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>
        <div class="sidebar">
            <?php include THEME_DIR . "inc/uyepanel_sidebar.php"; ?>
        </div>
    </div>
    <div class="clear"></div>
</div>