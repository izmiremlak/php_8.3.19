<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

// Oturum başlatma
session_start();

// Veritabanı bağlantısı (PDO kullanarak)
$dsn = 'mysql:host=localhost;dbname=emlak_sitesi';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
];

try {
    $db = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Veritabanı bağlantısı başarısız.');
}

// Kullanıcı girdisini sanitize etme
function sanitizeInput(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Hesap Ayarları</h4>
            </div>
        </div>
        
        <div class="row">
            <!-- Col 1 -->
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Yönetici Hesap Bilgileri<br>(Nasıl dolduracağınız kutucukların içinde yazılıdır)</h3>
                    </div>
                    <div class="panel-body">
                        <?php if ($hesap->email == 'info@izmirtr.com' && $hesap->parola == '!izmirTR5678izmirTR?') { ?>
                            <div class="panel panel-border panel-danger">
                                <div class="panel-heading">
                                    <h3 style="height:30px;" class="panel-title"><span id="yanip-sonen"><i class="fa fa-exclamation" aria-hidden="true"></i> Lütfen Yönetici Bilgilerinizi Değiştiriniz!</span></h3>
                                </div>
                                <div class="panel-body">
                                    <p>Kötü niyetli kişilerin admin panelinize erişerek sitenize zarar vermemesi için, standart olarak tanımlı gelen admin e-posta ve parola bilgilerinizi değiştiriniz.</p>
                                </div>
                            </div>
                        <?php } ?>
                        
                        <div id="hesap_status"></div>
                        <form role="form" class="form-horizontal" id="hesap_bilgi_form" method="POST" action="ajax.php?p=hesap_bilgileri" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="adi" class="col-sm-3 control-label">Firma Adı veya Adınız</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="adi" name="adi" value="<?= sanitizeInput($hesap->adi); ?>" placeholder="Buraya sayfamın sol üstünde logo nun yanında gözükecek şekilde firma adınızı yazınız.">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="soyadi" class="col-sm-3 control-label">Soyadınız</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="soyadi" name="soyadi" value="<?= sanitizeInput($hesap->soyadi); ?>" placeholder="İsminizi yazacaksanız buraya Soyadınızı yazınız.">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">E-Posta</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="email" name="email" value="<?= sanitizeInput($hesap->email); ?>" placeholder="Sitede kullandığınız E-postanızı yazınız.">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mparola" class="col-sm-3 control-label">Mevcut Parolanız</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="mparola" name="mparola" placeholder="Parola yazılı değilse, İşlem yapmak için mevcut parolanızı yazınız.">
                                </div>
                            </div>
                            <div class="form-group m-l-10">
                                <label for="yparola" class="col-sm-3 control-label">Yeni Parola Belirleyin</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="yparola" name="yparola" placeholder="Parolanızı değiştirmek istiyorsanız yazınız. İstemiyorsanız boş bırakınız.">
                                </div>
                            </div>
                            <div class="form-group m-l-10">
                                <label for="ytparola" class="col-sm-3 control-label">Yeni Parolayı Tekrar Girin</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="ytparola" name="ytparola" placeholder="Yeni Parolanızı Tekrar Yazınız.">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="avatar" class="col-sm-3 control-label">Profil Fotoğrafı</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="avatar" name="avatar">
                                    <span>(Aynı zamanda firma logosu olarak da kullanılabilir.)</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('hesap_bilgi_form','hesap_status');">Güncelle</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Col1 end -->
        </div>
    </div>
</div>

<script>
    var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script>
    $(document).ready(function(){
        setInterval(function(){
            $("#yanip-sonen").fadeToggle(500);
        }, 700); // 700 milisaniyede bir yanıp sönecek
    });
</script>