<!-- Sıcak İlanlar start -->
<div id="wrapper">

<? if($gayarlar->hizmetler_sidebar == 1){ ?>
<div class="sidebar">
    <? include THEME_DIR."inc/advanced_search.php"; ?>
</div>
<? } ?>

<div class="content" <?=($gayarlar->hizmetler_sidebar == 0) ? 'id="bigcontent"' : '';?>>
    <div class="altbaslik">
        <div id="pager2" class="pager"></div>
        <h4 id="sicakfirsatlar"><span style="color: #4CAF50;"><i class="fa fa-check-circle-o" aria-hidden="true"></i> <strong><a href="ilanlar?sicak=true"><span style="color: #4CAF50;"><?=dil("TX205");?></a></strong></h4>
    </div>

    <div class="list_carousel">
        <ul id="foo2">
            <?php
            $baslangic = 0;
            $bitis = 12;
            
            $sql = $db->query("SELECT DISTINCT t1.ilan_no, t1.id, t1.url, t1.fiyat, t1.baslik, t1.resim, t1.il_id, t1.ilce_id, t1.emlak_durum, t1.pbirim, t1.emlak_tipi FROM sayfalar AS t1 LEFT JOIN dopingler_501 AS t2 ON t2.ilan_id=t1.id AND t2.durum=1 AND t2.did=1 WHERE t2.btarih>NOW() AND t1.tipi=4 AND (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.durum=1 ORDER BY RAND() LIMIT ".$baslangic.",".$bitis);
            
            if($sql->rowCount() > 0){
            ?>
            <li>
            <?php
            while($row = $sql->fetch(PDO::FETCH_OBJ)){
                $row_lang = $db->query("SELECT ilan_no, id, url, fiyat, baslik, resim, il_id, ilce_id, emlak_durum, pbirim, emlak_tipi FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=".$row->ilan_no." AND dil='".$dil."' ");
                if($row_lang->rowCount() > 0) $row = $row_lang->fetch(PDO::FETCH_OBJ);
                $link = ($dayarlar->permalink == 'Evet') ? $row->url.'.html' : 'index.php?p=sayfa&id='.$row->id;
                if($row->fiyat != 0){
                    $fiyat_int = $gvn->para_int($row->fiyat);
                    $fiyat = $gvn->para_str($fiyat_int);
                }
                $sc_il = $db->query("SELECT * FROM il WHERE id=".$row->il_id)->fetch(PDO::FETCH_OBJ);
                $sc_ilce = $db->query("SELECT * FROM ilce WHERE id=".$row->ilce_id)->fetch(PDO::FETCH_OBJ);
                $adres = $sc_il->il_adi;
                $adres .= ($sc_ilce->ilce_adi != '') ? ' / '.$sc_ilce->ilce_adi : '';
            ?>
            <a href="<?=$link;?>">
                <div class="kareilan">
                    <span class="ilandurum" <?php echo ($row->emlak_durum == $emstlk) ? 'id="satilik"' : ''; echo ($row->emlak_durum == $emkrlk) ? 'id="kiralik"' : ''; ?>><?=$row->emlak_durum;?> / <?=$row->emlak_tipi;?></span>
                    <img title="Sıcak Fırsat" alt="Sıcak Fırsat" src="https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$row->resim;?>" width="234" height="201">
                    <div class="fiyatlokasyon" <? echo ($row->emlak_durum == $emkrlk) ? 'id="lokkiralik"' : ''; ?>>
                        <? if($row->fiyat != '' OR $row->fiyat != 0){ ?><h3><?=$fiyat;?> <?=$row->pbirim;?></h3><? } ?> 
                        <h4><?=$fonk->kisalt2($adres, 0, 25);?></h4>
                    </div>
                    <div class="kareilanbaslik">
                        <h3><?=$fonk->kisalt($row->baslik, 0, 95);?><?=(strlen($row->baslik) > 95) ? '...' : '';?></h3>
                    </div> 
                </div>
            </a>
            <? } ?>
            </li>
            <?php
            } 
            ?>
        </ul>
        <div class="clearfix"></div>
    </div>
</div>

</div> <!-- wrapper end -->
<div class="clear"></div>
<!-- Sıcak ilanlar end -->