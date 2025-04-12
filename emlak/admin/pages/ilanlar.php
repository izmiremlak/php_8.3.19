<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">İlanlar</h4>
            </div>
        </div>

        <div class="panel-group" id="accordion-test-2">
            <div class="panel panel-pink panel-color">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseOne-2" aria-expanded="true" class="collapsed">
                            Açıklamalar ve Talimatlar
                        </a>
                    </h4>
                </div>
                <div id="collapseOne-2" class="panel-collapse collapse" aria-expanded="true">
                    <div class="panel-body">
                        Bu alanda, tarafınızdan eklenmiş ilanlar ile üyelerin ekledikleri ilanları görebilirsiniz. Ayrıca üyeler her ilan ekleme ve güncelleme işlemi yaptığında, bu alanda tekrar sizin onayınıza sunulmaktadır. Onayladığınız taktirde site tarafında yayınlanmaktadır. Onaylanmayan ilanlar hiçbir şekilde yayınlanmaz. Ek olarak, onay bekleyen ilanları, ilanı ekleyen üye ve admin site tarafında görüntüleyebilir, fakat üçüncü bir ziyaretçi veya botlar göremez.
                    </div>
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
                                    <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=ilan_ekle';"> <i class="fa fa-plus"></i> Yeni Ekle</button>
                                </div>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onclick="TumuOnayla();">Seçilenleri Onayla</a></li>
                                        <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default m-t-20">
                        <div class="panel-body">
                            <?php
                            if ($hesap->tipi != 2) {
                                $sil = $gvn->rakam($_GET["sil"]);
                                if ($sil != "") {
                                    $snc = $db->prepare("SELECT id, video, ilan_no FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                    $snc->execute([$sil]);
                                    if ($snc->rowCount() > 0) {
                                        $snc = $snc->fetch(PDO::FETCH_OBJ);
                                        $multi = $db->query("SELECT id, ilan_no FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . $snc->ilan_no . " ORDER BY id ASC");
                                        $multif = $multi->fetch(PDO::FETCH_OBJ);
                                        $multidids = $db->query("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . $snc->ilan_no)->fetch(PDO::FETCH_OBJ)->ids;
                                        $mulid = ($multi->rowCount() > 1 && $snc->id == $multif->id) ? " IN(" . $multidids . ")" : "=" . $snc->id;

                                        $db->query("DELETE FROM sayfalar WHERE site_id_555=501 AND id" . $mulid);
                                        if ($snc->video != '') {
                                            $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $snc->video;
                                            if (file_exists($nirde)) {
                                                unlink($nirde);
                                            }
                                        }
                                        $quu = $db->query("SELECT id, resim FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id" . $mulid);
                                        while ($row = $quu->fetch(PDO::FETCH_OBJ)) {
                                            $pinfo = pathinfo("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/" . $row->resim);
                                            $folder = $pinfo["dirname"] . "/";
                                            $ext = $pinfo["extension"];
                                            $fname = $pinfo["filename"];
                                            $bname = $pinfo["basename"];

                                            @unlink($folder . "thumb/" . $bname);
                                            @unlink($folder . $bname);
                                            @unlink($folder . $fname . "_original." . $ext);
                                        }
                                        $db->query("DELETE FROM galeri_foto WHERE site_id_555=501 AND sayfa_id" . $mulid);
                                    }
                                    header("Location:index.php?p=ilanlar");
                                }

                                if ($_POST) {
                                    $idler = $_POST["id"];
                                    $action = $_POST["action"];

                                    if (count($idler) > 0) {
                                        foreach ($idler as $id) {
                                            $id = $gvn->rakam($id);
                                            if ($action == 'sil') {
                                                $snc = $db->prepare("SELECT id, video FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                                $snc->execute([$id]);
                                                if ($snc->rowCount() > 0) {
                                                    $snc = $snc->fetch(PDO::FETCH_OBJ);
                                                    $db->query("DELETE FROM sayfalar WHERE site_id_555=501 AND id=" . $snc->id);
                                                    if ($snc->video != '') {
                                                        $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $snc->video;
                                                        if (file_exists($nirde)) {
                                                            unlink($nirde);
                                                        }
                                                    }
                                                    $quu = $db->query("SELECT id, resim FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . $snc->id);
                                                    while ($row = $quu->fetch(PDO::FETCH_OBJ)) {
                                                        $pinfo = pathinfo("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/" . $row->resim);
                                                        $folder = $pinfo["dirname"] . "/";
                                                        $ext = $pinfo["extension"];
                                                        $fname = $pinfo["filename"];
                                                        $bname = $pinfo["basename"];

                                                        @unlink($folder . "thumb/" . $bname);
                                                        @unlink($folder . $bname);
                                                        @unlink($folder . $fname . "_original." . $ext);
                                                    }
                                                    $db->query("DELETE FROM galeri_foto WHERE site_id_555=501 AND sayfa_id=" . $snc->id);
                                                }
                                            } elseif ($action == 'onayla') {
                                                try {
                                                    $snc = $db->prepare("SELECT id, acid, tarih, baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                                    $snc->execute([$id]);
                                                } catch (PDOException $e) {
                                                    die($e->getMessage());
                                                }
                                                if ($snc->rowCount() > 0) {
                                                    $snc = $snc->fetch(PDO::FETCH_OBJ);
                                                    $acc = $db->query("SELECT id, CONCAT_WS(' ', adi, soyadi) AS adsoyad, unvan, email, telefon FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
                                                    $db->query("UPDATE sayfalar SET durum='1' WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->id);
                                                    $dnolms = explode(" | ", dil("NOLMUS"));
                                                    $nolms = $dnolms[1];
                                                    $otarih = date("d.m.Y", strtotime($snc->tarih));
                                                    $gtarih = date("d.m.Y");
                                                    $fonk->bildirim_gonder([$acc->adsoyad, $snc->id, $snc->baslik, $nolms, date("d.m.Y H:i")], "ilan_durumu", $acc->email, $acc->telefon);
                                                } else {
                                                    echo "Böyle bir ilan yok! #" . $id;
                                                }
                                            } else {
                                                echo "Action : " . $action;
                                            }
                                        }
                                    }
                                    header("Location:index.php?p=ilanlar");
                                }
                            } // tipi 0 değilse
                            ?>

                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th>Seç</th>
                                        <th>Ekleyen</th>
                                        <th width="30%">Başlık</th>
                                        <th>Fiyat</th>
                                        <th>Durum</th>
                                        <th>Hit</th>
                                        <th title="Güncel Tarih">G.Tarih</th>
                                        <th>Kontroller</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $sorgu		= $db->query("SELECT ilan_no,id,fiyat,baslik,gtarih,durum,acid,pbirim,url,hit FROM sayfalar WHERE (site_id_555=501 OR (site_id_888=100 AND durum=1 AND il_id=35) OR (site_id_777=501501 AND durum=1) OR (site_id_699=200 AND durum=1 AND il_id=35) OR (site_id_701=501501 AND durum=1) OR (site_id_702=300 AND durum=1)) AND tipi=4 AND ekleme=1 AND dil='".$dil."' ORDER BY durum ASC,id DESC LIMIT 0,500");
                                    while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                                        $ekleyen = $db->prepare("SELECT id, CONCAT_WS(' ', adi, soyadi) AS adsoyad, unvan FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
                                        $ekleyen->execute([$row->acid]);
                                        if ($ekleyen->rowCount() > 0) {
                                            $ekleyen = $ekleyen->fetch(PDO::FETCH_OBJ);
                                            $uye_name = ($ekleyen->unvan != '') ? $ekleyen->unvan : $ekleyen->adsoyad;
                                        }

                                        $fiyat_int = $gvn->para_int($row->fiyat);
                                        $fiyat = $gvn->para_str($fiyat_int);
                                        $link = ($dayarlar->permalink == 'Evet') ? $row->url . ".html" : "index.php?p=sayfa&id=" . $row->id;

                                        $isexpire = $db->query("SELECT DISTINCT t1.id FROM sayfalar AS t1 LEFT JOIN dopingler_501 AS t2 ON t2.ilan_id=t1.id AND t2.durum=1 WHERE (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.id=" . $row->id . " AND (t1.btarih>NOW() OR t2.btarih>NOW())");
                                        $surebitis = ($isexpire->rowCount() > 0) ? true : false;
                                    ?>
                                    <tr>
                                        <td class="mail-select">
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox<?=$row->id;?>" type="checkbox" name="id[]" value="<?=$row->id;?>">
                                                <label for="checkbox<?=$row->id;?>"></label>
                                            </div>
                                        </td>


<td>
    <a href="index.php?p=uye_duzenle&id=<?=$ekleyen->id;?>"><?=$fonk->kisalt2($uye_name, 0, 20);?></a>
</td>
<td width="30%">
    <a href="../<?=$link;?>" target="_blank"><?=$fonk->kisalt2($row->baslik, 0, 35);?></a><br />
    İlan No: <strong><?=$row->ilan_no;?></strong>
</td>
<td>
    <strong><?=$fiyat . ' ' . $row->pbirim;?></strong>
</td>
<td>
    <?php
    if ($row->durum == 0) {
        ?><span style="color:red;font-weight:bold;">Onay Bekliyor</span><?php
    } elseif (!$surebitis) {
        ?><span style="color:orange;font-weight:bold;">Süresi Doldu</span><?php
    } elseif ($row->durum == 1) {
        ?><span style="color:green;font-weight:bold;">Yayında</span><?php
    } elseif ($row->durum == 2) {
        ?><span style="color:green;font-weight:bold;">Reddedildi</span><?php
    } elseif ($row->durum == 3) {
        ?><span style="color:orange;font-weight:bold;">Pasif</span><?php
    }
    ?>
</td>
<td><?=$row->hit;?></td>
<td><?=date("d.m.Y", strtotime($row->gtarih));?></td>
<td>
    <div class="btn-group dropdown">
        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
        <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
        <ul class="dropdown-menu" role="menu">
            <li><a href="index.php?p=ilan_duzenle&id=<?=$row->id;?>">Görüntüle / Düzenle</a></li>
            <li><a href="#" onclick="TumuSil();">Sil</a></li>
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
        title: "Silme İşlemi",
        text: "Bu işlemi gerçekten yapmak istiyor musunuz ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Evet, Hemen!",
        closeOnConfirm: false
    }, function() {
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
    }, function() {
        $("#SelectForm").submit();
    });
}
</script>