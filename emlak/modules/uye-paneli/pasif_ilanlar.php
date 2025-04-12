<div class="headerbg" <?=($gayarlar->belgeler_resim  != '') ? 'style="background-image: url(uploads/'.$gayarlar->belgeler_resim.');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?=dil("TX302");?></h1>
            <div class="sayfayolu">
                <span><?=dil("TX303");?></span>
            </div>
        </div>
    </div>
    <div class="headerwhite"></div>
</div>

<div id="wrapper">
    <div class="uyepanel">
        <div class="content">
            <?php
            if($gayarlar->reklamlar == 1){ // Eğer reklamlar aktif ise...
                $detect  = (!isset($detect)) ? new Mobile_Detect : $detect;
                $rtipi   = 9;
                $reklamlar = $db->query("SELECT id FROM reklamlar_199 WHERE tipi={$rtipi} AND (btarih > NOW() OR suresiz=1)");
                $rcount  = $reklamlar->rowCount();
                $order   = ($rcount > 1) ? "ORDER BY RAND()" : "ORDER BY id DESC";
                $reklam  = $db->query("SELECT * FROM reklamlar_199 WHERE tipi={$rtipi} AND durum=0 AND (btarih > NOW() OR suresiz=1) ".$order." LIMIT 0,1")->fetch(PDO::FETCH_OBJ);
                if($rcount > 0){
            ?>
            <!-- 728 x 90 Reklam Alanı -->
            <div class="ad728home">
                <?=($detect->isMobile() || $detect->isTablet()) ? $reklam->mobil_kodu : $reklam->kodu;?>
            </div>
            <!-- 728 x 90 Reklam Alanı END-->
            <?php }} ?>

            <div class="uyedetay">
                <div class="uyeolgirisyap">
                    <h4 class="uyepaneltitle"><?=dil("TX304");?></h4>

                    <?php
                    if($hesap->turu == 1){
                        $dids          = $db->query("SELECT kid,id,GROUP_CONCAT(id SEPARATOR ',') AS danismanlar_501 FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=".$hesap->id)->fetch(PDO::FETCH_OBJ);
                        $danismanlar   = $dids->danismanlar;
                        $acids         = ($danismanlar == '') ? $hesap->id : $hesap->id.','.$danismanlar;
                    }else{
                        $acids         = $hesap->id;
                    }

                    $git = $gvn->zrakam($_GET["git"]);

                    $qry = $pagent->sql_query("SELECT DISTINCT t1.id,t1.url,t1.tarih,t1.baslik,t1.durum,t1.ilan_no,t1.hit,t1.resim FROM sayfalar AS t1 WHERE (t1.btarih<NOW() OR t1.durum=2 OR t1.durum=3) AND t1.site_id_555=501 AND t1.tipi=4 AND t1.ekleme=1 AND t1.dil='".$dil."' AND t1.durum!=4 AND t1.acid IN(".$acids.") ORDER BY t1.id DESC",$git,6);
                    //$qry		= $pagent->sql_query("SELECT * FROM sayfalar WHERE site_id_555=501 AND tipi=4 AND ekleme=1 AND (durum=2 OR durum=3) AND acid IN(".$acids.") ORDER BY id DESC",$git,6);
                    $query = $db->query($qry['sql']);
                    $adet  = $qry['toplam'];
                    ?>

                    <?php if($adet > 0 ){ ?>
                    <div id="hidden_result" style="display:none"></div>
                    <table width="100%" border="0" id="datatable">
                        <thead style="background:#ebebeb;">  
                            <tr>  
                                <th align="center"><strong><?=dil("TX232");?></strong></th>  
                                <th align="left"><strong><?=dil("TX233");?></strong></th>  
                                <th id="mobtd" align="center"><strong><?=dil("TX234");?></strong></th> 
                                <th align="center"><strong><?=dil("TX235");?></strong></th> 
                            </tr>  
                        </thead> 
                        <?php while($row = $query->fetch(PDO::FETCH_OBJ)){
                            $ilink       = ($dayarlar->permalink == 'Evet') ? $row->url.'.html' : 'index.php?p=sayfa&id='.$row->id;
                            $ilan_tarih  = date("d.m.Y",strtotime($row->tarih));
                            $isexpire    = $db->query("SELECT DISTINCT t1.id FROM sayfalar AS t1 LEFT JOIN dopingler_501 AS t2 ON t2.ilan_id=t1.id AND t2.durum=1 WHERE (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.id=".$row->id." AND (t1.btarih>NOW() OR t2.btarih>NOW())");
                            $surebitis   = ($isexpire->rowCount()>0) ? true : false;
                        ?>
                        <tr id="row_<?=$row->id;?>">
                            <td><img src="/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$row->resim;?>" width="100" height="75"/></td>
                            <td><a href="<?=$ilink;?>"><strong><?=$row->baslik;?></strong></a><br>
                                <span class="ilantarih"><?=dil("TX309");?> <?=$ilan_tarih;?></span>
                                <span class="ilantarih"><?php if($row->durum == 1 OR $row->durum == 3){ ?><?=dil("TX315");?> <?=$row->hit;?><?php } ?> </span>
                                <span class="ilantarih"><?=dil("TX140");?>: <?=$row->ilan_no;?></span>
                            </td>
                            <td align="center" id="mobtd">
                                <?php
                                if($row->durum == 0){
                                    ?><span style="color:red;font-weight:bold;"><?=dil("TX310");?></span><?php
                                }elseif($row->durum == 1){
                                    ?><span style="color:orange;font-weight:bold;"><?=($surebitis) ? dil("TX311") : dil("TX585");?></span><?php
                                }elseif($row->durum == 2){
                                    ?><span style="color:orange;font-weight:bold;"><?=dil("TX312");?></span><?php
                                }elseif($row->durum == 3){
                                    ?><span style="color:orange;font-weight:bold;"><?=dil("TX313");?></span><?php
                                }
                                ?>
                            </td>
                            <td width="15%" align="center">
                                <?php if($row->durum != 2){ ?><a title="Düzenle" class="uyeilankontrolbtn" href="uye-paneli?rd=ilan_duzenle&id=<?=$row->id;?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a><?php } ?>
                                <?php if($row->durum == 3){ ?><a title="Aktif Yap" class="uyeilankontrolbtn" href="javascript:;" onclick="ajaxHere('ajax.php?p=ilan_aktif&id=<?=$row->id;?>','hidden_result');"><i style="color:green;" class="fa fa-power-off" aria-hidden="true"></i></a><?php } ?>
                                <a title="Sil" class="uyeilankontrolbtn" href="javascript:;" onclick="ajaxHere('ajax.php?p=ilan_sil&id=<?=$row->id;?>','hidden_result');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                    <div class="clear"></div>
                    <?php }else{ ?> 
                    <h4 style="text-align:center;margin-top:60px;"><?=dil("TX314");?></h4>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <?php include THEME_DIR."inc/uyepanel_sidebar.php"; ?>
        </div>

        <div class="clear"></div>
    </div>
</div>