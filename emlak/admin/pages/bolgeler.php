<?php
// Veritabanı bağlantısı ve güvenlik işlemleri
require_once 'config.php'; // Veritabanı ve diğer ayarların yüklendiği dosya

function logError($errorMessage) {
    error_log($errorMessage, 3, '/path/to/error.log');
}

function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

try {
    $sil = $gvn->rakam($_GET["sil"] ?? '');
    if ($sil != '') {
        $db->query("DELETE FROM ulkeler_501 WHERE id=" . $sil);
        $db->query("DELETE FROM il WHERE ulke_id=" . $sil);
        $db->query("DELETE FROM ilce WHERE ulke_id=" . $sil);
        $db->query("DELETE FROM semt WHERE ulke_id=" . $sil);
        $db->query("DELETE FROM mahalle_koy WHERE ulke_id=" . $sil);
        header("Location:index.php?p=bolgeler");
        exit;
    }

    if ($_POST) {
        $idler = $_POST["id"];
        $action = $_POST["action"];
        if (count($idler) > 0) {
            foreach ($idler as $id) {
                $id = $gvn->rakam($id);
                if ($action == 'sil') {
                    $db->query("DELETE FROM ulkeler_501 WHERE id=" . $id);
                    $db->query("DELETE FROM il WHERE ulke_id=" . $id);
                    $db->query("DELETE FROM ilce WHERE ulke_id=" . $id);
                }
            }
        }
        header("Location:index.php?p=bolgeler");
        exit;
    }
} catch (Exception $e) {
    logError($e->getMessage());
    echo '<div class="alert alert-danger" role="alert">' . sanitizeInput($e->getMessage()) . '</div>';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bölgeler (Ülkeler)</title>
    <link rel="stylesheet" href="assets/css/admin.min.css">
    <link href="assets/plugins/notifications/notification.css" rel="stylesheet">
    <link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
    <link href="assets/vendor/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">Bölgeler (Ülkeler)</h4>
                </div>
            </div>
            <div id="VeriEkle" class="modal fade bs-example-modal-lg" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" style="width:60%; margin-left:auto;margin-right:auto;">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Ülke Ekle</h4>
                        </div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=bolgeler_ulke_ekle" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="adi" class="col-sm-1 control-label">Adı</label>
                                    <div class="col-sm-11">
                                        <input type="text" class="form-control" id="adi" name="adi" value="<?= sanitizeInput($adi ?? '') ?>" placeholder="">
                                    </div>
                                </div>
                                <div id="form_status"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms', 'form_status');"><i class="fa fa-plus" aria-hidden="true"></i> Ekle</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <form action="" method="POST" id="SelectForm">
                <input type="hidden" name="action" value="" id="action_hidden">
                <div class="row">
                    <div class="col-lg-12 col-md-8">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" data-toggle="modal" data-target="#VeriEkle"> <i class="fa fa-plus"></i> Yeni Ülke Ekle</button>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula</button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default m-t-20">
                            <div class="panel-body">
                                <table class="table table-hover mails datatable">
                                    <thead>
                                        <tr>
                                            <th>Seç</th>
                                            <th>Ülke Adı</th>
                                            <th>İl Sayısı</th>
                                            <th>İlçe Sayısı</th>
                                            <th>Kontroller</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sorgu = $db->query("SELECT * FROM ulkeler_501 ORDER BY id DESC LIMIT 0,500");
                                        while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                                            $topil = $db->query("SELECT id FROM il WHERE ulke_id=" . $row->id)->rowCount();
                                            $topilce = $db->query("SELECT id FROM ilce WHERE ulke_id=" . $row->id)->rowCount();
                                        ?>
                                        <tr>
                                            <td class="mail-select">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="checkbox<?= sanitizeInput($row->id) ?>" type="checkbox" name="id[]" value="<?= sanitizeInput($row->id) ?>">
                                                    <label for="checkbox<?= sanitizeInput($row->id) ?>"></label>
                                                </div>
                                            </td>
                                            <td><?= sanitizeInput($row->ulke_adi) ?></td>
                                            <td><?= sanitizeInput($topil) ?></td>
                                            <td><?= sanitizeInput($topilce) ?></td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                    <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="index.php?p=bolgeler_ulke&id=<?= sanitizeInput($row->id) ?>">Düzenle</a></li>
                                                        <li><a href="index.php?p=bolgeler&sil=<?= sanitizeInput($row->id) ?>">Sil</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        var resizefunc = [];
    </script>
    <script src="assets/js/admin.min.js"></script>
    <script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
    <script src="assets/plugins/notifications/notify-metro.js"></script>
    <script src="assets/plugins/notifications/notifications.js"></script>
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="assets/vendor/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.datatable').dataTable();
        });

        function TumuSil() {
            $("#action_hidden").val("sil");
            swal({  
                title: "Seçilenleri Sil",  
                text: "Bu işlemi gerçekten yapmak istiyor musunuz?",  
                type: "warning",  
                showCancelButton: true,  
                confirmButtonColor: "#DD6B55",  
                confirmButtonText: "Evet, Hemen!",  
                closeOnConfirm: false
            }, function() {  
                $("#SelectForm").submit();
            });
        }
    </script>
</body>
</html>