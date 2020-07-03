<?

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Страница мастера");

use Bitrix\Main\Context;
$request = Context::getCurrent()->getRequest();

$master = intval($request["id"]);
$arrow_group = 8;


function getListServicesByUsers($masters)
{
    $block_id = 27;
    $items    = array();
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
    $arFilter = array(
        "IBLOCK_ID"         => IntVal($block_id),
        "ACTIVE_DATE"       => "Y",
        "ACTIVE"            => "Y",
        "=PROPERTY_masters" => $masters
    );
    $res      = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);
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
                    <span class="status<?=($arUser["UF_STATUS"] == 20 ? ' busy' : '')?>"><?=($arUser["UF_STATUS"] == 20 ? 'Занят' : 'Свободен')?></span>
                    <?
                        $services = getListServicesByUsers($arUser["ID"]);
                        if(!empty($services)) {
                            echo '<div class="services_list"><div class="title">Список услуг: </div>';
                            foreach ($services as $item) {
                                echo '<div>- '.$item.'</div>';
                            }
                            echo '</div>';
                        }
                    ?>
                    <div class="master-button call-modal-master" data-id="7"><button>Предложить работу</button></div>
                </div>
            </div>
            <div class="master-description">
                <h3>О мастере</h3>
                <div><?=$arUser["WORK_PROFILE"]?></div>
            </div>
        <?}
        else echo 'Данный пользователь не является мастером';
    }
    else echo 'Мастер не найден';
}
else {
    $APPLICATION->IncludeComponent(
        "dlay:main.masters.list",
        "page",
        array()
    );
}


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");

?>