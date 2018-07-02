<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

use Whatasoft\Map\CMapMarker;
use Whatasoft\HighloadBlock\CCustomHighloadBlock;

$l_prefix = "WAS_GEOOBJECTSMAPBD_";

if(!CModule::IncludeModule("iblock")){
  return;
}
if(!CModule::IncludeModule("highloadblock")){
  return;
}
if(!CModule::IncludeModule("whatasoft.geoobjectsmapbd")){
  return;
}

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks = array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch()){
  $arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$arSorts = array("ASC"=>GetMessage($l_prefix ."IBLOCK_ASC"), "DESC"=>GetMessage($l_prefix ."IBLOCK_DESC"));
$arSortFields = array(
  "ID"=>GetMessage($l_prefix ."IBLOCK_FID"),
  "NAME"=>GetMessage($l_prefix ."IBLOCK_FNAME"),
  "ACTIVE_FROM"=>GetMessage($l_prefix ."IBLOCK_FACT"),
  "SORT"=>GetMessage($l_prefix ."IBLOCK_FSORT"),
  "TIMESTAMP_X"=>GetMessage($l_prefix ."IBLOCK_FTSAMP")
);

$list_empty = array("" => GetMessage($l_prefix ."EMPTY_VALUE"));
$list_text_fields = array();
$list_img_fields = array();
$iblock_text_fields = array("NAME", "PREVIEW_TEXT", "DETAIL_TEXT");
$iblock_img_fields = array("PREVIEW_PICTURE", "DETAIL_PICTURE");
$arFields = CIBlock::GetFields(isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"]);
foreach($arFields as $key => $arField){
  if(in_array($key, $iblock_text_fields)){
    $list_text_fields["FIELD_".$key] = $arField["NAME"];
  }
  if(in_array($key, $iblock_img_fields)){
    $list_img_fields["FIELD_".$key] = $arField["NAME"];
  }
}

$list_text_properties = array();
$list_file_properties = array();
$list_full_properties = array();
$dbProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while($arProp=$dbProp->Fetch()){
  if(in_array($arProp["PROPERTY_TYPE"], array("L", "N", "S")) && $arProp["MULTIPLE"] != "Y"){
    $list_text_properties["PROPERTY_".$arProp["CODE"]] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
  }
  if(in_array($arProp["PROPERTY_TYPE"], array("F")) && $arProp["MULTIPLE"] != "Y"){
    $list_file_properties["PROPERTY_".$arProp["CODE"]] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
  }
  $list_full_properties[$arProp["CODE"]] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
}

$arComponentParameters = array(
  "GROUPS" => array(
    "MAP_SETTINGS" => array(
       "NAME" => GetMessage($l_prefix ."GROUPS_MAP_SETTINGS")
    ),
    "MAP_BALLOON_SETTINGS" => array(
       "NAME" => GetMessage($l_prefix ."GROUPS_MAP_BALLOON_SETTINGS")
    ),
    "MAP_CLUSTER" => array(
       "NAME" => GetMessage($l_prefix ."GROUPS_MAP_CLUSTER")
    ),
  ),
  "PARAMETERS" => array(
    "IBLOCK_TYPE" => array(
      "PARENT" => "BASE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_LIST_TYPE"),
      "TYPE" => "LIST",
      "VALUES" => $arTypesEx,
      "DEFAULT" => "news",
      "REFRESH" => "Y",
    ),
    "IBLOCK_ID" => array(
      "PARENT" => "BASE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_LIST_ID"),
      "TYPE" => "LIST",
      "VALUES" => $arIBlocks,
      "DEFAULT" => '={$_REQUEST["ID"]}',
      "ADDITIONAL_VALUES" => "Y",
      "REFRESH" => "Y",
    ),
    "PARENT_SECTION" => array(
      "PARENT" => "BASE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_SECTION_ID"),
      "TYPE" => "STRING",
      "DEFAULT" => '',
    ),
    "PARENT_SECTION_CODE" => array(
      "PARENT" => "BASE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_SECTION_CODE"),
      "TYPE" => "STRING",
      "DEFAULT" => '',
    ),
    "INCLUDE_SUBSECTIONS" => array(
      "PARENT" => "BASE",
      "NAME" => GetMessage("CP_BNL_INCLUDE_SUBSECTIONS"),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "Y",
    ),
    "SORT_BY1" => array(
      "PARENT" => "DATA_SOURCE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_IBORD1"),
      "TYPE" => "LIST",
      "DEFAULT" => "ACTIVE_FROM",
      "VALUES" => $arSortFields,
      "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_ORDER1" => array(
      "PARENT" => "DATA_SOURCE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_IBBY1"),
      "TYPE" => "LIST",
      "DEFAULT" => "DESC",
      "VALUES" => $arSorts,
      "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_BY2" => array(
      "PARENT" => "DATA_SOURCE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_IBORD2"),
      "TYPE" => "LIST",
      "DEFAULT" => "SORT",
      "VALUES" => $arSortFields,
      "ADDITIONAL_VALUES" => "Y",
    ),
    "SORT_ORDER2" => array(
      "PARENT" => "DATA_SOURCE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_IBBY2"),
      "TYPE" => "LIST",
      "DEFAULT" => "ASC",
      "VALUES" => $arSorts,
      "ADDITIONAL_VALUES" => "Y",
    ),
    "REQUEST_LIMIT" => array(
      "PARENT" => "BASE",
      "NAME" => GetMessage($l_prefix ."REQUEST_LIMIT"),
      "TYPE" => "STRING",
      "DEFAULT" => '1000',
    ),
    "FILTER_NAME" => array(
      "PARENT" => "DATA_SOURCE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_FILTER"),
      "TYPE" => "STRING",
      "DEFAULT" => "",
    ),
    "CHECK_DATES" => array(
      "PARENT" => "DATA_SOURCE",
      "NAME" => GetMessage($l_prefix ."IBLOCK_CHECK_DATES"),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "Y",
    ),
    "DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
      "DETAIL",
      "DETAIL_URL",
      GetMessage($l_prefix ."IBLOCK_DETAIL_PAGE_URL"),
      "",
      "URL_TEMPLATES"
    ),

    'INIT_MAP_TYPE' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'INIT_MAP_TYPE'),
      'TYPE' => 'LIST',
      'VALUES' => array(
        'MAP' => GetMessage($l_prefix .'INIT_MAP_TYPE_MAP'),
        'SATELLITE' => GetMessage($l_prefix .'INIT_MAP_TYPE_SATELLITE'),
        'HYBRID' => GetMessage($l_prefix .'INIT_MAP_TYPE_HYBRID'),
      ),
      'DEFAULT' => 'MAP',
      'ADDITIONAL_VALUES' => 'N',
    ),
    'MAP_WIDTH' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'MAP_WIDTH'),
      'TYPE' => 'STRING',
      'DEFAULT' => '100%',
    ),
    'MAP_HEIGHT' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'MAP_HEIGHT'),
      'TYPE' => 'STRING',
      'DEFAULT' => '400px',
    ),
    'CONTROLS' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'CONTROLS'),
      'TYPE' => 'LIST',
      'MULTIPLE' => 'Y',
      'VALUES' => array(
        'GEO' => GetMessage($l_prefix .'CONTROLS_GEO'),
        'SEARCH' => GetMessage($l_prefix .'CONTROLS_SEARCH'),
        'ROUTE' => GetMessage($l_prefix .'CONTROLS_ROUTE'),
        'TRAFFIC' => GetMessage($l_prefix .'CONTROLS_TRAFFIC'),
        'TYPE' => GetMessage($l_prefix .'CONTROLS_TYPE'),
        'FULLSCREEN' => GetMessage($l_prefix .'CONTROLS_FULLSCREEN'),
        'ZOOM' => GetMessage($l_prefix .'CONTROLS_ZOOM'),
        'RULER' => GetMessage($l_prefix .'CONTROLS_RULER'),
      ),
      'DEFAULT' => array('TYPE', 'ZOOM'),
    ),
    'BEHAVIORS' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'BEHAVIORS'),
      'TYPE' => 'LIST',
      'MULTIPLE' => 'Y',
      'VALUES' => array(
        'SCROLL_ZOOM' => GetMessage($l_prefix .'BEHAVIORS_SCROLL_ZOOM'),
        'DBLCLICK_ZOOM' => GetMessage($l_prefix .'BEHAVIORS_DBLCLICK_ZOOM'),
        'RIGHT_MAGNIFIER' => GetMessage($l_prefix .'BEHAVIORS_RIGHT_MAGNIFIER'),
        'DRAGGING' => GetMessage($l_prefix .'BEHAVIORS_DRAGGING'),
      ),
      'DEFAULT' => array('DBLCLICK_ZOOM', 'DRAGGING'),
    ),
    'MAP_ID' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'MAP_ID'),
      'TYPE' => 'STRING',
      'DEFAULT' => '',
    ),
    'MAP_AUTO_SCALE_CENTER' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'MAP_AUTO_SCALE_CENTER'),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "Y",
    ),
    'MAP_SCALE' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'MAP_SCALE'),
      'TYPE' => 'STRING',
      'DEFAULT' => '10',
    ),
    'MAP_CENTER' => array(
      "PARENT" => "MAP_SETTINGS",
      'NAME' => GetMessage($l_prefix .'MAP_CENTER'),
      'TYPE' => 'CUSTOM',
      'JS_FILE' => '/bitrix/components/whatasoft/map.yandex.ajax.list/settings/settings.js',
      'JS_EVENT' => 'OnWASYMapBDSettingsEdit',
      'JS_DATA' => LANGUAGE_ID.'||'.GetMessage($l_prefix .'MAP_CENTER_SET'),
      'DEFAULT' => '55.76,37.64'
      /*'TYPE' => 'STRING',*/
    ),
    
    "MAP_BALLOON_COORDS" => array(
      "PARENT" => "BASE",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_COORDS"),
      "TYPE" => "LIST",
      "MULTIPLE" => "N",
      "VALUES" => array_merge($list_text_properties),
      "ADDITIONAL_VALUES" => "N",
    ),
    "MAP_COLOR" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_COLOR"),
      'TYPE' => 'STRING',
      'DEFAULT' => '9b7fc4',
    ),
    "MAP_BALLOON_COLOR" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_COLOR"),
      "TYPE" => "LIST",
      "MULTIPLE" => "N",
      "VALUES" => array_merge($list_empty, $list_text_properties),
      "ADDITIONAL_VALUES" => "N",
    ),
    "MAP_BALLOON_TITLE" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_TITLE"),
      "TYPE" => "LIST",
      "VALUES" => array_merge($list_empty, $list_text_fields, $list_text_properties),
      "MULTIPLE" => "N",
      "ADDITIONAL_VALUES" => "N",
      'DEFAULT' => 'FIELD_NAME',
    ),
    "MAP_BALLOON_NAME" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_NAME"),
      "TYPE" => "LIST",
      "VALUES" => array_merge($list_empty, $list_text_fields, $list_text_properties),
      "MULTIPLE" => "N",
      "ADDITIONAL_VALUES" => "N",
      'DEFAULT' => 'FIELD_NAME',
    ),
    "MAP_BALLOON_DETAIL_IMG" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_DETAIL_IMG"),
      "TYPE" => "LIST",
      "VALUES" => array_merge($list_empty, $list_img_fields, $list_file_properties),
      "MULTIPLE" => "N",
      "ADDITIONAL_VALUES" => "N",
      'DEFAULT' => 'FIELD_PREVIEW_PICTURE',
    ),
    "MAP_BALLOON_TEXT" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_TEXT"),
      "TYPE" => "LIST",
      "VALUES" => array_merge($list_empty, $list_text_fields, $list_text_properties),
      "MULTIPLE" => "N",
      "ADDITIONAL_VALUES" => "N",
      'DEFAULT' => 'FIELD_PREVIEW_TEXT',
    ),
    "MAP_BALLOON_PROPERTIES" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_PROPERTIES"),
      "TYPE" => "LIST",
      "MULTIPLE" => "Y",
      "VALUES" => array_merge($list_full_properties),
      "ADDITIONAL_VALUES" => "Y",
    ),
    'MAP_BALLOON_LINK_SHOW' => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      'NAME' => GetMessage($l_prefix .'MAP_BALLOON_LINK_SHOW'),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "N",
      "REFRESH" => "Y",
    ),
    "MAP_BALLOON_LINK_TEXT" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_LINK_TEXT"),
      'TYPE' => 'STRING',
      'DEFAULT' => GetMessage($l_prefix ."MAP_BALLOON_LINK_TEXT_DEF"),
    ),
    "MAP_BALLOON_LINK_NEW_WINDOW" => array(
      "PARENT" => "MAP_BALLOON_SETTINGS",
      "NAME" => GetMessage($l_prefix ."MAP_BALLOON_LINK_NEW_WINDOW"),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "Y",
    ),

    'MAP_CLUSTER' => array(
      "PARENT" => "MAP_CLUSTER",
      'NAME' => GetMessage($l_prefix .'MAP_CLUSTER'),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "Y",
      "REFRESH" => "Y",
    ),
    'MAP_CLUSTER_SIMPLE' => array(
      "PARENT" => "MAP_CLUSTER",
      'NAME' => GetMessage($l_prefix .'MAP_CLUSTER_SIMPLE'),
      "TYPE" => "CHECKBOX",
      "DEFAULT" => "N",
      "REFRESH" => "Y",
    ),
    "MAP_CLUSTER_CONTENT_WIDTH" => array(
      "PARENT" => "MAP_CLUSTER",
      "NAME" => GetMessage($l_prefix ."MAP_CLUSTER_CONTENT_WIDTH"),
      "TYPE" => "STRING",
      "DEFAULT" => "400",
    ),
    "MAP_CLUSTER_CONTENT_LEFT_COLUMN_WIDTH" => array(
      "PARENT" => "MAP_CLUSTER",
      "NAME" => GetMessage($l_prefix ."MAP_CLUSTER_CONTENT_LEFT_COLUMN_WIDTH"),
      "TYPE" => "STRING",
      "DEFAULT" => "120",
    ),
  ),
);

$mapMarker = CMapMarker::GetInstance();
$hlblock_markers_id = $mapMarker->GetHLBlockID();
if($hlblock_markers_id){
  $list_icons = array();
  $arFilter = array(
    "HLBLOCK_ID" => $hlblock_markers_id,
  );
  $dbIcons = CCustomHighloadBlock::GetList(array("UF_SORT" => "ASC"), $arFilter);
  while($arIcon = $dbIcons->Fetch()){
    $list_icons[$arIcon["UF_XML_ID"]] = "[".$arIcon["ID"]."] ".$arIcon["UF_NAME"];
  }
  $arComponentParameters["PARAMETERS"]["MAP_ICON"] = array(
    "PARENT" => "MAP_BALLOON_SETTINGS",
    "NAME" => GetMessage($l_prefix ."MAP_ICON"),
    "TYPE" => "LIST",
    "VALUES" => $list_empty + $list_icons,
    "MULTIPLE" => "N",
    "ADDITIONAL_VALUES" => "N",
    'DEFAULT' => '',
  );
  $arComponentParameters["PARAMETERS"]["USE_ICON_CONTENT"] = array(
    "PARENT" => "MAP_BALLOON_SETTINGS",
    "NAME" => GetMessage($l_prefix ."USE_ICON_CONTENT"),
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "N",
    "REFRESH" => "Y",
  );
  $arComponentParameters["PARAMETERS"]["ICON_CONTENT"] = array(
    "PARENT" => "MAP_BALLOON_SETTINGS",
    "NAME" => GetMessage($l_prefix ."ICON_CONTENT"),
    "TYPE" => "LIST",
    "VALUES" => array_merge($list_empty, $list_full_properties),
  );
  $arComponentParameters["PARAMETERS"]["USE_ELEMENT_ICON"] = array(
    "PARENT" => "MAP_BALLOON_SETTINGS",
    "NAME" => GetMessage($l_prefix ."USE_ELEMENT_ICON"),
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "N",
  );
  $arComponentParameters["PARAMETERS"]["USE_SECTION_ICON"] = array(
    "PARENT" => "MAP_BALLOON_SETTINGS",
    "NAME" => GetMessage($l_prefix ."USE_SECTION_ICON"),
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "N",
  );
}

$use_icon_content = ($arCurrentValues["USE_ICON_CONTENT"] === null && $arComponentParameters["PARAMETERS"]["USE_ICON_CONTENT"]["DEFAULT"] == "Y") || $arCurrentValues["USE_ICON_CONTENT"] == "Y";
if(!$use_icon_content){
  unset($arComponentParameters["PARAMETERS"]["ICON_CONTENT"]);
}

$auto_scale = ($arCurrentValues["MAP_AUTO_SCALE_CENTER"] === null && $arComponentParameters['PARAMETERS']['MAP_AUTO_SCALE_CENTER']['DEFAULT'] == 'Y') || $arCurrentValues["MAP_AUTO_SCALE_CENTER"] == "Y";

$show_link = ($arCurrentValues["MAP_BALLOON_LINK_SHOW"] === null && $arComponentParameters['PARAMETERS']['MAP_BALLOON_LINK_SHOW']['DEFAULT'] == 'Y') || $arCurrentValues["MAP_BALLOON_LINK_SHOW"] == "Y";
if(!$show_link){
  unset($arComponentParameters["PARAMETERS"]["MAP_BALLOON_LINK_TEXT"]);
}

$use_cluster = ($arCurrentValues["MAP_CLUSTER"] === null && $arComponentParameters['PARAMETERS']['MAP_CLUSTER']['DEFAULT'] == 'Y') || $arCurrentValues["MAP_CLUSTER"] == "Y";
if(!$use_cluster){
  unset($arComponentParameters["PARAMETERS"]["MAP_CLUSTER_SIMPLE"]);
  unset($arComponentParameters["PARAMETERS"]["MAP_CLUSTER_CONTENT_WIDTH"]);
  unset($arComponentParameters["PARAMETERS"]["MAP_CLUSTER_CONTENT_LEFT_COLUMN_WIDTH"]);
}

$use_cluster_simple = ($arCurrentValues["MAP_CLUSTER_SIMPLE"] === null && $arComponentParameters['PARAMETERS']['MAP_CLUSTER_SIMPLE']['DEFAULT'] == 'Y') || $arCurrentValues["MAP_CLUSTER_SIMPLE"] == "Y";
if($use_cluster_simple){
  unset($arComponentParameters["PARAMETERS"]["MAP_CLUSTER_CONTENT_WIDTH"]);
  unset($arComponentParameters["PARAMETERS"]["MAP_CLUSTER_CONTENT_LEFT_COLUMN_WIDTH"]);
}

CIBlockParameters::Add404Settings($arComponentParameters, $arCurrentValues);