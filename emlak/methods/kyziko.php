<?php
/**
 * pagenate Class
 * 
 * Bu sınıf, sayfalama işlemlerini gerçekleştirmek için kullanılır.
 * PHP 8.3.17 özelliklerini kullanarak yeniden düzenlenmiştir.
 */

class pagenate {

    /**
     * SQL sorgusunu çalıştırır ve sayfalama bilgilerini döndürür.
     *
     * @param string $sql
     * @param int $git
     * @param int $limit
     * @param array|bool $execute
     * @return array
     */
    function sql_query(string $sql, int $git, int $limit, array|bool $execute = false): array {
        global $db, $gvn;

        // Güvenlik kontrolleri
        $git = (strlen((string)$git) >= 11) ? 1 : $git;
        $git = $gvn->rakam($git);
        $limit = $gvn->rakam($limit);

        if (!is_numeric($git) || $git <= 0) {
            $git = 1;
        }

        // SQL sorgusunu hazırla ve çalıştır
        if (is_array($execute)) {
            $qrr = $db->prepare($sql);
            $qrr->execute($execute);
        } else {
            $qrr = $db->query($sql);
        }

        // Sayfa bilgilerini hesapla
        $count = $qrr->rowCount();
        $toplamsayfa = ceil($count / $limit);
        $baslangic = ($git - 1) * $limit;
        $basdan = ($git - 5 < 1) ? 1 : $git - 3;
        $kadar = ($git + 5 > $toplamsayfa) ? $toplamsayfa : $git + 3;
        $git = max(1, min($git, $toplamsayfa));

        return [
            'sql' => $sql . ' LIMIT ' . $baslangic . ',' . $limit . ' ',
            'basdan' => $basdan,
            'kadar' => $kadar,
            'baslangic' => $baslangic,
            'toplam' => $count
        ];
    }

    /**
     * Sayfalama linklerini oluşturur ve görüntüler.
     *
     * @param string $base_url
     * @param int $git
     * @param int $basdan
     * @param int $kadar
     * @param string $active_class
     * @param array $sorgu
     */
    function listele(string $base_url, int $git, int $basdan, int $kadar, string $active_class, array $sorgu): void {
        global $gvn;
        $git = $gvn->rakam($git);

        if ($git === '' || $git === 0) {
            $git = 1;
        }

        if ($kadar > 0) {
            ?>
            <span><a href="<?= htmlspecialchars($base_url) . ($git - 1); ?>"><?= (dil('PGN2') === '') ? 'Önceki' : dil('PGN2');?></a></span>
            <?php
            for ($i = $basdan; $i <= $kadar; $i++) {
                if ($i !== '') {
                    ?>
                    <span <?php if ($git == $i) { echo $active_class; } ?>><a href="<?= htmlspecialchars($base_url) . $i; ?>"><?= $i; ?></a></span> 
                    <?php
                }
            }
            ?>
            <span><a href="<?= htmlspecialchars($base_url) . ($git + 1 > $kadar ? $kadar : $git + 1); ?>"><?= (dil('PGN3') === '') ? 'Sonraki' : dil('PGN3');?></a></span> 
            <?php
        } // EĞER VARSA LİSTELENECEK ÖĞE
    } // fonksiyon kapanışı <MTQ4Mw==>
}