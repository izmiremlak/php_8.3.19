<?php
// Gerekli fonksiyonları içe aktarır.
include "functions.php";

// Kullanıcı oturum kontrolü ve çıkış işlemi.
if ($hesap->id != '') {
    AccountLogOut();
}

// Kullanıcıyı ana sayfaya yönlendirir.
header("Location: index.php");
exit();