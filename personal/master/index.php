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

        $services_block = 27;
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
        if ($user->Update($user_id, $fields))
            echo '<div class="alert alert-success">Настройки успешно сохранены.</div>';


    }

    $rsUser = CUser::GetByID($user_id);
    $arUser = $rsUser->Fetch();

    $services = array();

    // Получение сервиса услуг
    $arSelect = array("ID", "IBLOCK_ID", "NAME");
    $arFilter = array("IBLOCK_ID" => 27);
    $res      = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields                             = $ob->GetFields();
        $arProps                              = $ob->GetProperties();
        $services[$arFields["ID"]]["NAME"]    = $arFields["NAME"];
        $services[$arFields["ID"]]["CHECKED"] = (in_array($user_id, $arProps["masters"]["VALUE"]) ? "checked" : "");
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
            foreach ($services as $i => $service) {
                echo '<label><input ' . $service["CHECKED"] . ' type="checkbox" name="services[]" value="' . $i . '"/> ' . $service["NAME"] . '</label>';
            }
            ?>
        </div>
        <div class="but-r">
            <button class="btn btn-default" type="submit" name="save" value="Сохранить изменения"><span>Сохранить изменения</span>
            </button>
        </div>
    </form>
    </div>
<? } else {
    ?>
    Данная страница доступна только мастерам!
<? } ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>