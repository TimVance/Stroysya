<?

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->RestartBuffer();

$APPLICATION->IncludeComponent(
    "dlay:main.masters.list",
    ".default",
    array()
);

exit();
