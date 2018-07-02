<?php

    namespace Webgk\Main;

    /**
    * Модель каталога
    */
    class Catalog
    {

        /**
        * метод для добавления цен со скидкой в массив arresult списка товаров
        * 
        * @param mixed $arResult - весь массив данных из шаблона компонента
        */
        public static function addOldPricesToResult($arResult) {

            //собираем дополнительный массив для типов цен
            foreach ($arResult["PRICES"] as $priceId => $priceData) {
                //массив вида "xml_id цены" => "id цены"
                $arResult["PRICE_DATA_LIST"][$priceData["XML_ID"]] = $priceData["ID"];    
            }

            foreach ($arResult["ITEMS"] as $key => $item) {
                //проверяем свойство "старая цена"
                if ($item["PROPERTIES"]["STARAYA_TSENA"]["VALUE"]) {
                    $oldPrices = json_decode($item["PROPERTIES"]["STARAYA_TSENA"]["~VALUE"], true);
                    foreach ($oldPrices as $xmlId => $priceValue) {
                        if ($arResult["PRICE_DATA_LIST"][$xmlId]) {
                            $arResult["ITEMS"][$key]["ITEM_OLD_PRICES"][$arResult["PRICE_DATA_LIST"][$xmlId]] = $priceValue; 
                        }   
                    }  
                }
            }

            foreach ($arResult["ITEMS"] as $key => $item) {
                //переписываем цены у товара
                foreach ($item["PRICE_MATRIX"]["MATRIX"] as $priceId => $price) {
                    foreach ($price as $type => $data) {
                        //если какая-то цена снижена (есть "старая цена", которая больше)
                        if ($item["ITEM_OLD_PRICES"][$priceId] && $item["ITEM_OLD_PRICES"][$priceId] > $data["PRICE"]) {
                            $arResult["ITEMS"][$key]["PRICE_MATRIX"]["MATRIX"][$priceId][$type]["PRICE"] = $item["ITEM_OLD_PRICES"][$priceId];    
                            $arResult["ITEMS"][$key]["PRICE_MATRIX"]["MATRIX"][$priceId][$type]["PRINT_PRICE"] = $item["ITEM_OLD_PRICES"][$priceId] ." руб.";    
                        }
                    }    
                }
            }
            
            return $arResult;

        }
        
        /**
        * метод для добавления цен со скидкой в массив arresult карточки товара
        * 
        * @param mixed $arResult - весь массив данных из шаблона компонента
        */
        public static function addOldPricesToElement($arResult) {
            //собираем дополнительный массив для типов цен
            foreach ($arResult["CAT_PRICES"] as $priceId => $priceData) {
                //массив вида "xml_id цены" => "id цены"
                $arResult["PRICE_DATA_LIST"][$priceData["XML_ID"]] = $priceData["ID"];    
            }

            //проверяем свойство "старая цена"
            if ($arResult["PROPERTIES"]["STARAYA_TSENA"]["VALUE"]) {
                $oldPrices = json_decode($arResult["PROPERTIES"]["STARAYA_TSENA"]["~VALUE"], true);
                foreach ($oldPrices as $xmlId => $priceValue) {
                    if ($arResult["PRICE_DATA_LIST"][$xmlId]) {
                        $arResult["ITEM_OLD_PRICES"][$arResult["PRICE_DATA_LIST"][$xmlId]] = $priceValue; 
                    }   
                }  
            }

            //переписываем цены у товара
            foreach ($arResult["PRICE_MATRIX"]["MATRIX"] as $priceId => $price) {
                foreach ($price as $type => $data) {
                    //если какая-то цена снижена (есть "старая цена", которая больше)
                    if ($arResult["ITEM_OLD_PRICES"][$priceId] && $arResult["ITEM_OLD_PRICES"][$priceId] > $data["PRICE"]) {
                        $arResult["PRICE_MATRIX"]["MATRIX"][$priceId][$type]["PRICE"] = $arResult["ITEM_OLD_PRICES"][$priceId];    
                        $arResult["PRICE_MATRIX"]["MATRIX"][$priceId][$type]["PRINT_PRICE"] = $arResult["ITEM_OLD_PRICES"][$priceId] ." руб.";    
                    }
                }    
            }
            
            return $arResult;    
        }
        
        
        /**
        * обновление остатка товара на определенном складе
        * 
        * @param integer $itemId - ID товара
        * @param integer $storeId - ID склада
        * @param integer $quantity - ID количество
        */
        public static function updateItemStoreQuantity($itemId, $storeId, $quantity) {
            if (empty($itemId) || empty($storeId) || $quantity === null) {
                return false;
            }    
            
            $itemId = intval($itemId);
            $storeId = intval($storeId);
            $quantity = intval($quantity);
            
            $arFields = Array(
                "PRODUCT_ID" => $itemId,
                "STORE_ID" => $storeId,
                "AMOUNT" => $quantity,
            ); 
         
            //проверяем есть ли запись с остатками на указанном складе для текущего товара
            $checkItem = \CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $itemId, "STORE_ID" => $storeId))->Fetch();
            if ($checkItem["ID"]) {
                if ($checkItem["AMOUNT"] != $quantity) {
                   $result = \CCatalogStoreProduct::Update($checkItem["ID"], $arFields); 
                } else {
                   $result = true; 
                }
            } else {
                $result = \CCatalogStoreProduct::Add($arFields);    
            }
            
            if($result) {
                $result = \Webgk\Main\Catalog::updateItemQuantity($itemId);
            }   
            
            return $result;              
            
        }
        
        
        /**
        * обновление общего остатка товара суммарно по всем складам
        * 
        * @param integer $id - ID товара
        */
        public static function updateItemQuantity($id) {
            if (empty($id)) {
                return false;
            }    
            
            $itemStoreData = \CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $id));
            $totalQuantity = 0;
            while ($arStoreData = $itemStoreData->Fetch()) {
                $totalQuantity += $arStoreData["AMOUNT"];    
            }
            
            $result = \CCatalogProduct::Update($id, array("QUANTITY" => $totalQuantity));
            
            if ($result) {            
                $result = $totalQuantity;
            }
            
            return $result;
        }
        
        
        /**
        * обновление общего остатка товара суммарно по всем складам
        * 
        * @param string $xmlId - XML_ID склада
        */
        public static function getStoreIdByXmlId($xmlId) {
            $result = false;
            
            if (empty($xmlId)) {
                return false;
            }    
            
            $store = \CCatalogStore::GetList(array(), array("XML_ID" => $xmlId), false, false, array("ID"))->Fetch();
            $result = $store["ID"];
            
            return $result;
        }

}