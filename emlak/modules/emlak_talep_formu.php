<?php if (!defined("THEME_DIR")) { die(); } ?>
<!DOCTYPE html>
<html>
<head>

<!-- Meta Tags -->
<title><?=dil("TX480");?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="keywords" content="<?=htmlspecialchars($dayarlar->keywords, ENT_QUOTES, 'UTF-8');?>" />
<meta name="description" content="<?=htmlspecialchars($dayarlar->description, ENT_QUOTES, 'UTF-8');?>" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Tags -->

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include THEME_DIR . "inc/head.php"; ?>

<script>
// Tooltip oluşturma işlevi
function showTooltip(input, message) {
    // Mevcut tooltip'leri temizle
    $(input).parent().find('.error-tooltip').remove();

    // Tooltip oluştur
    const tooltip = document.createElement('div');
    tooltip.className = 'error-tooltip';
    tooltip.style.position = 'absolute';
    tooltip.style.background = '#ffeb3b'; // Daha belirgin sarı
    tooltip.style.border = '1px solid #fdd835';
    tooltip.style.padding = '16px 24px';
    tooltip.style.borderRadius = '8px';
    tooltip.style.color = '#333';
    tooltip.style.zIndex = '1000';
    tooltip.style.fontSize = '14px';
    tooltip.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
    tooltip.style.lineHeight = '1.5';
    tooltip.textContent = message;

    // Tooltip'in konumunu ayarla
    const rect = input.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    tooltip.style.top = `${rect.top + scrollTop - 60}px`; // Scroll hesaba katılarak üstte
    tooltip.style.left = `${rect.left + window.scrollX}px`;

    // Ok (üçgen) ekle
    const arrow = document.createElement('div');
    arrow.style.position = 'absolute';
    arrow.style.bottom = '-10px';
    arrow.style.left = '50%';
    arrow.style.transform = 'translateX(-50%)';
    arrow.style.width = '0';
    arrow.style.height = '0';
    arrow.style.borderLeft = '10px solid transparent';
    arrow.style.borderRight = '10px solid transparent';
    arrow.style.borderTop = '10px solid #ffeb3b'; // Sarı ok
    tooltip.appendChild(arrow);

    // Tooltip'i body'ye ekle
    document.body.appendChild(tooltip);

    // 3 saniye sonra kaldır
    setTimeout(() => tooltip.remove(), 3000);
}

// AJAX ile dropdown doldurma
function ajaxHere(url, targetId) {
    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            $('#' + targetId).html(response);
        },
        error: function() {
            alert('Bir hata oluştu. Lütfen tekrar deneyiniz.');
        }
    });
}

// Form doğrulama
function validateForm() {
    let isValid = true;

    const fields = [
        { id: 'adsoyad', type: 'text' },
        { id: 'telefon', type: 'text' },
        { id: 'email', type: 'text' },
        { id: 'emlak_tipi', type: 'select' },
        { id: 'ulke_id', type: 'select', optional: true },
        { id: 'il', type: 'select' },
        { id: 'ilce', type: 'select' },
        { id: 'mahalle', type: 'select' },
        { id: 'talep', type: 'select' },
        { id: 'mesaj', type: 'text' },
        { id: 'g-recaptcha-response', type: 'recaptcha' }
    ];

    for (let field of fields) {
        const element = document.getElementById(field.id);
        if (!element && !field.optional) continue;

        if (field.type === 'recaptcha') {
            const recaptcha = grecaptcha.getResponse();
            if (!recaptcha) {
                showTooltip(document.querySelector('.g-recaptcha'), 'Lütfen reCAPTCHA\'yı tamamlayın');
                isValid = false;
                break;
            }
        } else if (field.type === 'text' || field.type === 'textarea') {
            if (!element.value.trim()) {
                showTooltip(element, 'Bu alanı doldurun');
                isValid = false;
                break;
            }
        } else if (field.type === 'select' && !field.optional) {
            if (!element.value.trim()) {
                showTooltip(element, 'Bu listeden bir seçim yapın');
                isValid = false;
                break;
            }
        }
    }

    if (isValid) {
        AjaxFormS('EmlakTalepForm', 'EmlakTalepForm_output');
    }
}

// İlk yüklemede İl listesini doldur
document.addEventListener("DOMContentLoaded", function() {
    <?php if ($ulkelerc < 2 && isset($ulke)) { ?>
        ajaxHere('ajax.php?p=il_getir&ulke_id=<?= (int)$ulke->id; ?>', 'il');
    <?php } ?>
});
</script>

<style>
/* Tooltip için stil */
.error-tooltip {
    animation: fadein 0.5s;
    position: absolute;
}
@keyframes fadein {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

</head>
<body>

<?php include THEME_DIR . "inc/header.php"; ?>

<div class="headerbg" style="background-image: url(uploads/e115b36791.jpg);">
<div id="wrapper">
<div class="headtitle">
<h1><?=dil("TX480");?></h1>
<div class="sayfayolu">
<a href="index.html"><?=dil("TX136");?></a> / 
<span><?=dil("TX480");?></span>
</div>
</div>
</div>
<div class="headerwhite"></div>
</div>

<div id="wrapper">

<div class="content" id="bigcontent">

<?php include THEME_DIR . "inc/sosyal_butonlar.php"; ?>

<div class="altbaslik">
<h4><strong><?=dil("TX480");?></strong></h4>
</div>

<div class="clear"></div>

<div class="sayfadetay">

<div class="emlaktalepformu">
<form action="ajax.php?p=emlak_talep_formu" method="POST" id="EmlakTalepForm">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="40%"><?=dil("TX126");?></td>
    <td><input name="adsoyad" id="adsoyad" type="text" required></td>
  </tr>
  <tr>
    <td><?=dil("TX128");?></td>
    <td><input name="telefon" id="telefon" type="text" required></td>
  </tr>
  <tr>
    <td><?=dil("TX127");?></td>
    <td><input name="email" id="email" type="email" required></td>
  </tr>
  
  <?php
  $emlak_tipi = dil("EMLKTLP1");
  if ($emlak_tipi != '') {
  ?>
  <tr>
    <td><?=dil("TX54");?></td>
    <td>
    <select name="emlak_tipi" id="emlak_tipi" required>
	<?=$emlak_tipi;?>
	</select>
    </td>
  </tr>
  <?php } ?>
  
  <?php
  $ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
  $ulkelerc = $ulkeler->rowCount();
  if ($ulkelerc > 1) {
  ?>
  <tr>
    <td><?=dil("TX348");?></td>
    <td>
    <select name="ulke_id" id="ulke_id" onchange="ajaxHere('ajax.php?p=il_getir&ulke_id='+this.value,'il');">
        <option value=""><?=dil("TX348");?></option>
        <?php
		while ($row = $ulkeler->fetch(PDO::FETCH_OBJ)) {
		?><option value="<?=$row->id;?>"><?=htmlspecialchars($row->ulke_adi, ENT_QUOTES, 'UTF-8');?></option><?php
		}
		?>
    </select>
    </td>
  </tr>
  <?php } ?>
  
  <tr>
    <td><?=dil("TX55");?></td>
    <td>
    <select name="il" id="il" onchange="ajaxHere('ajax.php?p=ilce_getir&il_id='+this.value,'ilce');" required>
        <option value=""><?=dil("TX55");?></option>
        <?php
		if ($ulkelerc < 2) {
		$ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
		$sql = $db->query("SELECT id,il_adi FROM il WHERE ulke_id=" . (int)$ulke->id . " ORDER BY id ASC");
		} else {
		$sql = NULL;
		}
		if ($sql != NULL) {
		while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
		?><option value="<?=$row->id;?>"><?=htmlspecialchars($row->il_adi, ENT_QUOTES, 'UTF-8');?></option><?php
		}
		}
		?>
    </select>
    </td>
  </tr>
  
  <tr>
    <td><?=dil("TX56");?></td>
    <td>
    <select name="ilce" id="ilce" onchange="ajaxHere('ajax.php?p=mahalle_getir&ilce_id='+this.value,'mahalle');" required>
        <option value=""><?=dil("TX56");?></option>
    </select>
    </td>
  </tr>
  <tr>
    <td><?=dil("TX266");?></td>
    <td>
    <select name="mahalle" id="mahalle" required>
        <option value=""><?=dil("TX266");?></option>
    </select>
    </td>
  </tr>
  
  <?php
  $talepler = dil("EMLKTLP2");
  if ($talepler != '') {
  ?>
  <tr>
    <td><?=dil("TX481");?></td>
    <td>
    <select name="talep" id="talep" required>
	<?=$talepler;?>
	</select>
    </td>
  </tr>
  <?php } ?>
  
  <tr>
    <td><?=dil("TX482");?></td>
    <td><textarea name="mesaj" id="mesaj" required></textarea></td>
  </tr>
  
  <!-- reCAPTCHA V2 -->
  <tr>
    <td><?=dil("TX_RECAPTCHA");?></td>
    <td>
      <div class="g-recaptcha" id="g-recaptcha-response" data-sitekey="6LeOm_8qAAAAAK2kYZDabxefz7VJO6Wq_ppVTDZg"></div>
    </td>
  </tr>
  
  <tr>
    <td> </td>
    <td><a class="gonderbtn" onclick="validateForm();" href="javascript:void(0);"><?=dil("TX483");?></a></td>
  </tr>
  
  <tr>
    <td colspan="2"><div id="EmlakTalepForm_output" style="display:none"></div></td>
  </tr>
</table>
</form>
<div id="EmlakTalepForm_SUCCESS" style="display:none"><?=dil("TX645");?></div>
</div>

</div>
</div>

<div class="clear"></div>

<?php include THEME_DIR . "inc/ilanvertanitim.php"; ?>
</div>

<?php include THEME_DIR . "inc/footer.php"; ?>

</body>
</html>