<?php

// Güvenlik kontrolü: SERVER_HOST tanımlı değilse script çalışmaz
if (!defined("SERVER_HOST")) {
    die();
}

/**
 * Mobile Detect Library
 * =====================
 *
 * Motto: "Every business should have a mobile detection script to detect mobile readers"
 *
 * Mobile_Detect is a lightweight PHP class for detecting mobile devices (including tablets).
 * It uses the User-Agent string combined with specific HTTP headers to detect the mobile environment.
 *
 * @author      Current authors: Serban Ghita <serbanghita@gmail.com>
 *                               Nick Ilyin <nick.ilyin@gmail.com>
 *
 *              Original author: Victor Stanciu <vic.stanciu@gmail.com>
 *
 * @license     Code and contributions have 'MIT License'
 *              More details: https://github.com/serbanghita/Mobile-Detect/blob/master/LICENSE.txt
 *
 * @link        Homepage:     http://mobiledetect.net
 *              GitHub Repo:  https://github.com/serbanghita/Mobile-Detect
 *              Google Code:  http://code.google.com/p/php-mobile-detect/
 *              README:       https://github.com/serbanghita/Mobile-Detect/blob/master/README.md
 *              HOWTO:        https://github.com/serbanghita/Mobile-Detect/wiki/Code-examples
 *
 * @version     2.8.25
 */

class Mobile_Detect
{
    protected $useragent = '';
    protected $httpHeaders = [];
    protected $cloudfrontHeaders = [];
    protected $matching_regex = null;
    protected $matches_array = null;
    protected $detection_type = 'mobile';
    protected $cache = [];

    const VERSION_TYPE = 'text';
    const VERSION = '2.8.25';
    const DETECTION_TYPE_MOBILE = 'mobile';
    const DETECTION_TYPE_EXTENDED = 'extended';
    const MOBILE_GRADE_A = 'A';
    const MOBILE_GRADE_B = 'B';
    const MOBILE_GRADE_C = 'C';

    const HEADER_VIA = 'HTTP_VIA';
    const HEADER_UA = 'HTTP_USER_AGENT';
    const HEADER_ACCEPT = 'HTTP_ACCEPT';
    const HEADER_X_WAP = 'HTTP_X_WAP_PROFILE';
    const HEADER_PROFILE = 'HTTP_PROFILE';

    protected $phoneDevices = [];
    protected $tabletDevices = [];
    protected $operatingSystems = [];
    protected $browsers = [];
    protected $utilities = [];

    public function __construct()
    {
        $this->useragent = isset($_SERVER[self::HEADER_UA]) ? $_SERVER[self::HEADER_UA] : '';
        $this->httpHeaders = $_SERVER;
        $this->initProperties();
    }

    protected function initProperties()
    {
        try {
            $this->phoneDevices = [
                'iphone' => '\biPhone\b|\biPod\b',
                'android' => '\bAndroid\b',
                'blackberry' => '\bBlackBerry\b|\bBB10\b|\bRIM\sTablet\sOS\b',
                'dream' => 'Dream',
                'cupcake' => 'Cupcake',
                'webos' => '\bwebOS\b|\bPalm\b|\bPre\b|\bPixi\b',
                'windows' => 'Windows\sPhone|\bIEMobile\b',
                'symbian' => 'Symbian|\bSymbOS\b',
                'bada' => '\bBada\b',
                'htc' => 'HTC[\-_]?([A-Za-z0-9]+)',
                'samsung' => 'SAMSUNG[\-_]?([A-Za-z0-9]+)',
                'nokia' => 'Nokia[\-_]?([A-Za-z0-9]+)',
            ];

            $this->tabletDevices = [
                'ipad' => '\biPad\b',
                'android' => '\bAndroid\b(?!\sMobile)',
                'kindle' => 'Kindle|\bSilk\b',
                'blackberry' => '\bPlayBook\b|\bBB10\b',
                'windows' => 'Windows\sNT.*Touch',
                'xoom' => 'Xoom',
                'samsung' => 'GT-P\d{4}|SCH-P\d{3}|SM-T\d{3}',
            ];

            $this->operatingSystems = [
                'windows' => 'Windows\sPhone|Windows\sNT|Windows\sCE',
                'ios' => '\biPhone\b|\biPad\b|\biPod\b',
                'android' => '\bAndroid\b',
                'blackberry' => '\bBlackBerry\b|\bBB10\b',
                'webos' => '\bwebOS\b|\bPalm\b',
                'symbian' => 'Symbian|\bSymbOS\b',
                'bada' => '\bBada\b',
                'linux' => 'Linux',
            ];

            $this->browsers = [
                'chrome' => '\bCrMo\b|\bChrome\b',
                'firefox' => '\bFirefox\b',
                'safari' => '\bSafari\b(?!.*Chrome)',
                'opera' => 'Opera\b|OPR\b',
                'ie' => 'MSIE|Trident',
                'ucbrowser' => 'UCBrowser|UCWEB',
            ];

            $this->utilities = [
                'bot' => 'Googlebot|Bingbot|YandexBot|Slurp',
                'mobilebot' => 'Mediapartners-Google|AdsBot-Google',
                'facebook' => 'facebookexternalhit',
                'twitter' => 'Twitterbot',
            ];
        } catch (Exception $e) {
            $this->logAndDisplayError($e);
        }
    }

    public function isMobile($userAgent = null, $httpHeaders = null)
    {
        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }
        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }

        try {
            if ($this->getUserAgent() === 'Amazon CloudFront') {
                $cfHeaders = $this->getCfHeaders();
                if (isset($cfHeaders['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER']) && $cfHeaders['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] === 'true') {
                    return true;
                }
            }

            $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

            if ($this->checkHttpHeadersForMobile()) {
                return true;
            }

            $uaLower = strtolower($this->useragent);
            foreach ($this->phoneDevices as $regex) {
                if ($this->match($regex, $uaLower)) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            $this->logAndDisplayError($e);
            return false;
        }
    }

    public function isTablet($userAgent = null, $httpHeaders = null)
    {
        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }
        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }

        try {
            if ($this->getUserAgent() === 'Amazon CloudFront') {
                $cfHeaders = $this->getCfHeaders();
                if (isset($cfHeaders['HTTP_CLOUDFRONT_IS_TABLET_VIEWER']) && $cfHeaders['HTTP_CLOUDFRONT_IS_TABLET_VIEWER'] === 'true') {
                    return true;
                }
            }

            $this->setDetectionType(self::DETECTION_TYPE_MOBILE);
            $uaLower = strtolower($this->useragent);
            foreach ($this->tabletDevices as $regex) {
                if ($this->match($regex, $uaLower)) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            $this->logAndDisplayError($e);
            return false;
        }
    }

    protected function match($regex, $ua)
    {
        try {
            $match = preg_match('/' . $regex . '/i', $ua) === 1;
            if ($match) {
                $this->matching_regex = $regex;
                $this->matches_array = [];
            }
            return $match;
        } catch (Exception $e) {
            $this->logAndDisplayError($e);
            return false;
        }
    }

protected function logAndDisplayError(Exception $e)
{
    $errorMessage = sprintf(
        "[%s] Hata: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $e->getMessage() ?: 'Bilinmeyen bir hata oluştu',
        $e->getFile(),
        $e->getLine()
    );
    error_log($errorMessage . PHP_EOL, 3, __DIR__ . '/error_log.txt');
    if (ini_get('display_errors')) {
        echo '<p style="color: black;">' . htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') . '</p>';
    }
}
	
    public function is($key, $userAgent = null, $httpHeaders = null)
    {
        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }
        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }

        try {
            $this->setDetectionType(self::DETECTION_TYPE_EXTENDED);
            $keyLower = strtolower($key);
            $allRules = array_merge(
                $this->phoneDevices,
                $this->tabletDevices,
                $this->operatingSystems,
                $this->browsers,
                $this->utilities
            );

            if (array_key_exists($keyLower, $allRules)) {
                return $this->match($allRules[$keyLower], strtolower($this->useragent));
            }
            return false;
        } catch (Exception $e) {
            $this->logAndDisplayError($e);
            return false;
        }
    }

    public function getUserAgent()
    {
        return $this->useragent;
    }

    protected function setUserAgent($userAgent)
    {
        $this->useragent = $userAgent;
    }

    protected function setHttpHeaders($httpHeaders)
    {
        $this->httpHeaders = $httpHeaders;
    }

    protected function getCfHeaders()
    {
        return $this->cloudfrontHeaders;
    }

    public function setDetectionType($type = null)
    {
        if ($type === null) {
            $type = self::DETECTION_TYPE_MOBILE;
        }
        if ($type !== self::DETECTION_TYPE_MOBILE && $type !== self::DETECTION_TYPE_EXTENDED) {
            return;
        }
        $this->detection_type = $type;
    }

    public function getMatchingRegex()
    {
        return $this->matching_regex;
    }

    public function getMatchesArray()
    {
        return $this->matches_array;
    }

    public static function getPhoneDevices()
    {
        return (new self())->phoneDevices;
    }

    public static function getTabletDevices()
    {
        return (new self())->tabletDevices;
    }

    public static function getUserAgents()
    {
        return self::getBrowsers();
    }

    public static function getBrowsers()
    {
        return (new self())->browsers;
    }

    public static function getUtilities()
    {
        return (new self())->utilities;
    }

    public static function getMobileDetectionRules()
    {
        $self = new self();
        return array_merge(
            $self->phoneDevices,
            $self->tabletDevices,
            $self->operatingSystems,
            $self->browsers
        );
    }

    public function getMobileDetectionRulesExtended()
    {
        return array_merge(
            $this->phoneDevices,
            $this->tabletDevices,
            $this->operatingSystems,
            $this->browsers,
            $this->utilities
        );
    }

    public function getRules()
    {
        if ($this->detection_type == self::DETECTION_TYPE_EXTENDED) {
            return $this->getMobileDetectionRulesExtended();
        } else {
            return self::getMobileDetectionRules();
        }
    }

    public static function getOperatingSystems()
    {
        return (new self())->operatingSystems;
    }

    public function checkHttpHeadersForMobile()
    {
        $mobileHeaders = [
            self::HEADER_X_WAP => ['matches' => null],
            self::HEADER_PROFILE => ['matches' => null],
            self::HEADER_ACCEPT => ['matches' => ['wap']],
        ];

        foreach ($mobileHeaders as $mobileHeader => $matchType) {
            if (isset($this->httpHeaders[$mobileHeader])) {
                if (is_array($matchType['matches'])) {
                    foreach ($matchType['matches'] as $_match) {
                        if ($_match && strpos($this->httpHeaders[$mobileHeader], $_match) !== false) {
                            return true;
                        }
                    }
                    return false;
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 2) !== 'is') {
            throw new BadMethodCallException("No such method exists: $name");
        }
        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);
        $key = substr($name, 2);
        return $this->matchUAAgainstKey($key);
    }

    protected function matchDetectionRulesAgainstUA($userAgent = null)
    {
        $ua = $userAgent ?: $this->useragent;
        foreach ($this->getRules() as $_regex) {
            if (empty($_regex)) {
                continue;
            }
            if ($this->match($_regex, $ua)) {
                return true;
            }
        }
        return false;
    }

    protected function matchUAAgainstKey($key)
    {
        $key = strtolower($key);
        if (!isset($this->cache[$key])) {
            $_rules = array_change_key_case($this->getRules());
            $this->cache[$key] = !empty($_rules[$key]) && $this->match($_rules[$key], $this->useragent);
        }
        return $this->cache[$key];
    }

    public function prepareVersionNo($ver)
    {
        $ver = str_replace(['_', ' ', '/'], '.', $ver);
        $arrVer = explode('.', $ver, 2);
        if (isset($arrVer[1])) {
            $arrVer[1] = str_replace('.', '', $arrVer[1]);
        }
        return (float) implode('.', $arrVer);
    }

    public function version($propertyName, $type = self::VERSION_TYPE)
    {
        if (empty($propertyName)) {
            return false;
        }
        if ($type !== self::VERSION_TYPE && $type !== 'float') {
            $type = self::VERSION_TYPE;
        }

        $properties = []; // Eksik, orijinalde tanımlı olmalı
        if (isset($properties[$propertyName])) {
            $properties[$propertyName] = (array) $properties[$propertyName];
            foreach ($properties[$propertyName] as $propertyMatchString) {
                $propertyPattern = str_replace('[VER]', '([0-9._]+)', $propertyMatchString);
                if (preg_match(sprintf('#%s#is', $propertyPattern), $this->useragent, $match) && !empty($match[1])) {
                    return $type === 'float' ? $this->prepareVersionNo($match[1]) : $match[1];
                }
            }
        }
        return false;
    }

    public function mobileGrade()
    {
        $isMobile = $this->isMobile();

        if (
            ($this->is('iOS') && $this->version('iPad', 'float') >= 4.3) ||
            ($this->is('iOS') && $this->version('iPhone', 'float') >= 4.3) ||
            ($this->is('iOS') && $this->version('iPod', 'float') >= 4.3) ||
            ($this->version('Android', 'float') > 2.1 && $this->is('Webkit')) ||
            ($this->version('Windows Phone OS', 'float') >= 7.5) ||
            ($this->is('BlackBerry') && $this->version('BlackBerry', 'float') >= 6.0) ||
            ($this->match('Playbook.*Tablet')) ||
            ($this->version('webOS', 'float') >= 1.4 && $this->match('Palm|Pre|Pixi')) ||
            ($this->match('hp.*TouchPad')) ||
            ($this->is('Firefox') && $this->version('Firefox', 'float') >= 18) ||
            ($this->is('Chrome') && $this->is('AndroidOS') && $this->version('Android', 'float') >= 4.0) ||
            ($this->is('Skyfire') && $this->version('Skyfire', 'float') >= 4.1 && $this->is('AndroidOS') && $this->version('Android', 'float') >= 2.3) ||
            ($this->is('Opera') && $this->version('Opera Mobi', 'float') >= 11.5 && $this->is('AndroidOS')) ||
            ($this->is('MeeGoOS')) ||
            ($this->is('Tizen')) ||
            ($this->is('Dolfin') && $this->version('Bada', 'float') >= 2.0) ||
            (($this->is('UC Browser') || $this->is('Dolfin')) && $this->version('Android', 'float') >= 2.3) ||
            ($this->match('Kindle Fire') || ($this->is('Kindle') && $this->version('Kindle', 'float') >= 3.0)) ||
            ($this->is('AndroidOS') && $this->is('NookTablet')) ||
            ($this->version('Chrome', 'float') >= 16 && !$isMobile) ||
            ($this->version('Safari', 'float') >= 5.0 && !$isMobile) ||
            ($this->version('Firefox', 'float') >= 10.0 && !$isMobile) ||
            ($this->version('IE', 'float') >= 7.0 && !$isMobile) ||
            ($this->version('Opera', 'float') >= 10 && !$isMobile)
        ) {
            return self::MOBILE_GRADE_A;
        }

        if (
            ($this->is('iOS') && $this->version('iPad', 'float') < 4.3) ||
            ($this->is('iOS') && $this->version('iPhone', 'float') < 4.3) ||
            ($this->is('iOS') && $this->version('iPod', 'float') < 4.3) ||
            ($this->is('Blackberry') && $this->version('BlackBerry', 'float') >= 5 && $this->version('BlackBerry', 'float') < 6) ||
            ($this->version('Opera Mini', 'float') >= 5.0 && $this->version('Opera Mini', 'float') <= 7.0 && ($this->version('Android', 'float') >= 2.3 || $this->is('iOS'))) ||
            ($this->match('NokiaN8|NokiaC7|N97.*Series60|Symbian/3')) ||
            ($this->version('Opera Mobi', 'float') >= 11 && $this->is('SymbianOS'))
        ) {
            return self::MOBILE_GRADE_B;
        }

        if (
            ($this->version('BlackBerry', 'float') <= 5.0) ||
            ($this->match('MSIEMobile|Windows CE.*Mobile') || $this->version('Windows Mobile', 'float') <= 5.2) ||
            ($this->is('iOS') && $this->version('iPad', 'float') <= 3.2) ||
            ($this->is('iOS') && $this->version('iPhone', 'float') <= 3.2) ||
            ($this->is('iOS') && $this->version('iPod', 'float') <= 3.2) ||
            ($this->version('IE', 'float') <= 7.0 && !$isMobile)
        ) {
            return self::MOBILE_GRADE_C;
        }

        return self::MOBILE_GRADE_C;
    }
}

$detect = new Mobile_Detect();
$isMobile = $detect->isMobile();
$userAgent = $detect->getUserAgent();

if ($isMobile) {
    echo "<p>Mobil cihaz algılandı. User-Agent: " . htmlspecialchars($userAgent) . "</p>";
} else {
    echo "<p>Mobil cihaz algılanmadı. User-Agent: " . htmlspecialchars($userAgent) . "</p>";
}