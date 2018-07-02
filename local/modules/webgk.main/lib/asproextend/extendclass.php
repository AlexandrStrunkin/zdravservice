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

}
