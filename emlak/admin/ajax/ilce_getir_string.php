<?php
// il_adi parametresini güvenli bir şekilde al ve temizle
$il = $gvn->html_temizle($_GET["il_adi"]);

// İlçe dropdown için varsayılan seçenek
?><option value="">İlçe</option><?php

// Eğer il_adi boş ise hatayı logla ve işlemi sonlandır
if ($il == '') {
    error_log("İl adı boş. Tarih: " . date("Y-m-d H:i:s"));
    die('Hata: İl adı boş.');
}

// İl kontrolü ve hata yönetimi
try {
    $kontrol = $db->prepare("SELECT * FROM il WHERE il_adi=?");
    $kontrol->execute([$il]);

    if ($kontrol->rowCount() < 1) {
        error_log("Geçersiz il adı: $il. Tarih: " . date("Y-m-d H:i:s"));
        die('Hata: Geçersiz il adı.');
    }
    $il = $kontrol->fetch(PDO::FETCH_OBJ);

    // İlçeleri çek ve dropdown'a ekle
    $ilceler = $db->query("SELECT * FROM ilce WHERE il_id=" . $il->id . " ORDER BY ilce_adi ASC");
    while ($row = $ilceler->fetch(PDO::FETCH_OBJ)) {
        ?><option><?= htmlspecialchars($row->ilce_adi); ?></option><?php
    }

} catch (PDOException $e) {
    // Veritabanı hatasını logla ve site üzerinde göster
    error_log("Veritabanı hatası: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
    die('Hata: Veritabanı hatası.');
}