<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Entity;
use Bitrix\Main\Option;
use Webgk\Main\Hlblock\Prototype; 
if ($_REQUEST["phoneNumber"] && $_REQUEST["code"] && check_bitrix_sessid()) {
    $hlblock = Prototype::getInstance("SMSConfirmationCodes");

    $resultData = $hlblock->getElements(array(
        "select" => array("*"),
        "filter" => array("UF_PHONE_NUMBER" => $_REQUEST["phoneNumber"], "UF_SMS_CODE" => $_REQUEST["code"]),
        "cacheTime" => 0
    ));
    if (!empty($resultData)) {
        if ($resultData[0]["UF_TIMESTAMP_X"] >= time() - 180) {
            echo "ok";
        } else {
            echo "timestamp_error";    
        }
    } else {
        echo "error";    
    }    
} else {
    echo "error";
}
?>