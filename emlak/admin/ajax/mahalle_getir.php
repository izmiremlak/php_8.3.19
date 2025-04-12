<?php
// ilce_id parametresini güvenli bir şekilde al ve sayıya çevir
$ilce = $gvn->rakam($_GET["ilce_id"]);

// Mahalle dropdown için varsayılan seçenek
?><option value=""><?= htmlspecialchars(dil("TX264")); ?></option><?php

// Eğer ilce_id boş ise hatayı logla ve işlemi sonlandır
if ($ilce == '') {
    error_log("İlçe ID boş. Tarih: " . date("Y-m-d H:i:s"));
    die('Hata: İlçe ID boş.');
}

// İlçe kontrolü ve hata yönetimi
try {
    $kontrol = $db->prepare("SELECT * FROM ilce WHERE id=?");
    $kontrol->execute([$ilce]);

    if ($kontrol->rowCount() < 1) {
        error_log("Geçersiz ilçe ID: $ilce. Tarih: " . date("Y-m-d H:i:s"));
        die('Hata: Geçersiz ilçe ID.');
    }
    $ilce = $kontrol->fetch(PDO::FETCH_OBJ);

    // Semtleri ve Mahalleleri çek ve dropdown'a ekle
    $semtler = $db->query("SELECT * FROM semt WHERE ilce_id=" . $ilce->id . " ORDER BY semt_adi ASC");

    if ($semtler->rowCount() > 0) {
        while ($srow = $semtler->fetch(PDO::FETCH_OBJ)) {
            $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE semt_id=" . $srow->id . " AND ilce_id=" . $ilce->id . " ORDER BY mahalle_adi ASC");
            if ($mahalleler->rowCount() > 0) {
                ?><optgroup label="<?= htmlspecialchars($srow->semt_adi); ?>"><?php
                while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
                    ?><option value="<?= htmlspecialchars($row->id); ?>"><?= htmlspecialchars($row->mahalle_adi); ?></option><?php
                }
                ?></optgroup><?php
            }
        }
    } else {
        $mahalleler = $db->query("SELECT * FROM mahalle_koy WHERE ilce_id=" . $ilce->id . " ORDER BY mahalle_adi ASC");
        while ($row = $mahalleler->fetch(PDO::FETCH_OBJ)) {
            ?><option value="<?= htmlspecialchars($row->id); ?>"><?= htmlspecialchars($row->mahalle_adi); ?></option><?php
        }
    }

} catch (PDOException $e) {
    // Veritabanı hatasını logla ve site üzerinde göster
    error_log("Veritabanı hatası: " . $e->getMessage() . ". Tarih: " . date("Y-m-d H:i:s"));
    die('Hata: Veritabanı hatası.');
}