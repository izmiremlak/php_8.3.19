<?php
// Hata yönetimi için ayarları yapılandır
ini_set('log_errors', 1);
ini_set('error_log', THEME_DIR . 'logs/error_log.txt'); // Hata log dosyasının yolu
error_reporting(E_ALL); // Tüm hataları raporla

// Hataları sitede göster
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<div style='color: red;'><b>Hata:</b> [$errno] $errstr - $errfile:$errline</div>";
    return true;
}
set_error_handler("customErrorHandler");

// Mevcut sayfanın URL'sini alıyoruz ve XSS koruması uyguluyoruz
$link = htmlspecialchars(REQUEST_URL, ENT_QUOTES, 'UTF-8');
?>
<div class="paypasbutonlar">
    <h5><?= htmlspecialchars(dil("TX106"), ENT_QUOTES, 'UTF-8'); ?></h5>
    
    <a id="facepaylas" onclick="NewWindow(this.href,'Paylaş','545','600','no','center');return false" onfocus="this.blur()" target="_blank" href="https://www.facebook.com/sharer.php?u=<?= rawurlencode($link); ?>"><i class="fa fa-facebook-official" aria-hidden="true"></i> Facebook</a>
    
    <a id="twitpaylas" onclick="NewWindow(this.href,'Paylaş','545','600','no','center');return false" onfocus="this.blur()" target="_blank" href="https://twitter.com/intent/tweet?text=<?= rawurlencode($link); ?>"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter</a>
    
    <a id="googlepaylas" onclick="NewWindow(this.href,'Paylaş','545','600','no','center');return false" onfocus="this.blur()" target="_blank" href="https://plus.google.com/share?url=<?= rawurlencode($link); ?>"><i class="fa fa-google-plus" aria-hidden="true"></i> Google+</a>
    
    <a id="linkedpaylas" onclick="NewWindow(this.href,'Paylaş','545','600','no','center');return false" onfocus="this.blur()" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?= rawurlencode($link); ?>&title=<?= rawurlencode($link); ?>"><i class="fa fa-linkedin-square" aria-hidden="true"></i> LinkedIn</a>
    
    <a id="telegrampaylas" onclick="NewWindow(this.href,'Paylaş','545','600','no','center');return false" onfocus="this.blur()" target="_blank" href="https://t.me/share/url?url=<?= rawurlencode($link); ?>&text=<?= rawurlencode($link); ?>"><i class="fa fa-telegram" aria-hidden="true"></i> Telegram</a>
    
    <a id="whatsapppaylas" onclick="NewWindow(this.href,'Paylaş','545','600','no','center');return false" onfocus="this.blur()" target="_blank" href="https://api.whatsapp.com/send?text=<?= rawurlencode($link); ?>"><i class="fa fa-whatsapp" aria-hidden="true"></i> WhatsApp</a>
    
    <script language="javascript" type="text/javascript">
        var win = null;
        function NewWindow(mypage, myname, w, h, scroll, pos) {
            if (pos == "random") {
                LeftPosition = (screen.width) ? Math.floor(Math.random() * (screen.width - w)) : 100;
                TopPosition = (screen.height) ? Math.floor(Math.random() * ((screen.height - h) - 75)) : 100;
            }
            if (pos == "center") {
                LeftPosition = (screen.width) ? (screen.width - w) / 2 : 100;
                TopPosition = (screen.height) ? (screen.height - h) / 2 : 100;
            } else if ((pos != "center" && pos != "random") || pos == null) {
                LeftPosition = 0;
                TopPosition = 20;
            }
            settings = 'width=' + w + ',height=' + h + ',top=' + TopPosition + ',left=' + LeftPosition + ',scrollbars=' + scroll + ',location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no';
            win = window.open(mypage, myname, settings);
        }
    </script>
</div>