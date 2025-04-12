<?php

// Satılan dopinglerin yönetildiği sayfa

// Veritabanı bağlantısı ve güvenlik işlemleri
require_once 'config.php'; // Veritabanı ve diğer ayarların yüklendiği dosya

// Hata loglama fonksiyonu
function logError($errorMessage) {
    error_log($errorMessage, 3, '/path/to/error.log');
}

function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Hata işleme
try {
    $db->query("UPDATE dopingler_group_501 SET durumb='1' WHERE durumb=0");
} catch (Exception $e) {
    logError($e->getMessage());
    echo '<div class="alert alert-danger" role="alert">' . sanitizeInput($e->getMessage()) . '</div>';
}
?>            
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Satılan Dopingler</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onclick="TumuOnayla();">Seçilenleri Onayla</a></li>
                                        <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                    </ul>
                                </button>
                            </div>
                            <div class="btn-group">
                                <button type="button" onclick="window.location.href='index.php?p=doping_ayarlar';" class="btn btn-danger waves-effect waves-light"><i class="fa fa-cog"></i> Doping Ayarları</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default m-t-20">
                    <div class="panel-body">
                        <?php
                        if ($hesap->tipi != 2) {
                            $sil = $gvn->rakam($_GET["sil"]);
                            $onayla = $gvn->rakam($_GET["onayla"]);
                            if ($sil != "") {
                                $db->query("DELETE FROM dopingler_group_501 WHERE id=" . $sil);
                                $db->query("DELETE FROM dopingler_501 WHERE gid=" . $sil);
                                header("Location:index.php?p=dopingler");
                            } elseif ($onayla != "") {
                                $sip = $db->query("SELECT * FROM dopingler_group_501 WHERE id=" . $onayla)->fetch(PDO::FETCH_OBJ);
                                $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $sip->acid)->fetch(PDO::FETCH_OBJ);
                                $snc = $db->query("SELECT id, baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $sip->ilan_id)->fetch(PDO::FETCH_OBJ);

                                $adsoyad = $hesapp->adi;
                                $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
                                $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
                                $baslik = $snc->baslik . " " . dil("PAY_NAME");

                                $fiyat = $gvn->para_str($sip->tutar) . " " . dil("DOPING_PBIRIMI");
                                $neresi = "dopinglerim";

                                $fonk->bildirim_gonder(array($adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($sip->tarih)), SITE_URL . $neresi), "siparis_onay");

                                $db->query("UPDATE dopingler_group_501 SET durum='1' WHERE id=" . $onayla);
                                $db->query("UPDATE dopingler_501 SET durum='1' WHERE gid=" . $onayla);
                            }

                            if ($_POST) {
                                $idler = $_POST["id"];
                                $action = $_POST["action"];

                                if (count($idler) > 0) {
                                    foreach ($idler as $id) {
                                        $id = $gvn->rakam($id);
                                        if ($action == 'sil') {
                                            $db->query("DELETE FROM dopingler_group_501 WHERE id=" . $id);
                                            $db->query("DELETE FROM dopingler_501 WHERE gid=" . $id);
                                        } elseif ($action == 'onayla') {
                                            $sip = $db->query("SELECT * FROM dopingler_group_501 WHERE id=" . $id)->fetch(PDO::FETCH_OBJ);
                                            $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $sip->acid)->fetch(PDO::FETCH_OBJ);
                                            $snc = $db->query("SELECT id, baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $sip->ilan_id)->fetch(PDO::FETCH_OBJ);

                                            $adsoyad = $hesapp->adi;
                                            $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
                                            $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
                                            $baslik = $snc->baslik . " " . dil("PAY_NAME");

                                            $fiyat = $gvn->para_str($sip->tutar) . " " . dil("DOPING_PBIRIMI");
                                            $neresi = "dopinglerim";

                                            $fonk->bildirim_gonder(array($adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi), "siparis_onay");

                                            $db->query("UPDATE dopingler_group_501 SET durum='1' WHERE id=" . $id);
                                            $db->query("UPDATE dopingler_501 SET durum='1' WHERE gid=" . $id);
                                        }
                                    } // FOREACH END
                                } // eğer varsa
                                header("Location:index.php?p=dopingler");
                            }
                        } // tipi 0 değilse
                        ?>
                        <form action="" method="POST" id="SelectForm">
                            <input type="hidden" name="action" value="" id="action_hidden">
                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th width="6%">Seç</th>
                                        <th width="10%">Üye</th>
                                        <th>İlan</th>
                                        <th>Alış Tarihi</th>
                                        <th>Tutar</th>
                                        <th>Ödeme Yöntemi</th>
                                        <th>Durum</th>
                                        <th width="15%">Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $gids = array();
                                    $ilanlar = array();
                                    $sorgu = $db->query("SELECT * FROM dopingler_group_501 ORDER BY durum ASC, id DESC LIMIT 0,500");
                                    while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                                        $gids[] = $row;
                                        $uye = $db->prepare("SELECT id, unvan, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                        $uye->execute(array($row->acid));
                                        if ($uye->rowCount() > 0) {
                                            $uye = $uye->fetch(PDO::FETCH_OBJ);
                                            $name = ($uye->unvan == '') ? $uye->adsoyad : $uye->unvan;
                                        }

                                        $ilan = $db->prepare("SELECT id, url, baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                        $ilan->execute(array($row->ilan_id));
                                        if ($ilan->rowCount() > 0) {
                                            $ilan = $ilan->fetch(PDO::FETCH_OBJ);
                                            $baslik = $ilan->baslik;
                                            $ilanlar[$row->id] = $ilan;
                                        }
                                    ?>
                                    <tr>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?= sanitizeInput($row->id) ?>" type="checkbox" name="id[]" value="<?= sanitizeInput($row->id) ?>">
                                                <label for="checkbox<?= sanitizeInput($row->id) ?>"></label>
                                            </div>
                                        </td>
                                        <td><a href="index.php?p=uye_duzenle&id=<?= sanitizeInput($uye->id) ?>" target="_blank"><?= sanitizeInput($name) ?></a></td>
                                        <td><a href="index.php?p=ilan_duzenle&id=<?= sanitizeInput($ilan->id) ?>" target="_blank"><?= sanitizeInput($baslik) ?></a></td>
                                        <td><?= date("d.m.Y H:i", strtotime($row->tarih)) ?></td>
                                        <td><strong><?= sanitizeInput($gvn->para_str($row->tutar)) ?> <?= dil("DOPING_PBIRIMI") ?></strong></td>
                                        <td><?= sanitizeInput($row->odeme_yontemi) ?></td>
                                        <td id="doping<?= sanitizeInput($row->id) ?>_durum"><?php
                                            echo ($row->durum == 0) ? '<strong style="color:red">Onay Bekleniyor</strong>' : '';
                                            echo ($row->durum == 1) ? '<strong style="color:green">Onaylandı</strong>' : '';
                                            echo ($row->durum == 2) ? '<strong style="color:black">İptal Edildi</strong>' : '';
                                        ?></td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#" data-toggle="modal" data-target="#Group<?= sanitizeInput($row->id) ?>">Detaylar</a></li>
                                                    <li><a href="javascript:if(confirm('Gerçekten onaylamak istiyor musunuz?')){ajaxHere('ajax.php?p=dopingler&onayla=<?= sanitizeInput($row->id) ?>','hidden_result');}">Onayla</a></li>
                                                    <li><a href="index.php?p=dopingler&sil=<?= sanitizeInput($row->id) ?>">Sil</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </form>
                        <?php
                        foreach ($gids as $group) {
                            $id = $group->id;
                            $ilan = $ilanlar[$id];
                        ?>
                        <div id="Group<?= sanitizeInput($id) ?>" class="modal fade detay-modal" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Doping Detayları</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id="form<?= sanitizeInput($id) ?>_status"></div>
                                        <form role="form" class="form-horizontal" id="forms<?= sanitizeInput($id) ?>" method="POST" action="ajax.php?p=doping_duzenle&id=<?= sanitizeInput($id) ?>" onsubmit="return false;" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">İlan</label>
                                                <div class="col-sm-9">
                                                    <span style="display:block; margin-top:7px;">
                                                        <a href="index.php?p=ilan_duzenle&id=<?= sanitizeInput($ilan->id) ?>" target="_blank"><?= sanitizeInput($ilan->baslik) ?></a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Durum:</label>
                                                <div class="col-sm-9">
                                                    <div class="radio radio-info radio-inline">
                                                        <input type="radio" id="durum1_<?= sanitizeInput($id) ?>" value="1" name="durum" <?= ($group->durum == 1) ? 'checked' : ''; ?>>
                                                        <label for="durum1_<?= sanitizeInput($id) ?>">Onaylandı</label>
                                                    </div>
                                                    <div class="radio radio-info radio-inline">
                                                        <input type="radio" id="durum2_<?= sanitizeInput($id) ?>" value="2" name="durum" <?= ($group->durum == 2) ? 'checked' : ''; ?>>
                                                        <label for="durum2_<?= sanitizeInput($id) ?>">İptal Edildi</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Ödeme Yöntemi</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="odeme_yontemi">
                                                        <?php
                                                        foreach ($oyontemleri as $yontem) {
                                                            echo '<option ' . ($group->odeme_yontemi == $yontem ? 'selected' : '') . '>' . sanitizeInput($yontem) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Toplam Tutar</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="xtutar" value="<?= sanitizeInput($gvn->para_str($group->tutar)) ?>" placeholder="">
                                                </div>
                                            </div>
                                            <h3>Doping Ayarları</h3>
                                            <div class="form-group">
                                                <div class="col-sm-3">Doping Adı</div>
                                                <div class="col-sm-1">Süre</div>
                                                <div class="col-sm-2">Periyod</div>
                                                <div class="col-sm-2">Tutar</div>
                                                <div class="col-sm-4">Bitiş Tarihi</div>
                                            </div>
                                            <?php
                                            $bugun = date("Y-m-d");
                                            $sec = 0;
                                            $dopingler_501 = $db->query("SELECT * FROM dopingler_501 WHERE gid=" . $id . " ORDER BY did ASC");
                                            while ($row = $dopingler_501->fetch(PDO::FETCH_OBJ)) {
                                            ?>
                                            <input type="hidden" name="ids[]" value="<?= sanitizeInput($row->id) ?>">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label"><?= sanitizeInput($row->adi) ?></label>
                                                <div class="col-sm-1">
                                                    <input type="text" class="form-control" name="sure[<?= sanitizeInput($row->id) ?>]" value="<?= sanitizeInput($row->sure) ?>" placeholder="Süre">
                                                </div>
                                                <div class="col-sm-2">
                                                    <select class="form-control" name="periyod[<?= sanitizeInput($row->id) ?>]">
                                                        <?php
                                                        foreach ($periyod as $k => $v) {
                                                            echo '<option value="' . sanitizeInput($k) . '"' . ($row->periyod == $k ? ' selected' : '') . '>' . sanitizeInput($v) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <input type="text" class="form-control" name="tutar[<?= sanitizeInput($row->id) ?>]" value="<?= sanitizeInput($gvn->para_str($row->tutar)) ?>" placeholder="">
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="btarih[<?= sanitizeInput($row->id) ?>]" value="<?= ($row->btarih == '') ? '' : date("d.m.Y", strtotime($row->btarih)) ?>" placeholder="">
                                                </div>
                                            </div>
                                            <?php
                                            }
                                            ?>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success" onclick="AjaxFormS('forms<?= sanitizeInput($id) ?>','form<?= sanitizeInput($id) ?>_status');"><i class="fa fa-check"></i> Kaydet</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Kapat</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div><!-- Col1 end -->
        </div><!-- row end -->
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

    function TumuOnayla() {
        $("#action_hidden").val("onayla");
        swal({   
            title: "Seçilenleri Onayla",   
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
<style type="text/css">
body .modal {
  width: 90%; /* desired relative width */
  left: 5%; /* (100%-width)/2 */
  /* place center */
  margin-left:auto;
  margin-right:auto; 
}
.modal .modal-dialog { width: 80%; }
</style>
<div id="hidden_result" style="display:none"></div>
