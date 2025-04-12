<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

// functions.php zaten dahil edildiği için tekrar veritabanı bağlantısı yapmıyoruz
// $db, $hesap, $gvn ve dil() fonksiyonu admin/index.php üzerinden geliyor

// Oturum başlatma (functions.php’de zaten varsa bunu kaldırabiliriz)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı girdisini sanitize etme
function sanitizeInput(string $input): string {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// İstatistik silme işlemi
$istatistik_sil = filter_input(INPUT_GET, 'istatistik_sil', FILTER_SANITIZE_NUMBER_INT) ?? 0;
if ($hesap->tipi !== 2 && $hesap->tipi !== 0 && $istatistik_sil === 1) {
    $db->query("DELETE FROM sayfa_ge_501");
    $db->query("DELETE FROM ziyaret_ip_501");
    header("Location: index.php");
    exit;
}

// Tarih değişkenleri
$buay = date("Y-m");
$bugun = date("Y-m-d");
$buyil = date("Y");
$igun = (date("N") === '1') ? date("Y-m-d") : date("Y-m-d", strtotime("last monday"));

// Ziyaret istatistikleri
$son30gun_ziyaret = $db->query("SELECT SUM(toplam) as toplam, SUM(tekil) as toplam2 FROM sayfa_ge_501 WHERE tarih > DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetch()->toplam2 ?? 0;
$bugun_ziyaret = $db->query("SELECT toplam, tekil FROM sayfa_ge_501 WHERE tarih = '$bugun'")->fetch()->tekil ?? 0;

// Satış istatistikleri (Bu Ay ve Bu Yıl)
$st1_ay = $db->query("SELECT SUM(tutar) AS toplam FROM dopingler_group_501 WHERE durum = 1 AND tarih LIKE '%$buay%'")->fetch()->toplam ?? 0;
$st2_ay = $db->query("SELECT SUM(tutar) AS toplam FROM upaketler_501 WHERE durum = 1 AND tarih LIKE '%$buay%'")->fetch()->toplam ?? 0;
$st3_ay = $db->query("SELECT SUM(tutar) AS toplam FROM onecikan_danismanlar_501 WHERE durum = 1 AND tarih LIKE '%$buay%'")->fetch()->toplam ?? 0;
$st1_yil = $db->query("SELECT SUM(tutar) AS toplam FROM dopingler_group_501 WHERE durum = 1 AND tarih LIKE '%$buyil%'")->fetch()->toplam ?? 0;
$st2_yil = $db->query("SELECT SUM(tutar) AS toplam FROM upaketler_501 WHERE durum = 1 AND tarih LIKE '%$buyil%'")->fetch()->toplam ?? 0;
$st3_yil = $db->query("SELECT SUM(tutar) AS toplam FROM onecikan_danismanlar_501 WHERE durum = 1 AND tarih LIKE '%$buyil%'")->fetch()->toplam ?? 0;

$satis_aylik = $st1_ay + $st2_ay + $st3_ay;
$satis_yillik = $st1_yil + $st2_yil + $st3_yil;
$satis_aylik_str = explode(",", $gvn->para_str($satis_aylik))[0];
$satis_yillik_str = explode(",", $gvn->para_str($satis_yillik))[0];

// Üye istatistikleri
$uyeler_ay = $db->query("SELECT COUNT(id) AS toplam FROM hesaplar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND tipi = 0 AND olusturma_tarih LIKE '%$buay%'")->fetch()->toplam ?? 0;
$uyeler_toplam = $db->query("SELECT COUNT(id) AS toplam FROM hesaplar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND tipi = 0")->fetch()->toplam ?? 0;

// İlan istatistikleri
$ilanlar_bugun = $db->query("SELECT COUNT(id) AS toplam FROM sayfalar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND tipi = 4 AND ekleme = 1 AND (durum = 1 OR durum = 2) AND tarih LIKE '%$bugun%'")->fetch()->toplam ?? 0;
$ilanlar_toplam = $db->query("SELECT COUNT(id) AS toplam FROM sayfalar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND tipi = 4 AND ekleme = 1 AND (durum = 1 OR durum = 2)")->fetch()->toplam ?? 0;

// Son siparişler
$siparisler = ['a' => [], 'b' => []];

foreach ([
    ['table' => 'upaketler_501', 'urun' => 'Paket Üyeliği', 'birim' => 'UYELIKP_PBIRIMI', 'link' => 'upaket_duzenle'],
    ['table' => 'dopingler_group_501', 'urun' => 'İlan Doping', 'birim' => 'DOPING_PBIRIMI', 'link' => 'dopingler'],
    ['table' => 'onecikan_danismanlar_501', 'urun' => 'Danışman Doping', 'birim' => 'DONECIKAR_PBIRIMI', 'link' => 'dopingler']
] as $type) {
    $query = $db->query("SELECT id, acid, adi, tutar, durum, tarih FROM {$type['table']} ORDER BY id DESC LIMIT 7");
    while ($row = $query->fetch()) {
        $mictime = strtotime($row->tarih);
        $tarih = date("d.m.Y H:i", $mictime);
        $uye = $db->prepare("SELECT id, unvan, CONCAT_WS(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND id = ?");
        $uye->execute([$row->acid]);
        $uye_adi = ($uye->rowCount() > 0) ? (($uye_data = $uye->fetch())->unvan !== '' ? $uye_data->unvan : $uye_data->adsoyad) : "Yok";
        $urun = ($type['table'] === 'upaketler_501' ? $row->adi : $type['urun']) . " {$type['urun']}";
        $tutar = $gvn->para_str($row->tutar) . " " . dil($type['birim']);
        $durum = match ($row->durum) {
            0 => '<span style="color:red;"><strong>Onay Bekliyor</strong></span>',
            1 => '<span style="color:green;"><strong>Tamamlandı</strong></span>',
            2 => '<span style="color:black;"><strong>Reddedildi</strong></span>',
            default => ''
        };
        $link = "index.php?p={$type['link']}" . ($type['table'] === 'upaketler_501' ? "&id={$row->id}" : "");
        $durumu = $row->durum === 0 ? 'a' : 'b';
        $siparisler[$durumu][] = [
            'time' => $mictime,
            'tarih' => $tarih,
            'link' => $link,
            'uye_adi' => $uye_adi,
            'urun' => $urun,
            'tutar' => $tutar,
            'durum' => $durum,
        ];
    }
}

$siparisler1 = $siparisler['a'];
$siparisler2 = $siparisler['b'];

if (!empty($siparisler1)) {
    usort($siparisler1, fn($a, $b) => $a['time'] <=> $b['time']);
}
if (!empty($siparisler2)) {
    usort($siparisler2, fn($a, $b) => $b['time'] <=> $a['time']);
}

// Üyelik türleri
$turler = explode(",", dil("UYELIK_TURLERI"));

// Çöp resimler temizleme
try {
    $trashimg = $db->query("SELECT galeri_foto.id, galeri_foto.resim FROM galeri_foto LEFT JOIN sayfalar ON galeri_foto.sayfa_id = sayfalar.id WHERE sayfa_id != 0 AND sayfalar.id IS NULL");
    while ($row = $trashimg->fetch()) {
        @unlink("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/thumb/" . $row->resim);
        @unlink("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/" . $row->resim);
        $db->query("DELETE FROM galeri_foto WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND id = " . (int)$row->id);
    }
} catch (PDOException $e) {
    error_log("Çöp resimler temizlenirken hata: " . $e->getMessage());
}

// Çöp ilanlar temizleme
$trashadv = $db->query("SELECT id, video, baslik, tarih FROM sayfalar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND tipi = 4 AND ekleme = 0 AND tarih <= DATE_SUB(NOW(), INTERVAL 180 MINUTE)");
while ($snc = $trashadv->fetch()) {
    $db->query("DELETE FROM sayfalar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND id = " . (int)$snc->id);
    if ($snc->video !== '') {
        $nirde = "/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/videos/" . $snc->video;
        if (file_exists($nirde)) {
            unlink($nirde);
        }
    }
    $quu = $db->query("SELECT id, resim FROM galeri_foto WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND sayfa_id = " . (int)$snc->id);
    while ($row = $quu->fetch()) {
        $pinfo = pathinfo("/var/www/vhosts/turkiyeemlaksitesi.com.tr/turkiyeemlaksitesi.com.tr/uploads/" . $row->resim);
        $folder = $pinfo["dirname"] . "/";
        $ext = $pinfo["extension"];
        $fname = $pinfo["filename"];
        $bname = $pinfo["basename"];

        @unlink($folder . "thumb/" . $bname);
        @unlink($folder . $bname);
        @unlink($folder . $fname . "_original." . $ext);
    }
    $db->query("DELETE FROM galeri_foto WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND sayfa_id = " . (int)$snc->id);
}
?>

<!-- HTML Kısmı -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <div class="mini-stat clearfix bx-shadow" style="background-color:#ff9800;">
                    <a class="tooltip-bottom" data-tooltip="İlan Dopingleri, Üyelik Satışları ve Danışman Öne Çıkarma satışlarınızın tutarlarıdır." href="#" style="position:absolute;right:40px;top:5px;font-size:16px;color:white;">
                        <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                    </a>
                    <span class="mini-stat-icon"><i class="fa fa-try"></i></span>
                    <div class="mini-stat-info text-right" style="color:white;">
                        <span style="font-size:30px;" class=""><?= sanitizeInput($satis_aylik_str); ?> TL</span>
                        Bu Ay Net Satış
                    </div>
                    <div class="tiles-progress">
                        <div class="m-t-20">
                            <h5 class="text-uppercase text-white m-0">Bu Yıl <span class="pull-right"><strong><?= sanitizeInput($satis_yillik_str); ?></strong> TL</span></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="mini-stat clearfix bg-info bx-shadow">
                    <a class="tooltip-bottom" data-tooltip="Bireysel, Kurumsal ve Danışmanlar olarak toplam kullanıcı(üye) sayısıdır." href="#" style="position:absolute;right:40px;top:5px;font-size:16px;color:white;">
                        <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                    </a>
                    <span class="mini-stat-icon"><i class="fa fa-group" aria-hidden="true"></i></span>
                    <div class="mini-stat-info text-right">
                        <span style="font-size:30px;" class=""><?= sanitizeInput((string)$uyeler_ay); ?></span>
                        Bu Ay Üye Sayısı
                    </div>
                    <div class="tiles-progress">
                        <div class="m-t-20">
                            <h5 class="text-uppercase text-white m-0">Toplam Üye <span class="pull-right"><strong><?= sanitizeInput((string)$uyeler_toplam); ?></strong></span></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="mini-stat clearfix bg-purple bx-shadow">
                    <a class="tooltip-bottom" data-tooltip="Sitenizi ziyaret eden tekil kullanıcı sayısıdır." href="#" style="position:absolute;right:40px;top:5px;font-size:16px;color:white;">
                        <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                    </a>
                    <a class="tooltip-bottom" data-tooltip="Sayacı Sıfırla." title="Sıfırla" onclick="if(confirm('İstatistiği gerçekten silmek istiyor musunuz ?')){ window.location.href='index.php?istatistik_sil=1'; }" style="position:absolute;right:20px;top:5px;font-size:16px;color:white;">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>
                    <span class="mini-stat-icon"><i class="fa fa-eye" aria-hidden="true"></i></span>
                    <div class="mini-stat-info text-right">
                        <span style="font-size:30px;" class=""><?= sanitizeInput((string)$bugun_ziyaret); ?></span>
                        Bugün Tekil Ziyaret
                    </div>
                    <div class="tiles-progress">
                        <div class="m-t-20">
                            <h5 class="text-uppercase text-white m-0">Son 30 Günde <span class="pull-right"><strong><?= sanitizeInput((string)$son30gun_ziyaret); ?></strong></span></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="mini-stat clearfix bg-success bx-shadow">
                    <a class="tooltip-left" data-tooltip="Aktif/Pasif sitenize eklenen ilan miktarlarıdır." href="#" style="position:absolute;right:20px;top:5px;font-size:16px;color:white;">
                        <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                    </a>
                    <span class="mini-stat-icon"><i class="fa fa-globe" aria-hidden="true"></i></span>
                    <div class="mini-stat-info text-right">
                        <span style="font-size:30px;" class=""><?= sanitizeInput((string)$ilanlar_bugun); ?></span>
                        Bugün Eklenen İlan
                    </div>
                    <div class="tiles-progress">
                        <div class="m-t-20">
                            <h5 class="text-uppercase text-white m-0">Toplam İlan Sayısı <span class="pull-right"><strong><?= sanitizeInput((string)$ilanlar_toplam); ?></strong></span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="ion-ios7-cart"></i> Son Siparişler</h4>
                </div>
                <div class="panel-body">
                    <div class="inbox-widget nicescroll mx-box" style="overflow: hidden; outline: none;" tabindex="5000">
                        <?php foreach ($siparisler1 as $siparis) { ?>
                            <a href="<?= htmlspecialchars($siparis["link"], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="inbox-item">
                                    <p class="inbox-item-author"><strong><?= sanitizeInput($siparis["uye_adi"]); ?></strong></p>
                                    <p class="inbox-item-text"><strong>Ürün:</strong> <?= sanitizeInput($siparis["urun"]); ?> - <strong>Tutar:</strong> <?= sanitizeInput($siparis["tutar"]); ?><br>
                                        <?= $siparis["durum"]; ?></p>
                                    <p class="inbox-item-date"><?= sanitizeInput($siparis["tarih"]); ?></p>
                                </div>
                            </a>
                        <?php } ?>
                        <?php foreach ($siparisler2 as $siparis) { ?>
                            <a href="<?= htmlspecialchars($siparis["link"], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="inbox-item">
                                    <p class="inbox-item-author"><strong><?= sanitizeInput($siparis["uye_adi"]); ?></strong></p>
                                    <p class="inbox-item-text"><strong>Ürün:</strong> <?= sanitizeInput($siparis["urun"]); ?> - <strong>Tutar:</strong> <?= sanitizeInput($siparis["tutar"]); ?><br>
                                        <?= $siparis["durum"]; ?></p>
                                    <p class="inbox-item-date"><?= sanitizeInput($siparis["tarih"]); ?></p>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-globe" aria-hidden="true"></i> Onay Bekleyen İlanlar</h4>
                </div>
                <div class="panel-body">
                    <div class="inbox-widget nicescroll mx-box" style="overflow: hidden; outline: none;" tabindex="5000">
                        <?php
                        $query = $db->query("SELECT id, acid, baslik, tarih, durum FROM sayfalar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND ekleme = 1 AND tipi = 4 AND durum = 0 ORDER BY id DESC LIMIT 21");
                        if ($query->rowCount() > 0) {
                            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                                $mictime = strtotime($row->tarih);
                                $tarih = date("d.m.Y H:i", $mictime);
                                $uye = $db->prepare("SELECT id, unvan, CONCAT_WS(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND id = ?");
                                $uye->execute([$row->acid]);
                                $uye_adi = ($uye->rowCount() > 0) ? (($uye_data = $uye->fetch())->unvan !== '' ? $uye_data->unvan : $uye_data->adsoyad) : "Yok";
                                $durum = match ($row->durum) {
                                    0 => '<span style="color:red;"><strong>Onay Bekliyor</strong></span>',
                                    1 => '<span style="color:green;"><strong>Tamamlandı</strong></span>',
                                    2 => '<span style="color:black;"><strong>Reddedildi</strong></span>',
                                    default => ''
                                };
                                $link = "index.php?p=ilan_duzenle&id=" . (int)$row->id;
                        ?>
                                <a href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="inbox-item">
                                        <p class="inbox-item-author"><strong><?= sanitizeInput($uye_adi); ?></strong></p>
                                        <p class="inbox-item-text"><?= sanitizeInput($row->baslik); ?><br><?= $durum; ?></p>
                                        <p class="inbox-item-date"><?= sanitizeInput($tarih); ?></p>
                                    </div>
                                </a>
                        <?php }
                        } else { ?>
                            <center><br><br>
                                <h1 style="font-size:55px;"><i class="fa fa-check" aria-hidden="true"></i></h1>
                                <h4><strong>Tüm ilanlar onaylı durumdadır.</strong><br>Onay bekleyen ilan olması durumunda, ayrıca burada görüntülenir.</h4>
                            </center>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-group" aria-hidden="true"></i> Son Üyeler</h4>
                </div>
                <div class="panel-body">
                    <div class="inbox-widget nicescroll mx-box" style="overflow: hidden; outline: none;" tabindex="5000">
                        <?php
                        $query = $db->query("SELECT id, CONCAT_WS(' ', adi, soyadi) AS adsoyad, unvan, turu, olusturma_tarih FROM hesaplar WHERE site_id_555 = 501 AND tipi = 0 AND durum = 0 ORDER BY id DESC LIMIT 21");
                        while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                            $uye_adi = $row->unvan !== '' ? $row->unvan : $row->adsoyad;
                        ?>
                            <a href="index.php?p=uye_duzenle&id=<?= (int)$row->id; ?>">
                                <div class="inbox-item">
                                    <p class="inbox-item-author"><strong><?= sanitizeInput($uye_adi); ?></strong></p>
                                    <p class="inbox-item-text">(<?= sanitizeInput($turler[$row->turu] ?? 'Bilinmeyen Tür'); ?> Üye) - Üyelik Tarihi: <?= sanitizeInput(date("d.m.Y H:i", strtotime($row->olusturma_tarih))); ?></p>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mevcut stil kodları aynı kalabilir */
    .inbox-widget .inbox-item .inbox-item-text { font-size: 14px; color: #777; }
    [data-tooltip], .tooltip { position: relative; cursor: pointer; }
    .tooltip-bottom { color: #999; }
    .tooltip-bottom:hover { color: #000; }
    [data-tooltip]:before, [data-tooltip]:after, .tooltip:before, .tooltip:after {
        position: absolute; visibility: hidden; opacity: 0; transition: opacity .2s ease-in-out, visibility .2s ease-in-out, transform .2s cubic-bezier(0.71, 1.7, 0.77, 1.24);
        transform: translate3d(0, 0, 0); pointer-events: none;
    }
    [data-tooltip]:hover:before, [data-tooltip]:hover:after, [data-tooltip]:focus:before, [data-tooltip]:focus:after, .tooltip:hover:before, .tooltip:hover:after, .tooltip:focus:before, .tooltip:focus:after {
        visibility: visible; opacity: 1;
    }
    .tooltip:before, [data-tooltip]:before { z-index: 1001; border: 6px solid transparent; background: transparent; content: ""; }
    .tooltip:after, [data-tooltip]:after { z-index: 1000; padding: 8px; width: 160px; background-color: hsla(0, 0%, 20%, 0.9); color: #fff; content: attr(data-tooltip); font-size: 12px; line-height: 1.2; }
    [data-tooltip]:before, [data-tooltip]:after, .tooltip:before, .tooltip:after, .tooltip-top:before, .tooltip-top:after { bottom: 100%; left: 50%; }
    [data-tooltip]:before, .tooltip:before, .tooltip-top:before { margin-left: -6px; margin-bottom: -12px; border-top-color: hsla(0, 0%, 20%, 0.9); }
    [data-tooltip]:after, .tooltip:after, .tooltip-top:after { margin-left: -80px; }
    [data-tooltip]:hover:before, [data-tooltip]:hover:after, [data-tooltip]:focus:before, [data-tooltip]:focus:after, .tooltip:hover:before, .tooltip:hover:after, .tooltip:focus:before, .tooltip:focus:after, .tooltip-top:hover:before, .tooltip-top:hover:after, .tooltip-top:focus:before, .tooltip-top:focus:after {
        transform: translateY(-12px);
    }
    .tooltip-left:before, .tooltip-left:after { right: 100%; bottom: 50%; left: auto; }
    .tooltip-left:before { margin-left: 0; margin-right: -12px; margin-bottom: 0; border-top-color: transparent; border-left-color: hsla(0, 0%, 20%, 0.9); }
    .tooltip-left:hover:before, .tooltip-left:hover:after, .tooltip-left:focus:before, .tooltip-left:focus:after { transform: translateX(-12px); }
    .tooltip-bottom:before, .tooltip-bottom:after { top: 100%; bottom: auto; left: 50%; }
    .tooltip-bottom:before { margin-top: -12px; margin-bottom: 0; border-top-color: transparent; border-bottom-color: hsla(0, 0%, 20%, 0.9); }
    .tooltip-bottom:hover:before, .tooltip-bottom:hover:after, .tooltip-bottom:focus:before, .tooltip-bottom:focus:after { transform: translateY(12px); }
    .tooltip-right:before, .tooltip-right:after { bottom: 50%; left: 100%; }
    .tooltip-right:before { margin-bottom: 0; margin-left: -12px; border-top-color: transparent; border-right-color: hsla(0, 0%, 20%, 0.9); }
    .tooltip-right:hover:before, .tooltip-right:hover:after, .tooltip-right:focus:before, .tooltip-right:focus:after { transform: translateX(12px); }
    .tooltip-left:before, .tooltip-right:before { top: 3px; }
    .tooltip-left:after, .tooltip-right:after { margin-left: 0; margin-bottom: -16px; }
</style>

<script>
    var resizefunc = [];
</script>
<script src="assets/js/admin.min.js"></script>
<script src="assets/vendor/moment/moment.js"></script>
<script src="assets/vendor/waypoints/lib/jquery.waypoints.js"></script>
<script src="assets/plugins/counterup/jquery.counterup.min.js"></script>
<script src="assets/vendor/sweetalert/dist/sweetalert.min.js"></script>
<script src="assets/plugins/flot-chart/jquery.flot.js"></script>
<script src="assets/plugins/flot-chart/jquery.flot.time.js"></script>
<script src="assets/plugins/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="assets/plugins/flot-chart/jquery.flot.resize.js"></script>
<script src="assets/plugins/flot-chart/jquery.flot.pie.js"></script>
<script src="assets/plugins/flot-chart/jquery.flot.selection.js"></script>
<script src="assets/plugins/flot-chart/jquery.flot.stack.js"></script>
<script src="assets/plugins/flot-chart/jquery.flot.crosshair.js"></script>
<script src="assets/pages/jquery.todo.js"></script>
<script src="assets/pages/jquery.chat.js"></script>
<script src="assets/pages/jquery.dashboard.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.counter').counterUp({
            delay: 100,
            time: 1200
        });
    });
</script>