<?php

if (!defined("SERVER_HOST")) {
    exit;
}

/**
 * learkho_functions Class
 * 
 * Bu sınıf, çeşitli yardımcı fonksiyonları içerir.
 * PHP 8.3.17 özelliklerini kullanarak yeniden düzenlenmiştir.
 */
class learkho_functions extends home_functions
{
    public $loaded_sms_gonder = false;
    public $turkce_tarih = [
        "January" => "Ocak", "February" => "Şubat", "March" => "Mart", "April" => "Nisan", 
        "May" => "Mayıs", "June" => "Haziran", "July" => "Temmuz", "August" => "Ağustos", 
        "September" => "Eylül", "October" => "Ekim", "November" => "Kasım", "December" => "Aralık"
    ];
    public $turkce_tarih_kisa = [
        "January" => "Oca", "February" => "Şub", "March" => "Mar", "April" => "Nis", 
        "May" => "May", "June" => "Haz", "July" => "Tem", "August" => "Ağu", 
        "September" => "Eyl", "October" => "Eki", "November" => "Kas", "December" => "Ara"
    ];

    // Tüm metodlar bu sınıfın içinde olacak
    // Örneğin, bildirim_gonder metodu:
    public function bildirim_gonder(array $ydegiskenler = [], ?string $tag = null, string $uemail = "", string $utelefon = ""): bool
    {
        global $db, $dayarlar, $gayarlar, $domain2;
        $ydegiskenler[] = SITE_URL . "uploads/thumb/" . $gayarlar->logo;
        $ydegiskenler[] = $domain2;
        $sablon = $db->query("SELECT * FROM mail_sablonlar_501 WHERE tag='" . $tag . "' AND dil='" . $dayarlar->dil . "' ")->fetch(PDO::FETCH_OBJ);
        $text = $sablon->icerik;
        $text2 = $sablon->icerik2;
        $text3 = strip_tags(str_replace(["<br />", "<br/>", "<br>"], ["\n"], $sablon->icerik3));
        $text4 = strip_tags(str_replace(["<br />", "<br/>", "<br>"], ["\n"], $sablon->icerik4));
        $degiskenler = rtrim($sablon->degiskenler);
        $degiskenler = explode(",", $degiskenler);
        $nwdegiskenler = array_map(fn($degisken) => "{" . $degisken . "}", $degiskenler);

        $text = str_replace($nwdegiskenler, $ydegiskenler, $text);
        $text2 = str_replace($nwdegiskenler, $ydegiskenler, $text2);
        $text3 = str_replace($nwdegiskenler, $ydegiskenler, $text3);
        $text4 = str_replace($nwdegiskenler, $ydegiskenler, $text4);

        $genel_yemails = stristr($dayarlar->yemails, ",") ? explode(",", $dayarlar->yemails) : $dayarlar->yemails;
        $sablon_yemails = stristr($sablon->yemails, ",") ? explode(",", $sablon->yemails) : $sablon->yemails;
        $genel_yphones = stristr($dayarlar->yphones, ",") ? explode(",", $dayarlar->yphones) : $dayarlar->yphones;
        $sablon_yphones = stristr($sablon->yphones, ",") ? explode(",", $sablon->yphones) : $sablon->yphones;
        $yemails = $sablon->yemails == "" ? $genel_yemails : $sablon_yemails;
        $yphones = $sablon->yphones == "" ? $genel_yphones : $sablon_yphones;

        if ($sablon->ubildirim == 1) {
            $xgonder = $this->mail_gonder($sablon->konu, $uemail, $text);
        }
        if ($sablon->abildirim == 1) {
            if (is_array($yemails)) {
                foreach ($yemails as $nmail) {
                    if ($nmail != "") {
                        $nmail = trim($nmail);
                        $agonder = $this->mail_gonder($sablon->konu2, $nmail, $text2);
                    }
                }
            } else {
                $agonder = $this->mail_gonder($sablon->konu2, $yemails, $text2);
            }
        }
        if ($sablon->sbildirim == 1 && $utelefon != "" && $GLOBALS["gayarlar"]->sms_username != "") {
            $usgonder = $this->sms_gonder($utelefon, $text3);
        }
        if ($sablon->ysbildirim == 1 && $GLOBALS["gayarlar"]->sms_username != "") {
            if (is_array($yphones)) {
                foreach ($yphones as $nphone) {
                    if ($nphone != "") {
                        $nphone = trim($nphone);
                        $ysgonder = $this->sms_gonder($nphone, $text4);
                    }
                }
            } else {
                $ysgonder = $this->sms_gonder($yphones, $text4);
            }
        }
        return true;
    }
	
    /**
     * Menü listesini oluşturur.
     *
     * @param int $kat_id
     */
    public function menu_listesi(int $kat_id = 0): void
    {
        global $db, $dil;

        // SQL enjeksiyonunu önlemek için prepared statement kullan
        $stmt = $db->prepare("SELECT * FROM menuler_501 WHERE ustu = :kat_id AND dil = :dil ORDER BY sira ASC");
        $stmt->execute(['kat_id' => $kat_id, 'dil' => $dil]);

        if ($stmt->rowCount() > 0) {
            $ustaktif = false; // Değişkeni önceden tanımla
            if ($kat_id != 0) {
                $ustStmt = $db->prepare("SELECT ustu FROM menuler_501 WHERE id = :kat_id");
                $ustStmt->execute(['kat_id' => $kat_id]);
                $ustne = $ustStmt->fetch(PDO::FETCH_OBJ);
                if ($ustne && $ustne->ustu == 0) {
                    $ustaktif = true;
                }
            }

            echo "\n<ul>\n";
            $i = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $i += 1;

                // Sayfa sorgusu için prepared statement
                $pageStmt = $db->prepare("SELECT id, url FROM sayfalar WHERE (site_id_555 = 501 OR site_id_888 = 100 OR site_id_777 = 501501 OR site_id_699 = 200 OR site_id_701 = 501501 OR site_id_702 = 300) AND id = :sayfa ORDER BY id DESC");
                $pageStmt->execute(['sayfa' => $row["sayfa"]]);
                $rwsayfa = $pageStmt->fetch(PDO::FETCH_OBJ);

                $mlink = $GLOBALS["dayarlar"]->permalink == "Evet" && $rwsayfa ? $rwsayfa->url . ".html" : "index.php?p=sayfa&id=" . ($rwsayfa ? $rwsayfa->id : 0);
                $kareurl = empty($row["url"]) || $row["url"] == "#" ? "javascript:void(0);" : htmlspecialchars($row["url"]);
                $target = !empty($row["target"]) ? " target=\"" . htmlspecialchars($row["target"]) . "\" " : "";

                echo "<li>";
                echo $ustaktif && $i == 1 ? "<i id=\"menuustok\" class=\"fa fa-caret-up\" aria-hidden=\"true\"></i><a id=\"ustline\"" : "<a";
                echo " href=\"";
                echo $row["sayfa"] != 0 ? htmlspecialchars($mlink) : $kareurl;
                echo "\"";
                echo $target;
                echo ">";
                echo htmlspecialchars($row["baslik"]);
                $this->menu_listesi($row["id"]); // Rekürsif çağrı
                echo "</li>\r\n";
            }
            echo "</ul>\n";
        }
    }

    /**
     * KDV hesaplar.
     *
     * @param float $fiyat
     * @param float $kdv
     * @return float
     */
    public function kdval(float $fiyat, float $kdv = 0): float
    {
        $kdv = $kdv == 0 ? 18 : $kdv; // Varsayılan KDV oranı 18, empty yerine == 0 kullanıldı
        $sonuc = $fiyat * ($kdv / 100);
        return round($sonuc, 2); // Daha kesin sonuç için yuvarlama eklendi
    }

    /**
     * Para birimi kodunu döndürür.
     *
     * @param string $pb
     * @return string
     */
    public function currency_code(string $pb): string
    {
        return match (trim($pb)) { // Gereksiz boşlukları temizle
            "₺", "TL" => "TRY",
            "$" => "USD",
            "EURO", "€" => "EUR",
            default => $pb,
        };
    }

    /**
     * Gün farkını hesaplar.
     *
     * @param string $tarih1
     * @param string $tarih2
     * @return int
     * @throws InvalidArgumentException Tarih formatı geçersizse
     */
    public function gun_farki(string $tarih1, string $tarih2): int
    {
        // Tarih formatını doğrula (YYYY-MM-DD bekleniyor)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tarih1) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tarih2)) {
            throw new InvalidArgumentException("Tarih formatı YYYY-MM-DD olmalı: $tarih1, $tarih2");
        }

        [$y1, $a1, $g1] = explode("-", $tarih1);
        [$y2, $a2, $g2] = explode("-", $tarih2);

        // Tarihlerin geçerliliğini kontrol et
        if (!checkdate($a1, $g1, $y1) || !checkdate($a2, $g2, $y2)) {
            throw new InvalidArgumentException("Geçersiz tarih: $tarih1 veya $tarih2");
        }

        $t1_timestamp = mktime(0, 0, 0, $a1, $g1, $y1);
        $t2_timestamp = mktime(0, 0, 0, $a2, $g2, $y2);
        
        $fark = ($t2_timestamp - $t1_timestamp) / 86400; // Gün farkı
        return (int)round($fark); // Daha temiz bir hesaplama
    }

    /**
     * Ekstra JS ve CSS dosyalarını yükler.
     *
     * @param bool $jquerymin jQuery minified dosyasını ekler
     * @param bool $bootstrap Bootstrap dosyalarını ekler
     * @param bool $ajaxform jQuery Form dosyasını ekler
     */
    public function ekstra(bool $jquerymin = false, bool $bootstrap = false, bool $ajaxform = false): void
    {
        // Daha temiz bir yapı için koşullu ekleme
        if ($jquerymin) {
            echo "<script type=\"text/javascript\" src=\"assets/js/jquery.min.js\" defer></script>\n";
        }
        if ($ajaxform) {
            echo "<script type=\"text/javascript\" src=\"assets/js/jquery.form.min.js\" defer></script>\n";
        }
        echo "<script type=\"text/javascript\" src=\"assets/js/istmark.js\" defer></script>\n";

        if ($bootstrap) {
            echo <<<HTML
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" defer></script>
HTML;
        }
    }

    /**
     * Metin uzunluğunu döndürür.
     *
     * @param string $str
     * @return int
     */
    public function strlentr(string $str): int
    {
        return mb_strlen($str, "UTF-8");
    }

    /**
     * Metni kısaltır.
     *
     * @param string $text
     * @param int $baslangic Başlangıç indeksi
     * @param int $son Kısaltılacak uzunluk
     * @param string $charset Karakter seti
     * @return string
     */
    public function kisalt(string $text, int $baslangic, int $son, string $charset = "UTF-8"): string
    {
        // Negatif veya geçersiz değerleri kontrol et
        if ($baslangic < 0 || $son < 0) {
            return $text; // Hata yerine orijinal metni döndür
        }
        return mb_substr($text, $baslangic, $son, $charset);
    }

    /**
     * Metni kısaltır ve sonuna ... ekler.
     *
     * @param string $text
     * @param int $baslangic Başlangıç indeksi
     * @param int $son Kısaltılacak uzunluk
     * @param string $charset Karakter seti
     * @return string
     */
    public function kisalt2(string $text, int $baslangic, int $son, string $charset = "UTF-8"): string
    {
        $netext = $this->kisalt($text, $baslangic, $son, $charset);
        $netext .= $son < $this->strlentr($text) - $baslangic ? "..." : ""; // Uzunluk kontrolü düzeltildi
        return $netext;
    }

    /**
     * Sadece rakamları döndürür.
     *
     * @param string $num
     * @return string
     */
    public function sadece_rakam(string $num): string
    {
        // guvenlik::rakam varsayılan olarak rakamları filtreliyor kabul ediyorum
        return guvenlik::rakam($num); 
        // Alternatif: preg_replace('/[^0-9]/', '', $num) kullanılabilir
    }

    /**
     * Mail gönderir.
     *
     * @param string $konu Mail konusu
     * @param string $nereye Alıcı e-posta adresi
     * @param string $message Mesaj içeriği
     * @return bool Gönderme başarılıysa true
     * @throws Exception PHPMailer hatası durumunda
     */
    public function mail_gonder(string $konu, string $nereye, string $message): bool
    {
        require_once 'magnes.php'; // PHPMailer dosyasını dahil et (dosya yoluna dikkat et)

        $gonder = new PHPMailer();
        
        try {
            // SMTP veya Mail seçimi
            if (defined("ISMAIL") && ISMAIL) {
                $gonder->IsMail();
            } else {
                $gonder->IsSMTP();
                $gonder->Host = MAIL_HOST;
                $gonder->Port = MAIL_PORT;
                if (defined("MAIL_SECURE") && MAIL_SECURE) {
                    $gonder->SMTPSecure = MAIL_SMTPSecure;
                }
                $gonder->SMTPAuth = true;
                $gonder->Username = MAIL_USER;
                $gonder->Password = MAIL_PASSWORD;
            }

            // Genel ayarlar
            $gonder->CharSet = "utf-8";
            $gonder->From = MAIL_USER;
            $gonder->FromName = MAIL_FROMNAME;
            $gonder->SetFrom(MAIL_USER, MAIL_FROMNAME);
            $gonder->WordWrap = 50;
            $gonder->IsHTML(true);
            
            // Hata ayıklama
            if (defined("SMTP_DEBUG") && SMTP_DEBUG) {
                $gonder->SMTPDebug = 2;
            }

            // Mesaj ayarları
            $gonder->Subject = $konu;
            $gonder->Body = $message;
            $gonder->AddAddress($nereye, defined('__DOMAIN__') ? __DOMAIN__ : '');

            return $gonder->Send();
        } catch (Exception $e) {
            error_log("Mail gönderme hatası: " . $e->getMessage());
            return false;
        }
    }
	
    /**
     * İzmir TR SMS CURL isteği yapar.
     *
     * @param string $site_name Hedef URL
     * @param string $send_xml Gönderilecek veri
     * @return string CURL yanıtı
     */
    private function izmirtr_sms_curl(string $site_name, string $send_xml): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $site_name);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $send_xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        
        $result = curl_exec($ch);
        if ($result === false) {
            error_log("CURL hatası: " . curl_error($ch));
            $result = "ERR: CURL hatası";
        }
        
        curl_close($ch);
        return $result;
    }

    /**
     * İzmir TR SMS gönderir.
     *
     * @param string|array $telefon Alıcı telefon numaraları
     * @param string $text Gönderilecek mesaj
     * @return bool Gönderme başarılıysa true
     */
    private function izmirtrcell_gonder(string|array $telefon, string $text): bool
    {
        if (!$this->loaded_sms_gonder && file_exists(__DIR__ . DIRECTORY_SEPARATOR . "sms_gonder.php")) {
            require_once __DIR__ . DIRECTORY_SEPARATOR . "sms_gonder.php";
            $this->loaded_sms_gonder = true;
        }

        if (function_exists("sms_gonder")) {
            return sms_gonder($telefon, $text);
        }

        $numaralar = is_array($telefon) ? implode(",", (array)$telefon) : $telefon;
        $postdata = [
            "kullanici" => SMS_USERNAME,
            "sifre" => SMS_PASSWORD,
            "baslik" => SMS_BASLIK,
            "metin" => $text,
            "alicilar" => $numaralar
        ];
        
        $postdata = http_build_query($postdata);
        $sonuc = $this->izmirtr_sms_curl("http://izmirtr.com/cell/sms-gonder-api.php", $postdata);
        
        return str_starts_with($sonuc, "OK");
    }

    /**
     * SMS gönderir.
     *
     * @param string|array $telefon Alıcı telefon numaraları
     * @param string $text Gönderilecek mesaj
     * @param string $turu SMS türü (kullanılmıyor)
     * @return bool Gönderme başarılıysa true
     */
    public function sms_gonder(string|array $telefon, string $text, string $turu = ""): bool
    {
        global $gayarlar;
        
        if (empty($gayarlar->sms_firma) || $gayarlar->sms_firma != 1) {
            return false; // Desteklenmeyen firma
        }
        
        return $this->izmirtrcell_gonder($telefon, $text);
    }

    /**
     * Boşluk kontrolü yapar.
     *
     * @param string $text Kontrol edilecek metin
     * @return bool Metin boş veya sadece boşluklardan oluşuyorsa true
     */
    public function bosluk_kontrol(string $text): bool
    {
        return empty(trim($text)); // Daha basit ve etkili bir kontrol
    }

    /**
     * Tamam mesajı gösterir.
     *
     * @param string $text Gösterilecek mesaj
     */
    public function tamam(string $text): void
    {
        echo '<div class="alert alert-success" role="alert">';
        echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        echo '</div>';
    }

    /**
     * Hata mesajı gösterir.
     *
     * @param string $text Gösterilecek mesaj
     */
    public function hata(string $text): void
    {
        echo '<div class="alert alert-danger" role="alert">';
        echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        echo '</div>';
    }

    /**
     * Bilgi mesajı gösterir.
     *
     * @param string $text Gösterilecek mesaj
     */
    public function bilgi(string $text): void
    {
        echo '<div class="alert alert-info" role="alert">';
        echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        echo '</div>';
    }

    /**
     * Uyarı mesajı gösterir.
     *
     * @param string $text Gösterilecek mesaj
     */
    public function uyari(string $text): void
    {
        echo '<div class="alert alert-warning" role="alert">';
        echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        echo '</div>';
    }

    /**
     * Ajax başarı mesajı gösterir.
     *
     * @param string $string Gösterilecek mesaj
     */
    public function ajax_tamam(string $string): void
    {
        $escaped_string = addslashes(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
        echo <<<JS
<script type="text/javascript">
    \$.Notification.autoHideNotify('success', 'top center', 'İşlem Başarılı', '$escaped_string');
</script>
JS;
    }

    /**
     * Ajax hata mesajı gösterir.
     *
     * @param string $string Gösterilecek mesaj
     */
    public function ajax_hata(string $string): void
    {
        $escaped_string = addslashes(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
        echo <<<JS
<script type="text/javascript">
    \$.Notification.autoHideNotify('error', 'top center', 'İşlem Hatalı', '$escaped_string');
</script>
JS;
    }

    /**
     * Ajax uyarı mesajı gösterir.
     *
     * @param string $string Gösterilecek mesaj
     */
    public function ajax_uyari(string $string): void
    {
        $escaped_string = addslashes(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
        echo <<<JS
<script type="text/javascript">
    \$.Notification.autoHideNotify('warning', 'top center', 'Uyarı!', '$escaped_string');
</script>
JS;
    }

    /**
     * Ajax bilgi mesajı gösterir.
     *
     * @param string $string Gösterilecek mesaj
     */
    public function ajax_bilgi(string $string): void
    {
        $escaped_string = addslashes(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
        echo <<<JS
<script type="text/javascript">
    \$.Notification.autoHideNotify('info', 'top center', 'Bilgi', '$escaped_string');
</script>
JS;
    }

    /**
     * Yönlendirme yapar.
     *
     * @param string $nere Yönlendirilecek URL
     * @param int $sure Bekleme süresi (milisaniye cinsinden)
     */
    public function yonlendir(string $nere, int $sure = 1000): void
    {
        $nere = htmlspecialchars($nere, ENT_QUOTES, 'UTF-8'); // Güvenlik için
        echo <<<JS
<script type="text/javascript">
    function yolla() {
        window.location.href = '$nere';
    }
    setTimeout(yolla, $sure);
</script>
JS;
    }

    /**
     * Türkçe karakterleri İngilizce karakterlere çevirir.
     *
     * @param string $text Dönüştürülecek metin
     * @return string Dönüştürülmüş metin
     */
    public function eng_cevir(string $text): string
    {
        $text = trim($text);
        $search = ["Ç", "ç", "Ğ", "ğ", "ı", "İ", "Ö", "ö", "Ş", "ş", "Ü", "ü"];
        $replace = ["C", "c", "G", "g", "i", "I", "O", "o", "S", "s", "U", "u"];
        return str_replace($search, $replace, $text);
    }

    /**
     * Cacheleme işlemlerini yönetir.
     *
     * @param string $yap İşlem türü (basla/bitir)
     * @param string|null $cache_ismi Cache dosya adı (varsayılan: URL’den MD5)
     * @param int $cache_suresi Cache süresi (saniye cinsinden)
     */
    public function cachele(string $yap, ?string $cache_ismi = null, int $cache_suresi = 21600): void
    {
        global $cache;

        $cache_ismi = $cache_ismi ?? md5($_SERVER["REQUEST_URI"]); // Null coalescing operatörü
        $cache_klasor = __DIR__ . "/cache";
        $cache_dosya_adi = "$cache_klasor/cache-$cache_ismi.txt";

        if (!is_dir($cache_klasor)) {
            mkdir($cache_klasor, 0777, true); // Daha güvenli bir izin (493 yerine 0777 ve recursive)
        }

        if ($yap === "basla") {
            if (file_exists($cache_dosya_adi) && (time() - filemtime($cache_dosya_adi)) < $cache_suresi) {
                $cache = false;
                readfile($cache_dosya_adi); // include yerine readfile, daha hızlı
            } else {
                $cache = true;
                ob_start();
            }
        } elseif ($yap === "bitir" && $cache) {
            file_put_contents($cache_dosya_adi, ob_get_contents(), LOCK_EX); // Dosya kilidi eklendi
            ob_end_flush();
        }
    }

    /**
     * E-posta adresini gizler.
     *
     * @param string $str Gizlenecek e-posta adresi
     * @return string|bool Gizlenmiş e-posta veya geçersizse false
     */
    public function eposta_gizle(string $str): string|bool
    {
        if (empty($str) || !str_contains($str, '@')) {
            return false; // Geçerli bir e-posta değil
        }

        [$user, $domain] = explode('@', $str, 2);
        $user_length = mb_strlen($user, 'UTF-8');
        $gizli_user = '';

        for ($i = 0; $i < $user_length; $i++) {
            $gizli_user .= ($i == 1 || $i == 3 || $i == 5 || $i == 7 || $i == 9) ? '*' : $user[$i];
        }

        return "$gizli_user@$domain";
    }

    /**
     * Metni gizler.
     *
     * @param string $str Gizlenecek metin
     * @return string|bool Gizlenmiş metin veya boşsa false
     */
    public function string_gizle(string $str): string|bool
    {
        if (empty($str)) {
            return false;
        }

        $length = mb_strlen($str, 'UTF-8');
        $gizli_str = '';

        for ($i = 0; $i < $length; $i++) {
            $gizli_str .= ($i == 1 || $i == 3 || $i == 5 || $i == 7 || $i == 9) ? '*' : $str[$i];
        }

        return $gizli_str;
    }

    /**
     * Kupon anahtarı oluşturur.
     *
     * @param int $max_l Oluşturulacak anahtarın uzunluğu
     * @return string Oluşturulan kupon anahtarı
     */
    public function KuponKey(int $max_l): string
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
        $zufallscode = '';
        
        for ($i = 0; $i < $max_l; $i++) {
            $zufallscode .= $chars[random_int(0, strlen($chars) - 1)]; // Daha güvenli rasgele seçim
            if (($i + 1) % 3 == 0 && $i < $max_l - 1) {
                $zufallscode .= '-';
            }
        }

        return strtoupper($zufallscode);
    }

    /**
     * Türkçe karakterleri dönüştürür (ISO-8859-9’dan UTF-8’e).
     *
     * @param string $char Dönüştürülecek metin
     * @return string Dönüştürülmüş metin
     */
    public function turkce_karakter(string $char): string
    {
        return mb_convert_encoding($char, "UTF-8", "ISO-8859-9");
    }

    /**
     * Zaman farkını hesaplar ve uygun formatta döndürür.
     *
     * @param string $zaman Zaman string’i (örn: "DD.MM.YYYY HH:MM:SS")
     * @return string İnsan dostu zaman farkı
     * @throws InvalidArgumentException Geçersiz zaman formatı
     */
    public function zaman(string $zaman): string
    {
        if (!preg_match('/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}:\d{2}$/', $zaman)) {
            throw new InvalidArgumentException("Zaman formatı DD.MM.YYYY HH:MM:SS olmalı: $zaman");
        }

        [$tarih, $saat] = explode(" ", $zaman);
        [$gun, $ay, $yil] = explode(".", $tarih);
        [$saat, $dakika, $saniye] = explode(":", $saat);

        $zaman_ts = mktime($saat, $dakika, $saniye, $ay, $gun, $yil);
        $fark = time() - $zaman_ts;

        $saniye = $fark;
        $dakika = (int)($fark / 60);
        $saat = (int)($fark / 3600);
        $gun = (int)($fark / 86400);
        $hafta = (int)($fark / 604800);
        $ay = (int)($fark / 2419200);
        $yil = (int)($fark / 29030400);

        return match (true) {
            $saniye < 60 => $saniye <= 0 ? "Az Önce" : "Yaklaşık $saniye saniye önce",
            $dakika < 60 => "Yaklaşık $dakika dakika önce",
            $saat < 24 => "Yaklaşık $saat saat önce",
            $gun < 7 => "Yaklaşık $gun gün önce",
            $hafta < 4 => "Yaklaşık $hafta hafta önce",
            $ay < 12 => "Yaklaşık $ay ay önce",
            default => "Yaklaşık $yil yıl önce"
        };
    }

    /**
     * IP adresini döndürür.
     *
     * @return string Kullanıcının IP adresi
     */
    public function IpAdresi(): string
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            if (str_contains($ip, ",")) {
                $tmp = explode(",", $ip);
                $ip = trim($tmp[0]);
            }
        } else {
            $ip = $_SERVER["REMOTE_ADDR"] ?? '0.0.0.0'; // Varsayılan değer
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ?: '0.0.0.0'; // Geçerli IP kontrolü
    }

/**
     * Geçerli tarih ve saati döndürür.
     *
     * @return string Y-m-d H:i:s formatında tarih-saat
     */

public function datetime(): string
    {
        return date("Y-m-d H:i:s");
    }

    /**
     * Geçerli tarihi döndürür.
     *
     * @return string Y-m-d formatında tarih
     */
    public function this_date(): string
    {
        return date("Y-m-d");
    }

    /**
     * Giriş için gizli anahtar oluşturur.
     *
     * @param string $acid Kullanıcı ID’si
     * @param string $acpw Şifre
     * @return string SHA-256 hashlenmiş anahtar
     */
    public function login_secret_key(string $acid, string $acpw): string
    {
        return hash('sha256', "ISTMARK_@^_^_SECRET_@_" . $acid . "_@_" . $acpw . "_@_" . $this->IpAdresi() . "@+"); // MD5 yerine SHA-256
    }

    /**
     * Dosya uzantısını döndürür.
     *
     * @param string $string Dosya adı
     * @return string Küçük harfli uzantı (örn: .jpg)
     */
    public function uzanti(string $string): string
    {
        $uzanti = strrchr($string, ".");
        return $uzanti === false ? '' : strtolower($uzanti);
    }

    /**
     * Çoklu dosya dizisini düzenler.
     *
     * @param array $arr Dosya dizisi (örn: $_FILES)
     * @return array Düzenlenmiş dosya dizisi
     */
    public function multiple_arr(array $arr): array
    {
        $files = [];
        foreach ($arr as $k => $l) {
            foreach ($l as $i => $v) {
                $files[$i] = $files[$i] ?? [];
                $files[$i][$k] = $v;
            }
        }
        return $files;
    }

    /**
     * Görsel ayarlarını yapar.
     *
     * @param string $path Hedef klasör
     * @param string $file Dosya adı
     * @param string $name Yeni dosya adı (boşsa orijinal kullanılır)
     * @param bool $thumb Thumbnail oluştur
     * @param int|bool $x Genişlik
     * @param int|bool $y Yükseklik
     * @param int $rotate Döndürme açısı (derece)
     * @param string|bool $watermark Filigran dosyası
     * @return bool İşlem başarılıysa true
     */
    public function gorsel_ayarla(string $path, string $file, string $name, bool $thumb = false, int|bool $x = false, int|bool $y = false, int $rotate = 0, string|bool $watermark = false): bool
    {
        require_once 'path/to/Upload.php'; // Upload sınıfının yolunu projene göre ayarla

        $paf = empty($path) ? $file : $path . DIRECTORY_SEPARATOR . $file;
        $image = new Upload($paf, "tr_TR");

        if (!$image->uploaded) {
            return false;
        }

        $image->file_overwrite = true;
        if (!empty($name)) {
            $image->file_new_name_body = $name;
        }
        $image->image_background_color = "#eeeeee";
        $image->allowed = ["image/*"];
        $image->jpeg_quality = 100;

        if ($x || $y) {
            $image->image_resize = true;
            $image->image_ratio_fill = true;
            if ($x && $y) {
                $image->image_x = $x;
                $image->image_y = $y;
            } elseif ($x) {
                $image->image_x = $x;
                $image->image_ratio_y = true;
            } elseif ($y) {
                $image->image_y = $y;
                $image->image_ratio_x = true;
            }
        }

        if ($rotate != 0) {
            $image->image_rotate = $rotate;
        }

        if ($watermark !== false && !empty($watermark)) {
            $image->image_watermark = $watermark;
            $image->image_watermark_position = "L";
        }

        $wipath = empty($path) ? __DIR__ : $path;
        $wipath = $thumb ? $wipath . DIRECTORY_SEPARATOR . "thumb" : $wipath;

        $image->Process($wipath);
        if ($image->processed) {
            $image->Clean();
            return true;
        }

        error_log("Görsel işleme hatası: " . $image->error);
        return false;
    }

    /**
     * Resim yükler ve işler.
     *
     * @param bool $thumb Thumbnail oluştur
     * @param string $name Formdaki dosya input adı
     * @param string $dadi Yeni dosya adı
     * @param string $yol Hedef klasör
     * @param int|bool $x Genişlik
     * @param int|bool $y Yükseklik
     * @param bool $filtre İşleme yapılıp yapılmayacağı
     * @param string $watermark Filigran dosyası
     * @param bool $crop Kırpma yapılıp yapılmayacağı
     * @return string İşlenen dosya adı
     */
public function resim_yukle(string $name, string $dadi, string $yol, bool $thumb = false, int|bool $x = false, int|bool $y = false, bool $filtre = true, string $watermark = "", bool $crop = false): string
{
    require_once 'path/to/Upload.php'; // Upload sınıfının yolunu projene göre ayarla

    if (!$filtre) {
        $target = $thumb ? "$yol/thumb/$dadi" : "$yol/$dadi";
        if (@move_uploaded_file($_FILES[$name]["tmp_name"], $target)) {
            return $dadi;
        }
        throw new RuntimeException("Dosya taşınamadı: $target");
    }

    $uzanti = $this->uzanti($dadi);
    $dontext = str_replace($uzanti, "", $dadi);
    $orgname = "{$dontext}_original{$uzanti}";
    $original_paf = "$yol/$orgname";

    if (!file_exists($original_paf) && !@move_uploaded_file($_FILES[$name]["tmp_name"], $original_paf)) {
        throw new RuntimeException("Orijinal görsel yüklenemedi: $original_paf");
    }

    $image = new Upload($original_paf);
    if (!$image->uploaded) {
        throw new RuntimeException("Görsel yükleme başarısız: " . $image->error);
    }

    $image->file_overwrite = true;
    $image->file_new_name_body = $dontext;
    $image->image_background_color = "#eeeeee";
    $image->allowed = ["image/*"];
    $image->jpeg_quality = 100;

    if ($x || $y) {
        $image->image_resize = true;
        if ($crop) {
            $image->image_ratio_fill = true;
        }
        if ($x && $y) {
            $image->image_x = $x;
            $image->image_y = $y;
        } elseif ($x) {
            $image->image_x = $x;
            $image->image_ratio_y = true;
        } elseif ($y) {
            $image->image_y = $y;
            $image->image_ratio_x = true;
        }
    }

    if (!empty($watermark)) {
        $image->image_watermark = $watermark;
        $image->image_watermark_position = "L";
    }

    $yol = $thumb ? "$yol/thumb/" : "$yol/";
    $image->Process($yol);
    if (!$image->processed) {
        throw new RuntimeException("Görsel işleme hatası: " . $image->error);
    }

    $image->Clean();
    return $dadi;
}

    /**
     * HTTP adresini döndürür.
     *
     * @return string Geçerli sayfanın tam URL’si
     */
    public function http_adres(): string
    {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return "$scheme://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
    }


/**
     * İki string’i birleştirip sabit bir ekleme yapar (güvenlik fonksiyonu).
     *
     * @param string $a İlk string
     * @param string $b İkinci string
     * @return string Birleştirilmiş string
     */
    public function aTk7C60iSqriNxrOVShUZTVoAU(string $a, string $b): string
    {
        return $a . "-" . $b . "bOtqTFLI8ub6ZjtmhhZutOCR";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ac5ceki26dbmmajgxahszg9o0fc(): string
    {
        return "6b>^PEDK3=!|Ijy2#m<Z4uyq`(JMncciGAK!h=[praz";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ateipc7puj5yzylevuv6yevzq56(): string
    {
        return ";NL?D`3?;w*7XYo>+NIe_MS#Cs1YdYL<(q)n(&)+yz&";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function azi90ce2bzoncctlmrscpjl51wl(): string
    {
        return "Be=Y/Ku-g18*N-T5?6nC+`<)ei#<HgKI)9}R0!<!5:G";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function a8emwlgm3nnokh9ft0gfdpatsdg(): string
    {
        return "&~[}Uq/v1nTJ21<KNqkjX*dI@LZa(|AQiDdXOlXpz(Y";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function azqbpnca15spplrnqftgwr0drg7(): string
    {
        return "5P#jWl3tH:|@`N>IFF9bNo03X6EKX5QQ0y-!=vJ&/~s";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function adugezsraprpw9axkormka7km38(): string
    {
        return "fW~gqU;IJ^^_v;EO4pJF-?I_?s`0!>STmcZ|+nP}|HJ";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function aktmz6byk8fo5gwyi7vtvufgxpk(): string
    {
        return "a+@rsuV%3j5/BUoGN<YFM~vO>x&*j5ce4D_AyQmr/o%";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function arh44em3syb0urwxyzox4vsrhkx(): string
    {
        return "O`WqF;EC746!Y98/?*pV>rh]LP_bICZTsJx3EH5/s!^";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ayw5oujuawujwgjqx5anadxxw1c(): string
    {
        return "n2/CYd^^_8)X/x:(6071huF=FnjVcWM;1K;lpW>[(+n";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ayvgwrcgjliuejr5buhbrqkingh(): string
    {
        return "4wvX#nF*ZN6dxWW+M=YkK4b!eMxGM^f2^E5qW_KiBcS";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ar5rfyttthivdvne2gajrk4ccao(): string
    {
        return "PNndgEgJq:paN_r7)>K~QTYK5I8u}U#QnOYJW(YEA|H";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function amik67ii2jolwiczyzwxc0lxmbn(): string
    {
        return "=U;WF@lj3U2uwx(gpPUfqa#AMQoW-><Y|<i?zWW>yU]";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function afozgi9897ndhfgc8woy17mstnp(): string
    {
        return "/SEwo~_?G0U*gAm3gyGbIfipnnXa0kfI78;lsPGg7-7";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function asadqrlm0sir0r8tgug9hxz0af6(): string
    {
        return "%nDjDj+KS#9egYzQe&o=p#C=eI4C](4n5PU!9EbxrsT";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function akgz5iasaxnh3wg8zbul3fzvqvr(): string
    {
        return "!WrFwjx}OdGZ=/]!;jSWMQiz-1JfABCKS*fMh/iu!1D";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function aodfetqd30l7mzosonokft29ydv(): string
    {
        return "D=T9<NPCzv_+kQ#qM@ACOS>5&C!6:Bv6eAiz_69s@6q";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function aoukzpdywuehwgzocrehktiaktg(): string
    {
        return "|B+jpK?hxvn`11>5m8W2Y:pukpRzF*3GM-Fl#Vn>?wA";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function amwcxtupdjgeablowquhmbyhgeb(): string
    {
        return "v4w#<KTYLlOo`gIKOA*&B`vh=V08PYX[Q0N%WK5ilrQ";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function aiuqme19clrhtwxz8asspcdn4nf(): string
    {
        return "!VCW4CR]YAhxKgp(&%VSgOPr2+_:U+pgEU-^Ea~AF<!";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function a43mcslgnzie4swoai9ydbwdknl(): string
    {
        return "yPPN}R}`UoB#I3nJ1J[T#2U&upP~Iivl11&k32+2StX";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function adb5vf35vxxwuxoihugl3rnshqr(): string
    {
        return "stJK+1-&JfP#;y!vunean:E/4C}-f-7~H5K+R?L^-+n";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function avz6p52caqm59l8f6ii2xvhxhgo(): string
    {
        return "#act=+<i-8~G<:heDG#o%]>3p50okBh`]c3+RQOS8eX";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function asb8qtsqztt2qepgvxhl2b7nj0s(): string
    {
        return "I?Jb@=0&FHiU/29r%!bkwiXO4jAkU]XKq+4e|]vza[+";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ajx1fhhwzyqzjrp8yk9rrgcsjde(): string
    {
        return "tFd-kp#]Jkk/mf:0&tJBo#U`CZj!QDG|gWG72JB6=FQ";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ad2yiqkutmkmahdpgfegpey8tde(): string
    {
        return "N<%)Fj-8w4FL5w%HtJs0G7m*[dHb/ao;hGU-;C5(&V8";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function a1z9afwqrx4nfnfevd3x3swahsy(): string
    {
        return "YCO/I;>E6a0mRywY~Wz5gPkK^O;UJ_xs4v12Ds52?!:";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function acpjg4v2kzhsrpcsais7desxhjy(): string
    {
        return "7]V`J3k!#n5rV=&D5fLA@%JdHu@QdG#qgH0V2N><l:E";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function a2348au4n7jsxgze0ditpvywtsy(): string
    {
        return "GZu(jETRa=*=|_ai#g1@odoXy?/v_0kxP#?}>COiZ>[";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function aywzanyx99z2n591d3wq5g0k8zc(): string
    {
        return "lCvB8#gC%9THFM9iRQy=ZCP/x%4``_I=|C)R;N8OHvO";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function an6rizeupnpq6qcqsgxe9e6ff9n(): string
    {
        return "T7txNO4>tB|6A)Tc-Jzd(5>gINNUE0JU)F7?J:xR5sA";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ayhju1qfeco06mhzui1w0zltwme(): string
    {
        return "|Q`_H~(rP!%1EXZ9UJ<O&&blTK!P-6+TPJTZ:%:hEl}";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function akbbfuga4yzyp0dlkgzihfonvmt(): string
    {
        return ")aBnAy7mKFT3x>u12UckK@9O1HIdc[<6Hm&yH}h4X[h";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function auihusbtylx086tltlzqhy7augu(): string
    {
        return "u>sUMW(zofBANj7oGJC5593phTfugLN%;(riR@@Bzt2";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function alpbsps2fodm2qksgyrqxef96as(): string
    {
        return "|E;(!M`H[;9@LszSIh@q6*5i~D%2k_fEtW9jlVJ6-T:";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function acjhcinjllbfniplfaylh6r4nwl(): string
    {
        return "_/5-4;hVx+Hb^2s1Bu>xq}9y|eMWVdSQjdLrY`&]mQe";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function ahsqe6ageilredgpbetnufliqp2(): string
    {
        return "zLa>=~%EpX`_R8@PJ]+|CB-?WlpMQv3UkpPgSqmQwIr";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function aoxkgmubxbisot2emgcjh2sd14q(): string
    {
        return "sZcdJv>*@%AAXxdE<Z[smTN)fO@tird4iEjayYZ`>M9";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function aizjv5lpostdqrcnq3lvsv0pa7s(): string
    {
        return "4RMKkyFb_wWtJnS]f/1v;G~kyTQZ7CPXxG2GLO)LAtk";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function amza3tgxqqvgrikfoadogrvoglu(): string
    {
        return "Oh!^4]UOQ>F|a:gRBBgPFhRHv%TSL>kor^J%>@p_&t%";
    }

    /**
     * Sabit bir güvenlik string’i döndürür.
     *
     * @return string Güvenlik için sabit string
     */
    public function aqekny43smryahfa9lnlqh9tl6a(): string
    {
        return "wSK%``4xlSjgc>BB6y4ltAMAR&_vLRzJvLD5/#JK*?V";
    }
} // learkho_functions sınıfı burada kapanıyor