<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Страница мастера");

use Bitrix\Main\Context;
$request = Context::getCurrent()->getRequest();

$master = intval($request["id"]);
$arrow_group = 8;


function getListServicesByUsers($masters)
{
    $block_id = 37;
    $items    = array();
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
    $arFilter = array(
        "IBLOCK_ID"         => IntVal($block_id),
        "ACTIVE"            => "Y",
        "=PROPERTY_masters" => $masters
    );
    $res      = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 200), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps  = $ob->GetProperties();
        foreach ($arProps["masters"]["VALUE"] as $user) {
            $items[$arFields["ID"]] = $arFields["NAME"];
        }
    }
    return $items;
}


if (!empty($master)) {
    $rsUsers = CUser::GetByID($master);
    $arUser = $rsUsers->Fetch();
    if (!empty($arUser["ID"])) {
        $arGroup = CUser::GetUserGroup($master);
        if(in_array($arrow_group, $arGroup)) { ?>
            <h2 class="master_name"><?=$arUser["NAME"]?> <?=$arUser["SECOND_NAME"]?> <?=$arUser["LAST_NAME"]?></h2>
            <div class="flexBetween">
                <div class="master_photo">
                    <?php
                    if (!empty($arUser["PERSONAL_PHOTO"])) {
                        $img = CFile::ResizeImageGet(
                            $arUser["PERSONAL_PHOTO"],
                            array("width" => 240, "height" => 240),
                            BX_RESIZE_IMAGE_PROPORTIONAL
                        );
                        echo '<img src="'.$img["src"].'" alt="Фото мастера"/>';
                    }
                    else echo '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="17" viewBox="0 0 14 17">
                                  <defs>
                                    <style>
                                      .uscls-1 {
                                        fill: #222;
                                        fill-rule: evenodd;
                                      }
                                    </style>
                                  </defs>
                                  <path class="uscls-1" d="M13.969,16a1,1,0,1,1-2,0H11.927C11.578,14.307,9.518,13,7,13s-4.575,1.3-4.924,3H2.031a1,1,0,0,1-2,0,0.983,0.983,0,0,1,.1-0.424C0.7,12.984,3.54,11,7,11S13.332,13,13.882,15.6a1.023,1.023,0,0,1,.038.158c0.014,0.082.048,0.159,0.058,0.243H13.969ZM7,10a5,5,0,1,1,5-5A5,5,0,0,1,7,10ZM7,2a3,3,0,1,0,3,3A3,3,0,0,0,7,2Z"></path>
                                </svg>';
                    ?>
                </div>
                <div class="master-info">
                    <?
                        if (!empty($arUser["UF_RATING"])) {
                            if (!empty($arUser["UF_RATING"])) {
                                for ($i = 1; $i <= intval($arUser["UF_RATING"]); $i++) {
                                    echo '<svg class="star-rating star-active" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z" class=""></path></svg>';
                                }
                                for ($i = intval($arUser["UF_RATING"]); $i <= 4; $i++) {
                                    echo '<svg class="star-rating star-deactive" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z" class=""></path></svg>';
                                }
                            }
                        }
                    ?>
                    <span class="status<?=($arUser["UF_STATUS"] == 20 ? ' busy' : '')?>"><?=($arUser["UF_STATUS"] == 20 ? 'Занят' : 'Свободен')?></span>
                    <br>
                    <div class="regs">
                        <?
                            if (!empty($arUser["UF_REG"])) echo '<div>Регионы работ:</div>';
                            $regEnum = CUserFieldEnum::GetList(array(), array("USER_FIELD_NAME"=>"UF_REG"));
                            $arRegEnum_values = [];
                            while ($arRegEnum = $regEnum->GetNext()) {
                                $arRegEnum_values[$arRegEnum["ID"]] = $arRegEnum;
                            }
                            foreach ($arUser["UF_REG"] as $reg) {
                                echo '<div>'.$arRegEnum_values[$reg]["VALUE"].'</div>';
                            }
                        ?>
                    </div>
                    <div class="master-button call-modal-master" data-id="7"><button>Предложить работу</button></div>
                </div>
            </div>
            <?
            $services = getListServicesByUsers($arUser["ID"]);
            if(!empty($services)) {
                $arPrices = [];
                if (!empty($arUser["UF_PRICES"])) {
                    $arPrices = get_object_vars(json_decode($arUser["UF_PRICES"]));
                }
                echo '<div class="services_list"><div class="title">Список услуг: </div>';
                foreach ($services as $id_service => $item) {
                    echo '<div>- '.$item.(!empty($arPrices[$id_service]) ? ' - <span class="services_price">'.$arPrices[$id_service].' руб.</span>' : "").'</div>';
                }
                echo '</div>';
            }
            ?>
            <div class="master-description">
                <h3>О мастере</h3>
                <div><?=$arUser["WORK_PROFILE"]?></div>
            </div>
            <style>
                .star-rating {
                    display: inline-block;
                    width: 16px;
                    height: 16px;
                    color: #ccc;
                }

                .services-row_rating {
                    width: 100px;
                }

                .star-rating.star-active {
                    color: gold;
                }

                .services_price {
                    border: 1px solid #ccc;
                    padding: 3px 5px;
                    display: inline-block;
                    border-radius: 5px;
                }
            </style>
        <?}
        else echo 'Данный пользователь не является мастером';
    }
    else echo 'Мастер не найден';
}
else {
    /*
    $APPLICATION->IncludeComponent(
        "dlay:main.masters.list",
        "page",
        array()
    );
    */
    echo 'Не указан индетификатор мастера';
}


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");

?>