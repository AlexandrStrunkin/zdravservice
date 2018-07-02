<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Whatasoft\Map\CMapMarker;
use Whatasoft\Cache\CCacheParams;
use Bitrix\Main\Loader;
use Bitrix\Iblock;
use \Webgk\Main\Tools;

class MapYandexAjaxListComponent extends CBitrixComponent{
    private static $LANG_PREFIX = "WAS_GEOOBJECTSMAPBD_";

    public function onPrepareComponentParams($arParams){

        if(!isset($arParams["MAP_SCALE"]) || strlen($arParams["MAP_SCALE"]) < 1){
            $arParams["MAP_SCALE"] = "10";
        }
        if(!isset($arParams["MAP_CENTER"]) || strlen($arParams["MAP_CENTER"]) < 1){
            $arParams["MAP_LON"] = "55.76,37.64";
        }
        if(!isset($arParams["MAP_WIDTH"]) || strlen($arParams["MAP_WIDTH"]) < 1){
            $arParams["MAP_WIDTH"] = "100%";
        }
        if(!isset($arParams["MAP_HEIGHT"]) || strlen($arParams["MAP_HEIGHT"]) < 1){
            $arParams["MAP_HEIGHT"] = "400px";
        }

        $arParams["CONTROLS_TO_YMAP"] = array(
            "GEO" => "geolocationControl",
            "SEARCH" => "searchControl",
            "ROUTE" => "routeEditor",
            "TRAFFIC" => "trafficControl",
            "TYPE" => "typeSelector",
            "FULLSCREEN" => "fullscreenControl",
            "ZOOM" => "zoomControl",
            "RULER" => "rulerControl",
        );
        $arParams["MAP_YMAP_CONTROLS"] = array();
        foreach($arParams["CONTROLS"] as $control){
            if(isset($arParams["CONTROLS_TO_YMAP"][$control])){
                $arParams["MAP_YMAP_CONTROLS"][] = $arParams["CONTROLS_TO_YMAP"][$control];
            }
        }

        if(!in_array($arParams["INIT_MAP_TYPE"], array("MAP", "SATELLITE", "HYBRID"))){
            $arParams["INIT_MAP_TYPE"] = "MAP";
        }
        $arParams["INIT_MAP_TYPE"] = strtolower($arParams["INIT_MAP_TYPE"]);

        $arParams["BEHAVIORS_TO_YMAP"] = array(
            "SCROLL_ZOOM" => "scrollZoom",
            "DBLCLICK_ZOOM" => "dblClickZoom",
            "RIGHT_MAGNIFIER" => "rightMouseButtonMagnifier",
            "DRAGGING" => "drag",
        );
        $arParams["MAP_YMAP_BEHAVIORS"] = array();
        foreach($arParams["BEHAVIORS"] as $behavior){
            if(isset($arParams["BEHAVIORS_TO_YMAP"][$behavior])){
                $arParams["MAP_YMAP_BEHAVIORS"][] = $arParams["BEHAVIORS_TO_YMAP"][$behavior];
            }
        }

        if(!isset($arParams["MAP_CLUSTER_SIMPLE"])){
            $arParams["MAP_CLUSTER_SIMPLE"] = "N";
        }
        if(!$arParams["MAP_BALLOON_NAME"] && !$arParams["MAP_BALLOON_DETAIL_IMG"] && !$arParams["MAP_BALLOON_TEXT"] && $arParams["MAP_BALLOON_LINK_SHOW"] != "Y"){
            $arParams["MAP_CLUSTER_SIMPLE"] = "Y";
        }
        if(!isset($arParams["MAP_BALLOON_LINK_NEW_WINDOW"])){
            $arParams["MAP_BALLOON_LINK_NEW_WINDOW"] = "Y";
        }
        if(!isset($arParams["USE_ICON_CONTENT"])){
            $arParams["USE_ICON_CONTENT"] = "N";
        }
        if(!isset($arParams["USE_ELEMENT_ICON"])){
            $arParams["USE_ELEMENT_ICON"] = "N";
        }
        if(!isset($arParams["USE_SECTION_ICON"])){
            $arParams["USE_SECTION_ICON"] = "N";
        }

        $arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
        if(strlen($arParams["IBLOCK_TYPE"])<=0)
        $arParams["IBLOCK_TYPE"] = "news";
        $arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
        $arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);
        $arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N";

        $arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
        if(strlen($arParams["SORT_BY1"])<=0)
        $arParams["SORT_BY1"] = "ACTIVE_FROM";
        if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
        $arParams["SORT_ORDER1"]="DESC";

        if(strlen($arParams["SORT_BY2"])<=0)
        $arParams["SORT_BY2"] = "SORT";
        if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
        $arParams["SORT_ORDER2"]="ASC";

        $arParams["REQUEST_LIMIT"] = intval($arParams["REQUEST_LIMIT"]);
        if($arParams["REQUEST_LIMIT"] < 100){
            $arParams["REQUEST_LIMIT"] = 100;
        }else if($arParams["REQUEST_LIMIT"] > 5000){
            $arParams["REQUEST_LIMIT"] = 5000;
        }

        if(!is_array($arParams["MAP_BALLOON_PROPERTIES"])){
            $arParams["MAP_BALLOON_PROPERTIES"] = array();
        }
        foreach($arParams["MAP_BALLOON_PROPERTIES"] as $key=>$val){
            if($val===""){
                unset($arParams["MAP_BALLOON_PROPERTIES"][$key]);
            }
        }

        $arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";

        $arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);

        $arParams["CHECK_PERMISSIONS"] = $arParams["CHECK_PERMISSIONS"]!="N";

        $arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
        if(!is_array($arParams["GROUP_PERMISSIONS"])){
            $arParams["GROUP_PERMISSIONS"] = array(1);
        }

        $arParams["MAP_BALLOON_COORDS"] = str_replace("PROPERTY_", "", $arParams["MAP_BALLOON_COORDS"]);

        return $arParams;
    }

    public function executeComponent(){

        global $APPLICATION, $USER;
        if(!Loader::includeModule("whatasoft.geoobjectsmapbd")){
            ShowError(GetMessage(self::$LANG_PREFIX ."MODULE_NOT_INSTALLED"));
            return;
        }
        if(!Loader::includeModule("iblock")){
            ShowError(GetMessage(self::$LANG_PREFIX ."IBLOCK_MODULE_NOT_INSTALLED"));
            return;
        }
        if($this->arParams["WAS_FROM_AJAX"] == "Y"){
            return self::ajaxTemplate();
        }else{
            self::defaultTemplate();
        }

        // if(is_numeric($this->arParams["IBLOCK_ID"])){
        //   $rsIBlock = CIBlock::GetList(array(), array(
        //     "ACTIVE" => "Y",
        //     "ID" => $this->arParams["IBLOCK_ID"],
        //   ));
        // }else{
        //   $rsIBlock = CIBlock::GetList(array(), array(
        //     "ACTIVE" => "Y",
        //     "CODE" => $this->arParams["IBLOCK_ID"],
        //     "SITE_ID" => SITE_ID,
        //   ));
        // }

        // $this->arResult = $rsIBlock->GetNext();
        // if($this->arResult){
        //   if($this->arParams["WAS_FROM_AJAX"] == "Y"){
        //     return self::ajaxTemplate();
        //   }else{
        //     self::defaultTemplate();
        //   }
        // }else{
        //   Iblock\Component\Tools::process404(
        //     trim($this->arParams["MESSAGE_404"]) ?: GetMessage(self::$LANG_PREFIX ."SECTION_NA")
        //     ,true
        //     ,$this->arParams["SET_STATUS_404"] === "Y"
        //     ,$this->arParams["SHOW_404"] === "Y"
        //     ,$this->arParams["FILE_404"]
        //   );
        // }
    }

    private function prepareAjax(){
        \CJSCore::Init(array("fx", "jquery", "ajax"));
        $this->arParams["COMPONENT_TEMPLATE"] = $this->getTemplateName();
        $obCache = CCacheParams::GetInstance();
        $cache_id = $obCache->SetCache($this->getName(), $this->getTemplateName(), $this->arParams);
        $this->arResult["AJAX_CACHE_ID"] = $cache_id;
        $this->arResult["AJAX_SESSION_ID"] = bitrix_sessid();
    }

    private function defaultTemplate(){
        global $APPLICATION, $USER;

        $mapMarker = CMapMarker::GetInstance();

        $this->arParams["MAP_ICON_DATA"] = array();
        if(isset($this->arParams["MAP_ICON"]) && strlen($this->arParams["MAP_ICON"])){
            $this->arParams["MAP_ICON_DATA"] = $mapMarker->GetMarkerIcon($this->arParams["MAP_ICON"]);
        }

        if(strlen($this->arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $this->arParams["FILTER_NAME"])){
            $arrFilter = array();
        }else{
            $arrFilter = $GLOBALS[$this->arParams["FILTER_NAME"]];
            if(!is_array($arrFilter))
            $arrFilter = array();
        }

        $bUSER_HAVE_ACCESS = !$this->arParams["USE_PERMISSIONS"];
        if($this->arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"])){
            $arUserGroupArray = $USER->GetUserGroupArray();
            foreach($this->arParams["GROUP_PERMISSIONS"] as $PERM){
                if(in_array($PERM, $arUserGroupArray)){
                    $bUSER_HAVE_ACCESS = true;
                    break;
                }
            }
        }
        $this->arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;

        if(!defined('WAS_YMAP_SCRIPT_LOADED')){
            $this->arParams['YANDEX_VERSION'] = "2.1";
            switch(LANGUAGE_ID){
                case 'ru':
                $this->arParams['LOCALE'] = 'ru-RU';
                break;
                case 'ua':
                $this->arParams['LOCALE'] = 'ru-UA';
                break;
                case 'tk':
                $this->arParams['LOCALE'] = 'tr-TR';
                break;
                default:
                $this->arParams['LOCALE'] = 'en-US';
                break;
            }
            $this->arResult['MAPS_SCRIPT_URL'] = 'https://api-maps.yandex.ru/'.$this->arParams['YANDEX_VERSION'].'/?load=package.full&mode=release&lang='.$this->arParams['LOCALE'].'&wizard=bitrix&ns=whatasoftMaps';
            $APPLICATION->AddHeadScript($this->arResult['MAPS_SCRIPT_URL']);


            define('WAS_YMAP_SCRIPT_LOADED', 1);
        }

        $this->prepareAjax();
        $this->includeComponentTemplate();
    }

    private function ajaxTemplate(){
        global $APPLICATION;
        $response = array();
        $response["status"] = "ok";
        $response["errors"] = array();
        $mapMarker = CMapMarker::GetInstance();
        $hlblock_markers_table = $mapMarker->GetHLBlockTableName();
        $this->arResult["SECTIONS"] = array();
        //get all section icons
        // if($this->arParams["USE_SECTION_ICON"] == "Y"){
        //     $arSelect = array("ID", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "NAME", "UF_WAS_MAP_ICON");
        //     $arFilter = array("IBLOCK_ID" => $this->arResult["ID"]);
        //     $dbSections = CIBlockSection::GetList(array("left_margin" => "asc"), $arFilter, false, $arSelect);
        //     while($arSection = $dbSections->GetNext()){
        //         $arSection["ICON_DATA"] = $mapMarker->GetMarkerIconByID($arSection["UF_WAS_MAP_ICON"]);
        //         if(!count($arSection["ICON_DATA"]) && $arSection["IBLOCK_SECTION_ID"]){
        //             $arSection["ICON_DATA"] = $this->arResult["SECTIONS"][$arSection["IBLOCK_SECTION_ID"]]["ICON_DATA"];
        //         }
        //         $this->arResult["SECTIONS"][$arSection["ID"]] = $arSection;
        //     }
        // }

        $arPropsToSelect = array(0);
        $arIblockProps = array();
        $dbProps = CIBlock::GetProperties($this->arResult["ID"]);
        while($arProp = $dbProps->Fetch()){
            if($arProp["CODE"]){
                $arIblockProps[$arProp["CODE"]] = $arProp;
            }
            if(($arProp["USER_TYPE"] == "directory" || $arProp["USER_TYPE"] == "directory_plus") && $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"] == $hlblock_markers_table){
                $arPropsToSelect[] = $arProp["ID"];
            }
        }

        //SELECT
        $arSelect = array(
            // "ID",
            // "IBLOCK_ID",
            // "IBLOCK_SECTION_ID",
            // "NAME",
            // "ACTIVE_FROM",
            // "TIMESTAMP_X",
            // "DETAIL_PAGE_URL",
            // "LIST_PAGE_URL",
            // "DETAIL_TEXT",
            // "DETAIL_TEXT_TYPE",
            // "PREVIEW_TEXT",
            // "PREVIEW_TEXT_TYPE",
            // "PREVIEW_PICTURE",
            "ID","TITLE","PRODUCT_AMOUNT","GPS_N","GPS_S","PHONE","SCHEDULE","ADDRESS"
        );
        //WHERE
        $arFilter = array (
            // "IBLOCK_ID" => $this->arResult["ID"],
            "ACTIVE" => "Y",
            "PRODUCT_ID" => $this->arParams["ELEMENT_ID"],
            '!GPS_N' => [false, 0],
            '!GPS_S' => [false, 0],
            // "CHECK_PERMISSIONS" => $this->arParams['CHECK_PERMISSIONS'] ? "Y" : "N",
        );

        // if($this->arParams["CHECK_DATES"]){
        //     $arFilter["ACTIVE_DATE"] = "Y";
        // }

        // $this->arParams["PARENT_SECTION"] = CIBlockFindTools::GetSectionID(
        //     $this->arParams["PARENT_SECTION"],
        //     $this->arParams["PARENT_SECTION_CODE"],
        //     array(
        //         "GLOBAL_ACTIVE" => "Y",
        //         "IBLOCK_ID" => $this->arResult["ID"],
        //     )
        // );

        // if($this->arParams["PARENT_SECTION"]>0){
        //     $arFilter["SECTION_ID"] = $this->arParams["PARENT_SECTION"];
        //     if($this->arParams["INCLUDE_SUBSECTIONS"]){
        //         $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        //     }
        //
        //     $this->arResult["SECTION"]= array("PATH" => array());
        //     $rsPath = CIBlockSection::GetNavChain($this->arResult["ID"], $this->arParams["PARENT_SECTION"]);
        //     $rsPath->SetUrlTemplates("", $this->arParams["SECTION_URL"], $this->arParams["IBLOCK_URL"]);
        //     while($arPath = $rsPath->GetNext()){
        //         $ipropValues = new Iblock\InheritedProperty\SectionValues($this->arParams["IBLOCK_ID"], $arPath["ID"]);
        //         $arPath["IPROPERTY_VALUES"] = $ipropValues->getValues();
        //         $this->arResult["SECTION"]["PATH"][] = $arPath;
        //     }
        //
        //     $ipropValues = new Iblock\InheritedProperty\SectionValues($this->arResult["ID"], $this->arParams["PARENT_SECTION"]);
        //     $this->arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
        // }else{
            $this->arResult["SECTION"]= false;
        // }
        //ORDER BY
        $arSort = array(
            $this->arParams["SORT_BY1"]=>$this->arParams["SORT_ORDER1"],
            $this->arParams["SORT_BY2"]=>$this->arParams["SORT_ORDER2"],
        );
        if(!array_key_exists("ID", $arSort)){
            $arSort["ID"] = "DESC";
        }

        $map_prop2param = array(
            "COLOR" => "MAP_BALLOON_COLOR",
            "HEADER" => "MAP_BALLOON_NAME",
            "TITLE" => "MAP_BALLOON_TITLE",
            "TEXT" => "MAP_BALLOON_TEXT",
            "IMG" => "MAP_BALLOON_DETAIL_IMG",
        );

        if(isset($arIblockProps[$this->arParams["MAP_BALLOON_COORDS"]])){
            $arPropsToSelect[] = $arIblockProps[$this->arParams["MAP_BALLOON_COORDS"]]["ID"];
        }
        if(isset($arIblockProps[$this->arParams["ICON_CONTENT"]])){
            $arPropsToSelect[] = $arIblockProps[$this->arParams["ICON_CONTENT"]]["ID"];
        }
        foreach($map_prop2param as $key => $param){
            if(strpos($this->arParams[$param], "PROPERTY_") !== false){
                $prop = str_replace("PROPERTY_", "", $this->arParams[$param]);
                if(isset($arIblockProps[$prop])){
                    $arPropsToSelect[] = $arIblockProps[$prop]["ID"];
                }
            }
        }
        foreach($this->arParams["MAP_BALLOON_PROPERTIES"] as $prop){
            if(isset($arIblockProps[$prop])){
                $arPropsToSelect[] = $arIblockProps[$prop]["ID"];
            }
        }

        $arrFilter = $this->arParams["STORES"];
        if(!is_array($arrFilter)){
          $arrFilter = array();
        }

        // if(strlen($this->arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $this->arParams["FILTER_NAME"])){
        //     $arrFilter = array();
        // }else{
        //     $arrFilter = $GLOBALS[$this->arParams["FILTER_NAME"]];
        //     if(!is_array($arrFilter))
        //     $arrFilter = array();
        // }


        if($this->arParams["REQUEST_LIMIT"] <= 0){
            $this->arParams["REQUEST_LIMIT"] = 1000;
        }
        $arNavStartParams = array(
            "iNumPage" => isset($_POST["page"]) ? intval($_POST["page"]) : 1,
            "nPageSize" => $this->arParams["REQUEST_LIMIT"],
            'checkOutOfRange' => true,
        );

        $response["page"] = $arNavStartParams["iNumPage"] + 1;

        $this->arResult["ITEMS"] = array();
        $this->arResult["ELEMENTS"] = array();
        $arElementLink = array();
        $intKey = 0;

        $rsElement = CCatalogStore::GetList($arSort, array_merge($arFilter, $arrFilter), false, $arNavStartParams, $arSelect);

        // $rsElement->SetUrlTemplates($this->arParams["DETAIL_URL"], "", $this->arParams["IBLOCK_URL"]);

        while($obElement = $rsElement->Fetch()){
            $pattern = '/^\[.+\]/';
            $replace_to = '';
            $obElement['TITLE'] = preg_replace($pattern, $replace_to, $obElement['TITLE']);
            //$obElement['TITLE'] =
            $arItem = $obElement;
            $this->arResult["ITEMS"][$intKey] = $arItem;
            $this->arResult["ELEMENTS"][$intKey] = $arItem["ID"];
            $arElementLink[$arItem["ID"]] = &$this->arResult["ITEMS"][$intKey];
            $intKey++;
                //\Webgk\Main\Tools::log($obElement,'',true);
        }

        //get properties for all elements
        // $arElemFilter = array(
        //     "ID" => $this->arResult["ELEMENTS"],
        //     "IBLOCK_ID" => $this->arResult["ID"],
        // );
        // $arPropFilter = array(
        //     "ID" => $arPropsToSelect,
        // );
        // CIBlockElement::GetPropertyValuesArray($arElementLink, $this->arResult["ID"], $arElemFilter, $arPropFilter);

        $arCacheIcons = array();
        $arElementLink = array();
        $arFileIds = array();
        foreach($this->arResult["ITEMS"] as $item_key => &$arItem){

            // if ($arItem['PRODUCT_AMOUNT'] <= 0) {
            //     continue;
            // }

            $arItem["MAP"] = array();
            // $arItem["MAP"]["COORDINATES"] = $arItem["PROPERTIES"][$this->arParams["MAP_BALLOON_COORDS"]]["VALUE"];
            $arItem["MAP"]["COORDINATES"] = $arItem['GPS_N'].','.$arItem['GPS_S'];

            if(strlen($arItem["MAP"]["COORDINATES"]) < 3){
                continue;
            }

            $arItem["MAP"]["ICON_CONTENT"] = "";
            if(isset($arItem["PROPERTIES"][$this->arParams["ICON_CONTENT"]])){
                $arItem["MAP"]["ICON_CONTENT"] = $arItem["PROPERTIES"][$this->arParams["ICON_CONTENT"]]["VALUE"];
            }
            $arItem["MAP"]["LINK"] = ($this->arParams["MAP_BALLOON_LINK_SHOW"] == "Y" ? $arItem["DETAIL_PAGE_URL"] : "#");
            $arItem["MAP"]["PROPERTIES"] = array();
            foreach($this->arParams["MAP_BALLOON_PROPERTIES"] as $prop){
                if(isset($arItem["PROPERTIES"][$prop]) && $arItem["PROPERTIES"][$prop]["VALUE"]){
                    if(isset($arIblockProps[$prop])){
                        $arItem["MAP"]["PROPERTIES"][$prop] = $arItem["PROPERTIES"][$prop];
                        $arItem["MAP"]["DISPLAY_PROPERTIES"][$prop] = array(
                            "NAME" => $arIblockProps[$prop]["NAME"],
                            "VALUE" => $arItem["PROPERTIES"][$prop]["VALUE"],
                        );
                        if($arItem["PROPERTIES"][$prop]["USER_TYPE"] == "HTML"){
                            $arItem["MAP"]["DISPLAY_PROPERTIES"][$prop]["VALUE"] = $arItem["PROPERTIES"][$prop]["~VALUE"]["TEXT"];
                        }
                    }
                }
            }

            // foreach($map_prop2param as $key => $param){
            //     $arItem["MAP"][$key] = "";
            //     if(strpos($this->arParams[$param], "PROPERTY_") !== false){
            //         $prop = str_replace("PROPERTY_", "", $this->arParams[$param]);
            //         $arItem["MAP"][$key] = $arItem["PROPERTIES"][$prop]["VALUE"];
            //         if($arItem["PROPERTIES"][$prop]["USER_TYPE"] == "HTML"){
            //             $arItem["MAP"][$key] = $arItem["PROPERTIES"][$prop]["~VALUE"]["TEXT"];
            //         }
            //     }else if(strpos($this->arParams[$param], "FIELD_") !== false){
            //         $field = str_replace("FIELD_", "", $this->arParams[$param]);
            //         $arItem["MAP"][$key] = $arItem[$field];
            //     }
            // }
            $arItem["MAP"]["TITLE"] = $arItem['TITLE'];
            $arItem["MAP"]['HEADER'] = $arItem['TITLE'];
            $arItem["MAP"]["PROPERTIES"]['ADDRESS'] = $arItem['ADDRESS'];
            $arItem["MAP"]["DISPLAY_PROPERTIES"]['ADDRESS'] = array(
                "NAME" => "Адресс",
                "VALUE" => $arItem['ADDRESS']);
            $arItem["MAP"]["PROPERTIES"]['PHONE'] = $arItem['PHONE'];
            $arItem["MAP"]["DISPLAY_PROPERTIES"]['PHONE'] = array(
                "NAME" => "Телефон",
                "VALUE" => $arItem['PHONE']);
            $arItem["MAP"]["PROPERTIES"]['SCHEDULE'] = $arItem['SCHEDULE'];
            $arItem["MAP"]["DISPLAY_PROPERTIES"]['SCHEDULE'] = array(
                "NAME" => "Время работы",
                "VALUE" => $arItem['SCHEDULE']);

            // if(!is_array($arItem["MAP"]["IMG"]) && intval($arItem["MAP"]["IMG"])){
            //     $arFileIds[] = $arItem["MAP"]["IMG"];
            //     if(!isset($arElementLink[$arItem["MAP"]["IMG"]])){
            //         $arElementLink[$arItem["MAP"]["IMG"]] = array();
            //     }
            //     $arElementLink[$arItem["MAP"]["IMG"]][] = &$this->arResult["ITEMS"][$item_key];
            // }

            //get element icon
            // $arItem["MAP"]["ICON_DATA"] = array();
            // if($this->arParams["USE_ELEMENT_ICON"] == "Y"){
            //     foreach($arItem["PROPERTIES"] as $arProperty){
            //         if(in_array($arProperty["USER_TYPE"], array("directory", "directory_plus")) && $arProperty["USER_TYPE_SETTINGS"]["TABLE_NAME"] == $hlblock_markers_table){
            //             if(strlen($arProperty["VALUE"])){
            //                 $arItem["MAP"]["ICON_DATA"] = $mapMarker->GetMarkerIcon($arProperty["VALUE"]);
            //             }
            //             break;
            //         }
            //     }
            // }
            // if($this->arParams["USE_SECTION_ICON"] == "Y" && !count($arItem["MAP"]["ICON_DATA"]) && $arItem["IBLOCK_SECTION_ID"]){
            //     $arItem["MAP"]["ICON_DATA"] = $this->arResult["SECTIONS"][$arItem["IBLOCK_SECTION_ID"]]["ICON_DATA"];
            // }

            unset($arItem["PROPERTIES"]);
        }
        unset($arItem);

        //get all image files
        // if(count($arFileIds)){
        //     $dbFiles = CFile::GetList(array(), array("@ID" => implode(",", $arFileIds)));
        //     while($arFile = $dbFiles->GetNext()){
        //         $arFile["SRC"] = CFile::GetFileSRC($arFile, false, false);
        //         foreach($arElementLink[$arFile["ID"]] as &$arItem){
        //             $arItem["MAP"]["IMG"] = $arFile;
        //         }
        //         unset($arItem);
        //     }
        // }

        foreach($this->arResult["ITEMS"] as $item_key => &$arItem){
        //     $arItem["MAP"]["FOOTER"] = "";
        //     if($arItem["MAP"]["LINK"] && $arItem["MAP"]["LINK"] != "#"){
        //         $arItem["MAP"]["FOOTER"] = '<a href="'. $arItem["MAP"]["LINK"] .'"'. ($this->arParams["MAP_BALLOON_LINK_NEW_WINDOW"] == "Y" ? ' target="_blank"' : '') .'>'. htmlspecialcharsbx($this->arParams["MAP_BALLOON_LINK_TEXT"]) .'</a>';
        //     }
        //
        //     $arItem["MAP"]["CONTENT"] = "";
        //     if(is_array($arItem["MAP"]["IMG"]) && isset($arItem["MAP"]["IMG"]["SRC"])){
        //         $arItem["MAP"]["CONTENT"] .= '<div class="was-map-balloon-inner-img"><img src="'. $arItem["MAP"]["IMG"]["SRC"] .'" alt="" /></div>';
        //     }
        //     if(strlen($arItem["MAP"]["TEXT"])){
        //         $arItem["MAP"]["CONTENT"] .= '<div class="was-map-balloon-inner-text">'. $arItem["MAP"]["TEXT"] .'</div>';
        //     }
        //
            $arItem["MAP"]["SHOW_BALLOON"] = (strlen($arItem["MAP"]["HEADER"]) || strlen($arItem["MAP"]["CONTENT"]) || (strlen($arItem["MAP"]["FOOTER"])));
        }
        unset($arItem);

        $response["last"] = true;
        if(count($this->arResult["ITEMS"]) >= $this->arParams["REQUEST_LIMIT"]){
            $response["last"] = false;
        }

        $this->arResult["RESPONSE"] = $response;
        $this->includeComponentTemplate("ajax");

// Tools::log($this->arResult["RESPONSE"]);

        return $this->arResult["RESPONSE"];
    }
}
