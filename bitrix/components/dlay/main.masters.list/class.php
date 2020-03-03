<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ViewMasterList extends CBitrixComponent
{

    protected $errors = array();
    protected $block_id = 27;
    protected $group_id = 8;

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
            $arResult             = array();
            $arResult["sections"] = $this->getAllSections();
            $arResult["masters"]  = $this->getAllUsers();
            $arResult["services"] = $this->getListServicesByUsers($arResult["masters"]);
            $this->arResult       = $arResult;
        }
    }

    // Получение всех пользователей группы мастера
    protected function getAllUsers()
    {
        $data    = array();
        $filter  = Array("GROUPS_ID" => array($this->group_id));
        $arParameters = array("SELECT" => array("UF_*"));
        $rsUsers = CUser::GetList(($by = "ID"), ($order = "desc"), $filter, $arParameters);
        while ($rsUser = $rsUsers->Fetch()) {
            $data[$rsUser["ID"]]["name"] = $rsUser["NAME"] . ' ' . $rsUser["SECOND_NAME"] . ' ' . $rsUser["LAST_NAME"];
            $data[$rsUser["ID"]]["status"] = $rsUser["UF_STATUS"];
            if (!empty($rsUser["PERSONAL_PHOTO"]))
                $data[$rsUser["ID"]]["image"] = CFile::ResizeImageGet(
                    $rsUser["PERSONAL_PHOTO"],
                    array("width" => 64, "height" => 64),
                    BX_RESIZE_IMAGE_EXACT
                );
        }
        return $data;
    }

    // Получение услуг мастеров
    protected function getListServicesByUsers($masters)
    {
        $ids = array();
        foreach ($masters as $id => $master) {
            $ids[] = $id;
        }
        if (count($ids) > 0) {
            $items    = array();
            $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
            $arFilter = Array(
                "IBLOCK_ID"         => IntVal($this->block_id),
                "ACTIVE_DATE"       => "Y",
                "ACTIVE"            => "Y",
                "=PROPERTY_masters" => $ids
            );
            $res      = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 50), $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arProps  = $ob->GetProperties();
                foreach ($arProps["masters"]["VALUE"] as $user) {
                    $items[$user][$arFields["ID"]]["name"] = $arFields["NAME"];
                }
            }
            return $items;
        }
    }

    protected function getAllSections()
    {
        $items    = array();
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
        $arFilter = Array(
            "IBLOCK_ID"         => IntVal($this->block_id),
            "ACTIVE_DATE"       => "Y",
            "ACTIVE"            => "Y",
        );
        $res      = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 50), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $items[$arFields["ID"]]["name"] = $arFields["NAME"];
        }
        return $items;
    }

}