<?php
// Hata raporlama ve log dosyası ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php-error.log');

// Dosya yükleme işlemi varsa işlemleri başlatıyoruz
if ($_FILES) {
    if (!empty($hesap->id) && $hesap->tipi != 0) {

        // Dosyaları dizi halinde tutuyoruz
        $resimler = [
            'resim1', 'resim2', 'resim3', 'resim4', 'resim5', 'resim6',
            'resim7', 'resim8', 'resim9', 'resim10', 'resim11', 'resim12', 'resim13'
        ];

        // Dosyaları döngü ile işliyoruz
        foreach ($resimler as $resim) {
            if (!empty($_FILES[$resim]['tmp_name'])) {
                $resimTmp = $_FILES[$resim]['tmp_name'];
                $resimNm = $_FILES[$resim]['name'];
                $randNm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resimNm);

                // Resim yükleme işlemi
                $yuklenenResim = $fonk->resim_yukle(false, $resim, $randNm, '../uploads', $gorsel_boyutlari['headerbg']['orjin_x'], $gorsel_boyutlari['headerbg']['orjin_y']);
                if ($yuklenenResim) {
                    // Veritabanı güncelleme işlemi
                    $kolonIsmi = $resim . '_resim';
                    $sorgu = "UPDATE gayarlar_501 SET $kolonIsmi = '$yuklenenResim' ";
                    $avgn = $db->query($sorgu);

                    if ($avgn) {
                        $fonk->ajax_tamam(ucfirst($resim) . ' Arkaplan Görseli Güncellendi');
                        ?>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $('#<?= $resim ?>_src').attr("src", "../uploads/<?= $yuklenenResim; ?>");
                            });
                        </script>
                        <?php
                    } else {
                        $fonk->ajax_hata(ucfirst($resim) . ' Arkaplan Görseli Güncellenemedi. Bir hata oluştu!');
                    }
                } else {
                    $fonk->ajax_hata(ucfirst($resim) . ' Arkaplan Görseli Güncellenemedi. Bir hata oluştu!');
                }
            }
        }
    }
}