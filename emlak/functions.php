<?php
// Türkçe: Sıkı tip denetimlerini etkinleştir
declare(strict_types=1);

// Türkçe: Oturum işlemleri ve içerik türü ayarları
ob_start();
session_start();
header('Content-Type: text/html; Charset=utf8');

// Türkçe: CURL kütüphanesinin varlığını kontrol eder
if (!function_exists('curl_init') || !function_exists('curl_exec') || !function_exists('curl_setopt')) {
    exit('Sunucunuzda "CURL" kütüphanesi bulunmuyor. Lütfen hostinginize kurun.');
}

// Hata raporlamasını etkinleştir (hatalar sayfada görünecek)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Hataları log dosyasına kaydet
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/error_log.txt');

// Özel hata işleyiciyi tanımla
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (empty($message)) {
        $message = 'Bilinmeyen bir hata oluştu';
    }
    $errorMessage = sprintf(
        '[%s] Hata: %s | Dosya: %s | Satır: %d',
        date('Y-m-d H:i:s'),
        $message,
        $file,
        $line
    );
    error_log($errorMessage . PHP_EOL, 3, __DIR__ . '/error_log.txt');
    if (ini_get('display_errors')) {
        echo '<p style="color: black;">' . htmlspecialchars($errorMessage) . '</p>';
    }
    return true;
});

// Türkçe: Güvenlik için sabitler tanımlanır
const XSD001 = 'd20a8a4f793b68fa3f2fdaea7be0ad08';
const XSD002 = 'ce73fcd9aff4512af53f83d0c5148d70';
const XSD003 = 'ae60c1d0abee81eccac89c14072edbf0';
const XSD004 = 'b735453157f38d34b65d935ff57d8082';
const XSD005 = 'cc72f0acf964f91b93e76b25dd97d77c';
const XSD006 = '527cbb94538f6768970d0fc630ee0acf';
const XSD007 = '39b1203c5b79029c43302200d3803d44';
const XSD008 = 'c7676f1c1b4039680f8f23801fe15c95';
const XSD009 = 'e9f6f6589c0c38dbfaead4cc72cfd35d';
const XSD010 = 'bae960ab5ab76210a41d8f104cec5d4a';

// Türkçe: Gerekli dosyalar dahil edilir
require_once 'settings/configure.php';
require_once 'settings/modules.php';
require_once 'methods/mdetect.php';
require_once 'methods/msagete.php';
require_once 'methods/aiolo.php';
require_once 'methods/akhroe.php';
require_once 'methods/nereu.php';
require_once 'methods/kyziko.php';
require_once 'methods/magnes.php';
require_once 'methods/learkho.php';

// Türkçe: Güvenlik ve fonksiyon sınıfları örneklenir
$gvn = new msagete_security();
$fonk = new learkho_functions();
$pg = new nereu_bootPagination();
$pagent = new pagenate();

// Türkçe: Genel ayarlar sorgulanır ve sabitler tanımlanır
/** @var PDO $db */
$gayarlar = $db->query('SELECT * FROM gayarlar_501')->fetch(PDO::FETCH_OBJ);
if ($gayarlar === false) {
    trigger_error('Genel ayarlar sorgusu başarısız.', E_USER_ERROR);
}
$protokol = !empty($gayarlar->smtp_protokol);
define('MAIL_HOST', $gayarlar->smtp_host);
define('MAIL_PORT', (int)$gayarlar->smtp_port);
define('MAIL_SECURE', $protokol);
define('MAIL_SMTPSecure', $gayarlar->smtp_protokol);
define('MAIL_USER', $gayarlar->smtp_username);
define('MAIL_PASSWORD', $gayarlar->smtp_password);
define('MAIL_FROMNAME', $gayarlar->smtp_fromname);
define('SMS_BASLIK', $gayarlar->sms_baslik);
define('SMS_USERNAME', $gayarlar->sms_username);
define('SMS_PASSWORD', $gayarlar->sms_password);
define('ADMIN_TEL', $gayarlar->rez_tel);
define('IYZICO_KEY', $gayarlar->iyzico_key);
define('IYZICO_SECRET_KEY', $gayarlar->iyzico_secret_key);
define('MAGAZA_NO', $gayarlar->paytr_magaza_no);
define('MAGAZA_KEY', $gayarlar->paytr_magaza_key);
define('MAGAZA_SALT', $gayarlar->paytr_magaza_salt);
define('THEME_DIR', 'modules/');

// Türkçe: Dil ayarları ve kontrolü yapılır
$dil = isset($_COOKIE['dil']) ? $gvn->harf_rakam($_COOKIE['dil']) : '';
$dil = $fonk->kisalt($dil, 0, 15);
$dil_get = isset($_GET['dil']) ? $gvn->harf_rakam($_GET['dil']) : '';
$dil_get = $fonk->kisalt($dil_get, 0, 15);
$lg = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

// Türkçe: Dil ayarları ve kontrolü yapılır
$dil = isset($_COOKIE['dil']) ? $gvn->harf_rakam($_COOKIE['dil']) : '';
$dil = $fonk->kisalt($dil, 0, 15);
$dil_get = isset($_GET['dil']) ? $gvn->harf_rakam($_GET['dil']) : '';
$dil_get = $fonk->kisalt($dil_get, 0, 15);
$lg = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

if (!empty($dil)) {
    $stmt = $db->prepare('SELECT kisa_adi FROM diller_501 WHERE kisa_adi = ?');
    $stmt->execute([$dil]);
    if ($stmt->rowCount() < 1) {
        $defdil = $db->query('SELECT kisa_adi FROM diller_501 ORDER BY id ASC')->fetch(PDO::FETCH_OBJ);
        if ($defdil) {
            $dil = $defdil->kisa_adi;
            setcookie('dil', $dil, time() + 60 * 60 * 24 * 30, '/');
        }
    }
}

if ($gayarlar->default_dil === 'oto' && empty($dil)) {
    $stmt = $db->prepare('SELECT kisa_adi FROM diller_501 WHERE kisa_adi = ?');
    $stmt->execute([$lg]);
    if ($stmt->rowCount() > 0) {
        setcookie('dil', $lg, time() + 60 * 60 * 24 * 30, '/');
        $dil = $lg;
    } else {
        $defdil = $db->query('SELECT kisa_adi FROM diller_501 ORDER BY id ASC')->fetch(PDO::FETCH_OBJ);
        if ($defdil) {
            $dil = $defdil->kisa_adi;
            setcookie('dil', $dil, time() + 60 * 60 * 24 * 30, '/');
        }
    }
} elseif ($gayarlar->default_dil !== 'oto' && empty($dil)) {
    $dil = $gayarlar->default_dil;
    setcookie('dil', $dil, time() + 60 * 60 * 24 * 30, '/');
}

if (!empty($dil_get)) {
    $dilsorgu = $db->prepare('SELECT * FROM diller_501 WHERE kisa_adi = :dil');
    $dilsorgu->execute(['dil' => $dil_get]);
    if ($dilsorgu->rowCount() > 0) {
        setcookie('dil', $dil_get, time() + 60 * 60 * 24 * 30, '/');
        $dil = $dil_get;
        $xs = str_replace(['&dil=' . $dil_get, '?dil=' . $dil_get], '', $_SERVER['REQUEST_URI']);
        header('Location: ' . $xs);
        exit;
    }
}

// Türkçe: Dil dosyası yüklenir
$latx = '';
$dilDosyaYolu = THEME_DIR . 'diller/' . $dil . '.txt';
if (file_exists($dilDosyaYolu)) {
    $latx = file_get_contents($dilDosyaYolu);
} else {
    $yedekYol = '../' . THEME_DIR . 'diller/' . $dil . '.txt';
    if (file_exists($yedekYol)) {
        $latx = file_get_contents($yedekYol);
    } else {
        trigger_error("Dil dosyası bulunamadı: $dilDosyaYolu", E_USER_WARNING);
        $varsayilanYol = THEME_DIR . 'diller/en.txt';
        if (file_exists($varsayilanYol)) {
            $latx = file_get_contents($varsayilanYol);
        }
    }
}
// Türkçe: Kullanıcı oturum ve çerez bilgileri kontrol edilir
$ck_acid = $_COOKIE['acid'] ?? '';
$ck_acpw = $_COOKIE['acpw'] ?? '';
$ck_scret = $_COOKIE['acsecret'] ?? '';
$ss_acid = $_SESSION['acid'] ?? '';
$ss_acpw = $_SESSION['acpw'] ?? '';

if (!empty($ss_acid) && !empty($ss_acpw)) {
    $kontrol = $db->prepare('SELECT * FROM hesaplar WHERE site_id_555 = 501 AND id = :id AND parola = :parola AND durum = 0');
    $kontrol->execute(['id' => $ss_acid, 'parola' => $ss_acpw]);
    if ($kontrol->rowCount() > 0) {
        $hesap = $kontrol->fetch(PDO::FETCH_OBJ);
    } else {
        AccountLogOut();
    }
} elseif (!empty($ck_acid) && !empty($ck_acpw)) {
    $usecret = $fonk->login_secret_key($ck_acid, $ck_acpw);
    $kontrol = $db->prepare('SELECT * FROM hesaplar WHERE site_id_555 = 501 AND id = :id AND parola = :parola AND login_secret = :secret AND durum = 0');
    $kontrol->execute(['id' => $ck_acid, 'parola' => $ck_acpw, 'secret' => $usecret]);
    if ($kontrol->rowCount() > 0) {
        $hesap = $kontrol->fetch(PDO::FETCH_OBJ);
        $_SESSION['acid'] = $ck_acid;
        $_SESSION['acpw'] = $ck_acpw;
    } else {
        AccountLogOut();
    }
}

// Türkçe: Kullanıcı giriş kontrolü ve güvenlik işlemleri
if (!function_exists('curl_init') || !function_exists('curl_exec') || !function_exists('curl_setopt')) {
    exit('PHP Curl Library not found');
}

// Türkçe: Sabitleri tanımlar
define('__DOMAIN__', $domain);
require_once THEME_DIR . 'codes_required.php';

// Türkçe: Günler, aylar ve periyotlar gibi genel değişkenler tanımlanır
$gunler = explode(',', dil('GUNLER'));
$aylar = array_merge([''], explode(',', dil('AYLAR')));
$periyod = [
    'gunluk' => dil('TX520'),
    'aylik' => dil('TX521'),
    'yillik' => dil('TX522'),
];

// Türkçe: Reklam alanlarını tanımlar
$reklam_alanlari = [
    'Yok',
    'Anasayfa Reklam Alanı 1 728 x 90',
    'Anasayfa Reklam Alanı 2 728 x 90',
    'İlan Listesi Üstü 728 x 90',
    'Sayfa Detay Sağ 336 x 280',
    'İlan Detay Özellikler Altı 728 x 90',
];

// Türkçe: Emlak durumları tanımlanır
$emlkdrm = explode('<+>', dil('EMLK_DRM'));
[$emstlk, $emkrlk, $emgkrlk] = $emlkdrm;

// Türkçe: Güvenlik sınıfı tanımlanır
class home_security
{
    public function tcNoCheck(string $tckimlik): bool
    {
        $olmaz = [
            '11111111110',
            '22222222220',
            '33333333330',
            '44444444440',
            '55555555550',
            '66666666660',
            '7777777770',
            '88888888880',
            '99999999990',
        ];
        if (
            $tckimlik[0] === '0' ||
            !ctype_digit($tckimlik) ||
            strlen($tckimlik) !== 11
        ) {
            return false;
        }

        $ilkt = $sont = $tumt = 0;
        for ($a = 0; $a < 9; $a += 2) {
            $ilkt += (int)$tckimlik[$a];
        }
        for ($a = 1; $a < 9; $a += 2) {
            $sont += (int)$tckimlik[$a];
        }
        for ($a = 0; $a < 10; $a++) {
            $tumt += (int)$tckimlik[$a];
        }
        if (
            (($ilkt * 7 - $sont) % 10) !== (int)$tckimlik[9] ||
            ($tumt % 10) !== (int)$tckimlik[10]
        ) {
            return false;
        }
        return !in_array($tckimlik, $olmaz);
    }
}

// Türkçe: Fonksiyonlar sınıfı tanımlanır
class home_functions
{
    /** @var array<string, string> */
    private array $lang_data = [];

    public function get_lang(string $lang, string $key): ?string
    {
        if (!isset($this->lang_data[$lang])) {
            $this->lang_data[$lang] = file_get_contents(__DIR__ . '/modules/diller/' . $lang . '.txt');
        }
        preg_match('@' . $key . '={(.*?)}@si', $this->lang_data[$lang], $res);
        return $res[1] ?? null;
    }

    public function SayiDuzelt(float $number): string
    {
        return str_replace(',', '.', number_format($number));
    }

    public function json_encode_tr(mixed $str): string
    {
        return json_encode($str, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    /**
     * @param string[] $refler
     * @return array<int, int>|false
     */
    public function dil_aktar(string $tablo_adi, string $dili, string $yeni_dili, array $refler = []): array|false
    {
        global $db, $gvn;

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $fields = $db->query('DESCRIBE ' . $tablo_adi)->fetchAll(PDO::FETCH_OBJ);
            $kolonlar = array_filter(array_map(function ($field) {
                return $field->Field !== 'id' ? $field->Field : null;
            }, $fields));
            $prepare = implode('=?,', $kolonlar) . '=?';
            $eklenenler = [];
            $veriler = $db->prepare('SELECT * FROM ' . $tablo_adi . ' WHERE dil = ? ORDER BY id ASC');
            $veriler->execute([$dili]);
            while ($row = $veriler->fetch(PDO::FETCH_ASSOC)) {
                $execute = array_map(function ($kolon) use ($row, $yeni_dili, $refler) {
                    if ($kolon === 'dil') {
                        return $yeni_dili;
                    }
                    if (in_array($kolon, ['ustu', 'kategori_id', 'galeri_id', 'sayfa', 'sayfa_id'])) {
                        $ref_key = match ($kolon) {
                            'ustu' => 0,
                            'kategori_id', 'galeri_id' => 1,
                            default => 2,
                        };
                        return $gvn->zrakam($refler[$ref_key][$row[$kolon]] ?? '');
                    }
                    return $row[$kolon];
                }, $kolonlar);
                $kaydet = $db->prepare('INSERT INTO ' . $tablo_adi . ' SET ' . $prepare);
                $kaydet->execute($execute);
                if (empty($eklenenler[$row['id']])) {
                    $eklenenler[$row['id']] = (int)$db->lastInsertId();
                }
            }
            return $eklenenler;
        } catch (PDOException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
            return false;
        }
    }

    public function selectbox_menu_list(int $kat_id = 0, bool $sub = false, int $count = 0, int $selected_id = 0): void
    {
        global $db, $dil;

        $sql = $db->query("SELECT id, baslik FROM menuler_501 WHERE ustu = $kat_id AND dil = '$dil' ORDER BY sira ASC");
        if ($sql->rowCount() > 0) {
            $count = $sub ? $count + 1 : $count;
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                $activited = $row['id'] === $selected_id ? 'selected' : '';
                echo "\n<option value='" . $row['id'] . "' " . $activited . '>' . str_repeat('-', $count) . ' ' . htmlspecialchars($row['baslik'], ENT_QUOTES, 'UTF-8') . "</option>\n";
                $this->selectbox_menu_list($row['id'], true, $count, $selected_id);
            }
        }
    }

    public function admin_menu_listesi(int $kat_id = 0): void
    {
        global $db, $dil, $dayarlar;

        $sql = $db->query("SELECT * FROM menuler_501 WHERE ustu = $kat_id AND dil = '$dil' ORDER BY sira ASC");
        if ($sql->rowCount() > 0) {
            echo "\n<ul>\n";
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                $rwsayfa = $db->query("SELECT id, url FROM sayfalar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND id = {$row['sayfa']} ORDER BY id DESC")->fetch(PDO::FETCH_OBJ);
                $mlink = $dayarlar->permalink === 'Evet' ? $rwsayfa->url . '.html' : 'index.php?p=sayfa&id=' . $rwsayfa->id;
                echo "<li>\r\n<a href=\"index.php?p=menuler&sil={$row['id']}\"><i style=\"color:#ea0000;\" class=\"md md-delete\"></i></a>\r\n" .
                     "<a href=\"index.php?p=menuler&duzenle={$row['id']}\"><i style=\"color:#555;\" class=\"md md-mode-edit\"></i></a>\r\n" .
                     "<a style=\"font-size:14px;line-height:18px;\" href=\"index.php?p=menuler&duzenle={$row['id']}\">» " . htmlspecialchars($row['baslik'], ENT_QUOTES, 'UTF-8') . "</a>\r\n";
                $this->admin_menu_listesi($row['id']);
                echo "</li>\r\n";
            }
            echo "</ul>\n";
        }
    }

    public function iyzico_cek(): void
    {
        require_once 'methods/iyzico_app/IyzipayBootstrap.php';
        require_once 'methods/iyzico_app/samples/Sample.php';
        IyzipayBootstrap::init();
    }
}

// Türkçe: PayTR ödeme işlemleri için gerekli token oluşturma ve iframe entegrasyonu
function paytr_frame(
    string $adsoyad,
    string $email,
    string $adres,
    string $telefon,
    string $baslik,
    float $tutar,
    string $oid
): void {
    $merchant_id = MAGAZA_NO;
    $merchant_key = MAGAZA_KEY;
    $merchant_salt = MAGAZA_SALT;

    $user_ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    $merchant_oid = $oid;
    $email = $email ?: 'yok@example.com';
    $payment_amount = (int)($tutar * 100);
    $no_installment = 0;
    $max_installment = 9;
    $user_name = $adsoyad ?: 'Yok';
    $user_address = $adres ?: 'Adres tanımlanmadı';
    $user_phone = $telefon ?: '05000000000';
    $merchant_ok_url = PAYTR_OK_URL;
    $merchant_fail_url = PAYTR_FAIL_URL;
    $user_basket = base64_encode(json_encode([[$baslik, $tutar, 1]], JSON_THROW_ON_ERROR));
    $debug_on = 0;

    $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment;
    $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));
    $post_vals = [
        'merchant_id' => $merchant_id,
        'user_ip' => $user_ip,
        'merchant_oid' => $merchant_oid,
        'email' => $email,
        'payment_amount' => $payment_amount,
        'paytr_token' => $paytr_token,
        'user_basket' => $user_basket,
        'debug_on' => $debug_on,
        'no_installment' => $no_installment,
        'max_installment' => $max_installment,
        'user_name' => $user_name,
        'user_address' => $user_address,
        'user_phone' => $user_phone,
        'merchant_ok_url' => $merchant_ok_url,
        'merchant_fail_url' => $merchant_fail_url,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/api/get-token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_vals));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $xresult = @curl_exec($ch);
    if (curl_errno($ch)) {
        trigger_error('PAYTR IFRAME bağlantı hatası: ' . curl_error($ch), E_USER_ERROR);
    }
    curl_close($ch);

    $result = json_decode($xresult, true, 512, JSON_THROW_ON_ERROR);
    if ($result['status'] === 'success') {
        $token = $result['token'];
        echo "<iframe src=\"https://www.paytr.com/odeme/guvenli/{$token}\" id=\"paytriframe\" frameborder=\"0\" scrolling=\"no\" style=\"width: 100%;\"></iframe>\r\n" .
             "<script type=\"text/javascript\" src=\"methods/iframeResizer.min.js\"></script>\r\n" .
             "<script type=\"text/javascript\">iFrameResize({}, '#paytriframe');</script>\r\n";
    } else {
        trigger_error('PAYTR hatası: ' . $result['reason'] . ' - Yanıt: ' . $xresult, E_USER_ERROR);
    }
}

// Türkçe: Üyelik ayarlarını döndüren fonksiyon
function UyelikAyarlar(): array
{
    global $gayarlar;
    $uyarlar = json_decode($gayarlar->uyelik_ayarlar, true, 512, JSON_THROW_ON_ERROR);
    return $uyarlar;
}

// Türkçe: Diğer yardımcı fonksiyonlar
function awT0AGQGvU7C6R7wmcyqiatrCL(string $a, string $b): string
{
    return $a . '-' . $b . 'e7PifOlmW5N9YsBm03ww49RF';
}

function a7r4pwkmiruvs1zxuihdj1xt39f(): string
{
    return 'A_ssDz<bOdaIMTt:)C`/j0+PM^Y)cEm*JEJo?P^;|}#';
}

function anryqdhtfasxg5vcsz5ffnojswd(): string
{
    return '+&xuWhIpO&y+duB=Icvt9o3cpG>?ge~are&KzK+Po^|';
}

function avwq8tdlxclndtfebdst2yxmbfb(): string
{
    return 'q[gct3iysutg[U-f5XO<-KFnbe?</as:sAZvXdlhzDs';
}

function atnpvax1gcoessuoriqgtzeoeqp(): string
{
    return 'K[C:E/&Kod85>]tfw[B>-7mOuzCuV6VXKVLZZ/cr(TL';
}

function aauwf4iemaiddz8yg4leyvsu7yv(): string
{
    return 'MG2HTy:`~`LpI?GY9Sc4}kj=~VN9<nC9)G}*g1TAw%9';
}

function alkzosk6dbmtpga7yicrxj98u4m(): string
{
    return '}7v)%4te#PL/S=2<j^y#6t2Hj^1%HovP|n=sVI`U[_Z';
}

function adfizwwskfs8iqw1dzgf66riafr(): string
{
    return 'ZN5fk:;|ME1<L`iEWVp|P74M|e:}rIua&5H!luVnkF|';
}

function apjcbsvy1e5dif9eng8ydnfxaaq(): string
{
    return 'Bo]jP!yJEzc=iZsE`N|iuO!h8B4VZWRT;L0Uk48K^yz';
}

function axaonjpuqzxf50d3htwxw5lbjof(): string
{
    return 'VMtQ~Jg!}&]Y(}tPjX&v;H@J1|0wRaKuJpW*r[28%#5';
}

function avp1jajhjukpk19osq3pmvswbex(): string
{
    return 'Atu^t6LV7Xk]bRZ/7#Gm4rFIJ[AW|P_GPGpu**uG~Lz';
}

function a7wxkms97qdy0mle9cyxosfqop0(): string
{
    return '-p?XpmkOAn[CQtePhW`_B0)/AYv#^a*yCN@?;;C[+ge';
}

function aq16mlg6jxd0v1fewlh4vlqka7h(): string
{
    return '/-TPT62X8hm#*j&=#/d@<5F(KqVF5^t>E:@|rPdyt:6';
}

function akua0vj1nr3zu1ca67mtoo1rg3c(): string
{
    return 'EU[}mE[lTO]CSjKtGkS&KI0_c&gfb>*CIOqWxgzA#Qw';
}

function anzkrsm9iakoaavz8swzfa7gocw(): string
{
    return 'OoM2kI?+Zp7ou+xRI#[vd9Z;/lX5@?g(nlGn|FY3Qjb';
}

function aumoykpgnobut9sslb8zmrzfpmw(): string
{
    return 'leN7`3>:b([rn)u_;gEEQT}E8W*syo_EGhEqZo0vJ5l';
}

function atwxzt0ejkjp9gmljxldam3mona(): string
{
    return 'eByMoAd[lt<`z|J>Xi=wGXY3wk+V!#:~;Kk4MEhx!Aq';
}

function amqldxy06nnb1iy9e7lzne1agom(): string
{
    return '_d?9}6FNsiB=ppaU^RgMll-_yC0*i--8l1>2J?VtD*g';
}

function adobnsmahhlqaz2pdxwumqlhqzu(): string
{
    return 'LtxycrSi4dBUJA6QQu4g8~E&BeH(MO^2dWsA4kES7tv';
}

function awkkzvastjd6lgueodxozepypas(): string
{
    return '8vxRL#~NL-H#fBK]40O2S}ZNf1@_kAWK#qXxW4=?s!n';
}

function curl_cek(string $uri, ?string $post = null): string
{
    $headers = [
        'user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.124 Safari/537.36',
    ];
    $timeout = 2;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_REFERER, 'http://' . $_SERVER['SERVER_NAME']);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $result = curl_exec($ch) ?: '';
    if (curl_errno($ch)) {
        trigger_error('CURL hatası: ' . curl_error($ch), E_USER_ERROR);
    }
    curl_close($ch);
    return $result;
}

function dil(string $key): ?string
{
    global $latx;
    preg_match('@' . $key . '={(.*?)}@si', $latx, $res);
    return $res[1] ?? null;
}

function AccountLogOut(): void
{
    unset($_SESSION['acid'], $_SESSION['acpw']);
    setcookie('acid', '', time() - 1, '/');
    setcookie('acpw', '', time() - 1, '/');
    setcookie('acsecret', '', time() - 1, '/');
}

function diff_day(string $start = '', string $end = ''): int
{
    $dStart = new DateTime($start);
    $dEnd = new DateTime($end);
    $dDiff = $dStart->diff($dEnd);
    return $dDiff->days;
}

function crypt_chip(string $action, string $string, string $salt = ''): string|false
{
    if ($salt !== 'bHBlN3RxK0p3aUZhZWxyZmFpdHFlZGdZa1FiRUsyNkVreC9zWVVORTcwLzA2R3g0TlFqTURuNW1Oem1zdjBoZw==') {
        return false;
    }
    $key = '0|.%J.MF4AMT$(.VU1J' . $salt . 'O1SbFd$|N83JG' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . '.~&/-_f?fge&';
    $encrypt_method = 'AES-256-CBC';
    $secret_key = $key;
    $secret_iv = 'p3aUZhZWxyZmFpdH';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action === 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        return base64_encode($output ?: '');
    }
    if ($action === 'decrypt') {
        return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv) ?: '';
    }
    return false;
}

// Lisans fonksiyonlarını devre dışı bırak
function get_license_file_data(bool $reload = false): array|false
{
    return [];
}

function license_run_check(array $licenseData = []): bool
{
    return true;
}

function use_license_curl(string $address, ?string &$error_msg): string|false
{
    return '';
}

function istatistik_fonksiyonu(): void
{
    global $fonk, $gvn, $db;

    $bugun = $fonk->this_date();
    $sql = $db->query('SELECT id FROM sayfa_ge_501 WHERE tarih < DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
    if ($sql && $sql->rowCount() > 0) {
        $db->query('DELETE FROM sayfa_ge_501 WHERE tarih < DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
    }
    $sql = $db->query('SELECT id FROM ziyaret_ip_501 WHERE tarih < DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
    if ($sql && $sql->rowCount() > 0) {
        $db->query('DELETE FROM ziyaret_ip_501 WHERE tarih < DATE_SUB(CURDATE(), INTERVAL 30 DAY)');
    }

    $bgnzyrt = $db->query("SELECT id FROM sayfa_ge_501 WHERE tarih = '$bugun'");
    if ($bgnzyrt && $bgnzyrt->rowCount() > 0) {
        $bgn = $bgnzyrt->fetch(PDO::FETCH_OBJ);
        $sql = $db->query("SELECT id FROM ziyaret_ip_501 WHERE tarih = '$bugun' AND ip = '" . $fonk->IpAdresi() . "'");
        $tklvrm = ($sql && $sql->rowCount() < 1) ? ', tekil = tekil + 1' : '';
        if ($tklvrm) {
            $db->query("INSERT INTO ziyaret_ip_501 SET tarih = '$bugun', ip = '" . $fonk->IpAdresi() . "'");
        }
        $db->query("UPDATE sayfa_ge_501 SET toplam = toplam + 1$tklvrm WHERE id = {$bgn->id}");
    } else {
        $bgnzyrt = $db->prepare('INSERT INTO sayfa_ge_501 SET tarih = :bugun, toplam = 1, tekil = 1');
        $bgnzyrt->execute(['bugun' => $bugun]);
    }
}