<?php
// Kullanıcı kimliğini ve tipini kontrol et
if ($hesap->id != "" && $hesap->tipi != 0) {
    $sil = $gvn->rakam($_GET["sil"]);
    $onayla = $gvn->rakam($_GET["onayla"]);

    if ($sil != "") {
        // Paket silme işlemi
        $db->query("DELETE FROM upaketler_501 WHERE id=" . $sil);
        ?>
        <script type="text/javascript">
        $(document).ready(function(){
            $("#upaket<?=$sil;?>").fadeOut(500, function(){
                $("#upaket<?=$sil;?>").remove();
            });
        });
        </script>
        <?
        $fonk->ajax_tamam("Paket Silindi");
    } elseif ($onayla != "") {
        // Paket onaylama işlemi
        $paket = $db->query("SELECT * FROM upaketler_501 WHERE id=" . $onayla)->fetch(PDO::FETCH_OBJ);
        $hesapp = $db->query("SELECT * FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND id=" . $paket->acid)->fetch(PDO::FETCH_OBJ);

        $adsoyad = $hesapp->adi;
        $adsoyad .= ($hesapp->soyadi != '') ? ' ' . $hesapp->soyadi : '';
        $adsoyad = ($hesapp->unvan != '') ? $hesapp->unvan : $adsoyad;
        $baslik = $paket->adi . " " . dil("PAY_NAME2");

        $fiyat = $gvn->para_str($paket->tutar) . " " . dil("UYELIKP_PBIRIMI");
        $neresi = "paketlerim";

        $fonk->bildirim_gonder([$adsoyad, $hesapp->email, $hesapp->parola, $baslik, $fiyat, date("d.m.Y H:i", strtotime($fonk->datetime())), SITE_URL . $neresi], "siparis_onaylandi", $hesapp->email);

        $db->query("UPDATE upaketler_501 SET durum='1' WHERE id=" . $onayla);
        ?>
        <script type="text/javascript">
        $(document).ready(function(){
            $("#upaket<?=$onayla;?>_durum").html('<strong style="color:green">Onaylandı</strong>');
        });
        </script>
        <?
    }
}