<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();                 
use Webgk\Main\ClientBonusInfo;
$arResult["BONUS_BALANCE"] = "";
$arResult["BONUS_BALANCE"] = ClientBonusInfo::gettingUserBalanceFromDB($arResult["arUser"]["PERSONAL_PHONE"]);
?>