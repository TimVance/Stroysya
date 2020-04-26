<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if($arResult['SHOW_SMS_FIELD'] && !$arResult["strProfileError"]){
	CJSCore::Init('phone_auth');
}

global $arTheme;

// get phone auth params
list($bPhoneAuthSupported, $bPhoneAuthShow, $bPhoneAuthRequired, $bPhoneAuthUse) = Aspro\Next\PhoneAuth::getOptions();
?>
<div class="module-form-block-wr lk-page border_block">
	<?if($arResult["strProfileError"]):?>
		<?//ShowError($arResult["strProfileError"]);?>
		<div class="alert alert-danger"><?=$arResult["strProfileError"]?></div>
	<?endif;?>
	<?if($arResult['DATA_SAVED'] === 'Y'):?>
		<div class="alert alert-success"><?=GetMessage('PROFILE_DATA_SAVED')?></div>
	<?endif;?>
	<?if($arResult["SHOW_SMS_FIELD"] && !$arResult["strProfileError"]):?>
		<div class="alert alert-success"><?=GetMessage('main_profile_code_sent')?></div>
	<?endif;?>
	<div class="form-block-wr">
		<?if($arResult["SHOW_SMS_FIELD"] && !$arResult["strProfileError"]):?>
			<form method="post" name="form1" class="main" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
				<?=$arResult["BX_SESSION_CHECK"]?>
				<input type="hidden" name="lang" value="<?=LANG?>" />
				<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
				<input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult["SIGNED_DATA"])?>" />
				<div class="form-control">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label><?=GetMessage("main_profile_code")?><span class="star">*</span></label>
							<input size="30" type="text" name="SMS_CODE" value="<?=htmlspecialcharsbx($arResult["SMS_CODE"])?>" autocomplete="off" />
						</div>
					</div>
				</div>
				<div class="but-r">
					<button class="btn btn-default" type="submit" name="code_submit_button" value=""><span><?=GetMessage("main_profile_send")?></span></button>
				</div>
				<div id="bx_profile_error" style="display:none"><?ShowError("error")?></div>
				<div id="bx_profile_resend"></div>
				<script>
				new BX.PhoneAuth({
					containerId: 'bx_profile_resend',
					errorContainerId: 'bx_profile_error',
					interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
					data:
						<?=CUtil::PhpToJSObject([
							'signedData' => $arResult["SIGNED_DATA"],
						])?>,
					onError:
						function(response)
						{
							var errorDiv = BX('bx_profile_error');
							var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
							errorNode.innerHTML = '';
							for(var i = 0; i < response.errors.length; i++)
							{
								errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
							}
							errorDiv.style.display = '';
						}
				});
				</script>
			</form>
		<?else:?>
			<form method="post" name="form1" class="main" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
				<?=$arResult["BX_SESSION_CHECK"]?>
				<input type="hidden" name="lang" value="<?=LANG?>" />
				<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
				<?if($arTheme["LOGIN_EQUAL_EMAIL"]["VALUE"] == "Y"):?>
					<input type="hidden" name="LOGIN" maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"]?>" />
				<?else:?>
					<div class="form-control">
						<div class="wrap_md">
							<div class="iblock label_block">
								<label><?=GetMessage("PERSONAL_LOGIN")?><span class="star">*</span></label>
								<input required type="text" name="LOGIN" required value="<?=$arResult["arUser"]["LOGIN"]?>" />
							</div>
						</div>
					</div>
				<?endif;?>
				<?if($arTheme["PERSONAL_ONEFIO"]["VALUE"] == "Y"):?>
					<div class="form-control">
						<div class="wrap_md">
							<div class="iblock label_block">
								<label><?=GetMessage("PERSONAL_FIO")?><span class="star">*</span></label>
								<?
								$arName = array();
								if(!$arResult["strProfileError"])
								{
									if($arResult["arUser"]["LAST_NAME"]){
										$arName[] = $arResult["arUser"]["LAST_NAME"];
									}
									if($arResult["arUser"]["NAME"]){
										$arName[] = $arResult["arUser"]["NAME"];
									}
									if($arResult["arUser"]["SECOND_NAME"]){
										$arName[] = $arResult["arUser"]["SECOND_NAME"];
									}
								}
								else
									$arName[] = htmlspecialcharsbx($_POST["NAME"]);
								?>
								<input required type="text" name="NAME" maxlength="50" value="<?=implode(' ', $arName);?>" />
							</div>
							<div class="iblock text_block">
								<?=GetMessage("PERSONAL_NAME_DESCRIPTION")?>
							</div>
						</div>
					</div>
				<?else:?>
					<div class="form-control">
						<div class="wrap_md">
							<div class="iblock label_block">
								<label><?=GetMessage("PERSONAL_LASTNAME")?></label>
								<input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"];?>" />
							</div>
						</div>
					</div>
					<div class="form-control">
						<div class="wrap_md">
							<div class="iblock label_block">
								<label><?=GetMessage("PERSONAL_NAME")?></label>
								<input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"];?>" />
							</div>
						</div>
					</div>
					<div class="form-control">
						<div class="wrap_md">
							<div class="iblock label_block">
								<label><?=GetMessage("PERSONAL_SECONDNAME")?></label>
								<input type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"];?>" />
							</div>
						</div>
					</div>
				<?endif;?>
				<div class="form-control">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label><?=GetMessage("PERSONAL_EMAIL")?><span class="star">*</span></label>
							<input required type="text" name="EMAIL" maxlength="50" placeholder="name@company.ru" value="<? echo $arResult["arUser"]["EMAIL"]?>" />
						</div>
						<div class="iblock text_block">
							<?if($arTheme["LOGIN_EQUAL_EMAIL"]["VALUE"] != "Y"):?>
								<?=GetMessage("PERSONAL_EMAIL_SHORT_DESCRIPTION")?>
							<?else:?>
								<?=GetMessage("PERSONAL_EMAIL_DESCRIPTION")?>
							<?endif;?>
						</div>
					</div>
				</div>
				<?$mask = \Bitrix\Main\Config\Option::get('aspro.next', 'PHONE_MASK', '+7 (999) 999-99-99');?>
				<div class="form-control">
					<div class="wrap_md">
						<div class="iblock label_block">
							<label><?=GetMessage("PERSONAL_PHONE")?><span class="star">*</span></label>
							<?
							if(strlen($arResult["arUser"]["PERSONAL_PHONE"]) && strpos($arResult["arUser"]["PERSONAL_PHONE"], '+') === false && strpos($mask, '+') !== false){
								$arResult["arUser"]["PERSONAL_PHONE"] = '+'.$arResult["arUser"]["PERSONAL_PHONE"];
							}
							?>
							<input required type="tel" name="PERSONAL_PHONE" class="phone" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" />
						</div>
						<div class="iblock text_block">
							<?=GetMessage("PERSONAL_PHONE_DESCRIPTION")?>
						</div>
					</div>
				</div>
				<?if($arResult['PHONE_REGISTRATION']):?>
					<div class="form-control">
						<div class="wrap_md">
							<div class="iblock label_block">
								<label><?=GetMessage("main_profile_phone_number")?><?=($arResult['PHONE_REQUIRED'] ? '<span class="star">*</span>' : '')?></label>
								<?
								if(strlen($arResult["arUser"]["PHONE_NUMBER"]) && strpos($arResult["arUser"]["PHONE_NUMBER"], '+') === false && strpos($mask, '+') !== false){
									$arResult["arUser"]["PHONE_NUMBER"] = '+'.$arResult["arUser"]["PHONE_NUMBER"];
								}
								?>
								<input <?=($arResult['PHONE_REQUIRED'] ? 'required' : '')?> type="tel" name="PHONE_NUMBER" class="phone" maxlength="255" value="<?=$arResult["arUser"]["PHONE_NUMBER"]?>" />
							</div>
							<div class="iblock text_block">
								<?=GetMessage("PHONE_NUMBER_DESCRIPTION".($bPhoneAuthUse ? '_WITH_AUTH' : ''))?>
							</div>
						</div>
					</div>
				<?endif;?>
				<div class="but-r">
					<button class="btn btn-default" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE_TITLE") : GetMessage("MAIN_ADD_TITLE"))?>"><span><?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE_TITLE") : GetMessage("MAIN_ADD_TITLE"))?></span></button>
				</div>
			</form>
			<?if($arResult["SOCSERV_ENABLED"]){ $APPLICATION->IncludeComponent("bitrix:socserv.auth.split", "main", array("SUFFIX"=>"form", "SHOW_PROFILES" => "Y","ALLOW_DELETE" => "Y"),false);}?>
		<?endif;?>
	</div>
	<script>
	$(document).ready(function(){
		$(".form-block-wr form").validate({rules:{ EMAIL: { email: true }}	});
	})
	</script>
</div>


<?php

$user_id = $USER->getId();
$group_id = 8;

if(in_array($group_id, CUser::GetUserGroup($user_id))) {
    echo '<div class="border_block">';

    echo '<h3>Настройки мастера</h3>';

    // Удаление фото
    if (!empty($_POST["del_photo"])) {

        $user = new CUser;

        $rsUserPhoto = CUser::GetByID($user_id);
        $arUserPhoto = $rsUserPhoto->Fetch();

        CFile::Delete((int) $arUserPhoto["PERSONAL_PHOTO"]);

        $arUserField['PERSONAL_PHOTO'] = Array('del' => 'Y', 'old_file' => (int) $arUserPhoto["PERSONAL_PHOTO"]);
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


        // Загрузка файла
        if($_FILES["new_file"]) {
            move_uploaded_file($_FILES["new_file"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/upload/tmp/".$_FILES["new_file"]["name"]);
            $arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/upload/tmp/".$_FILES["new_file"]["name"]);
            $arFile["MODULE_ID"] = "main";
            $fid = CFile::SaveFile($arFile, "main");

            if (intval($fid)>0) {
                $arPhoto = CFile::MakeFileArray($fid);
                $userIm    = new CUser;
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
        $arSelect = Array("ID", "IBLOCK_ID", "NAME");
        $arFilter = Array("IBLOCK_ID" => $services_block);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, array(), $arSelect);
        while($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps  = $ob->GetProperties();
            $masters = array();
            foreach ($arProps["masters"]["VALUE"] as $master) {
                $masters[$master] = $master;
            }
            if(!empty($masters)) {
                if(in_array($arFields["ID"], $services)) $masters[$user_id] = $user_id;
                else {
                    // Если мастер один - удаляем
                    if(count($masters) == 1 && in_array($user_id, $masters)) {
                        CIBlockElement::SetPropertyValues($arFields["ID"], $services_block, 0, 'masters');
                        continue;
                    }
                    else unset($masters[$user_id]);
                }
            }
            else $masters[$user_id] = $user_id; // Если мастеров не было
            CIBlockElement::SetPropertyValuesEx($arFields["ID"], $services_block, array('masters' => $masters));
        }


        $user = new CUser;
        $fields = array();
        if (!empty($text)) $fields["WORK_PROFILE"] = $text;
        if (!empty($status)) $fields["UF_STATUS"] = $status;
        if ($user->Update($user_id, $fields))
            echo '<div class="alert alert-success">Настройки успешно сохранены.</div>';



    }

    $rsUser = CUser::GetByID($user_id);
    $arUser = $rsUser->Fetch();

    $services = array();

    // Получение сервиса услуг
    $arSelect = Array("ID", "IBLOCK_ID", "NAME");
    $arFilter = Array("IBLOCK_ID" => 27);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, array(), $arSelect);
    while($ob = $res->GetNextElement()){
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();
        $services[$arFields["ID"]]["NAME"] = $arFields["NAME"];
        $services[$arFields["ID"]]["CHECKED"] = (in_array($user_id, $arProps["masters"]["VALUE"]) ? "checked" : "");
    }

    ?>

    <div class="form-control">
        <label>Фотография</label>
        <?
            $file = CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            if(!empty($file)) {
                echo '<div class="photo">';
                    echo '<div><img src="'.$file["src"].'" alt="photo"/></div>';
                    echo '<form method="post">
                        <input type="hidden" name="del_photo" value="true">
                        <input type="submit" value="Удалить фото">
                    </form>';
                echo '</div>';
            }
        ?>
    </div>
    <form method="post" enctype="multipart/form-data">
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
                    <option <?=($arUser["UF_STATUS"] == 19 ? 'selected' : "")?> value="19">Свободен</option>
                    <option <?=($arUser["UF_STATUS"] == 20 ? 'selected' : "")?> value="20">Занят</option>
                </select>
            </div>
            <div class="iblock text_block"></div>
        </div>
    </div>
    <div class="form-control">
        <div class="wrap_md">
            <div class="iblock label_block">
                <label>Обо мне</label>
                <textarea name="text" cols="30" rows="10"><?=$arUser["WORK_PROFILE"]?></textarea>
            </div>
            <div class="iblock text_block"></div>
        </div>
    </div>
    <div class="form-control">
        <label>Список услуг</label>
        <?
        foreach ($services as $i => $service) {
            echo '<label><input '.$service["CHECKED"].' type="checkbox" name="services[]" value="'.$i.'"/> '.$service["NAME"].'</label>';
        }
        ?>
    </div>
    <div class="but-r">
        <button class="btn btn-default" type="submit" name="save" value="Сохранить изменения"><span>Сохранить изменения</span></button>
    </div>
    </form>
    </div>
<?}?>