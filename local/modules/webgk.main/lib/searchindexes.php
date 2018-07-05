<?
namespace Webgk\Main;
use Bitrix\Main\Loader;
use Bitrix\Main as MainModule;
use Bitrix\Iblock;    
use Bitrix\Main\Entity;
use Bitrix\Main\Option;
use Webgk\Main\Hlblock\Prototype;
 
class SearchIndexes {

    public function addSearchIndexHLBlockElement (&$arFields) {
        $iblockObj = IblockPrototype::getInstanceByCode('catalog_1c');
        $catalogIblockId = $iblockObj->getId();
        if ($arFields["IBLOCK_ID"] == $catalogIblockId) {
            $propsValues = array();
            $itemInfo = CIBlockElement::GetList (
                array(), 
                array("ID" => $arFields["ID"]), 
                false, 
                false, 
                array(
                    "ID",
                    "DETAIL_PICTURE", 
                    "PROPERTY_DEYSTVUYUSHCHEE_VESHCHESTVO", 
                    "PROPERTY_PROIZVODITEL",
                    "PROPERTY_PRIMENENIE"
                )
            );
            while ($itemProps = $itemInfo -> Fetch()) {
                $propsValues["UF_ACTIVE_SUBSTANCE"] = $itemProps["PROPERTY_DEYSTVUYUSHCHEE_VESHCHESTVO_VALUE"];
                $propsValues["UF_BRAND"] = $itemProps["PROPERTY_PROIZVODITEL_VALUE"];
                $propsValues["UF_PROBLEM"] = $itemProps["PROPERTY_PRIMENENIE_VALUE"];
                if (strlen($itemProps["DETAIL_PICTURE"])) {
                    $pictArr = CFile::GetFileArray($itemProps["DETAIL_PICTURE"]);
                    $propsValues["UF_DETAIL_PICTURE"] = $pictArr["SRC"];
                }    
            }
            $propsValues["UF_ITEM_NAME"] = $arFields["NAME"];
            $propsValues["UF_ITEM_URL"] = $arFields["DETAIL_PAGE_URL"];
            $propsValues["UF_ITEM_ID"] = $arFields["ID"];
            $hlblockObj = HlblockPrototype::getInstance('SearchIndexes');
            $hlblockResultData = $hlblockObj->getElements(array('filter' => array("UF_ITEM_ID" => $arFields["ID"])));
            if ($arFields["ACTIVE"] == "N") {
                if (!empty($hlblockResultData)) {
                    foreach ($hlblockResultData as $curResult) {
                        $result = $hlblockObj->deleteData($curResult["ID"]);
                    }    
                }
            } else {
                if (!empty($hlblockResultData)) {
                    foreach ($hlblockResultData as $curResult) {

                        $result = $hlblockObj->updateData($curResult["ID"], $propsValues);
                    }
                } else {

                    $result = $hlblockObj->addData($propsValues);
                }
            }
        }   
    } 
}?>