<?php
// Gerekli fonksiyonları içe aktarır.
include "functions.php";

// JSON içeriği için doğru içerik türünü ayarlar.
header("Content-Type: application/json");

// JSON verilerini yazar.
echo json_encode([
    "sDecimal" => ",",
    "sEmptyTable" => dil("TX667"),
    "sInfo" => "_TOTAL_ kayıttan _START_ - _END_ " . dil("TX668"),
    "sInfoEmpty" => dil("TX669"),
    "sInfoFiltered" => "(_MAX_ " . dil("TX670") . ")",
    "sInfoPostFix" => "",
    "sInfoThousands" => ".",
    "sLengthMenu" => "<div class='dttblegoster'>_MENU_</div> <span class='datatbspan'>" . dil("TX671") . "</span>",
    "sLoadingRecords" => dil("TX672"),
    "sProcessing" => dil("TX673"),
    "sSearch" => "<span id='mobdataspan' class='datatbspan'>" . dil("TX674") . "</span>",
    "sZeroRecords" => dil("TX675"),
    "oPaginate" => [
        "sFirst" => dil("TX676"),
        "sLast" => dil("TX677"),
        "sNext" => dil("TX678"),
        "sPrevious" => dil("TX679"),
    ],
    "oAria" => [
        "sSortAscending" => ": " . dil("TX680"),
        "sSortDescending" => ": " . dil("TX681"),
    ],
]);