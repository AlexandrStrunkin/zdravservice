<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die(); 
global $USER;
$curUserInfo = CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("ID" => $USER->GetID()));
$curUserArr = array();
while ($curUser = $curUserInfo -> Fetch()) {
    $curUserArr = $curUser;
    $arResult["CURRENT_PHONE_NUMBER"] = $curUser["PERSONAL_PHONE"];
}
foreach ($arResult["PROPERTY_LIST"] as $propertyID) {
    if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"] == "EMAIL") {
        $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"] = $curUserArr["EMAIL"];
        $arResult["ELEMENT_PROPERTIES"][$propertyID][0]["VALUE"] = $curUserArr["EMAIL"];       
    }
    if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"] == "PHONE_NUMBER") {
        $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"] = $curUserArr["PERSONAL_PHONE"];
        $arResult["ELEMENT_PROPERTIES"][$propertyID][0]["VALUE"] = $curUserArr["PERSONAL_PHONE"];       
    }
    if ($propertyID == "NAME") {
        $arResult["NAME_DEFAULT_VALUE"] = $curUserArr["LAST_NAME"] . " " . $curUserArr["NAME"] . " " . $curUserArr["SECOND_NAME"];
    }
}
//echo "<pre>"; print_r($arResult["PROPERTY_LIST_FULL"]); echo "</pre>";?>