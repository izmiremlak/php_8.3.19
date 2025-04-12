<!-- Meta Etiketleri -->
<?php
if (!isset($dayarlar)) {
    $dayarlar = new stdClass();
    $dayarlar->title = 'Varsayılan Başlık';
    $dayarlar->keywords = '';
    $dayarlar->description = '';
}
?>
<title><?= htmlspecialchars($dayarlar->title); ?></title>
<meta name="keywords" content="<?= htmlspecialchars($dayarlar->keywords); ?>" />
<meta name="description" content="<?= htmlspecialchars($dayarlar->description); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="robots" content="All" />
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<!-- Meta Etiketleri -->