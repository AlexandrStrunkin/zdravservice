<?
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NO_AGENT_CHECK", true);

$response = array();
$response["status"] = "error";
$response["wrong_session"] = false;
$response["exec_time"] = microtime(true);
$response["errors"] = array();

$wrong_formatted_cache = false;
if(isset($_POST["cache_id"])){
  $cache_id = base64_decode($_POST["cache_id"]);
  $cache_id_items = explode("|", $cache_id);
  if($cache_id_items >= 5){
    define("SITE_ID", $cache_id_items[0]);
    define("SITE_TEMPLATE_ID", $cache_id_items[1]);
  }else{
    $wrong_formatted_cache = true;
  }
}

if(isset($_POST["site_id"]) && !defined("SITE_ID")){
  define("SITE_ID", $_POST["site_id"]);
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

Loc::loadMessages(__FILE__);
$l_prefix = "WAS_AJAX_UTIL_";

global $APPLICATION;
$APPLICATION->SetShowIncludeAreas(false);

if($wrong_formatted_cache){
  $response["errors"][] = GetMessage($l_prefix ."ERROR_CACHE_FORMAT");
}
if(!Loader::includeModule("iblock")){
  $response["errors"][] = GetMessage($l_prefix ."ERROR_MODULE_NOT_FOUND", array("#MODULE#" => "iblock"));
}
if(isset($need_include_module) && $need_include_module){
  if(!Loader::includeModule($need_include_module)){
    $response["errors"][] = GetMessage($l_prefix ."ERROR_MODULE_NOT_FOUND", array("#MODULE#" => $need_include_module));
  }
}
if(isset($need_session_check) && $need_session_check){
  if(bitrix_sessid() != $_POST["session_id"]){
    $response["errors"][] = GetMessage($l_prefix ."ERROR_SESSION");
    $response["wrong_session"] = true;
  }
}
if(isset($need_cache_check) && $need_cache_check){
  if(!isset($_POST["cache_id"])){
    $response["errors"][] = GetMessage($l_prefix ."ERROR_CACHE");
  }
}
if(!isset($need_not_convert_post) || !$need_not_convert_post){
  $_POST = $APPLICATION->ConvertCharsetArray($_POST, "UTF-8", SITE_CHARSET);
}
$response["session_id"] = bitrix_sessid();
?>