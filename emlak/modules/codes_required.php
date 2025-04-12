<?php
declare(strict_types=1);

error_reporting(E_ALL); // Tüm hata raporlamalarını aç
ini_set('display_errors', '1'); // Hataları ekranda göster
ini_set('log_errors', '1'); // Hataları log dosyasına yaz
ini_set('error_log', __DIR__ . '/error_log.log'); // Log dosyasının yolu

$dirs = dirname($_SERVER["SCRIPT_NAME"]);
if (substr($dirs, -1) != '/') {
    $dirs = $dirs . '/';
}

if (strstr($dirs, "admin")) {
    $dirs = rtrim($dirs, "/");
    $bl = explode("/", $dirs);
    $snbl = end($bl);
    $dirs = str_replace($snbl, "", $dirs);
}

/**
 * SSL kontrol fonksiyonu
 * @return bool
 */
function isSSL(): bool
{
    return (
        (isset($_SERVER['https']) && $_SERVER['https'] == 'on') ||
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
        (isset($_SERVER["HTTP_SSL"]) && $_SERVER["HTTP_SSL"] == "TRUE") ||
        (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == '443') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    );
}

/**
 * www kontrol fonksiyonu
 * @param string|bool $hostname
 * @return string|false
 */
function www_check($hostname = false)
{
    if (!$hostname) {
        $hostname = $_SERVER["HTTP_HOST"];
    }
    $www = substr($hostname, 0, 4);
    return $www == "www." ? $www : false;
}

define("SITE_URL", (isSSL() ? "https" : "http") . "://" . www_check() . $domain2 . $dirs);

$clear_req = $gvn->html_temizle($_SERVER["REQUEST_URI"]);
define("ORGIN_URL", (isSSL() ? "https" : "http") . "://" . www_check() . $domain2 . "/");
define("REQUEST_URL", (isSSL() ? "https" : "http") . "://" . www_check() . $domain2 . $clear_req);

/*
Görsel Boyutları
*/
$gorsel_boyutlari = [];

// Logo için ayarlar
$gorsel_boyutlari['sehirler'] = [
    'orjin_x' => 267,
    'orjin_y' => 380
];

// Logo için ayarlar
$gorsel_boyutlari['logo'] = [
    'thumb_x' => false,
    'thumb_y' => 90,
    'orjin_x' => false,
    'orjin_y' => 90
];

// Katalog için ayarlar
$gorsel_boyutlari['ekatalog'] = [
    'thumb_x' => 200,
    'thumb_y' => 283,
    'orjin_x' => false,
    'orjin_y' => false
];

// HeaderBG için ayarlar
$gorsel_boyutlari['headerbg'] = [
    'thumb_x' => 300,
    'thumb_y' => false,
    'orjin_x' => 1920,
    'orjin_y' => 350
];

// Footer BG için ayarlar
$gorsel_boyutlari['footerbg'] = [
    'thumb_x' => 1600,
    'thumb_y' => 520,
    'orjin_x' => 1600,
    'orjin_y' => 520
];

// Slider için ayarlar
$gorsel_boyutlari['slider'] = [
    'thumb_x' => 300,
    'thumb_y' => false,
    'orjin_x' => 1920,
    'orjin_y' => 700
];

// Üye Avatar için ayarlar
$gorsel_boyutlari['avatar'] = [
    'thumb_x' => 250,
    'thumb_y' => 250,
    'orjin_x' => 250,
    'orjin_y' => 250,
];

// Foto Galeri için ayarlar
$gorsel_boyutlari['foto_galeri'] = [
    'thumb_x' => 235,
    'thumb_y' => 201,
    'orjin_x' => 750,
    'orjin_y' => 562
];

// Anasayfa Bloklar için ayarlar
$gorsel_boyutlari['abloklar'] = [
    'thumb_x' => false,
    'thumb_y' => false,
    'orjin_x' => 100,
    'orjin_y' => 100
];

// Referanslar için ayarlar
$gorsel_boyutlari['referanslar'] = [
    'thumb_x' => 221,
    'thumb_y' => 150,
    'orjin_x' => false,
    'orjin_y' => false
];

// Danışmanlar için ayarlar
$gorsel_boyutlari['danismanlar'] = [
    'thumb_x' => 200,
    'thumb_y' => 150,
];

// Markalar için ayarlar
$gorsel_boyutlari['markalar'] = [
    'thumb_x' => 200,
    'thumb_y' => 150,
    'orjin_x' => false,
    'orjin_y' => false
];

// Müşteri Yorumlar için ayarlar
$gorsel_boyutlari['musteri_yorumlar'] = [
    'thumb_x' => 100,
    'thumb_y' => 100,
    'orjin_x' => false,
    'orjin_y' => false
];

// Sayfalar için ayarlar
$gorsel_boyutlari['sayfalar'] = [
    'resim1' => [
        'thumb_x' => 300,
        'thumb_y' => false,
        'orjin_x' => false,
        'orjin_y' => false
    ],
    'resim2' => [
        'thumb_x' => 300,
        'thumb_y' => false,
        'orjin_x' => 1920,
        'orjin_y' => 350
    ]
];

// Yazılar için ayarlar
$gorsel_boyutlari['yazilar'] = [
    'resim1' => [
        'thumb_x' => 300,
        'thumb_y' => 240,
        'orjin_x' => false,
        'orjin_y' => false
    ],
    'resim2' => [
        'thumb_x' => 300,
        'thumb_y' => 240,
        'orjin_x' => 1920,
        'orjin_y' => 350
    ]
];

// Haberler için ayarlar
$gorsel_boyutlari['haber_ve_duyurular'] = [
    'resim1' => [
        'thumb_x' => 300,
        'thumb_y' => 240,
        'orjin_x' => false,
        'orjin_y' => false
    ],
    'resim2' => [
        'thumb_x' => 300,
        'thumb_y' => 240,
        'orjin_x' => 1920,
        'orjin_y' => 350
    ]
];

// Hizmetler için ayarlar
$gorsel_boyutlari['hizmetler'] = [
    'resim1' => [
        'thumb_x' => 350,
        'thumb_y' => 350,
        'orjin_x' => false,
        'orjin_y' => false
    ],
    'resim2' => [
        'thumb_x' => 350,
        'thumb_y' => 350,
        'orjin_x' => 1920,
        'orjin_y' => 350
    ]
];

// Ürünler için ayarlar
$gorsel_boyutlari['urunler'] = [
    'resim1' => [
        'thumb_x' => 750,
        'thumb_y' => 562,
        'orjin_x' => false,
        'orjin_y' => false
    ],
    'resim2' => [
        'thumb_x' => 1,
        'thumb_y' => 1,
        'orjin_x' => 1920,
        'orjin_y' => 350
    ]
];

// Projeler için ayarlar
$gorsel_boyutlari['projeler'] = [
    'resim1' => [
        'thumb_x' => 320,
        'thumb_y' => 290,
        'orjin_x' => 770,
        'orjin_y' => 520
    ],
    'resim2' => [
        'thumb_x' => 10,
        'thumb_y' => 10,
        'orjin_x' => 1920,
        'orjin_y' => 350
    ]
];

// Kategoriler için ayarlar
$gorsel_boyutlari['kategoriler'] = [
    'resim1' => [
        'thumb_x' => 295,
        'thumb_y' => 143,
        'orjin_x' => false,
        'orjin_y' => false
    ],
    'resim2' => [
        'thumb_x' => 295,
        'thumb_y' => 143,
        'orjin_x' => 1920,
        'orjin_y' => 350
    ]
];