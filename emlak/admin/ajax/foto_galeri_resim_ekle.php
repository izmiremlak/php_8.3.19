<?php
if($hesap->id != "" AND $hesap->tipi != 0){
    if($_FILES){
        $resim1tmp = $_FILES['file']["tmp_name"];
        $resim1nm = $_FILES['file']["name"];

        $randnm = strtolower(substr(md5(uniqid(rand())), 0,10)).$fonk->uzanti($resim1nm);
        $resim = $fonk->resim_yukle(true,'file',$randnm,'/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads',$gorsel_boyutlari['foto_galeri']['thumb_x'],$gorsel_boyutlari['foto_galeri']['thumb_y']);
        $resim = $fonk->resim_yukle(false,'file',$randnm,'/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads',$gorsel_boyutlari['foto_galeri']['orjin_x'],$gorsel_boyutlari['foto_galeri']['orjin_y']);

        $db->query("INSERT INTO galeri_foto SET site_id_888=100,site_id_777=501501,site_id_699=200,site_id_700=335501,site_id_701=501501,site_id_702=300,site_id_555=501,site_id_450=000,site_id_444=000,site_id_333=335,site_id_335=335,site_id_334=334,site_id_306=306,site_id_222=200,site_id_111=100,resim='".$resim."', dil='".$dil."' ");
    }
}
?>