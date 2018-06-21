<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Entity;
use Bitrix\Main\Option;
use Webgk\Main\Hlblock\Prototype; 
if ($_REQUEST["phoneNumber"] && $_REQUEST["code"]) {
    $hlblock = Prototype::getInstance("SMSConfirmationCodes");

    $resultData = $hlblock->getElements(array(
        "select" => array("*"),
        "filter" => array("UF_PHONE_NUMBER" => $_REQUEST["phoneNumber"]),
        "cacheTime" => 0
    ));
    if (!empty($resultData)) {
        if ($_REQUEST["code"] == $resultData[0]["UF_SMS_CODE"]) {
            echo "ok";
        } else {
            echo "error";
        }
    }    
} else {
    echo "error";
}
?>