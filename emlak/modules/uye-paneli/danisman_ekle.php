<?php
// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hata loglama
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

if ($hesap->turu != 1) {
    header("Location:uye-paneli");
    die();
}

$danisman_limit = $hesap->danisman_limit;
$paketi = $db->query("SELECT * FROM upaketler_501 WHERE acid=" . (int)$hesap->id . " AND durum=1 AND btarih>NOW()");
if ($paketi->rowCount() > 0) {
    $paketi = $paketi->fetch(PDO::FETCH_OBJ);
    $danisman_limit += ($paketi->danisman_limit == 0) ? 9999 : $paketi->danisman_limit;
    $danisman_limit -= $db->query("SELECT id FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND kid=" . (int)$paketi->acid . " AND pid=" . (int)$paketi->id)->rowCount();
}
?>
<div class="headerbg" <?= ($gayarlar->belgeler_resim != '') ? 'style="background-image: url(uploads/' . htmlspecialchars($gayarlar->belgeler_resim, ENT_QUOTES, 'UTF-8') . ');"' : ''; ?>>
    <div id="wrapper">
        <div class="headtitle">
            <h1><?= htmlspecialchars(dil("TX494"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="sayfayolu">
                <span><?= htmlspecialchars(dil("TX495"), ENT_QUOTES, 'UTF-8'); ?></span>
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
    <h4 class="uyepaneltitle"><?= htmlspecialchars(dil("TX494"), ENT_QUOTES, 'UTF-8'); ?></h4>

    <div class="alert-info"><?= htmlspecialchars(dil("TX656"), ENT_QUOTES, 'UTF-8'); ?></div>

    <?php if ($danisman_limit < 1) { ?>
    <?= htmlspecialchars(dil("TX662"), ENT_QUOTES, 'UTF-8'); ?>
    <?php } else { ?>

    <form action="ajax.php?p=danisman_ekle" method="POST" id="DanismanEkleForm" enctype="multipart/form-data">
    <table width="100%" border="0">

    <tr>
        <td><?= htmlspecialchars(dil("TX363"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td>
            <input type="file" name="avatar" id="avatar" style="display:none;" />
            <div class="uyeavatar">
                <a title="Foto Yükle" class="avatarguncelle" href="javascript:void(0);" onclick="document.getElementById('avatar').click();"><i class="fa fa-camera" aria-hidden="true"></i></a>
                <img src="https://www.turkiyeemlaksitesi.com.tr/uploads/default-avatar.png" id="avatar_image" /><br>
                <span style="font-size: 13px; color: #777;"><?= htmlspecialchars(dil("TX654"), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX126"), ENT_QUOTES, 'UTF-8'); ?> <span style="color:red">*</span></td>
        <td><input name="adsoyad" type="text"></td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX127"), ENT_QUOTES, 'UTF-8'); ?> <span style="color:red">*</span></td>
        <td><input name="email" type="text"></td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX128"), ENT_QUOTES, 'UTF-8'); ?> <?= ($gayarlar->sms_aktivasyon == 1) ? '<span style="color:red">*</span>' : ''; ?></td>
        <td><input name="telefon" id="gsm" type="text"></td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX390"), ENT_QUOTES, 'UTF-8'); ?></td>
        <td><input name="sabit_telefon" type="text" id="telefon" value="<?= htmlspecialchars($hesap->sabit_telefon, ENT_QUOTES, 'UTF-8'); ?>"></td>
    </tr>

    <tr>
        <td><?= htmlspecialchars(dil("TX129"), ENT_QUOTES, 'UTF-8'); ?> <span style="color:red">*</span></td>
        <td><input name="parola" type="password"></td>
    </tr>
    <tr>
        <td><?= htmlspecialchars(dil("TX130"), ENT_QUOTES, 'UTF-8'); ?> <span style="color:red">*</span></td>
        <td><input name="parola_tekrar" type="password"></td>
    </tr>

    <tr>
        <td colspan="2">
            <span style="margin-bottom:10px; float: left;"><strong><?= htmlspecialchars(dil("TX644"), ENT_QUOTES, 'UTF-8'); ?></strong></span><div class="clear"></div>
            <textarea style="width:100%;" name="hakkinda" class="thetinymce" id="hakkinda" placeholder="<?= htmlspecialchars(dil("TX429"), ENT_QUOTES, 'UTF-8'); ?>"></textarea>
        </td>
    </tr>

    <tr style="font-size:13px;display:none">
        <td colspan="2">
            <h5 style="margin-bottom:7px;"><strong><?= htmlspecialchars(dil("TX398"), ENT_QUOTES, 'UTF-8'); ?></strong></h5>
            <input id="telefond_check" class="checkbox-custom" name="telefond" value="1" type="checkbox" style="width:100px;">
            <label for="telefond_check" class="checkbox-custom-label"><span class="checktext"><?= htmlspecialchars(dil("TX386"), ENT_QUOTES, 'UTF-8'); ?></span></label>
            <div class="clear" style="margin-bottom:5px;"></div>
            
            <input id="sabittelefond_check" class="checkbox-custom" name="sabittelefond" value="1" type="checkbox" style="width:100px;">
            <label for="sabittelefond_check" class="checkbox-custom-label"><span class="checktext"><?= htmlspecialchars(dil("TX387"), ENT_QUOTES, 'UTF-8'); ?></span></label>
            <div class="clear" style="margin-bottom:5px;"></div>
            
            <input id="epostad_check" class="checkbox-custom" name="epostad" value="1" type="checkbox" style="width:100px;">
            <label for="epostad_check" class="checkbox-custom-label"><span class="checktext"><?= htmlspecialchars(dil("TX388"), ENT_QUOTES, 'UTF-8'); ?></span></label>
            <div class="clear" style="margin-bottom:5px;"></div>
            
            <input id="avatard_check" class="checkbox-custom" name="avatard" value="1" type="checkbox" style="width:100px;">
            <label for="avatard_check" class="checkbox-custom-label"><span class="checktext"><?= htmlspecialchars(dil("TX389"), ENT_QUOTES, 'UTF-8'); ?></span></label>
            <div class="clear" style="margin-bottom:5px;"></div>
            
            <h5 style="margin-bottom:7px;margin-top:10px;"><strong><?= htmlspecialchars(dil("TX399"), ENT_QUOTES, 'UTF-8'); ?></h5>
            <input checked id="checkbox-6" class="checkbox-custom" name="mail_izin" value="1" type="checkbox" style="width:100px;">
            <label for="checkbox-6" class="checkbox-custom-label"><span class="checktext"><?= htmlspecialchars(dil("TX251"), ENT_QUOTES, 'UTF-8'); ?></span></label>
            <div class="clear" style="margin-bottom:5px;"></div>
            <input checked id="checkbox-7" class="checkbox-custom" name="sms_izin" value="1" type="checkbox" style="width:100px;">
            <label for="checkbox-7" class="checkbox-custom-label"><span class="checktext"><?= htmlspecialchars(dil("TX252"), ENT_QUOTES, 'UTF-8'); ?></span></label>
        </td>
    </tr>

    <tr>
        <td style="border:none" colspan="2">
            <a href="javascript:;" id="ButtonSubmit" onclick="ButtonSubmit();" class="btn"><i class="fa fa-refresh" aria-hidden="true"></i> <?= htmlspecialchars(dil("TX505"), ENT_QUOTES, 'UTF-8'); ?></a>
        </td>
    </tr>

    <tr>
        <td style="border:none" colspan="2" align="center"><div id="DanismanEkleForm_output" style="display:none"></div></td>
    </tr>

    </table>
    </form>
    <script src="<?= THEME_DIR; ?>tinymce/tinymce.min.js"></script>
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
        var stbutton = document.getElementById("ButtonSubmit");
        var stonc = stbutton.getAttribute("onclick");
        var stinn = stbutton.innerHTML;
        stbutton.removeAttribute("onclick");
        stbutton.innerHTML = '<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
        document.getElementById('hakkinda').innerHTML = tinymce.get('hakkinda').getContent();
        document.getElementById("DanismanEkleForm_output").style.display = 'none';
        $("#DanismanEkleForm").ajaxForm({
            target: '#DanismanEkleForm_output',
            complete: function () {
                document.getElementById("DanismanEkleForm_output").style.display = 'block';
                stbutton.setAttribute("onclick", stonc);
                stbutton.innerHTML = stinn;
            }
        }).submit();
    }
    </script>
    <?php } ?>

</div>

<div id="TamamDiv" style="display:none">

<!-- TAMAM MESAJ -->
<div style="margin-bottom:70px;text-align:center;" id="BasvrTamam">
    <i style="font-size:80px;color:green;" class="fa fa-check"></i>
    <h2 style="color:green;font-weight:bold;"><?= htmlspecialchars(dil("TX497"), ENT_QUOTES, 'UTF-8'); ?></h2>
    <!--br/>
    <h4>---</h4-->
</div>
<!-- TAMAM MESAJ -->

</div>

</div>
</div>
</div>

<div class="clear"></div>
</div>
</div>