<?

namespace Webgk\Main\AsproExtend;

use Bitrix\Main\Config\Option;

Class Extendclass{

    const partnerName	= 'aspro';
    const solutionName	= 'next';
    const moduleID		= ASPRO_NEXT_MODULE_ID;
    const wizardID		= 'aspro:next';
    const devMode 		= false;

    public static function GetQuantityArray($totalCount, $arItemIDs = array(), $useStoreClick="N" , $regionalStoreQuantity = 0){
		static $arQuantityOptions, $arQuantityRights;
		if($arQuantityOptions === NULL){
			$arQuantityOptions = array(
				"USE_WORD_EXPRESSION" => Option::get(self::moduleID, "USE_WORD_EXPRESSION", "Y", SITE_ID),
				"MAX_AMOUNT" => Option::get(self::moduleID, "MAX_AMOUNT", "10", SITE_ID),
				"MIN_AMOUNT" => Option::get(self::moduleID, "MIN_AMOUNT", "2", SITE_ID),
				"EXPRESSION_FOR_MIN" => Option::get(self::moduleID, "EXPRESSION_FOR_MIN", GetMessage("EXPRESSION_FOR_MIN_DEFAULT"), SITE_ID),
				"EXPRESSION_FOR_MID" => Option::get(self::moduleID, "EXPRESSION_FOR_MID", GetMessage("EXPRESSION_FOR_MID_DEFAULT"), SITE_ID),
				"EXPRESSION_FOR_MAX" => Option::get(self::moduleID, "EXPRESSION_FOR_MAX", GetMessage("EXPRESSION_FOR_MAX_DEFAULT"), SITE_ID),
				"EXPRESSION_FOR_EXISTS" => Option::get(self::moduleID, "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID),
				"EXPRESSION_FOR_NOTEXISTS" => Option::get(self::moduleID, "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS_DEFAULT"), SITE_ID),
				"SHOW_QUANTITY_FOR_GROUPS" => (($tmp = Option::get(self::moduleID, "SHOW_QUANTITY_FOR_GROUPS", "", SITE_ID)) ? explode(",", $tmp) : array()),
				"SHOW_QUANTITY_COUNT_FOR_GROUPS" => (($tmp = Option::get(self::moduleID, "SHOW_QUANTITY_COUNT_FOR_GROUPS", "", SITE_ID)) ? explode(",", $tmp) : array()),
			);

			$arQuantityRights = array(
				"SHOW_QUANTITY" => false,
				"SHOW_QUANTITY_COUNT" => false,
			);

			global $USER;
			$res = \CUser::GetUserGroupList(self::GetUserID());
			while ($arGroup = $res->Fetch()){
				if(in_array($arGroup["GROUP_ID"], $arQuantityOptions["SHOW_QUANTITY_FOR_GROUPS"])){
					$arQuantityRights["SHOW_QUANTITY"] = true;
				}
				if(in_array($arGroup["GROUP_ID"], $arQuantityOptions["SHOW_QUANTITY_COUNT_FOR_GROUPS"])){
					$arQuantityRights["SHOW_QUANTITY_COUNT"] = true;
				}
			}
		}

		$indicators = 0;
		$totalAmount = $totalText = $totalHTML = $totalHTMLs = '';

        if ($regionalStoreQuantity <= 0 && $totalCount <= 0) {
            $arQuantityOptions["EXPRESSION_FOR_NOTEXISTS"] = "Нет в наличии";
        }

		if($arQuantityRights["SHOW_QUANTITY"]){
			if($totalCount > $arQuantityOptions["MAX_AMOUNT"]){
				$indicators = 3;
				$totalAmount = $arQuantityOptions["EXPRESSION_FOR_MAX"];
			}
			elseif($totalCount < $arQuantityOptions["MIN_AMOUNT"] && $totalCount > 0){
				$indicators = 1;
				$totalAmount = $arQuantityOptions["EXPRESSION_FOR_MIN"];
			}
			else{
				$indicators = 2;
				$totalAmount = $arQuantityOptions["EXPRESSION_FOR_MID"];
			}

			if($totalCount > 0){
				if($arQuantityRights["SHOW_QUANTITY_COUNT"]){
					$totalHTML = '<span class="first'.($indicators >= 1 ? ' r' : '').'"></span><span class="'.($indicators >= 2 ? ' r' : '').'"></span><span class="last'.($indicators >= 3 ? ' r' : '').'"></span>';
				}
				else{
					$totalHTML = '<span class="first r"></span>';
				}
			}
			else{
				$totalHTML = '<span class="null"></span>';
			}

			if($totalCount > 0){
				$totalText = $arQuantityOptions["EXPRESSION_FOR_EXISTS"];
			}else{



				if($useStoreClick=="Y"){
					$totalText = "<span class='store_view'>".$arQuantityOptions["EXPRESSION_FOR_NOTEXISTS"]."</span>";
				}else{
                    $totalText = $arQuantityOptions["EXPRESSION_FOR_NOTEXISTS"];
				}
			}

			if($arQuantityRights["SHOW_QUANTITY_COUNT"] && $totalCount > 0){
				if($arQuantityOptions["USE_WORD_EXPRESSION"] == "Y"){
					if(strlen($totalAmount)){
						if($useStoreClick=="Y"){
							$totalText = "<span class='store_view'>".$totalAmount."</span>";
						}else{
							$totalText = $totalAmount;
						}
					}
				}
				else{
					if($useStoreClick=="Y"){
						$totalText .= (strlen($totalText) ? " <span class='store_view'>(".$totalCount.")</span>" : "<span class='store_view'>".$totalCount."</span>");
					}else{
						$totalText .= (strlen($totalText) ? " (".$totalCount.")" : $totalCount);
					}
				}
			}
			$totalHTMLs ='<div class="item-stock" '.($arItemIDs["STORE_QUANTITY"] ? "id=".$arItemIDs["STORE_QUANTITY"] : "").'>';
			if ($totalCount > 0) {
				$totalHTMLs .= '<span class="icon '.$arClass[1].' stock'.'"></span><span class="value">'.$totalText.'</span>';
			}else{
				$totalHTMLs .= '<span class="value">'.$totalText.'</span>';
			}
			$totalHTMLs .='</div>';
		}

		$arOptions = array("OPTIONS" => $arQuantityOptions, "RIGHTS" => $arQuantityRights, "TEXT" => $totalText, "HTML" => $totalHTMLs);

		foreach(GetModuleEvents(ASPRO_NEXT_MODULE_ID, 'OnAsproGetTotalQuantityBlock', true) as $arEvent) // event for manipulation store quantity block
			ExecuteModuleEventEx($arEvent, array($totalCount, &$arOptions));

		return $arOptions;
	}

    public static function CheckTypeCount($totalCount){
		if(is_float($totalCount))
			return floatval($totalCount);
		else
			return intval($totalCount);
	}

	public static function GetTotalCount($arItem, $arParams = array(), $regionStorage = false){
		$totalCount = 0;
		if($arParams['USE_REGION'] == 'Y' && $arParams['STORES'])
		{
			$arSelect = array('ID', 'PRODUCT_AMOUNT');
			$arFilter = array(
				'ID' => $arParams['STORES'],
                "UF_REGION_STORAGE" => $regionStorage,
			);
			if($arItem['OFFERS'])
			{
				foreach($arItem['OFFERS'] as $arOffer)
				{
					$quantity = 0;
					$rsStore = \CCatalogStore::GetList(array(), array_merge($arFilter, array('PRODUCT_ID' => $arOffer['ID'])), false, false, $arSelect);
					while($arStore = $rsStore->Fetch())
					{
						$quantity += $arStore['PRODUCT_AMOUNT'];
					}
					$totalCount += $quantity;
				}
			}
			else
			{
				$rsStore = \CCatalogStore::GetList(array(), array_merge($arFilter, array('PRODUCT_ID' => $arItem['ID'])), false, false, $arSelect);
				while($arStore = $rsStore->Fetch())
				{
					$quantity += $arStore['PRODUCT_AMOUNT'];
				}
				$totalCount = $quantity;
			}
		}
		else
		{
			if($arItem['OFFERS'])
			{
				foreach($arItem['OFFERS'] as $arOffer)
					$totalCount += $arOffer['CATALOG_QUANTITY'];
			}
			else
				$totalCount = ($arItem['~CATALOG_QUANTITY'] != $arItem['CATALOG_QUANTITY'] ? $arItem['~CATALOG_QUANTITY'] : $arItem['CATALOG_QUANTITY']);
		}

		foreach(GetModuleEvents(ASPRO_NEXT_MODULE_ID, 'OnAsproGetTotalQuantity', true) as $arEvent) // event for manipulation total quantity
			ExecuteModuleEventEx($arEvent, array($arItem, $arParams, &$totalCount));

		return self::CheckTypeCount($totalCount);
	}

    public static function GetUserID(){
        static $userID;
        if($userID === NULL)
        {
            global $USER;
            $userID = \CUser::GetID();
            $userID = ($userID > 0 ? $userID : 0);
        }
        return $userID;
    }

    public static function GetAddToBasketArray(&$arItem, $totalCount = 0, $defaultCount = 1, $basketUrl = '', $bDetail = false, $arItemIDs = array(), $class_btn = "small", $arParams=array()){
		static $arAddToBasketOptions, $bUserAuthorized;
		if($arAddToBasketOptions === NULL){
			$arAddToBasketOptions = array(
				"SHOW_BASKET_ONADDTOCART" => Option::get(self::moduleID, "SHOW_BASKET_ONADDTOCART", "Y", SITE_ID) == "Y",
				"USE_PRODUCT_QUANTITY_LIST" => Option::get(self::moduleID, "USE_PRODUCT_QUANTITY_LIST", "Y", SITE_ID) == "Y",
				"USE_PRODUCT_QUANTITY_DETAIL" => Option::get(self::moduleID, "USE_PRODUCT_QUANTITY_DETAIL", "Y", SITE_ID) == "Y",
				"BUYNOPRICEGGOODS" => Option::get(self::moduleID, "BUYNOPRICEGGOODS", "NOTHING", SITE_ID),
				"BUYMISSINGGOODS" => Option::get(self::moduleID, "BUYMISSINGGOODS", "ADD", SITE_ID),
				"EXPRESSION_ORDER_BUTTON" => Option::get(self::moduleID, "EXPRESSION_ORDER_BUTTON", GetMessage("EXPRESSION_ORDER_BUTTON_DEFAULT"), SITE_ID),
				"EXPRESSION_ORDER_TEXT" => Option::get(self::moduleID, "EXPRESSION_ORDER_TEXT", GetMessage("EXPRESSION_ORDER_TEXT_DEFAULT"), SITE_ID),
				"EXPRESSION_SUBSCRIBE_BUTTON" => Option::get(self::moduleID, "EXPRESSION_SUBSCRIBE_BUTTON", GetMessage("EXPRESSION_SUBSCRIBE_BUTTON_DEFAULT"), SITE_ID),
				"EXPRESSION_SUBSCRIBED_BUTTON" => Option::get(self::moduleID, "EXPRESSION_SUBSCRIBED_BUTTON", GetMessage("EXPRESSION_SUBSCRIBED_BUTTON_DEFAULT"), SITE_ID),
			);

			global $USER;
			$bUserAuthorized = $USER->IsAuthorized();
		}



		$buttonText = $buttonHTML = $buttonACTION = '';
		$quantity=$ratio=1;
		$max_quantity=0;
		$float_ratio=is_double($arItem["CATALOG_MEASURE_RATIO"]);


		if($arItem["CATALOG_MEASURE_RATIO"]){
			$quantity=$arItem["CATALOG_MEASURE_RATIO"];
			$ratio=$arItem["CATALOG_MEASURE_RATIO"];
		}else{
			$quantity=$defaultCount;
		}
		if($arItem["CATALOG_QUANTITY_TRACE"]=="Y"){
			if($totalCount < $quantity){
				$quantity=($totalCount>$arItem["CATALOG_MEASURE_RATIO"] ? $totalCount : $arItem["CATALOG_MEASURE_RATIO"] );
			}
			$max_quantity=$totalCount;
		}

		$canBuy = $arItem["CAN_BUY"];
		if($arParams['USE_REGION'] == 'Y' && $arParams['STORES'])
			$canBuy = ($totalCount || ((!$totalCount && $arItem["CATALOG_QUANTITY_TRACE"] == "N") || (!$totalCount && $arItem["CATALOG_QUANTITY_TRACE"] == "Y" && $arItem["CATALOG_CAN_BUY_ZERO"] == "Y")));
		$arItem["CAN_BUY"] = $canBuy;

		$arItemProps = (($arParams['PRODUCT_PROPERTIES']) ? implode(';', $arParams['PRODUCT_PROPERTIES']) : "");
		$partProp=($arParams["PARTIAL_PRODUCT_PROPERTIES"] ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : "" );
		$addProp=($arParams["ADD_PROPERTIES_TO_BASKET"] ? $arParams["ADD_PROPERTIES_TO_BASKET"] : "" );
		$emptyProp=$arItem["EMPTY_PROPS_JS"];
		global $arTheme;
		if($arItem["OFFERS"]){
			$type_sku = (isset($arTheme["TYPE_SKU"]["VALUE"]) ? $arTheme["TYPE_SKU"]["VALUE"] : $arTheme["TYPE_SKU"]);
			if(!$bDetail && $arItem["OFFERS_MORE"] != "Y" && (is_array($arTheme) && $type_sku != "TYPE_2")){
				$buttonACTION = 'ADD';
				$buttonText = array(GetMessage('EXPRESSION_ADDTOBASKET_BUTTON_DEFAULT'), GetMessage('EXPRESSION_ADDEDTOBASKET_BUTTON_DEFAULT'));
				$buttonHTML = '<span class="btn btn-default transition_bg '.$class_btn.' read_more1 to-cart animate-load" id="'.$arItemIDs['BUY_LINK'].'" data-offers="N" data-iblockID="'.$arItem["IBLOCK_ID"].'" data-item="'.$arItem["ID"].'"><i></i><span>'.$buttonText[0].'</span></span><a rel="nofollow" href="'.$basketUrl.'" id="'.$arItemIDs['BASKET_LINK'].'" class="'.$class_btn.' in-cart btn btn-default transition_bg" data-item="'.$arItem["ID"].'"  style="display:none;"><i></i><span>'.$buttonText[1].'</span></a>';
			}
			elseif(($bDetail && $arItem["FRONT_CATALOG"] == "Y") || $arItem["OFFERS_MORE"]=="Y" || (is_array($arTheme) && $type_sku == "TYPE_2")){
				$buttonACTION = 'MORE';
				$buttonText = array(GetMessage('EXPRESSION_READ_MORE_OFFERS_DEFAULT'));
				$buttonHTML = '<a class="btn btn-default basket read_more" rel="nofollow" href="'.$arItem["DETAIL_PAGE_URL"].'" data-item="'.$arItem["ID"].'">'.$buttonText[0].'</a>';
			}
		}
		else{
			if($bPriceExists = isset($arItem["MIN_PRICE"]) && $arItem["MIN_PRICE"]["VALUE"] > 0){
				// price exists
				if($totalCount > 0){
					// rest exists
					if((isset($arItem["CAN_BUY"]) && $arItem["CAN_BUY"]) || (isset($arItem["MIN_PRICE"]) && $arItem["MIN_PRICE"]["CAN_BUY"] == "Y")){
						if($bDetail && $arItem["FRONT_CATALOG"] == "Y"){

						}else{

							$arItem["CAN_BUY"] = 1;
							$buttonACTION = 'ADD';
							$buttonText = array(GetMessage('EXPRESSION_ADDTOBASKET_BUTTON_DEFAULT'), GetMessage('EXPRESSION_ADDEDTOBASKET_BUTTON_DEFAULT'));
							$buttonHTML = '<span data-value="'.$arItem["MIN_PRICE"]["DISCOUNT_VALUE"].'" data-currency="'.$arItem["MIN_PRICE"]["CURRENCY"].'" class="'.$class_btn.' to-cart btn btn-default transition_bg animate-load" data-item="'.$arItem["ID"].'" data-float_ratio="'.$float_ratio.'" data-ratio="'.$ratio.'" data-bakset_div="bx_basket_div_'.$arItem["ID"].'" data-props="'.$arItemProps.'" data-part_props="'.$partProp.'" data-add_props="'.$addProp.'"  data-empty_props="'.$emptyProp.'" data-offers="'.$arItem["IS_OFFER"].'" data-iblockID="'.$arItem["IBLOCK_ID"].'"  data-quantity="'.$quantity.'"><i></i><span>'.$buttonText[0].'</span></span><a rel="nofollow" href="'.$basketUrl.'" class="'.$class_btn.' in-cart btn btn-default transition_bg" data-item="'.$arItem["ID"].'"  style="display:none;"><i></i><span>'.$buttonText[1].'</span></a>';
						}
					}
				}
				else{
					if(!strlen($arAddToBasketOptions['EXPRESSION_ORDER_BUTTON'])){
						$arAddToBasketOptions['EXPRESSION_ORDER_BUTTON']=GetMessage("EXPRESSION_ORDER_BUTTON_DEFAULT");
					}
					// no rest
                }
			}
		}

		$arOptions = array("OPTIONS" => $arAddToBasketOptions, "TEXT" => $buttonText, "HTML" => $buttonHTML, "ACTION" => $buttonACTION, "RATIO_ITEM" => $ratio, "MIN_QUANTITY_BUY" => $quantity, "MAX_QUANTITY_BUY" => $max_quantity, "CAN_BUY" => $canBuy);

		foreach(GetModuleEvents(ASPRO_NEXT_MODULE_ID, 'OnAsproGetBuyBlockElement', true) as $arEvent) // event for manipulation with buy block element
			ExecuteModuleEventEx($arEvent, array($arItem, $totalCount, $arParams, &$arOptions));

		return $arOptions;
	}

    public static function checkVersionModule($version = '1.0.0', $module="catalog"){
        if($info = CModule::CreateModuleObject($module))
        {
            if(!CheckVersion($version, $info->MODULE_VERSION))
                return true;
        }
        return false;
    }
    public static function formatJsName($name = ''){
        return htmlspecialcharsbx($name);
    }

}
