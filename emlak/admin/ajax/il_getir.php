<?php
// GET isteğinden ulke_id değerini güvenli bir şekilde al
$ulke_id = $gvn->rakam($_GET["ulke_id"]);

echo '<option value="">İl</option>';

// ulke_id boşsa işlemi sonlandır
if ($ulke_id == '') {
    die();
    exit;
}

// Veritabanında ülke kontrolü yap
$kontrol = $db->prepare("SELECT * FROM ulkeler_501 WHERE id=?");
$kontrol->execute([$ulke_id]);

// Eğer ülke bulunamazsa işlemi sonlandır
if ($kontrol->rowCount() < 1) {
    die();
    exit;
}
$ulke = $kontrol->fetch(PDO::FETCH_OBJ);

// Seçilen ülkeye ait illeri getir ve HTML olarak listele
try {
    $iller = $db->prepare("SELECT * FROM il WHERE ulke_id=? ORDER BY il_adi ASC");
    $iller->execute([$ulke->id]);
    while ($row = $iller->fetch(PDO::FETCH_OBJ)) {
        echo '<option value="' . htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row->il_adi, ENT_QUOTES, 'UTF-8') . '</option>';
    }
} catch (PDOException $e) {
    error_log($e->getMessage(), 3, '/var/log/php_errors.log');
    echo '<option value="">Bir hata oluştu.</option>';
}