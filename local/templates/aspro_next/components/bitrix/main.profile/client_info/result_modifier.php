<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();                 
use Webgk\Main\ClientBonusInfo;
$arResult["BONUS_BALANCE"] = "";
$arResult["BONUS_BALANCE"] = ClientBonusInfo::gettingUserBalanceFromDB($arResult["arUser"]["PERSONAL_PHONE"]);

if ($arResult["arUser"]["PERSONAL_GENDER"] == "M") {
    $arResult["arUser"]["PERSONAL_GENDER"] = GetMessage("MALE_GENDER");    
} else if ($arResult["arUser"]["PERSONAL_GENDER"] == "F") {
    $arResult["arUser"]["PERSONAL_GENDER"] = GetMessage("FEMALE_GENDER");
}
?>