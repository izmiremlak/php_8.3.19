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

// Mesajları okundu olarak işaretleme
$db->query("UPDATE mail_501 SET durumb='1' WHERE tipi=0 AND durumb=0");

?>            
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Gelen Mesajlar</h4>
            </div>
        </div>
        
        <? /*
        <div id="MailGonder" class="modal fade bs-example-modal-lg" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Mail Gönder</h4>
            </div>
            <form role="form" class="form-horizontal"  id="forms" method="POST" action="ajax.php?p=mail_gonder" onsubmit="return false;" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="konu" class="col-sm-1 control-label">Konu</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" id="konu" name="konu" value="" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="kime" class="col-sm-1 control-label">Alıcı</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control" id="kime" name="kime" value="" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <label class="control-label">Mesajınız</label>
                            <textarea class="summernote form-control" rows="9" id="mesaj" name="mesaj"></textarea>
                        </div>
                    </div>
                    <div id="form_status"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-purple waves-effect waves-light" onclick=" AjaxFormS('forms','form_status');"><i class="fa fa-paper-plane" aria-hidden="true"></i> Mesajı Gönder</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                </div>
            </form>
        </div>
        </div>
        </div>*/?>
        
        <div class="row">
            <div class="col-lg-12 col-md-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onclick="TumuOkundu();">Okundu</a></li>
                                    <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <a href="index.php?p=mail_gonder" target="_blank"><button type="button" class="btn btn-success waves-effect waves-light w-lg m-b-5"> <i class="fa fa-paper-plane" aria-hidden="true"></i> Yeni Mail</button></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default m-t-20">
                    <div class="panel-body">
                        <?php
                        if($hesap->tipi != 2){
                            $okundu = filter_input(INPUT_GET, 'okundu', FILTER_SANITIZE_NUMBER_INT);
                            $okunmadi = filter_input(INPUT_GET, 'okunmadi', FILTER_SANITIZE_NUMBER_INT);
                            $sil = filter_input(INPUT_GET, 'sil', FILTER_SANITIZE_NUMBER_INT);
                            
                            if($okundu){
                                $db->query("UPDATE mail_501 SET durum='1' WHERE id=$okundu");
                                header("Location:index.php?p=gelen_mesajlar");
                            } elseif($okunmadi){
                                $db->query("UPDATE mail_501 SET durum='0' WHERE id=$okunmadi");
                                header("Location:index.php?p=gelen_mesajlar");
                            } elseif($sil){
                                $db->query("DELETE FROM mail_501 WHERE id=$sil");
                                header("Location:index.php?p=gelen_mesajlar");
                            }
                            
                            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                                $idler = $_POST["id"] ?? [];
                                $action = $_POST["action"] ?? '';
                                
                                if(count($idler) > 0){
                                    foreach($idler as $id){
                                        $id = (int) $id;
                                        if($action === 'okundu'){
                                            $db->query("UPDATE mail_501 SET durum='1' WHERE id=$id");
                                        } elseif($action === 'okunmadi'){
                                            $db->query("UPDATE mail_501 SET durum='0' WHERE id=$id");
                                        } elseif($action === 'sil'){
                                            $db->query("DELETE FROM mail_501 WHERE id=$id");
                                        }
                                    }
                                }
                                header("Location:index.php?p=gelen_mesajlar");
                            }
                        }
                        ?>
                        <form action="" method="POST" id="SelectForm">
                            <input type="hidden" name="action" value="" id="action_hidden">    
                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th>Seç</th>
                                        <th>Adı Soyadı</th>
                                        <th>Telefon</th>
                                        <th>E-Posta</th>
                                        <th>Tarih</th>
                                        <th>Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sorgu = $db->query("SELECT * FROM mail_501 WHERE tipi=0 ORDER BY durum ASC, id DESC LIMIT 0,500");
                                    while($msg = $sorgu->fetch(PDO::FETCH_OBJ)){
                                    ?>
                                    <tr <?=($msg->durum == 0) ? 'style="background-color:#fde1e1"' : ''; ?>>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?=$msg->id;?>" type="checkbox" name="id[]" value="<?=$msg->id;?>">
                                                <label for="checkbox<?=$msg->id;?>"></label>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($msg->adsoyad, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($msg->telefon, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($msg->email, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?= htmlspecialchars($msg->tarih, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <div id="myModal<?=$msg->id;?>" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title"><?= htmlspecialchars($msg->adsoyad, ENT_QUOTES, 'UTF-8'); ?> kişinin mesajı</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            $mesaji = urlencode('
                                                            <table width="100%" align="center" cellpadding="0" cellspacing="0">
                                                              <tbody>
                                                            <tr>
                                                                <td width="100%" style="border-bottom-width: 1px;border-bottom-style: dotted;border-bottom-color: #CCC;padding:3px;" scope="col"><h3 style="font-size:20px; font-family:Calibri, Helvetica, sans-serif; color:#00599D; font-weight:bold;">Bilgilendirme</h3></td>
                                                              </tr>
                                                              <tr>
                                                                <td style="border-bottom-width: 1px;border-bottom-style: dotted;border-bottom-color: #CCC;padding:3px;" scope="col"><p><span style="font-size:14px; font-family:Calibri, Helvetica, sans-serif;">Sn. '.$msg->adsoyad.'     </span><br></p>
                                                                    <p><span style="font-size:14px; font-family:Calibri, Helvetica, sans-serif;">Sitemiz üzerinden gönderdiğiniz mesajınıza istinaden sizinle irtibat kurulmuştur.</span></p>
                                                                    <p><span style="font-family: Calibri, Helvetica, sans-serif; font-size: 14px"><strong>MESAJINIZI BURAYA YAZABİLİRSİNİZ.<br></strong></span></p>
                                                                    <p><span style="font-size:14px; font-family:Calibri, Helvetica, sans-serif;">    ------------- ALINTI -------------<br>
                                                                <b>Gönderici Adı Soyadı:</b> '.$msg->adsoyad.'<br>
                                                                <b>IP No:</b> '.$msg->ip.'<br>
                                                                <b>Gönderim Zamanı:</b> '.$msg->tarih.'<br>
                                                                <b>Mesajı;</b>
                                                               '.$msg->mesaj.'
                                                               <br>
                                                                ------------- ALINTI -------------</span></p>
                                                                    <p><span style="font-size:14px; font-family:Calibri, Helvetica, sans-serif;">Saygılarımızla,<br><span style="font-weight: bold;">Emlak Sitesi</span></span></p>
                                                                </td>
                                                              </tr>
                                                            </tbody>
                                                            </table>
                                                            ');?>
                                                            <p>
                                                                IP Adresi: <strong><?= htmlspecialchars($msg->ip, ENT_QUOTES, 'UTF-8'); ?></strong> &nbsp; Tarih: <strong><?= htmlspecialchars($msg->tarih, ENT_QUOTES, 'UTF-8'); ?></strong>
                                                            </p>
                                                            <p><?= nl2br(htmlspecialchars($msg->mesaj, ENT_QUOTES, 'UTF-8')); ?></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a href="index.php?p=mail_gonder&kime=<?= urlencode($msg->email); ?>&mesaj=<?= urlencode($mesaji); ?>" target="_blank"><button type="button" class="btn btn-success">Cevapla</button></a>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#" data-toggle="modal" data-target="#myModal<?= $msg->id; ?>">Mesajı Gör</a></li>
                                                    <li><a href="index.php?p=gelen_mesajlar&okundu=<?= $msg->id; ?>">Okundu</a></li>
                                                    <li><a href="index.php?p=gelen_mesajlar&sil=<?= $msg->id; ?>">Sil</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                    
<script>
    var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
<link href="assets/vendor/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">
<link href="assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
<style type="text/css">
</style>
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="assets/vendor/sweetalert/dist/sweetalert.min.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('.datatable').dataTable();
});

function TumuOkundu() {
    $("#action_hidden").val("okundu");
    swal({
        title: "Seçilenleri Okundu Olarak İşaretleme",
        text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Evet, Hemen!",
        closeOnConfirm: false
    }, function(){   
        // swal("Deleted!", "İşlem Başarıyla Gerçekleşti.", "success"); 
        $("#SelectForm").submit();
    });
}

function TumuOkunmadi() {
    $("#action_hidden").val("okunmadi");
    swal({
        title: "Seçilenleri Okunmadı Olarak İşaretleme",
        text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Evet, Hemen!",
        closeOnConfirm: false
    }, function(){   
        // swal("Deleted!", "İşlem Başarıyla Gerçekleşti.", "success"); 
        $("#SelectForm").submit();
    });
}

function TumuSil() {
    $("#action_hidden").val("sil");
    swal({
        title: "Seçilenleri Sil",
        text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Evet, Hemen!",
        closeOnConfirm: false
    }, function(){
        //  swal("Deleted!", "İşlem Başarıyla Gerçekleşti.", "success");
        $("#SelectForm").submit();
    });
}
</script>

<script>
jQuery(document).ready(function() {
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true, // set focus to editable area after initializing summernote
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0], editor, welEditable);
        }
    });

    $(".modal-wide").on("show.bs.modal", function() {
        var height = $(window).height();
        var width = $(window).width();
        $(this).find(".modal-body").css("height", "100%");
        $(this).find(".modal-body").css("width","100%");
    });
});

function Cevaplandir(id, email) {
    $("#kime").val(email);
    var hide_mesaj = $("#hide" + id).val();

    $('#myModal' + id).modal('hide');
    $('#MailGonder').modal('show');

    $("#mesaj").code(hide_mesaj);

    setTimeout(function() {
        $("#konu").focus();
    }, 500);
}
</script>