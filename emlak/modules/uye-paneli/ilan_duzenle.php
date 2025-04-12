<?php
// Kullanıcıdan gelen ID'yi güvenli hale getirir
$id = $gvn->rakam($_GET["id"]);

// Veritabanında ilgili ID'ye sahip sayfayı kontrol eder
$kontrol = $db->prepare("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND id=?");
$kontrol->execute(array($id));
if ($kontrol->rowCount() < 1) {
    // Eğer sayfa bulunamazsa kullanıcıyı aktif ilanlar sayfasına yönlendirir
    header("Location: aktif-ilanlar");
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

// Kullanıcı hesabını ve kimliğini kontrol eder
$acc = $db->query("SELECT id,kid FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
$kid = $acc->kid;
if ($snc->acid != $hesap->id && $hesap->id != $kid) {
    // Eğer kullanıcı yetkili değilse aktif ilanlar sayfasına yönlendirir
    header("Location: aktif-ilanlar");
    die();
}

// İlgili ilan numarasına sahip sayfaları çeker
$multi = $db->query("SELECT id,ilan_no FROM sayfalar WHERE site_id_555=501 AND ilan_no=" . $snc->ilan_no . " ORDER BY id ASC");
$multict = $multi->rowCount();
$multif = $multi->fetch(PDO::FETCH_OBJ);
$multidids = $db->query("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM sayfalar WHERE site_id_555=501 AND ilan_no=" . $snc->ilan_no)->fetch(PDO::FETCH_OBJ)->ids;

// Dil bilgilerini çeker
$dilx = $db->query("SELECT * FROM diller_501 WHERE kisa_adi='" . $snc->dil . "' ")->fetch(PDO::FETCH_OBJ);

// Fiyat ve aidat bilgilerini güvenli hale getirir
$fiyat_int = $gvn->para_int($snc->fiyat);
$fiyat = $gvn->para_str($fiyat_int);

$aidat_int = $gvn->para_int($snc->aidat);
$aidat = $gvn->para_str($aidat_int);

// Galeri fotoğraflarını çeker
$yfotolar = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . $snc->id . " ORDER BY sira ASC");
$yfotolarcnt = $yfotolar->rowCount();

// Paket kontrolleri başlatılır
if ($hesap->kid == 0 && $hesap->turu == 0) { // Bireysel
    $acids = $hesap->id;
    $pkacid = $acids;
} elseif ($hesap->kid == 0 && $hesap->turu == 1) { // Kurumsal
    $dids = $db->query("SELECT kid,id,GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=" . $hesap->id)->fetch(PDO::FETCH_OBJ);
    $danismanlar = $dids->danismanlar;
    $acids = ($danismanlar == '') ? $hesap->id : $hesap->id . ',' . $danismanlar;
    $pkacid = $hesap->id;
} elseif ($hesap->kid != 0 && $hesap->turu == 2) { // Danışman
    $kurumsal = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $hesap->kid);
    if ($kurumsal->rowCount() > 0) {
        $kurumsal = $kurumsal->fetch(PDO::FETCH_OBJ);
        $dids = $db->query("SELECT kid,id,GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=" . $kurumsal->id)->fetch(PDO::FETCH_OBJ);
        $danismanlar = $dids->danismanlar;
        $acids = ($danismanlar == '') ? $kurumsal->id : $kurumsal->id . ',' . $danismanlar;
        $pkacid = $kurumsal->id;
    } else {
        $acids = $hesap->id;
        $pkacid = $acids;
    }
} else {
    $acids = $hesap->id;
    $pkacid = $acids;
}
// Paket kontrolleri biter

$aylik_ilan_limit = $hesap->aylik_ilan_limit;
$ilan_resim_limit = $hesap->ilan_resim_limit;
$ilan_yayin_sure = $hesap->ilan_yayin_sure;
$ilan_yayin_periyod = $hesap->ilan_yayin_periyod;

if (isset($kurumsal) && $kurumsal) {
    $aylik_ilan_limit = $kurumsal->aylik_ilan_limit;
    $ilan_resim_limit = $kurumsal->ilan_resim_limit;
    $ilan_yayin_sure = $kurumsal->ilan_yayin_sure;
    $ilan_yayin_periyod = $kurumsal->ilan_yayin_periyod;
}

$paketi = $db->query("SELECT * FROM upaketler_501 WHERE acid=" . $pkacid . " AND durum=1 AND btarih>NOW()");
if ($paketi->rowCount() > 0) {
    $paketi = $paketi->fetch(PDO::FETCH_OBJ);
    $aylik_ilan_limit = ($paketi->aylik_ilan_limit == 0) ? 99999 : $paketi->aylik_ilan_limit;
    $ilan_resim_limit = ($paketi->ilan_resim_limit == 0) ? 99999 : $paketi->ilan_resim_limit;
    $ilan_yayin_sure = ($paketi->ilan_yayin_sure == 0) ? 120 : $paketi->ilan_yayin_sure;
    $ilan_yayin_periyod = ($paketi->ilan_yayin_sure == 0) ? "yillik" : $paketi->ilan_yayin_periyod;
    $paketegore = "AND pid=" . $paketi->id . " ";
}

$buay = date("Y-m");
$aylik_ilan_limit -= $db->query("SELECT tarih,id FROM sayfalar WHERE site_id_555=501 AND ekleme=1 AND tipi=4 " . $paketegore . "AND acid IN(" . $acids . ") AND tarih LIKE '%" . $buay . "%' ")->rowCount();

$resim_limit = $ilan_resim_limit;
$ilan_resim_limit -= $yfotolarcnt;

?>
<div class="headerbg" <?=($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . $gayarlar->belgeler_resim . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?=dil("TX255");?></h1>
            <div class="sayfayolu">
                <span><?=dil("TX256");?></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">
    <div class="uyepanel">
        <div class="sidebar">
            <?php include THEME_DIR . "inc/uyepanel_sidebar.php"; ?>
        </div>
        
        <div class="content">
            <div class="uyedetay">
                <div class="uyeolgirisyap">
                    <h4 class="uyepaneltitle"><?=dil("TX255");?></h4>
                    
                    <style>
                        /* Sekme listesini stillendirin */
                        ul.tab {
                            list-style-type: none;
                            margin: 0;
                            padding: 0;
                            overflow: hidden;
                            border: 1px solid #eee;
                            background-color: #fff;
                            margin-top:42px;
                        }
                        
                        /* Liste öğelerini yan yana yerleştirin */
                        ul.tab li {float: left;}
                        
                        /* Liste öğelerinin içindeki bağlantıları stillendirin */
                        ul.tab li a {
                            display: inline-block;
                            color: black;
                            text-align: center;
                            padding: 14px 50px;
                            text-decoration: none;
                            transition: 0.3s;
                            font-size: 17px;
                        }
                        
                        /* Bağlantıların üzerine gelindiğinde arka plan rengini değiştirme */
                        ul.tab li a:hover {background-color: #ddd;}
                        
                        /* Aktif/geçerli sekme bağlantısı sınıfı oluşturun */
                        ul.tab li a:focus, .active {background-color: #eee;}
                        
                        /* Sekme içeriğini stillendirin */
                        .tabcontent {
                            display: none;
                            padding: 6px 12px;
                            border: 1px solid #eee;
                            border-top: none;
                        }
                        .tabcontent h4 { margin-top:10px; }
                        .tabcontent {
                            -webkit-animation: fadeEffect 1s;
                            animation: fadeEffect 1s; /* Geçiş efekti 1 saniye sürer */
                        }
                        
                        @-webkit-keyframes fadeEffect {
                            from {opacity: 0;}
                            to {opacity: 1;}
                        }
                        
                        @keyframes fadeEffect {
                            from {opacity: 0;}
                            to {opacity: 1;}
                        }
                    </style>
                    
                    <ul class="tab">
                        <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'ilandetaylari')" <?=($_GET["goto"]=="") ? 'id="defaultOpen"' : '';?>><i class="fa fa-info" aria-hidden="true"></i> İlan Detayları</a></li>
                        <?php if ($multict > 0 && $snc->id == $multif->id): ?>
                            <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'ilanfotolari')" <?=($_GET["goto"]=="photos") ? 'id="defaultOpen"' : '';?>><i class="fa fa-camera" aria-hidden="true"></i> İlan Fotoğrafları</a></li>
                            <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'ilanvideo')" <?=($_GET["goto"]=="videos") ? 'id="defaultOpen"' : '';?>><i class="fa fa-video-camera" aria-hidden="true"></i> İlan Videosu</a></li>
                            <?php if ($gayarlar->dopingler_501 == 1): ?>
                                <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'dopingler_501')" <?=($_GET["goto"]=="doping") ? 'id="defaultOpen"' : '';?>><i class="fa fa-rocket" aria-hidden="true"></i> Dopingler</a></li>
                            <?php endif; ?>
                        <?php endif; // ana ilan ?>
                    </ul>

                    <div id="ilandetaylari" class="tabcontent">
                        <form action="ajax.php?p=ilan_duzenle&id=<?=$snc->id;?>" method="POST" id="IlanOlusturForm" enctype="multipart/form-data">
                            <div class="clear"></div>
                            <?=str_replace(array("[aylik_ilan_limit]","[ilan_resim_limit]","[ilan_yayin_sure]"),array($aylik_ilan_limit,$resim_limit,$ilan_yayin_sure . " " . $periyod[$ilan_yayin_periyod]),$fonk->get_lang($snc->dil,"TX258"));?>
                            <div class="clear"></div>

                            <?php if ($multict > 0 && $snc->id == $multif->id): ?>
                                <ul class="etab">
                                    <li><a href="javascript:void(0)" class="etablinks" onclick="openTab(event, 'etab1')" id="edefaultOpen"><?=$dilx->gosterim_adi;?></a></li>
                                    <?php
                                    $dilop = array();
                                    $i = 1;
                                    $sql = $db->query("SELECT * FROM diller_501 WHERE kisa_adi!='" . $dilx->kisa_adi . "' ORDER BY sira ASC");
                                    while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                                        $i++;
                                        $dilop[$i] = $row;
                                        ?>
                                        <li><a href="javascript:void(0)" class="etablinks" onclick="openTab(event, 'etab<?=$i;?>')" id="edefaultOpen"><?=$row->gosterim_adi;?></a></li>
                                    <?php } ?>
                                </ul>

                                <?php
                                for ($o = 2; $o <= $i; $o++) {
                                    if (isset($dilop[$o])) {
                                        $op = $dilop[$o];
                                        $esnc = $db->query("SELECT * FROM sayfalar WHERE site_id_555=501 AND ilan_no='" . $snc->ilan_no . "' AND dil='" . $op->kisa_adi . "' ");
                                        if ($esnc->rowCount() > 0) {
                                            $esnc = $esnc->fetch(PDO::FETCH_OBJ);
                                        }
                                        ?>
                                        <div id="etab<?=$o;?>" class="etabcontent">
                                            <table width="100%" border="0">
                                                <tr>
                                                    <td><?=dil("TX258");?> <span style="color:red">*</span></td>
                                                    <td><input name="tabs[<?=$op->kisa_adi;?>][baslik]" value="<?=$esnc->baslik;?>" type="text"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><br>
                                                        <div class="ilanaciklamalar">
                                                            <h3><strong><?=dil("TX288");?></strong></h3>
                                                            <div class="clear"></div>
                                                            <textarea class="thetinymce" id="icerik<?=$o;?>" name="tabs[<?=$op->kisa_adi;?>][icerik]"><?=$esnc->icerik;?></textarea>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                                <div id="etab1" class="etabcontent">
                            <?php endif; ?>
                            <table width="100%" border="0">
                                <tr>
                                    <td><?=$fonk->get_lang($snc->dil,"TX258");?> <span style="color:red">*</span></td>
                                    <td><input name="baslik" type="text" value="<?=$snc->baslik;?>"></td>
                                </tr>
                                <tr>
                                    <td><?=$fonk->get_lang($snc->dil,"TX259");?> <span style="color:red">*</span></td>
                                    <td>
                                        <input name="fiyat" type="text" value="<?=$fiyat;?>" id="ilantutar" data-mask="#.##0" data-mask-reverse="true" data-mask-maxlength="false">
                                        <select name="pbirim" id="ilanpbirimi">
                                            <?php
                                            $pbirimler = explode(",",$fonk->get_lang($snc->dil,"PARA_BIRIMI"));
                                            foreach ($pbirimler as $birim) {
                                                ?><option <?=($snc->pbirim == $birim) ? 'selected' : '';?>><?=$birim;?></option><?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
								

<tr>
    <td><?=$fonk->get_lang($snc->dil,"TX260");?></td>
    <td><input disabled type="text" value="<?=$snc->ilan_no;?>"></td>
</tr>
<?php
$emlkdrm = $fonk->get_lang($snc->dil,"EMLK_DRM");
if($emlkdrm != ''){
    ?>
    <tr>
        <td><?=$fonk->get_lang($snc->dil,"TX261");?> <span style="color:red">*</span></td>
        <td>
            <select name="emlak_durum">
                <?php
                $parc = explode("<+>",$emlkdrm);
                foreach($parc as $val){
                    ?><option <?=($snc->emlak_durum == $val) ? 'selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<?php
$emlktp = $fonk->get_lang($snc->dil,"EMLK_TIPI");
if($emlktp != ''){
    ?>
    <tr>
        <td><?=$fonk->get_lang($snc->dil,"TX262");?> <span style="color:red">*</span></td>
        <td>
            <select name="emlak_tipi" onchange="konut_getir(this.options[this.selectedIndex].value);">
                <?php
                $parc = explode("<+>",$emlktp);
                $isyeri = $parc[1];
                $arsa = $parc[2];
                foreach($parc as $val){
                    ?><option <?=($snc->emlak_tipi == $val) ? 'selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<?php
$ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
$ulkelerc = $ulkeler->rowCount();
if($ulkelerc>1){
    ?>
    <tr>
        <td><?=$fonk->get_lang($snc->dil,"TX348");?> <span style="color:red">*</span></td>
        <td>
            <select id="ulke_id" name="ulke_id" onchange="ajaxHere('ajax.php?p=il_getir&ulke_id='+this.options[this.selectedIndex].value,'il'),$('#ilce').html(''),$('#semt').html(''),yazdir();">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
                <?php
                while($row = $ulkeler->fetch(PDO::FETCH_OBJ)){
                    ?><option value="<?=$row->id;?>" <?=($snc->ulke_id == $row->id) ? 'selected' : '';?>><?=$row->ulke_adi;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<tr>
    <td><?=$fonk->get_lang($snc->dil,"TX263");?> <span style="color:red">*</span></td>
    <td>
        <select id="il" name="il" onchange="ajaxHere('ajax.php?p=ilce_getir&il_id='+this.options[this.selectedIndex].value,'ilce'),$('#semt').html(''),yazdir();">
            <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
            <?php
            if($ulkelerc<2){
                $ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
                $sql = $db->query("SELECT id,il_adi FROM il WHERE ulke_id=".$ulke->id." ORDER BY id ASC");
            }else{
                $sql = $db->query("SELECT id,il_adi FROM il WHERE ulke_id=".$snc->ulke_id." ORDER BY id ASC");
            }
            while($row = $sql->fetch(PDO::FETCH_OBJ)){
                if($row->id == $snc->il_id){
                    $il_adi = $row->il_adi;
                }
                ?><option value="<?=$row->id;?>" <?=($row->id == $snc->il_id) ? 'selected' : '';?>><?=$row->il_adi;?></option><?php
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td><?=$fonk->get_lang($snc->dil,"TX265");?></td>
    <td>
        <select name="ilce" id="ilce" onchange="ajaxHere('ajax.php?p=mahalle_getir&ilce_id='+this.options[this.selectedIndex].value,'semt'),yazdir();">
            <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
            <option value="0"><?=$fonk->get_lang($snc->dil,'TX349');?></option>
            <?php
            if($snc->il_id != ''){
                $sql = $db->query("SELECT id,ilce_adi FROM ilce WHERE il_id=".$snc->il_id." ORDER BY id ASC");
                while($row = $sql->fetch(PDO::FETCH_OBJ)){
                    if($row->id == $snc->ilce_id){
                        $ilce_adi = $row->ilce_adi;
                    }
                    ?><option value="<?=$row->id;?>" <?=($row->id == $snc->ilce_id) ? 'selected' : '';?>><?=$row->ilce_adi;?></option><?php
                }
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td><?=$fonk->get_lang($snc->dil,"TX266");?></td>
    <td>
        <select onchange="yazdir();" name="mahalle" id="semt">
            <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
            <option value="0"><?=$fonk->get_lang($snc->dil,'TX349');?></option>
            <?php
            if($snc->ilce_id != 0){
                $semtler = $db->query("SELECT * FROM semt WHERE ilce_id=".$snc->ilce_id);
                if($semtler->rowCount()>0){
                    while($srow = $semtler->fetch(PDO::FETCH_OBJ)){
                        $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE semt_id=".$srow->id." AND ilce_id=".$snc->ilce_id." ORDER BY mahalle_adi ASC");
                        if($mahalleler->rowCount()>0){
                            ?><optgroup label="<?=$srow->semt_adi;?>"><?php
                            while($row = $mahalleler->fetch(PDO::FETCH_OBJ)){
                                if($snc->mahalle_id == $row->id){
                                    $mahalle_adi = $row->mahalle_adi;
                                }
                                ?><option value="<?=$row->id;?>" <?=($snc->mahalle_id == $row->id) ? 'selected' : '';?>><?=$row->mahalle_adi;?></option><?php
                            }
                        }
                        ?></optgroup><?php
                    }
                }else{
                    $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE ilce_id=".$snc->ilce_id." ORDER BY mahalle_adi ASC");
                    while($row = $mahalleler->fetch(PDO::FETCH_OBJ)){
                        if($snc->mahalle_id == $row->id){
                            $mahalle_adi = $row->mahalle_adi;
                        }
                        ?><option value="<?=$row->id;?>" <?=($snc->mahalle_id == $row->id) ? 'selected' : '';?>><?=$row->mahalle_adi;?></option><?php
                    }
                }
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td><?=$fonk->get_lang($snc->dil,"TX270");?> <span style="color:red">*</span></td>
    <td><input name="metrekare" type="text" value="<?=$snc->metrekare;?>"></td>
</tr>
<tr id="brut_metrekare_con">
    <td><?=$fonk->get_lang($snc->dil,"TX2701");?> </td>
    <td><input name="brut_metrekare" type="text" value="<?=$snc->brut_metrekare;?>"></td>
</tr>
<?php
if($snc->emlak_tipi == $isyeri){
    $knttipi = $fonk->get_lang($snc->dil,"KNT_TIPI2");
}else{
    $knttipi = $fonk->get_lang($snc->dil,"KNT_TIPI");
}
if($knttipi != ''){
    ?>
    <tr id="konut_tipi_con">
        <td><?=$fonk->get_lang($snc->dil,"TX267");?></td>
        <td>
            <select name="konut_tipi">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX267");?></option>
                <option value=""><?=$fonk->get_lang($snc->dil,"TX267");?></option>
                <?php
                $parc = explode("<+>",$knttipi);
                foreach($parc as $val){
                    ?><option <?=($snc->konut_tipi == $val) ? 'selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<?php
$kntsekli = $fonk->get_lang($snc->dil,"KNT_SEKLI");
if($kntsekli != ''){
    ?>
    <tr id="konut_sekli_con">
        <td><?=$fonk->get_lang($snc->dil,"TX268");?></td>
        <td>
            <select name="konut_sekli">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX268");?></option>
                <?php
                $parc = explode("<+>",$kntsekli);
                foreach($parc as $val){
                    ?><option <?=($snc->konut_sekli == $val) ? 'selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<?php
$bulundkat = $fonk->get_lang($snc->dil,"BULND_KAT");
if($bulundkat != ''){
    ?>
    <tr id="bulundugu_kat_con">
        <td><?=$fonk->get_lang($snc->dil,"TX269");?></td>
        <td>
            <select name="bulundugu_kat">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX269");?></option>
                <?php
                $parc = explode("<+>",$bulundkat);
                foreach($parc as $val){
                    ?><option <?=($snc->bulundugu_kat == $val) ? 'selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<?php
$ypidrm = $fonk->get_lang($snc->dil,"YAPI_DURUM");
if($ypidrm != ''){
    ?>
    <tr id="yapi_durum_con">
        <td><?=$fonk->get_lang($snc->dil,"TX271");?></td>
        <td>
            <select name="yapi_durum">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX271");?></option>
                <?php
                $parc = explode("<+>",$ypidrm);
                foreach($parc as $val){
                    ?><option <?=($snc->yapi_durum == $val) ? 'selected' : ''; ?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<?php
$odasayisiy = $fonk->get_lang($snc->dil,"ODA_SAYISI");
if($odasayisiy != ''){
    ?>
    <tr id="oda_sayisi_con">
        <td><?=$fonk->get_lang($snc->dil,"TX272");?></td>
        <td>
            <select name="oda_sayisi">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX272");?></option>
                <?php
                $parc = explode("<+>",$odasayisiy);
                foreach($parc as $val){
                    ?><option <?=($snc->oda_sayisi == $val) ? 'selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?> 


<tr id="bina_yasi_con">
    <td><?=$fonk->get_lang($snc->dil,"TX273");?></td>
    <td><input name="bina_yasi" type="text" value="<?=$snc->bina_yasi;?>"></td>
</tr>
<tr id="bina_kat_sayisi_con">
    <td><?=$fonk->get_lang($snc->dil,"TX274");?></td>
    <td><input name="bina_kat_sayisi" type="text" value="<?=$snc->bina_kat_sayisi;?>"></td>
</tr>
<?php
$isitma = $fonk->get_lang($snc->dil,"ISITMA");
if($isitma != ''){
    ?>
    <tr id="isitma_con">
        <td><?=$fonk->get_lang($snc->dil,"TX275");?></td>
        <td>
            <select name="isitma">
                <?php
                $parc = explode("<+>",$isitma);
                foreach($parc as $val){
                    ?><option <?=($snc->isitma == $val) ? 'selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<tr id="banyo_sayisi_con">
    <td><?=$fonk->get_lang($snc->dil,"TX276");?></td>
    <td><input name="banyo_sayisi" type="text" value="<?=$snc->banyo_sayisi;?>"></td>
</tr>
<tr id="esyali_con">
    <td><?=$fonk->get_lang($snc->dil,"TX277");?></td>
    <td>
        <select name="esyali">
            <option value=""><?=$fonk->get_lang($snc->dil,"TX277");?></option>
            <option value="1" <?=($snc->esyali == 1) ? 'selected' : '';?>><?=$fonk->get_lang($snc->dil,"TX167");?></option>
            <option value="0" <?=($snc->esyali == 0 && $snc->esyali != '') ? 'selected' : '';?>><?=$fonk->get_lang($snc->dil,"TX168");?></option>
        </select>
    </td>
</tr>
<?php
$kuldrm = $fonk->get_lang($snc->dil,"KUL_DURUM");
if($kuldrm != ''){
    ?>
    <tr id="kullanim_durum_con">
        <td><?=$fonk->get_lang($snc->dil,"TX278");?></td>
        <td>
            <select name="kullanim_durum">
                <?php
                $parc = explode("<+>",$kuldrm);
                foreach($parc as $val){
                    ?><option <?=($snc->kullanim_durum == $val) ? 'selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<tr id="site_ici_con">
    <td><?=$fonk->get_lang($snc->dil,"TX279");?></td>
    <td>
        <select name="site_ici">
            <option value=""><?=$fonk->get_lang($snc->dil,"TX279");?></option>
            <option value="1" <?=($snc->site_ici == 1) ? 'selected' : '';?>><?=$fonk->get_lang($snc->dil,"TX167");?></option>
            <option value="0" <?=($snc->site_ici == 0 && $snc->site_ici != '') ? 'selected' : '';?>><?=$fonk->get_lang($snc->dil,"TX168");?></option>
        </select>
    </td>
</tr>
<tr id="aidat_con">
    <td><?=$fonk->get_lang($snc->dil,"TX280");?></td>
    <td><input name="aidat" type="text" value="<?=$aidat;?>"></td>
</tr>
<tr class="arsa_icin">
    <td><?=$fonk->get_lang($snc->dil,"TX327");?></td>
    <td><input name="metrekare_fiyat" type="text" value="<?=$snc->metrekare_fiyat;?>"></td>
</tr>
<tr class="arsa_icin">
    <td><?=$fonk->get_lang($snc->dil,"TX328");?></td>
    <td><input name="ada_no" type="text" value="<?=$snc->ada_no;?>"></td>
</tr>
<tr class="arsa_icin">
    <td><?=$fonk->get_lang($snc->dil,"TX329");?></td>
    <td><input name="parsel_no" type="text" value="<?=$snc->parsel_no;?>"></td>
</tr>
<tr class="arsa_icin">
    <td><?=$fonk->get_lang($snc->dil,"TX330");?></td>
    <td><input name="pafta_no" type="text" value="<?=$snc->pafta_no;?>"></td>
</tr>
<tr class="arsa_icin">
    <td><?=$fonk->get_lang($snc->dil,"TX331");?></td>
    <td>
        <?php
        $kaks_emsal = $fonk->get_lang($snc->dil,"KAKS_EMSAL");
        if($kaks_emsal != ''){
            ?>
            <select name="kaks_emsal">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
                <?php
                $parc = explode("<+>",$kaks_emsal);
                foreach($parc as $val){
                    ?><option<?=($snc->kaks_emsal===$val) ? ' selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
            <?php
        }else{
            ?>
            <input name="kaks_emsal" type="text" value="<?=$snc->kaks_emsal;?>">
            <?php } ?>
    </td>
</tr>
<tr class="arsa_icin">
    <td><?=$fonk->get_lang($snc->dil,"TX332");?></td>
    <td>
        <?php
        $gabari = $fonk->get_lang($snc->dil,"GABARI");
        if($gabari != ''){
            ?>
            <select name="gabari">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
                <?php
                $parc = explode("<+>",$gabari);
                foreach($parc as $val){
                    ?><option<?=($snc->gabari=== $val) ? ' selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
            <?php
        }else{
            ?>
            <input name="gabari" type="text" value="<?=$snc->gabari;?>">
            <?php } ?>
    </td>
</tr>
<?php
$imar_drm = $fonk->get_lang($snc->dil,"IMAR_DURUM");
if($imar_drm != ''){
    ?>
    <tr class="arsa_icin">
        <td><?=$fonk->get_lang($snc->dil,"TX682");?></td>
        <td>
            <select name="imar_durum" class="form-control">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
                <?php
                $parc = explode("<+>",$imar_drm);
                foreach($parc as $val){
                    ?><option<?=($snc->imar_durum=== $val) ? ' selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<?php
$tapu_drm = $fonk->get_lang($snc->dil,"TAPU_DRM");
if($tapu_drm != ''){
    ?>
    <tr class="arsa_icin">
        <td><?=$fonk->get_lang($snc->dil,"TX333");?></td>
        <td>
            <select name="tapu_durumu" class="form-control">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
                <?php
                $parc = explode("<+>",$tapu_drm);
                foreach($parc as $val){
                    ?><option<?=($snc->tapu_durumu=== $val) ? ' selected' : '';?>><?=$val;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<tr class="arsa_icin">
    <td><?=$fonk->get_lang($snc->dil,"TX334");?></td>
    <td>
        <select name="katk" class="form-control">
            <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
            <option<?php echo ($snc->katk == $fonk->get_lang($snc->dil,"TX167")) ? ' selected' : ''; ?>><?=$fonk->get_lang($snc->dil,"TX167");?></option>
            <option<?php echo ($snc->katk == $fonk->get_lang($snc->dil,"TX168")) ? ' selected' : ''; ?>><?=$fonk->get_lang($snc->dil,"TX168");?></option>
        </select>
    </td>
</tr>
<?php
if($fonk->get_lang($snc->dil,"TX335") != ''){
    $exp = explode(",",$fonk->get_lang($snc->dil,"TX653"));
    ?>
    <tr>
        <td><?=$fonk->get_lang($snc->dil,"TX335");?></td>
        <td>
            <select name="krediu">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX264");?></option>
                <?php
                foreach($exp as $row){
                    ?><option<?=($row == $snc->krediu) ? " selected" : '';?>><?=$row;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<tr class="arsa_icin">
    <td><?=$fonk->get_lang($snc->dil,"TX336");?></td>
    <td>
        <select name="takas" class="form-control">
            <option value=""><?=$fonk->get_lang($snc->dil,"TX277");?></option>
            <option<?=($snc->takas == $fonk->get_lang($snc->dil,"TX167")) ? 'selected' : '';?>><?=$fonk->get_lang($snc->dil,"TX167");?></option>
            <option<?=($snc->takas == $fonk->get_lang($snc->dil,"TX168")) ? 'selected' : '';?>><?=$fonk->get_lang($snc->dil,"TX168");?></option>
        </select>
    </td>
</tr>
<?php
if($fonk->get_lang($snc->dil,"KIMDEN") != ''){
    $exp = explode(",",$fonk->get_lang($snc->dil,"KIMDEN"));
    ?>
    <tr>
        <td><?=$fonk->get_lang($snc->dil,"TX460");?></td>
        <td>
            <select name="kimden">
                <option value=""><?=$fonk->get_lang($snc->dil,"TX460");?></option>
                <?php
                foreach($exp as $row){
                    ?><option <?=($snc->kimden == $row) ? 'selected' : '';?>><?=$row;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>


<?php
// Yetki bilgilerini gösteren alan
if ($fonk->get_lang($snc->dil, "TX624") != '') {
    $exp = explode(",", $fonk->get_lang($snc->dil, "TX625"));
    ?>
    <tr>
        <td><?=$fonk->get_lang($snc->dil, "TX624");?></td>
        <td>
            <select name="yetkis">
                <option value=""><?=$fonk->get_lang($snc->dil, "TX624");?></option>
                <?php
                foreach ($exp as $row) {
                    ?><option <?=($snc->yetkis == $row) ? 'selected' : '';?>><?=$row;?></option><?php
                }
                ?>
            </select>
        </td>
    </tr>
<?php } ?>
<tr id="yetki_bilgisi_con">
    <td><?=dil("TX2731");?> <span style="color:red">*</span></td>
    <td><input name="yetki_bilgisi" type="text"></td>
</tr>
</table>
<br/>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> SÖZLEŞMELİ İLANLARINIZ </strong></font><br/><font color="black">Sözleşmeli İlanlarınızı, Daha Hızlı Pazarlamak İçin,</font><br/> <font color="black">Kutunun İçine <font color="red"><strong> 100 </font></strong><font color="black">Yazınız.</font></p>
    <input type="text" class="form-control" id="site_id_888" name="site_id_888" value="<?=$snc->site_id_888;?>" >
</div>
<div class="form-group" style="text-align: center;">
    <p><font color="blue"> Sözleşmeli İlanınızı, Üye Emlakçılarımız ile Paylaşmak İçin,</font>&nbsp&nbsp&nbsp<font color="black">Kutunun İçine <font color="red"><strong> 100 </font></strong><font color="black">Yazınız.</font></p>
    <input type="text" class="form-control" id="site_id_777" name="site_id_777" value="<?=$snc->site_id_777;?>" >
</div>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> KAPALI PORTFÖY İLANLARINIZ </strong></font><br/><font color="black"> Kapalı Portföy İlanlarınızı, Artık Sitenize, Girerek Saklayabilir</font><br/><font color="black">Kapalı Portföy İlanlarınızı, Üye Emlakçılarımız ile Paylaşmak için,</font><br/> <font color="black">Kutunun İçine <font color="red"><strong> 200 </font></strong><font color="black">Yazınız.</font></p>
    <input type="text" class="form-control" id="site_id_699" name="site_id_699" value="<?=$snc->site_id_699;?>" >
</div>
<div class="form-group" style="text-align: center;">
    <p><font color="blue"> Kapalı Portföy İlanlarınızı, KİMSE İLE PAYLAŞMAK İSTEMEZSENİZ,</font><br/> <font color="black"> Kutucuğun İçine size verdiğimiz <font color="red"><strong> KAPALI </font></strong><font color="black">Yazınız.</font></p>
    <input type="text" class="form-control" id="site_id_700" name="site_id_700" value="<?=$snc->site_id_700;?>" >
</div>
<div class="form-group" style="text-align: center;">
    <p><font color="blue"> Kapalı Portföy İlanlarınızı, GRUBUNUZDAKİ EMLAKÇILARLA PAYLAŞMAK İçin </font><br/> <font color="black"> Kutucuğun İçine,<font color="red"><strong> GRUP KAPALI </font></strong><font color="black">Yazınız.</font></p>
    <input type="text" class="form-control" id="site_id_701" name="site_id_701" value="<?=$snc->site_id_701;?>" >
</div>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> EMLAK TALEPLERİNİZİ PAYLAŞIN </strong></font><br/><font color="black"> Sitenize girdiğiniz Müşteri Talepleriniz, PORTALLARIMIZDA, Sizin Bilgileriniz ile yayınlanacaktır.</font><br/> <font color="blue">Emlak Taleplerinizi Paylaşmak için,</font><br/> <font color="black">Kutunun İçine <font color="red"><strong> 300 </font></strong><font color="black">Yazınız.</font></p>
    <input type="text" class="form-control" id="site_id_702" name="site_id_702" value="<?=$snc->site_id_702;?>" >
</div>

<table>    
<br/>
<div class="form-group" style="text-align: center;">
    <p><strong><font color="blue"> İLANINIZI, PORTALLARIMIZDA DA YAYINLAYABİLİRSİNİZ </font> <br/><font color="red">İSTEDİĞİNİZ ( Portalın / Portalların ) KUTUCUĞUNU TEKRAR İŞARETLEYİNİZ. </font></strong><br/><font color="black"> ( TEKRAR İŞARETLEMEDİĞİNİZ ( Portalımızda / Portallarımızda ) İLANINIZ YAYINLANMAZ.) </font><br/><font color="black">( İLANINIZ, Portallarımızda Sizin Bilgilerinizle Yayınlanır. )</font><br/><br/>
<table>    
<!-- Checkbox’lar Başlangıcı -->
<table>
    <div style="margin-top: 10px;">
        <!-- İZMİR EMLAK SİTESİ -->
        <label for="site_id_335_checkbox">İZMİR EMLAK SİTESİ  </label>
        <input type="checkbox" id="site_id_335_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" 
            <?php echo ($snc->site_id_335 == 335) ? 'checked' : ''; ?> />
        <label style="float:left;margin-right:10px;" for="site_id_335_checkbox" class="stm-checkbox-label"></label>
        <span style="margin-right:5px;font-size:14px;margin-top:5px;"> 
            ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( izmiremlaksitesi.com.tr )</strong></font> Portalımızda Yayınlanır. )
        </span>
        <input type="hidden" id="site_id_335" name="site_id_335" value="<?php echo $snc->site_id_335; ?>">
    </div>

    <div style="margin-top: 10px;">
        <!-- İSTANBUL EMLAK SİTESİ -->
        <label for="site_id_334_checkbox">İSTANBUL EMLAK SİTESİ  </label>
        <input type="checkbox" id="site_id_334_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" 
            <?php echo ($snc->site_id_334 == 334) ? 'checked' : ''; ?> />
        <label style="float:left;margin-right:10px;" for="site_id_334_checkbox" class="stm-checkbox-label"></label>
        <span style="margin-right:5px;font-size:14px;margin-top:5px;"> 
            ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( istanbulemlaksitesi.com.tr )</strong></font> Portalımızda Yayınlanır. )
        </span>
        <input type="hidden" id="site_id_334" name="site_id_334" value="<?php echo $snc->site_id_334; ?>">
    </div>

    <div style="margin-top: 10px;">
        <!-- ANKARA EMLAK SİTESİ -->
        <label for="site_id_306_checkbox">ANKARA EMLAK SİTESİ  </label>
        <input type="checkbox" id="site_id_306_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" 
            <?php echo ($snc->site_id_306 == 306) ? 'checked' : ''; ?> />
        <label style="float:left;margin-right:10px;" for="site_id_306_checkbox" class="stm-checkbox-label"></label>
        <span style="margin-right:5px;font-size:14px;margin-top:5px;"> 
            ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( ankaraemlaksitesi.com.tr )</strong></font> Portalımızda Yayınlanır. )
        </span>
        <input type="hidden" id="site_id_306" name="site_id_306" value="<?php echo $snc->site_id_306; ?>">
    </div>
</table>
<!-- Checkbox’lar Bitişi -->

<!-- JavaScript Kodu -->
<script>
function updateSiteIds() {
    var checkboxes = [
        { checkbox: document.getElementById('site_id_335_checkbox'), hiddenInput: document.getElementById('site_id_335'), siteId: 335 },
        { checkbox: document.getElementById('site_id_334_checkbox'), hiddenInput: document.getElementById('site_id_334'), siteId: 334 },
        { checkbox: document.getElementById('site_id_306_checkbox'), hiddenInput: document.getElementById('site_id_306'), siteId: 306 }
    ];

    checkboxes.forEach(function(entry) {
        if (entry.checkbox && entry.checkbox.checked) {
            entry.hiddenInput.value = entry.siteId; // İşaretliyse site ID’sini yaz
        } else if (entry.checkbox) {
            entry.hiddenInput.value = 0; // İşaretsizse 0 yaz
        }
    });
}

// Sayfa yüklendiğinde mevcut durumu hidden input’lara yansıt
window.onload = function() {
    updateSiteIds();
};
</script>

<?php
$delm1 = explode("<+>", $fonk->get_lang($snc->dil, "CEPHE"));
$delm2 = explode("<+>", $fonk->get_lang($snc->dil, "IC_OZELLIKLER"));
$delm3 = explode("<+>", $fonk->get_lang($snc->dil, "DIS_OZELLIKLER"));
$delm4 = explode("<+>", $fonk->get_lang($snc->dil, "ALTYAPI_OZELLIKLER"));
$delm5 = explode("<+>", $fonk->get_lang($snc->dil, "KONUM_OZELLIKLER"));
$delm6 = explode("<+>", $fonk->get_lang($snc->dil, "GENEL_OZELLIKLER"));
$delm7 = explode("<+>", $fonk->get_lang($snc->dil, "MANZARA_OZELLIKLER"));
$cdelm1 = count($delm1);
$cdelm2 = count($delm2);
$cdelm3 = count($delm3);
$cdelm4 = count($delm4);
$cdelm5 = count($delm5);
$cdelm6 = count($delm6);
$cdelm7 = count($delm7);

if ($cdelm1 > 1 OR $cdelm2 > 1 OR $cdelm3 > 1 OR $cdelm4 > 1 OR $cdelm5 > 1 OR $cdelm6 > 1 OR $cdelm7 > 1) {
    ?>
    <tr id="ozellikler_con">
        <td colspan="2">
            <div class="ilanaciklamalar">
                <h3><?=$fonk->get_lang($snc->dil, "TX284");?></h3>
                <?php
                $checkbox = 0;
                if ($cdelm1 > 1) {
                    $ielm = explode("<+>", $snc->cephe_ozellikler);
                    ?>
                    <div class="ilanozellik tipi_konut">
                        <h4><?=$fonk->get_lang($snc->dil, "TX285");?></h4>
                        <?php
                        foreach ($delm1 as $val) {
                            $checked = (in_array($val, $ielm)) ? 'checked' : '';
                            ?>
                            <span><label><input name="cephe_ozellikler[]" value="<?=$val;?>" <?=$checked;?> type="checkbox"> <?=$val;?></label></span>
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
                <?php
                if ($cdelm2 > 1) {
                    $ielm = explode("<+>", $snc->ic_ozellikler);
                    ?>
                    <div class="ilanozellik tipi_konut">
                        <h4><?=$fonk->get_lang($snc->dil, "TX286");?></h4>
                        <?php
                        foreach ($delm2 as $val) {
                            $checked = (in_array($val, $ielm)) ? 'checked' : '';
                            ?>
                            <span><label><input name="ic_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
                <?php
                if ($cdelm3 > 1) {
                    $ielm = explode("<+>", $snc->dis_ozellikler);
                    ?>
                    <div class="ilanozellik tipi_konut">
                        <h4><?=$fonk->get_lang($snc->dil, "TX287");?></h4>
                        <?php
                        foreach ($delm3 as $val) {
                            $checked = (in_array($val, $ielm)) ? 'checked' : '';
                            ?>
                            <span><label><input name="dis_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
                <?php
                if ($cdelm4 > 1) {
                    $ielm = explode("<+>", $snc->altyapi_ozellikler);
                    ?>
                    <div class="ilanozellik tipi_arsa">
                        <h4><?=$fonk->get_lang($snc->dil, "TX323");?></h4>
                        <?php
                        foreach ($delm4 as $val) {
                            $checked = (in_array($val, $ielm)) ? 'checked' : '';
                            ?>
                            <span><label><input name="altyapi_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
                <?php
                if ($cdelm5 > 1) {
                    $ielm = explode("<+>", $snc->konum_ozellikler);
                    ?>
                    <div class="ilanozellik tipi_arsa">
                        <h4><?=$fonk->get_lang($snc->dil, "TX324");?></h4>
                        <?php
                        foreach ($delm5 as $val) {
                            $checked = (in_array($val, $ielm)) ? 'checked' : '';
                            ?>
                            <span><label><input name="konum_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
                <?php
                if ($cdelm6 > 1) {
                    $ielm = explode("<+>", $snc->genel_ozellikler);
                    ?>
                    <div class="ilanozellik tipi_arsa">
                        <h4><?=$fonk->get_lang($snc->dil, "TX325");?></h4>
                        <?php
                        foreach ($delm6 as $val) {
                            $checked = (in_array($val, $ielm)) ? 'checked' : '';
                            ?>
                            <span><label><input name="genel_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
                <?php
                if ($cdelm7 > 1) {
                    $ielm = explode("<+>", $snc->manzara_ozellikler);
                    ?>
                    <div class="ilanozellik tipi_arsa">
                        <h4><?=$fonk->get_lang($snc->dil, "TX326");?></h4>
                        <?php
                        foreach ($delm7 as $val) {
                            $checked = (in_array($val, $ielm)) ? 'checked' : '';
                            ?>
                            <span><label><input name="manzara_ozellikler[]" type="checkbox" <?=$checked;?> value="<?=$val;?>"><?=$val;?></label></span>
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>
        </td>
    </tr>
<?php } ?>
								

<tr>
    <td colspan="2">
        <strong><i class="fa fa-map-marker" aria-hidden="true"></i> <?=$fonk->get_lang($snc->dil,"TX289");?></strong>
        <br><span style="font-size:13px;"><?=$fonk->get_lang($snc->dil,"TX0");?><?=$fonk->get_lang($snc->dil,"TX290");?></span>
        <br><br>
        <div class="gmapsecenek">
            <input disabled class="form-control" id="map_il" value="<?=$il_adi;?>" type="text" placeholder="Şehir">
            <input disabled id="map_ilce" class="form-control" value="<?=$ilce_adi;?>" type="text" placeholder="İlçe">
            <input disabled onchange="yazdir();" type="text" id="map_mahalle" class="form-control" value="<?=$mahalle_adi;?>" placeholder="Mahalle">
            <input onchange="yazdir();" type="text" class="form-control" name="map_cadde" value="" placeholder="Cadde">
            <input onchange="yazdir();" type="text" class="form-control" name="map_sokak" value="" placeholder="Sokak">
        </div>
        <input type="text" class="form-control" id="map_adres" name="map_adres" placeholder="Adres yazınız..." style="display: none;">
        <input type="text" id="coords" name="maps" value="<?=$snc->maps;?>" style="display:none;" />
        <div id="map" style="width: 100%; height: 300px"></div>
        <?php
        $coords = ($snc->maps == '') ? "41.003917,28.967299" : $snc->maps;
        list($lat, $lng) = explode(",", $coords);
        ?>
        <input type="hidden" value="<?php echo $lat; ?>" id="g_lat">
        <input type="hidden" value="<?php echo $lng; ?>" id="g_lng">
        <script type="text/javascript">
            function initMap() {
                var g_lat = parseFloat(document.getElementById("g_lat").value);
                var g_lng = parseFloat(document.getElementById("g_lng").value);
                var map = new google.maps.Map(document.getElementById('map'), {
                    dragable:true,
                    zoom: 15,
                    center: {lat:g_lat,lng:g_lng}
                });
                var geocoder = new google.maps.Geocoder();
                
                var marker = new google.maps.Marker({
                    position:{
                        lat:g_lat,
                        lng:g_lng
                    },
                    map:map,
                    draggable:true
                });
                
                jQuery('#map_adres').on('change', function(){
                    var val = $(this).val();
                    geocodeAddress(marker,geocoder, map,val);
                });

                google.maps.event.addListener(marker,'dragend',function(){
                    dragend(marker);
                });
            }

            function geocodeAddress(marker,geocoder, resultsMap,address) {
                if(address){
                    geocoder.geocode({'address': address}, function(results, status) {
                        if (status === 'OK') {
                            resultsMap.setCenter(results[0].geometry.location);
                            marker.setMap(resultsMap);
                            marker.setPosition(results[0].geometry.location);
                            dragend(marker);
                        } else {
                            console.log('Geocode was not successful for the following reason: ' + status+" word: "+address);
                        }
                    });
                }
            }
            
            function dragend(marker){
                var lat = marker.getPosition().lat();
                var lng = marker.getPosition().lng();
                $("#coords").val(lat+","+lng);
            }
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $gayarlar->google_api_key; ?>&callback=initMap"></script>
    </td>
</tr>
<tr>
    <td colspan="2"><br>
        <div class="ilanaciklamalar">
            <h3><strong><?=dil("TX288");?></strong></h3>
            <div class="clear"></div>
            <textarea class="thetinymce" id="icerik" name="icerik"></textarea>
        </div>
    </td>
</tr>
</table>
</div><!-- tab1 end -->
<br/><br/>
<div class="form-group">
    <label class="col-sm-3 control-label"><font color="red"><strong>İlan Notunuz</strong></font></label>
    <div class="col-sm-9">
        <textarea name="notu" class="form-control" placeholder="Bu notu sadece siz görebilirsiniz. (İlanı Paylaşacaksanız, buraya özel bir şey yazmayınız.)"><?=$snc->notu;?></textarea>
    </div>
</div>
<table width="100%" border="0">
    <tr>
        <td style="border:none" colspan="2">
            <div id="IlanOlusturForm_output" style="display:none"></div>
            <a href="javascript:;" id="IlanSubmit" onclick="IlanSubmit();" class="btn"><?=$fonk->get_lang($snc->dil,"TX291");?></a>
        </td>
    </tr>
</table>
</form>
</div>
</div>
<?php if ($multict > 0 && $snc->id == $multif->id) { ?>
    <div id="ilanfotolari" class="tabcontent">
        <?php
        $gurl = "ajax.php?p=ilan_galeri_resim_yukle&id=" . $id;
        ?>
        <link href="modules/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css">
        <script type="text/javascript">
            var gurl = "<?=$gurl;?>";
            var max_dosya = <?=$ilan_resim_limit;?>;

            function YuklemeBitti(){
                window.location.href = "uye-paneli?rd=ilan_duzenle&id=<?=$snc->id;?>&goto=photos";
            }
        </script>
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script src="modules/dropzone/dist/dropzone_galeri.js"></script>

        <?php if ($ilan_resim_limit <1) { ?>
            <div class="alert-error"><?=str_replace("[resim_limit]", $resim_limit, dil("TX612"));?></div>
            <div class="clear"></div>
        <?php } ?>

        <div class="alert alert-info" role="alert"><?=str_replace("[ilan_resim_limit]", $ilan_resim_limit, dil("TX339"));?></div>

        <div class="clear"></div>
        <br />
        <div class="m-b-30">
            <form action="#" class="dropzone" id="dropzone">
                <div class="fallback">
                    <input name="file" type="file" multiple="multiple">
                </div>
            </form>
        </div>

        <div class="clear"></div>
        <br />

        <?php
        $detect = (!isset($detect)) ? new Mobile_Detect : $detect;
        $isMobile = $detect->isMobile() || $detect->isTablet();
        ?>

        <form action="ajax.php?p=galeri_foto_guncelle&ilan_id=<?=$snc->id;?>" method="POST" id="ilanGaleriFotolar">
            <div class="ilandigerfotolar" id="yuklenen_fotolar">
                <ul id="list" class="uk-nestable"<?php echo !$isMobile ? ' data-uk-nestable="{maxDepth:1}"' : ''; ?>>
                    <?php
                    if ($yfotolarcnt > 0) {
                        $i = 0;
                        while ($row = $yfotolar->fetch(PDO::FETCH_OBJ)) {
                            $i += 1;
                            ?>
                            <li class="uk-nestable-item" id="xrow_<?=$row->id;?>" data-id="<?=$i;?>" data-idi="<?=$row->id;?>">
                                <div class="ilandetayfotos gallery">
                                    <?php if (!$isMobile) { ?><div class="ilanfototasi"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div><?php } ?>
                                    <div class="clear"></div>
                                    <a rel="prettyPhoto[gallery1]" href="https://www.turkiyeemlaksitesi.com.tr/uploads/<?=$row->resim;?>" target="_blank"><img src="https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$row->resim;?>" alt=""></a>
                                    <a title="Sil" class="ilanfotosil" href="javascript:ajaxHere('ajax.php?p=resim_sil&ilan_id=<?=$snc->id;?>&resim_id=<?=$row->id;?>','ilanGaleriFotolar_output');" id="xrowd_<?=$row->id;?>"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    <a style="z-index:33;" id="ilandondur" title="Döndür" class="ilanfotosil" href='javascript:window.open("<?=SITE_URL."rotate/".$row->id;?>", "mywindow","status=1,toolbar=0,resizable=1,width=500,height=500");'><i class="fa fa-repeat" aria-hidden="true"></i></a>
                                    <div style="position:absolute;font-size:13px;">
                                        <input type="radio" id="kapak-<?=$row->id;?>" class="radio-custom" name="kapak" value="<?=$row->resim;?>" <?=($row->resim == $snc->resim) ? 'checked' : '';?> style="width:100px;">
                                        <label for="kapak-<?=$row->id;?>" class="radio-custom-label"><span class="checktext"><?=dil("TX342");?></span></label>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul><!-- uk content nestable end -->
            </div>

            <div class="clear"></div>
            <hr style="border: 1px solid #eee;"><br>
            <div align="right">
                <a href="javascript:AjaxFormS('ilanGaleriFotolar','ilanGaleriFotolar_output');" class="btn"><?=dil("TX291");?></a>
                <div id="ilanGaleriFotolar_output" style="display:none; float: right; margin-top: 15px; margin-right: 15px;"></div>
            </div>
        </form>

        <style>
            .ilandetayfotos{float:left;margin:5px;height:115px;position:relative}
            .ilandetayfotos img{box-shadow:0px 0px 5px #b1b1b1}
            .ilanfotosil{margin-top:0px;left:0px;position:absolute;background:rgba(0,0,0,0.3);color:white;padding:5px;padding-left:10px;padding-right:10px;font-size:16px;border-radius:2px;cursor:pointer}
            .ilanfotosil:hover{background:red;color:white}
            #ilandondur{margin:0px;right:0px;left:auto}
            .ilanfototasi{filter:alpha(opacity=80);cursor:-webkit-grabbing;margin-top:60px;right:0px;position:absolute;background:rgba(0,0,0,0.3);color:white;padding:5px;padding-left:10px;padding-right:10px}
        </style>

        <div class="clear"></div>
    </div>

    <div id="ilanvideo" class="tabcontent">
        <?php if ($snc->video != '') { ?>
            <div id="VideoVarContent">
                <div align="center">
                    <video width="70%" height="500" controls><source src="https://www.turkiyeemlaksitesi.com.tr/uploads/videos/<?=$snc->video;?>" type="video/mp4"><?=dil("VIDEO_SUPPORT");?></video>
                </div>
                <div class="clear"></div>
                <div align="right">
                    <br />
                    <a href="javascript:;" class="btn" onclick="ajaxHere('ajax.php?p=video_sil&ilan_id=<?=$snc->id;?>','SilOutput');"><i class="fa fa-trash-o" aria-hidden="true"></i> Videoyu Kaldır</a>
                </div>
                <div class="clear"></div>
                <div id="SilOutput" style="display:none"></div>
            </div><!-- VideoVarContent end -->
        <?php } ?>

        <div id="galeri_video_ekle" <?=($snc->video != '') ? 'style="display:none"' : '';?>>
            <div class="alert alert-info" role="alert"><?=dil("TX4572");?></div>
            <div style="height:200px;float:left;width:100%;">
                <form action="ajax.php?p=galeri_video_guncelle&ilan_id=<?=$snc->id;?>&from=adv" method="POST" id="VideoForm" enctype="multipart/form-data">
                    <center><input type="file" name="video" id="VideoSec" /></center>
                    <div class="clear"></div>
                </form>
                <div class="yuklebar" id="YuklemeBar" style="display:none">
                    <span id="percent">0%</span>
                    <div class="yuklebarasama animated flash" id="YuklemeDurum"></div>
                </div>
                <div class="clear"></div>
            </div>
            <hr style="border: 1px solid #eee;"><br>
            <div align="right">
                <a style="margin-left: 15px;" class="btn" href="javascript:YuklemeBaslat();"><?=dil("TX442");?> <i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
            </div>
            <div class="clear"></div>
            <div align="right"><br>
                <div id="VideoForm_output" style="display:none"></div>
            </div>
            <div class="clear"></div>
        </div><!-- galeri video ekle end -->
    </div><!-- ilanvideo div end -->
				
				
<?php
// Doping ayarları kontrol ediliyor
if ($gayarlar->dopingler_501 == 1) { ?>
    <div id="dopingler_501" class="tabcontent">
        <?php
        list($dzaman1a, $dzaman1b) = explode("|", $gayarlar->dzaman1);
        list($dzaman2a, $dzaman2b) = explode("|", $gayarlar->dzaman2);
        list($dzaman3a, $dzaman3b) = explode("|", $gayarlar->dzaman3);
        $dzaman1b = $periyod[$dzaman1b];
        $dzaman2b = $periyod[$dzaman2b];
        $dzaman3b = $periyod[$dzaman3b];

        $odeme = $gvn->harf_rakam($_GET["odeme"]);
        $customs = $_SESSION["custom"];
        if ($fonk->bosluk_kontrol($customs) == false) {
            $custom = base64_decode($customs);
            $custom = json_decode($custom, true);
        }
        $from = "adv";
        ?>
        <?php if ($odeme == "true" && $_SESSION["custom"] != '') { ?>
            <form action="uye-paneli" method="GET" id="OdemeYontemiForm">
                <input type="hidden" name="rd" value="ilan_duzenle">
                <input type="hidden" name="id" value="<?=$snc->id;?>">
                <input type="hidden" name="goto" value="doping">

                <br>
                <table class="dopingtable" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td bgcolor="#eee"><h5><strong><?=dil("TX535");?></strong></h5></td>
                        <td align="center" bgcolor="#eee"><strong><?=dil("TX536");?></strong></td>
                    </tr>

                    <?php
                    $dopingler_501 = $custom["dopingler_501"];
                    foreach ($dopingler_501 as $row) {
                        ?>
                        <tr>
                            <td><?=dil("DOPING" . $row["did"]);?></td>
                            <td align="center">(<?=$row["sure"];?> <?=$periyod[$row["periyod"]];?>)<br><strong><?=$gvn->para_str($row["tutar"]);?> <?=dil("DOPING_PBIRIMI");?></strong></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                <h4 style="float:right;margin-top:25px;margin-bottom:25px;" id="ToplamOdenecek"><?=dil("TX523");?>: <strong><font id="toplam_tutar"><?=$gvn->para_str($custom["toplam_tutar"]);?></font> <?=dil("DOPING_PBIRIMI");?></strong></h4>

                <div class="clear"></div>
                <hr style="border: 1px solid #eee;"><br>
                <div style="width: 200px; margin: auto;">
                    <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX537");?></h4>

                    <input id="odeme1" class="radio-custom" name="odeme" value="havale_eft" type="radio" style="width:100px;">
                    <label for="odeme1" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?=dil("TX538");?></span></label>
                    <div class="clear"></div>

                    <?php if ($gayarlar->paytr == 1) { ?>
                        <input id="odeme2" class="radio-custom" name="odeme" value="paytr" type="radio" style="width:100px;">
                        <label for="odeme2" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?=dil("TX539");?></span></label>
                        <div class="clear"></div>
                    <?php } ?>

                    <?php if ($gayarlar->iyzico == 1) { ?>
                        <input id="odeme3" class="radio-custom" name="odeme" value="iyzico" type="radio" style="width:100px;">
                        <label for="odeme3" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext"><?=dil("TX539");?></span></label>
                        <div class="clear"></div>
                    <?php } ?>

                    <?php if ($gayarlar->paypal == 1) { ?>
                        <input id="odeme4" class="radio-custom" name="odeme" value="paypal" type="radio" style="width:100px;">
                        <label for="odeme4" class="radio-custom-label" style="margin-bottom:12px;"><span class="checktext">PayPal</span></label>
                        <div class="clear"></div>
                    <?php } ?>
                </div>
                <br>
                <hr style="border: 1px solid #eee;">
                <div class="clear"></div>
                <div align="right">
                    <a style="margin-left: 15px;" class="btn" href="javascript:void(0);" onclick="OdemeYap();" id="DopingleButon"><i class="fa fa-check" aria-hidden="true"></i> <?=dil("TX540");?></a>
                    <a class="btn" href="uye-paneli?rd=ilan_duzenle&id=<?=$id;?>&goto=doping"><i class="fa fa-arrow-left" aria-hidden="true"></i> <?=dil("TX541");?></a>
                </div>
                <div id="OdemeYontemiForm_output"></div>
            </form>
            <script type="text/javascript">
                var odeme_yontemi;

                function OdemeYap() {
                    odeme_yontemi = $("input[name='odeme']:checked").val();

                    if (odeme_yontemi == undefined || odeme_yontemi == '') {
                        $("#OdemeYontemiForm_output").html('<span class="error"><?=dil("TX542");?></span>');
                    } else {
                        $("#OdemeYontemiForm").submit();
                    }
                }
            </script>
            <div class="clear"></div>
        <?php } elseif ($odeme == "havale_eft" && $_SESSION["custom"] != '') { // Ödeme Banka Havale/EFT ile ise... ?>
            <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX543");?></h4>
            <p><?=$dayarlar->hesap_numaralari;?></p>
            <div style="text-align:center;margin-top:25px;">
                <a class="btn" href="javascript:;" onclick="ajaxHere('ajax.php?p=ilan_doping_siparis&id=<?=$snc->id;?>&odeme=havale_eft&from=<?=$from;?>','SipSonuc');"><?=dil("TX544");?> <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                <div class="clear"></div>
                <div id="SipSonuc"></div>
                <a class="btn" href="javascript:window.history.back();"><?=dil("TX515");?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
            </div>
        <?php } elseif ($odeme == "paytr" && $_SESSION["custom"] != '') { // Ödeme PayTR ile ise... ?>
            <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX543");?></h4>
            <?php
            if ($_SESSION["advfrom"] == '') {
                $_SESSION["advfrom"] = $from;
            }
            $fiyat_int = $custom["toplam_tutar"];
            $urunadi = dil("PAY_NAME");
            $oid = time();
            $ftutar = ($fiyat_int * 100);
            $ftutar = (stristr($ftutar, ".")) ? explode(".", $ftutar)[0] : $ftutar;

            $sipce = $db->prepare("INSERT INTO paytr_checks_501 SET acid=?, oid=?, status=?, custom=?, tarih=?, tutar=?");
            $sipce->execute([$hesap->id, $oid, 'waiting', $customs, $fonk->datetime(), $fiyat_int]);
            ?>
            <!-- SanalPos frame kodu -->
            <?php $fonk->paytr_frame($hesap->adi . " " . $hesap->soyadi, $hesap->email, $hesap->adres, $hesap->telefon, $urunadi, $ftutar, $oid); ?><!-- SanalPos frame kodu end -->
            <a class="btn" href="javascript:window.history.back();"><?=dil("TX515");?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
        <?php } elseif ($odeme == "iyzico" && $_SESSION["custom"] != '') { // Ödeme iyzico ile ise... ?>
            <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX543");?></h4>
            <?php
            if ($_SESSION["advfrom"] == '') {
                $_SESSION["advfrom"] = $from;
            }

            $fonk->iyzico_cek();

            class CheckoutFormSample
            {
                public function should_initialize_checkout_form($tutar, $adi, $soyadi, $email, $site_url) {
                    # create request class
                    $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
                    $request->setLocale(\Iyzipay\Model\Locale::TR);
                    $request->setConversationId("65465464646");
                    $request->setPrice($tutar);
                    $request->setPaidPrice($tutar);
                    $request->setBasketId("BI101");
                    $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
                    $request->setCallbackUrl($site_url . "odeme-sonuc");
                    $buyer = new \Iyzipay\Model\Buyer();
                    $buyer->setId("BY789");
                    $buyer->setName($adi);
                    $buyer->setSurname($soyadi);
                    #$buyer->setGsmNumber($gsm);
                    $buyer->setEmail($email);
                    $buyer->setIdentityNumber("74300864791");
                    #$buyer->setLastLoginDate("2015-10-05 12:43:35");
                    #$buyer->setRegistrationDate("2013-04-21 15:12:09");
                    $buyer->setRegistrationAddress("Address");
                    $buyer->setIp($_SERVER['REMOTE_ADDR']);
                    $buyer->setCity("Istanbul");
                    $buyer->setCountry("Turkey");
                    $buyer->setZipCode("34732");
                    $request->setBuyer($buyer);
                    $shippingAddress = new \Iyzipay\Model\Address();
                    $shippingAddress->setContactName("Jane Doe");
                    $shippingAddress->setCity("Istanbul");
                    $shippingAddress->setCountry("Turkey");
                    $shippingAddress->setAddress("Address");
                    $shippingAddress->setZipCode("34742");
                    $request->setShippingAddress($shippingAddress);
                    $billingAddress = new \Iyzipay\Model\Address();
                    $billingAddress->setContactName("Jane Doe");
                    $billingAddress->setCity("Istanbul");
                    $billingAddress->setCountry("Turkey");
                    $billingAddress->setAddress("Address");
                    $billingAddress->setZipCode("34742");
                    $request->setbillingAddress($billingAddress);
                    $basketItems = array();
                    $firstBasketItem = new \Iyzipay\Model\BasketItem();
                    $firstBasketItem->setId("BI101");
                    $firstBasketItem->setName("Test");
                    $firstBasketItem->setCategory1("Test1");
                    $firstBasketItem->setCategory2("Test2");
                    $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                    $firstBasketItem->setPrice($tutar);
                    #$firstBasketItem->setSubMerchantKey("sub merchant key");
                    #$firstBasketItem->setSubMerchantPrice("0.18");
                    $basketItems[0] = $firstBasketItem;
                    $request->setBasketItems($basketItems);
                    # make request
                    $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, Sample::options());
                    # print result
                    return $checkoutFormInitialize;
                    #return $checkoutFormInitialize->getCheckoutFormContent();
                }
            }

            $fiyat_int = $custom["toplam_tutar"];

            $sample = new CheckoutFormSample();
            $sonuc = $sample->should_initialize_checkout_form($fiyat_int, $hesap->adi, $hesap->soyadi, $hesap->email, SITE_URL);
            $stat = $sonuc->getstatus();
            if ($stat == 'success') {
                echo $sonuc->getCheckoutFormContent();
                ?>
                <div style="width: 80%;margin-top: 20px;">
                    <div id="iyzipay-checkout-form" class="responsive"></div>
                </div>
                <?php
            } else {
                echo '<span class="error">Hata Mesajı: ' . $sonuc->geterrorMessage() . '</span>';
            }
            ?>
            <a class="btn" href="javascript:window.history.back();"><?=dil("TX515");?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>

        <?php } elseif ($odeme == "paypal" && $_SESSION["custom"] != '') { // Ödeme PayPal ile ise... ?>
            <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?=dil("TX543");?></h4>
            <?php
            if ($_SESSION["advfrom"] == '') {
                $_SESSION["advfrom"] = $from;
            }
            ?>
            <center>
                <H4><?=dil("TX545");?></H4>
            </center>
            <div id="OdemeButon" style="text-align:center;margin-top:25px;"><a class="btn" href="javascript:;" onclick="ajaxHere('ajax.php?p=ilan_doping_siparis&id=<?=$snc->id;?>&odeme=paypal&from=<?=$from;?>','SipSonuc');"><?=dil("TX544");?> <i class="fa fa-angle-double-right" aria-hidden="true"></i></a></div>
            <h4 style="color:green;margin-top:20px; display:none" id="SipGoster"><?=dil("TX547");?></h4>
            <a class="btn" href="javascript:window.history.back();"><?=dil("TX515");?> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
            <div class="clear"></div>
            <div id="SipSonuc"></div>
        <?php } else { unset($_SESSION["custom"]); unset($_SESSION["advfrom"]); ?>
            <form action="ajax.php?p=ilan_dopingle&id=<?=$id;?>&from=<?=$from;?>" method="POST" id="DopingleForm">
                <div class="alert alert-info" role="alert"><?=dil("TX5182");?></div>
                <br>
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td bgcolor="#eee"><h5><strong><?=dil("TX519");?></strong></h5></td>
                        <td align="center" bgcolor="#eee"><strong><?=$dzaman1a . " " . $dzaman1b;?></strong></td>
                        <td align="center" bgcolor="#eee"><strong><?=$dzaman2a . " " . $dzaman2b;?></strong></td>
                        <td align="center" bgcolor="#eee"><strong><?=$dzaman3a . " " . $dzaman3b;?></strong></td>
                    </tr>

                    <?php
                    $dopingler_501 = $db->query("SELECT * FROM doping_ayarlar_501 ORDER BY id ASC");
                    $docount = $dopingler_501->rowCount();
                    $aco = 0;
                    while ($row = $dopingler_501->fetch(PDO::FETCH_OBJ)) {
                        ## Eğer aynı dopingten varsa ve süresi bitmemişse... ##
                        $isdoping = $db->prepare("SELECT id, durum FROM dopingler_501 WHERE ilan_id=? AND did=? AND btarih > NOW()");
                        $isdoping->execute([$snc->id, $row->id]);
                        ?>
                        <tr>
                            <td><?=dil("DOPING" . $row->id);?></td>

                            <?php if ($isdoping->rowCount() > 0) {
                                $isdoping = $isdoping->fetch(PDO::FETCH_OBJ);
                                ?>
                                <td align="center" colspan="3">
                                    <?php if ($isdoping->durum == 0) { ?>
                                        <h5 style="color:orange;"><i class="fa fa-clock-o"></i> <?=dil("TX533");?></h5>
                                    <?php } elseif ($isdoping->durum == 1) { ?>
                                        <h5 style="color:green;"><i class="fa fa-check"></i> <?=dil("TX526");?></h5>
                                    <?php } ?>
                                </td>
											

<?php } else { $aco += 1; ?>
    <td align="center"><label><input name="doping[<?=$row->id;?>]" class="checkbox_one" type="checkbox" value="1" data-money="<?=$row->fiyat1;?>"> <?=$gvn->para_str($row->fiyat1);?> <?=dil("DOPING_PBIRIMI");?></label></td>
    <td align="center"><label><input name="doping[<?=$row->id;?>]" class="checkbox_one" type="checkbox" value="2" data-money="<?=$row->fiyat2);?>"> <?=$gvn->para_str($row->fiyat2);?> <?=dil("DOPING_PBIRIMI");?></label></td>
    <td align="center"><label><input name="doping[<?=$row->id;?>]" class="checkbox_one" type="checkbox" value="3" data-money="<?=$row->fiyat3);?>"> <?=$gvn->para_str($row->fiyat3);?> <?=dil("DOPING_PBIRIMI");?></label></td>
<?php } ?>
</tr>
<?php } ?>
</table>

<div class="clear"></div>

<?php if ($aco > 0) { ?>
    <h4 style="float:right;margin-top:25px;margin-bottom:25px;" id="ToplamOdenecek"><?=dil("TX523");?>: <strong><font id="toplam_tutar">0</font> <?=dil("DOPING_PBIRIMI");?></strong></h4>

    <div class="clear"></div>

    <hr style="border: 1px solid #eee;"><br>
    <div align="right">
        <a style="margin-left: 15px;" class="btn" href="javascript:void(0);" onclick="AjaxFormS('DopingleForm','DopingleForm_output');" id="DopingleButon"><i class="fa fa-check" aria-hidden="true"></i> <?=dil("TX540");?></a>
    </div>
<?php } ?>

<div id="DopingleForm_output" style="display:none" align="left"></div>
<div class="clear"></div>
<script type="text/javascript">
    var formatter = new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY',
        minimumFractionDigits: 2,
    });

    $(document).ready(function(){
        ToplamDegisti();
        $(".checkbox_one").change(function(){
            var elem = $(this);
            $(".checkbox_one[name='"+elem.attr("name")+"']").not(this).prop("checked",false);
            ToplamDegisti();
        });
    });

    function ToplamDegisti(){
        var toplam_tutar = 0, tutar = 0;
        $("#dopingler_501 table tr td input:checked").each(function(index){
            tutar = $(this).attr("data-money");
            tutar = parseFloat(tutar);
            toplam_tutar += tutar;
        });

        if (toplam_tutar > 0) {
            toplam_tutar = formatter.format(toplam_tutar);
            toplam_tutar = toplam_tutar.replace("₺","");
            $("#toplam_tutar").html(toplam_tutar);
        } else {
            $("#toplam_tutar").html(toplam_tutar);
        }
    }
</script>
</form>
</div>
<?php } ?>
</div>
<?php } // ana ilan ?>
<script>

    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    document.getElementById("defaultOpen").click();
</script>

<script src="<?=THEME_DIR;?>tinymce/tinymce.min.js"></script>
<script type="application/x-javascript">
    tinymce.init({
        selector:".thetinymce",
        height: 300,
        language: 'tr',
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern imagetools'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image',
        toolbar2: 'print preview media | forecolor backcolor emoticons',
    });
</script>

<script type="text/javascript">
    function IlanSubmit() {
        var stbutton = $("#IlanSubmit");
        var stonc = stbutton.attr("onclick");
        var stinn = stbutton.html();
        stbutton.removeAttr("onclick");
        stbutton.html('Bekleyiniz...');

        $(".thetinymce").each(function(){
            var idi = $(this).attr("id");
            $("#"+idi).html(tinymce.get(idi).getContent());
        });

        $("#IlanOlusturForm_output").fadeOut(400);
        $("#IlanOlusturForm").ajaxForm({
            target: '#IlanOlusturForm_output',
            complete: function(){
                $("#IlanOlusturForm_output").fadeIn(400);

                stbutton.attr("onclick", stonc);
                stbutton.html(stinn);
            }
        }).submit();
    }

    $(document).ready(function(){
        var select = "#yetki_bilgisi_con,#brut_metrekare_con,#site_id_888_con,#site_id_777_con,#site_id_700_con,#site_id_701_con,#site_id_661_con,#site_id_662_con,#site_id_663_con,#site_id_664_con,#site_id_665_con,#yapi_durum_con,#oda_sayisi_con,#isitma_con,#banyo_sayisi_con,#esyali_con,#kullanim_durum_con,#site_ici_con,#aidat_con,.arsa_icin";
        <?php echo ($snc->emlak_tipi == $arsa) ? '$(".tipi_konut").slideUp(500); $(select).slideUp(500);' : ''; ?>
        <?php echo ($snc->emlak_tipi != $arsa) ? '$(".tipi_arsa,.arsa_icin").hide(1);' : ''; ?>

        $("select[name='emlak_tipi']").change(function(){
            var val = $(this).val();
            if (val == '<?=$arsa;?>') {
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
        if (tipi == "<?=$isyeri;?>") {
            $("select[name=konut_tipi]").html("<?php $knttipi = dil("KNT_TIPI2"); ?><option value=''><?=dil("TX57");?></option><?php $parc = explode("<+>", $knttipi); foreach($parc as $val) { ?><option><?=$val;?></option><?php } ?>");
        } else {
            $("select[name=konut_tipi]").html("<?php $knttipi = dil("KNT_TIPI"); ?><option value=''><?=dil("TX57");?></option><?php $parc = explode("<+>", $knttipi); foreach($parc as $val) { ?><option><?=$val;?></option><?php } ?>");
        }
    }

    function yazdir() {
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

        if (il != undefined && il != '' && il != '<?=dil("TX264");?>') {
            if (ulke != undefined && ulke != '' && ulke != '<?=dil("TX264");?>') {
                neler += ", " + ulke;
            }
            neler += il;
            $("#map_il").val(il);
            if (ilce != undefined && ilce != '' && ilce != '<?=dil("TX264");?>') {
                neler += ", " + ilce;
                $("#map_ilce").val(ilce);
                if (maha != undefined && maha != '' && maha != '<?=dil("TX264");?>') {
                    neler += ", " + maha;
                    $("#map_mahalle").val(maha);
                } else {
                    $("#map_mahalle").val('');
                }

                if (cadde != undefined && cadde != '' && cadde != '<?=dil("TX264");?>') {
                    neler += ", " + cadde;
                }

                if (sokak != undefined && sokak != '' && sokak != '<?=dil("TX264");?>') {
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

<script>
    function openTab(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("etabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("etablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementById("edefaultOpen").click();
</script>

<style>
    /* Listeyi stillendirin */
    ul.etab {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Liste öğelerini yan yana yerleştirin */
    ul.etab li {float: left;}

    /* Liste öğelerinin içindeki bağlantıları stillendirin */
    ul.etab li a {
        display: inline-block;
        color: black;
        text-align: center;
        padding: 14px 13px;
        text-decoration: none;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Bağlantıların üzerine gelindiğinde arka plan rengini değiştirme */
    ul.etab li a:hover {background-color: #ddd;}

    /* Aktif/geçerli sekme bağlantısı sınıfı oluşturun */
    ul.etab li a:focus, .active {background-color: #ccc;}

    /* Sekme içeriğini stillendirin */
    .etabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }
    .etabcontent h4 { margin-top:10px;}
    .etabcontent {
        -webkit-animation: fadeEffect 1s;
        animation: fadeEffect 1s; /* Geçiş efekti 1 saniye sürer */
    }

    @-webkit-keyframes fadeEffect {
        from {opacity: 0;}
        to {opacity: 1;}
    }

    @keyframes fadeEffect {
        from {opacity: 0;}
        to {opacity: 1;}
    }
</style>

<div id="TamamDiv" style="display:none">
    <!-- TAMAM MESAJ -->
    <div style="margin-bottom:70px;text-align:center;" id="BasvrTamam">
        <i style="font-size:80px;color:green;" class="fa fa-check"></i>
        <h2 style="color:green;font-weight:bold;"><?=dil("TX295");?></h2>
        <br/>
        <h4><?=dil("TX296");?></h4>
    </div>
    <!-- TAMAM MESAJ -->
</div>
</div>
</div>
</div>

<div class="clear"></div>
</div>
</div>