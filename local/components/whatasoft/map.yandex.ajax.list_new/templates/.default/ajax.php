<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

use \Webgk\Main\Tools;

//customize this in your template
$arResult["JSON"] = array(
    "type" => "FeatureCollection",
);
$arElements = array();
foreach($arResult["ITEMS"] as $arItem){
    if(strlen($arItem["MAP"]["COORDINATES"]) < 1){
        continue;
    }
    $arElement = array();
    $arElement["type"] = "Feature";
    $arElement["id"] = $arItem["ID"];
    $arElement["geometry"] = array(
        "type" => "Point",
        "coordinates" => explode(",", $arItem["MAP"]["COORDINATES"]),
    );

    $arElement["properties"] = array(
        "elementId" => $arItem["ID"],
        "iconContent" => $arItem["MAP"]["ICON_CONTENT"],
        "iconContentClass" => "simple", //custom icon content class
        "clusterCaption" => $arItem["MAP"]["HEADER"],
        "hintContent" => $arItem["MAP"]["TITLE"],
    );
    if($arItem["MAP"]["SHOW_BALLOON"]){
        $arElement["properties"]["balloonContentHeader"] = $arItem["MAP"]["HEADER"];

        /////////balloon content/////////
        $balloon_content = "";
        if(is_array($arItem["MAP"]["IMG"]) && isset($arItem["MAP"]["IMG"]["SRC"])){
            $balloon_content .= '<div class="was-map-balloon-inner-img"><img src="'. $arItem["MAP"]["IMG"]["SRC"] .'" alt="" /></div>';
        }
        if(strlen($arItem["MAP"]["TEXT"])){
            $balloon_content .= '<div class="was-map-balloon-inner-text">'. $arItem["MAP"]["TEXT"] .'</div>';
        }
        if(count($arItem["MAP"]["DISPLAY_PROPERTIES"])){
            $balloon_content .= '<div class="was-map-balloon-inner-props">';
            foreach($arItem["MAP"]["DISPLAY_PROPERTIES"] as $arProp){
                $balloon_content .= '<div class="was-map-balloon-inner-prop"><b>'. $arProp["NAME"] .'</b>: '. (is_array($arProp["VALUE"]) ? implode(", ", $arProp["VALUE"]) : $arProp["VALUE"]) .'</div>';
            }
            $balloon_content .= '</div>';
        }
        /////////balloon content/////////

        $arElement["properties"]["balloonContentBody"] = $balloon_content;
        $arElement["properties"]["balloonContentFooter"] = $arItem["MAP"]["FOOTER"];
    }

    $arElement["options"] = array();
    if(count($arItem["MAP"]["ICON_DATA"])){
        $arOffset = explode(",", $arItem["MAP"]["ICON_DATA"]["OFFSET"]);
        foreach($arOffset as &$arEl){
            $arEl = intval($arEl);
        }
        $arContentSize = explode(",", $arItem["MAP"]["ICON_DATA"]["CONTENT_SIZE"]);
        foreach($arContentSize as &$arEl){
            $arEl = intval($arEl);
        }
        unset($arEl);
        $arContentOffset = explode(",", $arItem["MAP"]["ICON_DATA"]["CONTENT_OFFSET"]);
        foreach($arContentOffset as &$arEl){
            $arEl = intval($arEl);
        }
        unset($arEl);

        $arElement["options"]["iconLayout"] = "default#image";
        $arElement["options"]["iconImageHref"] = $arItem["MAP"]["ICON_DATA"]["FILE"]["SRC"];
        $arElement["options"]["iconImageSize"] = array(intval($arItem["MAP"]["ICON_DATA"]["WIDTH"]), intval($arItem["MAP"]["ICON_DATA"]["HEIGHT"]));
        $arElement["options"]["iconImageOffset"] = $arOffset;
        if($arParams["USE_ICON_CONTENT"] == "Y"){
            $arElement["options"]["iconLayout"] = "default#imageWithContent";
            $arElement["options"]["iconContentSize"] = $arContentSize;
            $arElement["options"]["iconContentOffset"] = $arContentOffset;
        }
    }else if(strlen($arItem["MAP"]["COLOR"])){
        $arElement["options"]["iconColor"] = "#". $arItem["MAP"]["COLOR"];
    }
    $arElements[] = $arElement;
}
$arResult["JSON"]["features"] = $arElements;
$arResult["RESPONSE"]["data"] = $arResult["JSON"];
?>
