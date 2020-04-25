<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

$ids = array();

// Новая заявка
$ids[313] = 313;

foreach ($arResult["ITEMS"] as $i => $item) {
    $ids[$item["PROPERTIES"]["status"]["VALUE"]] = $item["PROPERTIES"]["status"]["VALUE"];
}

$arResult["STATUS"] = array();
if (!empty($ids)) {
    $arSelect = Array("ID", "NAME", "PROPERTY_color");
    $arFilter = Array("IBLOCK_ID" => 29, "ID" => $ids);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $arResult["STATUS"][$arFields["ID"]]["NAME"] = $arFields["NAME"];
        $arResult["STATUS"][$arFields["ID"]]["COLOR"] = $arFields["PROPERTY_COLOR_VALUE"];
    }
}