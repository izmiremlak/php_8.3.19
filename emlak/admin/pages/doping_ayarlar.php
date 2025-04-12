<?php
// Doping ayarları sayfası

// Veritabanı bağlantısı ve güvenlik işlemleri
require_once 'config.php'; // Veritabanı ve diğer ayarların yüklendiği dosya

// Hata loglama fonksiyonu
function logError($errorMessage) {
    error_log($errorMessage, 3, '/path/to/error.log');
}

function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Hata işleme
try {
    list($dzaman1a, $dzaman1b) = explode("|", $gayarlar->dzaman1);
    list($dzaman2a, $dzaman2b) = explode("|", $gayarlar->dzaman2);
    list($dzaman3a, $dzaman3b) = explode("|", $gayarlar->dzaman3);
} catch (Exception $e) {
    logError($e->getMessage());
    echo '<div class="alert alert-danger" role="alert">' . sanitizeInput($e->getMessage()) . '</div>';
}
?>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Doping Ayarları</h4>
            </div>
        </div>
        <div class="row">
            <style>
                .standartuyeliktable tr td {padding:10px;border-bottom:1px solid #eee;border-right:1px solid #eee;}
                .standartuyeliktable tr th {padding:10px;border-bottom:1px solid #eee;border-right:1px solid #eee;}
                .standartuyeliktable input {width:100px;float:left;}
                .standartuyeliktable select {width:100px;float:left;margin-left:5px;}
            </style>
            <!-- Col 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="alert alert-info" role="alert">Kullanıcıların ilan ekleme/düzenleme aşamalarında satın almak istediği doping özelliklerini ve ücretlerini tanımlayabilirsiniz.</div>
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=doping_ayarlar" onsubmit="return false;" enctype="multipart/form-data">
                            <table class="standartuyeliktable" width="100%" border="0" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Doping Adı</th>
                                        <th>
                                            <input type="text" class="form-control" name="dzaman1a" value="<?= sanitizeInput($dzaman1a); ?>">
                                            <select class="form-control" name="dzaman1b">
                                                <?php
                                                foreach ($periyod as $k => $v) {
                                                    echo '<option value="' . sanitizeInput($k) . '"' . ($dzaman1b == $k ? " selected" : '') . '>' . sanitizeInput($v) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </th>
                                        <th>
                                            <input type="text" class="form-control" name="dzaman2a" value="<?= sanitizeInput($dzaman2a); ?>">
                                            <select class="form-control" name="dzaman2b">
                                                <?php
                                                foreach ($periyod as $k => $v) {
                                                    echo '<option value="' . sanitizeInput($k) . '"' . ($dzaman2b == $k ? " selected" : '') . '>' . sanitizeInput($v) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </th>
                                        <th>
                                            <input type="text" class="form-control" name="dzaman3a" value="<?= sanitizeInput($dzaman3a); ?>">
                                            <select class="form-control" name="dzaman3b">
                                                <?php
                                                foreach ($periyod as $k => $v) {
                                                    echo '<option value="' . sanitizeInput($k) . '"' . ($dzaman3b == $k ? " selected" : '') . '>' . sanitizeInput($v) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = $db->query("SELECT * FROM doping_ayarlar_501 ORDER BY id ASC");
                                    while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <tr>
                                        <td><?= sanitizeInput($row->adi); ?></td>
                                        <td><input type="text" class="form-control" name="fiyat1[<?= sanitizeInput($row->id); ?>]" value="<?= sanitizeInput($gvn->para_str($row->fiyat1)); ?>" placeholder="Tutar"></td>
                                        <td><input type="text" class="form-control" name="fiyat2[<?= sanitizeInput($row->id); ?>]" value="<?= sanitizeInput($gvn->para_str($row->fiyat2)); ?>" placeholder="Tutar"></td>
                                        <td><input type="text" class="form-control" name="fiyat3[<?= sanitizeInput($row->id); ?>]" value="<?= sanitizeInput($gvn->para_str($row->fiyat3)); ?>" placeholder="Tutar"></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <br>
                            <div align="right">
                                <button type="submit" class="btn btn-purple waves-effect waves-light" onclick="AjaxFormS('forms','form_status');">Güncelle</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- Col1 end -->
        </div><!-- row end -->
    </div>
</div>

<script>
    var resizefunc = [];

    function sendFile(file, editor, welEditable) {
        // Resim gönderme işlemi burada yapılacak
    }
</script>
<script src="assets/js/admin.min.js"></script>
<link href="assets/plugins/notifications/notification.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css">
<link href="assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
<script src="assets/vendor/notifyjs/dist/notify.min.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<script src="assets/plugins/notifications/notifications.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script src="assets/vendor/summernote/dist/summernote.min.js"></script>
<script>
    jQuery(document).ready(function() {
        $('.wysihtml5').wysihtml5();

        $('.summernote').summernote({
            height: 200, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: true, // set focus to editable area after initializing summernote
            onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0], editor, welEditable);
            }
        });
    });
</script>
</body>
</html>