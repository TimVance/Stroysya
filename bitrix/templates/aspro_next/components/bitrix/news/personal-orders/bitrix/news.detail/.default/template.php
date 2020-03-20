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
<div class="news-detail">
    <h2>Заявка №  <?=$arResult["ID"]?></h2>
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img
			class="detail_picture"
			border="0"
			src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
			width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
			height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
			alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
			title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
			/>
	<?endif?>
<!--	--><?//if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
<!--		<span class="news-date-time">--><?//=$arResult["DISPLAY_ACTIVE_FROM"]?><!--</span>-->
<!--	--><?//endif;?>
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<div class="order-title">Заявка на проведение работ</div>
        <div class="order-value"><?=$arResult["NAME"]?></div>
	<?endif;?>
	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
		<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
	<?endif;?>
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
	<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
        <div class="order-title">Описание работ</div>
        <div class="order-value"><?echo $arResult["DETAIL_TEXT"];?></div>
	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	<div style="clear:both"></div>
	<br />
	<?foreach($arResult["FIELDS"] as $code=>$value):
		if ('PREVIEW_PICTURE' == $code || 'DETAIL_PICTURE' == $code)
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?
			if (!empty($value) && is_array($value))
			{
				?><img border="0" src="<?=$value["SRC"]?>" width="<?=$value["WIDTH"]?>" height="<?=$value["HEIGHT"]?>"><?
			}
		}
		else
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?><?
		}
		?><br />
	<?endforeach;?>
    <div class="order-title"><?=$arResult["DISPLAY_PROPERTIES"]["files"]["NAME"]?></div>
    <div class="order-value">
        <? foreach ($arResult["DISPLAY_PROPERTIES"]["files"]["FILE_VALUE"] as $file): ?>
            <div class="order-value-item"><a download href="<?=$file["SRC"]?>"><?=$file["ORIGINAL_NAME"]?></a></div>
        <? endforeach; ?>
    </div>
    <div class="order-title">Крайний срок</div>
    <div class="order-value"><?=$arResult["DISPLAY_PROPERTIES"]["date"]["VALUE"]?></div>
    <div class="order-title">Бюджет</div>
    <div class="order-value"><?=$arResult["DISPLAY_PROPERTIES"]["price"]["VALUE"]?></div>
    <div class="order-title">Исполнитель</div>
    <div class="order-value"><?=$arResult["DISPLAY_PROPERTIES"]["master"]["VALUE"]?></div>
    <div class="order-title">Заказчик</div>
    <div class="order-value"><?=$arResult["DISPLAY_PROPERTIES"]["user"]["VALUE"]?></div>
</div>