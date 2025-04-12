<?php
// Hata loglama fonksiyonu
function logError($errorMessage) {
    error_log($errorMessage, 3, '/path/to/error.log');
}

// Kullanıcıdan gelen veriyi temizleme fonksiyonu
function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Foto Galeri</h4>
            </div>
        </div>
        <form action="" method="POST" id="SelectForm">
            <input type="hidden" name="action" value="" id="action_hidden">
            <div class="row">
                <div class="col-lg-12 col-md-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="btn-toolbar" role="toolbar">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info waves-effect waves-light w-lg m-b-5" onclick="window.location.href='index.php?p=foto_galeri_ekle';">
                                        <i class="fa fa-plus"></i> Yeni Ekle
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary waves-effect waves-light dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Seçilenlere Uygula
                                        <ul class="dropdown-menu">
                                            <li><a href="#" onclick="TumuSil();">Seçilenleri Sil</a></li>
                                        </ul>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default m-t-20">
                        <div class="panel-body">
                            <?php
                            // Kullanıcı tipi kontrolü
                            if ($hesap->tipi != 2) {
                                $sil = $gvn->rakam($_GET["sil"]);
                                if ($sil != "") {
                                    // Silme işlemi
                                    $db->query("DELETE FROM kategoriler_501 WHERE id=" . $sil);
                                    header("Location:index.php?p=foto_galeri");
                                }
                                if ($_POST) {
                                    $idler = $_POST["id"];
                                    $action = $_POST["action"];
                                    if (count($idler) > 0) {
                                        foreach ($idler as $id) {
                                            $id = $gvn->rakam($id);
                                            if ($action == 'sil') {
                                                // Seçilenleri silme işlemi
                                                $db->query("DELETE FROM kategoriler_501 WHERE id=" . $id);
                                            }
                                        } // FOREACH END
                                    } // eğer varsa
                                    header("Location:index.php?p=foto_galeri");
                                }
                            } // tipi 0 değilse
                            ?>
                            <table class="table table-hover mails datatable">
                                <thead>
                                    <tr>
                                        <th>Seç</th>
                                        <th>Başlık</th>
                                        <th style="display:none">Url Adresi</th>
                                        <th>Sıra</th>
                                        <th>Tarih</th>
                                        <th>Kontroller</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sorgu = $db->query("SELECT * FROM kategoriler_501 WHERE tipi=1 AND dil='" . $dil . "' ORDER BY id DESC LIMIT 0,500");
                                    while ($row = $sorgu->fetch(PDO::FETCH_OBJ)) {
                                        ?>
                                        <tr>
                                            <td class="mail-select">
                                                <div class