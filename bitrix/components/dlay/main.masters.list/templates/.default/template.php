<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<pre style="display: none">'; <? print_r($arResult); ?> </pre>

<? if (count($arResult["items"]) > 0): ?>
    <div class="services-list">
        <? foreach ($arResult["items"] as $item): ?>
            <div class="services-row">
                <div class="service-row_name"><?=$arResult["users"][$item["props"]["master"]["VALUE"]]["NAME"];?></div>
                <div class="service-row_button"><button>Предложить работу</button></div>
            </div>
        <? endforeach; ?>
    </div>
<? endif; ?>