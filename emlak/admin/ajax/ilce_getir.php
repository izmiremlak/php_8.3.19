<?php
// Güvenlik önlemleri - il_id parametresini güvenli şekilde al
$il = $gvn->rakam($_GET["il_id"]);

// İlçe dropdown için varsayılan seçenek
?><option value="">İlçe</option><?php

// Eğer il_id boş ise işlemi sonlandır
if ($il == '') {
    error_log("İl ID boş. Tarih: " . date("Y-m-d H:i:s"));
    die('Hata: İl ID boş.');
}

// İl kontrolü
try {
    $kontrol = $db->prepare("SELECT * FROM il WHERE id=?");
    $kontrol->execute([$il]);

    if ($kontrol->rowCount() < 1) {
        error_log("Geçersiz il ID: $il. Tarih: " . date("Y-m-d H:i:s"));
        die('Hata: Geçersiz il ID.');
    }
    $il = $kontrol->fetch(PDO::FETCH_OBJ);

    // İlçeleri çekme ve dropdown'a ekleme
    $ilceler = $db->query("SELECT * FROM ilce WHERE il_id=" . $il->id . " ORDER BY ilce_adi ASC");
    while ($row = $ilceler->fetch(PDO::FETCH_OBJ)) {
        ?><option value="<?= htmlspecialchars($row->id); ?>"><?= htmlspecialchars($row->ilce_adi); ?></option><?php
    }

} catch (PDOException $e) {
    error_log("Veritabanı hatası: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
    die('Hata: Veritabanı hatası.');
}