<?php
// Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
if ($hesap->id !== "" && $hesap->tipi !== 0) {

    // Dosya yükleme işlemi
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] !== '') {
        if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
            // Rastgele bir dosya adı oluştur ve dosya uzantısını al
            $name = strtolower(substr(md5(uniqid(rand())), 0, 13));
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = $name . '.' . $ext;
            $destination = '../uploads/editor/' . $filename;
            $location = $_FILES['file']['tmp_name'];

            // Dosyayı belirtilen yere taşı
            if (move_uploaded_file($location, $destination)) {
                echo SITE_URL . 'uploads/editor/' . $filename;
            } else {
                // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
                $errorMessage = 'Dosya yüklenirken bir hata oluştu.';
                error_log($errorMessage, 3, '/var/log/php_errors.log');
                echo $errorMessage;
            }
        } else {
            // Yükleme hatasını log dosyasına yaz ve kullanıcıya göster
            $errorMessage = 'Ooops! Yükleme işlemi sırasında şu hata oluştu: ' . $_FILES['file']['error'];
            error_log($errorMessage, 3, '/var/log/php_errors.log');
            echo $errorMessage;
        }
    }
}