<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ViewMasterList extends CBitrixComponent
{

    protected $errors = array();
    protected $block_id = 27;

    public function onIncludeComponentLang()
    {
        Loc::loadMessages(__FILE__);
    }

    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->getResult();
            $this->includeComponentTemplate();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

    protected function checkModules()
    {
        if (!Loader::includeModule('iblock'))
            throw new SystemException(Loc::getMessage('CPS_MODULE_NOT_INSTALLED', array('#NAME#' => 'iblock')));
    }

    protected function getResult()
    {
        if ($this->errors)
            throw new SystemException(current($this->errors));

        if ($this->StartResultCache()) {
            $arResult          = array();
            $arResult["items"] = $this->getList();
            $arResult["users"] = $this->getUsers($arResult["items"]);
            $this->arResult    = $arResult;
        }
    }

    protected function getList()
    {
        $items    = array();
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
        $arFilter = Array("IBLOCK_ID" => IntVal($this->block_id), "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
        $res      = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 50), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields               = $ob->GetFields();
            $arProps                = $ob->GetProperties();
            $items[$arFields["ID"]] = array(
                "fields" => $arFields,
                "props"  => $arProps,
            );
        }
        return $items;
    }

    protected function getUsers($items)
    {
        $user_ids = array();
        $data = array();
        foreach ($items as $item) {
            if (!empty($item["props"]["master"]["VALUE"]))
                $user_ids[] = $item["props"]["master"]["VALUE"];
            if (!empty($item["props"]["client"]["VALUE"]))
                $user_ids[] = $item["props"]["client"]["VALUE"];
        }
        if (count($user_ids) > 0) {
            $user_ids = array_unique($user_ids);
            $filter   = Array("ID" => implode("|", $user_ids),);
            $rsUsers  = CUser::GetList(($by = "ID"), ($order = "desc"), $filter);
            while ($rsUser = $rsUsers->Fetch()) {
                $data[$rsUser["ID"]]["NAME"] = $rsUser["NAME"].' '.$rsUser["SECOND_NAME"].' '.$rsUser["LAST_NAME"];
            }
            return $data;
        }
    }

}