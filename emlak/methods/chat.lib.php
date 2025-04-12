<?php 
if(!defined("SERVER_HOST")) { 
    die(); 
}

// Kullanıcı ID'si kontrolü
if(strlen($uid) > 11) {
    $uid = 0;
}

if($uid == $bid) {
    $uid = 0;
}

// Kullanıcı bilgilerini kontrol et ve yükle
if($uid != 0 && $uid != $bid) {
    $uyeKontrol = $db->prepare("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id = ?");
    $uyeKontrol->execute([$uid]);

    if($uyeKontrol->rowCount() > 0) {
        $uye = $uyeKontrol->fetch(PDO::FETCH_OBJ);
        $uyavatar = empty($uye->avatar) || $uye->avatard == 1 ? 'uploads/default-avatar.png' : 'uploads/thumb/' . $uye->avatar;
        $uyturu = $uturu[$uye->turu];

        $uyadsoyad = $uye->adi;
        $uyadsoyad .= !empty($uye->soyadi) ? ' ' . $uye->soyadi : '';
        $uyadsoyad = !empty($uye->unvan) ? $uye->unvan : $uyadsoyad;

        $uyeProLink = "profil/" . (empty($uye->nick_adi) ? $uye->id : $uye->nick_adi);

        $KarsiEngel = 0; // Karşı Taraf Engeli
        $BenEngel = 0; // Benim Taraf Engeli
        $ilkSohbet = 1; // İlk kez birbiriyle sohbet edeceklerse
        $isileti = 0; // İletisi var mı? 0 yok, 1 var.

        // Karşı taraf engellemiş mi kontrol ediyoruz
        $kEngelKont = $db->prepare("SELECT id, tarih FROM engelli_kisiler_501 WHERE kim = ? AND kimi = ?");
        $kEngelKont->execute([$uid, $bid]);
        if($kEngelKont->rowCount() > 0) {
            $KarsiEngel = 1;
        }

        // Benim taraf engellemiş mi kontrol ediyoruz
        $bEngelKont = $db->prepare("SELECT id, tarih FROM engelli_kisiler_501 WHERE kim = ? AND kimi = ?");
        $bEngelKont->execute([$bid, $uid]);
        if($bEngelKont->rowCount() > 0) {
            $BenEngel = 1;
        }

        // Yeni mi mesajlaşacaklar kontrol ediyoruz
        $MesajLine = $db->prepare("SELECT * FROM mesajlar_501 WHERE (kimden = :bana AND kime = :ona) OR (kimden = :ona AND kime = :bana)");
        $MesajLine->execute(['bana' => $bid, 'ona' => $uid]);
        if($MesajLine->rowCount() > 0) {
            $MesajLine = $MesajLine->fetch(PDO::FETCH_OBJ);
            $ilkSohbet = 0;

            $mesajiler = $db->query("SELECT DISTINCT mi.id FROM mesaj_iletiler_501 AS mi INNER JOIN mesajlar_501 AS mr ON mi.mid = mr.id WHERE mi.mid = " . $MesajLine->id . " AND ((mi.as LIKE '" . $bid . "/%') OR (mi.as LIKE '%/" . $bid . "/%'))");
            if($mesajiler->rowCount() > 0) {
                $isileti = 1;
            }
        }

        // Bildirim balonunu güncelliyoruz
        if($ilkSohbet == 0) {
            $db->query("UPDATE mesaj_iletiler_501 SET durum = '1' WHERE mid = " . $MesajLine->id . " AND gid != " . $bid . " AND durum = 0");
        }
    } else {
        $uid = 0;
    }
}