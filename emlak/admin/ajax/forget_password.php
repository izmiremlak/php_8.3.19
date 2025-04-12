<?php
// POST isteği olup olmadığını kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kullanıcının giriş yapıp yapmadığını ve doğru tipte olup olmadığını kontrol et
    if ($hesap->id === "") {
        // Çerez kontrolü
        $cerez = $_COOKIE["parola_hatirlat"] ?? '';
        if (!empty($cerez)) {
            die($fonk->bilgi("Az önce bildirim yapıldı. 15 dk. kadar bekleyin."));
        }

        // E-posta doğrulama ve temizleme
        $email = $gvn->eposta($_POST["email"]);
        if (empty($email)) {
            die($fonk->uyari("Kayıtlı e-posta adresinizi girin."));
        }
        if (!$gvn->eposta_kontrol($email)) {
            die($fonk->hata("Geçersiz E-Posta formatı"));
        }

        // Veritabanında e-posta kontrolü
        $stmt = $db->prepare("SELECT email, parola, id FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND email = :eposta AND tipi = 1");
        $stmt->execute(['eposta' => $email]);

        if ($stmt->rowCount() > 0) {
            $hesap = $stmt->fetch(PDO::FETCH_OBJ);

            // Parola hatırlatma e-postası gönderme
            $message = 'Yönetici Giriş Parolanız: <strong>' . htmlspecialchars($hesap->parola, ENT_QUOTES, 'UTF-8') . '</strong>';
            $gonder = $fonk->mail_gonder('Yönetim Parola Hatırlatma', $hesap->email, $message);

            if ($gonder) {
                $fonk->tamam("Parola Bilgileriniz E-posta Adresinize Gönderildi.");
                setcookie("parola_hatirlat", $email, time() + 60 * 15, "/");
            } else {
                // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
                $errorMessage = "Mail gönderilemedi!";
                error_log($errorMessage, 3, '/var/log/php_errors.log');
                die($fonk->hata($errorMessage));
            }
        } else {
            // Hata durumunda hatayı log dosyasına yaz ve kullanıcıya göster
            $errorMessage = "Bu e-posta sistemimizdeki ile uyuşmuyor! <br/>Sistem yöneticiniz ile irtibat sağlayınız.";
            error_log($errorMessage, 3, '/var/log/php_errors.log');
            die($fonk->hata($errorMessage));
        }
    }
}