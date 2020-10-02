<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;
use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ViewMasterList extends CBitrixComponent
{

    protected $errors = array();
    protected $block_id = 37;
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

        $arParams = $this->arParams;

        $arResult       = array();
        $arResult       = $this->getDetailUsers($arParams["ID"]);
        $this->arResult = $arResult;
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

            $arParams = $this->arParams;
            $service_id = $arParams["ID"];

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

                if ($arFields["ID"] != $service_id) continue;

                foreach ($arProps["masters"]["VALUE"] as $user) {

                    $rsUser = CUser::GetByID($user);
                    $arUser = $rsUser->Fetch();
                    $user_prices = [];
                    if (!empty($arUser["UF_PRICES"]))
                        $user_prices = get_object_vars(json_decode($arUser["UF_PRICES"]));

                    $items[$user][$arFields["ID"]]["name"] = $arFields["NAME"];
                    $items[$user][$arFields["ID"]]["price"] = '';
                    if (!empty($user_prices[$arFields["ID"]])) {
                        $items[$user][$arFields["ID"]]["price"] = $user_prices[$arFields["ID"]];
                    }
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
            $data[$rsUser["ID"]]["rating"] = $rsUser["UF_RATING"];
            $data[$rsUser["ID"]]["reg"] = $rsUser["UF_REG"];
            if (!empty($rsUser["PERSONAL_PHOTO"]))
                $data[$rsUser["ID"]]["image"] = CFile::ResizeImageGet(
                    $rsUser["PERSONAL_PHOTO"],
                    array("width" => 64, "height" => 64),
                    BX_RESIZE_IMAGE_EXACT
                );
        }
        return $data;
    }

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    private function getDetailUsers($id) {
        $items    = array();
        $arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
        $arFilter = array(
            "IBLOCK_ID"   => IntVal($this->block_id),
            "ACTIVE_DATE" => "Y",
            "ACTIVE"      => "Y",
            "=ID"         => $id
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

}