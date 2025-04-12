<?php
$id = $gvn->rakam($_GET["id"]);

// Veritabanından hesap kontrolü
$kontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=? AND kid=?");
$kontrol->execute([$id, $hesap->id]);
if ($kontrol->rowCount() < 1) {
    header("Location: eklenen-danismanlar");
    die();
}
$snc = $kontrol->fetch(PDO::FETCH_OBJ);

?>
<div class="headerbg" <?=($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . $gayarlar->belgeler_resim . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?=dil("TX501");?></h1>
            <div class="sayfayolu">
                <span><?=dil("TX502");?></span>
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
                    <h4 class="uyepaneltitle"><?=dil("TX501");?></h4>
                    <ul class="tab">
                        <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'uyelik_bilgileri')" <?=($_GET["goto"] == "") ? 'id="defaultOpen"' : '';?>><?=dil("TX609");?></a></li>
                        <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'ilanlari')" <?=($_GET["goto"] == "ilanlari") ? 'id="defaultOpen"' : '';?>><?=dil("TX504");?></a></li>
                        <li><a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'ilanlari2')" <?=($_GET["goto"] == "ilanlari2") ? 'id="defaultOpen"' : '';?>><?=dil("TX507");?></a></li>
                    </ul>

                    <div id="uyelik_bilgileri" class="tabcontent">
                        <form action="ajax.php?p=danisman_duzenle&id=<?=$snc->id;?>" method="POST" id="DanismanEkleForm" enctype="multipart/form-data">
                            <table width="100%" border="0">
                                <tr>
                                    <td><?=dil("TX363");?></td>
                                    <td>
                                        <input type="file" name="avatar" id="avatar" style="display:none;" />
                                        <div class="uyeavatar">
                                            <a title="Foto Yükle" class="avatarguncelle" href="javascript:void(0);" onclick="$('#avatar').click();"><i class="fa fa-camera" aria-hidden="true"></i></a>
                                            <img src="<?=($snc->avatar == '') ? 'https://www.turkiyeemlaksitesi.com.tr/uploads/default-avatar.png' : 'https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/' . $snc->avatar;?>" id="avatar_image" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hesap Durumu</td>
                                    <td>
                                        <input id="durum_0" class="radio-custom" name="durum" value="0" type="radio" style="width:25px;" <?=($snc->durum == 0) ? 'checked' : '';?>>
                                        <label for="durum_0" class="radio-custom-label" style="margin-right: 28px;"><span class="checktext"><?=dil("TX491");?></span></label>
                                        <input id="durum_1" class="radio-custom" name="durum" value="1" type="radio" style="width:25px;" <?=($snc->durum == 1) ? 'checked' : '';?>>
                                        <label for="durum_1" class="radio-custom-label" style="margin-right: 28px;"><span class="checktext"><?=dil("TX490");?></span></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?=dil("TX126");?> <span style="color:red">*</span></td>
                                    <td><input name="adsoyad" type="text" value="<?=$snc->adi . " " . $snc->soyadi;?>"></td>
                                </tr>
                                <tr>
                                    <td><?=dil("TX396");?></td>
                                    <td>
                                        <div class="prufilurllink">
                                            <a target="_blank" href="profil/<?=($snc->nick_adi == '') ? $snc->id : $snc->nick_adi;?>"><?php echo SITE_URL; ?>profil/<?=($snc->nick_adi == '') ? $snc->id : $snc->nick_adi;?></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?=dil("TX127");?> <span style="color:red">*</span></td>
                                    <td><input name="email" type="text" value="<?=$snc->email;?>"></td>
                                </tr>
                                <tr>
                                    <td><?=dil("TX128");?> <?=($gayarlar->sms_aktivasyon == 1) ? '<span style="color:red">*</span>' : '';?></td>
                                    <td><input name="telefon" id="gsm" type="text" value="<?=$snc->telefon;?>"></td>
                                </tr>
                                <tr>
                                    <td><?=dil("TX390");?></td>
                                    <td><input name="sabit_telefon" type="text" id="telefon" value="<?=$snc->sabit_telefon;?>"></td>
                                </tr>
                                <tr>
                                    <td><?=dil("TX129");?></td>
                                    <td><input name="parola" type="password" placeholder="<?=dil("TX254");?>"></td>
                                </tr>
                                <tr>
                                    <td><?=dil("TX130");?></td>
                                    <td><input name="parola_tekrar" type="password" placeholder="<?=dil("TX254");?>"></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span style="margin-bottom:10px; float: left;"><strong><?=dil("TX644");?></strong></span>
                                        <div class="clear"></div>
                                        <textarea style="width:100%;" name="hakkinda" class="thetinymce" id="hakkinda" placeholder="<?=dil("TX429");?>"><?=$snc->hakkinda;?></textarea>
                                    </td>
                                </tr>
                                <tr style="font-size:13px;display:none">
                                    <td colspan="2">
                                        <h5 style="margin-bottom:7px;"><strong><?=dil("TX398");?></strong></h5>
                                        <input id="telefond_check" class="checkbox-custom" name="telefond" value="1" type="checkbox" <?=($snc->telefond == 1) ? 'checked' : '';?> style="width:100px;">
                                        <label for="telefond_check" class="checkbox-custom-label"><span class="checktext"><?=dil("TX386");?></span></label>
                                        <div class="clear" style="margin-bottom:5px;"></div>
                                        <input id="sabittelefond_check" class="checkbox-custom" name="sabittelefond" value="1" type="checkbox" <?=($snc->sabittelefond == 1) ? 'checked' : '';?> style="width:100px;">
                                        <label for="sabittelefond_check" class="checkbox-custom-label"><span class="checktext"><?=dil("TX387");?></span></label>
                                        <div class="clear" style="margin-bottom:5px;"></div>
                                        <input id="epostad_check" class="checkbox-custom" name="epostad" value="1" type="checkbox" <?=($snc->epostad == 1) ? 'checked' : '';?> style="width:100px;">
                                        <label for="epostad_check" class="checkbox-custom-label"><span class="checktext"><?=dil("TX388");?></span></label>
                                        <div class="clear" style="margin-bottom:5px;"></div>
                                        <input id="avatard_check" class="checkbox-custom" name="avatard" value="1" type="checkbox" <?=($snc->avatard == 1) ? 'checked' : '';?> style="width:100px;">
                                        <label for="avatard_check" class="checkbox-custom-label"><span class="checktext"><?=dil("TX389");?></span></label>
                                        <div class="clear" style="margin-bottom:5px;"></div>
                                        <h5 style="margin-bottom:7px;margin-top:10px;"><strong><?=dil("TX399");?></strong></h5>
                                        <input id="checkbox-6" class="checkbox-custom" name="mail_izin" value="1" type="checkbox" <?=($snc->mail_izin == 1) ? 'checked' : '';?> style="width:100px;">
                                        <label for="checkbox-6" class="checkbox-custom-label"><span class="checktext"><?=dil("TX251");?></span></label>
                                        <div class="clear" style="margin-bottom:5px;"></div>
                                        <input id="checkbox-7" class="checkbox-custom" name="sms_izin" value="1" type="checkbox" <?=($snc->sms_izin == 1) ? 'checked' : '';?> style="width:100px;">
                                        <label for="checkbox-7" class="checkbox-custom-label"><span class="checktext"><?=dil("TX252");?></span></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:none" colspan="2">
                                        <a href="javascript:;" id="ButtonSubmit" onclick="ButtonSubmit();" class="btn"><i class="fa fa-refresh" aria-hidden="true"></i> <?=dil("TX505");?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border:none" colspan="2" align="center">
                                        <div id="DanismanEkleForm_output" style="display:none"></div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <script src="<?=THEME_DIR;?>tinymce/tinymce.min.js"></script>
                        <script type="application/x-javascript">
                            tinymce.init({
                                selector: "#hakkinda",
                                height: 200,
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
                            function ButtonSubmit() {
                                var stbutton = $("#ButtonSubmit");
                                var stonc = stbutton.attr("onclick");
                                var stinn = stbutton.html();
                                stbutton.removeAttr("onclick");
                                stbutton.html('<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
                                $('#hakkinda').html(tinymce.get('hakkinda').getContent());
                                $("#DanismanEkleForm_output").fadeOut(400);
                                $("#DanismanEkleForm").ajaxForm({
                                    target: '#DanismanEkleForm_output',
                                    complete: function() {
                                        $("#DanismanEkleForm_output").fadeIn(400);
                                        stbutton.attr("onclick", stonc);
                                        stbutton.html(stinn);
                                    }
                                }).submit();
                            }
                        </script>
                    </div>

                    <div id="ilanlari" class="tabcontent">
                        <?php
                        $git = $gvn->zrakam($_GET["git"]);
                        $qry = $pagent->sql_query("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND dil='" . $dil . "' AND ekleme=1 AND tipi=4 AND (durum=1 OR durum=0) AND acid=" . $snc->id . " ORDER BY id DESC", $git, 6);
                        $query = $db->query($qry['sql']);
                        $adet = $qry['toplam'];
                        ?>
                        <!-- İlanlar burada listelenecek -->
                  

<?php
if ($adet > 0) {
?>
<div id="hidden_result" style="display:none"></div>
<table width="100%" border="0" id="uyepanelilantable">
  <tr>
    <td id="mobtd" bgcolor="#EFEFEF"><strong><?=dil("TX232");?></strong></td>
    <td bgcolor="#EFEFEF"><strong><?=dil("TX233");?></strong></td>
    <td align="center" bgcolor="#EFEFEF"><strong><?=dil("TX234");?></strong></td>
    <td width="15%" align="center" bgcolor="#EFEFEF"><strong><?=dil("TX235");?></strong></td>
  </tr>
  
<?php
while ($row = $query->fetch(PDO::FETCH_OBJ)) {
  $ilink = ($dayarlar->permalink == 'Evet') ? $row->url . '.html' : 'index.php?p=sayfa&id=' . $row->id;
  $ilan_tarih = date("d.m.Y", strtotime($row->tarih));
?>
  <tr id="row_<?=$row->id;?>">
    <td id="mobtd"><img src="https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$row->resim;?>" width="100" height="75"/></td>
    <td><a href="<?=$ilink;?>"><strong><?php echo $row->baslik;?></strong></a><br>
    <span class="ilantarih"><?=dil("TX236");?> <?=$ilan_tarih;?></span>
    <span class="ilantarih"><?php if ($row->durum == 1 OR $row->durum == 3) { ?><?=dil("TX237");?><?php echo $row->hit;?><?php } ?></span>
    </td>
    <td align="center">
  <?php
  if ($row->durum == 0) {
  ?><span style="color:red;font-weight:bold;"><?=dil("TX238");?></span><?php
  } elseif ($row->durum == 1) {
  ?><span style="color:green;font-weight:bold;"><?=dil("TX239");?></span><?php
  } elseif ($row->durum == 2) {
  ?><span style="color:green;font-weight:bold;"><?=dil("TX240");?></span><?php
  } elseif ($row->durum == 3) {
  ?><span style="color:orange;font-weight:bold;"><?=dil("TX241");?></span><?php
  }
  ?>
  </td>
    <td width="15%" align="center">
    <a title="Düzenle" class="uyeilankontrolbtn" href="uye-paneli?rd=ilan_duzenle&id=<?=$row->id;?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
    <div class="clearmob"></div>
    <a title="Sil" class="uyeilankontrolbtn" href="javascript:;" onclick="ajaxHere('ajax.php?p=ilan_sil&id=<?=$row->id;?>','hidden_result');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
    </td>
  </tr>
  <?php } ?>
  
  </table>

<div class="clear"></div>
<div class="sayfalama">
<?php echo $pagent->listele('uye-paneli?rd=danisman_duzenle&id=' . $snc->id . '&goto=ilanlari&git=', $git, $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
</div>

<?php } else { ?> 
<h4 style="text-align:center;margin-top:60px;"><?=dil("TX659");?></h4>
<br><br>
<?php } ?>

</div><!-- ilanlari end -->

<div id="ilanlari2" class="tabcontent">
<?php
$git2 = $gvn->zrakam($_GET["git2"]);
$qry = $pagent->sql_query("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND dil='" . $dil . "' AND ekleme=1 AND tipi=4 AND (durum=2 OR durum=3) AND acid=" . $snc->id . " ORDER BY id DESC", $git2, 6);
$query = $db->query($qry['sql']);
$adet = $qry['toplam'];
?>

<?php
if ($adet > 0) {
?>
<div id="hidden_result" style="display:none"></div>
<table width="100%" border="0" id="uyepanelilantable">
  <tr>
    <td id="mobtd" bgcolor="#EFEFEF"><strong><?=dil("TX232");?></strong></td>
    <td bgcolor="#EFEFEF"><strong><?=dil("TX233");?></strong></td>
    <td align="center" bgcolor="#EFEFEF"><strong><?=dil("TX234");?></strong></td>
    <td width="15%" align="center" bgcolor="#EFEFEF"><strong><?=dil("TX235");?></strong></td>
  </tr>

<?php
while ($row = $query->fetch(PDO::FETCH_OBJ)) {
  $ilink = ($dayarlar->permalink == 'Evet') ? $row->url . '.html' : 'index.php?p=sayfa&id=' . $row->id;
  $ilan_tarih = date("d.m.Y", strtotime($row->tarih));
?>
  <tr id="row_<?=$row->id;?>">
    <td id="mobtd"><img src="https://www.turkiyeemlaksitesi.com.tr/uploads/thumb/<?=$row->resim;?>" width="100" height="75"/></td>
    <td><a href="<?=$ilink;?>"><strong><?php echo $row->baslik;?></strong></a><br>
    <span class="ilantarih"><?=dil("TX236");?> <?=$ilan_tarih;?></span>
    <span class="ilantarih"><?php if ($row->durum == 1 OR $row->durum == 3) { ?><?=dil("TX237");?><?php echo $row->hit;?><?php } ?></span>
    </td>
    <td align="center">
  <?php
  if ($row->durum == 0) {
  ?><span style="color:red;font-weight:bold;"><?=dil("TX238");?></span><?php
  } elseif ($row->durum == 1) {
  ?><span style="color:green;font-weight:bold;"><?=dil("TX239");?></span><?php
  } elseif ($row->durum == 2) {
  ?><span style="color:green;font-weight:bold;"><?=dil("TX240");?></span><?php
  } elseif ($row->durum == 3) {
  ?><span style="color:orange;font-weight:bold;"><?=dil("TX241");?></span><?php
  }
  ?>
  </td>
    <td width="15%" align="center">
    <a title="Düzenle" class="uyeilankontrolbtn" href="uye-paneli?rd=ilan_duzenle&id=<?=$row->id;?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
    <div class="clearmob"></div>
    <a title="Sil" class="uyeilankontrolbtn" href="javascript:;" onclick="ajaxHere('ajax.php?p=ilan_sil&id=<?=$row->id;?>','hidden_result');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
    </td>
  </tr>
  <?php } ?>
  
  </table>

<div class="clear"></div>
<div class="sayfalama">
<?php echo $pagent->listele('uye-paneli?rd=danisman_duzenle&id=' . $snc->id . '&goto=ilanlari2&git2=', $git2, $qry['basdan'], $qry['kadar'], 'class="sayfalama-active"', $query); ?>
</div>

<?php } else { ?> 
<h4 style="text-align:center;margin-top:60px;"><?=dil("TX660");?></h4><br><br>
<?php } ?>

</div><!-- ilanlari2 end -->

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

</div>
</div>
</div>
</div>

<div class="clear"></div>
</div>
</div>				  
