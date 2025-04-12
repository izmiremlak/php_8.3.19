<?php
// Kullanıcı kimliğini ve tipini kontrol et
if ($hesap->id != "" && $hesap->tipi != 0) {
    if ($_FILES) {
        // Resim yükleme işlemleri
        $resim1tmp = $_FILES['file']["tmp_name"];
        $resim1nm = $_FILES['file']["name"];
        $randnm = strtolower(substr(md5(uniqid(rand())), 0, 10)) . $fonk->uzanti($resim1nm);
        $resim = $fonk->resim_yukle(true, 'file', $randnm, '../uploads', $gorsel_boyutlari['markalar']['thumb_x'], $gorsel_boyutlari['markalar']['thumb_y']);
        $resim = $fonk->resim_yukle(false, 'file', $randnm, '../uploads', $gorsel_boyutlari['markalar']['orjin_x'], $gorsel_boyutlari['markalar']['orjin_y']);

        // Veritabanına ekleme
        $db->query("INSERT INTO markalar SET resim='$resim', dil='$dil' ");
    }
}