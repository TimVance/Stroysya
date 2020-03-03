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
            <div class="services-row_image"></div>
            <div class="services-row_name">Имя</div>
            <div class="services-row_list">Услуги мастера</div>
            <div class="status">Статус</div>
            <div class="services-row_button"></div>
        </div>
        <? foreach ($arResult["masters"] as $id_user => $item): ?>
            <div class="services-row">
                <div class="services-row_image">
                    <div class="services-row_image-wrap">
                        <a href="#"><img src="<?=$item["image"]["src"];?>" alt="<?=$item["name"];?>"></a>
                    </div>
                </div>
                <div class="services-row_name"><a href="#"><?=$item["name"];?></a></div>
                <div class="services-row_list">
                    <? foreach ($arResult["services"][$id_user] as $id_service => $service): ?>
                        <div class="services-row_list-item"><?=$service["name"]?></div>
                    <? endforeach; ?>
                </div>
                <div class="services-row_status"><span<?=($item["status"] == 20 ? ' class="busy"' : '')?>><?=($item["status"] == 20 ? 'Занят' : 'Свободен')?></span></div>
                <div class="services-row_button"><button>Предложить работу</button></div>
            </div>
        <? endforeach; ?>
    </div>
<? endif; ?>