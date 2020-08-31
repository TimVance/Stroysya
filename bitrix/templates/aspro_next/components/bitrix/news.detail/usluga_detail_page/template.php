<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);


$APPLICATION->IncludeComponent(
    "dlay:main.masters.detail",
    "",
    array("ID" => $arResult["ID"]),
    $component
);