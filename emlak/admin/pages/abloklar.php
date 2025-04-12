<?php
// Veritabanı bağlantısı ve güvenlik işlemleri
$id = $gvn->rakam($_GET["sil"] ?? '');
if ($id != "") {
    $db->query("DELETE FROM abloklar WHERE id=" . $id);
    header("Location:index.php?p=abloklar");
    exit;
}

if ($_POST) {
    $idler = $_POST["id"];
    $action = $_POST["action"];

    if (count($idler) > 0) {
        foreach ($idler as $id) {
            $id = $gvn->rakam($id);
            if ($action == 'sil') {
                $db->query("DELETE FROM abloklar WHERE id=" . $id);
            }
        }
    }
    header("Location:index.php?p=abloklar");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servisler</title>
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
                    <h4 class="pull-left page-title">Servisler</h4>
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
                                        <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=ablok_ekle';"> <i class="fa fa-plus"></i> Yeni Servis Ekle</button>
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
                                            <th>Başlık</th>
                                            <th>Url Adresi</th>
                                            <th>Sıra</th>
                                            <th>Kontroller</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sorgu = $db->query("SELECT * FROM abloklar WHERE dil='" . $dil . "' ORDER BY id DESC LIMIT 0,500");
                                        while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                                        ?>
                                        <tr>
                                            <td class="mail-select">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="checkbox<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="id[]" value="<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>">
                                                    <label for="checkbox<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>"></label>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($row->baslik, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars($row->url, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?= htmlspecialchars($row->sira, ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <div class="btn-group dropdown">
                                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                    <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li><a href="index.php?p=ablok_duzenle&id=<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>">Düzenle</a></li>
                                                        <li><a href="index.php?p=abloklar&sil=<?= htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8'); ?>">Sil</a></li>
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