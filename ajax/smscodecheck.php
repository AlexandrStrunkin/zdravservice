<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Entity;
use Bitrix\Main\Option;
use Webgk\Main\Hlblock\Prototype; 
if ($_REQUEST["phoneNumber"] && $_REQUEST["code"] && check_bitrix_sessid()) {
    $phoneNumber = preg_replace("/\D/", "", $_REQUEST["phoneNumber"]);
    if (strlen($phoneNumber) == 11) {
        if (substr($phoneNumber, 0, 1) == "8") {
            $userPhone = substr_replace($phoneNumber, "7", 0, 1);
        }
        $phoneNumber = "+".$phoneNumber;    
    } 
    $hlblock = Prototype::getInstance("SMSConfirmationCodes");

    $resultData = $hlblock->getElements(array(
        "select" => array("*"),
        "filter" => array("UF_PHONE_NUMBER" => $phoneNumber, "UF_SMS_CODE" => $_REQUEST["code"]),
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