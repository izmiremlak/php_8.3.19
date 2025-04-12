<?php

$db->query("UPDATE onecikan_danismanlar_501 SET durumb='1' WHERE durumb=0");

?>            
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Danışman Öne Çıkartma Satışları</h4>
            </div>
        </div>
					
        <div class="row">
            <div class="col-lg-12 col-md-8">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onclick="TumuOnayla();">Seçilenleri Onayla</a></li>
                                    <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <button type="button" onclick="window.location.href='index.php?p=onecikarma_ayarlar';" class="btn btn-danger waves-effect waves-light"><i class="fa fa-cog"></i> Danışman Öne Çıkartma Ücretleri</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default m-t-20">
                    <div class="panel-body">
                        <?php
                        if($hesap->tipi != 2){
                            $sil = $gvn->rakam($_GET["sil"]);
                            $onayla = $gvn->rakam($_GET["onayla"]);
                            if($sil != ""){
                                $thi = $db->query("SELECT did FROM onecikan_danismanlar_501 WHERE id=".$sil)->fetch(PDO::FETCH_OBJ);
                                $db->query("DELETE FROM onecikan_danismanlar_501 WHERE id=".$sil." ");
                                $db->query("UPDATE hesaplar SET onecikar='0', onecikar_btarih='' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$thi->did);

                                header("Location:index.php?p=onecikan_danismanlar");
                            } elseif($onayla != ""){
                                $sip = $db->query("SELECT * FROM onecikan_danismanlar_501 WHERE id=".$onayla)->fetch(PDO::FETCH_OBJ);
                                $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$sip->acid)->fetch(PDO::FETCH_OBJ);
                                $danisman = $db->query("SELECT id, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$sip->did)->fetch(PDO::FETCH_OBJ);

                                $adsoyad = $hesapp->adi;
                                $adsoyad .= ($hesapp->soyadi != '') ? ' '.$hesapp->soyadi : '';
                                $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
                                $baslik = $danisman->adsoyad." ".dil("PAY_NAME3");

                                $fiyat = $gvn->para_str($sip->tutar)." ".dil("DONECIKAR_PBIRIMI");
                                $neresi = "eklenen-danismanlar";

                                $fonk->bildirim_gonder(array($adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL.$neresi), "siparis_onaylandi", $hesapp->email, $hesapp->telefon);

                                $thi = $sip;
                                $btarih = $thi->btarih;
                                $db->query("UPDATE onecikan_danismanlar_501 SET durum='1' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$onayla." ");
                                $db->query("UPDATE hesaplar SET onecikar='1', onecikar_btarih='".$btarih."' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$thi->did);
                                header("Location:index.php?p=onecikan_danismanlar");
                            }

                            if($_POST){
                                $idler = $_POST["id"];
                                $action = $_POST["action"];

                                if(count($idler) > 0){
                                    foreach($idler as $id){
                                        $id = $gvn->rakam($id);
                                        if($action == "sil"){
                                            $thi = $db->query("SELECT did FROM onecikan_danismanlar_501 WHERE id=".$id)->fetch(PDO::FETCH_OBJ);
                                            $db->query("DELETE FROM onecikan_danismanlar_501 WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$id." ");
                                            $db->query("UPDATE hesaplar SET onecikar='0', onecikar_btarih='' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$thi->did);
                                        } elseif($action == "onayla"){
                                            $sip = $db->query("SELECT * FROM onecikan_danismanlar_501 WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$id)->fetch(PDO::FETCH_OBJ);
                                            $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$sip->acid)->fetch(PDO::FETCH_OBJ);
                                            $danisman = $db->query("SELECT id, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$sip->did)->fetch(PDO::FETCH_OBJ);

                                            $adsoyad = $hesapp->adi;
                                            $adsoyad .= ($hesapp->soyadi != '') ? ' '.$hesapp->soyadi : '';
                                            $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
                                            $baslik = $danisman->adsoyad." ".dil("PAY_NAME3");

                                            $fiyat = $gvn->para_str($sip->tutar)." ".dil("DONECIKAR_PBIRIMI");
                                            $neresi = "eklenen-danismanlar";

                                            $fonk->bildirim_gonder(array($adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL.$neresi), "siparis_onaylandi", $hesapp->email, $hesapp->telefon);

                                            $thi = $sip;
                                            $btarih = $thi->btarih;
                                            $db->query("UPDATE onecikan_danismanlar_501 SET durum='1' WHERE id=".$id." ");
                                            $db->query("UPDATE hesaplar SET onecikar='1', onecikar_btarih='".$btarih."' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$thi->did);
                                        }
                                    } // FOREACH END
                                } // eğer varsa

                                header("Location:index.php?p=onecikan_danismanlar");
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
                                        <th>Danışman</th>
                                        <th>Tarih</th>
                                        <th>Tutar</th>
                                        <th>Ödeme Yöntemi</th>
                                        <th>Durum</th>
                                        <th width="15%">Kontroller</th>
                                    </tr>
                                </thead>
                                        
                                <tbody>
                                    <?php
                                    $detaylar = array();
                                    $danismanlar = array();
                                    $sorgu = $db->query("SELECT * FROM onecikan_danismanlar_501 ORDER BY durum ASC, id DESC LIMIT 0,500");
                                    while($row = $sorgu->fetch(PDO::FETCH_OBJ)){
                                        $detaylar[] = $row;
                                        $uye = $db->prepare("SELECT id, unvan, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                        $uye->execute(array($row->acid));
                                        if($uye->rowCount() > 0){
                                            $uye = $uye->fetch(PDO::FETCH_OBJ);
                                            $name = ($uye->unvan == '') ? $uye->adsoyad : $uye->unvan;
                                        }
                                        
                                        $danisman = $db->prepare("SELECT id, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                        $danisman->execute(array($row->did));
                                        if($danisman->rowCount() > 0){
                                            $danisman = $danisman->fetch(PDO::FETCH_OBJ);
                                            $danismanad = $danisman->adsoyad;
                                            $danismanlar[$row->id] = $danisman;
                                        }
                                    ?>
                                    <tr>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?=$row->id;?>" type="checkbox" name="id[]" value="<?=$row->id;?>">
                                                <label for="checkbox<?=$row->id;?>"></label>
                                            </div>
                                        </td>
                                        <td><a href="index.php?p=uye_duzenle&id=<?=$uye->id;?>" target="_blank"><?=$name;?></a></td>
                                        <td><a href="index.php?p=uye_duzenle&id=<?=$danisman->id;?>" target="_blank"><?=$danismanad;?></a></td>
                                        <td><?=date("d.m.Y H:i", strtotime($row->tarih));?></td>
                                        <td><strong title="<?=$row->sure." ".$periyod[$row->periyod];?>"><?=$gvn->para_str($row->tutar);?> <?=dil("DONECIKAR_PBIRIMI");?></strong></td>
                                        <td><?=$row->odeme_yontemi;?></td>
                                        <td><?php
                                            echo ($row->durum == 0) ? '<strong style="color:red">Onay Bekleniyor</strong>' : ''; 
                                            echo ($row->durum == 1) ? '<strong style="color:green">Onaylandı</strong>' : ''; 
                                            echo ($row->durum == 2) ? '<strong style="color:black">İptal Edildi</strong>' : ''; 
                                        ?></td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                                                <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#" data-toggle="modal" data-target="#Detay<?=$row->id;?>">Detaylar</a></li>
                                                    <li><a href="index.php?p=onecikan_danismanlar&onayla=<?=$row->id;?>">Onayla</a></li>
                                                    <li><a href="index.php?p=onecikan_danismanlar&sil=<?=$row->id;?>">Sil</a></li>
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
foreach($detaylar as $detay){
$id = $detay->id;
$danisman = $danismanlar[$id];
?>
<div id="Detay<?=$id;?>" class="modal fade detay-modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Sipariş Detayları</h4>
            </div>
            <div class="modal-body">
                <div id="form<?=$id;?>_status"></div>
                <form role="form" class="form-horizontal" id="forms<?=$id;?>" method="POST" action="ajax.php?p=onecikan_danisman_duzenle&id=<?=$id;?>" onsubmit="return false;" enctype="multipart/form-data">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Danışman</label>
                        <div class="col-sm-9">
                            <span style="display:block; margin-top:7px;">
                                <a href="index.php?p=uye_duzenle&id=<?=$danisman->id;?>" target="_blank"><?=$danisman->adsoyad;?></a>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Durum:</label>
                        <div class="col-sm-9">
                            <div class="radio radio-info radio-inline">
                                <input type="radio" id="durum1_<?=$id;?>" value="1" name="durum" <?=($detay->durum == 1) ? 'checked' : '';?>>
                                <label for="durum1_<?=$id;?>">Onaylandı</label>
                            </div>
                            <div class="radio radio-info radio-inline">
                                <input type="radio" id="durum2_<?=$id;?>" value="2" name="durum" <?=($detay->durum == 2) ? 'checked' : '';?>>
                                <label for="durum2_<?=$id;?>">İptal Edildi</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Ödeme Yöntemi</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="odeme_yontemi">
                                <?php
                                foreach($oyontemleri as $yontem){
                                ?><option <?=($detay->odeme_yontemi == $yontem) ? 'selected' : ''; ?>><?=$yontem;?></option><?
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tutar</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="tutar" value="<?=$gvn->para_str($detay->tutar);?>" placeholder="">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Bitiş Süresi</label>
                        <div class="col-sm-1">
                            <input type="text" class="form-control" name="sure" value="<?=$detay->sure;?>" placeholder="Süre">
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control" name="periyod">
                                <?php
                                foreach($periyod AS $k=>$v){
                                ?><option value="<?=$k;?>"<?=($detay->periyod == $k) ? " selected" : '';?>><?=$v;?></option><?
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="btarih" value="<?=($detay->btarih == '') ? '' : date("d.m.Y", strtotime($detay->btarih));?>" placeholder="Bitiş Tarihi Örn:25.05.2017">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="AjaxFormS('forms<?=$id;?>', 'form<?=$id;?>_status');"><i class="fa fa-check"></i> Kaydet</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Kapat</button>
            </div>
        </div>
    </div>
</div>
<? } ?>
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
						