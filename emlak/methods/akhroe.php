<?php
// Bu sınıf, web sitesindeki farklı sayfalama seviyelerini yönetir (hem ajax hem normal)
class Pagination {

    // Basit Sayfalama Mantığı
    function simple_pagination_links(int $totalPages, int $totalLinks, int $selectedPage): array 
    {
        $arr = [];
        if ($totalPages < $totalLinks) {
            // Tüm sayfaları ekle
            for ($i = 1; $i <= $totalPages; $i++) {
                $arr[] = $i;
            }
        } else {
            $startIndex = $selectedPage;
            $lowerBound = $startIndex - (int)floor($totalLinks / 2);
            $upperBound = $startIndex + (int)floor($totalLinks / 2);

            // Alt sınır 1'den küçükse
            if ($lowerBound < 1) {
                $upperBound += (1 - $lowerBound);
                $lowerBound = 1;
            }

            // Üst sınır toplam sayfa sayısından büyükse
            if ($upperBound > $totalPages) {
                $lowerBound -= ($upperBound - $totalPages);
                $upperBound = $totalPages;
            }

            // Sayfa aralığını ekle
            for ($i = $lowerBound; $i <= $upperBound; $i++) {
                $arr[] = $i;
            }
        }
        return $arr;
    }
     
    // Gelişmiş Sayfalama Mantığı
    function advance_pagination_links(int $totalPages, int $selectedPage): array 
    {
        $arr = [];
        $lowerArr = [];
        $upperArr = [];

        $indexer = [4, 40, 50, 400, 500, 4000, 5000, 40000, 50000];
        $pattern = [1, 1, 1, 4, 40, 50, 400, 500, 4000, 5000, 40000];

        if ($selectedPage == 1) {
            // İlk sayfa için 15 bağlantı ekle
            $value = 0;
            for ($i = 1; $i <= 16; $i++) {
                $value = $i <= 7 ? $i : $value + $indexer[$i - 8];
                $value = min($value, $totalPages);
                if (!in_array($value, $arr)) {
                    $arr[] = $value;
                }
            }
        } elseif ($selectedPage > 1) {
            if ($totalPages <= 16) {
                // Tüm sayfaları ekle
                for ($i = 1; $i <= 16; $i++) {
                    $value = min($i, $totalPages);
                    if (!in_array($value, $arr)) {
                        $arr[] = $value;
                    }
                }
            } else {
                // Alt ve üst sınırları hesapla
                for ($i = 0; $i <= 7; $i++) {
                    $value = $selectedPage - $pattern[$i];
                    if ($value > 0 && !in_array($value, $lowerArr)) {
                        $lowerArr[] = $value;
                    }
                }
                $value = 0;
                for ($i = 0; $i <= 7; $i++) {
                    $value = $selectedPage + $pattern[$i];
                    if ($value > $totalPages) {
                        $value = $totalPages;
                    }
                    if (!in_array($value, $upperArr)) {
                        $upperArr[] = $value;
                    }
                }

                // Alt sınırları ekle
                for ($i = count($lowerArr) - 1; $i >= 0; $i--) {
                    $arr[] = $lowerArr[$i];
                }

                // Seçili sayfayı ekle
                $arr[] = $selectedPage;

                // Üst sınırları ekle
                foreach ($upperArr as $value) {
                    $arr[] = $value;
                }
            }
        }
        return $arr;
    }
}