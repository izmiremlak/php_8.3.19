<?php
// Gerekli fonksiyonları içe aktarır.
include "functions.php";

// İçerik türünü XML olarak ayarlar.
header("Content-Type:xml; Charset=utf-8");

// XML başlangıcını ve urlset etiketini ekler.
echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
';

// Sayfalar tablosundan verileri çeker ve URL'leri oluşturur.
$cek = $db->query("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi=4 AND ekleme=1 AND durum=1 AND dil='" . $dil . "' ORDER BY id DESC LIMIT 50000");

while ($veri = $cek->fetch(PDO::FETCH_OBJ)) {
    $link = ($dayarlar->permalink == 'Evet') ? $veri->url . '.html' : 'index.php?p=sayfa&id=' . $veri->id;
    echo "<url>
    <loc>" . htmlspecialchars(SITE_URL . $link, ENT_QUOTES, 'UTF-8') . "</loc>
    <changefreq>always</changefreq>
    </url>\n";
}

// Diğer sayfalar tablosundan verileri çeker ve URL'leri oluşturur.
$cek = $db->query("SELECT * FROM sayfalar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND tipi!=4 AND dil='" . $dil . "' ORDER BY id DESC LIMIT 50000");

while ($veri = $cek->fetch(PDO::FETCH_OBJ)) {
    $link = ($dayarlar->permalink == 'Evet') ? $veri->url . '.html' : 'index.php?p=sayfa&id=' . $veri->id;
    echo "<url>
    <loc>" . htmlspecialchars(SITE_URL . $link, ENT_QUOTES, 'UTF-8') . "</loc>
    <changefreq>always</changefreq>
    </url>\n";
}

// Referanslar tablosundan verileri çeker ve URL'leri oluşturur.
$cek = $db->query("SELECT * FROM referanslar_501 WHERE dil='" . $dil . "' ORDER BY sira ASC");

while ($veri = $cek->fetch(PDO::FETCH_OBJ)) {
    $link = (stristr($veri->website, "http")) ? $veri->link : SITE_URL . $veri->website;
    echo "<url>
    <loc>" . htmlspecialchars(str_replace(array("&"), array("&amp;"), $link), ENT_QUOTES, 'UTF-8') . "</loc>
    <changefreq>always</changefreq>
    </url>\n";
}

// XML bitiş etiketini ekler.
echo '</urlset>';