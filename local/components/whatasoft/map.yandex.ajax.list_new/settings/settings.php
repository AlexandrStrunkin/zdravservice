<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/whatasoft/map.yandex.ajax.list/lang/'.LANGUAGE_ID.'/settings.php');

$l_prefix = "WAS_GEOOBJECTSMAPBD_SETTINGS_";

$obJSPopup = new CJSPopup('',
	array(
		'TITLE' => GetMessage($l_prefix .'SET_POPUP_TITLE'),
		'SUFFIX' => 'yandex_map',
		'ARGS' => ''
	)
);

$arData = array();
if($_REQUEST['MAP_DATA']){
	$coords = explode(",", $_REQUEST["MAP_DATA"]);
	$arData["yandex_lat"] = $coords[0];
	$arData["yandex_lon"] = $coords[1];
	$arData["yandex_scale"] = $_REQUEST["MAP_SCALE"];
}
?>
<script type="text/javascript" src="/bitrix/js/whatasoft.geoobjectsmapbd/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
  $.noConflict();
</script>
<script type="text/javascript" src="/bitrix/components/whatasoft/map.yandex.ajax.list/settings/settings_load.js"></script>
<script type="text/javascript">
  BX.loadCSS('/bitrix/components/whatasoft/map.yandex.ajax.list/settings/settings.css');
  window._global_BX_UTF = <?echo defined('BX_UTF') && BX_UTF == true ? 'true' : 'false'?>;
  window.jsYandexMess = {
    noname: '<?echo CUtil::JSEscape(GetMessage($l_prefix .'SET_NONAME'))?>',
    nothing_found: '<?echo CUtil::JSEscape(GetMessage($l_prefix .'PARAM_INIT_MAP_NOTHING_FOUND'))?>'
  };

  if(null != window.jsWASYMapBDSearch){
    window.jsWASYMapBDSearch.clear();
  }

  if(null != window.jsWASYMapBD){
    window.jsWASYMapBD.clear();
  }
</script>
<form name="was_popup_form_ymap_bd">
<?
$obJSPopup->ShowTitlebar();
$obJSPopup->StartDescription('bx-edit-menu');
?>
	<p><b><?=GetMessage($l_prefix .'SET_POPUP_WINDOW_TITLE')?></b></p>
	<p class="note"><?=GetMessage($l_prefix .'SET_POPUP_WINDOW_DESCRIPTION')?></p>
<?
$obJSPopup->StartContent();
?>
<div class="was_map_cont">
<?
$APPLICATION->IncludeComponent('bitrix:map.yandex.system', '', array(
	'MAP_WIDTH' => 500,
	'MAP_HEIGHT' => 375,
	'INIT_MAP_LAT' => $arData['yandex_lat'],
	'INIT_MAP_LON' => $arData['yandex_lon'],
	'INIT_MAP_SCALE' => $arData['yandex_scale'],
	'CONTROLS' => array("ZOOM","MINIMAP","SCALELINE"),
	'OPTIONS' => array('ENABLE_SCROLL_ZOOM', 'ENABLE_DBLCLICK_ZOOM', 'ENABLE_DRAGGING'),
	'MAP_ID' => 'was_ymap_bd_list',
	'ONMAPREADY' => 'jsWASYMapBD.init',
	'ONMAPREADY_PROPERTY' => 'jsWASYMapBD.map',
	'DEV_MODE' => 'Y',
), false, array('HIDE_ICONS' => 'Y'));
?>
</div>
<div class="was_map_controls">
  <b><?=GetMessage($l_prefix .'SET_START_POS')?></b><br />
  <ul class="was_map_controls_list">
    <li><?=GetMessage($l_prefix .'SET_START_POS_LAT')?>: <span class="was_yandex_lat_value"></span><input type="hidden" name="was_yandex_lat" value="<?=htmlspecialcharsbx($arData['yandex_lat'])?>" /></li>
    <li><?=GetMessage($l_prefix .'SET_START_POS_LON')?>: <span class="was_yandex_lon_value"></span><input type="hidden" name="was_yandex_lon" value="<?=htmlspecialcharsbx($arData['yandex_lon'])?>" /></li>
    <li><?=GetMessage($l_prefix .'SET_START_POS_SCALE')?>: <span class="was_yandex_scale_value"></span><input type="hidden" name="was_yandex_scale" value="<?=htmlspecialcharsbx($arData['yandex_scale'])?>" /></li>
    <li><input type="checkbox" class="was_yandex_position_fix" id="was_yandex_bd_position_fix" name="was_yandex_position_fix" value="Y"<?if($arData['yandex_scale']){?> checked="checked"<?}?> /> <label for="was_yandex_bd_position_fix"><?=GetMessage($l_prefix .'SET_START_POS_FIX')?></label>&nbsp;|&nbsp;<a href="javascript:void(0)" class="was_restore_position"><?=GetMessage($l_prefix .'SET_START_POS_RESTORE')?></a></li>
  </ul>
</div>
<div class="bx-yandex-map-address-search" id="was_yandex_map_address_search">
	<?=GetMessage($l_prefix .'SET_ADDRESS_SEARCH')?>: <input type="text" name="address" value="" style="width: 380px;" onkeyup="jsWASYMapBDSearch.setTypingStarted(this)" autocomplete="off" />
</div>
<?
$obJSPopup->StartButtons();
?>
<input type="submit" value="<?=GetMessage($l_prefix .'SET_SUBMIT')?>" onclick="return jsWASYMapBD.__saveChanges();" class="adm-btn-save"/>
<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");?>