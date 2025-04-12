<?php
// Gerekli dosyaları dahil et
include "../functions.php";

// Kullanıcı kimliği kontrolü
if (!empty($hesap->id)) {
    // Giriş yapmış kullanıcıyı yönlendir ve işlemi sonlandır
    header("Location: index.php");
    die("Access Denied User");
    exit;
}

// Demo modu kontrolü
$demo = (str_contains($_SERVER["HTTP_HOST"], "izmirtr.com")) ? 1 : 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Yönetim Paneli Giriş Sayfası">
    <meta name="author" content="Site Yönetimi">
    <link rel="shortcut icon" href="assets/images/favicon_1.ico">
    <title>Yönetim Paneli</title>
    <link href="assets/css/admin.min.css" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,300,600,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <!-- IE eski sürümler için destek -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script type="text/javascript" src="assets/istmark/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="assets/istmark/jquery.form.min.js"></script>
    <!-- Google reCAPTCHA Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body <?= ($demo === 1) ? 'onload="AjaxFormS(\'LoginForm\',\'login_status\');"' : ''; ?>>
    <div class="wrapper-page">
        <div class="panel panel-color panel-primary panel-pages">
            <div class="panel-heading bg-img" style="background-color:#333;">
                <div class="bg-overlay"></div>
                <h3 class="text-center m-t-10 text-white">YÖNETİM PANELİ</h3>
            </div>
            <div class="panel-body">
                <div id="login_status"></div>
                
                <form class="form-horizontal m-t-20" action="ajax.php?p=login" id="LoginForm" method="POST" onsubmit="return false;">
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control input-lg" type="text" name="email" placeholder="E-Posta" value="<?= ($demo === 1) ? 'demo@example.com' : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control input-lg" type="password" name="parola" placeholder="Parola" value="<?= ($demo === 1) ? 'demo' : ''; ?>">
                        </div>
                    </div>
                    <!-- Google reCAPTCHA -->
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="g-recaptcha" data-sitekey="6LeOm_8qAAAAAK2kYZDabxefz7VJO6Wq_ppVTDZg"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="checkbox checkbox-primary">
                                <input id="checkbox-signup" name="otut" value="1" type="checkbox" checked>
                                <label for="checkbox-signup">Beni Hatırla</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <div class="col-xs-12">
                            <button class="btn btn-default btn-rounded waves-effect m-b-5" type="submit" onclick="AjaxFormS('LoginForm','login_status');">Oturum Aç</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-7"><a href="forget_password.php"><i class="fa fa-lock m-r-5"></i> Parolamı Hatırlat</a></div>
                        <div class="col-sm-5 text-right"><a href="../"><i class="fa fa-home"></i> Anasayfaya Dön</a></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    var resizefunc = [];

    // AJAX form gönderim işlevi
    function AjaxFormS(formId, statusId) {
        $('#' + statusId).html('<p style="color:blue;">Form gönderiliyor...</p>');

        // reCAPTCHA kontrolü
        if (typeof grecaptcha === 'undefined') {
            $('#' + statusId).html('<p style="color:red;">Hata: reCAPTCHA yüklenemedi!</p>');
            return false;
        }

        setTimeout(function() {
            var recaptchaResponse = grecaptcha.getResponse();

            if (recaptchaResponse.length === 0) {
                $('#' + statusId).html('<p style="color:red;">Hata: Lütfen "Ben robot değilim" kutusunu işaretleyin!</p>');
                return false;
            }

            var formData = $('#' + formId).serialize() + '&g-recaptcha-response=' + recaptchaResponse;

            <?php if ($demo === 1) { ?>
                console.log("Demo modunda gönderilen veri: " + formData);
            <?php } ?>

            $.ajax({
                url: 'ajax.php?p=login',
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log("Sunucu yanıtı: " + response);
                    $('#' + statusId).html(response);

                    if (response.indexOf("Giriş yapılıyor...") !== -1) {
                        setTimeout(function() {
                            window.location.href = 'index.php';
                        }, 1000);
                    } else if (response.indexOf("hatalı mail") !== -1) {
                        $('#' + statusId).html('<p style="color:red;">Hata: Hatalı e-posta adresi girdiniz!</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX hatası: " + error + " (HTTP Kodu: " + xhr.status + ")");
                    $('#' + statusId).html('<p style="color:red;">Hata: Sunucu ile bağlantı kurulamadı! (HTTP Kodu: ' + xhr.status + ')</p>');
                }
            });
        }, 500);
    }
    </script>
    <script src="assets/js/admin.min.js"></script>
</body>
</html>