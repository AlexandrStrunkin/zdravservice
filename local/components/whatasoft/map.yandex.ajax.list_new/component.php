<?
// if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
// /** @var CBitrixComponent $this */
// /** @var array $arParams */
// /** @var array $arResult */
// /** @var string $componentPath */
// /** @var string $componentName */
// /** @var string $componentTemplate */
// /** @global CDatabase $DB */
// /** @global CUser $USER */
// /** @global CMain $APPLICATION */
//
// /** @global CIntranetToolbar $INTRANET_TOOLBAR */
// global $INTRANET_TOOLBAR;
//
// use Bitrix\Main\Loader,
// 	Bitrix\Iblock;
//
// if(!Loader::includeModule("whatasoft.geoobjectsmapbd")){
//   ShowError(GetMessage("WAS_GEOOBJECTSMAPBD_MODULE_NOT_INSTALLED"));
//   return;
// }
//
// \CJSCore::Init(array("jquery"));
//
// $mapMarker = CMapMarker::GetInstance();
// $hlblock_markers_table = $mapMarker->GetHLBlockTableName();
//
// $arParams["MAP_ICON_DATA"] = array();
// if(isset($arParams["MAP_ICON"]) && strlen($arParams["MAP_ICON"])){
//   $arParams["MAP_ICON_DATA"] = $mapMarker->GetMarkerIcon($arParams["MAP_ICON"]);
// }
//
// if(!isset($arParams["MAP_SCALE"]) || strlen($arParams["MAP_SCALE"]) < 1){
// 	$arParams["MAP_SCALE"] = "10";
// }
// if(!isset($arParams["MAP_CENTER"]) || strlen($arParams["MAP_CENTER"]) < 1){
// 	$arParams["MAP_LON"] = "55.76,37.64";
// }
// if(!isset($arParams["MAP_WIDTH"]) || strlen($arParams["MAP_WIDTH"]) < 1){
// 	$arParams["MAP_WIDTH"] = "100%";
// }
// if(!isset($arParams["MAP_HEIGHT"]) || strlen($arParams["MAP_HEIGHT"]) < 1){
// 	$arParams["MAP_HEIGHT"] = "400px";
// }
//
// $arParams["CONTROLS_TO_YMAP"] = array(
//   'GEO' => 'geolocationControl',
//   'SEARCH' => 'searchControl',
//   'ROUTE' => 'routeEditor',
//   'TRAFFIC' => 'trafficControl',
//   'TYPE' => 'typeSelector',
//   'FULLSCREEN' => 'fullscreenControl',
//   'ZOOM' => 'zoomControl',
//   'RULER' => 'rulerControl',
// );
// $arParams["MAP_YMAP_CONTROLS"] = array();
// foreach($arParams["CONTROLS"] as $control){
//   if(isset($arParams["CONTROLS_TO_YMAP"][$control])){
//     $arParams["MAP_YMAP_CONTROLS"][] = $arParams["CONTROLS_TO_YMAP"][$control];
//   }
// }
//
// if(!in_array($arParams["INIT_MAP_TYPE"], array("MAP", "SATELLITE", "HYBRID"))){
//   $arParams["INIT_MAP_TYPE"] = "MAP";
// }
// $arParams["INIT_MAP_TYPE"] = strtolower($arParams["INIT_MAP_TYPE"]);
//
// $arParams["BEHAVIORS_TO_YMAP"] = array(
//   'SCROLL_ZOOM' => 'scrollZoom',
//   'DBLCLICK_ZOOM' => 'dblClickZoom',
//   'RIGHT_MAGNIFIER' => 'rightMouseButtonMagnifier',
//   'DRAGGING' => 'drag',
// );
// $arParams["MAP_YMAP_BEHAVIORS"] = array();
// foreach($arParams["BEHAVIORS"] as $behavior){
//   if(isset($arParams["BEHAVIORS_TO_YMAP"][$behavior])){
//     $arParams["MAP_YMAP_BEHAVIORS"][] = $arParams["BEHAVIORS_TO_YMAP"][$behavior];
//   }
// }
//
// if(!isset($arParams["MAP_CLUSTER_SIMPLE"])){
//   $arParams["MAP_CLUSTER_SIMPLE"] = "N";
// }
// if(!$arParams["MAP_BALLOON_NAME"] && !$arParams["MAP_BALLOON_DETAIL_IMG"] && !$arParams["MAP_BALLOON_TEXT"] && $arParams["MAP_BALLOON_LINK_SHOW"] != "Y"){
//   $arParams["MAP_CLUSTER_SIMPLE"] = "Y";
// }
// if(!isset($arParams["MAP_BALLOON_LINK_NEW_WINDOW"])){
//   $arParams["MAP_BALLOON_LINK_NEW_WINDOW"] = "Y";
// }
//
// $arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
// if(strlen($arParams["IBLOCK_TYPE"])<=0)
// 	$arParams["IBLOCK_TYPE"] = "news";
// $arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
// $arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);
// $arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N";
//
// $arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
// if(strlen($arParams["SORT_BY1"])<=0)
// 	$arParams["SORT_BY1"] = "ACTIVE_FROM";
// if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
// 	$arParams["SORT_ORDER1"]="DESC";
//
// if(strlen($arParams["SORT_BY2"])<=0)
// 	$arParams["SORT_BY2"] = "SORT";
// if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
// 	$arParams["SORT_ORDER2"]="ASC";
//
// $arParams["REQUEST_LIMIT"] = intval($arParams["REQUEST_LIMIT"]);
// if($arParams["REQUEST_LIMIT"] < 100){
//   $arParams["REQUEST_LIMIT"] = 100;
// }else if($arParams["REQUEST_LIMIT"] > 5000){
//   $arParams["REQUEST_LIMIT"] = 5000;
// }
//
// if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])){
// 	$arrFilter = array();
// }else{
// 	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
// 	if(!is_array($arrFilter))
// 		$arrFilter = array();
// }
//
// $arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";
//
// $arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);
//
// $arParams["CHECK_PERMISSIONS"] = $arParams["CHECK_PERMISSIONS"]!="N";
//
// $arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
// if(!is_array($arParams["GROUP_PERMISSIONS"])){
// 	$arParams["GROUP_PERMISSIONS"] = array(1);
// }
//
// $bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
// if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"])){
// 	$arUserGroupArray = $USER->GetUserGroupArray();
// 	foreach($arParams["GROUP_PERMISSIONS"] as $PERM){
// 		if(in_array($PERM, $arUserGroupArray)){
// 			$bUSER_HAVE_ACCESS = true;
// 			break;
// 		}
// 	}
// }
//
// $arParams["MAP_BALLOON_COORDS"] = str_replace("PROPERTY_", "", $arParams["MAP_BALLOON_COORDS"]);
//
// if(!defined('WAS_YMAP_SCRIPT_LOADED')){
// 	$arParams['YANDEX_VERSION'] = "2.1";
// 	switch(LANGUAGE_ID){
// 		case 'ru':
// 			$arParams['LOCALE'] = 'ru-RU';
// 		break;
// 		case 'ua':
// 			$arParams['LOCALE'] = 'ru-UA';
// 		break;
// 		case 'tk':
// 			$arParams['LOCALE'] = 'tr-TR';
// 		break;
// 		default:
// 			$arParams['LOCALE'] = 'en-US';
// 		break;
// 	}
// 	$arResult['MAPS_SCRIPT_URL'] = 'https://api-maps.yandex.ru/'.$arParams['YANDEX_VERSION'].'/?load=package.full&mode=release&lang='.$arParams['LOCALE'].'&wizard=bitrix&ns=whatasoftMaps';
//   $APPLICATION->AddHeadScript($arResult['MAPS_SCRIPT_URL']);
//   define('WAS_YMAP_SCRIPT_LOADED', 1);
// }
//
// $arParams["arrFilter"] = $arrFilter;
//
// $obCache = CCacheParams::GetInstance();
// $cache_id = $obCache->SetCache($componentName, $arParams);
// $arResult["AJAX_SESSION_ID"] = bitrix_sessid();
//
// if(!Loader::includeModule("iblock")){
//   ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
//   return;
// }
// if(is_numeric($arParams["IBLOCK_ID"])){
//   $rsIBlock = CIBlock::GetList(array(), array(
//     "ACTIVE" => "Y",
//     "ID" => $arParams["IBLOCK_ID"],
//   ));
// }else{
//   $rsIBlock = CIBlock::GetList(array(), array(
//     "ACTIVE" => "Y",
//     "CODE" => $arParams["IBLOCK_ID"],
//     "SITE_ID" => SITE_ID,
//   ));
// }
// if($arResult = $rsIBlock->GetNext()){
//   $arResult["AJAX_CACHE_ID"] = $cache_id;
//   $arResult["AJAX_SESSION_ID"] = bitrix_sessid();
//   $arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
//   $this->includeComponentTemplate();
// }else{
//   Iblock\Component\Tools::process404(
//     trim($arParams["MESSAGE_404"]) ?: GetMessage("WAS_GEOOBJECTSMAPBD_SECTION_NA")
//     ,true
//     ,$arParams["SET_STATUS_404"] === "Y"
//     ,$arParams["SHOW_404"] === "Y"
//     ,$arParams["FILE_404"]
//   );
// }
//
// if(isset($arResult["ID"])){
// 	$arTitleOptions = null;
// 	if($USER->IsAuthorized()){
// 		if($APPLICATION->GetShowIncludeAreas() || (is_object($GLOBALS["INTRANET_TOOLBAR"]) && $arParams["INTRANET_TOOLBAR"]!=="N"))
// 		{
// 			if(Loader::includeModule("iblock")){
// 				$arButtons = CIBlock::GetPanelButtons(
// 					$arResult["ID"],
// 					0,
// 					$arParams["PARENT_SECTION"],
// 					array("SECTION_BUTTONS"=>false)
// 				);
//
// 				if($APPLICATION->GetShowIncludeAreas()){
// 					$this->addIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
// 				}
//
// 				if(
// 					is_array($arButtons["intranet"])
// 					&& is_object($INTRANET_TOOLBAR)
// 					&& $arParams["INTRANET_TOOLBAR"]!=="N"
// 				){
// 					$APPLICATION->AddHeadScript('/bitrix/js/main/utils.js');
// 					foreach($arButtons["intranet"] as $arButton)
// 						$INTRANET_TOOLBAR->AddButton($arButton);
// 				}
// 			}
// 		}
// 	}
//
// 	return $arResult["ELEMENTS"];
// }
