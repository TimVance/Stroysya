<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<div class="maxwidth-theme">
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
                <div class="services-row_list">Стоимость</div>
                <div class="status">Статус</div>
                <div class="reg">Регионы</div>
                <div class="rating"></div>
                <div class="services-row_button"></div>
            </div>
            <? $count = 0; ?>
            <? foreach ($arResult["masters"] as $id_user => $item): ?>
                <? if(empty($arResult["services"][$id_user])) continue; ?>
                <?
                    if (!empty($arParams["count"])) {
                        if (intval($arParams["count"]) <= $count) break;
                        $count++;
                    }
                ?>
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
                            <span><? if (!empty($service["price"])) echo $service["price"]. ' руб.'; ?></span>
                        <? endforeach; ?>
                    </div>
                    <div class="services-row_status"><span<?=($item["status"] == 20 ? ' class="busy"' : '')?>><?=($item["status"] == 20 ? 'Занят' : 'Свободен')?></span></div>
                    <div class="services-row_reg">
                        <?
                            $regEnum          = CUserFieldEnum::GetList(array(), array("USER_FIELD_NAME" => "UF_REG"));
                            $arRegEnum_values = [];
                            while ($arRegEnum = $regEnum->GetNext()) {
                                $arRegEnum_values[$arRegEnum["ID"]] = $arRegEnum;
                            }
                            foreach ($item["reg"] as $reg) {
                                echo '<div>'.$arRegEnum_values[$reg]["VALUE"].'</div>';
                            }
                        ?>
                    </div>
                    <div class="services-row_rating">
                        <?
                            if (!empty($item["rating"])) {
                                for ($i = 1; $i <= intval($item["rating"]); $i++) {
                                    echo '<svg class="star-rating star-active" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z" class=""></path></svg>';
                                }
                                for ($i = intval($item["rating"]); $i <= 4; $i++) {
                                    echo '<svg class="star-rating star-deactive" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z" class=""></path></svg>';
                                }
                            }
                        ?>
                    </div>
                    <div class="services-row_button call-modal-master" data-id="<?=$id_user?>"><button>Предложить работу</button></div>
                </div>
            <? endforeach; ?>
        </div>
    <? else: ?>
        Мастеров не найдено!
    <? endif; ?>
    </div>
    <? if (!empty($arParams["count"])): ?>
        <div class="link-masters"><a class="btn btn-default" href="/masters/">Все мастера</a></div>
    <? endif; ?>
</div>