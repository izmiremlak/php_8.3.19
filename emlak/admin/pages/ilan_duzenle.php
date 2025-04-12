<?php
// Sıkı tip kontrolü ve hata raporlaması ayarları
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

// Oturum başlatma
session_start();

// Veritabanı bağlantısı (PDO kullanarak)
$dsn = 'mysql:host=localhost;dbname=emlak_sitesi';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Hata modu ayarı
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Varsayılan fetch modu
];

try {
    $db = new PDO($dsn, $username, $password, $options); // PDO nesnesi oluşturma
} catch (PDOException $e) {
    error_log($e->getMessage()); // Hata mesajını log dosyasına yazma
    die('Veritabanı bağlantısı başarısız.'); // Hata durumunda scripti durdurma
}

// Kullanıcı girdisini sanitize etme fonksiyonu
function sanitizeInput(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); // Girdiyi HTML özel karakterlerinden temizleme
}

// GET parametresini güvenli şekilde al
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id) {
    header("Location:index.php?p=ilanlar");
    exit;
}

try {
    $snc = $db->prepare("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=:ids");
    $snc->execute(['ids' => $id]);
    if ($snc->rowCount() > 0) {
        $snc = $snc->fetch(PDO::FETCH_OBJ);
    } else {
        header("Location:index.php?p=ilanlar");
        exit;
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo "<div class='error'>Bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.</div>";
    exit;
}

// İlgili ilan bilgilerini çekme ve ayarlama
$multi = $db->query("SELECT id, ilan_no FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . $snc->ilan_no . " ORDER BY id ASC");
$multict = $multi->rowCount();
$multif = $multi->fetch(PDO::FETCH_OBJ);
$multidids = $db->query("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no=" . $snc->ilan_no)->fetch(PDO::FETCH_OBJ)->ids;

$dilx = $db->query("SELECT * FROM diller_501 WHERE kisa_adi='" . $snc->dil . "'")->fetch(PDO::FETCH_OBJ);

$fiyat_int = $gvn->para_int($snc->fiyat);
$fiyat = $gvn->para_str($fiyat_int);

$aidat_int = $gvn->para_int($snc->aidat);
$aidat = $gvn->para_str($aidat_int);

$ekleyen = $db->query("SELECT id, adi, soyadi, tipi, unvan FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $snc->acid)->fetch(PDO::FETCH_OBJ);
$goto = sanitizeInput($_GET["goto"]);

$ekleyen_kim = ($ekleyen->unvan != '') ? $ekleyen->unvan : $ekleyen->adi . " " . $ekleyen->soyadi;

?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">İlan Düzenle</h4>
            </div>
        </div>

        <div class="panel-group" id="accordion-test-2">
            <div class="panel panel-pink panel-color">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseOne-2" aria-expanded="true" class="collapsed">
                            İLAN PAYLAŞIMI ve PORTALLARDA İLAN DÜZENLEME AÇIKLAMASI
                        </a>
                    </h4>
                </div>
                <div id="collapseOne-2" class="panel-collapse collapse" aria-expanded="true">
                    <div class="panel-body">
                        <font color="red"><strong>İLAN PAYLAŞIMI GÜNCELLEMESİ</strong></font><br />
                        İlanınızın Paylaşma Seçeneklerini Buradan Değiştirebilirsiniz. İlanınızı Paylaşımdan Kaldırmak için, İlgili Kutucuğa <font color="red"><strong> Sıfır ( 0 ) Yazınız</strong></font>.<br /><br />
                        <font color="red"><strong>PORTALLARIMIZDA YAYINLAMA</strong></font><br />
                        İlanınızı, Emlak Portallarımızda Yayınlanmasını İstiyorsanız, İlgili Portalın / Portalların Kutucuğunu <font color="red"><strong>YENİDEN İŞARETLEMENİZ GEREKİR. </strong></font><br />
                        İlanınız, İşaretlemediğiniz Portalımızda / Portallarımızda Yayınlanmaz.
                    </div>
                </div>
            </div>
        </div>

        <style>
            .form-group .ilanozellik span {color: #555;}
            .form-group .ilanozellik span label input {float: left; margin-right: 5px;}
        </style>

        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs tabs">
                    <li class="<?= ($goto == '') ? "active " : ''; ?>tab">
                        <a href="#tab1" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">İlan Detayları</span></a>
                    </li>

                    <?php if ($multict > 0 && $snc->id == $multif->id) { ?>

                    <li class="<?= ($goto == 'photos') ? "active " : ''; ?>tab">
                        <a href="#tab2" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Foto Galeri</span></a>
                    </li>

                    <li class="<?= ($goto == 'video') ? "active " : ''; ?>tab">
                        <a href="#tab3" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Video</span></a>
                    </li>

                    <?php if ($gayarlar->dopingler_501 == 1) { ?>
                    <li class="<?= ($goto == 'doping') ? "active " : ''; ?>tab">
                        <a href="#tab4" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Dopingler</span></a>
                    </li>
                    <?php } ?>
                    <?php } // eğer ana ilan ise ?>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane<?= ($goto == '') ? " active " : ''; ?>" id="tab1">
                        <div class="row">
                            <!-- Col 1 -->
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div id="form_status"></div>
                                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=ilan_duzenle&id=<?= $snc->id; ?>" onsubmit="return false;" enctype="multipart/form-data">
                                            <?php
                                            $sql = $db->query("SELECT * FROM diller_501 WHERE kisa_adi!='" . $dilx->kisa_adi . "' ORDER BY sira ASC");
                                            $sqlct = $sql->rowCount();
                                            if ($multict > 0 && $snc->id == $multif->id) {
                                            ?>
                                            <div style="clear:both"></div>
                                            <ul class="nav nav-tabs tabs">
                                                <li class="active tab">
                                                    <a href="#etab1" data-toggle="tab" aria-expanded="false">
                                                        <span class="hidden-xs"><?= $dilx->gosterim_adi; ?></span></a>
                                                </li>
                                                <?php
                                                $dilop = [];
                                                $i = 1;
                                                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                                                    $i++;
                                                    $dilop[$i] = $row;
                                                ?>
                                                <li class="tab">
                                                    <a href="#etab<?= $i; ?>" data-toggle="tab" aria-expanded="false">
                                                        <span class="hidden-xs"><?= $row->gosterim_adi; ?></span></a>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                            <div class="tab-content">
                                                <?php
                                                for ($o = 2; $o <= $i; $o++) {
                                                    if (isset($dilop[$o])) {
                                                        $op = $dilop[$o];

                                                        $esnc = $db->query("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND ilan_no='" . $snc->ilan_no . "' AND dil='" . $op->kisa_adi . "'");
                                                        if ($esnc->rowCount() > 0) {
                                                            $esnc = $esnc->fetch(PDO::FETCH_OBJ);
                                                        }
                                                ?>
                                                <div class="tab-pane" id="etab<?= $o; ?>">
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Başlık</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" name="tabs[<?= $op->kisa_adi; ?>][baslik]" value="<?= $esnc->baslik; ?>" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-1 control-label">Açıklamalar</label>
                                                        <div class="col-sm-11">
                                                            <textarea class="summernote form-control" rows="9" name="tabs[<?= $op->kisa_adi; ?>][icerik]"><?= $esnc->icerik; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <?= $fonk->bilgi("Google'da rekabeti düşük kelimelerde organik olarak ilk sayfaya yükselmek için mutlaka aşağıdaki bilgileri doldurunuz. Sadece sayfa içeriği ile alakalı bilgiler girmelisiniz. Aksi halde spam cezası alabilirsiniz."); ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">SEO Başlık (Title)</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" name="tabs[<?= $op->kisa_adi; ?>][title]" value="<?= $esnc->title; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">SEO Kelimeler (Keywords)</label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control" name="tabs[<?= $op->kisa_adi; ?>][keywords]" value="<?= $esnc->keywords; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">SEO Açıklama (Description)</label>
                                                        <div class="col-sm-9">
                                                            <textarea class="form-control" rows="5" name="tabs[<?= $op->kisa_adi; ?>][description]"><?= $esnc->description; ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?php } ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
												
							
<div class="tab-pane active" id="etab1"><!-- tab1 start -->

    <div class="form-group">
        <label class="col-sm-3 control-label">Ekleyen</label>
        <div class="col-sm-9">
            <span style="display:block; margin-top:7px;">
                <a href="index.php?p=uye_duzenle&id=<?= $ekleyen->id; ?>" target="_blank"><?= $ekleyen_kim; ?></a>
            </span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Durum:</label>
        <div class="col-sm-9">
            <div class="radio radio-info radio-inline">
                <input type="radio" id="durum1" value="1" name="durum" <?= ($snc->durum == 1) ? 'checked' : ''; ?>>
                <label for="durum1">Onaylandı</label>
            </div>

            <div class="radio radio-info radio-inline">
                <input type="radio" id="durum3" value="3" name="durum" <?= ($snc->durum == 3) ? 'checked' : ''; ?>>
                <label for="durum3">Pasif</label>
            </div>

            <div class="radio radio-info radio-inline">
                <input type="radio" id="durum2" value="2" name="durum" <?= ($snc->durum == 2) ? 'checked' : ''; ?>>
                <label for="durum2">Reddedildi</label>
            </div>
            <?php if ($snc->durum == 4) { ?>
            <div class="radio radio-info radio-inline">
                <input type="radio" id="durum4" value="4" name="durum" <?= ($snc->durum == 4) ? 'checked' : ''; ?>>
                <label for="durum4">Silindi</label>
            </div>
            <?php } ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Yayın Oluşturma Tarihi</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="otarih" value="<?= ($snc->tarih == '' || $snc->tarih == '0000-00-00 00:00:00') ? '' : date("d.m.Y H:i", strtotime($snc->tarih)); ?>" placeholder="">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Yayın Bitiş Tarihi</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="btarih" value="<?= ($snc->btarih == '' || $snc->btarih == '0000-00-00 00:00:00') ? '' : date("d.m.Y", strtotime($snc->btarih)); ?>" placeholder="">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Danışman</label>
        <div class="col-sm-9">
            <select class="form-control" name="danisman_id">
                <option value="0">Yok</option>
                <?php
                $sql = $db->query("SELECT id,concat_ws(' ',adi,soyadi) AS adsoyad FROM hesaplar WHERE site_id_555=501 AND tipi=0 AND turu=2 ORDER BY id DESC");
                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                    ?><option value="<?= $row->id; ?>" <?= ($row->id == $snc->danisman_id) ? 'selected' : ''; ?>><?= $row->adsoyad; ?></option><?php
                }

                $sql = $db->query("SELECT * FROM danismanlar_501 ORDER BY id ASC");
                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                    ?><option value="<?= $row->id; ?>" <?= ($row->id == $snc->danisman_id) ? 'selected' : ''; ?>><?= $row->adsoyad; ?></option><?php
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Başlık <span style="color:red">*</span></label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="baslik" value="<?= $snc->baslik; ?>" placeholder="">
        </div>
    </div>
	

<div class="form-group">
    <label class="col-sm-3 control-label">Fiyatı <span style="color:red">*</span></label>
    <div class="col-sm-9">
        <input data-mask="#.##0" data-mask-reverse="true" data-mask-maxlength="false" type="text" class="form-control" name="fiyat" value="<?= $fiyat; ?>" placeholder="" style="width:200px;float:left;margin-right:5px;">
        <select class="form-control" name="pbirim" style="width:100px">
            <script src="../modules/js/zjquery.mask.js" defer></script>
            <script src="../modules/js/zinputmask.js" defer></script>
            <?php
            $pbirimler = explode(",", $fonk->get_lang($snc->dil, "PARA_BIRIMI"));
            foreach ($pbirimler as $birim) {
                ?><option <?= ($snc->pbirim == $birim) ? 'selected' : ''; ?>><?= $birim; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">İlan No</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" disabled value="<?= $snc->ilan_no; ?>" placeholder="">
    </div>
</div>

<?php
$emlkdrm = $fonk->get_lang($snc->dil, "EMLK_DRM");
if ($emlkdrm != '') {
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Emlak Durumu <span style="color:red">*</span></label>
    <div class="col-sm-9">
        <select name="emlak_durum" class="form-control">
            <?php
            $parc = explode("<+>", $emlkdrm);
            foreach ($parc as $val) {
                ?><option <?= ($snc->emlak_durum == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>


<?php
$emlktp = $fonk->get_lang($snc->dil, "EMLK_TIPI");
if ($emlktp != '') {
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Emlak Tipi <span style="color:red">*</span></label>
    <div class="col-sm-9">
        <select name="emlak_tipi" class="form-control" onchange="konut_getir(this.options[this.selectedIndex].value);">
            <?php
            $parc = explode("<+>", $emlktp);
            foreach ($parc as $val) {
                ?><option <?= ($snc->emlak_tipi == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>
		
		
<?php
$kntsekli = $fonk->get_lang($snc->dil, "KNT_SEKLI");
if ($kntsekli != '') {
?>
<div class="form-group" id="konut_sekli_con">
    <label class="col-sm-3 control-label">Konut Şekli</label>
    <div class="col-sm-9">
        <select name="konut_sekli" class="form-control">
            <option value="">Konut Şekli</option>
            <?php
            $parc = explode("<+>", $kntsekli);
            foreach ($parc as $val) {
                ?><option <?= ($snc->konut_sekli == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php
if ($snc->emlak_tipi == $isyeri) {
    $knttipi = $fonk->get_lang($snc->dil, "KNT_TIPI2");
} else {
    $knttipi = $fonk->get_lang($snc->dil, "KNT_TIPI");
}
if ($knttipi != '') {
?>
<div class="form-group" id="konut_tipi_con">
    <label class="col-sm-3 control-label">Konut Tipi</label>
    <div class="col-sm-9">
        <select name="konut_tipi" class="form-control">
            <option value="">Konut Tipi</option>
            <?php
            $parc = explode("<+>", $knttipi);
            foreach ($parc as $val) {
                ?><option <?= ($snc->konut_tipi == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php
$ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
$ulkelerc = $ulkeler->rowCount();
if ($ulkelerc > 1) {
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Ülke <span style="color:red">*</span></label>
    <div class="col-sm-9">
        <select id="ulke_id" name="ulke_id" class="form-control" onchange="ajaxHere('ajax.php?p=il_getir&ulke_id='+this.options[this.selectedIndex].value,'il');yazdir();">
            <option value="">Seçiniz</option>
            <?php
            while ($row = $ulkeler->fetch(PDO::FETCH_OBJ)) {
                ?><option value="<?= $row->id; ?>" <?= ($snc->ulke_id == $row->id) ? 'selected' : ''; ?>><?= $row->ulke_adi; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group">
    <label class="col-sm-3 control-label">İl <span style="color:red">*</span></label>
    <div class="col-sm-9">
        <select name="il" id="il" class="form-control" onchange="yazdir();ajaxHere('ajax.php?p=ilce_getir&il_id='+this.options[this.selectedIndex].value,'ilce');">
            <option value="">Seçiniz</option>
            <?php
            if ($ulkelerc < 2) {
                $ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
                $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id=" . $ulke->id . " ORDER BY id ASC");
            } else {
                $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id=" . $snc->ulke_id . " ORDER BY id ASC");
            }
            while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                if ($row->id == $snc->il_id) {
                    $il_adi = $row->il_adi;
                }
                ?><option value="<?= $row->id; ?>" <?= ($row->id == $snc->il_id) ? 'selected' : ''; ?>><?= $row->il_adi; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">İlçe </label>
    <div class="col-sm-9">
        <select onchange="yazdir();ajaxHere('ajax.php?p=mahalle_getir&ilce_id='+this.options[this.selectedIndex].value,'semt');" name="ilce" id="ilce" class="form-control">
            <option value="">Seçiniz</option>
            <option value="0">Yok</option>
            <?php
            if ($snc->il_id != '') {
                $sql = $db->query("SELECT id, ilce_adi FROM ilce WHERE il_id=" . $snc->il_id . " ORDER BY id ASC");
                while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                    if ($row->id == $snc->ilce_id) {
                        $ilce_adi = $row->ilce_adi;
                    }
                    ?><option value="<?= $row->id; ?>" <?= ($row->id == $snc->ilce_id) ? 'selected' : ''; ?>><?= $row->ilce_adi; ?></option><?php
                }
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Mahalle</label>
    <div class="col-sm-9">
        <select onchange="yazdir();" name="mahalle" id="semt" class="form-control">
            <option value="">Seçiniz</option>
            <option value="0">Yok</option>
            <?php
            if ($snc->mahalle_id != 0 || $snc->ilce != 0) {
                if ($snc->ilce == 0 && $snc->mahalle_id != 0) {
                    $ilcene = $db->query("SELECT ilce_id, id FROM mahalle_koy WHERE id=" . $snc->mahalle_id);
                    if ($ilcene->rowCount() > 0) {
                        $ilcene = $ilcene->fetch(PDO::FETCH_OBJ)->ilce_id;
                    }
                } elseif ($snc->ilce != 0) {
                    $ilcene = $snc->ilce;
                } else {
                    $ilcene = 0;
                }

                if ($ilcene != 0) {
                    $semtler = $db->query("SELECT * FROM semt WHERE ilce_id=" . $ilcene);
                    if ($semtler->rowCount() > 0) {
                        while ($srow = $semtler->fetch(PDO::FETCH_OBJ)) {
                            $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE semt_id=" . $srow->id . " AND ilce_id=" . $ilcene . " ORDER BY mahalle_adi ASC");
                            if ($mahalleler->rowCount() > 0) {
                                ?><optgroup label="<?= $srow->semt_adi; ?>"><?php
                                while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                                    if ($snc->mahalle_id == $row->id) {
                                        $mahalle_adi = $row->mahalle_adi;
                                    }
                                    ?><option value="<?= $row->id; ?>" <?= ($snc->mahalle_id == $row->id) ? 'selected' : ''; ?>><?= $row->mahalle_adi; ?></option><?php
                                }
                                ?></optgroup><?php
                            }
                        }
                    } else {
                        $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE ilce_id=" . $ilcene . " ORDER BY mahalle_adi ASC");
                        while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                            if ($snc->mahalle_id == $row->id) {
                                $mahalle_adi = $row->mahalle_adi;
                            }
                            ?><option value="<?= $row->id; ?>" <?= ($snc->mahalle_id == $row->id) ? 'selected' : ''; ?>><?= $row->mahalle_adi; ?></option><?php
                        }
                    }
                }
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="metrekare" class="col-sm-3 control-label">Net Metrekare <span style="color:red">*</span></label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="metrekare" name="metrekare" value="<?= $snc->metrekare; ?>">
    </div>
</div>

<div class="form-group">
    <label for="brut_metrekare" class="col-sm-3 control-label">Brüt Metrekare</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="brut_metrekare" name="brut_metrekare" value="<?= $snc->brut_metrekare; ?>">
    </div>
</div>

<?php
$bulundkat = $fonk->get_lang($snc->dil, "BULND_KAT");
if ($bulundkat != '') {
?>
<div class="form-group" id="bulundugu_kat_con">
    <label class="col-sm-3 control-label">Bulunduğu Kat</label>
    <div class="col-sm-9">
        <select name="bulundugu_kat" class="form-control">
            <option value="">Bulunduğu Kat</option>
            <?php
            $parc = explode("<+>", $bulundkat);
            foreach ($parc as $val) {
                ?><option <?= ($snc->bulundugu_kat == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>


<?php
// Fonksiyonlar ve güvenlik kontrolleri
$ypidrm = $fonk->get_lang($snc->dil, "YAPI_DURUM");
if ($ypidrm != '') {
?>
<div class="form-group" id="yapi_durum_con">
    <label class="col-sm-3 control-label">Yapının Durumu</label>
    <div class="col-sm-9">
        <select name="yapi_durum" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $ypidrm);
            foreach ($parc as $val) {
                ?><option <?= ($snc->yapi_durum == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?> 


<?php
$odasayisiy = $fonk->get_lang($snc->dil, "ODA_SAYISI");
if ($odasayisiy != '') {
?>
<div class="form-group" id="oda_sayisi_con">
    <label class="col-sm-3 control-label">Oda Sayısı</label>
    <div class="col-sm-9">
        <select name="oda_sayisi" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $odasayisiy);
            foreach ($parc as $val) {
                ?><option <?= ($snc->oda_sayisi == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group" id="bina_yasi_con">
    <label for="bina_yasi" class="col-sm-3 control-label">Bina Yaşı</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="bina_yasi" name="bina_yasi" value="<?= htmlspecialchars($snc->bina_yasi, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group" id="bina_kat_sayisi_con">
    <label for="bina_kat_sayisi" class="col-sm-3 control-label">Bina Kat Sayısı</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="bina_kat_sayisi" name="bina_kat_sayisi" value="<?= htmlspecialchars($snc->bina_kat_sayisi, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<?php
$isitma = $fonk->get_lang($snc->dil, "ISITMA");
if ($isitma != '') {
?>
<div class="form-group" id="isitma_con">
    <label class="col-sm-3 control-label">Isıtma</label>
    <div class="col-sm-9">
        <select name="isitma" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $isitma);
            foreach ($parc as $val) {
                ?><option <?= ($snc->isitma == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group" id="banyo_sayisi_con">
    <label for="banyo_sayisi" class="col-sm-3 control-label">Banyo Sayısı</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="banyo_sayisi" name="banyo_sayisi" value="<?= htmlspecialchars($snc->banyo_sayisi, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group" id="esyali_con">
    <label class="col-sm-3 control-label">Eşyalı mı?</label>
    <div class="col-sm-9">
        <select name="esyali" class="form-control">
            <option value="">Seçiniz</option>
            <option value="1" <?= ($snc->esyali == 1) ? 'selected' : ''; ?>>Evet</option>
            <option value="0" <?= ($snc->esyali == 0 && $snc->esyali != '') ? 'selected' : ''; ?>>Hayır</option>
        </select>
    </div>
</div>

<?php
$kuldrm = $fonk->get_lang($snc->dil, "KUL_DURUM");
if ($kuldrm != '') {
?>
<div class="form-group" id="kullanim_durum_con">
    <label class="col-sm-3 control-label">Kullanım Durumu</label>
    <div class="col-sm-9">
        <select name="kullanim_durum" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $kuldrm);
            foreach ($parc as $val) {
                ?><option <?= ($snc->kullanim_durum == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group" id="site_ici_con">
    <label class="col-sm-3 control-label">Site İçerisinde mi?</label>
    <div class="col-sm-9">
        <select name="site_ici" class="form-control">
            <option value="">Seçiniz</option>
            <option value="1" <?= ($snc->site_ici == 1) ? 'selected' : ''; ?>>Evet</option>
            <option value="0" <?= ($snc->site_ici == 0 && $snc->site_ici != '') ? 'selected' : ''; ?>>Hayır</option>
        </select>
    </div>
</div>

<div class="form-group" id="aidat_con">
    <label class="col-sm-3 control-label">Aidat</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="aidat" value="<?= htmlspecialchars($aidat, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">M² Fiyatı</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="metrekare_fiyat" value="<?= htmlspecialchars($snc->metrekare_fiyat, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Ada No</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="ada_no" value="<?= htmlspecialchars($snc->ada_no, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Parsel No</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="parsel_no" value="<?= htmlspecialchars($snc->parsel_no, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Pafta No</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="pafta_no" value="<?= htmlspecialchars($snc->pafta_no, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Kaks (Emsal)</label>
    <div class="col-sm-9">
        <?php
        $kaks_emsl = dil("KAKS_EMSAL");
        if ($kaks_emsl != '') {
        ?>
        <select name="kaks_emsal" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $kaks_emsl);
            foreach ($parc as $val) {
                ?><option <?= ($snc->kaks_emsal == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
        <?php
        } else {
        ?>
        <input type="text" class="form-control" name="kaks_emsal" value="<?= htmlspecialchars($snc->kaks_emsal, ENT_QUOTES, 'UTF-8'); ?>">
        <?php } ?>
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Gabari</label>
    <div class="col-sm-9">
        <?php
        $gabari = dil("GABARI");
        if ($gabari != '') {
        ?>
        <select name="gabari" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $gabari);
            foreach ($parc as $val) {
                ?><option <?= ($snc->gabari == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
        <?php
        } else {
        ?>
        <input type="text" class="form-control" name="gabari" value="<?= htmlspecialchars($snc->gabari, ENT_QUOTES, 'UTF-8'); ?>">
        <?php } ?>
    </div>
</div>

<?php
$imar_drm = dil("IMAR_DURUM");
if ($imar_drm != '') {
?>
<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">İmar Durumu</label>
    <div class="col-sm-9">
        <select name="imar_durum" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $imar_drm);
            foreach ($parc as $val) {
                ?><option <?= ($snc->imar_durum == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php
$tapu_drm = $fonk->get_lang($snc->dil, "TAPU_DRM");
if ($tapu_drm != '') {
?>
<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Tapu Durumu</label>
    <div class="col-sm-9">
        <select name="tapu_durumu" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            $parc = explode("<+>", $tapu_drm);
            foreach ($parc as $val) {
                ?><option <?= ($snc->tapu_durumu == $val) ? 'selected' : ''; ?>><?= $val; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?> 


<?php
// Fonksiyonlar ve güvenlik kontrolleri
?>
<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Kat Karşılığı</label>
    <div class="col-sm-9">
        <select name="katk" class="form-control">
            <option value="">Seçiniz</option>
            <option <?= ($snc->katk == $fonk->get_lang($snc->dil, "TX167")) ? "selected" : ''; ?>><?= $fonk->get_lang($snc->dil, "TX167"); ?></option>
            <option <?= ($snc->katk == $fonk->get_lang($snc->dil, "TX168")) ? "selected" : ''; ?>><?= $fonk->get_lang($snc->dil, "TX168"); ?></option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Krediye Uygunluk</label>
    <div class="col-sm-9">
        <select name="krediu" class="form-control">
            <option value="">Seçiniz</option>
            <option <?= ($snc->krediu == $fonk->get_lang($snc->dil, "TX167")) ? "selected" : ''; ?>><?= $fonk->get_lang($snc->dil, "TX167"); ?></option>
            <option <?= ($snc->krediu == $fonk->get_lang($snc->dil, "TX168")) ? "selected" : ''; ?>><?= $fonk->get_lang($snc->dil, "TX168"); ?></option>
        </select>
    </div>
</div>

<div class="form-group arsa_icin">
    <label class="col-sm-3 control-label">Takas</label>
    <div class="col-sm-9">
        <select name="takas" class="form-control">
            <option value="">Seçiniz</option>
            <option <?= ($snc->takas == $fonk->get_lang($snc->dil, "TX167")) ? "selected" : ''; ?>><?= $fonk->get_lang($snc->dil, "TX167"); ?></option>
            <option <?= ($snc->takas == $fonk->get_lang($snc->dil, "TX168")) ? "selected" : ''; ?>><?= $fonk->get_lang($snc->dil, "TX168"); ?></option>
        </select>
    </div>
</div>

<?php
if ($fonk->get_lang($snc->dil, "KIMDEN") != '') {
    $exp = explode(",", $fonk->get_lang($snc->dil, "KIMDEN"));
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Kimden</label>
    <div class="col-sm-9">
        <select name="kimden" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            foreach ($exp as $row) {
                ?><option <?= ($row == $snc->kimden) ? "selected" : ''; ?>><?= $row; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php
if ($fonk->get_lang($snc->dil, "TX624") != '') {
    $exp = explode(",", $fonk->get_lang($snc->dil, "TX625"));
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Yetki Sözleşmesi</label>
    <div class="col-sm-9">
        <select name="yetkis" class="form-control">
            <option value="">Seçiniz</option>
            <?php
            foreach ($exp as $row) {
                ?><option <?= ($row == $snc->yetkis) ? "selected" : ''; ?>><?= $row; ?></option><?php
            }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<div class="form-group">
    <label for="yetki_bilgisi" class="col-sm-3 control-label"><br/>Yetki Belgesi No.<span style="color:red">*</span></label>
    <div class="col-sm-9">
        <font color="green"><strong>Buraya <font color="red">Emlak Yetki Belgenizin <font color="green">Numarasını Giriniz.</strong></font>
        <input type="text" class="form-control" id="yetki_bilgisi" name="yetki_bilgisi" value="<?= htmlspecialchars($snc->yetki_bilgisi, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<table>
<br/>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> SÖZLEŞMELİ İLANLARINIZ </strong></font><br/><font color="black">Sözleşmeli İlanlarınızı, Daha Hızlı Pazarlamak İçin,</font><br/> <font color="black">İsterseniz, Üyelerimizle ve / veya Grup Oluşturarak, Grup Üyeleriniz ile Paylaşabilirsiniz.</font><br/> <font color="black">Paylaştığınız İlanda, <font color="blue">Size ait - Firma İsmi, Logo, Telefon vs.</font> Olmamalıdır.<br/> <font color="black">İlan Açıklamasının en altına <font color="blue">Bu İlan Yetkilisi Tarafından Paylaşıma Açılmıştır </font> Yazınız <br/> <font color="black">Sözleşmeli İlan Kutucuklarını Doldurduysanız, </font><br/><font color="red"><strong>KAPALI PORTFÖY ve TALEP KUTUCUKLARINI İŞARETLEMEYİNİZ. </strong> </font><br/><br/></p>
</div>

<div class="form-group">
    <label for="site_id_888" class="col-sm-3 control-label"><br/>İLAN PAYLAŞIMI 01</label>
    <div class="col-sm-9">
        <font color="blue"> Sözleşmeli İlanınızı, Üye Emlakçılarımız ile Paylaşmak İçin,</font>&nbsp&nbsp&nbsp<font color="black">Kutunun İçine <font color="red"><strong> 100 </font></strong><font color="black">Yazınız.
        <input type="text" class="form-control" id="site_id_888" name="site_id_888" value="<?= htmlspecialchars($snc->site_id_888, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>	

<div class="form-group">
    <label for="site_id_777" class="col-sm-3 control-label"><br/>İLAN PAYLAŞIMI 02</label>
    <div class="col-sm-9">
        <font color="blue"> Sözleşmeli İlanınızı, Grubunuzdaki Emlakçılar ile Paylaşmak İçin </font>&nbsp&nbsp&nbsp<font color="black">Kutunun İçine <font color="red"><strong> GRUP KODUNU </font></strong><font color="black">Yazınız
        <input type="text" class="form-control" id="site_id_777" name="site_id_777" value="<?= htmlspecialchars($snc->site_id_777, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>
</table>	

<table>	
<br/>
<div class="form-group" style="text-align: center;">
    <p><font color="red"><strong> KAPALI PORTFÖY İLANLARINIZ </strong></font><br/><font color="black"> Kapalı Portföy İlanlarınızı, Artık Sitenize, Girerek Saklayabilir</font><br/><font color="black">Linkini Müşterilerinize Yollayabilir, Ofisinizde Sunum Yapabilirsiniz.</font> <br/><font color="black">İsterseniz, Üyelerimizle ve / veya Grup Oluşturarak, Grup Üyeleriniz ile Paylaşabilirsiniz.<br/><font color="red"> <strong>KAPALI PORTFÖY İLANLARI, HİÇBİR SİTEDE YAYINLANMAZ.</strong></font><br/><font color="blue"> Sitenize Girdiğiniz İlanları, Admin Kısmından, Sadece Siz Görebilirsiniz.</font><br/><font color="blue"> Paylaştığınız İlanları, Paylaştığınız Üyeler, Kendi Sitelerinin Admin kısmına girerek Görebilirler. </font><br/><font color="black">Kapalı Portföy İlan Kutucuklarını Doldurduysanız, </font><br/><font color="red"><strong>SÖZLEŞMELİ İLAN ve TALEPLER KUTUCUKLARINI DOLDURMAYINIZ. </strong> </font><br/><br/>
 </div>

<br/>

<div class="form-group">
    <label for="site_id_699" class="col-sm-3 control-label"><br/><br/><font color="red"> KAPALI PORTFÖY 01 </font></label>
    <div class="col-sm-9">
        <font color="blue"> Kapalı Portföy İlanlarınızı, Üye Emlakçılarımız ile Paylaşmak için,</font><br/> <font color="black"> Kutucuğun İçine <font color="red"><strong> 200 </font></strong><font color="black">Yazınız. ( İlan Başlık Yazısının Başına </font> <font color="red"><strong> KAPALI </font></strong><font color="black">Yazmayı Unutmayınız )</font>  
        <input type="text" class="form-control" id="site_id_699" name="site_id_699" value="<?= htmlspecialchars($snc->site_id_699, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>	

<div class="form-group">
    <label for="site_id_700" class="col-sm-3 control-label"><br/><br/><font color="red"> KAPALI PORTFÖY 02 </font></label>
    <div class="col-sm-9">
        <font color="blue"> Kapalı Portföy İlanlarınızı, PAYLAŞMAK İSTEMEZSENİZ,</font><br/> <font color="black"> Kutucuğun İçine size verdiğimiz <font color="red"><strong> ŞİFREYİ </font></strong><font color="black">Yazınız.
        <input type="text" class="form-control" id="site_id_700" name="site_id_700" value="<?= htmlspecialchars($snc->site_id_700, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group">
    <label for="site_id_701" class="col-sm-3 control-label"><br/><br/><font color="red"> KAPALI PORTFÖY 03 </font></label>
    <div class="col-sm-9">
        <font color="blue"> Kapalı Portföy İlanlarınızı, GRUBUNUZDAKİ EMLAKÇILARLA PAYLAŞMAK İçin </font><br/> <font color="black"> Kutucuğun İçine,<font color="red"><strong> GRUP KODUNU </font></strong><font color="black">Yazınız.
        <input type="text" class="form-control" id="site_id_701" name="site_id_701" value="<?= htmlspecialchars($snc->site_id_701, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>	

<table>	 
<br/>
<div class="form-group" style="text-align: center;">
    <p><strong><font color="blue"> İLANINIZI, PORTALLARIMIZDA DA YAYINLAYABİLİRSİNİZ </font> <br/><font color="red">İSTEDİĞİNİZ ( Portalın / Portalların ) KUTUCUĞUNU TEKRAR İŞARETLEYİNİZ. </font></strong><br/><font color="black"> ( TEKRAR İŞARETLEMEDİĞİNİZ ( Portalımızda / Portallarımızda ) İLANINIZ YAYINLANMAZ.) </font><br/><font color="black">( İLANINIZ, Portallarımızda Sizin Bilgilerinizle Yayınlanır. )</font><br/><br/>
<table>	 
	
<!-- Checkbox’lar Başlangıcı -->
<table>
    <div style="margin-top: 10px;">
        <!-- İZMİR EMLAK SİTESİ -->
        <label for="site_id_335_checkbox">İZMİR EMLAK SİTESİ  </label>
        <input type="checkbox" id="site_id_335_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" 
            <?php echo ($snc->site_id_335 == 335) ? 'checked' : ''; ?> />
        <label style="float:left;margin-right:10px;" for="site_id_335_checkbox" class="stm-checkbox-label"></label>
        <span style="margin-right:5px;font-size:14px;margin-top:5px;"> 
            ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( izmiremlaksitesi.com.tr )</strong></font> Portalımızda Yayınlanır. )
        </span>
        <input type="hidden" id="site_id_335" name="site_id_335" value="<?= htmlspecialchars($snc->site_id_335, ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div style="margin-top: 10px;">
        <!-- İSTANBUL EMLAK SİTESİ -->
        <label for="site_id_334_checkbox">İSTANBUL EMLAK SİTESİ  </label>
        <input type="checkbox" id="site_id_334_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" 
            <?php echo ($snc->site_id_334 == 334) ? 'checked' : ''; ?> />
        <label style="float:left;margin-right:10px;" for="site_id_334_checkbox" class="stm-checkbox-label"></label>
        <span style="margin-right:5px;font-size:14px;margin-top:5px;"> 
            ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( istanbulemlaksitesi.com.tr )</strong></font> Portalımızda Yayınlanır. )
        </span>
        <input type="hidden" id="site_id_334" name="site_id_334" value="<?= htmlspecialchars($snc->site_id_334, ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div style="margin-top: 10px;">
        <!-- ANKARA EMLAK SİTESİ -->
        <label for="site_id_306_checkbox">ANKARA EMLAK SİTESİ  </label>
        <input type="checkbox" id="site_id_306_checkbox" style="margin-right: 10px;" onclick="updateSiteIds()" 
            <?php echo ($snc->site_id_306 == 306) ? 'checked' : ''; ?> />
        <label style="float:left;margin-right:10px;" for="site_id_306_checkbox" class="stm-checkbox-label"></label>
        <span style="margin-right:5px;font-size:14px;margin-top:5px;"> 
            ( Kutucuğu İşaretlerseniz, İLANINIZ <font color="red"><strong>( ankaraemlaksitesi.com.tr )</strong></font> Portalımızda Yayınlanır. )
        </span>
        <input type="hidden" id="site_id_306" name="site_id_306" value="<?= htmlspecialchars($snc->site_id_306, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</table>
<!-- Checkbox’lar Bitişi -->


<!-- JavaScript Kodu -->
<script>
function updateSiteIds() {
    const checkboxes = [
        { checkbox: document.getElementById('site_id_335_checkbox'), hiddenInput: document.getElementById('site_id_335'), siteId: 335 },
        { checkbox: document.getElementById('site_id_334_checkbox'), hiddenInput: document.getElementById('site_id_334'), siteId: 334 },
        { checkbox: document.getElementById('site_id_306_checkbox'), hiddenInput: document.getElementById('site_id_306'), siteId: 306 }
    ];

    checkboxes.forEach(function(entry) {
        if (entry.checkbox && entry.checkbox.checked) {
            entry.hiddenInput.value = entry.siteId; // İşaretliyse site ID’sini yaz
        } else if (entry.checkbox) {
            entry.hiddenInput.value = 0; // İşaretsizse 0 yaz
        }
    });
}

// Sayfa yüklendiğinde mevcut durumu hidden input’lara yansıt
window.onload = function() {
    updateSiteIds();
};
</script>

<?php
$delm1 = explode("<+>", $fonk->get_lang($snc->dil, "CEPHE"));
$delm2 = explode("<+>", $fonk->get_lang($snc->dil, "IC_OZELLIKLER"));
$delm3 = explode("<+>", $fonk->get_lang($snc->dil, "DIS_OZELLIKLER"));
$delm4 = explode("<+>", $fonk->get_lang($snc->dil, "ALTYAPI_OZELLIKLER"));
$delm5 = explode("<+>", $fonk->get_lang($snc->dil, "KONUM_OZELLIKLER"));
$delm6 = explode("<+>", $fonk->get_lang($snc->dil, "GENEL_OZELLIKLER"));
$delm7 = explode("<+>", $fonk->get_lang($snc->dil, "MANZARA_OZELLIKLER"));
$cdelm1 = count($delm1);
$cdelm2 = count($delm2);
$cdelm3 = count($delm3);
$cdelm4 = count($delm4);
$cdelm5 = count($delm5);
$cdelm6 = count($delm6);
$cdelm7 = count($delm7);
if ($cdelm1 > 1 || $cdelm2 > 1 || $cdelm3 > 1 || $cdelm4 > 1 || $cdelm5 > 1 || $cdelm6 > 1 || $cdelm7 > 1) {
?>
<div class="form-group">
    <div class="col-sm-12">
        <style type="text/css">
        .ilanaciklamalar h3 {
            float: left;
            font-size: 18px;
            font-weight: 700;
            width: 100%;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom-width: 2px;
            border-bottom-style: solid;
            border-bottom-color: #ed2d2d
        }

        .ilanozellik {
            margin: auto;
            width: 90%
        }

        .ilanozellik h4 {
            float: left;
            width: 100%;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: #CCC;
            margin-top: 20px
        }

        .ilanaciklamalar {
            margin-bottom: 30px;
            float: left;
            width: 100%
        }

        .ilanozellik span {
            float: left;
            width: 183px;
            margin-bottom: 10px;
            color: #ccc;
            font-size: 14px
        }

        #ozellikaktif {
            color: #000;
            font-weight: 700
        }

        .ilanozellik span i {
            color: #4CAF50;
            margin-right: 7px
        }
        </style>
        <div class="ilanaciklamalar" id="ozellikler_con">
            <h3>Özellikleri Seçin</h3>

            <?php
            $checkbox = 0;
            if ($cdelm1 > 1) {
                $ielm = explode("<+>", $snc->cephe_ozellikler);
            ?>
            <div class="ilanozellik tipi_konut">
                <h4><strong>Cephe</strong></h4>
                <?php
                foreach ($delm1 as $val) {
                    $checked = (in_array($val, $ielm)) ? 'checked' : '';
                ?>
                <span><label style="font-weight:normal;"><input name="cephe_ozellikler[]" value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>" <?= $checked; ?> type="checkbox"> <?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></label></span>
                <?php
                }
                ?>
            </div>
            <?php } ?>

            <?php
            if ($cdelm2 > 1) {
                $ielm = explode("<+>", $snc->ic_ozellikler);
            ?>
            <div class="ilanozellik tipi_konut">
                <h4><strong>İç Özellikler</strong></h4>
                <?php
                foreach ($delm2 as $val) {
                    $checked = (in_array($val, $ielm)) ? 'checked' : '';
                ?>
                <span><label style="font-weight:normal;"><input name="ic_ozellikler[]" type="checkbox" <?= $checked; ?> value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></label></span>
                <?php
                }
                ?>
            </div>
            <?php } ?>

            <?php
            if ($cdelm3 > 1) {
                $ielm = explode("<+>", $snc->dis_ozellikler);
            ?>
            <div class="ilanozellik tipi_konut">
                <h4><strong>Dış Özellikler</strong></h4>
                <?php
                foreach ($delm3 as $val) {
                    $checked = (in_array($val, $ielm)) ? 'checked' : '';
                ?>
                <span><label style="font-weight:normal;"><input name="dis_ozellikler[]" type="checkbox" <?= $checked; ?> value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></label></span>
                <?php
                }
                ?>
            </div>
            <?php } ?>

            <?php
            if ($cdelm4 > 1) {
                $ielm = explode("<+>", $snc->altyapi_ozellikler);
            ?>
            <div class="ilanozellik tipi_arsa">
                <h4><strong>Altyapı Özellikler</strong></h4>
                <?php
                foreach ($delm4 as $val) {
                    $checked = (in_array($val, $ielm)) ? 'checked' : '';
                ?>
                <span><label style="font-weight:normal;"><input name="altyapi_ozellikler[]" type="checkbox" <?= $checked; ?> value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></label></span>
                <?php
                }
                ?>
            </div>
            <?php } ?>

            <?php
            if ($cdelm5 > 1) {
                $ielm = explode("<+>", $snc->konum_ozellikler);
            ?>
            <div class="ilanozellik tipi_arsa">
                <h4><strong>Konum Özellikler</strong></h4>
                <?php
                foreach ($delm5 as $val) {
                    $checked = (in_array($val, $ielm)) ? 'checked' : '';
                ?>
                <span><label style="font-weight:normal;"><input name="konum_ozellikler[]" type="checkbox" <?= $checked; ?> value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></label></span>
                <?php
                }
                ?>
            </div>
            <?php } ?>

            <?php
            if ($cdelm6 > 1) {
                $ielm = explode("<+>", $snc->genel_ozellikler);
            ?>
            <div class="ilanozellik tipi_arsa">
                <h4><strong>Genel Özellikler</strong></h4>
                <?php
                foreach ($delm6 as $val) {
                    $checked = (in_array($val, $ielm)) ? 'checked' : '';
                ?>
                <span><label style="font-weight:normal;"><input name="genel_ozellikler[]" type="checkbox" <?= $checked; ?> value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></label></span>
                <?php
                }
                ?>
            </div>
            <?php } ?>

            <?php
            if ($cdelm7 > 1) {
                $ielm = explode("<+>", $snc->manzara_ozellikler);
            ?>
            <div class="ilanozellik tipi_arsa">
                <h4><strong>Manzara Özellikler</strong></h4>
                <?php
                foreach ($delm7 as $val) {
                    $checked = (in_array($val, $ielm)) ? 'checked' : '';
                ?>
                <span><label style="font-weight:normal;"><input name="manzara_ozellikler[]" type="checkbox" <?= $checked; ?> value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></label></span>
                <?php
                }
                ?>
            </div>
            <?php } ?>

        </div>
    </div>
</div>
<?php } ?>

<div class="form-group kurumsal">
    <label for="google_maps" class="col-sm-3 control-label">Google Maps<br><span style="font-weight:lighter;font-size:14px;">Harita konumu otomatik olarak belirlediğiniz il/ilçe/mahalle'ye göre işaretlenmektedir. Dilerseniz cadde veya sokak ekleyerek de daraltabilirsiniz. Hassas işaretleme için imleci sürükleyip bırakınız.</span></label>
    <div class="col-sm-9">
        <div class="form-group">
            <div class="col-sm-11">
                <h4><strong><i class="fa fa-map-marker" aria-hidden="true"></i> Konum Belirleyin.</strong></h4>
            </div>
        </div>

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Şehir</label>
            <div class="col-sm-11">
                <input disabled class="form-control" id="map_il" value="<?= htmlspecialchars($il_adi, ENT_QUOTES, 'UTF-8'); ?>" type="text">
            </div><!-- col end -->
        </div><!-- row end -->

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">İlçe</label>
            <div class="col-sm-11">
                <input disabled id="map_ilce" class="form-control" value="<?= htmlspecialchars($ilce_adi, ENT_QUOTES, 'UTF-8'); ?>" type="text">
            </div><!-- col end -->
        </div><!-- row end -->

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Mahalle</label>
            <div class="col-sm-11">
                <input disabled onchange="yazdir();" type="text" id="map_mahalle" class="form-control" value="<?= htmlspecialchars($mahalle_adi, ENT_QUOTES, 'UTF-8'); ?>">
            </div><!-- col end -->
        </div><!-- row end -->

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Cadde</label>
            <div class="col-sm-11">
                <input onchange="yazdir();" type="text" class="form-control" name="map_cadde" value="" placeholder="varsa cadde giriniz.">
            </div><!-- col end -->
        </div><!-- row end -->

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Sokak</label>
            <div class="col-sm-11">
                <input onchange="yazdir();" type="text" class="form-control" name="map_sokak" value="" placeholder="varsa sokak giriniz.">
            </div><!-- col end -->
        </div><!-- row end -->
		

<input type="text" class="form-control" id="map_adres" name="map_adres" placeholder="Adres yazınız..." style="display: none;">
        <input type="text" id="coords" name="maps" value="<?= htmlspecialchars($snc->maps, ENT_QUOTES, 'UTF-8'); ?>" style="display:none;" />

        <div id="map" style="width: 100%; height: 300px"></div>

        <?php
        $coords = ($snc->maps == '') ? "41.003917,28.967299" : $snc->maps;
        list($lat, $lng) = explode(",", $coords);
        ?>
        <input type="hidden" value="<?= htmlspecialchars($lat, ENT_QUOTES, 'UTF-8'); ?>" id="g_lat">
        <input type="hidden" value="<?= htmlspecialchars($lng, ENT_QUOTES, 'UTF-8'); ?>" id="g_lng">

        <script type="text/javascript">
        function initMap() {
            const g_lat = parseFloat(document.getElementById("g_lat").value);
            const g_lng = parseFloat(document.getElementById("g_lng").value);
            const map = new google.maps.Map(document.getElementById('map'), {
                dragable: true,
                zoom: 15,
                center: { lat: g_lat, lng: g_lng }
            });
            const geocoder = new google.maps.Geocoder();

            const marker = new google.maps.Marker({
                position: {
                    lat: g_lat,
                    lng: g_lng
                },
                map: map,
                draggable: true
            });

            jQuery('#map_adres').on('change', function() {
                const val = $(this).val();
                geocodeAddress(marker, geocoder, map, val);
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                dragend(marker);
            });
        }

        function geocodeAddress(marker, geocoder, resultsMap, address) {
            if (address) {
                geocoder.geocode({ 'address': address }, function(results, status) {
                    if (status === 'OK') {
                        resultsMap.setCenter(results[0].geometry.location);
                        marker.setMap(resultsMap);
                        marker.setPosition(results[0].geometry.location);
                        dragend(marker);
                    } else {
                        console.log('Geocode was not successful for the following reason: ' + status + " word: " + address);
                    }
                });
            }
        }

        function dragend(marker) {
            const lat = marker.getPosition().lat();
            const lng = marker.getPosition().lng();
            document.getElementById("coords").value = lat + "," + lng;
        }
        </script>
    </div>
</div>


</div>
</div>

<div class="form-group">
    <label for="icerik" class="col-sm-1 control-label">Açıklamalar</label>
    <div class="col-sm-11">
        <textarea class="summernote form-control" rows="9" id="icerik" name="icerik"><?= htmlspecialchars($snc->icerik, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">İlan Notunuz</label>
    <div class="col-sm-9">
        <textarea name="notu" class="form-control" placeholder="Bu notu sadece siz görebilirsiniz. ( İlanı Paylaşacaksanız, Buraya Özel Birşey Yazmayınız.) "><?= htmlspecialchars($snc->notu, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>
</div>

<?= $fonk->bilgi(" Google'da rekabeti düşük kelimelerde organik olarak ilk sayfaya yükselmek için mutlaka aşağıdaki bilgileri doldurunuz. Sadece sayfa içeriği ile alakalı bilgiler girmelisiniz. Aksi halde spam cezası alabilirsiniz."); ?>

<div class="form-group">
    <label for="title" class="col-sm-3 control-label">SEO Başlık (Title)</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($snc->title, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group">
    <label for="keywords" class="col-sm-3 control-label">SEO Kelimeler (Keywords)</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="keywords" name="keywords" value="<?= htmlspecialchars($snc->keywords, ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<div class="form-group">
    <label for="description" class="col-sm-3 control-label">SEO Açıklama (Description)</label>
    <div class="col-sm-9">
        <textarea class="form-control" rows="5" id="description" name="description"><?= htmlspecialchars($snc->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>
</div>

<?php if ($multict > 0 && $snc->id == $multif->id) { ?>
</div><!-- tab1 end-->
</div><!-- tabcontent end -->
<?php } ?>

<div align="right">
    <button type="button" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms', 'form_status');">Kaydet</button>
</div>
</form>

</div>
</div>
</div><!-- Col1 end -->
</div><!-- row end -->
</div> <!-- tab1 end -->

<?php if ($multict > 0 && $snc->id == $multif->id) { ?>
<div class="tab-pane<?= ($goto == 'photos') ? " active " : ''; ?>" id="tab2">
    <div class="alert alert-info" role="alert">
        <strong style="color:red;">İlan Fotoğraflarının Uzantısı .jpeg Olmalıdır.</strong><br>
        <strong style="color:red;">İlan fotoğraflarını 600x800 pixel veya 900x1200 pixel olarak ve Yatay çekerseniz, Fotoğraflar sitenizde daha güzel gözükür</strong><br>
        Sistem, yüklenen fotoğrafları filtreden geçirerek otomatik olarak tekrar boyutlandırmaktadır.<br>
        Yükleme yaptıktan sonra kapak fotoğrafını değiştirebilir ve Fotoğrafların yerlerini, kaydırarak sıralayabilirsiniz.
    </div>

    <div class="m-b-30">
        <form action="#" class="dropzone" id="dropzone">
            <div class="fallback">
                <input name="file" type="file" multiple="multiple">
            </div>
        </form>
    </div>

    <style>
        .ilanfototasi {
            position: absolute;
            margin-left: 135px;
            color: white;
            font-size: 22px;
            opacity: 0.8;
            filter: alpha(opacity=80);
            cursor: -webkit-grabbing;
        }

        .ilanfototasi:hover {
            opacity: 1.8;
            filter: alpha(opacity=100);
        }
    </style>

    <div id="silsnc"></div>

    <form role="form" class="form-horizontal" action="ajax.php?p=galeri_guncelle&ilan_id=<?= $snc->id; ?>" method="POST" id="GaleriGuncelleForm">
        <div class="row port">
            <div class="portfolioContainer">
                <ul id="list" class="uk-nestable" data-uk-nestable="{maxDepth:1}">
                    <?php
                    $linkcek = "https://www.turkiyeemlaksitesi.com.tr";
                    $sql = $db->query("SELECT * FROM galeri_foto WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND sayfa_id=" . $snc->id . " AND dil='" . $dil . "' ORDER BY sira ASC");
                    $i = 0;
                    while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                        $i += 1;
                    ?>
                    <li class="uk-nestable-item" data-id="<?= $i; ?>" data-idi="<?= $row->id; ?>" id="foto_<?= $row->id; ?>">
                        <div class="col-sm-6 col-lg-3 col-md-4 webdesign illustrator">
                            <div class="gal-detail thumb">
                                <div class="ilanfototasi"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div>
                                <a href="<?= $linkcek; ?>/uploads/<?= $row->resim; ?>" class="image-popup"><img src="<?= $linkcek; ?>/uploads/thumb/<?= $row->resim; ?>" width="150" height="150" class="thumb-img" alt="work-thumbnail"></a>
                                <div class="clearfix"></div>
                                <div class="radio radio-success radio-single">
                                    <input type="radio" id="<?= $row->id; ?>" name="kapak" value="<?= $row->resim; ?>" <?= ($snc->resim == $row->resim) ? 'checked' : ''; ?>><label for="<?= $row->id; ?>">Kapak Görseli Seç</label>
                                </div>
                                <a style="margin-top: -60px; float: right;" href="javascript:;" onclick="ajaxHere('ajax.php?p=galeri_foto_sil&id=<?= $row->id; ?>', 'silsnc');"><button type="button" class="btn btn-icon waves-effect waves-light btn-danger m-b-5"><i class="fa fa-trash" aria-hidden="true"></i></button></a>
                                <a title="Döndür" style="margin-top: -60px; margin-right: 40px; float: right;" href='javascript:window.open ("<?= SITE_URL . "rotate/" . $row->id; ?>", "mywindow", "status=1,toolbar=0,resizable=1,width=" + window.innerWidth + ",height=" + window.innerHeight + 100 + "").moveTo(0, 0);'><button type="button" class="btn btn-icon waves-effect waves-light btn-info m-b-5"><i class="fa fa-repeat" aria-hidden="true"></i></button></a>
                            </div>
                        </div>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <div align="right">
            <button type="button" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('GaleriGuncelleForm', 'silsnc');">Güncelle</button>
        </div>
    </form>
</div><!-- tab2 end -->

<div class="tab-pane<?= ($goto == 'video') ? " active " : ''; ?>" id="tab3">
    <?php if ($snc->video != '') { ?>
    <div id="VideoVarContent">
        <div align="center">
            <video width="70%" height="500" controls>
                <source src="/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/<?= htmlspecialchars($snc->video, ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4">
                <?= $fonk->get_lang($snc->dil, "VIDEO_SUPPORT"); ?>
            </video>
        </div>
        <div class="clear"></div>
        <div align="right">
            <br />
            <a href="javascript:;" class="btn btn-danger" onclick="ajaxHere('ajax.php?p=video_sil&ilan_id=<?= $snc->id; ?>', 'SilOutput');"><i class="fa fa-trash-o" aria-hidden="true"></i> Videoyu Kaldır</a>
        </div>
        <div class="clear"></div>
        <div id="SilOutput" style="display:none"></div>
    </div><!-- VideoVarContent end -->
    <?php } ?>

    <div id="galeri_video_ekle" <?= ($snc->video != '') ? 'style="display:none"' : ''; ?>>
        <div class="alert alert-info" role="alert"><?= $fonk->get_lang($snc->dil, "TX4572"); ?></div>
        <div style="height:200px;float:left;width:100%;">
            <form action="ajax.php?p=galeri_video_guncelle&ilan_id=<?= $snc->id; ?>&from=adv" method="POST" id="VideoForm" enctype="multipart/form-data">
                <center><input type="file" name="video" id="VideoSec" /></center>
                <div class="clear"></div>
            </form>
            <div class="yuklebar" id="YuklemeBar" style="display:none">
                <span id="percent">0%</span>
                <div class="yuklebarasama animated flash" id="YuklemeDurum"></div>
            </div>
            <div class="clear"></div>
        </div>
        <hr style="border: 1px solid #eee;">
        <br>
        <div align="right">
            <a style="margin-left: 15px;" class="btn btn-success" href="javascript:YuklemeBaslat();"><?= $fonk->get_lang($snc->dil, "TX442"); ?> <i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
        </div>
        <div class="clear"></div>
        <div align="right"><br>
            <div id="VideoForm_output" style="display:none"></div>
        </div>
        <div class="clear"></div>
    </div><!-- galeri video ekle end -->
</div><!-- tab3 end -->


<?php if ($gayarlar->dopingler_501 == 1) { ?>
<div class="tab-pane<?= ($goto == 'doping') ? " active " : ''; ?>" id="tab4">
    <?php
    list($dzaman1a, $dzaman1b) = explode("|", $gayarlar->dzaman1);
    list($dzaman2a, $dzaman2b) = explode("|", $gayarlar->dzaman2);
    list($dzaman3a, $dzaman3b) = explode("|", $gayarlar->dzaman3);
    $dzaman1b = $periyod[$dzaman1b];
    $dzaman2b = $periyod[$dzaman2b];
    $dzaman3b = $periyod[$dzaman3b];
    $from = "adv";
    ?>
    <form action="ajax.php?p=ilan_dopingle&id=<?= $id; ?>&from=<?= $from; ?>" method="POST" id="DopingleForm">
        <h4 style="font-weight:bold;margin-bottom:20px;color:#be2527;font-size:18px;"><?= $fonk->get_lang($snc->dil, "TX517"); ?></h4>
        <div class="alert alert-info" role="alert"><?= $fonk->get_lang($snc->dil, "TX518"); ?></div>
        <br>
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
                <td bgcolor="#eee"><h5><strong><?= $fonk->get_lang($snc->dil, "TX519"); ?></strong></h5></td>
                <td align="center" bgcolor="#eee"><strong><?= $dzaman1a . " " . $dzaman1b; ?></strong></td>
                <td align="center" bgcolor="#eee"><strong><?= $dzaman2a . " " . $dzaman2b; ?></strong></td>
                <td align="center" bgcolor="#eee"><strong><?= $dzaman3a . " " . $dzaman3b; ?></strong></td>
                <td align="center" bgcolor="#eee"><strong>Sınırsız</strong></td>
            </tr>

            <?php
            $bugun = date("Y-m-d");
            $sec = 0;
            $dopingler_501 = $db->query("SELECT * FROM doping_ayarlar_501 ORDER BY id ASC");
            while ($row = $dopingler_501->fetch(PDO::FETCH_OBJ)) {
                ## Eğer aynı dopingten varsa ve süresi bitmemişse... ##
                $isdoping = $db->prepare("SELECT * FROM dopingler_501 WHERE ilan_id=? AND did=? AND btarih > NOW()");
                $isdoping->execute(array($snc->id, $row->id));
            ?>
            <tr>
                <td><?= $fonk->get_lang($snc->dil, "DOPING" . $row->id); ?></td>
                <?php if ($isdoping->rowCount() > 0) {
                    $isdoping = $isdoping->fetch(PDO::FETCH_OBJ);
                ?>
                <td align="center" colspan="4">
                    <?php if ($isdoping->durum == 0) { ?>
                    <h5 style="color:orange;"><i class="fa fa-check"></i> <?= $fonk->get_lang($snc->dil, "TX533"); ?></h5>
                    <?php } elseif ($isdoping->durum == 1) {
                        $kgun = $fonk->gun_farki($isdoping->btarih, $bugun);
                    ?>
                    <?php if ($isdoping->sure == 100 && $isdoping->periyod == "yillik") { ?>
                    <strong style="color:green">Süresiz</strong>
                    <?php } elseif ($kgun < 0) { ?>
                    <strong style="color:red"><i class="fa fa-clock-o"></i> <?= $fonk->get_lang($snc->dil, "TX562"); ?></strong>
                    <?php } else { ?>
                    <strong><i class="fa fa-clock-o"></i> <?= ($kgun == 0) ? $fonk->get_lang($snc->dil, "TX563") : $kgun . " " . $fonk->get_lang($snc->dil, "TX564"); ?></strong>
                    <?php } ?>
                    <?php } ?>
                </td>
                <?php } else { $sec += 1; ?>
                <td align="center">
                    <label><input name="doping[<?= $row->id; ?>]" class="checkbox_one" type="checkbox" value="1"> Seç</label>
                </td>
                <td align="center">
                    <label><input name="doping[<?= $row->id; ?>]" class="checkbox_one" type="checkbox" value="2"> Seç</label>
                </td>
                <td align="center">
                    <label><input name="doping[<?= $row->id; ?>]" class="checkbox_one" type="checkbox" value="3"> Seç</label>
                </td>
                <td align="center">
                    <label><input name="doping[<?= $row->id; ?>]" class="checkbox_one" type="checkbox" value="4"> Seç</label>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
    </form>
</div><!-- tab4 end -->
<?php } ?>

<div class="clear"></div>

<?php if ($sec > 0) { ?>
<hr style="border: 1px solid #eee;">
<br>
<div align="right">
<a  style="margin-left: 15px;"  class="btn btn-success" href="javascript:void(0);" onclick="AjaxFormS('DopingleForm', 'DopingleForm_output');" id="DopingleButon"><i class="fa fa-check" aria-hidden="true"></i> <?= $fonk->get_lang($snc->dil, "TX524"); ?></a>
</div>
<div id="DopingleForm_output" style="display:none" align="left"></div>
<?php } ?>

</form> 
</div><!-- tab3 end -->
<?php } ?>

</div>
</div>

</div>

</div>
</div>

</div>

</div>
<script>
    var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css">
<link href="assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
<link rel="stylesheet" href="assets/vendor/magnific-popup/dist/magnific-popup.css">
<link href="assets/vendor/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="assets/css/components/nestable.almost-flat.min.css">
<link rel="stylesheet" href="assets/css/components/nestable.min.css">
<link rel="stylesheet" href="assets/css/components/nestable.gradient.min.css">
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>
<script src="assets/js/uikit.min.js"></script>
<script src="assets/js/components/nestable.min.js"></script>
<script>
$('.uk-nestable').on('change.uk.nestable', function(e) {
    var data = $("#list").data("nestable").serialize();
    $.post("ajax.php?p=galeri_guncelle&ilan_id=<?= $snc->id; ?>&from=nestable", {value : data}, function (a) {
        $("#silsnc").html(a);
    });
});

jQuery(document).ready(function() {
    $('.summernote').summernote({
        height: 200, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: false // set focus to editable area after initializing summernote
    });
});
</script>

<script>
var gurl = 'ajax.php?p=ilan_duzenle&id=<?= $snc->id; ?>&galeri=1';
function YuklemeBitti() {
    window.location.href = "index.php?p=ilan_duzenle&id=<?= $snc->id; ?>&goto=photos#tab2";
}
</script>
<script src="assets/vendor/dropzone/dist/dropzone_galeri.js"></script>

<script type="text/javascript" src="assets/vendor/isotope/dist/isotope.pkgd.min.js"></script>
<script type="text/javascript" src="assets/vendor/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.image-popup').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-fade',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        }
    });
});

$(document).ready(function() {
    var select = "#yetki_bilgisi_con,#brut_metrekare_con,#site_id_888_con,#site_id_777_con,#site_id_699_con,#site_id_700_con,#site_id_701_con,#site_id_702_con,#site_id_661_con,#site_id_662_con,#site_id_663_con,#site_id_664_con,#site_id_665_con,#site_id_666_con,#site_id_667_con,#site_id_668_con,#site_id_669_con,#site_id_335_con,#site_id_334_con,#site_id_306_con,#konut_sekli_con,#konut_tipi_con,#bulundugu_kat_con,#yapi_durum_con,#oda_sayisi_con,#bina_yasi_con,#bina_kat_sayisi_con,#isitma_con,#banyo_sayisi_con,#esyali_con,#kullanim_durum_con,#site_ici_con,#aidat_con,#notu_con";
    <?php echo ($snc->emlak_tipi == $arsa) ? '$(".tipi_konut").slideUp(500); $(select).slideUp(500);' : ''; ?>
    <?php echo ($snc->emlak_tipi != $arsa) ? '$(".tipi_arsa,.arsa_icin").hide(1);' : ''; ?>

    $("select[name='emlak_tipi']").change(function() {
        var val = $(this).val();
        if (val == '<?= $arsa; ?>') {
            $(select).slideUp(500);
            $(".tipi_konut").slideUp(500);
            $(".tipi_arsa,.arsa_icin").slideDown(500);
        } else {
            $(select).slideDown(500);
            $(".tipi_arsa,.arsa_icin").slideUp(500);
            $(".tipi_konut").slideDown(500);
        }
    });
});

function konut_getir(tipi) {
    if (tipi == "<?= $isyeri; ?>") {
        $("select[name=konut_tipi]").html("<?php
            $knttipi = $fonk->get_lang($snc->dil, "KNT_TIPI2");
            ?><option value=''><?= $fonk->get_lang($snc->dil, "TX57"); ?></option><?php
            $parc = explode("<+>", $knttipi);
            foreach ($parc as $val) {
            ?><option><?= $val; ?></option><?php
            }
            ?>");
    } else {
        $("select[name=konut_tipi]").html("<?php
            $knttipi = $fonk->get_lang($snc->dil, "KNT_TIPI");
            ?><option value=''><?= $fonk->get_lang($snc->dil, "TX57"); ?></option><?php
            $parc = explode("<+>", $knttipi);
            foreach ($parc as $val) {
            ?><option><?= $val; ?></option><?php
            }
            ?>");
    }
}

/* Map Ayarları */
function yazdir() {
    var ulke = $("#ulke_id").val();
    ulke = $("#ulke_id option[value='" + ulke + "']").text();
    var il = $("#il").val();
    il = $("#il option[value='" + il + "']").text();
    var ilce = $("#ilce").val();
    ilce = $("#ilce option[value='" + ilce + "']").text();
    var maha = $("#semt").val();
    maha = $("#semt option[value='" + maha + "']").text();
    var cadde = $("input[name='map_cadde']").val();
    var sokak = $("input[name='map_sokak']").val();
    var neler = "";

    if (il != undefined && il != '' && il != '<?= $fonk->get_lang($snc->dil, "TX264"); ?>') {
        if (ulke != undefined && ulke != '' && ulke != '<?= $fonk->get_lang($snc->dil, "TX264"); ?>') {
            neler += ", " + ulke;
        }
        neler += il;
        $("#map_il").val(il);
        if (ilce != undefined && ilce != '' && ilce != '<?= $fonk->get_lang($snc->dil, "TX264"); ?>') {
            neler += ", " + ilce;
            $("#map_ilce").val(ilce);
            if (maha != undefined && maha != '' && maha != '<?= $fonk->get_lang($snc->dil, "TX264"); ?>') {
                neler += ", " + maha;
                $("#map_mahalle").val(maha);
            } else {
                $("#map_mahalle").val('');
            }
            if (cadde != undefined && cadde != '' && cadde != '<?= $fonk->get_lang($snc->dil, "TX264"); ?>') {
                neler += ", " + cadde;
            }
            if (sokak != undefined && sokak != '' && sokak != '<?= $fonk->get_lang($snc->dil, "TX264"); ?>') {
                neler += ", " + sokak;
            }
        } else {
            $("#map_ilce").val('');
        }
    } else {
        $("#map_il").val('');
    }
    $("input[name='map_adres']").val(neler);
    GetMap();
}

function GetMap() {
    $("#map_adres").trigger("change");
}
</script>
<script type="text/javascript">
function DosyaBoyutu(input_id) {
    var input, file;
    if (!window.FileReader) {
        console.log("File API'si bu tarayıcıda henüz desteklenmiyor.");
        return 0;
    } else {
        input = document.getElementById(input_id);
        if (!input) {
            console.log("Hm, ilgili input elementi yerinde yok :)");
            return 0;
        } else if (!input.files) {
            console.log("Bu tarayıcı dosya girdilerinin `files` özelliğini desteklemiyor gibi görünüyor.");
            return 0;
        } else if (!input.files[0]) {
            console.log("Dosya seçilmemiş görünüyor.");
            return 0;
        } else {
            file = input.files[0];
            return file.size;
        }
    }
}

function DosyaUzantiKontrol(input_id, Uzantilar) {
    var oInput = document.getElementById(input_id);
    if (oInput.type == "file") {
        var sFileName = oInput.value;
        if (sFileName.length > 0) { // eğer dosya seçilmişse
            var blnValid = false;
            for (var j = 0; j < Uzantilar.length; j++) { // izin verilen uzantılar döndürüyoruz...
                var sCurExtension = Uzantilar[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            } // izin verilen uzantılar döndürüyoruz...
            if (blnValid) { // eğer uzantı geçerliyse
                return true;
            } else { // eğer uzantı geçersiz ise...
                console.log("Sorry, " + sFileName + " is invalid, allowed extensions are: " + Uzantilar.join(", "));
                return false;
            } // eğer uzantı geçersiz ise...
        } else { // Eğer dosya seçilmemiş ise
            return false;
        } // Eğer dosya seçilmemiş ise
    } else { // Tipi dosya değil ise
        return false;
    } // Tipi dosya değil ise
}


function YuklemeBaslat() {
    var video = $("#VideoSec")[0].files.length;
    if (video < 1) {
        $("#VideoForm_output").html("<span class='error'><?= $fonk->get_lang($snc->dil, "TX454"); ?></span>");
        $("#VideoForm_output").fadeIn(600);
    } else {
        var videoSize = DosyaBoyutu('VideoSec');
        var VideoValid = DosyaUzantiKontrol('VideoSec', ['.mp4']);
        if (videoSize > <?= $fonk->get_lang($snc->dil, "VIDEO_MAX_BAYT"); ?>) {
            $("#VideoForm_output").html("<span class='error'><?= $fonk->get_lang($snc->dil, "TX455"); ?></span>");
            $("#VideoForm_output").fadeIn(600);
        } else if (!VideoValid) {
            $("#VideoForm_output").html("<span class='error'><?= $fonk->get_lang($snc->dil, "TX456"); ?></span>");
            $("#VideoForm_output").fadeIn(600);
        } else { // Eğer video boyutu çok değilse
            $("#VideoForm_output").fadeOut(400);
            $("#VideoForm").slideUp(400, function() {
                $("#YuklemeBar").slideDown(400);
            });
            var bar = $('#YuklemeDurum');
            var percent = $('#percent');
            $("#VideoForm").ajaxForm({
                target: '#VideoForm_output',
                beforeSend: function() {
                    percent.attr("style", "");
                    var percentVal = '0%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percent_style = percent.attr("style");
                    if (percent_style == undefined) {
                        percent_style = '';
                    }
                    if (percentComplete >= 47 && percent_style == '') {
                        percent.attr("style", "color:#FFF;z-index:3;position:relative;");
                    }
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                complete: function() {
                    $("#VideoForm_output").fadeIn(400);
                }
            }).submit();
        } // Video max boyutu aşmıyorsa...
    } // Video seçilmiş ise devam...
}
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= htmlspecialchars($gayarlar->google_api_key, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap"></script>