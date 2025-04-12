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
    $id = $gvn->rakam($_GET["id"]);
    $snc = $db->prepare("SELECT * FROM ilce WHERE id=:ids");
    $snc->execute(['ids' => $id]);

    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ);
    } else {
        header("Location:index.php?p=bolgeler");
        exit;
    }
} catch (Exception $e) {
    logError($e->getMessage());
    echo '<div class="alert alert-danger" role="alert">' . sanitizeInput($e->getMessage()) . '</div>';
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">İlçe: <?= sanitizeInput($snc->ilce_adi) ?></h4>
            </div>
        </div>

        <div id="VeriEkle" class="modal fade bs-example-modal-lg" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="width:60%; margin-left:auto;margin-right:auto;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Semt Ekle</h4>
                    </div>
                    <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=bolgeler_semt_ekle&ilce_id=<?= sanitizeInput($snc->id) ?>" onsubmit="return false;" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">Adı</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" name="adi" placeholder="">
                                </div>
                            </div>
                            <div id="form_status"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms', 'form_status');">
                                <i class="fa fa-plus" aria-hidden="true"></i> Ekle
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="VeriEkle2" class="modal fade bs-example-modal-lg" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="width:60%; margin-left:auto;margin-right:auto;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Mahalle & Köy Ekle</h4>
                    </div>
                    <form role="form" class="form-horizontal" id="forms2" method="POST" action="ajax.php?p=bolgeler_mahalle_koy_ekle&ilce_id=<?= sanitizeInput($snc->id) ?>" onsubmit="return false;" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-sm-1 control-label">Semt</label>
                                <div class="col-sm-11">
                                    <select class="form-control" name="semt">
                                        <option value="0">Yok</option>
                                        <?php
                                        $semtler = [];
                                        $sql = $db->query("SELECT id, semt_adi FROM semt WHERE ilce_id=" . $snc->id . " ORDER BY semt_adi ASC");
                                        while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                                            $semtler[$row->id] = $row->semt_adi;
                                            echo '<option value="' . sanitizeInput($row->id) . '">' . sanitizeInput($row->semt_adi) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="adi" class="col-sm-1 control-label">Adı</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control" name="adi" placeholder="">
                                </div>
                            </div>
                            <div id="form2_status"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms2', 'form2_status');">
                                <i class="fa fa-plus" aria-hidden="true"></i> Ekle
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- modal end -->

        <div class="row">
            <div class="col-lg-12 col-md-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" data-toggle="modal" data-target="#VeriEkle"> 
                                    <i class="fa fa-plus"></i> Yeni Semt Ekle
                                </button>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" data-toggle="modal" data-target="#VeriEkle2"> 
                                    <i class="fa fa-plus"></i> Yeni Mahalle & Köy Ekle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default m-t-20">
                    <div class="panel-body">
                        <?php
                        if ($hesap->tipi != 2) {
                            $sil = $gvn->rakam($_GET["sil"] ?? '');
                            $sil2 = $gvn->rakam($_GET["sil2"] ?? '');

                            if ($sil != "") {
                                $db->query("DELETE FROM semt WHERE id=" . $sil);
                                header("Location:index.php?p=bolgeler_ilce&id=" . $snc->id);
                                exit;
                            }

                            if ($sil2 != "") {
                                $db->query("DELETE FROM mahalle_koy WHERE id=" . $sil2);
                                header("Location:index.php?p=bolgeler_ilce&id=" . $snc->id);
                                exit;
                            }
                        }
                        ?>

                        <div id="form_statuss"></div>
                        <form role="form" class="form-horizontal" id="formss" method="POST" action="ajax.php?p=bolgeler_ilce&id=<?= sanitizeInput($snc->id) ?>" onsubmit="return false;">
                            <div class="form-group">
                                <label for="ilce_adi" class="col-sm-3 control-label">İlçe Adı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ilce_adi" name="ilce_adi" value="<?= sanitizeInput($snc->ilce_adi) ?>" placeholder="">
                                </div>
                            </div>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('formss', 'form_statuss');">Kaydet</button>
                            </div>
                        </form>
                        <hr />

                        <?php
                        foreach ($semtler as $semt_id => $semt_adi) {
                            $k = $semt_id;
                            $v = $semt_adi;
                        ?>
                        <div id="VeriDuzenle<?= sanitizeInput($k) ?>" class="modal fade bs-example-modal-lg" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="width:60%; margin-left:auto;margin-right:auto;">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Semt Düzenle</h4>
                                    </div>
                                    <form role="form" class="form-horizontal" id="forms<?= sanitizeInput($k) ?>" method="POST" action="ajax.php?p=bolgeler_semt_duzenle&id=<?= sanitizeInput($k) ?>" onsubmit="return false;" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="col-sm-1 control-label">Adı</label>
                                                <div class="col-sm-11">
                                                    <input type="text" class="form-control" name="adi" value="<?= sanitizeInput($v) ?>" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms<?= sanitizeInput($k) ?>', 'form_status<?= sanitizeInput($k) ?>');">
                                                <i class="fa fa-plus" aria-hidden="true"></i> Kaydet
                                            </button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <?php
                        $mahkoyler = [];
                        $sorgu = $db->query("SELECT id, mahalle_adi, semt_id FROM mahalle_koy WHERE ilce_id=" . $snc->id . " ORDER BY id DESC");
                        while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                            $k = $row->id;
                            $v = $row->mahalle_adi;
                            $mahkoyler[$k] = [$row->semt_id, $v];
                        ?>
                        <div id="VeriDuzenle2_<?= sanitizeInput($k) ?>" class="modal fade bs-example-modal-lg" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content" style="width:60%; margin-left:auto;margin-right:auto;">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Mahalle & Köy Düzenle</h4>
                                    </div>
                                    <form role="form" class="form-horizontal" id="forms2_<?= sanitizeInput($k) ?>" method="POST" action="ajax.php?p=bolgeler_mahkoy_duzenle&id=<?= sanitizeInput($k) ?>" onsubmit="return false;" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="col-sm-1 control-label">Semt</label>
                                                <div class="col-sm-11">
                                                    <select class="form-control" name="semt">
                                                        <option value="0">Yok</option>
                                                        <?php
                                                        foreach ($semtler as $semt_id => $semt_adi) {
                                                            echo '<option value="' . sanitizeInput($semt_id) . '" ' . ($semt_id == $row->semt_id ? 'selected' : '') . '>' . sanitizeInput($semt_adi) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-1 control-label">Adı</label>
                                                <div class="col-sm-11">
                                                    <input type="text" class="form-control" name="adi" value="<?= sanitizeInput($v) ?>" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms2_<?= sanitizeInput($k) ?>', 'form_status<?= sanitizeInput($k) ?>');">
                                                <i class="fa fa-plus" aria-hidden="true"></i> Kaydet
                                            </button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Kapat</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>

                        <h3>Semtler</h3>        
                        <table class="table table-hover mails datatable">
                            <thead>
                                <tr>
                                    <th style="display:none">Seç</th>
                                    <th>Semt Adı</th>
                                    <th>Toplam Mahalle & Köy</th>
                                    <th>Kontroller</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($semtler as $semt_id => $semt_adi) {
                                    $topmah = $db->query("SELECT id FROM mahalle_koy WHERE semt_id=" . $semt_id)->rowCount();
                                ?>
                                <tr>
                                    <td class="mail-select" style="display:none">
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkbox<?= sanitizeInput($semt_id) ?>" type="checkbox" name="id[]" value="<?= sanitizeInput($semt_id) ?>">
                                            <label for="checkbox<?= sanitizeInput($semt_id) ?>"></label>
                                        </div>
                                    </td>
                                    <td><?= sanitizeInput($semt_adi) ?></td>
                                    <td><?= sanitizeInput($topmah) ?></td>
                                    <td>
                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#" data-toggle="modal" data-target="#VeriDuzenle<?= sanitizeInput($semt_id) ?>">Düzenle</a></li>
                                                <li><a href="index.php?p=bolgeler_ilce&id=<?= sanitizeInput($snc->id) ?>&sil=<?= sanitizeInput($semt_id) ?>">Sil</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>

                        <hr />

                        <h3>Mahalle & Köyler</h3>        
                        <table class="table table-hover mails datatable">
                            <thead>
                                <tr>
                                    <th style="display:none">Seç</th>
                                    <th>Mahalle Adı</th>
                                    <th>Semt Adı</th>
                                    <th>Kontroller</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($mahkoyler as $mahalle_id => $mah) {
                                    $semt_adi = ($semtler[$mah[0]] != '') ? $semtler[$mah[0]] : "Yok";
                                ?>
                                <tr>
                                    <td class="mail-select" style="display:none">
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkboxx<?= sanitizeInput($mahalle_id) ?>" type="checkbox" name="idd[]" value="<?= sanitizeInput($mahalle_id) ?>">
                                            <label for="checkboxx<?= sanitizeInput($mahalle_id) ?>"></label>
                                        </div>
                                    </td>
                                    <td><?= sanitizeInput($mah[1]) ?></td>
                                    <td><?= sanitizeInput($semt_adi) ?></td>
                                    <td>
                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="#" data-toggle="modal" data-target="#VeriDuzenle2_<?= sanitizeInput($mahalle_id) ?>">Düzenle</a></li>
                                                <li><a href="index.php?p=bolgeler_ilce&id=<?= sanitizeInput($snc->id) ?>&sil2=<?= sanitizeInput($mahalle_id) ?>">Sil</a></li>
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
    </div>
</div>

<script>
    var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
<link href="assets/vendor/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">
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
        text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",   
        type: "warning",   
        showCancelButton: true,   
        confirmButtonColor: "#DD6B55",   
        confirmButtonText: "Evet, Hemen!",   
        closeOnConfirm: false 
    }, function(){   
        $("#SelectForm").submit();
    });
}
</script>