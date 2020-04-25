<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<? if (count($arResult["sections"]) > 0): ?>
    <h2>Услуги сервиса</h2>
    <div class="sections-list">
    <? foreach ($arResult["sections"] as $id_section => $section): ?>
        <div data-category-id="<?=$id_section?>" class="sections-list_item"><?=$section["name"]?></div>
    <? endforeach; ?>
    </div>
<? endif; ?>

<?php
global $USER;
$auth = false;
if ($USER->IsAuthorized()) $auth = true;
?>

<? echo '<div class="services-list__wrapper">'; ?>
<? if (count($arResult["masters"]) > 0): ?>
    <div class="services-list">
        <div class="services-row">
            <div class="services-row_fio">Мастер</div>
            <div class="services-row_list">Услуги мастера</div>
            <div class="status">Статус</div>
            <div class="services-row_button"></div>
        </div>
        <? foreach ($arResult["masters"] as $id_user => $item): ?>
            <? if(empty($arResult["services"][$id_user])) continue; ?>
            <div class="services-row">
                <div class="services-row_fio">
                    <span class="services-row_image-wrap">
                        <? if (!empty($item["image"]["src"])): ?>
                            <a href="/masters/?id=<?=$id_user?>"><img src="<?=$item["image"]["src"];?>" alt="<?=$item["name"];?>"></a>
                        <? else: ?>
                            <a href="/masters/?id=<?=$id_user?>"><span class="no-photo-master"></span></a>
                        <? endif; ?>
                    </span>
                    <span class="services-row_name-wrap">
                        <a href="/masters/?id=<?=$id_user?>"><?=$item["name"];?></a>
                    </span>
                </div>
                <div class="services-row_list">
                    <? foreach ($arResult["services"][$id_user] as $id_service => $service): ?>
                        <div class="services-row_list-item"><?=$service["name"]?></div>
                    <? endforeach; ?>
                </div>
                <div class="services-row_status"><span<?=($item["status"] == 20 ? ' class="busy"' : '')?>><?=($item["status"] == 20 ? 'Занят' : 'Свободен')?></span></div>
                <div class="services-row_button call-modal-master" data-id="<?=$id_user?>"><button>Предложить работу</button></div>
            </div>
        <? endforeach; ?>
    </div>
<? else: ?>
    Услуг не найдено!
<? endif; ?>
</div>
