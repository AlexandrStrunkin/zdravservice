<?
namespace Webgk\Main;

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Webgk\Main\Tools;

/**
 * Основной класс модуля
 */
class Sale {

    public function changeBasketWebUpdate($ID, $arBasket = array()) {
        if($arBasket["TYPE"] != "IGNORE_HANDLERS") {
            $oldBasket = self::prepareBasketForWeb();
            if(count($oldBasket) > 0) {
                $arNewBasket = self::getNewBasketFromWeb($oldBasket);
                if(count($arNewBasket) > 0) {;
                    self::saveNewBasketFromWeb($arNewBasket);
                }
            }
        }
    }

    public function prepareBasketForWeb(){
        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
        foreach($basket as $basketItem) {
            $arBasket[] = array(
                "PRODUCT_XML_ID" => $basketItem->getField("PRODUCT_XML_ID"),
                "QUANTITY"       => $basketItem->getField("QUANTITY"),
                "PRICE"          => $basketItem->getBasePrice(),
            );
        }
        return $arBasket;
    }

    public function saveNewBasketFromWeb($arNewBasketItems){
        if(count($arNewBasketItems) > 0 && $arNewBasketItems) {
            $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
            foreach ($basket as $oldBasketItem) {
                if(!empty($arNewBasketItems[(string)$oldBasketItem->getField("PRODUCT_XML_ID")])) {
                    $oldBasketItem->setFields($arNewBasketItems[(string)$oldBasketItem->getField("PRODUCT_XML_ID")]);
                    $oldBasketItem->save();
                }
            }
        }
    }

    public function getNewBasketFromWeb($arBasketItems = array()){
        if(count($arBasketItems) > 0) {
            foreach($arBasketItems as $basketItem){
                if(!empty($basketItem["PRODUCT_XML_ID"]) && !empty($basketItem["QUANTITY"])) {
                    $arRequest["tabl"][] = array(
                        "IDassort" => strval($basketItem["PRODUCT_XML_ID"]),
                        "price"    => strval($basketItem["PRICE"]),
                        "quantity" => strval(intval($basketItem["QUANTITY"])),
                    );
                }
            }

            if(count($arRequest["tabl"]) > 0) {
                $json_request = json_encode($arRequest);
                $url      = \COption::GetOptionString("grain.customsettings", "ws_basket_url");
                $login    = \COption::GetOptionString("grain.customsettings", "ws_bonus_login");
                $password = \COption::GetOptionString("grain.customsettings", "ws_bonus_password");

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_PORT => 333,
                    CURLOPT_POST => 1,
                    CURLOPT_USERPWD => $login.":".$password,
                    CURLOPT_POSTFIELDS => $json_request
                ]);

                $xml_response = curl_exec($curl);
                curl_close($curl);

                $xml_object_response = new \SimpleXMLElement($xml_response);
                if($xml_object_response->success == true && count($xml_object_response->Basket->Lines->Line) > 0) {
                    $arNewBasket = array();
                    $actionsIblockObj = \Webgk\Main\Iblock\Prototype::getInstanceById(IB_promo_actions);
                    foreach($xml_object_response->Basket->Lines->Line as $newBasketItem){
                        $inOldBasket = false;
                        $discountXmlID = "";
                        $discountXmlID = trim((string)$newBasketItem->action);
                        $actionElement = array();
                        $newPrice = 0;
                        if(strlen($discountXmlID) > 0) {
                            $actionElement = $actionsIblockObj->getElements(array(
                                    "filter" => array(
                                        "XML_ID" => $discountXmlID,
                                    ),
                                    "select" => array("NAME", "DETAIL_PAGE_URL"),
                                    "cacheTime" => 0
                                )
                            );
                            $newPrice = (float)$newBasketItem->summa / (float)$newBasketItem->quantity;
                        }
                        foreach($arBasketItems as $oldBasketItem){
                            if((string)$newBasketItem->IDassort == $oldBasketItem["PRODUCT_XML_ID"]){
                                $inOldBasket = true;
                                $arNewBasket[(string)$newBasketItem->IDassort] = array(
                                    'QUANTITY'       => (int)$newBasketItem->quantity,
                                    'PRICE'          => $newPrice > 0 ? $newPrice : (float)$newBasketItem->price,
                                    'CUSTOM_PRICE'   => "Y",
                                    'TYPE'           => "IGNORE_HANDLERS",
                                    "DISCOUNT_NAME"  => (strlen($actionElement[0]["NAME"]) > 0) ? "<a href=".$actionElement[0]["DETAIL_PAGE_URL"].">".$actionElement[0]["NAME"]."</a>" : "",
                                    "DISCOUNT_VALUE" => (float)$newBasketItem->discount
                                );
                            }
                        }
                        if(!$inOldBasket) {
                            $arNewBasket[(string)$newBasketItem->IDassort] = array(
                                'QUANTITY'       => (int)$newBasketItem->quantity,
                                'PRICE'          => (float)$newBasketItem->price,
                                'CUSTOM_PRICE'   => "Y",
                                'TYPE'           => "IGNORE_HANDLERS",
                                "DISCOUNT_NAME"  => $actionElement["NAME"] ? "<a href=".$actionElement["DETAIL_PAGE_URL"].">".$actionElement["NAME"]."</a>": "",
                                "DISCOUNT_VALUE" => (float)$newBasketItem->discount
                            );
                        }
                    }
                }
                return $arNewBasket;
            }
        }
        return false;
    }
}

?>