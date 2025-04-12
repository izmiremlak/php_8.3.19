<?php

if (!defined("SERVER_HOST")) {
    exit;
}

// msagete_security sınıfı, çeşitli güvenlik ve veri işleme işlevlerini içerir
class msagete_security extends home_security
{
    // HTML temizleme fonksiyonu
    public function html_temizle(string $text): string
    {
        return strip_tags(trim(filtre2($text)));
    }

    // Dizi içindeki tüm elemanları PermaLink fonksiyonuna tabi tutar
    public function PermaLinkArray(array $arr): array
    {
        foreach ($arr as $k => $v) {
            $arr[$k] = $this->PermaLink($v);
        }
        return $arr;
    }

    // Para formatlama fonksiyonu
    public function para_str(float $number = 0.0): string
    {
        if (function_exists("money_format")) {
            setlocale(LC_MONETARY, "tr_TR");
            $tutar = money_format("%i", $number);
            $tutar = str_replace(" TRY", "", $tutar);
        } else {
            if (class_exists("NumberFormatter")) {
                $fmt = new NumberFormatter("tr_TR", NumberFormatter::CURRENCY);
                $tutar = $fmt->formatCurrency($number, "TRY");
                $tutar = str_replace(["₺", "TL", " "], "", $tutar);
            } else {
                if (function_exists("number_format")) {
                    $tutar = number_format($number, 2, ",", ".");
                } else {
                    $tutar = (string)$number;
                }
            }
        }
        return substr($tutar, -3) == ",00" ? substr($tutar, 0, -3) : $tutar;
    }

    // Para formatını sayıya çevirme fonksiyonu
    public function para_int(string $money): float
    {
        $cleanString = preg_replace("/([^0-9\\.,])/i", "", $money);
        $onlyNumbersString = preg_replace("/([^0-9])/i", "", $money);
        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;
        $stringWithCommaOrDot = preg_replace("/([,\\.])/", "", $cleanString, $separatorsCountToBeErased);
        $removedThousendSeparator = preg_replace("/(\\.|,)(?=[0-9]{3,}\$)/", "", $stringWithCommaOrDot);
        return (float)str_replace(",", ".", $removedThousendSeparator);
    }

    // Alt karakter temizleme fonksiyonu
    public function alt_replace(string $string): string
    {
        $search = [chr(194) . chr(160), chr(194) . chr(144), chr(194) . chr(157), chr(194) . chr(129), chr(194) . chr(141), chr(194) . chr(143), chr(194) . chr(173), chr(173)];
        $string = str_replace($search, "", $string);
        return trim($string);
    }

    // Türkçe karakter kontrolü
    public function turkce_karakter_kontrol(string $string): bool
    {
        return (bool) preg_match("/[ÖÇŞİĞÜüğışöç]/", $string);
    }

    // Mesaj temizleme ve formatlama fonksiyonu
    public function mesaj(string $string): string
    {
        $string = trim($string);
        $string = filtre2($string);
        $string = nl2br($string);
        $string = strip_tags($string, "<br>");
        return $string;
    }

    // HTML metin temizleme fonksiyonu
    public function html_text(string $string): string
    {
        return addslashes($string);
    }

    // Genel metin temizleme fonksiyonu
    public function text(string $string): string
    {
        return addslashes(filtre2(htmlspecialchars($string)));
    }

    // Metni küçük harfe çevirme fonksiyonu
    private function temizle(string $metin): string
    {
        return mb_convert_case(str_ireplace("I", "ı", $metin), MB_CASE_LOWER, "UTF-8");
    }

    // Başlık temizleme ve formatlama fonksiyonu
    public function title(string $string): string
    {
        $title = htmlspecialchars(strip_tags(stripslashes($string)));
        return filtre2(preg_replace("/[^ a-z-A-Z-0-9ÇİĞÖŞÜçığöşü.,]['\"+_]/", "", $title));
    }

    // İsim temizleme ve formatlama fonksiyonu
    public function isim(string $string): string
    {
        $title = htmlspecialchars(strip_tags(stripslashes($string)));
        return preg_replace("/[^ []a-z-A-Z-0-9ÇİĞÖŞÜçığöşü.]/", "", $title);
    }

    // Sadece metin temizleme fonksiyonu
    public function sadece_text(string $string): string
    {
        return $this->toAscii($string);
    }

    // Harf ve rakam temizleme fonksiyonu
    public function harf_rakam(string $string = ""): string
    {
    $string = strip_tags(stripslashes($string));
    return preg_replace("/[^a-z-A-Z-0-9ÇİĞÖŞÜçığöşü_]/", "", $string);
    }

    // Sadece harf temizleme fonksiyonu
    public function harf(string $string): string
    {
        $string = strip_tags(stripslashes($string));
        return preg_replace("/[^a-z-A-ZÇİĞÖŞÜçığöşü]/", "", $string);
    }

    // Sadece rakam temizleme fonksiyonu
    public static function rakam(string $string): string
    {
        $string = strip_tags(stripslashes($string));
        return preg_replace("/[^0-9.]/", "", $string);
    }

    // Sayı temizleme fonksiyonu
    public static function sayi(string $string): string
    {
        $string = strip_tags(stripslashes($string));
        return preg_replace("/[^0-9.\-]/", "", $string);
    }

    // Para formatındaki rakamları temizleme fonksiyonu
    public function prakam(string $string): string
    {
        $string = strip_tags(stripslashes($string));
        $string = preg_replace("/[^0-9.,]/", "", $string);
        return $string == "" ? "0" : $string;
    }

    // Sıfırlanmış rakam temizleme fonksiyonu
    public function zrakam(string $string): string
    {
        $string = $this->rakam($string);
        return $string == "" ? "0" : $string;
    }

    // Parola temizleme fonksiyonu
    public function parola(string $string): string
    {
        $string = strip_tags(stripslashes($string));
        return preg_replace("/[^a-z-A-Z-0-9ÇİĞÖŞÜçığöşü@!#\$%^&._]/", "", $string);
    }

    // E-posta temizleme fonksiyonu
    public function eposta(string $string): string
    {
        $string = strip_tags(stripslashes($string));
        return preg_replace("/[^a-z-A-Z-0-9@._]/", "", $string);
    }

    // Çerez ve oturum temizleme fonksiyonu
    public function cookie_session(string $string): string
    {
        $string = strip_tags(stripslashes($string));
        return preg_replace("/[^ a-z-A-Z-0-9ÇİĞÖŞÜçığöşü._@]/", "", $string);
    }

    // URL temizleme fonksiyonu
    public function url(string $string): string
    {
        return mysql_real_escape_string(strip_tags(stripslashes($string)));
    }

    // URL doğrulama fonksiyonu
    public function url_kontrol(string $url): bool
    {
        return (bool) filter_var($url, FILTER_VALIDATE_URL);
    }

    // E-posta doğrulama fonksiyonu
    public function eposta_kontrol(string $eposta): bool
    {
        return (bool) filter_var($eposta, FILTER_VALIDATE_EMAIL);
    }

    // HTML filtreleme fonksiyonu
    public function filtre(string $str): string
    {
        return strip_tags($str, "<br><p><b><i><o><table><tr><td><th><thead><tbody><img><a><font><span><strong><em><ul><ol><li><h1><h2><h3><h4><h5>");
    }

    // Flood engeli fonksiyonu
    public function flood_engeli(): void
    {
        if (time() - 1 < $_SESSION["sec"]) {
            error_log("Flood yapmayın!"); // Log the error
            echo "Flood yapmayın!";
            exit;
        }
        $_SESSION["sec"] = time();
    }

    // PermaLink oluşturma fonksiyonu
    public function PermaLink(string $str, array $options = []): string
    {
        $str = str_replace(["&#39;", "&quot;", "&#39;", "&quot;"], " ", $str);
        $str = mb_convert_encoding((string)$str, "UTF-8", mb_list_encodings());
        $defaults = ["delimiter" => "-", "limit" => null, "lowercase" => true, "replacements" => [], "transliterate" => true];
        $options = array_merge($defaults, $options);
        $char_map = [
            "À" => "A", "Á" => "A", "Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A", "Æ" => "AE", "Ç" => "C", "È" => "E", "É" => "E", "Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I", 
            // ...
        ];
        $str = preg_replace(array_keys($options["replacements"]), $options["replacements"], $str);
        if ($options["transliterate"]) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }
        $str = preg_replace("/[^\p{L}\p{Nd}]+/u", $options["delimiter"], $str);
        $str = preg_replace("/(" . preg_quote($options["delimiter"], "/") . "){2,}/", "$1", $str);
        $str = mb_substr($str, 0, $options["limit"] ? $options["limit"] : mb_strlen($str, "UTF-8"), "UTF-8");
        $str = trim($str, $options["delimiter"]);
        return $options["lowercase"] ? mb_strtolower($str, "UTF-8") : $str;
    }

    // Metni ASCII formatına çevirme fonksiyonu
    private function toAscii(string $str): string
    {
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", "", $str);
        $clean = strtolower(trim($clean, ""));
        $clean = preg_replace("/[\/_|+ -]+/", "", $clean);
        return $clean;
    }
}

// Diğer yardımcı fonksiyonlar
function ag7xjCywFCJVWMPe7sjWbZnzQg($a, $b): string
{
    return $a . "-" . $b . "1fbo1CcJXpDMErhc1oZO3rJs";
}

function a9lv9bll1v0muilrweoojrmzccz(): string
{
    return "_]X}|gHwPv/t|1bG&8p[V:|EZD2AL9/[r4D]X3^Joee";
}

function aoxbsf76hk6lpufb8t5hnmzrcpu(): string
{
    return "Ry&5:|V:6=02!dPJ84PzBe+d^(ElV)GkGk<VYE-OW0f";
}

function aytq7drdmlgk8pldljyrdwcrp3f(): string
{
    return "G%jGJ2)0+I_PisIEnlc[!Q985F-4XJ<k=?HNC@P?d)v";
}

function aiocvsemnhlc0byrgplavfbgdio(): string
{
    return "-sK>bLjI:LAx@u8W&j)>2g3b4[VfNh}qVOzun;Sgi:v";
}

function afvchoylvosrrg11rfojhrki6pu(): string
{
    return "p2uRk<NqL}G+ZGCHIOm)|Lxf34I)F_EZAe9hlkLo5;/";
}

function atrtowbkpnduybvopplrlxdt3tj(): string
{
    return "yIq^}K}Z2^=R`c7V8?k5S2zbjA]q(=(%%(3>;x^gKGm";
}

function ayskatnqgai753nhiir5jxzmzxi(): string
{
    return "(VDc%ru#%oo*oe%SAyMXLT&~JN>K|Y;zZ03ck[NIX)/";
}

function auszyrsfrsa0gaugp25hmeauckh(): string
{
    return "jH:)1NOBW!~g4a/!z4&y(jR[(FV2TtjO7Y~NsY}rRTN";
}

function aqypbcvyxlok8qnogspnvxawvto(): string
{
    return "/&vGJ>D?cGzpb`Nl*X]xeCJ4FS:DloZ=!RFxjPEaL-P";
}

function atg6wcqkqj6jdw2npev7zvlqoxi(): string
{
    return ":dGU?QCnvk@~P6r=4>hh8i5_q<;xfB[WD=DQk0OsO?a";
}

function aydfynu6ryqb1eungusfhifl0sq(): string
{
    return "Rel)Z6+FeC`BUDwOJ!)k1|D5*Y+99<VSgO+nYX;k*LJ";
}

function ahohsulw5czeicnyhgi3sfmh0jx(): string
{
    return "tJ-=jVU#?iQ-|Xf9vSyB3Y@)Y-P>yJ`h#?f/=rHQ+E0";
}

function atzvcnlskzcarfxmwglkleyqv3q(): string
{
    return "g7acm3jIPF#JBLg`MGxx(w*LJrqq#UtO4%5SS+h/2Qm";
}

function atpi5zbeqh3bxmdi8lmd16hfm0n(): string
{
    return "mG?Ifjy7J67jWIg#I&i~9U49ea=</L=t4<2cYR7&m^Z";
}

function azl62gyjkg5ou0kibfsql5jnr6a(): string
{
    return "Q73X)UHx|Y`lXQ2dc[D%Y>c<+|nmz3%/QlAranYJPpT";
}

function axut825uvasycoerqxy3elwdmmd(): string
{
    return "z/d_^23W:NOE@zwgo:W<(8/PG@3X1(RSB<IAZ};6-(]";
}

function a7dvrqi9bbsqxedufti4pgh6q1q(): string
{
    return "7z|PDau7JmCCT<I)V2xX4i}2tSH6P6C2*vutcA7vz8M";
}

function auwuute9fbhlwpnkxvqxgv1rbza(): string
{
    return "7t}QVluw6]MFCGDa6p-p:cSkq?nXj)[-4]zAo7C/J4<";
}

function adzjwycyls7ypu0eagy6zvvc7dl(): string
{
    return "Ap^3OaZ!<PKcD8L<;hX]pEbDyq=(xa*0#R158N5*Sw}";
}

function ayvjtmteyksfse7vutwllrmw7y4(): string
{
    return "_pWx&L!P4beK@b4HJryg`~vhn)HkSgGc%asgtqjjPK<";
}

function av2a8utikv40gmiqrbozqs9icre(): string
{
    return "a%[hqtItRBT]7t^R6SE7vDd+t|B;Gy^%5o:9/R9ZK8s";
}

function a4nw4xh4i8dhcvpnbvqo8v4sudf(): string
{
    return "}e6Gi9:V}F#xl`9G8+wcerW%ab|*kHDz%<QPrt`pqm-";
}

function aqzco5qdcxv9u2803ds6pxk1ik3(): string
{
    return "25Mmo@~Gg`nPgBBkQ=sG)pEhB-24@8~qwK;@+KIB/;O";
}

function a0ijjjgaj03h9fquyqfzflxgamj(): string
{
    return "GsqaBzP!=/q<*+Tr&U%A*x_u~X^zj(;_rUiw-}@u`m}";
}

function atp3eeulf4cpzgmjkqsq4rfilak(): string
{
    return "P/C=7n;@3odz(mHvvHo97Krfx_MBPyS2/pFa(Fn|%3v";
}

function atiiq4jpklywjf7fecwtmi1qmvd(): string
{
    return ")+G1#[O]h/UpF>e!cpN+Sl`}nFD#:FQC|*0_ZEjuk#[";
}

function aqgodjoghlzcoe9wksuxcvm2k7v(): string
{
    return "J-jA)q34EBCa:AVKn41N@6[L*p+>w3xAmaW#RL/4zPw";
}

function adax4batpihtyytna2v68schjyn(): string
{
    return "y_~H:TA@tA@^6qI7S^FFs]qc*R6!J%0=w[`;)t~~<9D";
}

function axknuycbghuvzeoys3d5dayiaot(): string
{
    return "=2~j1q>dZpR#OPnSw1DaC#huJm>O[z47&Id3Cm?Wl_B";
}

function accufqi5cznemt60tx0wadz6bsd(): string
{
    return "idb21JtjoXq_sgvfQQ/q00&D19rb(Y`Sh!Ee>SHNsEt";
}

function aivehoqqeonrca7jpvfitq8gqms(): string
{
    return "6?+#-`7=633AGakXbja6V:5(8/Sg/s10Vak<Y<cMt/`";
}

// ASCII karakterleri dönüştürme fonksiyonu
function ascii(string $text): string
{
    $replace = ["Ç", "İ", "Ğ", "Ö", "Ş", "Ü", "ç", "ı", "ğ", "ö", "ş", "ü"];
    $search = ["&#199;", "&#304;", "&#286;", "&#214;", "&#350;", "&#220;", "&#231;", "&#305;", "&#287;", "&#246;", "&#351;", "&#252;"];
    return str_replace($search, $replace, $text);
}

// Girdi temizleme fonksiyonu
function filtre2(string $deger): string
{
    return str_replace(["'", "\"", "\\&#39;", "\\&quot;"], ["&#39;", "&quot;", "&#39;", "&quot;"], $deger);
}