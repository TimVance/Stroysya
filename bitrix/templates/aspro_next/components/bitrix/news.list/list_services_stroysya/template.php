<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<? if (count($arResult["ITEMS"]) == 0) { echo 'Заявок не найдено!'; return; } ?>


<?
$user_id = $USER->getId();
$group_id = 8;
if(in_array($group_id, CUser::GetUserGroup($user_id))) {
    // Если это мастер
    echo '<h3>Вы исполнитель в следующих заявках</h3>';
}
else {
    // Если это заказчик
    echo '<h3>Отправленные заявки</h3>';
}
?>


<div class="news-list prsl-orders">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>

<div class="prsl-orders__item">
    <div class="prsl-orders__name">Наименование</div>
    <div class="prsl-orders__anons">Описание</div>
    <div class="prsl-orders__date">Дата</div>
    <div class="prsl-orders__price">Бюджет</div>
    <div class="prsl-orders__status">Статус</div>
</div>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="prsl-orders__item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
            <div class="prsl-orders__name">
                <?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
                    <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br />
                <?else:?>
                    <b><?echo $arItem["NAME"]?></b><br />
                <?endif;?>
            </div>
		<?endif;?>
        <div class="prsl-orders__anons"><?echo mb_substr($arItem["DETAIL_TEXT"], 0,140);?>...</div>
        <div class="prsl-orders__date">
            <?=FormatDate("j F Y", MakeTimeStamp($arItem["DISPLAY_PROPERTIES"]["date"]["VALUE"]))?>
        </div>
        <div class="prsl-orders__price">
            <? echo number_format($arItem["DISPLAY_PROPERTIES"]["price"]["VALUE"], "0", "", " ");?>
        </div>
        <div class="prsl-orders__status">
            <span style="background-color: <?=$arResult["STATUS"][$arItem["DISPLAY_PROPERTIES"]["status"]["VALUE"]]["COLOR"]?>">
                <?=$arResult["STATUS"][$arItem["DISPLAY_PROPERTIES"]["status"]["VALUE"]]["NAME"]?>
            </span>
        </div>
	</div>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
