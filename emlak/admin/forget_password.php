<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Creative Business - Kurumsal Firma Scripti">
    <meta name="author" content="İdris Selvi">
    <link rel="shortcut icon" href="assets/images/favicon_1.ico">
    <title>Şifreni mi unuttun ?</title>
    <link href="assets/css/admin.min.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]><script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script><![endif]-->
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,300,600,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="assets/istmark/jquery-1.6.1.min.js"></script>
    <script type="text/javascript" src="assets/istmark/jquery.form.min.js"></script>
    <script type="text/javascript" src="assets/istmark/login_main.js"></script>
</head>

<body>
    <div class="wrapper-page">
        <div class="panel panel-color panel-primary panel-pages">
            <div class="panel-heading bg-img" style="background-color:#333;">
                <div class="bg-overlay"></div>
                <h3 class="text-center m-t-10 text-white">Parolamı Hatırlat</h3>
            </div>
            <div class="panel-body">
                <div id="forget_status"></div>
                <form method="post" action="ajax.php?p=forget_password" role="form" class="text-center" id="ForgetPassword" method="POST" onsubmit="return false;">
                    <div class="form-group m-b-0">
                        <div class="input-group">
                            <input type="text" class="form-control input-lg" placeholder="Kayıtlı e-posta adresiniz" name="email">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary btn-lg waves-effect waves-light">Gönder</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group m-t-30">
                        <div class="col-sm-7"><a href="login.php"><i class="fa fa-user m-r-5"></i> Giriş'e Dön</a></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        var resizefunc = [];
    </script>
    <script src="assets/js/admin.min.js"></script>
</body>
</html>