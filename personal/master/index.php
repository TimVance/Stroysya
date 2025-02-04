<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Настройки мастера");
?>

<?php

$user_id  = $USER->getId();
$group_id = 8;

if (in_array($group_id, CUser::GetUserGroup($user_id))) {
    echo '<div class="border_block">';

    echo '<h3>Настройки мастера</h3>';

    // Удаление фото
    if (!empty($_POST["del_photo"])) {

        $user = new CUser;

        $rsUserPhoto = CUser::GetByID($user_id);
        $arUserPhoto = $rsUserPhoto->Fetch();

        CFile::Delete((int)$arUserPhoto["PERSONAL_PHOTO"]);

        $arUserField['PERSONAL_PHOTO'] = array('del' => 'Y', 'old_file' => (int)$arUserPhoto["PERSONAL_PHOTO"]);
        if ($user->Update($user_id, $arUserField))
            echo '<div class="alert alert-success">Фото пользователя успешно удалено.</div>';

    }

    // Удаление фото работ
    if (!empty($_GET["del_photos"])) {

        $user_del = new CUser;

        $rsUserPhotos_del = CUser::GetByID($user_id);
        $arUserPhoto_del = $rsUserPhotos_del->Fetch();

        foreach ($arUserPhoto_del["UF_PHOTOS"] as $ufphoto) {
            if ($_GET["del_photos"] == $ufphoto) {
                CFile::Delete((int)$_GET["del_photos"]);
                $arUserField_del['UF_PHOTOS'][] = array('del' => 'Y', 'old_file' => (int)$_GET["del_photos"]);
            }
            else $arUserField_del['UF_PHOTOS'][] = CFile::MakeFileArray($ufphoto);
        }

        if ($user_del->Update($user_id, $arUserField_del))
            echo '<div class="alert alert-success">Фото работ пользователя успешно удалено.</div>';

    }

    // Обновление пользователя
    if (!empty($_POST["edit_master"])) {

        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $request->getPostList()->toArray(); // массив post параметров

        $status   = $request->get("m_status");
        $text     = $request->get("text");
        $services = $request->get("services");
        $inn      = $request->get("inn");
        $snils    = $request->get("snils");
        $rasch    = $request->get("rasch");
        $bik      = $request->get("bik");
        $reg      = $request->get("reg");
        $price = $request->get("price");


        // Загрузка файла
        if ($_FILES["new_file"]) {
            move_uploaded_file($_FILES["new_file"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . "/upload/tmp/" . $_FILES["new_file"]["name"]);
            $arFile              = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"] . "/upload/tmp/" . $_FILES["new_file"]["name"]);
            $arFile["MODULE_ID"] = "main";
            $fid                 = CFile::SaveFile($arFile, "main");

            if (intval($fid) > 0) {
                $arPhoto = CFile::MakeFileArray($fid);
                $userIm  = new CUser;
                $fields  = array(
                    "PERSONAL_PHOTO" => $arPhoto,
                );
                $userIm->Update($USER->GetID(), $fields);
                CFile::Delete($fid);
                unlink($_SERVER["DOCUMENT_ROOT"] . "/upload/tmp/" . $_FILES["new_file"]["name"]);
            }
        }

        if ($_FILES["photos"]) {
            $fields_images = [];
            foreach ($_FILES["photos"]["name"] as $k => $upload_photo_name) {
                move_uploaded_file($_FILES["photos"]["tmp_name"][$k], $_SERVER["DOCUMENT_ROOT"] . "/upload/tmp/" . $_FILES["photos"]["name"][$k]);
                $arFile = [];
                $arFile              = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"] . "/upload/tmp/" . $_FILES["photos"]["name"][$k]);
                $arFile["MODULE_ID"] = "main";
                $fid = [];
                $fid                 = CFile::SaveFile($arFile, "main");
                if (intval($fid) > 0) {
                    $arPhoto = [];
                    $arPhoto = CFile::MakeFileArray($fid);
                    $fields_images[]  = $arPhoto;
                }
            }

            if (!empty($fields_images)) {
                $userIm  = new CUser;

                $rsUserPhotos = CUser::GetByID($user_id);
                $arUserPhotos = $rsUserPhotos->Fetch();

                foreach ($arUserPhotos["UF_PHOTOS"] as $photo_downloaded) {
                    $fields_images[] = CFile::MakeFileArray($photo_downloaded);
                }

                $fields = [
                    "UF_PHOTOS" => $fields_images
                ];
                $userIm->Update($USER->GetID(), $fields);

            }

        }

        $services_block = 37;
        // Получаем все записи и обновляем
        $arSelect = array("ID", "IBLOCK_ID", "NAME");
        $arFilter = array("IBLOCK_ID" => $services_block);
        $res      = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps  = $ob->GetProperties();
            $masters  = array();
            foreach ($arProps["masters"]["VALUE"] as $master) {
                $masters[$master] = $master;
            }
            if (!empty($masters)) {
                if (in_array($arFields["ID"], $services)) $masters[$user_id] = $user_id;
                else {
                    // Если мастер один - удаляем
                    if (count($masters) == 1 && in_array($user_id, $masters)) {
                        CIBlockElement::SetPropertyValues($arFields["ID"], $services_block, 0, 'masters');
                        continue;
                    } else unset($masters[$user_id]);
                }
            } else $masters[$user_id] = $user_id; // Если мастеров не было
            CIBlockElement::SetPropertyValuesEx($arFields["ID"], $services_block, array('masters' => $masters));
        }


        $user   = new CUser;
        $fields = array();
        if (!empty($text)) $fields["WORK_PROFILE"] = $text;
        if (!empty($status)) $fields["UF_STATUS"] = $status;
        if (!empty($inn)) $fields["UF_INN"] = $inn;
        if (!empty($snils)) $fields["UF_SNILS"] = $snils;
        if (!empty($rasch)) $fields["UF_RASCH"] = $rasch;
        if (!empty($bik)) $fields["UF_BIK"] = $bik;
        if (!empty($reg)) $fields["UF_REG"] = $reg;
        if (!empty($price)) $fields["UF_PRICES"] = json_encode($price);
        if ($user->Update($user_id, $fields))
            echo '<div class="alert alert-success">Настройки успешно сохранены.</div>';


    }

    $rsUser = CUser::GetByID($user_id);
    $arUser = $rsUser->Fetch();

    $services = array();

    // Получение сервиса услуг
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "IBLOCK_SECTION_ID");
    $arFilter = array("IBLOCK_ID" => 37);
    $arSection = [];
    $res_sections = CIBlockSection::GetList(Array("LEFT_MARGIN" => "ASC"), $arFilter, true, [
        "ID",
        "IBLOCK_ID",
        "IBLOCK_SECTION_ID",
        "NAME",
        "DEPTH_LEVEL"
    ]);
    while ($res_section = $res_sections->GetNext()) {
        if ($res_section["DEPTH_LEVEL"] == 1) $arSection[$res_section["ID"]]["INFO"] = $res_section;
        else $arSection[$res_section["IBLOCK_SECTION_ID"]]["CHILD"][$res_section["ID"]] = $res_section;

    }
    $res      = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields                             = $ob->GetFields();
        $arProps                              = $ob->GetProperties();
        $services[$arFields["IBLOCK_SECTION_ID"]][$arFields["ID"]]["ID"]    = $arFields["ID"];
        $services[$arFields["IBLOCK_SECTION_ID"]][$arFields["ID"]]["NAME"]    = $arFields["NAME"];
        $services[$arFields["IBLOCK_SECTION_ID"]][$arFields["ID"]]["CHECKED"] = (in_array($user_id, $arProps["masters"]["VALUE"]) ? "checked" : "");
    }

    ?>

    <div class="form-control">
        <label>Фотография</label>
        <?
        $file = CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width' => 150, 'height' => 150), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if (!empty($file)) {
            echo '<div class="photo">';
            echo '<div><img src="' . $file["src"] . '" alt="photo"/></div>';
            echo '<form method="post">
                    <input type="hidden" name="del_photo" value="true">
                    <input type="submit" value="Удалить фото">
                </form>';
            echo '</div>';
        }
        ?>
    </div>
    <form class="edit_master" method="post" enctype="multipart/form-data">
        <input type="hidden" name="edit_master" value="true">
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <input type="file" name="new_file">
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <label>Статус</label>
                    <select name="m_status">
                        <option <?= ($arUser["UF_STATUS"] == 19 ? 'selected' : "") ?> value="19">Свободен</option>
                        <option <?= ($arUser["UF_STATUS"] == 20 ? 'selected' : "") ?> value="20">Занят</option>
                    </select>
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <?

        $regEnum = CUserFieldEnum::GetList(array(), array("USER_FIELD_NAME"=>"UF_REG"));
        $arRegEnum_values = [];
        while ($arRegEnum = $regEnum->GetNext()) {
            $arRegEnum_values[] = $arRegEnum;
        }

        ?>
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <label>Регионы работы</label>
                    <select multiple name="reg[]">
                        <? foreach ($arRegEnum_values as $arRegEnum_value): ?>
                            <option
                                    value="<?=$arRegEnum_value["ID"]?>"
                                <?=(in_array($arRegEnum_value["ID"], $arUser["UF_REG"]) ? "selected" : "")?>
                            >
                                <?=$arRegEnum_value["VALUE"]?>
                            </option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <label>ИНН</label>
                    <input type="text" name="inn" value="<?=$arUser["UF_INN"]?>">
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <label>СНИЛС</label>
                    <input type="text" name="snils" value="<?=$arUser["UF_SNILS"]?>">
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <label>Расчетный счет</label>
                    <input type="text" name="rasch" value="<?=$arUser["UF_RASCH"]?>">
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <label>Бик банка</label>
                    <input type="text" name="bik" value="<?=$arUser["UF_BIK"]?>">
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <label>Обо мне</label>
                    <textarea name="text" cols="30" rows="10"><?= $arUser["WORK_PROFILE"] ?></textarea>
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <div class="form-control">
            <label>Список услуг</label>
            <?
            $prices = get_object_vars(json_decode($arUser["UF_PRICES"]));
            foreach ($arSection as $arSectionItem) {
                echo '<div>'.$arSectionItem["INFO"]["NAME"].'</div>';
                foreach ($services[$arSectionItem["INFO"]["ID"]] as $el_service1) {
                    echo '<label class="price-service-wrapper" style="margin-left: 20px">
                        <input ' . $el_service1["CHECKED"] . ' type="checkbox" name="services[]" value="' . $el_service1["ID"] . '"/>
                        ' . $el_service1["NAME"] . '<input class="price-service '.($el_service1["CHECKED"] == "checked" ? "visible" : "").'" type="text" name="price['.$el_service1["ID"].']" placeholder="Стоимость" '.(!empty($prices[$el_service1["ID"]]) ? 'value="'.$prices[$el_service1["ID"]].'"' : "").'>
                    <span style="display: none" class="currency">руб.</span></label>';
                }
                foreach ($arSectionItem["CHILD"] as $arSectionChild) {
                    echo '<div style="margin-left: 20px; margin-top: 5px">--'.$arSectionChild["NAME"].'</div>';
                    foreach ($services[$arSectionChild["ID"]] as $el_service) {
                        echo '<label class="price-service-wrapper" style="margin-left: 40px">
                            <input ' . $el_service["CHECKED"] . ' type="checkbox" name="services[]" value="' . $el_service["ID"] . '"/> 
                            ' . $el_service["NAME"] . '<input class="price-service '.($el_service["CHECKED"] == "checked" ? "visible" : "").'" type="text" name="price['.$el_service["ID"].']" placeholder="Стоимость" '.(!empty($prices[$el_service["ID"]]) ? 'value="'.$prices[$el_service["ID"]].'"' : "").'>
                            <span style="display: none" class="currency">руб.</span></label>';
                    }
                }
            }
            ?>
        </div>
        <br><br>
        <div class="form-control">
            <div class="wrap_md">
                <div class="iblock label_block">
                    <label>Фото работ</label>
                    <div class="photos_masters">
                    <?
                    foreach ($arUser["UF_PHOTOS"] as $photos) {
                        if (empty($photos)) continue;
                        $file = CFile::ResizeImageGet($photos, array('width' => 150, 'height' => 150), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        echo '<div><img src="'.$file["src"].'"><br><div class="del_photos" data-id="'.$photos.'">Удалить</div></div>';
                    }
                    ?>
                    </div>
                    <br>
                    <br>
                    <input type="file" name="photos[]" multiple>
                </div>
                <div class="iblock text_block"></div>
            </div>
        </div>
        <br>
        <div class="but-r">
            <button class="btn btn-default" type="submit" name="save" value="Сохранить изменения"><span>Сохранить изменения</span>
            </button>
        </div>
    </form>
    </div>
    <style>
        .price-service {
            display: inline-block;
            width: 200px !important;
            margin-left: 20px;
            opacity: 0;
            visibility: hidden;
            height: 20px !important;
        }
        .price-service.visible {
            opacity: 1;
            visibility: visible;
        }
        .form-control label {
            margin-bottom: 0px;
        }
        .photos_masters div {
            display: inline-block;
        }
        .photos_masters img {
            width: 150px;
            height: auto;
            margin-right: 10px;
        }
        .del_photos {
            cursor: pointer;
        }
        .del_photos:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        $(function () {
            $(".price-service-wrapper input[type='checkbox']").change(function () {
                let input = $(this);
                if (input.prop("checked")) {
                    input.parent().find(".price-service").addClass("visible");
                    input.parent().find(".currency").show();
                }
                else {
                    input.parent().find(".price-service").removeClass("visible");
                    input.parent().find(".currency").hide();
                }
            });
            $(".del_photos").click(function () {
                location.replace("https://stroysya.com/personal/master/?del_photos=" + $(this).data("id"));
            });
        });
    </script>
<? } else {
    ?>
    Данная страница доступна только мастерам!
<? } ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>