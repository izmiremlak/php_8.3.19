<?php

declare(strict_types=1); // PHP 8.3 için sıkı tip kontrolü

// Hata raporlama ayarları: Tüm hataları göster ve logla
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Hata loglama ayarları: Hataları dosyaya kaydet
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../../logs/error_log.txt');

// Özel hata işleyici: Hataları yakalar ve hem loga hem ekrana yazar
if (!function_exists('customErrorHandler')) {
    /**
     * Hataları hem loga yazar hem ekranda gösterir
     *
     * @param int $errno Hata numarası
     * @param string $errstr Hata mesajı
     * @param string|null $errfile Hata dosyası
     * @param int|null $errline Hata satırı
     * @return bool Hata işlendi mi
     */
    function customErrorHandler(int $errno, string $errstr, ?string $errfile = null, ?int $errline = null): bool
    {
        $errorMessage = "[$errno] $errstr - Dosya: " . ($errfile ?? 'bilinmiyor') . " - Satır: " . ($errline ?? 'bilinmiyor');
        error_log($errorMessage);
        echo "<div style='color: red;'><b>Hata:</b> $errorMessage</div>";
        return true;
    }
}
set_error_handler('customErrorHandler');

// SEO meta etiketleri: Mevcut mantık korunarak optimize edildi
$metaDescription = htmlspecialchars("Genel Ayarlar - Site Yönetimi ve Ayarları", ENT_QUOTES, 'UTF-8');
echo "<meta name='description' content='$metaDescription'>";
echo "<meta name='robots' content='noindex, nofollow'>"; // Admin sayfası indexlenmesin
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<meta charset='UTF-8'>";

?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Genel Ayarlar</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs tabs">
                    <li class="active tab">
                        <a href="#tab1" data-toggle="tab" aria-expanded="true">
                            <span class="hidden-xs">Site Ayarları</span>
                        </a>
                    </li>
                    <li class="tab">
                        <a href="#tab2" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Site Bilgileri</span>
                        </a>
                    </li>
                    <li class="tab">
                        <a href="#tab3" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Site SEO</span>
                        </a>
                    </li>
                    <li class="tab">
                        <a href="#tab4" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">İletişim Ayarları</span>
                        </a>
                    </li>
                    <li class="tab">
                        <a href="#tab5" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Sosyal Medya Ayarları</span>
                        </a>
                    </li>
                    <li class="tab">
                        <a href="#tab6" data-toggle="tab" aria-expanded="false">
                            <span class="hidden-xs">Mail Ayarları</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
					
<!-- Tab içerikleri -->>
					<div class="tab-pane active" id="tab1">
                        <div id="tab1_status"></div>
                        <!-- Site ayarları formu: AJAX ile güncelleme yapacak -->
                        <form role="form" class="form-horizontal" id="tab1_form" method="POST" action="ajax.php?p=gayarlar" onsubmit="return false;" enctype="multipart/form-data">
                            <!-- Üst Logo Yükleme Alanı -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Üst Logo Yükle</h3>
                                    </div>
                                    <div class="panel-body" style="height:150px;">
                                        <div class="form-group">
                                            <label for="logo" class="col-sm-3 control-label">Logo</label>
                                            <span>Logo en fazla 230px - 70px olmalı</span>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" id="logo" name="logo" style="width:210px;" accept="image/*">
                                                <br />
                                                <img src="../uploads/thumb/<?php echo htmlspecialchars($gayarlar->logo ?? '', ENT_QUOTES, 'UTF-8'); ?>" id="logo_src" height="50" alt="Üst Logo" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Logo Yükleme Alanı -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Footer(Alt) Logo Yükle</h3>
                                    </div>
                                    <div class="panel-body" style="height:150px;">
                                        <div class="form-group">
                                            <label for="footer_logo" class="col-sm-3 control-label">Footer(Alt) Logo</label>
                                            <span>Logo en fazla 210px - 50px olmalı</span>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control" id="footer_logo" name="footer_logo" style="width:210px;" accept="image/*">
                                                <br />
                                                <img style="background:#ccc;" src="../uploads/thumb/<?php echo htmlspecialchars($gayarlar->footer_logo ?? '', ENT_QUOTES, 'UTF-8'); ?>" id="footer_logo_src" height="50" alt="Footer Logo" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Site Durumu -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Site Durumu</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Site Durumu</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="site_durum">
                                                    <option value="1" <?php echo ($gayarlar->site_durum ?? 0) == 1 ? 'selected' : ''; ?>>Açık</option>
                                                    <option value="0" <?php echo ($gayarlar->site_durum ?? 0) == 0 ? 'selected' : ''; ?>>Kapalı</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- WWW Kullanımı -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">WWW Kullanımı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">WWW Kullanımı</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="site_www">
                                                    <option value="1" <?php echo ($gayarlar->site_www ?? 0) == 1 ? 'selected' : ''; ?>>Açık</option>
                                                    <option value="0" <?php echo ($gayarlar->site_www ?? 0) == 0 ? 'selected' : ''; ?>>Kapalı</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SSL Kullanımı -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">SSL Kullanımı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">SSL Kullanımı</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="site_ssl">
                                                    <option value="1" <?php echo ($gayarlar->site_ssl ?? 0) == 1 ? 'selected' : ''; ?>>Açık</option>
                                                    <option value="0" <?php echo ($gayarlar->site_ssl ?? 0) == 0 ? 'selected' : ''; ?>>Kapalı</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Güncelleme Butonu -->
                            <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('tab1_form', 'tab1_status');">Güncelle</button>
                        </form>
                    </div>
					                    <div class="tab-pane" id="tab2">
                        <div id="tab2_status"></div>
                        <!-- Site bilgileri formu: AJAX ile güncelleme yapacak -->
                        <form role="form" class="form-horizontal" id="tab2_form" method="POST" action="ajax.php?p=gayarlar" onsubmit="return false;">
                            <!-- Site Başlığı -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Site Başlığı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Site Başlığı</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="site_baslik" value="<?php echo htmlspecialchars($gayarlar->site_baslik ?? '', ENT_QUOTES, 'UTF-8'); ?>" maxlength="255">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Site Açıklaması -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Site Açıklaması</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Site Açıklaması</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="site_aciklama" rows="3"><?php echo htmlspecialchars($gayarlar->site_aciklama ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Site Anahtar Kelimeleri -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Site Anahtar Kelimeleri</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Anahtar Kelimeler</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="site_keywords" value="<?php echo htmlspecialchars($gayarlar->site_keywords ?? '', ENT_QUOTES, 'UTF-8'); ?>" maxlength="255">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Site Sahibi -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Site Sahibi</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Site Sahibi</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="site_sahibi" value="<?php echo htmlspecialchars($gayarlar->site_sahibi ?? '', ENT_QUOTES, 'UTF-8'); ?>" maxlength="255">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Güncelleme Butonu -->
                            <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('tab2_form', 'tab2_status');">Güncelle</button>
                        </form>
                    </div>

					                    <div class="tab-pane" id="tab3">
                        <div id="tab3_status"></div>
                        <!-- Site SEO formu: AJAX ile güncelleme yapacak -->
                        <form role="form" class="form-horizontal" id="tab3_form" method="POST" action="ajax.php?p=gayarlar" onsubmit="return false;">
                            <!-- SEO Başlığı -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">SEO Başlığı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">SEO Başlığı</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="seo_baslik" value="<?php echo htmlspecialchars($gayarlar->seo_baslik ?? '', ENT_QUOTES, 'UTF-8'); ?>" maxlength="70">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Açıklaması -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">SEO Açıklaması</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">SEO Açıklaması</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="seo_aciklama" rows="3"><?php echo htmlspecialchars($gayarlar->seo_aciklama ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Anahtar Kelimeler -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">SEO Anahtar Kelimeler</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Anahtar Kelimeler</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="seo_keywords" value="<?php echo htmlspecialchars($gayarlar->seo_keywords ?? '', ENT_QUOTES, 'UTF-8'); ?>" maxlength="255">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Güncelleme Butonu -->
                            <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('tab3_form', 'tab3_status');">Güncelle</button>
                        </form>
                    </div>

					                    <div class="tab-pane" id="tab4">
                        <div id="tab4_status"></div>
                        <!-- İletişim ayarları formu: AJAX ile güncelleme yapacak -->
                        <form role="form" class="form-horizontal" id="tab4_form" method="POST" action="ajax.php?p=gayarlar" onsubmit="return false;">
                            <!-- Telefon Numarası -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Telefon Numarası</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Telefon</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="telefon" value="<?php echo htmlspecialchars($gayarlar->telefon ?? '', ENT_QUOTES, 'UTF-8'); ?>" maxlength="20">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- E-posta Adresi -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">E-posta Adresi</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">E-posta</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($gayarlar->email ?? '', ENT_QUOTES, 'UTF-8'); ?>" maxlength="255">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Adres -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Adres</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Adres</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="adres" rows="3"><?php echo htmlspecialchars($gayarlar->adres ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Güncelleme Butonu -->
                            <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('tab4_form', 'tab4_status');">Güncelle</button>
                        </form>
                    </div>

					                    <div class="tab-pane" id="tab5">
                        <div id="tab5_status"></div>
                        <!-- Sosyal medya ayarları formu: AJAX ile güncelleme yapacak -->
                        <form role="form" class="form-horizontal" id="tab5_form" method="POST" action="ajax.php?p=gayarlar" onsubmit="return false;">
                            <!-- Facebook -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Facebook</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Facebook URL</label>
                                            <div class="col-sm-9">
                                                <input type="url" class="form-control" name="facebook" value="<?php echo htmlspecialchars($gayarlar->facebook ?? '', ENT_QUOTES, 'UTF-8'); ?>" maxlength="255">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Twitter -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Twitter</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                            
											                    <div class="tab-pane" id="tab6">
                        <div id="tab6_status"></div>
                        <!-- Mail ayarları formu: AJAX ile güncelleme yapacak -->
                        <form role="form" class="form-horizontal" id="tab6_form" method="POST" action="ajax.php?p=gayarlar" onsubmit="return false;">
                            <!-- Mail Sunucusu -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Mail Sunucusu</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="mail_sunucu" class="col-sm-3 control-label">Sunucu</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="mail_sunucu" name="mail_sunucu" value="<?php echo htmlspecialchars($gayarlar->mail_sunucu ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mail Portu -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Mail Portu</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="mail_port" class="col-sm-3 control-label">Port</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="mail_port" name="mail_port" value="<?php echo htmlspecialchars($gayarlar->mail_port ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mail Kullanıcı Adı -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Mail Kullanıcı Adı</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="mail_kullanici" class="col-sm-3 control-label">Kullanıcı Adı</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="mail_kullanici" name="mail_kullanici" value="<?php echo htmlspecialchars($gayarlar->mail_kullanici ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mail Şifresi -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Mail Şifresi</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="mail_sifre" class="col-sm-3 control-label">Şifre</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" id="mail_sifre" name="mail_sifre" value="<?php echo htmlspecialchars($gayarlar->mail_sifre ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Güncelleme Butonu -->
                            <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('tab6_form', 'tab6_status');">Güncelle</button>
                        </form>
                    </div>
                    <!-- Dosya burada bitiyor, kapanış etiketleri aşağıda -->
                </div>
            </div>
        </div>
    </div>
</div>

											
					
