<?
$response["exec_time"] = (microtime(true) - $response["exec_time"]);
$response = $APPLICATION->ConvertCharsetArray($response, SITE_CHARSET, "UTF-8");

echo json_encode($response);
// \Webgk\Main\Tools::log($response,'',true);
die();
?>
