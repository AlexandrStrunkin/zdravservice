<?php
namespace Webgk\Main;
use Bitrix\Main\Loader;
use Bitrix\Main as MainModule;
use Bitrix\Iblock;    
use Bitrix\Main\Entity;
use Bitrix\Main\Option; 
use Webgk\Main\Hlblock\Prototype;
use Webgk\Main\CSV\CSVToArray;

/**
* ����� �� ������ � �������������� ��������, ��������� ������� �� CSV � ������ � HL-������
*/
class normItems {
    /**
    * ��������� ������� �� CSV �� ���������� � ������������� �������
    * 
    * @param mixed $csvFileUrl
    */
    function NormItemsCSVParse($csvFileUrl) {
        $csvFileArr = CSVToArray::CSVParse($csvFileUrl, array("item_name", "item_quantity"));
        foreach ($csvFileArr as $importedItemInfo) {
            normItems::updatingElementsForHLQuantityBlock($importedItemInfo);
        }
    }
    /**
    * ���������� � ���������� ��������� HL-����� � ���������� ������������� �������
    * 
    * @param mixed $importedItemInfo
    */
    function updatingElementsForHLQuantityBlock($importedItemInfo) {
        if (!empty($importedItemInfo["item_name"])) {
            $hlblock = Prototype::getInstance("GoodsQuantity");
            $resultData = $hlblock->getElements(array(
                "select" => array("*"),
                "filter" => array("UF_ITEM_ID" => $importedItemInfo["item_name"])
            ));
            if (!empty($resultData)) {
                foreach ($resultData as $curResult) {

                    $result = $hlblock->updateData($curResult["ID"], array(
                        'UF_ITEM_ID' => $importedItemInfo["item_name"],
                        'UF_ITEM_QUANTITY' => $importedItemInfo["item_quantity"],
                    ));
                }
            } else {

                $result = $hlblock->addData(array(
                    'UF_ITEM_ID' => $importedItemInfo["item_name"],
                    'UF_ITEM_QUANTITY' => $importedItemInfo["item_quantity"],
                ));
            }
            $resultId = $result->getId();
            if ($resultId) {
                return $resultId;
            } else {
                return false;
            }
        }    
    }
}?>