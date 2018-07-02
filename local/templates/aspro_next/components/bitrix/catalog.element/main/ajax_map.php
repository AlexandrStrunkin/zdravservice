<?require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$id = $_POST['id'];
$arrMapFilter = $_POST['stores'];
$arrMapFilter['>PRODUCT_AMOUNT'] = 0;


?>
<div class="modal_popup_window overlay"></div>
<div id="stores_map" class="modal_popup_window popup">

	<a href="#" class="close modal_popup_window_close"><i></i></a>

	<div class="popup_content">
		<div class="popup_head">
			<h2>Наличие в аптеках города</h2>
		</div>

		<?$APPLICATION->IncludeComponent(
			"whatasoft:map.yandex.ajax.list_new",
			"",
			Array(
				"BEHAVIORS" => array("DBLCLICK_ZOOM","DRAGGING"),
				"CHECK_DATES" => "Y",
				"CONTROLS" => array("TYPE","ZOOM"),
				"DETAIL_URL" => "",
				"FILTER_NAME" => "arrMapFilter",
				"STORES" => $arrMapFilter,
				"IBLOCK_ID" => "",
				"IBLOCK_TYPE" => "-",
				"INCLUDE_SUBSECTIONS" => "Y",
				"INIT_MAP_TYPE" => "MAP",
				"MAP_AUTO_SCALE_CENTER" => "Y",
				"MAP_BALLOON_COLOR" => "",
				"MAP_BALLOON_COORDS" => "",
				"MAP_BALLOON_DETAIL_IMG" => "FIELD_PREVIEW_PICTURE",
				"MAP_BALLOON_LINK_NEW_WINDOW" => "Y",
				"MAP_BALLOON_LINK_SHOW" => "N",
				"MAP_BALLOON_NAME" => "FIELD_NAME",
				"MAP_BALLOON_PROPERTIES" => array("",""),
				"MAP_BALLOON_TEXT" => "FIELD_PREVIEW_TEXT",
				"MAP_BALLOON_TITLE" => "TITLE",
				"MAP_CENTER" => "55.76,37.64",
				"MAP_CLUSTER" => "Y",
				"MAP_CLUSTER_CONTENT_LEFT_COLUMN_WIDTH" => "120",
				"MAP_CLUSTER_CONTENT_WIDTH" => "400",
				"MAP_CLUSTER_SIMPLE" => "Y",
				"MAP_COLOR" => "009b76",
				"MAP_HEIGHT" => "40vh",
				"MAP_ICON" => "",
				"MAP_ID" => "",
				"MAP_SCALE" => "10",
				"MAP_WIDTH" => "100%",
				"MESSAGE_404" => "",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"REQUEST_LIMIT" => "1000",
				"SET_STATUS_404" => "N",
				"SHOW_404" => "N",
				"SORT_BY1" => "ACTIVE_FROM",
				"SORT_BY2" => "SORT",
				"SORT_ORDER1" => "DESC",
				"SORT_ORDER2" => "ASC",
				"USE_ELEMENT_ICON" => "N",
				"USE_ICON_CONTENT" => "Y",
				"USE_SECTION_ICON" => "N",
				"ELEMENT_ID" => $id,
				)
			);?>
		</div>
</div>
