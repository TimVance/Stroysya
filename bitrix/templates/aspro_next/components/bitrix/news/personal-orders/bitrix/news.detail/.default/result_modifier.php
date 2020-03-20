<?php

$master_id = $arResult["DISPLAY_PROPERTIES"]["master"]["VALUE"];
$master = CUser::GetByID($master_id)->Fetch();
$arResult["DISPLAY_PROPERTIES"]["master"]["VALUE"] = $master["NAME"].' '.$master["SECOND_NAME"].' '.$master["LAST_NAME"];

$user_id = $arResult["DISPLAY_PROPERTIES"]["user"]["VALUE"];
$user = CUser::GetByID($user_id)->Fetch();
$arResult["DISPLAY_PROPERTIES"]["user"]["VALUE"] = $user["NAME"].' '.$user["SECOND_NAME"].' '.$user["LAST_NAME"];