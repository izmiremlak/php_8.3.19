<?php
// admin/logout.php

// Gerekli dosyaların dahil edilmesi
include "../functions.php";

// Kullanıcı giriş kontrolü ve çıkış işlemi
if ($hesap->id != "") {
    AccountLogOut();
}

// Kullanıcıyı giriş sayfasına yönlendirme
header("Location:login.php");