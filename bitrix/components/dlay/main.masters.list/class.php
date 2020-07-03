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

    private function checkModules()
    {
        if (!Loader::includeModule('iblock'))
            throw new SystemException(Loc::getMessage('CPS_MODULE_NOT_INSTALLED', array('#NAME#' => 'iblock')));
    }

    private function getResult()
    {
        if ($this->errors)
            throw new SystemException(current($this->errors));


        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $post    = $request->getPostList();
        if ($post["ajax"] && $post["component"] == "services") {
            if (!empty($post["cats"]))
                $cats = explode(",", $post["cats"]);
            $arResult       = array();
            $arResult       = $this->getUsersByFilter($cats);
            $this->arResult = $arResult;
        }
        else {
            if ($this->StartResultCache()) {
                $arResult             = array();
                $arResult["sections"] = $this->getAllSections();
                $arResult["masters"]  = $this->getAllUsers();
                $arResult["services"] = $this->getListServicesByUsers($arResult["masters"]);
                $this->arResult       = $arResult;
            }
        }
    }

    // Получение всех пользователей группы мастера
    private function getAllUsers()
    {
        $data         = array();
        $filter       = array("GROUPS_ID" => $this->group_id);
        $arParameters = array("SELECT" => array("UF_*"));
        $rsUsers      = CUser::GetList(($by = "ID"), ($order = "desc"), $filter, $arParameters);
        while ($rsUser = $rsUsers->Fetch()) {
            $data[$rsUser["ID"]]["name"]   = $rsUser["NAME"] . ' ' . $rsUser["SECOND_NAME"] . ' ' . $rsUser["LAST_NAME"];
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
    private function getListServicesByUsers($masters)
    {
        $ids = array();
        foreach ($masters as $id => $master) {
            $ids[] = $id;
        }
        if (count($ids) > 0) {
            $items    = array();
            $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
            $arFilter = array(
                "IBLOCK_ID"         => IntVal($this->block_id),
                "ACTIVE_DATE"       => "Y",
                "ACTIVE"            => "Y",
                "=PROPERTY_masters" => $ids
            );
            $res      = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);
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

    private function getAllSections()
    {
        $items    = array();
        $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
        $arFilter = array(
            "IBLOCK_ID"   => IntVal($this->block_id),
            "ACTIVE_DATE" => "Y",
            "ACTIVE"      => "Y",
        );
        $res      = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields                       = $ob->GetFields();
            $items[$arFields["ID"]]["name"] = $arFields["NAME"];
        }
        return $items;
    }

    private function getUsersByFilter($cats)
    {
        $items    = array();
        $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
        $arFilter = array(
            "IBLOCK_ID"   => IntVal($this->block_id),
            "ACTIVE_DATE" => "Y",
            "ACTIVE"      => "Y",
            "=ID"         => $cats
        );
        $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize" => 50), $arSelect);
        $masters = array();
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps  = $ob->GetProperties();
            foreach ($arProps["masters"]["VALUE"] as $user) {
                $masters[$user] = $user;
            }
        }
        return array(
            "ajax" => true,
            "masters" => (!empty($masters) ? $this->getUsers($masters) : array()),
            "services" => (!empty($masters) ? $this->getListServicesByUsers($masters) : array()),
        );
    }

    // Получение всех пользователей группы мастера
    private function getUsers($items = array())
    {
        $data         = array();
        $filter       = array("ID" => implode('|', $items), "GROUPS_ID" => $this->group_id);
        $arParameters = array("SELECT" => array("UF_*"));
        $rsUsers      = CUser::GetList(($by = "ID"), ($order = "desc"), $filter, $arParameters);
        while ($rsUser = $rsUsers->Fetch()) {
            $data[$rsUser["ID"]]["name"]   = $rsUser["NAME"] . ' ' . $rsUser["SECOND_NAME"] . ' ' . $rsUser["LAST_NAME"];
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

}