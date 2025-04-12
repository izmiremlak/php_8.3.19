<?php
$baslangic = 0;
$bitis = 20;
$sql = $db->query("SELECT DISTINCT t1.ilan_no, t1.id, t1.url, t1.fiyat, t1.baslik, t1.resim, t1.il_id, t1.ilce_id, t1.emlak_durum, t1.pbirim, t1.emlak_tipi FROM sayfalar AS t1 LEFT JOIN dopingler_501 AS t2 ON t2.ilan_id=t1.id AND t2.durum=1 AND t2.did=3 WHERE t2.btarih>NOW() AND (t1.site_id_555=501 OR t1.site_id_888=100 OR t1.site_id_777=501501 OR t1.site_id_699=200 OR t1.site_id_701=501501 OR t1.site_id_702=300) AND t1.tipi=4 AND t1.durum=1 ORDER BY RAND() LIMIT ".$baslangic.",".$bitis);
if($sql->rowCount() > 0){
?>
<!-- Öne Çıkan İlanlar -->
<div id="wrapper"> 
    <div class="content" id="bigcontent">
        <div class="altbaslik">
            <div class="nextprevbtns">
                <span id="slider-next"><a id="prev4" class="bx-prev" href="" style="display: inline;"><i id="prevnextbtn" class="fa fa-angle-left"></i></a></span>
                <span id="slider-prev"><a id="next4" class="bx-next" href="" style="display: inline;"><i id="prevnextbtn" class="fa fa-angle-right"></i></a></span>
            </div>
            <h4 id="sicakfirsatlar1"><i class="fa fa-clock-o" aria-hidden="true"></i> <strong><a href="ilanlar?onecikan=true"><?=dil("TX479");?></a></strong></h4>
        </div>

        <div class="list_carousel">
            <ul id="foo4">
                <?php
                while($row = $sql->fetch(PDO::FETCH_OBJ)){

                    $row_lang = $db->query("SELECT ilan_no, id, url, fiyat, baslik, resim, il_id, ilce_id, emlak_durum, pbirim, emlak_tipi FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=".$row->ilan_no." AND dil='".$dil."' ");
                    if($row_lang->rowCount() > 0) $row = $row_lang->fetch(PDO::FETCH_OBJ);
                    
                    ?><li><?
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
                            <span class="ilandurum" <?php echo ($row->emlak_durum == $emstlk) ? 'id="satilik"' : ''; echo ($row->emlak_durum == $emkrlk) ? 'id="kiralik"' : ''; ?>><?=$row->emlak_durum;?>
                            /  <?=$row->emlak_tipi;?>
                            </span>
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
                    </li>
                <? } ?>
            </ul>
        </div>
    </div>
</div>
<div class="clear"></div>
<!-- Öne Çıkan İlanlar END-->
<?}?>
<style>
i.fa.fa-clock-o {
    color:#bba813;
}
</style>