<?php
$turler = explode(",", dil("UYELIK_TURLERI"));
?>
<div class="content">
    <div class":container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">Yeni Üye Ekle</h4>
            </div>
        </div>
        <div class="row">
            <!-- Col 1 -->
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div id="form_status"></div>
                        <form role="form" class="form-horizontal" id="forms" method="POST" action="ajax.php?p=uye_ekle" onsubmit="return false;" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Üyelik Türü</label>
                                <div class="col-sm-9">
                                    <?php foreach ($turler as $k => $v) { ?>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="turu_<?=htmlspecialchars($k, ENT_QUOTES, 'UTF-8');?>" value="<?=htmlspecialchars($k, ENT_QUOTES, 'UTF-8');?>" name="turu">
                                            <label for="turu_<?=htmlspecialchars($k, ENT_QUOTES, 'UTF-8');?>"><?=htmlspecialchars($v, ENT_QUOTES, 'UTF-8');?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Otomatik Onay</label>
                                <div class="col-sm-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="ilan_aktifet_check" type="checkbox" name="ilan_aktifet" value="1">
                                        <label for="ilan_aktifet_check"><strong>Aktif</strong></label>
                                        <span style="font-size:14px;">(Eklenen ilanlar otomatik onaylanarak yayınlanır.)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group danisman">
                                <label for="adsoyad" class="col-sm-3 control-label">Bağlı olduğu kurumsal</label>
                                <div class="col-sm-9">
                                    <select name="kid" class="form-control">
                                        <option value="0">Seçiniz</option>
                                        <?php
                                        $smt = $db->query("SELECT id, unvan, concat_ws(' ', adi, soyadi) AS adsoyad FROM hesaplar WHERE (site_id_555=501 OR site_id_888=100 OR site_id_777=501501 OR site_id_699=200 OR site_id_701=501501 OR site_id_702=300) AND turu=1 ORDER BY id DESC")->fetchAll(PDO::FETCH_OBJ);
                                        foreach ($smt as $row) {
                                            ?><option value="<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>"><?=($row->unvan == '') ? htmlspecialchars($row->adsoyad, ENT_QUOTES, 'UTF-8') : htmlspecialchars($row->unvan, ENT_QUOTES, 'UTF-8')." (".htmlspecialchars($row->adsoyad, ENT_QUOTES, 'UTF-8').") ";?></option><?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="adsoyad" class="col-sm-3 control-label">Adı Soyadı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="adsoyad" name="adsoyad" placeholder="">
                                </div>
                            </div>
                            <?php if ($dayarlar->permalink == 'Evet') { ?>
                                <div class="form-group" id="urlDiv" style="display:none">
                                    <label for="url" class="col-sm-3 control-label">Profil URL</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="url" name="nick_adi" value="" placeholder="">
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group danisman">
                                <label class="col-sm-3 control-label">Öne Çıkar</label>
                                <div class="col-sm-8">
                                    <div class="checkbox checkbox-success">
                                        <input id="onecikar_check" type="checkbox" name="onecikar" value="1">
                                        <label for="onecikar_check"><strong>Aktif</strong></label>
                                        <span>(Anasayfada "Öne Çıkan Danışmanlar"da yayınlanır.)</span><br>
                                    </div>
                                </div>
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-3">
                                    <input placeholder="Bitiş Tarihi (01.01.2017)" type="text" class="form-control" id="onecikar_btarih" name="onecikar_btarih" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">E-posta Adresi</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Buraya Emailinizi yazınız">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dunvan" class="col-sm-3 control-label">Bağlı Olduğunuz Firma</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="dunvan" name="dunvan" placeholder="Bağlı Olduğunuz Firma Adını Yazınız">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="webadres" class="col-sm-3 control-label">Web Adresi</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="webadres" name="webadres" placeholder="Emlak Sitenizin Adını Yazınız">
                                </div>
                            </div>
                            <div class="form-group bireysel">
                                <label for="tcno" class="col-sm-3 control-label">T.C No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="tcno" name="tcno" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="parola" class="col-sm-3 control-label">Parola</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="parola" name="parola" placeholder="Parolanızı Yazınız">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="telefon" class="col-sm-3 control-label">Gsm</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="telefon" name="telefon" placeholder="Cep Telefonunuzu Yazınız">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sabit_telefon" class="col-sm-3 control-label">Sabit Telefon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="sabit_telefon" name="sabit_telefon" placeholder="Sabit telefonunuz Yoksa Cep Telefonunuzu Yazınız. Yoksa İlanda Telefonunuz Gözükmez">
                                </div>
                            </div>
                            <div class="form-group kurumsal">
                                <label for="unvan" class="col-sm-3 control-label">Kurumsal Firma Adı</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="unvan" name="unvan" placeholder="Site Sahibi İseniz Kurumsal Firma Adınızı Yazınız - Değilseniz BOŞ BIRAKIN">
                                </div>
                            </div>
                            <div class="form-group kurumsal">
                                <label for="vergi_no" class="col-sm-3 control-label">Vergi No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="vergi_no" name="vergi_no" placeholder="">
                                </div>
                            </div>
                            <div class="form-group kurumsal">
                                <label for="vergi_dairesi" class="col-sm-3 control-label">Vergi Dairesi</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="vergi_dairesi" name="vergi_dairesi" placeholder="">
                                </div>
                            </div>
                            <?php
                            $ulkeler = $db->query("SELECT * FROM ulkeler_501 ORDER BY id ASC");
                            $ulkelerc = $ulkeler->rowCount();
                            if ($ulkelerc > 1) {
                            ?>
                            <div class="form-group kurumsal">
                                <label for="ulke_id" class="col-sm-3 control-label">Ülke</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="ulke_id" name="ulke_id" onchange="yazdir();ajaxHere('ajax.php?p=il_getir&ulke_id='+this.options[this.selectedIndex].value,'il');">
                                        <option value="">Seçiniz</option>
                                        <?php
                                        while ($row = $ulkeler->fetch(PDO::FETCH_OBJ)) {
                                        ?><option value="<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>"><?=htmlspecialchars($row->ulke_adi, ENT_QUOTES, 'UTF-8');?></option><?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="form-group kurumsal">
    <label for="il" class="col-sm-3 control-label">İl</label>
    <div class="col-sm-9">
        <select class="form-control" id="il" name="il" onchange="ajaxHere('ajax.php?p=ilce_getir&il_id='+this.options[this.selectedIndex].value,'ilce'),$('#semt').html(''),yazdir();">
            <option value="">Seçiniz</option>
            <?php
            if ($ulkelerc < 2) {
                $ulke = $ulkeler->fetch(PDO::FETCH_OBJ);
                $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id=" . $ulke->id . " ORDER BY id ASC");
            } else {
                $sql = $db->query("SELECT id, il_adi FROM il WHERE ulke_id=" . $snc->ulke_id . " ORDER BY id ASC");
            }
            while ($row = $sql->fetch(PDO::FETCH_OBJ)) {
                ?><option value="<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8');?>"><?=htmlspecialchars($row->il_adi, ENT_QUOTES, 'UTF-8');?></option><?php
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group kurumsal">
    <label for="ilce" class="col-sm-3 control-label">İlçe</label>
    <div class="col-sm-9">
        <select class="form-control" name="ilce" id="ilce" onchange="yazdir();ajaxHere('ajax.php?p=mahalle_getir&ilce_id='+this.options[this.selectedIndex].value,'semt');">
            <option value="">Seçiniz</option>
            <option value="0">Yok</option>
        </select>
    </div>
</div>

<div class="form-group kurumsal">
    <label for="mahalle" class="col-sm-3 control-label">Mahalle</label>
    <div class="col-sm-9">
        <select class="form-control" onchange="yazdir();" name="mahalle" id="semt">
            <option value="">Seçiniz</option>
            <option value="0">Yok</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="adres" class="col-sm-3 control-label">Açık Adres</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="adres" name="adres" placeholder="Gerekli ise doldurunuz.">
    </div>
</div>

<div class="form-group kurumsal">
    <label for="maps" class="col-sm-3 control-label">Google Maps<br><span style="font-weight:lighter;font-size:14px;">Harita konumu otomatik olarak belirlediğiniz il/ilçe/mahalle'ye göre işaretlenmektedir. Dilerseniz cadde veya sokak ekleyerek de daraltabilirsiniz. Hassas işaretleme için imleci sürükleyip bırakınız.</span></label>
    <div class="col-sm-9">
        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Şehir</label>
            <div class="col-sm-11">
                <input disabled class="form-control" id="map_il" type="text">
            </div><!-- col end -->
        </div><!-- row end -->

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">İlçe</label>
            <div class="col-sm-11">
                <input disabled id="map_ilce" class="form-control" type="text">
            </div><!-- col end -->
        </div><!-- row end -->

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Mahalle</label>
            <div class="col-sm-11">
                <input disabled onchange="yazdir();" type="text" id="map_mahalle" class="form-control">
            </div><!-- col end -->
        </div><!-- row end -->

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Cadde</label>
            <div class="col-sm-11">
                <input onchange="yazdir();" type="text" class="form-control" name="map_cadde" value="" placeholder="varsa cadde giriniz.">
            </div><!-- col end -->
        </div><!-- row end -->

        <div class="form-group" style="float:left; width:170px;">
            <label class="col-sm-1 control-label">Sokak</label>
            <div class="col-sm-11">
                <input onchange="yazdir();" type="text" class="form-control" name="map_sokak" value="" placeholder="varsa sokak giriniz.">
            </div><!-- col end -->
        </div><!-- row end -->

        <input type="text" class="form-control" id="map_adres" name="map_adres" placeholder="Adres yaznz..." style="display: none;">
        <input type="text" id="coords" name="maps" style="display:none;" />

        <div id="map" style="width: 100%; height: 300px"></div>
        <script type="text/javascript">
            function initMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    dragable: true,
                    zoom: 15,
                    center: { lat: 41.003917, lng: 28.967299 }
                });
                var geocoder = new google.maps.Geocoder();

                var marker = new google.maps.Marker({
                    position: {
                        lat: 41.003917,
                        lng: 28.967299
                    },
                    map: map,
                    draggable: true
                });

                jQuery('#map_adres').on('change', function() {
                    var val = $(this).val();
                    geocodeAddress(marker, geocoder, map, val);
                });

                google.maps.event.addListener(marker, 'dragend', function() {
                    dragend(marker);
                });
            }

            function geocodeAddress(marker, geocoder, resultsMap, address) {
                if (address) {
                    geocoder.geocode({ 'address': address }, function(results, status) {
                        if (status === 'OK') {
                            resultsMap.setCenter(results[0].geometry.location);
                            marker.setMap(resultsMap);
                            marker.setPosition(results[0].geometry.location);
                            dragend(marker);
                        } else {
                            console.log('Geocode was not successful for the following reason: ' + status + " word: " + address);
                        }
                    });
                }
            }

            function dragend(marker) {
                var lat = marker.getPosition().lat();
                var lng = marker.getPosition().lng();
                $("#coords").val(lat + "," + lng);
            }
        </script>
    </div>
</div>

<div class="form-group">
    <label for="avatar" class="col-sm-3 control-label">Profil Resmi</label>
    <div class="col-sm-9">
        <input type="file" class="form-control" id="avatar" name="avatar">
        <br />
        <img width="100" src="" id="avatar_src">
    </div>
</div>
<div class="form-group kurumsal danisman">
    <label for="hakkinda" class="col-sm-3 control-label">Hakkında Yazısı</label>
    <div class="col-sm-9">
        <textarea class="summernote form-control" style="width:200px;" id="hakkinda" name="hakkinda"></textarea>
    </div>
</div>
<div align="right">
    <button type="submit" class="btn btn-purple waves-effect waves-light" onclick=" AjaxFormS('forms','form_status');">Kaydet</button>
</div>
</form>
</div>
</div>
</div>
<!-- Col1 end -->
</div><!-- row end -->
</div><!-- container end -->
</div><!-- content end -->

<script>
    var resizefunc = [];
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
            height: 200, // editör yüksekliği
            minHeight: null, // editörün minimum yüksekliği
            maxHeight: null, // editörün maksimum yüksekliği
            focus: true, // summernote başlatıldığında odaklanmanın ayarlanması
            onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0], editor, welEditable);
            }
        });
    });
</script>
<?php if($dayarlar->permalink == 'Evet'){ ?>
<script>
$(document).ready(function() {
    $("#adsoyad").keyup(function(){
        var adsoyad = $(this).val();
        if(adsoyad == ''){
            $("#urlDiv").slideUp(400);
            $("#url").val('');
        } else {
            adsoyad = $.trim(adsoyad);
            var data = urlTitle(adsoyad);
            $("#url").val(data);
            $("#urlDiv").slideDown(400);
        }
    });
    $("#unvan").keyup(function(){
        var unvan = $(this).val();
        if(unvan == ''){
            $("#urlDiv").slideUp(400);
            $("#url").val('');
        } else {
            unvan = $.trim(unvan);
            var data = urlTitle(unvan);
            $("#url").val(data);
            $("#urlDiv").slideDown(400);
        }
    });
});
function urlTitle(text) {
    var characters = ['"','\'',' ', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '_', '{', '}', '[', ']', '|', '/', '<', '>', ',', '.', '?', '--'];
    var characters2 = ['ç','ı','ü','ğ','ö','ş','Ç','İ','Ü','Ğ','Ö','Ş'];
    var transcharac = ['c','i','u','g','o','s','C','I','U','G','O','S'];
    for (var i = 0; i < characters.length; i++) {
        var char  = String(characters[i]);
        text = text.replace(new RegExp("\\" + char, "g"), '-');
    }
    for (var xi = 0; xi < characters2.length; xi++) {
        var xchar = String(characters2[xi]);
        var xchar2 = String(transcharac[xi]);
        text = text.replace(new RegExp("\\" + xchar, "g"), xchar2);
    }
    text = text.toLowerCase();
    return text;
}
$(document).ready(function() {
    TurKontrol();
    $("input[name='turu']").change(function(){
        TurKontrol();
    });
});
function TurKontrol(){
    var turu = $("input[name='turu']:checked").val();
    if(turu == 0){
        $(".kurumsal,.danisman").slideUp(500,function(){
            $(".bireysel").slideDown(500);
        });
    } else if(turu == 1){
        $(".bireysel,.danisman").slideUp(500,function(){
            $(".kurumsal").slideDown(500);
        });
    } else if(turu == 2){
        $(".bireysel,.kurumsal").slideUp(500,function(){
            $(".danisman").slideDown(500);
        });
    } else {
        $(".bireysel,.kurumsal,.danisman").slideUp(500);
    }
}
function yazdir(){
    var ulke = $("#ulke_id").val();
    ulke = $("#ulke_id option[value='"+ulke+"']").text();
    var il = $("#il").val();
    il = $("#il option[value='"+il+"']").text();
    var ilce = $("#ilce").val();
    ilce = $("#ilce option[value='"+ilce+"']").text();
    var maha = $("#semt").val();
    maha = $("#semt option[value='"+maha+"']").text();
    var cadde = $("input[name='map_cadde']").val();
    var sokak = $("input[name='map_sokak']").val();
    var neler = "";
    if(il != undefined && il != '' && il != '<?=dil("TX264");?>'){
        if(ulke != undefined && ulke != '' && ulke != '<?=dil("TX264");?>'){
            neler += ", " + ulke;
        }
        neler += il;
        $("#map_il").val(il);
        if(ilce != undefined && ilce != '' && ilce != '<?=dil("TX264");?>'){
            neler += ", " + ilce;
            $("#map_ilce").val(ilce);
            if(maha != undefined && maha != '' && maha != '<?=dil("TX264");?>'){
                neler += ", " + maha;
                $("#map_mahalle").val(maha);
            } else {
                $("#map_mahalle").val('');
            }
            if(cadde != undefined && cadde != '' && cadde != '<?=dil("TX264");?>'){
                neler += ", " + cadde;
            }
            if(sokak != undefined && sokak != '' && sokak != '<?=dil("TX264");?>'){
                neler += ", " + sokak;
            }
        } else {
            $("#map_ilce").val('');
        }
    } else {
        $("#map_il").val('');
    }
    $("input[name='map_adres']").val(neler);
    GetMap();
}
function GetMap(){
    $("#map_adres").trigger("change")
}
</script>
<?php } ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=htmlspecialchars($gayarlar->google_api_key, ENT_QUOTES, 'UTF-8');?>&callback=initMap"></script>