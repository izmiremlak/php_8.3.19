<?php
// Hata raporlama seviyesini ayarla ve hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt'); // Hataları bu dosyaya yaz

// Rastgele bir ilan numarası oluştur
$ilan_no = random_int(10000000, 99999999);

// Güvenli rakam çekme fonksiyonu ile acid ve id parametrelerini al
$acid = $gvn->rakam(filter_input(INPUT_GET, 'acid', FILTER_SANITIZE_NUMBER_INT));
$id = $gvn->rakam(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));

// Eğer id boş değilse
if ($id !== '') {
    try {
        // Veritabanından sayfa bilgilerini çek
        $snc = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = :ids");
        $snc->execute(['ids' => $id]);

        // Eğer sonuç varsa
        if ($snc->rowCount() > 0) {
            $snc = $snc->fetch(PDO::FETCH_OBJ);
        } else {
            // Yoksa ilan ekle sayfasına yönlendir
            header("Location: index.php?p=ilan_ekle");
            exit;
        }
    } catch (PDOException $e) {
        // Hata mesajını log dosyasına yaz ve ekranda göster
        error_log($e->getMessage());
        echo "Veritabanı hatası: " . $e->getMessage();
    }
}

// Eğer acid boş değilse
if ($acid !== '') {
    try {
        // Veritabanından hesap bilgilerini çek
        $kontrol = $db->prepare("SELECT id, adi, soyadi, CONCAT_WS(' ', adi, soyadi) AS tam_ad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = ?");
        $kontrol->execute([$acid]);

        // Eğer sonuç varsa
        if ($kontrol->rowCount() > 0) {
            $acc = $kontrol->fetch(PDO::FETCH_OBJ);
        }
    } catch (PDOException $e) {
        // Hata mesajını log dosyasına yaz ve ekranda göster
        error_log($e->getMessage());
        echo "Veritabanı hatası: " . $e->getMessage();
    }
}
?>

<!-- HTML İçeriği Başlıyor -->

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Yeni İlan Ekle</h4>
            </div>
        </div>

        <div class="panel-group" id="accordion-test-2">
            <div class="panel panel-pink panel-color">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseOne-2" aria-expanded="true" class="collapsed">
                            İLAN PAYLAŞIMI ve PORTALLARDA İLAN YAYINLAMA AÇIKLAMASI
                        </a>
                    </h4>
                </div>
                <div id="collapseOne-2" class="panel-collapse collapse" aria-expanded="true">
                    <div class="panel-body">
                        <font color="red"><strong> SÖZLEŞMELİ İLAN PAYLAŞIMI </strong></font><br/>
                        <font color="black">Elinizdeki Sözleşmeli İlanlarınızı<font color="red"> Üyelerimiz</font> ile Paylaşabilir, Onların Paylaştığı Sözleşmeli İlanları, Kendi İlanınız gibi, Müşterilerinize Sunabilirsiniz. </font><br/>
                        <font color="red"><strong> ÖNEMLİ : </strong></font> <font color="black">Paylaştığınız İlanlar, Paylaştığınız Üyelerin sitelerinde Yayınlanacağı için 
                        İlanın içinde Sizi Çağrıştıracak, Firma İsmi, Telefon, Logo vs Olmamasına Lütfen Dikkat Ediniz.<br/>
                        İlan Açıklamanızın en altına</font> <font color="red"><strong> Bu İlan, Yetkilisi Tarafından Paylaşıma Açılmıştır</strong> </font>Yazınız.</font><br/><br/>
                        <font color="red"><strong>KAPALI PORTFÖY İLAN PAYLAŞIMI </strong></font><br/>
                        <font color="black">Bildiğiniz gibi 1 Ocak 2024 tarihinden itibaren Sözleşme Yapılmayan İlanlar, Emlak Portallarında Yayınlanamayacaktır</font><br/>
                        Bu nedenle Sözleşme yapamadığınız, Sözlü olarak onay aldığınız veya Sitelerde Yayınlamak İstemediğiniz</font><br/><br/>
                        <font color="red"><strong> Kapalı Portföy İlanlarınızı Sitenize Girerek Saklayabilir, Linkini Müşterinizle Paylaşabilirsiniz.</strong></font><br/>
                        <font color="black"> İsterseniz, Birlikte Çalıştığınız Emlakcılar ve Emlak Danışmanları ile Grup Oluşturarak, Aranızda Paylaşabilirsiniz.</font><br/>
                        <font color="red"><strong> ÖNEMLİ : </strong></font> <font color="black"> Sitenize girdiğiniz veya Paylaştığınız KAPALI PORTFÖY İlanlarınız, Hiçbir Sitede Yayınlanmaz. Sitelerinize giren hiç kimse tarafından Görülmez.</font><br/><br/>
                        <font color="red"><strong> İLANLARINIZIN PORTALLARIMIZDA YAYINLANMASI</strong></font><br/>
                        <font color="black"> İlanlarınızı, Sitenize Girerken İşaretleyerek </font><font color="red"><strong> www.izmiremlaksitesi.com.tr – www.istanbulemlaksitesi.com.tr – www.ankaraemlaksitesi.com.tr</strong></font> <font color="black">  Emlak Portallarımızda, Yayınlayabilirsiniz.</font><br/>
                        <font color="black">  İlanlarınız </font><font color="red"><strong> www.turkiyeemlaksitesi.com.tr </strong></font> <font color="black">  Ana Emlak Portalımızda Otomatik Yayınlanır. Siz silince Her Yerden Silinir.</font><br/>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .form-group .ilanozellik span {color: #555;}
            .form-group .ilanozellik span label input {float: left; margin-right: 5px;}
        </style>

        <div class="row">
            <!-- Col 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <!-- İlan Aşamaları start -->
                        <div class="asamaline"></div>
                        <div class="ilanasamalar">
                            <div class="ilanasamax"><center><h3>1</h3><div class="clear"></div><?= dil("TX527"); ?></center></div>
                            <div class="ilanasamax"><center><h3>2</h3><div class="clear"></div><?= dil("TX528"); ?></center></div>
                            <div class="ilanasamax"><center><h3>3</h3><div class="clear"></div><?= dil("TX530"); ?></center></div>
                            <?php if ($gayarlar->dopingler_501 == 1) { ?>
                                <div class="ilanasamax"><center><h3>4</h3><div class="clear"></div><?= dil("TX531"); ?></center></div>
                            <?php } ?>
                            <div class="ilanasamax islem_tamam"><center><h3><?= ($gayarlar->dopingler_501 == 1) ? 5 : 4; ?></h3><div class="clear"></div><?= dil("TX532"); ?></center></div>
                        </div>
                        <!-- İlan Aşamaları END -->

                        <?php
                        $asama = $gvn->rakam(filter_input(INPUT_GET, 'asama', FILTER_SANITIZE_NUMBER_INT));

                        if ($id !== '') {
                            if ($asama == 0 || $asama == '') { // aşama 0 foto galeri ayarı...
                                $gurl = "ajax.php?p=ilan_duzenle&id=" . $id . "&galeri=1";

                                $yfotolar = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id = " . $snc->id . " ORDER BY sira ASC");
                                $yfotolarcnt = $yfotolar->rowCount();
                        ?>
                                <div id="galeri_foto_ekle">
                                    <script type="text/javascript">
                                        function YuklemeBitti() {
                                            window.location.href = "index.php?p=ilan_ekle&id=<?= $id; ?>";
                                        }
                                    </script>
                                    <h4 style="font-weight:bold;font-size:18px;">İlana ait Fotoğraflar Yükleyin;</h4>
                                    <div class="alert alert-info" role="alert">
                                        <strong style="color:red;">İlan Fotoğraflarının Uzantısı .jpeg Olmalıdır.</strong><br>
                                        <strong style="color:red;">İlan fotoğraflarını 600x800 pixel veya 900x1200 pixel olarak ve Yatay çekerseniz, Fotoğraflar sitenizde daha güzel gözükür</strong><br>
                                        Sistem, yüklenen fotoğrafları filtreden geçirerek otomatik olarak tekrar boyutlandırmaktadır.<br>
                                        Yükleme yaptıktan sonra kapak fotoğrafını değiştirebilir ve Fotoğrafların yerlerini, kaydırarak sıralayabilirsiniz.
                                    </div>
                                    <div class="m-b-30">
                                        <form action="#" class="dropzone" id="dropzone">
                                            <div class="fallback">
                                                <input name="file" type="file" multiple="multiple">
                                            </div>
                                        </form>
                                    </div>
                                    <h4 style="font-weight:bold;font-size:18px;">Kapak Fotoğrafı Seçin;</h4>
                                    <div class="alert alert-info" role="alert">
                                        İlanınız için, yüklediğiniz fotoğraflardan bir kapak görseli seçmeyi unutmayın. Fotoğrafların Yerlerini kaydırarak sıralayabilirsiniz.
                                    </div>
                                    <div id="silsnc"></div>
                                    <form role="form" class="form-horizontal" action="ajax.php?p=galeri_guncelle&ilan_id=<?= $snc->id; ?>&from=insert" method="POST" id="GaleriGuncelleForm">
                                        <div class="row port">
                                            <div class="portfolioContainer">
                                                <ul id="list" class="uk-nestable" data-uk-nestable="{maxDepth:1}">
                                                    <?php
                                                    $linkcek = "https://www.turkiyeemlaksitesi.com.tr";
                                                    $i = 0;
                                                    while ($row = $yfotolar->fetch(PDO::FETCH_OBJ)) {
                                                        $i++;
                                                    ?>
                                                        <li class="uk-nestable-item" data-id="<?= $i; ?>" data-idi="<?= $row->id; ?>" id="foto_<?= $row->id; ?>">
                                                            <div class="col-sm-6 col-lg-3 col-md-4 webdesign illustrator" id="foto_<?= $row->id; ?>">
                                                                <div class="gal-detail thumb">
                                                                    <div class="ilanfototasi"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div>
                                                                    <a href="<?= $linkcek; ?>/uploads/<?= $row->resim; ?>" class="image-popup"><img src="<?= $linkcek; ?>/uploads/thumb/<?= $row->resim; ?>" width="150" height="150" class="thumb-img" alt="work-thumbnail"></a>
                                                                    <div class="clearfix"></div>
                                                                    <div class="radio radio-success radio-single">
                                                                        <input type="radio" id="<?= $row->id; ?>" name="kapak" value="<?= $row->resim; ?>" <?= ($snc->resim == $row->resim) ? 'checked' : ''; ?>>
                                                                        <label for="<?= $row->id; ?>">Kapak Görseli Seç</label>
                                                                    </div>
                                                                    <a style="margin-top: -60px; float: right;" href="javascript:;" onclick="ajaxHere('ajax.php?p=galeri_foto_sil&id=<?= $row->id; ?>', 'silsnc');">
                                                                        <button type="button" class="btn btn-icon waves-effect waves-light btn-danger m-b-5"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                    </a>
                                                                    <a style="margin-top: -60px; margin-right:40px; float: right;" href='javascript:window.open("<?= SITE_URL . "rotate/" . $row->id; ?>", "mywindow","status=1,toolbar=0,resizable=1,width="+window.innerWidth+",height="+window.innerHeight+100+"").moveTo(0, 0);'>
                                                                        <button type="button" class="btn btn-icon waves-effect waves-light btn-info m-b-5"><i class="fa fa-repeat" aria-hidden="true"></i></button>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div align="right">
                                            <button type="button" class="btn btn-success waves-effect waves-light" onclick="AjaxFormS('GaleriGuncelleForm', 'silsnc');">Devam Et <i class="fa fa-arrow-right"></i></button>
                                        </div>
                                    </form>
                                </div><!-- galeri foto ekle end -->
                        <?php
                            } // aşama 0 foto galeri ayarı...

                            if ($asama == 1) { // video sistemi start...
                        ?>
                                <div id="galeri_video_ekle">
                                    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= dil("TX444"); ?></h4>
                                    <div class="alert alert-info" role="alert"><?= dil("TX457"); ?></div>
                                    <div style="height:200px;float:left;width:100%;">
                                        <form action="ajax.php?p=galeri_video_guncelle&ilan_id=<?= $snc->id; ?>&from=insert<?= ($snc->video != '') ? '&video=1' : '&video=0'; ?>" method="POST" id="VideoForm" enctype="multipart/form-data">
                                            <center><input type="file" name="video" id="VideoSec" /></center>
                                            <div class="clear"></div>
                                        </form>
                                        <div class="yuklebar" id="YuklemeBar" style="display:none">
                                            <span id="percent">0%</span>
                                            <div class="yuklebarasama animated flash" id="YuklemeDurum"></div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <hr style="border: 1px solid #eee;">
                                    <br>
                                    <div align="right">
                                        <a style="margin-left: 15px;" class="btn btn-info" href="javascript:YuklemeBaslat();"><?= dil("TX442"); ?> <i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
                                        <a class="btn btn-success" href="javascript:atla();"><?= dil("TX443"); ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="clear"></div>
                                    <div align="right"><br>
                                        <div id="VideoForm_output" style="display:none"></div>
                                    </div>
                                </div><!-- galeri video ekle end -->
                        <?php
                            } // video sistemi end
                        }
                        ?>
						
						
if ($asama == 2 && $gayarlar->dopingler_501 == 1) {
    // Doping zamanlarını al ve periyotlara göre ayarla
    list($dzaman1a, $dzaman1b) = explode("|", $gayarlar->dzaman1);
    list($dzaman2a, $dzaman2b) = explode("|", $gayarlar->dzaman2);
    list($dzaman3a, $dzaman3b) = explode("|", $gayarlar->dzaman3);
    $dzaman1b = $periyod[$dzaman1b];
    $dzaman2b = $periyod[$dzaman2b];
    $dzaman3b = $periyod[$dzaman3b];

    $from = "insert";
?>
<div id="doping_ekle"> <!-- doping ekle div start -->

<div class="clear"></div>

<form action="ajax.php?p=ilan_dopingle&id=<?= $id; ?>&from=<?= $from; ?>" method="POST" id="DopingleForm">

<h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= dil("TX517"); ?></h4>

<div class="alert alert-info" role="alert"><?= dil("TX518"); ?></div>
<br>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#eee"><h5><strong><?= dil("TX519"); ?></strong></h5></td>
    <td align="center" bgcolor="#eee"><strong><?= $dzaman1a . " " . $dzaman1b; ?></strong></td>
    <td align="center" bgcolor="#eee"><strong><?= $dzaman2a . " " . $dzaman2b; ?></strong></td>
    <td align="center" bgcolor="#eee"><strong><?= $dzaman3a . " " . $dzaman3b; ?></strong></td>
    <td align="center" bgcolor="#eee"><strong>Sınırsız</strong></td>
  </tr>
  
  <?php
  $bugun = date("Y-m-d");
  $sec = 0;
  $dopingler_501 = $db->query("SELECT * FROM doping_ayarlar_501 ORDER BY id ASC");
  while ($row = $dopingler_501->fetch(PDO::FETCH_OBJ)) {
    // Eğer aynı dopingten varsa ve süresi bitmemişse...
    try {
      $isdoping = $db->prepare("SELECT * FROM dopingler_501 WHERE ilan_id = ? AND did = ? AND btarih > NOW()");
      $isdoping->execute([$snc->id, $row->id]);
    } catch (PDOException $e) {
      // Hata mesajını log dosyasına yaz ve ekranda göster
      error_log($e->getMessage());
      echo "Veritabanı hatası: " . $e->getMessage();
    }
  ?>
  <tr>
    <td><?= dil("DOPING" . $row->id); ?></td>
    
    <?php if ($isdoping->rowCount() > 0) {
        $isdoping = $isdoping->fetch(PDO::FETCH_OBJ);
    ?>
    <td align="center" colspan="4">
        <?php if ($isdoping->durum == 0) { ?>
            <h5 style="color:orange;"><i class="fa fa-check"></i> <?= dil("TX533"); ?></h5>
        <?php } elseif ($isdoping->durum == 1) {
            $kgun = $fonk->gun_farki($isdoping->btarih, $bugun);
        ?>
            <?php if ($isdoping->sure == 100 && $isdoping->periyod == "yillik") { ?>
                <strong style="color:green">Süresiz</strong>
            <?php } elseif ($kgun < 0) { ?>
                <strong style="color:red"><i class="fa fa-clock-o"></i> <?= dil("TX562"); ?></strong>
            <?php } else { ?>
                <strong><i class="fa fa-clock-o"></i> <?= ($kgun == 0) ? dil("TX563") : $kgun . " " . dil("TX564"); ?></strong>
            <?php } ?>
        <?php } ?>
    </td>
    <?php } else { $sec += 1; ?>
    <td align="center">
        <label><input name="doping[<?= $row->id; ?>]" class="checkbox_one" type="checkbox" value="1" > Seç</label>
    </td>
    <td align="center">
        <label><input name="doping[<?= $row->id; ?>]" class="checkbox_one" type="checkbox" value="2"> Seç</label>
    </td>
    <td align="center">
        <label><input name="doping[<?= $row->id; ?>]" class="checkbox_one" type="checkbox" value="3"> Seç</label>
    </td>
    <td align="center">
        <label><input name="doping[<?= $row->id; ?>]" class="checkbox_one" type="checkbox" value="4"> Seç</label>
    </td>
    <?php } ?>
  </tr>
  <?php } ?>
</table>

<div class="clear"></div>

<?php if ($sec > 0) { ?>
<hr style="border: 1px solid #eee;">
<br>
<div align="right">
    <a style="margin-left: 15px;" class="btn btn-success" href="javascript:void(0);" onclick="AjaxFormS('DopingleForm', 'DopingleForm_output');" id="DopingleButon"><i class="fa fa-check" aria-hidden="true"></i> <?= dil("TX524"); ?></a>
    <a class="btn btn-danger" href="javascript:atla();"><?= dil("TX443"); ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
</div>
<div id="DopingleForm_output" style="display:none" align="left"></div>
<?php } else { ?>
<hr style="border: 1px solid #eee;">
<br>
<div align="right">
    <a class="btn btn-danger" href="javascript:atla();"><?= dil("TX443"); ?> <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
</div>
<?php } ?>

</form> 

</div><!-- doping ekle div end asama 2 end -->
<?php } ?>
// asama 2 end


<div id="TamamDiv" style="display:none">
<!-- TAMAM MESAJ --><center>
<div style="margin-top:30px;margin-bottom:70px;text-align:center;" id="BasvrTamam">
<i style="font-size:80px;color:green;" class="fa fa-check"></i>
<h2 style="color:green;font-weight:bold;">İşleminiz Başarıyla Gerçekleşti.</h2>
<br/>
<h4>İlan başarıyla eklenmiştir. Birazdan yönlendirileceksiniz.</h4>
</div></center>
<!-- TAMAM MESAJ -->
</div>
<?php

} else { ?>

<div id="form_status"></div>
<form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=ilan_ekle" onsubmit="return false;" enctype="multipart/form-data">
<input type="hidden" name="ilan_no" value="<?= $ilan_no; ?>" />

<div style="clear:both"></div>
<ul class="nav nav-tabs tabs">
    <li class="active tab">
        <a href="#tab1" data-toggle="tab" aria-expanded="false">
        <span class="hidden-xs"><?= $dilx->gosterim_adi; ?></span></a>
    </li>
    
    <?php
    $dilop = array();
    $i = 1;
    try {
        $sql = $db->query("SELECT * FROM diller_501 WHERE kisa_adi != ? ORDER BY sira ASC", [$dil]);
        while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
            $i++;
            $dilop[$i] = $row;
    ?>
    <li class="tab">
        <a href="#tab<?= $i; ?>" data-toggle="tab" aria-expanded="false">
        <span class="hidden-xs"><?= $row->gosterim_adi; ?></span></a>
    </li>
    <?php 
        }
    } catch (PDOException $e) {
        // Hata mesajını log dosyasına yaz ve ekranda göster
        error_log($e->getMessage());
        echo "Veritabanı hatası: " . $e->getMessage();
    }
    ?>
</ul>

<div class="tab-content">
    <?php
    for ($o = 2; $o <= $i; $o++) {
        $op = $dilop[$o];
    ?>
    <div class="tab-pane" id="tab<?= $o; ?>">
        <div class="form-group">
            <label class="col-sm-3 control-label">Başlık</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="tabs[<?= $op->kisa_adi; ?>][baslik]" value="" placeholder="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-1 control-label">Açıklamalar</label>
            <div class="col-sm-11">
                <textarea class="summernote form-control" rows="9" name="tabs[<?= $op->kisa_adi; ?>][icerik]"></textarea>
            </div>
        </div>
        <?= $fonk->bilgi("Google'da rekabeti düşük kelimelerde organik olarak ilk sayfaya yükselmek için mutlaka aşağıdaki bilgileri doldurunuz. Sadece sayfa içeriği ile alakalı bilgiler girmelisiniz. Aksi halde spam cezası alabilirsiniz."); ?>
        <div class="form-group">
            <label class="col-sm-3 control-label">SEO Başlık (Title)</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="tabs[<?= $op->kisa_adi; ?>][title]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">SEO Kelimeler (Keywords)</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="tabs[<?= $op->kisa_adi; ?>][keywords]" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">SEO Açıklama (Description)</label>
            <div class="col-sm-9">
                <textarea class="form-control" rows="5" name="tabs[<?= $op->kisa_adi; ?>][description]"></textarea>
            </div>
        </div>
    </div>
    <?php } ?>
    
    <div class="tab-pane active" id="tab1"><!-- tab1 start -->
    <?php if ($acc->adi != '') { ?>
        <input type="hidden" name="accid" value="<?= $acc->id; ?>" />
        <div class="form-group">
            <label class="col-sm-3 control-label">Ekleyen</label>
            <div class="col-sm-9">
                <a href="index.php?p=uye_duzenle&id=<?= $acc->id; ?>" target="_blank"><?= $acc->adi . " " . $acc->soyadi; ?></a>
            </div>
        </div>
    <?php } ?>

    <?php if ($acc->id == '' || $acc->turu != 2) { ?>
        <div class="form-group">
            <label class="col-sm-3 control-label">Danışman</label>
            <div class="col-sm-9">
                <select class="form-control" name="danisman_id">
                    <option value="0">Yok</option>
                    <?php
                    try {
                        $sql = $db->query("SELECT id, CONCAT_WS(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE site_id_555=501 AND tipi = 0 AND turu = 2 ORDER BY id DESC");
                        while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                    ?>
                    <option value="<?= $row->id; ?>"><?= $row->adsoyad; ?></option>
                    <?php 
                        }
                        $sql = $db->query("SELECT * FROM danismanlar_501 ORDER BY id ASC");
                        while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                    ?>
                    <option value="<?= $row->id; ?>"><?= $row->adsoyad; ?></option>
                    <?php 
                        }
                    } catch (PDOException $e) {
                        // Hata mesajını log dosyasına yaz ve ekranda göster
                        error_log($e->getMessage());
                        echo "Veritabanı hatası: " . $e->getMessage();
                    }
                    ?>
                </select>
            </div>
        </div>
    <?php } ?>

    <div class="form-group">
        <label class="col-sm-3 control-label">Başlık <span style="color:red">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="baslik" value="" placeholder="">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Fiyatı <span style="color:red">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="fiyat" value="" placeholder="" style="width:200px;float:left;margin-right:5px;" data-mask="#.##0" data-mask-reverse="true" data-mask-maxlength="false">
            <script src="../modules/js/zjquery.mask.js" defer></script>
            <script src="../modules/js/zinputmask.js" defer></script>
            <select class="form-control" name="pbirim" style="width:100px">
                <?php
                $pbirimler = explode(",", dil("PARA_BIRIMI"));
                foreach ($pbirimler as $birim) {
                ?>
                <option><?= $birim; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">İlan No</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" disabled value="<?= $ilan_no; ?>" placeholder="">
        </div>
    </div>

    <?php
    $emlkdrm = dil("EMLK_DRM");
    if ($emlkdrm != '') {
    ?>
    <div class="form-group">
        <label class="col-sm-3 control-label">Emlak Durumu <span style="color:red">*</span></label>
        <div class="col-sm-9">
            <select name="emlak_durum" class="form-control">
                <?php
                $parc = explode("<+>", $emlkdrm);
                foreach ($parc as $val) {
                ?>
                <option><?= $val; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php } ?>

    <?php
    $emlktp = dil("EMLK_TIPI");
    if ($emlktp != '') {
    ?>
    <div class="form-group">
        <label class="col-sm-3 control-label">Emlak Tipi <span style="color:red">*</span></label>
        <div class="col-sm-9">
            <select name="emlak_tipi" class="form-control" onchange="konut_getir(this.options[this.selectedIndex].value);">
                <?php
                $parc = explode("<+>", $emlktp);
                $isyeri = $parc[1];
                $arsa = $parc[2];
                foreach ($parc as $val) {
                ?>
                <option><?= $val; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php } ?>
    
    <?php
    $kntsekli = dil("KNT_SEKLI");
    if ($kntsekli != '') {
    ?>
    <div class="form-group" id="konut_sekli_con">
        <label class="col-sm-3 control-label">Konut Şekli</label>
        <div class="col-sm-9">
            <select name="konut_sekli" class="form-control">
                <option value="">Konut Şekli</option>
                <?php
                $parc = explode("<+>", $kntsekli);
                foreach ($parc as $val) {
                ?>
                <option><?= $val; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php } ?>
    
    <?php
    $knttipi = dil("KNT_TIPI");
    if ($knttipi != '') {
    ?>
    <div class="form-group" id="konut_tipi_con">
        <label class="col-sm-3 control-label">Konut Tipi</label>
        <div class="col-sm-9">
            <select name="konut_tipi" class="form-control">
                <option value="">Konut Tipi</option>
                <?php
                $parc = explode("<+>", $knttipi);
                foreach ($parc as $val) {
                ?>
                <option><?= $val; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php } ?>

    <?php
    try {
        $ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
        $ulkelerc = $ulkeler->rowCount();
        if ($ulkelerc > 1) {
    ?>
    <div class="form-group">
        <label class="col-sm-3 control-label">Ülke <span style="color:red">*</span></label>
        <div class="col-sm-9">
            <select id="ulke_id" name="ulke_id" class="form-control" onchange="ajaxHere('ajax.php?p=il_getir&ulke_id=' + this.options[this.selectedIndex].value, 'il'); yazdir();">
                <option value="">Seçiniz</option>
                <?php
                while ($row = $ulkeler->fetch(PDO::FETCH_OBJ)) {
                ?>
                <option value="<?= $row->id; ?>"><?= $row->ulke_adi; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
<?php } ?>


<div class="form-group">
    <label class="col-sm-3 control-label">İl <span style="color:red">*</span></label>
    <div class="col-sm-9">
        <select id="il" name="il" class="form-control" onchange="ajaxHere('ajax.php?p=ilce_getir&il_id=' + this.options[this.selectedIndex].value, 'ilce'); yazdir();">
            <option value="">Seçiniz</option>
            <?php
            if ($ulkelerc < 2) {
                try {
                    $ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
                    $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id = ? ORDER BY id ASC", [$ulke->id]);
                    while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
            ?>
                    <option value="<?= $row->id; ?>"><?= $row->il_adi; ?></option>
            <?php
                    }
                } catch (PDOException $e) {
                    // Hata mesajını log dosyasına yaz ve ekranda göster
                    error_log($e->getMessage());
                    echo "Veritabanı hatası: " . $e->getMessage();
                }
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">İlçe</label>
    <div class="col-sm-9">
        <select onchange="yazdir(); ajaxHere('ajax.php?p=mahalle_getir&ilce_id=' + this.options[this.selectedIndex].value, 'semt');" name="ilce" id="ilce" class="form-control">
            <option value="">Seçiniz</option>
            <option value="0">Yok</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Mahalle</label>
    <div class="col-sm-9">
        <select onchange="yazdir();" name="mahalle" id="semt" class="form-control">
            <option value="">Seçiniz</option>
            <option value="0">Yok</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="metrekare" class="col-sm-3 control-label">Net Metrekare <span style="color:red">*</span></label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="metrekare" name="metrekare" value="">
    </div>
</div>

<div class="form-group" id="brut_metrekare_con">
    <label for="brut_metrekare" class="col-sm-3 control-label">Brüt Metrekare</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="brut_metrekare" name="brut_metrekare" value="">
    </div>
</div>

<?php
$bulundkat = dil("BULND_KAT");
if ($bulundkat != '') {
?>
<div class="form-group" id="bulundugu_kat_con">
    <label class="col-sm-3 control-label">Bulunduğu Kat</label>
    <div class="col-sm-9">
        <select name="bulundugu_kat" class="form-control">
            <option value="">Bulunduğu Kat</option>
            <?php
            $parc = explode("<+>", $bulundkat);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<?php
$ypidrm = dil("YAPI_DURUM");
if ($ypidrm != '') {
?>
<div class="form-group" id="yapi_durum_con">
    <label class="col-sm-3 control-label">Yapının Durumu</label>
    <div class="col-sm-9">
        <select name="yapi_durum" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $ypidrm);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<?php
$odasayisiy = dil("ODA_SAYISI");
if ($odasayisiy != '') {
?>
<div class="form-group" id="oda_sayisi_con">
    <label class="col-sm-3 control-label">Oda Sayısı</label>
    <div class="col-sm-9">
        <select name="oda_sayisi" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $odasayisiy);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group" id="bina_yasi_con">
    <label for="bina_yasi" class="col-sm-3 control-label">Bina Yaşı</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="bina_yasi" name="bina_yasi" value="">
    </div>
</div>

<div class="form-group" id="bina_kat_sayisi_con">
    <label for="bina_kat_sayisi" class="col-sm-3 control-label">Bina Kat Sayısı</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="bina_kat_sayisi" name="bina_kat_sayisi" value="">
    </div>
</div>

<?php
$isitma = dil("ISITMA");
if ($isitma != '') {
?>
<div class="form-group" id="isitma_con">
    <label class="col-sm-3 control-label">Isıtma</label>
    <div class="col-sm-9">
        <select name="isitma" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $isitma);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group" id="banyo_sayisi_con">
    <label for="banyo_sayisi" class="col-sm-3 control-label">Banyo Sayısı</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="banyo_sayisi" name="banyo_sayisi" value="">
    </div>
</div>

<div class="form-group" id="esyali_con">
    <label class="col-sm-3 control-label">Eşyalı mı?</label>
    <div class="col-sm-9">
        <select name="esyali" class="form-control">
            <option value="">Seçiniz</option>
            <option value="1">Evet</option>
            <option value="0">Hayır</option>
        </select>
    </div>
</div>

<?php
$kuldrm = dil("KUL_DURUM");
if ($kuldrm != '') {
?>
<div class="form-group" id="kullanim_durum_con">
    <label class="col-sm-3 control-label">Kullanım Durumu</label>
    <div class="col-sm-9">
        <select name="kullanim_durum" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $kuldrm);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group" id="site_ici_con">
    <label class="col-sm-3 control-label">Site İçerisinde mi?</label>
    <div class="col-sm-9">
        <select name="site_ici" class="form-control">
            <option value="">Seçiniz</option>
            <option value="1">Evet</option>
            <option value="0">Hayır</option>
        </select>
    </div>
</div>

<div class="form-group" id="aidat_con">
    <label class="col-sm-3 control-label">Aidat</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="aidat" value="">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">M² Fiyatı</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="metrekare_fiyat" value="">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Ada No</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="ada_no" value="">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Parsel No</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="parsel_no" value="">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Pafta No</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="pafta_no" value="">
    </div>
</div>


<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Kaks (Emsal)</label>
    <div class="col-sm-9">
        <?php
        $kaks_emsl = dil("KAKS_EMSAL");
        if ($kaks_emsl != '') {
        ?>
        <select name="kaks_emsal" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $kaks_emsl);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
        <?php
        } else {
        ?>
        <input type="text" class="form-control" name="kaks_emsal" value="">
        <?php } ?>
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Gabari</label>
    <div class="col-sm-9">
        <?php
        $gabari = dil("GABARI");
        if ($gabari != '') {
        ?>
        <select name="gabari" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $gabari);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
        <?php
        } else {
        ?>
        <input type="text" class="form-control" name="gabari" value="">
        <?php } ?>
    </div>
</div>

<?php
$imar_drm = dil("IMAR_DURUM");
if ($imar_drm != '') {
?>
<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">İmar Durumu</label>
    <div class="col-sm-9">
        <select name="imar_durum" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $imar_drm);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<?php
$tapu_drm = dil("TAPU_DRM");
if ($tapu_drm != '') {
?>
<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Tapu Durumu</label>
    <div class="col-sm-9">
        <select name="tapu_durumu" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $tapu_drm);
            foreach ($parc as $val) {
            ?>
            <option><?= $val; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Kat Karşılığı</label>
    <div class="col-sm-9">
        <select name="katk" class="form-control">
            <option value="">Seçiniz</option>
            <option><?= dil("TX167"); ?></option>
            <option><?= dil("TX168"); ?></option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Krediye Uygunluk</label>
    <div class="col-sm-9">
        <select name="krediu" class="form-control">
            <option value="">Seçiniz</option>
            <option><?= dil("TX167"); ?></option>
            <option><?= dil("TX168"); ?></option>
        </select>
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Takas</label>
    <div class="col-sm-9">
        <select name="takas" class="form-control">
            <option value="">Seçiniz</option>
            <option><?= dil("TX167"); ?></option>
            <option><?= dil("TX168"); ?></option>
        </select>
    </div>
</div>

<?php
if (dil("KIMDEN") != '') {
$exp = explode(",", dil("KIMDEN"));
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Kimden</label>
    <div class="col-sm-9">
        <select name="kimden" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            foreach ($exp as $row) {
            ?>
            <option><?= $row; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<?php
if (dil("TX624") != '') {
$exp = explode(",", dil("TX625"));
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Yetki Sözleşmesi</label>
    <div class="col-sm-9">
        <select name="yetkis" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            foreach ($exp as $row) {
            ?>
            <option><?= $row; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group" id="yetki_bilgisi_con">
    <label for="yetki_bilgisi" class="col-sm-3 control-label"><br/>Yetki Belgesi No.<span style="color:red">*</span></label>
    <div class="col-sm-9">
        <font color="green"><strong>Buraya <font color="red">Emlak Yetki Belgenizin <font color="green">Numarasını Giriniz.</strong></font>
        <input type="text" class="form-control" id="yetki_bilgisi" name="yetki_bilgisi" value="<?= $snc->yetki_bilgisi; ?>">
    </div>
</div>


<table>    
<br/>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> SÖZLEŞMELİ İLANLARINIZ </strong></font><br/><font color="black">Sözleşmeli İlanlarınızı, Daha Hızlı Pazarlamak İçin,</font><br/> <font color="black">İsterseniz, Üyelerimizle ve / veya Grup Oluşturarak, Grup Üyeleriniz ile Paylaşabilirsiniz.</font><br/> <font color="black">Paylaştığınız İlanda, <font color="blue">Size ait - Firma İsmi, Logo, Telefon vs.</font> Olmamalıdır.<br/> <font color="black">İlan Açıklamasının en altına <font color="blue">Bu İlan Yetkilisi Tarafından Paylaşıma Açılmıştır </font> Yazınız <br/> <font color="black">Sözleşmeli İlan Kutucuklarını Doldurduysanız, </font><br/><font color="red"><strong>KAPALI PORTFÖY ve TALEP KUTUCUKLARINI İŞARETLEMEYİNİZ. </strong> </font><br/><br/></p>
</div>
                                
<div class="form-group" id="site_id_888_con">
    <label for="site_id_888" class="col-sm-3 control-label"><br/>İLAN PAYLAŞIMI 01</label>
    <div class="col-sm-9">
        <font color="blue">Sözleşmeli İlanınızı, Üye Emlakçılarımız ile Paylaşmak İçin,</font>&nbsp&nbsp&nbsp<font color="black">Kutunun İçine <font color="red"><strong> 100 </font></strong><font color="black">Yazınız.
        <input type="text" class="form-control" id="site_id_888" name="site_id_888" value="<?= $snc->site_id_888; ?>" >
    </div>
</div>

<div class="form-group" id="site_id_777_con">
    <label for="site_id_777" class="col-sm-3 control-label"><br/>İLAN PAYLAŞIMI 02</label>
    <div class="col-sm-9">
        <font color="blue"> Sözleşmeli İlanınızı, Grubunuzdaki Emlakçılar ile Paylaşmak İçin </font>&nbsp&nbsp&nbsp<font color="black">Kutunun İçine <font color="red"><strong> GRUP KODUNU </font></strong><font color="black">Yazınız
        <input type="text" class="form-control" id="site_id_777" name="site_id_777" value="<?= $snc->site_id_777; ?>" >
    </div>
</div>
</table>

<table>    
<br/>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> KAPALI PORTFÖY İLANLARINIZ </strong></font><br/><font color="black"> Kapalı Portföy İlanlarınızı, Artık Sitenize, Girerek Saklayabilir</font><br/><font color="black">Linkini Müşterilerinize Yollayabilir, Ofisinizde Sunum Yapabilirsiniz.</font> <br/><font color="black">İsterseniz, Üyelerimizle ve / veya Grup Oluşturarak, Grup Üyeleriniz ile Paylaşabilirsiniz.<br/><font color="red"> <strong>KAPALI PORTFÖY İLANLARI, HİÇBİR SİTEDE YAYINLANMAZ.</strong></font><br/><font color="blue"> Sitenize Girdiğiniz İlanları, Admin Kısmından, Sadece Siz Görebilirsiniz.</font><br/><font color="blue"> Paylaştığınız İlanları, Paylaştığınız Üyeler, Kendi Sitelerinin Admin kısmına girerek Görebilirler. </font><br/><font color="black">Kapalı Portföy İlan Kutucuklarını Doldurduysanız, </font><br/><font color="red"><strong>SÖZLEŞMELİ İLAN ve TALEPLER KUTUCUKLARINI DOLDURMAYINIZ. </strong> </font><br/><br/>
 </div>

<div class="form-group" id="site_id_699_con">
    <label for="site_id_699" class="col-sm-3 control-label"><br/><br/><font color="red"> KAPALI PORTFÖY 01 </font></label>
    <div class="col-sm-9">
        <font color="blue"> Kapalı Portföy İlanlarınızı, Üye Emlakçılarımız ile Paylaşmak için,</font><br/> <font color="black"> Kutucuğun İçine <font color="red"><strong> 200 </font></strong><font color="black">Yazınız. ( İlan Başlık Yazısının Başına </font> <font color="red"><strong> KAPALI </font></strong><font color="black">Yazmayı Unutmayınız )</font> 
        <input type="text" class="form-control" id="site_id_699" name="site_id_699" value="<?= $snc->site_id_699; ?>" >
    </div>
</div>    

<div class="form-group" id="site_id_700_con">
    <label for="site_id_700" class="col-sm-3 control-label"><br/><br/><font color="red"> KAPALI PORTFÖY 02 </font></label>
    <div class="col-sm-9">
        <font color="blue"> Kapalı Portföy İlanlarınızı, PAYLAŞMAK İSTEMEZSENİZ,</font><br/> <font color="black"> Kutucuğun İçine size verdiğimiz <font color="red"><strong> ŞİFREYİ </font></strong><font color="black">Yazınız.
        <input type="text" class="form-control" id="site_id_700" name="site_id_700" value="<?= $snc->site_id_700; ?>" >
    </div>
</div>    

<div class="form-group" id="site_id_701_con">
    <label for="site_id_701" class="col-sm-3 control-label"><br/><br/><font color="red"> KAPALI PORTFÖY 03 </font></label>
    <div class="col-sm-9">
        <font color="blue"> Kapalı Portföy İlanlarınızı, GRUBUNUZDAKİ EMLAKÇILARLA PAYLAŞMAK İçin </font><br/> <font color="black"> Kutucuğun İçine,<font color="red"><strong> GRUP KODUNU </font></strong><font color="black">Yazınız.
        <input type="text" class="form-control" id="site_id_701" name="site_id_701" value="<?= $snc->site_id_701; ?>" >
    </div>
</div>    
</table>

<table>     
<br/>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> EMLAK TALEPLERİNİZİ PAYLAŞIN </strong></font><br/><font color="black"> Sıtenize girdiğiniz Müşteri Talepleriniz, PORTALLARIMIZDA, Sizin Bilgileriniz ile YAYINLANIR. </font><br/><font color="black"> Gireceğiniz Taleplerin Başlığının Başına </font><font color="red"><strong> TALEP </strong></font> <font color="black">Yazmayı unutmayınız.</font> <br/><font color="blue"> ( PAYLAŞILAN TALEPLERİ, Sizin Sitenizdede, Sizin Bilgileriniz ile Göstermemizi isterseniz, Bizi arayabilirsiniz.) </font> <br/><font color="black">Emlak Talep İlan Kutucuğunu Doldurduysanız, </font><br/><font color="red"><strong>SÖZLEŞMELİ İLAN ve KAPALI PORTFÖY KUTUCUKLARINI DOLDURMAYINIZ. </strong> </font><br/><br/>
 </div>

<div class="form-group" id="site_id_702_con">
    <label for="site_id_702" class="col-sm-3 control-label"><br/><br/><font color="red"> EMLAK TALEBİ </font></label>
    <div class="col-sm-9">
        <font color="blue">Emlak Taleplerinizi Paylaşmak için,</font><br/> <font color="black">Kutunun İçine <font color="red"><strong> 300 </font></strong><font color="black">Yazınız. Diğer Kutucukları İşaretlemeyiniz.
        <input type="text" class="form-control" id="site_id_702" name="site_id_702" value="<?= $snc->site_id_702; ?>" >
    </div>
</div>
</table>

<table>    
<br/>
<div class="form-group" style="text-align: center;">
    <p><strong><font color="red"> İLANLARINIZI PORTALLARIMIZDA DA YAYINLAYABİLİRSİNİZ </font></strong> <br/><font color="black">( İLANINIZ, Portallarımızda Sizin Bilgilerinizle Yayınlanır. )</font><br/><br/>

    <!-- İZMİR EMLAK SİTESİ -->
    <div style="margin-top: 10px;">
        <label for="site_id_335_checkbox">İZMİR EMLAK SİTESİ &nbsp&nbsp</label>
        <input type="checkbox" id="site_id_335_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" />
        <label style="float:left;margin-right:10px;" for="target_check" class="stm-checkbox-label"></label><span style="margin-right:0px;font-size:14px;margin-top:5px;"> ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( izmiremlaksitesi.com.tr )</strong></font> Portalımızda da Yayınlanır. )</span>
        <input type="hidden" id="site_id_335" name="site_id_335" value="<?= $snc->site_id_335; ?>">
    </div>

    <!-- İSTANBUL EMLAK SİTESİ -->
    <div style="margin-top: 10px;">
        <label for="site_id_334_checkbox">İSTANBUL EMLAK SİTESİ &nbsp&nbsp</label>
        <input type="checkbox" id="site_id_334_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" />
        <label style="float:left;margin-right:10px;" for="target_check" class="stm-checkbox-label"></label><span style="margin-right:5px;font-size:14px;margin-top:5px;"> ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( istanbulemlaksitesi.com.tr )</strong></font> portalımızda da Yayınlanır. )</span>
        <input type="hidden" id="site_id_334" name="site_id_334" value="<?= $snc->site_id_334; ?>">
    </div>

    <!-- ANKARA EMLAK SİTESİ -->
    <div style="margin-top: 10px;">
        <label for="site_id_306_checkbox">ANKARA EMLAK SİTESİ &nbsp&nbsp</label>
        <input type="checkbox" id="site_id_306_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" />
        <label style="float:left;margin-right:10px;" for="target_check" class="stm-checkbox-label"></label><span style="margin-right:15px;font-size:14px;margin-top:5px;"> ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( ankaraemlaksitesi.com.tr )</strong></font> portalımızda Yayınlanır. )</span>
        <input type="hidden" id="site_id_306" name="site_id_306" value="<?= $snc->site_id_306; ?>">
    </div>
</div>
</table>

<script>
function updateSiteIds() {
    var checkboxes = [
        { checkbox: document.getElementById('site_id_335_checkbox'), hiddenInput: document.getElementById('site_id_335'), siteId: 335 },
        { checkbox: document.getElementById('site_id_334_checkbox'), hiddenInput: document.getElementById('site_id_334'), siteId: 334 },
        { checkbox: document.getElementById('site_id_306_checkbox'), hiddenInput: document.getElementById('site_id_306'), siteId: 306 }
    ];

    checkboxes.forEach(function(entry) {
        if (entry.checkbox.checked) {
            entry.hiddenInput.value = entry.siteId;
        } else {
            entry.hiddenInput.value = 0;
        }
    });
}
</script>

<?php
$delm1 = explode("<+>", dil("CEPHE"));
$delm2 = explode("<+>", dil("IC_OZELLIKLER"));
$delm3 = explode("<+>", dil("DIS_OZELLIKLER"));
$delm4 = explode("<+>", dil("ALTYAPI_OZELLIKLER"));
$delm5 = explode("<+>", dil("KONUM_OZELLIKLER"));
$delm6 = explode("<+>", dil("GENEL_OZELLIKLER"));
$delm7 = explode("<+>", dil("MANZARA_OZELLIKLER"));

$cdelm1 = count($delm1);
$cdelm2 = count($delm2);
$cdelm3 = count($delm3);
$cdelm4 = count($delm4);
$cdelm5 = count($delm5);
$cdelm6 = count($delm6);
$cdelm7 = count($delm7);
if ($cdelm1 > 1 OR $cdelm2 > 1 OR $cdelm3 > 1 OR $cdelm4 > 1 OR $cdelm5 > 1 OR $cdelm6 > 1 OR $cdelm7 > 1) {
?>
<div class="form-group">
    <div class="col-sm-12">

<style type="text/css">
.ilanaciklamalar h3 {float:left;font-size:18px;font-weight:700;width:100%;padding-bottom:10px;margin-bottom:10px;border-bottom-width:2px;border-bottom-style:solid;border-bottom-color:#ed2d2d}
.ilanozellik {margin:auto;width:90%}
.ilanozellik h4 {float:left;width:100%;margin-bottom:10px;padding-bottom:10px;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#CCC;margin-top:20px}
.ilanaciklamalar {margin-bottom:30px;float:left;width:100%}
.ilanozellik span {float:left;width:183px;margin-bottom:10px;color:#ccc;font-size:14px}
#ozellikaktif {color:#000;font-weight:700}
.ilanozellik span i {color:#4CAF50;margin-right:7px}
</style>
<div class="ilanaciklamalar" id="ozellikler_con">
    <h3>Özellikleri Seçin</h3>

    <?php
    $checkbox = 0;
    if ($cdelm1 > 1) {
        $ielm = explode("<+>", $ilan->cephe_ozellikler);
    ?>
    <div class="ilanozellik tipi_konut">
        <h4><strong>Cephe</strong></h4>
        <?php
        foreach ($delm1 as $val) {
        ?>
        <span><label style="font-weight:normal;"><input name="cephe_ozellikler[]" value="<?= $val; ?>" type="checkbox"> <?= $val; ?></label></span>
        <?php } ?>
    </div>
    <?php } ?>

    <?php
    if ($cdelm2 > 1) {
        $ielm = explode("<+>", $ilan->ic_ozellikler);
    ?>
    <div class="ilanozellik tipi_konut">
        <h4><strong>İç Özellikler</strong></h4>
        <?php
        foreach ($delm2 as $val) {
        ?>
        <span><label style="font-weight:normal;"><input name="ic_ozellikler[]" type="checkbox" value="<?= $val; ?>"><?= $val; ?></label></span>
        <?php } ?>
    </div>
    <?php } ?>

    <?php
    if ($cdelm3 > 1) {
        $ielm = explode("<+>", $ilan->dis_ozellikler);
    ?>
    <div class="ilanozellik tipi_konut">
        <h4><strong>Dış Özellikler</strong></h4>
        <?php
        foreach ($delm3 as $val) {
        ?>
        <span><label style="font-weight:normal;"><input name="dis_ozellikler[]" type="checkbox" value="<?= $val; ?>"><?= $val; ?></label></span>
        <?php } ?>
    </div>
    <?php } ?>
	
	
if ($cdelm4 > 1) {
    $ielm = explode("<+>", $ilan->altyapi_ozellikler);
?>
<div class="ilanozellik tipi_arsa">
<h4><strong>Altyapı Özellikler</strong></h4>
<?php
    foreach ($delm4 as $val) {
?>
<span><label style="font-weight:normal;"><input name="altyapi_ozellikler[]" type="checkbox" value="<?= $val; ?>"><?= $val; ?></label></span>
<?php
    }
?>
</div>
<?php } ?>

<?php
if ($cdelm5 > 1) {
    $ielm = explode("<+>", $ilan->konum_ozellikler);
?>
<div class="ilanozellik tipi_arsa">
<h4><strong>Konum Özellikler</strong></h4>
<?php
    foreach ($delm5 as $val) {
?>
<span><label style="font-weight:normal;"><input name="konum_ozellikler[]" type="checkbox" value="<?= $val; ?>"><?= $val; ?></label></span>
<?php
    }
?>
</div>
<?php } ?>

<?php
if ($cdelm6 > 1) {
    $ielm = explode("<+>", $ilan->genel_ozellikler);
?>
<div class="ilanozellik tipi_arsa">
<h4><strong>Genel Özellikler</strong></h4>
<?php
    foreach ($delm6 as $val) {
?>
<span><label style="font-weight:normal;"><input name="genel_ozellikler[]" type="checkbox" value="<?= $val; ?>"><?= $val; ?></label></span>
<?php
    }
?>
</div>
<?php } ?>

<?php
if ($cdelm7 > 1) {
    $ielm = explode("<+>", $ilan->manzara_ozellikler);
?>
<div class="ilanozellik tipi_arsa">
<h4><strong>Manzara Özellikler</strong></h4>
<?php
    foreach ($delm7 as $val) {
?>
<span><label style="font-weight:normal;"><input name="manzara_ozellikler[]" type="checkbox" value="<?= $val; ?>"><?= $val; ?></label></span>
<?php
    }
?>
</div>
<?php } ?>

</div>
</div>
</div>
<?php } ?>

<div class="form-group">
    <label for="google_maps" class="col-sm-3 control-label">Google Maps<br><span style="font-weight:lighter;font-size:14px;">Harita konumu otomatik olarak belirlediğiniz il/ilçe/mahalle'ye göre işaretlenmektedir. Dilerseniz cadde veya sokak ekleyerek de daraltabilirsiniz. Hassas işaretleme için imleci sürükleyip bırakınız.</span></label>
    <div class="col-sm-9">
        <div class="form-group">
            <div class="col-sm-11">
                <h4><strong><i class="fa fa-map-marker" aria-hidden="true"></i> Konum Belirleyin.</strong></h4>
            </div>
        </div>

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Şehir</label>
            <div class="col-sm-11">
                <input disabled class="form-control" id="map_il" type="text">
            </div>
        </div>

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">İlçe</label>
            <div class="col-sm-11">
                <input disabled id="map_ilce" class="form-control" type="text">
            </div>
        </div>

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Mahalle</label>
            <div class="col-sm-11">
                <input disabled onchange="yazdir();" type="text" id="map_mahalle" class="form-control" value="">
            </div>
        </div>

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Cadde</label>
            <div class="col-sm-11">
                <input onchange="yazdir();" type="text" class="form-control" name="map_cadde" value="">
            </div>
        </div>

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Sokak</label>
            <div class="col-sm-11">
                <input onchange="yazdir();" type="text" class="form-control" name="map_sokak" value="">
            </div>
        </div>

        <input type="text" class="form-control" id="map_adres" name="map_adres" placeholder="Adres yazınız..." style="display: none;">
        <input type="text" id="coords" name="maps" style="display:none;" />

        <div id="map" style="width: 100%; height: 300px"></div>
        <input type="hidden" value="41.003917" id="g_lat">
        <input type="hidden" value="28.967299" id="g_lng">
        <script type="text/javascript">
            function initMap() {
                var g_lat = parseFloat(document.getElementById("g_lat").value);
                var g_lng = parseFloat(document.getElementById("g_lng").value);
                var map = new google.maps.Map(document.getElementById('map'), {
                    dragable: true,
                    zoom: 15,
                    center: {lat: g_lat, lng: g_lng}
                });
                var geocoder = new google.maps.Geocoder();

                var marker = new google.maps.Marker({
                    position: {
                        lat: g_lat,
                        lng: g_lng
                    },
                    map: map,
                    draggable: true
                });

                jQuery('#map_adres').on('change', function() {
                    var val = $(this).val();
                    geocodeAddress(marker, geocoder, map, val);
                });

                google.maps.event.addListener(marker, 'dragend', function() {
                    dragend(marker);
                });
            }

            function geocodeAddress(marker, geocoder, resultsMap, address) {
                if (address) {
                    geocoder.geocode({'address': address}, function(results, status) {
                        if (status === 'OK') {
                            resultsMap.setCenter(results[0].geometry.location);
                            marker.setMap(resultsMap);
                            marker.setPosition(results[0].geometry.location);
                            dragend(marker);
                        } else {
                            console.log('Geocode was not successful for the following reason: ' + status + " word: " + address);
                        }
                    });
                }
            }

            function dragend(marker) {
                var lat = marker.getPosition().lat();
                var lng = marker.getPosition().lng();
                $("#coords").val(lat + "," + lng);
            }
        </script>
    </div>
</div>

<!--div class="form-group">
    <label class="col-sm-3 control-label">Danışman</label>
    <div class="col-sm-9">
        <select class="form-control" name="danisman_id">
            <option value="0">Yok</option>
            <?php
            $i = 0;
            try {
                $sql = $db->query("SELECT * FROM danismanlar_501 ORDER BY id ASC");
                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                    $i = $i + 1;
            ?>
            <option value="<?= $row->id; ?>" <?= ($i == 1) ? 'selected' : ''; ?>><?= $row->adsoyad; ?></option>
            <?php
                }
            ?>
        </select>
    </div>
</div-->


<div class="form-group">
    <label for="icerik" class="col-sm-1 control-label">Açıklamalar</label>
    <div class="col-sm-11">
        <textarea class="summernote form-control" rows="9" id="icerik" name="icerik"></textarea>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">İlan Notunuz</label>
    <div class="col-sm-9">
        <textarea name="notu" class="form-control" placeholder="Bu notu sadece siz görebilirsiniz. ( İlanı Paylaşacaksanız, Buraya Özel Birşey Yazmayınız.) "></textarea>
    </div>
</div>

<?= $fonk->bilgi("Google'da rekabeti düşük kelimelerde organik olarak ilk sayfaya yükselmek için mutlaka aşağıdaki bilgileri doldurunuz. Sadece sayfa içeriği ile alakalı bilgiler girmelisiniz. Aksi halde spam cezası alabilirsiniz."); ?>

<div class="form-group">
    <label for="title" class="col-sm-3 control-label">SEO Başlık (Title)</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="title" name="title" value="">
    </div>
</div>

<div class="form-group">
    <label for="keywords" class="col-sm-3 control-label">SEO Kelimeler (Keywords)</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="keywords" name="keywords" value="">
    </div>
</div>

<div class="form-group">
    <label for="description" class="col-sm-3 control-label">SEO Açıklama (Description)</label>
    <div class="col-sm-9">
        <textarea class="form-control" rows="5" id="description" name="description"></textarea>
    </div>
</div>
</div><!-- tab1 end -->

</div><!-- tabcontent end -->


<div align="right">
    <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms','form_status');" id="IlanSubmit"><i class="fa fa-camera"></i> Aşama 2 / Fotoğraf Yükle</button>
</div>

</form>
<?php } // eğer galeri değil ise ?>

</div>
</div>
</div>
</div>
<!-- Col1 end -->
</div><!-- row end -->
</div>
</div>
</div>
</div>
</div>
</div>

<style>
.ilanfototasi {
    position: absolute;
    margin-left: 135px;
    color: white;
    font-size: 22px;
    opacity: 0.8;
    filter: alpha(opacity=80);
    cursor: -webkit-grabbing;
}
.ilanfototasi:hover {
    opacity: 1.8;
    filter: alpha(opacity=100);
}
#DopingleForm table tr td {
    padding: 5px;
    border-bottom: 1px solid #ddd;
}
</style>

<script>
    var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css">
<link href="assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
<link rel="stylesheet" href="assets/vendor/magnific-popup/dist/magnific-popup.css">
<link href="assets/vendor/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="assets/css/components/nestable.almost-flat.min.css" />
<link rel="stylesheet" href="assets/css/components/nestable.min.css" />
<link rel="stylesheet" href="assets/css/components/nestable.gradient.min.css" />
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>
<script src="assets/js/uikit.min.js"></script>
<script src="assets/js/components/nestable.min.js"></script>
<script>
    $('.uk-nestable').on('change.uk.nestable', function(e) {
        var data = $("#list").data("nestable").serialize();
        $.post("ajax.php?p=galeri_guncelle&ilan_id=<?= $snc->id; ?>&from=nestable", {value: data}, function (a) {
            $("#silsnc").html(a);
        });
    });

    jQuery(document).ready(function() {
        $('.wysihtml5').wysihtml5();

        $('.summernote').summernote({
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: false // set focus to editable area after initializing summernote
        });
    });
</script>
<?php if ($gurl != '') { ?>
<script type="text/javascript">
var gurl = "<?= $gurl; ?>";
</script>
<script src="assets/vendor/dropzone/dist/dropzone_galeri.js"></script>
<?php } ?>
<script type="text/javascript" src="assets/vendor/isotope/dist/isotope.pkgd.min.js"></script>
<script type="text/javascript" src="assets/vendor/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.image-popup').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            mainClass: 'mfp-fade',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
            }
        });
    });

    $(document).ready(function() {
        $(".tipi_arsa,.arsa_icin").hide(1);
        $("select[name='emlak_tipi']").change(function() {
            var val = $(this).val();
            var select = 
                "#yetki_bilgisi_con,#brut_metrekare_con,#site_id_888_con,#site_id_777_con,#site_id_699_con,#site_id_700_con,#site_id_701_con,#site_id_702_con,#site_id_661_con,#site_id_662_con,#site_id_663_con,#site_id_664_con,#site_id_665_con,#site_id_666_con,#site_id_667_con,#site_id_668_con,#site_id_669_con,#site_id_335_con,#site_id_334_con,#site_id_306_con,#konut_sekli_con,#konut_tipi_con,#bulundugu_kat_con,#yapi_durum_con,#oda_sayisi_con,#bina_yasi_con,#bina_kat_sayisi_con,#isitma_con,#banyo_sayisi_con,#esyali_con,#kullanim_durum_con,#site_ici_con,#aidat_con,#notu_con";

            if (val == '<?= $arsa; ?>') {
                $(select).slideUp(500);
                $(".tipi_konut").slideUp(500);
                $(".tipi_arsa,.arsa_icin").slideDown(500);
            } else {
                $(select).slideDown(500);
                $(".tipi_arsa,.arsa_icin").slideUp(500);
                $(".tipi_konut").slideDown(500);
            }
        });
    });

    function konut_getir(tipi) {
        if (tipi == "<?= $isyeri; ?>") {
            $("select[name=konut_tipi]").html("<?php
            $knttipi = dil("KNT_TIPI2");
            ?><option value=''><?= dil("TX57"); ?></option><?php
            $parc = explode("<+>", $knttipi);
            foreach ($parc as $val) {
            ?><option><?= $val; ?></option><?php
            }
            ?>");
        } else {
            $("select[name=konut_tipi]").html("<?php
            $knttipi = dil("KNT_TIPI");
            ?><option value=''><?= dil("TX57"); ?></option><?php
            $parc = explode("<+>", $knttipi);
            foreach ($parc as $val) {
            ?><option><?= $val; ?></option><?php
            }
            ?>");
        }
    }

    /* Map Ayarları */
    function yazdir() {
        var ulke = $("#ulke_id").val();
        ulke = $("#ulke_id option[value='" + ulke + "']").text();
        var il = $("#il").val();
        il = $("#il option[value='" + il + "']").text();
        var ilce = $("#ilce").val();
        ilce = $("#ilce option[value='" + ilce + "']").text();
        var maha = $("#semt").val();
        maha = $("#semt option[value='" + maha + "']").text();
        var cadde = $("input[name='map_cadde']").val();
        var sokak = $("input[name='map_sokak']").val();
        var neler = "";

        if (il != undefined && il != '' && il != '<?= dil("TX264"); ?>') {
            if (ulke != undefined && ulke != '' && ulke != '<?= dil("TX264"); ?>') {
                neler += ", " + ulke;
            }
            neler += il;
            $("#map_il").val(il);
            if (ilce != undefined && ilce != '' && ilce != '<?= dil("TX264"); ?>') {
                neler += ", " + ilce;
                $("#map_ilce").val(ilce);
                if (maha != undefined && maha != '' && maha != '<?= dil("TX264"); ?>') {
                    neler += ", " + maha;
                    $("#map_mahalle").val(maha);
                } else {
                    $("#map_mahalle").val('');
                }
                if (cadde != undefined && cadde != '' && cadde != '<?= dil("TX264"); ?>') {
                    neler += ", " + cadde;
                }
                if (sokak != undefined && sokak != '' && sokak != '<?= dil("TX264"); ?>') {
                    neler += ", " + sokak;
                }
            } else {
                $("#map_ilce").val('');
            }
        } else {
            $("#map_il").val('');
        }
        $("input[name='map_adres']").val(neler);
        GetMap();
    }

    function GetMap() {
        $("#map_adres").trigger("change");
    }
</script>

<?php if ($id != '' && ($asama == '' || $asama == 0)) { ?>
<script type="text/javascript">
$(document).ready(function() {
    $(".ilanasamax:eq(1)").attr("id", "asamaaktif");
});
</script>
<?php } elseif ($asama == 1) { ?>
<script type="text/javascript">
$(document).ready(function() {
    $(".ilanasamax:eq(2)").attr("id", "asamaaktif");
});
</script>

<script type="text/javascript">
function DosyaBoyutu(input_id) {
    var input, file;
    if (!window.FileReader) {
        console.log("File API'si bu tarayıcıda henüz desteklenmiyor.");
        return 0;
    } else {
        input = document.getElementById(input_id);
        if (!input) {
            console.log("Hm, ilgili input elementi yerinde yok :)");
            return 0;
        } else if (!input.files) {
            console.log("Bu tarayıcı dosya girdilerinin `files` özelliğini desteklemiyor gibi görünüyor.");
            return 0;
        } else if (!input.files[0]) {
            console.log("Dosya seçilmemiş görünüyor.");
            return 0;
        } else {
            file = input.files[0];
            return file.size;
        }
    }
}

function DosyaUzantiKontrol(input_id, Uzantilar) {
    var oInput = document.getElementById(input_id);
    if (oInput.type == "file") {
        var sFileName = oInput.value;
        if (sFileName.length > 0) { // eğer dosya seçilmişse
            var blnValid = false;
            for (var j = 0; j < Uzantilar.length; j++) { // izin verilen uzantılar döndürüyoruz...
                var sCurExtension = Uzantilar[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            } // izin verilen uzantılar döndürüyoruz...
            if (blnValid) { // eğer uzantı geçerliyse
                return true;
            } else { // eğer uzantı geçersiz ise...
                console.log("Sorry, " + sFileName + " is invalid, allowed extensions are: " + Uzantilar.join(", "));
                return false;
            } // eğer uzantı geçersiz ise...
        } else { // Eğer dosya seçilmemiş ise
            return false;
        } // Eğer dosya seçilmemiş ise
    } else { // Tipi dosya değil ise
        return false;
    } // Tipi dosya değil ise
}

function YuklemeBaslat() {
    var video = $("#VideoSec")[0].files.length;

    if (video < 1) {
        $("#VideoForm_output").html("<span class='error'><?= dil("TX454"); ?></span>");
        $("#VideoForm_output").fadeIn(600);
    } else {
        var videoSize = DosyaBoyutu('VideoSec');
        var VideoValid = DosyaUzantiKontrol('VideoSec', ['.mp4']);

        if (videoSize > <?= dil("VIDEO_MAX_BAYT"); ?>) {
            $("#VideoForm_output").html("<span class='error'><?= dil("TX455"); ?></span>");
            $("#VideoForm_output").fadeIn(600);
        } else if (!VideoValid) {
            $("#VideoForm_output").html("<span class='error'><?= dil("TX456"); ?></span>");
            $("#VideoForm_output").fadeIn(600);
        } else { // Eğer video boyutu çok değilse
            $("#VideoForm_output").fadeOut(400);
            $("#VideoForm").slideUp(400, function() {
                $("#YuklemeBar").slideDown(400);
            });

            var bar = $('#YuklemeDurum');
            var percent = $('#percent');
            $("#VideoForm").ajaxForm({
                target: '#VideoForm_output',
                beforeSend: function() {
                    percent.attr("style", "");
                    var percentVal = '0%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percent_style = percent.attr("style");
                    if (percent_style == undefined) {
                        percent_style = '';
                    }
                    if (percentComplete >= 47 && percent_style == '') {
                        percent.attr("style", "color:#FFF;z-index:3;position:relative;");
                    }
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                complete: function() {
                    $("#VideoForm_output").fadeIn(400);
                }
            }).submit();
        } // Video max boyutu aşmıyorsa...
    } // Video seçilmiş ise devam...
}

function atla() {
    <?php if ($gayarlar->dopingler_501 == 1) { ?>
    window.location.href = 'index.php?p=ilan_ekle&id=<?= $id; ?>&asama=2';
    <?php } else { ?>
    $(".ilanasamax").removeAttr("id");
    $(".islem_tamam").attr("id", "asamaaktif");
    $("#galeri_video_ekle").hide(1, function() {
        $("#TamamDiv").show(1);
    });
    $('html, body').animate({scrollTop: 0}, 500);
    $("head").prepend('<meta http-equiv="refresh" content="5;url=index.php?p=ilanlar" />');
    <?php } ?>
}
</script>
<?php } elseif ($asama == 2) { ?>
<script type="text/javascript">
$(document).ready(function() {
    $(".ilanasamax:eq(3)").attr("id", "asamaaktif");

    $(".checkbox_one").change(function() {
        var elem = $(this);
        $(".checkbox_one[name='" + elem.attr("name") + "']").not(this).prop("checked", false);
    });
});
</script>
<script type="text/javascript">
function atla() {
    $("#doping_ekle").hide(1, function() {
        $("#TamamDiv").show(1);
        $(".ilanasamax").removeAttr("id");
        $(".islem_tamam").attr("id", "asamaaktif");
    });
    $('html, body').animate({scrollTop: 0}, 500);
    $("head").prepend('<meta http-equiv="refresh" content="5;url=index.php?p=ilanlar" />');
}
</script>
<?php } elseif ($id == '' && $asama == '') { ?>
<script type="text/javascript">
$(document).ready(function() {
    $(".ilanasamax:eq(0)").attr("id", "asamaaktif");
});
</script>
<?php } ?>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gayarlar->google_api_key; ?>&callback=initMap"></script>