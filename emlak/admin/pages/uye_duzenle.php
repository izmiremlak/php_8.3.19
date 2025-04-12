<?php

// Güvenlik ve hata yönetimi için gerekli ayarların yapılması
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hataların log dosyasına yazılması için ayar
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

// Hataların hem log dosyasına hem de siteye yazılması
function hata_yakala($errno, $errstr, $errfile, $errline) {
    $hata_mesaji = "[$errno] $errstr - $errfile:$errline";
    error_log($hata_mesaji);
    echo "<div class='alert alert-danger'>$hata_mesaji</div>";
}
set_error_handler("hata_yakala");

// ID'yi güvenli bir şekilde alıp işleme
$id = $gvn->rakam($_GET["id"]);

// Veritabanından hesap bilgilerini çekme
$snc = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:ids");
$snc->execute(['ids' => $id]);

if ($snc->rowCount() > 0) {
    $snc = $snc->fetch(PDO::FETCH_OBJ);
} else {
    header("Location:index.php?p=uyeler");
    exit;
}

// Avatar URL'si belirleniyor
$avatar = empty($snc->avatar) ? 'https://www.turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : 'https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/' . htmlspecialchars($snc->avatar, ENT_QUOTES, 'UTF-8');

// Üyelik türlerini ayrıştırma
$turler = explode(",", dil("UYELIK_TURLERI"));

// Kurumsal hesap bilgilerini çekme
if ($snc->turu == 2) {
    $kurumsal = $db->prepare("SELECT id, adi, soyadi, unvan FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
    $kurumsal->execute([$snc->kid]);
    if ($kurumsal->rowCount() > 0) {
        $kurumsal = $kurumsal->fetch(PDO::FETCH_OBJ);
    }
}

// GET parametrelerini güvenli hale getirme
$goto = $gvn->harf_rakam($_GET["goto"]);
?> 
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Üye Görüntüle</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs tabs">
                    <li class="<?= empty($goto) ? "active" : ''; ?> tab">
                        <a href="#tab1" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Üye Bilgileri</span>
                        </a>
                    </li>
                    <li class="tab">
                        <a href="#tab2" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">İlanları</span>
                        </a>
                    </li>
                    <?php if ($snc->turu == 1) { ?>
                        <li class="tab">
                            <a href="#tab3" data-toggle="tab" aria-expanded="false">
                                <span class="hidden-xs">Danışmanları</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($snc->turu == 0 || $snc->turu == 1) { ?>
                        <li class="tab">
                            <a href="#tab4" data-toggle="tab" aria-expanded="false">
                                <span class="hidden-xs">Mağaza Paketleri</span>
                            </a>
                        </li>
                    <?php } ?>
                    <li class="tab">
                        <a href="#tab5" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Dopingleri</span>
                        </a>
                    </li>
                    <li class="<?= $goto == 'mesajlar' ? "active" : ''; ?> tab">
                        <a href="#tab6" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Mesajları</span>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane <?= empty($goto) ? "active" : ''; ?>" id="tab1">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=uye_duzenle&id=<?= htmlspecialchars($snc->id, ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="panel-group panel-group-joined" id="accordion-test"> <!-- accordion start -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion-test" href="#uyekisiselbilgi" class="" aria-expanded="true">Genel Bilgiler<br></a>
                                        </h4>
                                    </div>
                                    <div id="uyekisiselbilgi" class="panel-collapse collapse in" aria-expanded="true">
                                        <div style="padding:20px;">
                                            <div class="form-group" <?= $snc->tipi == 1 ? 'style="display:none"' : ''; ?>>
                                                <label class="col-sm-3 control-label">Üyelik Türü</label>
                                                <div class="col-sm-9">
                                                    <?php foreach ($turler as $k => $v) { ?>
                                                        <div class="radio radio-info radio-inline">
                                                            <input type="radio" id="turu_<?= $k; ?>" value="<?= $k; ?>" name="turu" <?= $snc->turu == $k ? 'checked' : ''; ?>>
                                                            <label for="turu_<?= $k; ?>"><?= htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); ?></label>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Otomatik Onay</label>
                                                <div class="col-sm-9">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="ilan_aktifet_check" type="checkbox" name="ilan_aktifet" value="1" <?= $snc->ilan_aktifet == 1 ? 'checked' : ''; ?>>
                                                        <label for="ilan_aktifet_check"><strong>Aktif</strong></label>
                                                        <span style="font-size:14px;">(Eklenen ilanlar otomatik onaylanarak yayınlanır.)</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($snc->turu == 2) { ?>
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label">Bağlı Olduğu Kurumsal</label>
                                                    <div class="col-sm-9">
                                                        <span style="display:block; margin-top:6px;">
                                                            <a href="index.php?p=uye_duzenle&id=<?= htmlspecialchars($kurumsal->id, ENT_QUOTES, 'UTF-8'); ?>"><?= empty($kurumsal->unvan) ? htmlspecialchars($kurumsal->adi . ' ' . $kurumsal->soyadi, ENT_QUOTES, 'UTF-8') : htmlspecialchars($kurumsal->unvan, ENT_QUOTES, 'UTF-8'); ?></a>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <label for="adsoyad" class="col-sm-3 control-label">Adı Soyadı</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="adsoyad" name="adsoyad" value="<?= htmlspecialchars($snc->adi . ' ' . $snc->soyadi, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="nick_adi" class="col-sm-3 control-label">Profil URL</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="nick_adi" name="nick_adi" value="<?= htmlspecialchars($snc->nick_adi, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                                </div>
                                            </div>
                                            <?php if ($snc->tipi != 1) { ?>
                                                <div class="form-group">
                                                    <label for="email" class="col-sm-3 control-label">E-posta Adresi</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="email" name="email" value="<?= htmlspecialchars($snc->email, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buraya Emailinizi yazınız">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="dunvan" class="col-sm-3 control-label">Bağlı Olduğunuz Firma</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="dunvan" name="dunvan" value="<?= htmlspecialchars($snc->dunvan, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Bağlı Olduğunuz Firma Adını Yazınız">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group bireysel">
                                                <label for="tcno" class="col-sm-3 control-label">T.C No</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="tcno" name="tcno" value="<?= htmlspecialchars($snc->tcno, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="webadres" class="col-sm-3 control-label">Web Adresi</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="webadres" name="webadres" value="<?= htmlspecialchars($snc->webadres, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Emlak Sitenizin Adını Yazınız">
                                                </div>
                                            </div>
                                            <?php if ($snc->tipi != 1) { ?>
                                                <div class="form-group">
                                                    <label for="parola" class="col-sm-3 control-label">Parola</label>
                                                    <div class="col-sm-9">
                                                        <input type="password" class="form-control" id="parola" name="parola" value="<?= htmlspecialchars($snc->parola, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Parolanızı Yazınız">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <label for="telefon" class="col-sm-3 control-label">Gsm</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="telefon" name="telefon" value="<?= htmlspecialchars($snc->telefon, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cep Telefonunuzu Yazınız">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="sabit_telefon" class="col-sm-3 control-label">Sabit Telefon</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="sabit_telefon" name="sabit_telefon" value="<?= htmlspecialchars($snc->sabit_telefon, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Sabit telefonunuz Yoksa Cep Telefonunuzu Yazınız. Yoksa İlanda Telefonunuz Gözükmez">
                                                </div>
                                            </div>
                                            <div class="form-group kurumsal">
                                                <label for="unvan" class="col-sm-3 control-label">Kurumsal Firma Adı</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="unvan" name="unvan" value="<?= htmlspecialchars($snc->unvan, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Site Sahibi İseniz Kurumsal Firma Adınızı Yazınız - Değilseniz BOŞ BIRAKIN">
                                                </div>
                                            </div>
                                            <div class="form-group kurumsal">
                                                <label for="vergi_no" class="col-sm-3 control-label">Vergi No</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="vergi_no" name="vergi_no" value="<?= htmlspecialchars($snc->vergi_no, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                                </div>
                                            </div>
                                            <div class="form-group kurumsal">
                                                <label for="vergi_dairesi" class="col-sm-3 control-label">Vergi Dairesi</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" id="vergi_dairesi" name="vergi_dairesi" value="<?= htmlspecialchars($snc->vergi_dairesi, ENT_QUOTES, 'UTF-8'); ?>" placeholder="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="avatar" class="col-sm-3 control-label">Profil Resmi</label>
                                                <div class="col-sm-9">
                                                    <input type="file" class="form-control" id="avatar" name="avatar">
                                                    <br />
                                                    <img width="100" src="<?= $avatar; ?>" id="avatar_src">
                                                </div>
                                            </div>
                                            <?php if ($snc->turu == 2) { ?>
                                                <div class="form-group danisman">
                                                    <label class="col-sm-3 control-label">Öne Çıkar</label>
                                                    <div class="col-sm-8">
                                                        <div class="checkbox checkbox-success">
                                                            <input id="onecikar_check" type="checkbox" name="onecikar" value="1" <?= $snc->onecikar == 1 ? 'checked' : ''; ?>>
                                                            <label for="onecikar_check"><strong>Aktif</strong></label>
                                                            <span>(Anasayfada "Öne Çıkan Danışmanlar"da yayınlanır.)</span><br>
                                                        </div>
                                                    </div><!-- col-sm-3 end -->
                                                    <label class="col-sm-3 control-label"></label>
                                                    <div class="col-sm-3">
                                                        <input placeholder="Bitiş Tarihi (01.01.2017)" type="text" class="form-control" id="onecikar_btarih" name="onecikar_btarih" value="<?= ($snc->onecikar_btarih == '0000-00-00 00:00:00' || $snc->onecikar_btarih == '') ? '' : date("d.m.Y", strtotime($snc->onecikar_btarih)); ?>">
                                                    </div><!-- col-sm-3 end -->
                                                </div>
                                            <?php } ?>
                                            <div class="form-group kurumsal danisman">
                                                <label for="hakkinda" class="col-sm-3 control-label">Hakkında Yazısı</label>
                                                <div class="col-sm-9">
                                                    <textarea class="summernote form-control" style="width:200px;" id="hakkinda" name="hakkinda"><?= htmlspecialchars($snc->hakkinda, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group" <?= $snc->tipi == 1 ? 'style="display:none"' : ''; ?>>
                                                <label class="col-sm-3 control-label" style="color:red;">Üyeyi Engelle</label>
                                                <div class="col-sm-9">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="durum_check" type="checkbox" name="durum" value="1" <?= $snc->durum == 1 ? 'checked' : ''; ?>>
                                                        <label for="durum_check"><strong>Aktif</strong></label><br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>


<div class="panel panel-default">
    <div class="panel-heading" >
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion-test" href="#adresbilgileri" class="collapsed" aria-expanded="false">Adres Bilgileri</a>
        </h4>
    </div>

    <div id="adresbilgileri" class="panel-collapse collapse" aria-expanded="false">
        <div style="padding:20px;">
            <?php
            // Ülkeler tablosundan verileri çekme ve ülke sayısını kontrol etme
            $ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
            $ulkelerc = $ulkeler->rowCount();
            if ($ulkelerc > 1) {
            ?>
                <div class="form-group kurumsal">
                    <label for="ulke_id" class="col-sm-3 control-label">Ülke</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="ulke_id" name="ulke_id" onchange="yazdir();ajaxHere('ajax.php?p=il_getir&ulke_id='+this.options[this.selectedIndex].value,'il');">
                            <option value="">Seçiniz</option>
                            <?php
                            while ($row = $ulkeler->fetch(PDO::FETCH_OBJ)) {
                            ?><option value="<?=$row->id;?>" <?=($snc->ulke_id == $row->id) ? 'selected' : '';?>><?=htmlspecialchars($row->ulke_adi, ENT_QUOTES, 'UTF-8');?></option><?
                            }
                            ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
                <div class="form-group kurumsal">
                    <label for="il" class="col-sm-3 control-label">İl</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="il" name="il" onchange="ajaxHere('ajax.php?p=ilce_getir&il_id='+this.options[this.selectedIndex].value,'ilce'),$('#semt').html(''),yazdir();">
                            <option value="">Seçiniz</option>
                            <?php
                            if ($ulkelerc < 2) {
                                $ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
                                $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id=".$ulke->id." ORDER BY id ASC");
                            } else {
                                $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id=".$snc->ulke_id." ORDER BY id ASC");
                            }
                            while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                                if ($row->id == $snc->il_id) {
                                    $il_adi = $row->il_adi;
                                }
                            ?><option value="<?=$row->id;?>" <?=($row->id == $snc->il_id) ? 'selected' : '';?>><?=htmlspecialchars($row->il_adi, ENT_QUOTES, 'UTF-8');?></option><?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group kurumsal">
                    <label for="ilce" class="col-sm-3 control-label">İlçe</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="ilce" id="ilce" onchange="yazdir();ajaxHere('ajax.php?p=mahalle_getir&ilce_id='+this.options[this.selectedIndex].value,'semt');">
                            <option value="">Seçiniz</option>
                            <option value="0">Yok</option>
                            <?php
                            if ($snc->il_id != '') {
                                $sql = $db->query("SELECT id, ilce_adi FROM ilce WHERE il_id=".$snc->il_id." ORDER BY id ASC");
                                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                                    if ($row->id == $snc->ilce_id) {
                                        $ilce_adi = $row->ilce_adi;
                                    }
                                ?><option value="<?=$row->id;?>" <?=($row->id == $snc->ilce_id) ? 'selected' : '';?>><?=htmlspecialchars($row->ilce_adi, ENT_QUOTES, 'UTF-8');?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group kurumsal">
                    <label for="mahalle" class="col-sm-3 control-label">Mahalle</label>
                    <div class="col-sm-9">
                        <select class="form-control" onchange="yazdir();" name="mahalle" id="semt">
                            <option value="">Seçiniz</option>
                            <option value="0">Yok</option>
                            <?php
                            if ($snc->ilce_id != 0) {
                                $semtler = $db->query("SELECT * FROM semt WHERE ilce_id=".$snc->ilce_id);
                                if ($semtler->rowCount() > 0) {
                                    while ($srow = $semtler->fetch(PDO::FETCH_OBJ)) {
                                        $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE semt_id=".$srow->id." AND ilce_id=".$snc->ilce_id." ORDER BY mahalle_adi ASC");
                                        if ($mahalleler->rowCount() > 0) {
                                        ?><optgroup label="<?=htmlspecialchars($srow->semt_adi, ENT_QUOTES, 'UTF-8');?>"><?php
                                            while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                                                if ($snc->mahalle_id == $row->id) {
                                                    $mahalle_adi = $row->mahalle_adi;
                                                }
                                            ?><option value="<?=$row->id;?>" <?=($snc->mahalle_id == $row->id) ? 'selected' : '';?>><?=htmlspecialchars($row->mahalle_adi, ENT_QUOTES, 'UTF-8');?></option><?php
                                            }
                                        ?></optgroup><?php
                                        }
                                    }
                                } else {
                                    $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE ilce_id=".$snc->ilce_id." ORDER BY mahalle_adi ASC");
                                    while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                                        if ($snc->mahalle_id == $row->id) {
                                            $mahalle_adi = $row->mahalle_adi;
                                        }
                                    ?><option value="<?=$row->id;?>" <?=($snc->mahalle_id == $row->id) ? 'selected' : '';?>><?=htmlspecialchars($row->mahalle_adi, ENT_QUOTES, 'UTF-8');?></option><?php
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="adres" class="col-sm-3 control-label">Açık Adres</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="adres" name="adres" value="<?=htmlspecialchars($snc->adres, ENT_QUOTES, 'UTF-8');?>" placeholder="">
                    </div>
                </div>
                <div class="form-group kurumsal">
                    <label for="maps" class="col-sm-3 control-label">Google Maps<br><span style="font-weight:lighter;font-size:14px;">Harita konumu otomatik olarak belirlediğiniz il/ilçe/mahalle'ye göre işaretlenmektedir. Dilerseniz cadde veya sokak ekleyerek de daraltabilirsiniz. Hassas işaretleme için imleci sürükleyip bırakınız.</span></label>
                    <div class="col-sm-9">
                        <div class="form-group" style="float:left; width:170px;">
                            <label class="col-sm-1 control-label">Şehir</label>
                            <div class="col-sm-11">
                                <input disabled class="form-control" id="map_il" value="<?=htmlspecialchars($il_adi, ENT_QUOTES, 'UTF-8');?>" type="text">
                            </div><!-- col end -->
                        </div><!-- row end -->
                        <div class="form-group" style="float:left; width:170px;">
                            <label class="col-sm-1 control-label">İlçe</label>
                            <div class="col-sm-11">
                                <input disabled id="map_ilce" class="form-control" value="<?=htmlspecialchars($ilce_adi, ENT_QUOTES, 'UTF-8');?>" type="text">
                            </div><!-- col end -->
                        </div><!-- row end -->
                        <div class="form-group" style="float:left; width:170px;">
                            <label class="col-sm-1 control-label">Mahalle</label>
                            <div class="col-sm-11">
                                <input disabled onchange="yazdir();" type="text" id="map_mahalle" class="form-control" value="<?=htmlspecialchars($mahalle_adi, ENT_QUOTES, 'UTF-8');?>">
                            </div><!-- col end -->
                        </div><!-- row end -->
                        <div class="form-group" style="float:left; width:170px;">
                            <label class="col-sm-1 control-label">Cadde</label>
                            <div class="col-sm-11">
                                <input onchange="yazdir();" type="text" class="form-control" name="map_cadde" value="" placeholder="varsa cadde giriniz.">
                            </div><!-- col end -->
                        </div><!-- row end -->
                        <div class="form-group" style="float:left; width:170px;">
                            <label class="col-sm-1 control-label">Sokak</label>
                            <div class="col-sm-11">
                                <input onchange="yazdir();" type="text" class="form-control" name="map_sokak" value="" placeholder="varsa sokak giriniz.">
                            </div><!-- col end -->
                        </div><!-- row end -->
                        <input type="text" class="form-control" id="map_adres" name="map_adres" placeholder="Adres yaznz..." style="display: none;">
                        <input type="text" id="coords" name="maps" value="<?=htmlspecialchars($snc->maps, ENT_QUOTES, 'UTF-8');?>" style="display:none;" />
                        <div id="map" style="width: 100%; height: 300px"></div>
                        <?php
                        $coords = ($snc->maps == '') ? '41.003917,28.967299' : $snc->maps;
                        list($lat, $lng) = explode(",", $coords);
                        ?>
                        <input type="hidden" value="<?=htmlspecialchars($lat, ENT_QUOTES, 'UTF-8');?>" id="g_lat">
                        <input type="hidden" value="<?=htmlspecialchars($lng, ENT_QUOTES, 'UTF-8');?>" id="g_lng">
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

                                jQuery('#map_adres').on('change', function () {
                                    var val = $(this).val();
                                    geocodeAddress(marker, geocoder, map, val);
                                });

                                google.maps.event.addListener(marker, 'dragend', function () {
                                    dragend(marker);
                                });

                            }

                            function geocodeAddress(marker, geocoder, resultsMap, address) {
                                if (address) {
                                    geocoder.geocode({'address': address}, function (results, status) {
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
</div>
</div>
</div>
<div class="panel panel-default">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" data-parent="#accordion-test" href="#gizlilikbildirim" class="collapsed" aria-expanded="false">Gizlilik ve Bildirim Ayarları</a>
</h4>
</div>

<div id="gizlilikbildirim" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
<div style="padding:20px;">
<div class="form-group">
    <label class="col-sm-3 control-label">GSM Gizle</label>
    <div class="col-sm-9">
        <div class="checkbox checkbox-success">
            <input id="telefond_check" type="checkbox" name="telefond" value="1" <?=($snc->telefond == 1) ? 'checked' : '';?>>
            <label for="telefond_check"><STRONG>Aktif</STRONG></label><br>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Sabit Tel Gizle</label>
    <div class="col-sm-9">
        <div class="checkbox checkbox-success">
            <input id="sabittelefond_check" type="checkbox" name="sabittelefond" value="1" <?=($snc->sabittelefond == 1) ? 'checked' : '';?>>
            <label for="sabittelefond_check"><STRONG>Aktif</STRONG></label><br>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">E-posta Adresi Gizle</label>
    <div class="col-sm-9">
        <div class="checkbox checkbox-success">
            <input id="epostad_check" type="checkbox" name="epostad" value="1" <?=($snc->epostad == 1) ? 'checked' : '';?>>
            <label for="epostad_check"><STRONG>Aktif</STRONG></label><br>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Profil Resmi Gizle</label>
    <div class="col-sm-9">
        <div class="checkbox checkbox-success">
            <input id="avatard_check" type="checkbox" name="avatard" value="1" <?=($snc->avatard == 1) ? 'checked' : '';?>>
            <label for="avatard_check"><STRONG>Aktif</STRONG></label><br>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">SMS Bildirimleri</label>
    <div class="col-sm-9">
        <div class="checkbox checkbox-success">
            <input id="sms_izin_check" type="checkbox" name="sms_izin" value="1" <?=($snc->sms_izin == 1) ? 'checked' : '';?>>
            <label for="sms_izin_check"><STRONG>Aktif</STRONG></label><br>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">E-Posta Bildirimleri</label>
    <div class="col-sm-9">
        <div class="checkbox checkbox-success">
            <input id="mail_izin_check" type="checkbox" name="mail_izin" value="1" <?=($snc->mail_izin == 1) ? 'checked' : '';?>>
            <label for="mail_izin_check"><STRONG>Aktif</STRONG></label><br>
        </div>
    </div>
</div>
</div>
</div>
</div>


<div class="panel panel-default">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" data-parent="#accordion-test" href="#limitlendirme" class="collapsed" aria-expanded="false">Limitlendirme Ayarları</a>
</h4>
</div>

<div id="limitlendirme" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
<div style="padding:20px;">
<div class="alert alert-info" role="alert">Üyenin standart olarak tanımlı olmasını istediğiniz limitlendirme ayarlarını düzenleyebilirsiniz.</div>

<div class="form-group kurumsal">
    <label class="col-sm-3 control-label">Danışman Ekleme</label>
    <div class="col-sm-1">
        <input type="text" name="danisman_limit" class="form-control" value="<?=htmlspecialchars($snc->danisman_limit, ENT_QUOTES, 'UTF-8');?>">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Aylık İlan Limiti</label>
    <div class="col-sm-1">
        <input type="text" name="aylik_ilan_limit" class="form-control" value="<?=htmlspecialchars($snc->aylik_ilan_limit, ENT_QUOTES, 'UTF-8');?>">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Resim Ekleme Limiti</label>
    <div class="col-sm-1">
        <input type="text" name="ilan_resim_limit" class="form-control" value="<?=htmlspecialchars($snc->ilan_resim_limit, ENT_QUOTES, 'UTF-8');?>">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">İlan Yayında Kalma Süresi</label>
    <div class="col-sm-1">
        <input type="text" name="ilan_yayin_sure" class="form-control" value="<?=htmlspecialchars($snc->ilan_yayin_sure, ENT_QUOTES, 'UTF-8');?>">
    </div>
    <div class="col-sm-2">
        <select name="ilan_yayin_periyod" class="form-control">
        <?php
        foreach ($periyod as $k => $v) {
        ?><option value="<?=$k;?>" <?=($snc->ilan_yayin_periyod == $k) ? "selected" : '';?>><?=htmlspecialchars($v, ENT_QUOTES, 'UTF-8');?></option><?php
        }
        ?>
        </select>
    </div>
</div>
</div>
</div>
</div>


<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion-test" href="#istatistik" class="collapsed" aria-expanded="false">İstatistik</a>
        </h4>
    </div>

    <div id="istatistik" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
        <div style="padding:20px;">
            <div class="form-group">
                <label class="col-sm-3 control-label">Oluşturma Tarihi</label>
                <div class="col-sm-9">
                    <span style="display:block;margin-top:7px;">
                    <?=htmlspecialchars($snc->olusturma_tarih, ENT_QUOTES, 'UTF-8');?>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Son Giriş Tarihi</label>
                <div class="col-sm-9">
                    <span style="display:block;margin-top:7px;">
                    <?=htmlspecialchars($snc->son_giris_tarih, ENT_QUOTES, 'UTF-8');?>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Son IP Adresi</label>
                <div class="col-sm-9">
                    <span style="display:block;margin-top:7px;">
                    <?=htmlspecialchars($snc->ip, ENT_QUOTES, 'UTF-8');?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

</div><!-- accordion end -->

<div align="right">
    <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms','form_status');">Kaydet</button>
</div>

</form>

</div><!-- tab1 end -->

<div class="tab-pane" id="tab2">

    <div class="btn-group">
        <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=ilan_ekle&acid=<?=htmlspecialchars($snc->id, ENT_QUOTES, 'UTF-8');?>';">
            <i class="fa fa-plus"></i> Yeni Ekle
        </button>
    </div>

    <div class="clear:both"></div>

    <?php
    if ($snc->turu == 1) {
        $dids = $db->query("SELECT kid, id, GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=".$snc->id)->fetch(PDO::FETCH_OBJ);
        $danismanlar = $dids->danismanlar;
        $acids = ($danismanlar == '') ? $snc->id : $snc->id.','.$danismanlar;
    } else {
        $acids = $snc->id;
    }

    $qry = $pagent->sql_query("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND acid IN(".$acids.") AND ekleme=1 AND tipi=4 ORDER BY durum ASC, id DESC", $gvn->rakam($_GET["git"]), 500);
    $query = $db->query($qry['sql']);
    $adet = $qry["toplam"];
    if ($adet > 0) {
    ?>
    <table class="datatable table table-hover mails">
    <thead>
    <tr>
        <th>Başlık</th>
        <th>Fiyat</th>
        <th>Durum</th>
        <th title="Güncel Tarih">G.Tarih</th>
        <th>Kontroller</th>
    </tr>
    </thead>
    <tbody>
    <?php
    while ($row = $query->fetch(PDO::FETCH_OBJ)) {
        $fiyat_int = $gvn->para_int($row->fiyat);
        $fiyat = $gvn->para_str($fiyat_int);
    ?>
    <tr id="row_<?=$row->id;?>">
        <td><?=htmlspecialchars($row->baslik, ENT_QUOTES, 'UTF-8');?></td>
        <td><strong><?=htmlspecialchars($fiyat, ENT_QUOTES, 'UTF-8').' '.htmlspecialchars($row->pbirim, ENT_QUOTES, 'UTF-8');?></strong></td>
        <td>
        <?php
        if ($row->durum == 0) {
        ?><span style="color:red;font-weight:bold;">Onay Bekliyor</span><?php
        } elseif ($row->durum == 1) {
        ?><span style="color:green;font-weight:bold;">Yayında</span><?php
        } elseif ($row->durum == 2) {
        ?><span style="color:green;font-weight:bold;">Reddedildi</span><?php
        } elseif ($row->durum == 3) {
        ?><span style="color:orange;font-weight:bold;">Pasif</span><?php
        }
        ?>
        </td>
        <td><?=date("d.m.Y", strtotime($row->gtarih));?></td>
        <td>
        <div class="btn-group dropdown">
            <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="index.php?p=ilan_duzenle&id=<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>&acid=<?=htmlspecialchars($row->acid, ENT_QUOTES, 'UTF-8');?>">Görüntüle / Düzenle</a></li>
                <li><a href="javascript:;" onclick="if(confirm('Bu işlemi gerçekten yapmak istiyor musunuz ?')){ajaxHere('ajax.php?p=ilanlar&id=<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>','form_status');}">Sil</a></li>
            </ul>
        </div>
        </td>
    </tr>
    <?php
    }
    ?>
    </tbody>
    </table>

    <?php if ($adet > 500) { ?>
    <div class="clear"></div>
    <div class="sayfalama">
    <?php echo $pagent->listele('index.php?p=uye_duzenle&id='.htmlspecialchars($snc->id, ENT_QUOTES, 'UTF-8').'&git=', $gvn->rakam($_GET["git"]), $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
    </div>
    <?php } ?>

    <?php } else { ?>
    <b style="color:#aa1818;">İlan Bulunmuyor.</b> <br />
    <?php } ?>

</div><!-- tab2 end -->

<?php if ($snc->turu == 1) { ?>
<div class="tab-pane" id="tab3">

    <table class="table table-hover mails datatable">
    <thead>
    <tr>
        <th style="display:none">Seç</th>
        <th>Adı Soyadı</th>
        <th>E-Posta</th>
        <th>Telefon</th>
        <th>Oluşturma Tarihi</th>
        <th>Kontroller</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    $sorgu = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=".$snc->id." ORDER BY id DESC LIMIT 0,500");
    while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
        $i += 1;
    ?>
    <tr id="danismanrow_<?=$row->id;?>">
        <td style="display:none" class="mail-select"><?=$i;?></td>
        <td><b><a href="index.php?p=uye_duzenle&id=<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>"><?=htmlspecialchars($row->adi.' '.$row->soyadi, ENT_QUOTES, 'UTF-8');?></a></b></td>
        <td><?=htmlspecialchars($row->email, ENT_QUOTES, 'UTF-8');?></td>
        <td><?=htmlspecialchars($row->telefon, ENT_QUOTES, 'UTF-8');?></td>
        <td><?=date("d.m.Y H:i", strtotime($row->olusturma_tarih));?></td>
        <td>
        <div class="btn-group dropdown">
            <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="index.php?p=uye_duzenle&id=<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>">Görüntüle</a></li>
                <li><a href="javascript:;" onclick="DanismanSil(<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>);">Sil</a></li>
            </ul>
        </div>
        </td>
    </tr>
    <?php
    }
    ?>
    </tbody>
    </table>

</div><!-- tab3 end -->
<?php } ?>
							
					
<?php if($snc->turu == 0 || $snc->turu == 1){?>
<div class="tab-pane" id="tab4">
    <table class="table table-hover mails datatable">
        <thead>
        <tr>
            <th style="display:none">Sıra</th>
            <th>Paket</th>
            <th>Alış Tarihi</th>
            <th>Tutar</th>
            <th>Ödeme Yöntemi</th>
            <th>Durum</th>
            <th>Kontroller</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sorgu = $db->query("SELECT * FROM upaketler_501 WHERE acid=".$snc->id." ORDER BY durum ASC,id DESC LIMIT 0,500");
        $i = 0;
        while($row = $sorgu->fetch(PDO::FETCH_OBJ)){
            $i++;
        ?>
        <tr id="upaket<?=$row->id;?>">
            <td style="display:none"><?=$i;?></td>
            <td><?=htmlspecialchars($row->adi, ENT_QUOTES, 'UTF-8');?></td>
            <td><?=date("d.m.Y H:i",strtotime($row->tarih));?></td>
            <td><strong title="<?=htmlspecialchars($row->sure." ".$periyod[$row->periyod], ENT_QUOTES, 'UTF-8');?>"><?=$gvn->para_str($row->tutar);?> <?=dil("UYELIKP_PBIRIMI");?></strong></td>
            <td><?=htmlspecialchars($row->odeme_yontemi, ENT_QUOTES, 'UTF-8');?></td>
            <td id="upaket<?=$row->id;?>_durum"><?php
                echo ($row->durum == 0) ? '<strong style="color:red">Onay Bekleniyor</strong>' : ''; 
                echo ($row->durum == 1) ? '<strong style="color:green">Onaylandı</strong>' : ''; 
                echo ($row->durum == 2) ? '<strong style="color:black">İptal Edildi</strong>' : ''; 
            ?></td>
            <td>
                <div class="btn-group dropdown">
                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                    <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="index.php?p=upaket_duzenle&id=<?=$row->id;?>">Düzenle</a></li>
                        <li><a href="javascript:void(0);" onclick="if(confirm('Onaylamak istiyor musunuz?')){ajaxHere('ajax.php?p=upaketler&onayla=<?=$row->id;?>','hidden_result');}">Onayla</a></li>
                        <li><a href="javascript:;" onclick="if(confirm('Silmek istiyor musunuz?')){ajaxHere('ajax.php?p=upaketler&sil=<?=$row->id;?>','hidden_result');}">Sil</a></li>
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
<?php } ?>

<div class="tab-pane" id="tab5">
    <table class="table table-hover mails datatable">
        <thead>
        <tr>
            <th style="display:none">Sıra</th>
            <th>İlan</th>
            <th>Alış Tarihi</th>
            <th>Tutar</th>
            <th>Ödeme Yöntemi</th>
            <th>Durum</th>
            <th>Kontroller</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $gids = [];
        $ilanlar = [];
        $sorgu = $db->query("SELECT * FROM dopingler_group_501 WHERE acid=".$snc->id." ORDER BY durum ASC,id DESC LIMIT 0,500");
        $x = 0;
        while($row = $sorgu->fetch(PDO::FETCH_OBJ)){
            $x++;
            $gids[] = $row;
            $ilan = $db->prepare("SELECT id,url,baslik FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
            $ilan->execute([$row->ilan_id]);
            if($ilan->rowCount() > 0){
                $ilan = $ilan->fetch(PDO::FETCH_OBJ);
                $baslik = $ilan->baslik;
                $ilanlar[$row->id] = $ilan;
            }
        ?>
        <tr id="doping<?=$row->id;?>">
            <td style="display:none"><?=$x;?></td>
            <td><a href="index.php?p=ilan_duzenle&id=<?=$ilan->id;?>" target="_blank"><?=htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8');?></a></td>
            <td><?=date("d.m.Y H:i",strtotime($row->tarih));?></td>
            <td><strong><?=$gvn->para_str($row->tutar);?> <?=dil("DOPING_PBIRIMI");?></strong></td>
            <td><?=htmlspecialchars($row->odeme_yontemi, ENT_QUOTES, 'UTF-8');?></td>
            <td id="doping<?=$row->id;?>_durum"><?php
                echo ($row->durum == 0) ? '<strong style="color:red">Onay Bekleniyor</strong>' : ''; 
                echo ($row->durum == 1) ? '<strong style="color:green">Onaylandı</strong>' : ''; 
                echo ($row->durum == 2) ? '<strong style="color:black">İptal Edildi</strong>' : ''; 
            ?></td>
            <td>
                <div class="btn-group dropdown">
                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">İşlemler</button>
                    <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false"><i class="caret"></i></button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" data-toggle="modal" data-target="#Group<?=$row->id;?>">Detaylar</a></li>
                        <li><a href="javascript:;" onclick="if(confirm('Onaylamak istiyor musunuz?')){ajaxHere('ajax.php?p=dopingler&onayla=<?=$row->id;?>','hidden_result');}">Onayla</a></li>
                        <li><a href="javascript:;" onclick="if(confirm('Silmek istiyor musunuz?')){ajaxHere('ajax.php?p=dopingler&sil=<?=$row->id;?>','hidden_result');}">Sil</a></li>
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


<?php
    foreach($gids as $group){
        $id = $group->id;
        $ilan = $ilanlar[$id];
    ?>
    <div id="Group<?=$id;?>" class="modal fade detay-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Doping Detayları</h4>
                </div>
                <div class="modal-body">
                    <div id="form<?=$id;?>_status"></div>
                    <form role="form" class="form-horizontal" id="forms<?=$id;?>" method="POST" action="ajax.php?p=doping_duzenle&id=<?=$id;?>" onsubmit="return false;" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">İlan</label>
                            <div class="col-sm-9">
                                <span style="display:block; margin-top:7px;">
                                    <a href="index.php?p=ilan_duzenle&id=<?=$ilan->id;?>" target="_blank"><?=htmlspecialchars($ilan->baslik, ENT_QUOTES, 'UTF-8');?></a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Durum:</label>
                            <div class="col-sm-9">
                                <div class="radio radio-info radio-inline">
                                    <input type="radio" id="durum1_<?=$id;?>" value="1" name="durum" <?=($group->durum == 1) ? 'checked' : '';?>>
                                    <label for="durum1_<?=$id;?>">Onaylandı</label>
                                </div>
                                <div class="radio radio-info radio-inline">
                                    <input type="radio" id="durum2_<?=$id;?>" value="2" name="durum" <?=($group->durum == 2) ? 'checked' : '';?>>
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
                                ?><option <?=($group->odeme_yontemi == $yontem) ? 'selected' : ''; ?>><?=htmlspecialchars($yontem, ENT_QUOTES, 'UTF-8');?></option><?php
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Toplam Tutar</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="xtutar" value="<?=$gvn->para_str($group->tutar);?>" placeholder="">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
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
$dopingler_501 = $db->query("SELECT * FROM dopingler_501 WHERE gid=".$id." ORDER BY did ASC");
while($row = $dopingler_501->fetch(PDO::FETCH_OBJ)){
?>
<input type="hidden" name="ids[]" value="<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>">
<div class="form-group">
  <label class="col-sm-3 control-label"><?=htmlspecialchars($row->adi, ENT_QUOTES, 'UTF-8');?></label>
  <div class="col-sm-1">
    <input type="text" class="form-control" name="sure[<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>]" value="<?=htmlspecialchars($row->sure, ENT_QUOTES, 'UTF-8');?>" placeholder="Süre">
  </div>
  <div class="col-sm-2">
    <select class="form-control" name="periyod[<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>]">
      <?php
      foreach($periyod AS $k=>$v){
      ?><option value="<?=htmlspecialchars($k, ENT_QUOTES, 'UTF-8');?>"<?=($row->periyod == $k) ? " selected" : '';?>><?=htmlspecialchars($v, ENT_QUOTES, 'UTF-8');?></option><?php
      }
      ?>
    </select>
  </div>
  <div class="col-sm-2">
    <input type="text" class="form-control" name="tutar[<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>]" value="<?=$gvn->para_str($row->tutar);?>" placeholder="">
  </div>
  <div class="col-sm-4">
    <input type="text" class="form-control" name="btarih[<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>]" value="<?=($row->btarih == '') ? '' : date("d.m.Y",strtotime($row->btarih));?>" placeholder="Bitiş Tarihi Örn:25.05.2017">
  </div>
</div>
<?php
}
?>

</form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-success" onclick="AjaxFormS('forms<?=$id;?>','form<?=$id;?>_status');"><i class="fa fa-check"></i> Kaydet</button>
  <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Kapat</button>
</div>
</div>
</div>
</div>
<?php
}
?>

</div><!-- tab5 end -->

<div class="tab-pane<?=($goto == 'mesajlar') ? " active " : '';?>" id="tab6">
<div class="row">
  <div class="col-md-3">
    <h4><strong>Kişiler</strong></h4>
    <hr>
    <div class="contact-list nicescroll">
      <ul class="list-group contacts-list">
        <?php
        $uid = $gvn->zrakam($_GET["uid"]);
        $bid = $snc->id;
        try {
          $kisilerListe = $db->prepare("SELECT DISTINCT mr.* FROM mesajlar_501 AS mr INNER JOIN mesaj_iletiler_501 AS mi ON mi.mid = mr.id WHERE (mr.kimden=:idim OR mr.kime=:idim) AND ((mr.kime=:idim) OR (mr.kimden=:idim)) ORDER BY mr.starih ASC");
          $kisilerListe->execute(['idim' => $bid]);
        } catch (PDOException $e) {
          die($e->getMessage());
        }

        $say = 0;
        while ($row = $kisilerListe->fetch(PDO::FETCH_OBJ)) {
          $say++;
          $acid = ($row->kimden != $bid) ? $row->kimden : 0;
          $acid = ($row->kime != $bid) ? $row->kime : $acid;
          $account = $db->query("SELECT id, adi, soyadi, avatar, avatard, unvan FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=".$acid)->fetch(PDO::FETCH_OBJ);
          $avatar = ($account->avatar == '') ? '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/thumb/'.htmlspecialchars($account->avatar, ENT_QUOTES, 'UTF-8');
          $aktifet = ($uid == $account->id) ? " active" : '';
          $adsoyad = ($account->unvan != '') ? htmlspecialchars($account->unvan, ENT_QUOTES, 'UTF-8') : htmlspecialchars($account->adi." ".$account->soyadi, ENT_QUOTES, 'UTF-8');
        ?>
        <li class="list-group-item<?=$aktifet;?>">
          <a href="javascript:SohbetGoster(<?=$acid;?>);">
            <div class="avatar">
              <img src="<?=$avatar;?>" alt="">
            </div>
            <span class="name"><?=$adsoyad;?></span>
          </a>
          <span class="clearfix"></span>
        </li>
        <?php
        }
        ?>
      </ul>
    </div>
  </div><!-- left col-md end -->           


<div class="col-md-9">
    <?php
    $adsoyadim = ($snc->unvan != '') ? htmlspecialchars($snc->unvan, ENT_QUOTES, 'UTF-8') : htmlspecialchars($snc->adi." ".$snc->soyadi, ENT_QUOTES, 'UTF-8');
    $avatarim = ($snc->avatar == '') ? '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/thumb/'.htmlspecialchars($snc->avatar, ENT_QUOTES, 'UTF-8');
    if($uid != 0){ // Eğer üye seçmişse...
      $uyeKontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=?");
      $uyeKontrol->execute([$uid]);
      if($uyeKontrol->rowCount() > 0){
    ?>
    <div class="chat-conversation">
      <ul class="conversation-list nicescroll" id="iletiler">
        <?php
        $uye = $uyeKontrol->fetch(PDO::FETCH_OBJ);
        $uyavatar = ($uye->avatar == '') ? '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : '/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/thumb/'.htmlspecialchars($uye->avatar, ENT_QUOTES, 'UTF-8');
        $uyadsoyad = ($uye->unvan != '') ? htmlspecialchars($uye->unvan, ENT_QUOTES, 'UTF-8') : htmlspecialchars($uye->adi." ".$uye->soyadi, ENT_QUOTES, 'UTF-8');
        $MesajLine = $db->prepare("SELECT * FROM mesajlar_501 WHERE (kimden=:bana AND kime=:ona) OR (kimden=:ona AND kime=:bana)");
        $MesajLine->execute(['bana' => $bid, 'ona' => $uid]);
        if($MesajLine->rowCount() > 0){
          $MesajLine = $MesajLine->fetch(PDO::FETCH_OBJ);
          $mesaj_id = $MesajLine->id;
        } else {
          $mesaj_id = 0;
        }

        $miletiler = $db->prepare("SELECT DISTINCT mi.* FROM mesaj_iletiler_501 AS mi INNER JOIN mesajlar_501 AS mr ON mi.mid=mr.id WHERE mi.mid=:mesaj_id AND ((mr.kime=:bid) OR (mr.kimden=:bid)) ORDER BY id ASC");
        $miletiler->execute(['mesaj_id' => $mesaj_id, 'bid' => $bid]);
        while ($row = $miletiler->fetch(PDO::FETCH_OBJ)) {
          $benyazdm = ($row->gid == $bid) ? " odd" : '';
          $avatari = ($row->gid == $bid) ? $avatarim : $uyavatar;
          $asne = ($row->gid == $bid) ? $adsoyadim : $uyadsoyad;
          $tarih = explode("|", date("d.m.Y|H:i", strtotime($row->tarih)));
          $tarihi = $tarih[0];
          $saati = $tarih[1];
        ?>
        <li class="clearfix<?=$benyazdm;?>">
          <div class="chat-avatar">
            <img src="<?=$avatari;?>" alt="">
            <i><?=$tarihi;?><br/><?=$saati;?></i>
          </div>
          <div class="conversation-text">
            <div class="ctext-wrap">
              <i><?=$asne;?></i>
              <p><?=htmlspecialchars($row->ileti, ENT_QUOTES, 'UTF-8');?></p>
            </div>
          </div>
        </li>
        <?php } ?>
      </ul>
    </div>
    <?php } } else { ?>
      <h4 style="text-align:center;margin-top:60px;"><strong>Lütfen sol taraftan seçim yapınız.</strong><br><br>Anlık görüşme geçmişini incelemek için sol taraftan seçim yapınız.<br><i style="margin-top:30px;font-size:65px;" class="fa fa-arrow-left" aria-hidden="true"></i></h4>
    <?php } ?>
  </div><!-- right col-md-6 end -->
</div><!-- .row end -->
</div><!-- tab6 end -->

</div>
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
<link href="assets/plugins/tagsinput/jquery.tagsinput.css" rel="stylesheet">
<link href="assets/plugins/toggles/toggles.css" rel="stylesheet">
<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
<link href="assets/vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
<link href="assets/plugins/colorpicker/colorpicker.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="assets/plugins/codemirror/codemirror.css">
<link rel="stylesheet" href="assets/plugins/codemirror/ambiance.css">
<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet">
<link href="assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
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
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script src="assets/plugins/tagsinput/jquery.tagsinput.min.js"></script>
<script src="assets/plugins/toggles/toggles.min.js"></script>
<script src="assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="assets/plugins/colorpicker/bootstrap-colorpicker.js"></script>
<script src="assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/plugins/spinner/spinner.min.js"></script>
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>
<script src="assets/pages/jquery.chat.js"></script>
<script src="assets/js/jquery.nicescroll.js"></script>
<script>

function DanismanSil(id){
    var x = confirm("Gerçekten Silmek İstiyor Musunuz?");
    
    if(x){
        var y = confirm("Üyenin ilanları silinsin mi?");
        y = y ? 1 : 0;
        ajaxHere("ajax.php?p=danisman_sil&id="+id+"&ilan_sil="+y,"hidden_result");
    }
}

jQuery(document).ready(function() {
    $('.summernote').summernote({
        height: 150, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: false, // set focus to editable area after initializing summernote
        onImageUpload: function(files, editor, welEditable) {
            sendFile(files[0], editor, welEditable);
        }
    });
    
    // Tags Input
    jQuery('.tags').tagsInput({width:'auto'});

    // Form Toggles
    jQuery('.toggle').toggles({on: true});

    // Time Picker
    jQuery('.timepicker').timepicker({defaultTIme: false});
    jQuery('.timepicker2').timepicker({showMeridian: false});
    jQuery('.timepicker3').timepicker({minuteStep: 15});

    // Date Picker
    jQuery('.datepicker').datepicker();
    jQuery('.datepicker-inline').datepicker();
    jQuery('.datepicker-multiple').datepicker({
        numberOfMonths: 3,
        showButtonPanel: true
    });

    // Color Picker
    $('.colorpicker-default').colorpicker({
        format: 'hex'
    });
    $('.colorpicker-rgba').colorpicker();

    // Spinner
    $('#spinner1').spinner();
    $('#spinner2').spinner({disabled: true});
    $('#spinner3').spinner({value:0, min: 0, max: 10});
    $('#spinner4').spinner({value:0, step: 5, min: 0, max: 200});

    $('.datatable').dataTable();
    TurKontrol();
    $("input[name='turu']").change(TurKontrol);
    $('#iletiler').animate({scrollTop:$('#iletiler')[0].scrollHeight},500);
});

/* Tür Kontrolü */
function TurKontrol(){
    var turu = $("input[name='turu']:checked").val();
    
    if(turu == 0){
        $(".kurumsal,.danisman").slideUp(500, function(){
            $(".bireysel").slideDown(500);
        });
    } else if(turu == 1){
        $(".bireysel,.danisman").slideUp(500, function(){
            $(".kurumsal").slideDown(500);
        });
    } else if(turu == 2){
        $(".bireysel,.kurumsal").slideUp(500, function(){
            $(".danisman").slideDown(500);
        });
    } else {
        $(".bireysel,.kurumsal,.danisman").slideUp(500);
    }
}

/* Harita Ayarları */
function yazdir(){
    var ulke = $("#ulke_id").val();
    ulke = $("#ulke_id option[value='"+ulke+"']").text();
    var il = $("#il").val();
    il = $("#il option[value='"+il+"']").text();
    var ilce = $("#ilce").val();
    ilce = $("#ilce option[value='"+ilce+"']").text();
    var maha = $("#semt").val();
    maha = $("#semt option[value='"+maha+"']").text();
    var cadde = $("input[name='map_cadde']").val();
    var sokak = $("input[name='map_sokak']").val();
    var neler = "";

    if(il != undefined && il != '' && il != '<?=dil("TX264");?>'){
        if(ulke != undefined && ulke != '' && ulke != '<?=dil("TX264");?>'){
            neler += ", " + ulke;
        }
        neler += il;
        $("#map_il").val(il);
        if(ilce != undefined && ilce != '' && ilce != '<?=dil("TX264");?>'){
            neler += ", " + ilce;
            $("#map_ilce").val(ilce);
            if(maha != undefined && maha != '' && maha != '<?=dil("TX264");?>'){
                neler += ", " + maha;
                $("#map_mahalle").val(maha);
            } else {
                $("#map_mahalle").val('');
            }
            if(cadde != undefined && cadde != '' && cadde != '<?=dil("TX264");?>'){
                neler += ", " + cadde;
            }
            if(sokak != undefined && sokak != '' && sokak != '<?=dil("TX264");?>'){
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

function GetMap(){
    $("#map_adres").trigger("change");
}

function SohbetGoster(uid){
    window.location.href='index.php?p=uye_duzenle&id=<?=$snc->id;?>&uid='+uid+'&goto=mesajlar#tab6';
}
</script>
<div id="hidden_result" style="display:none"></div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo htmlspecialchars($gayarlar->google_api_key, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap"></script>