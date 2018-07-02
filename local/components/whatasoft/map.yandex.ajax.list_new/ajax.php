<?
use Whatasoft\Cache\CCacheParams;

$need_session_check = true;
$need_cache_check = true;
//$need_not_convert_post = true;
$need_include_module = "whatasoft.geoobjectsmapbd";
require_once($_SERVER["DOCUMENT_ROOT"]."/local/components/whatasoft/ajax.util/include_before.php");

global $APPLICATION;


if(!count($response["errors"])){
    $obCache = CCacheParams::GetInstance();
    $arParams = $obCache->GetCache($_POST["cache_id"]);
    if(is_array($arParams)){
        $response["status"] = "ok";
        $component_response = $APPLICATION->IncludeComponent(
            "whatasoft:map.yandex.ajax.list_new",
            $arParams["COMPONENT_TEMPLATE"],
            $arParams,
            null,
            array("HIDE_ICONS" => "Y")
        );
        $response = array_merge($response, $component_response);
    }else{
        $response["errors"][] = "Wrong cache ID";
    }

}

require_once($_SERVER["DOCUMENT_ROOT"]."/local/components/whatasoft/ajax.util/include_after.php");
?>
