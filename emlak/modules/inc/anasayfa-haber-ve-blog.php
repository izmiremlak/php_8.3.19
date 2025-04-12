<!-- blok2 start -->
<div class="haberveblog fadein">
    <div class="haberveblog-overlay">
        <div id="wrapper">
            <div class="hbveblog fadeleft" id="haber_ve_duyurular">
                <h4><i class="fa fa-bullhorn" aria-hidden="true"></i> <?=dil("TX135");?></h4>
                <h6><a href="haber-ve-duyurular"><?=dil("TX206");?></a></h6>
                <div class="hbveblog-container">
                    <div class="foovekaciklama">
                        <?php
                        $i = 0;
                        $sql = $db->query("SELECT id, baslik, url, icerik, resim FROM sayfalar WHERE site_id_555=501 AND tipi=2 AND dil='".$dil."' ORDER BY id DESC LIMIT 0,5");
                        while($row = $sql->fetch(PDO::FETCH_OBJ)){
                            $i++;
                            $link = ($dayarlar->permalink == 'Evet') ? $row->url.'.html' : 'index.php?p=sayfa&id='.$row->id;
                            $icerik = strip_tags($row->icerik);
                        ?>
                        <div class="icerikler" style="display:none">
                            <a href="<?=$link;?>"><img title="<?=$row->baslik;?>" alt="<?=$row->baslik;?>" src="uploads/thumb/<?=$row->resim;?>" width="250" height="198"></a>
                            <p><? echo $fonk->kisalt($icerik,0,200); ?> <a href="<?=$link;?>"><strong><?=dil("TX207");?></strong></a><strong></strong></p>
                        </div>
                        <? } ?>
                    </div>
                    <div class="hbblogbasliklar">
                        <?php
                        $i = 0;
                        $eq = -1;
                        $sql = $db->query("SELECT baslik FROM sayfalar WHERE site_id_555=501 AND tipi=2 AND dil='".$dil."' ORDER BY id DESC LIMIT 0,5");
                        while($row = $sql->fetch(PDO::FETCH_OBJ)){
                            $i++;
                            $eq++;
                        ?>
                        <h5 data-index="<?=$eq;?>"><strong><?=$i;?>.)</strong> <?=$row->baslik;?></h5>
                        <? if($i != 5){ ?><span class="hbblogline"></span><? } ?>
                        <? } ?>
                    </div>
                </div>
            </div>

            <div class="hbveblog faderight" id="homeblog">
                <h4><i class="fa fa-rss" aria-hidden="true"></i> <?=dil("TX208");?></h4>
                <h6><a href="yazilar"><?=dil("TX206");?></a></h6>
                <div class="hbveblog-container">
                    <div class="foovekaciklama"> 
                        <?php
                        $i = 0;
                        $sql = $db->query("SELECT id, baslik, url, icerik, resim FROM sayfalar WHERE site_id_555=501 AND tipi=1 AND dil='".$dil."' ORDER BY id DESC LIMIT 0,5");
                        while($row = $sql->fetch(PDO::FETCH_OBJ)){
                            $i++;
                            $link = ($dayarlar->permalink == 'Evet') ? $row->url.'.html' : 'index.php?p=sayfa&id='.$row->id;
                            $icerik = strip_tags($row->icerik);
                        ?>
                        <div class="icerikler" style="display:none">
                            <a href="<?=$link;?>"><img title="<?=$row->baslik;?>" alt="<?=$row->baslik;?>" src="uploads/thumb/<?=$row->resim;?>" width="250" height="198"></a>
                            <p><? echo $fonk->kisalt($icerik,0,220); ?> <a href="<?=$link;?>"><strong><?=dil("TX207");?></strong></a><strong></strong></p>
                        </div>
                        <? } ?>
                    </div>
                    <div class="clearmob"></div>
                    <div class="hbblogbasliklar">
                        <?php
                        $i = 0;
                        $eq = -1;
                        $sql = $db->query("SELECT baslik FROM sayfalar WHERE site_id_555=501 AND tipi=1 AND dil='".$dil."' ORDER BY id DESC LIMIT 0,5");
                        while($row = $sql->fetch(PDO::FETCH_OBJ)){
                            $i++;
                            $eq++;
                        ?>
                        <h5 data-index="<?=$eq;?>"><strong><?=$i;?>.)</strong> <?=$row->baslik;?></h5>
                        <? if($i != 5){ ?><span class="hbblogline"></span><? } ?>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div><!-- wrap end -->
    </div>
</div>
<div class="clear"></div>
<!-- blok2 end -->