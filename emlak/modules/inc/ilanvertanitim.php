<?php
// Blok 3 aktif mi kontrol ediyoruz
if($dayarlar->blok3 == 1){ ?>
    <div class="ilanvertanitim">
        <div class="ilanbigbtn fadeup">
            <h1><?= htmlspecialchars(dil("TX99"), ENT_QUOTES, 'UTF-8'); ?></h1>
            <a class="gonderbtn" href="emlak-talep-formu"><?= htmlspecialchars(dil("TX100"), ENT_QUOTES, 'UTF-8'); ?></a>
            <div class="clearmob"></div>
        </div>
    </div>
<?php } ?>