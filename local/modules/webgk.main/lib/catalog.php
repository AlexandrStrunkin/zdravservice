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
        function addOldPricesToResult($arResult) {

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
        function addOldPricesToElement($arResult) {
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

}