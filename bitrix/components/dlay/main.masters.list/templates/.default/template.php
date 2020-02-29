<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<pre style="display: none">'; <? print_r($arResult); ?> </pre>

<? if (count($arResult["sections"]) > 0): ?>
    <h2>Услуги сервиса</h2>
    <div class="sections-list">
    <? foreach ($arResult["sections"] as $id_section => $section): ?>
        <div class="sections-list_item"><?=$section["name"]?></div>
    <? endforeach; ?>
    </div>
<? endif; ?>

<? if (count($arResult["masters"]) > 0): ?>
    <div class="services-list">
        <div class="services-row">
            <div class="service-row_image"></div>
            <div class="service-row_name">Имя</div>
            <div class="service-row_list">Услуги мастера</div>
            <div class="service-row_button"></div>
        </div>
        <? foreach ($arResult["masters"] as $id_user => $item): ?>
            <div class="services-row">
                <div class="service-row_image">
                    <div class="service-row_image-wrap">
                        <img src="<?=$item["image"]["src"];?>" alt="<?=$item["name"];?>">
                    </div>
                </div>
                <div class="service-row_name"><?=$item["name"];?></div>
                <div class="service-row_list">
                    <? foreach ($arResult["services"][$id_user] as $id_service => $service): ?>
                        <div class="service-row_list-item"><?=$service["name"]?></div>
                    <? endforeach; ?>
                </div>
                <div class="service-row_button"><button>Предложить работу</button></div>
            </div>
        <? endforeach; ?>
    </div>
<? endif; ?>