<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Entity;
use Bitrix\Main\Option;
use Webgk\Main\Hlblock\Prototype;
if ($_REQUEST["phoneNumber"] && $_REQUEST["code"] && check_bitrix_sessid()) {
    $phoneNumber = \Webgk\Main\Tools::formatUserPhone($_REQUEST["phoneNumber"]);
    if ($_REQUEST["oldPhoneNumber"]) {
        $oldPhoneNumber = \Webgk\Main\Tools::formatUserPhone($_REQUEST["oldPhoneNumber"]);
        $userId = "";
        $userList = CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("PERSONAL_PHONE" => $oldPhoneNumber));
        while ($arUsers = $userList -> Fetch()) {
            $userId = $arUsers["ID"];
        }
    }
    $hlblock = Prototype::getInstance("SMSConfirmationCodes");
    $resultData = $hlblock->getElements(array(
        "select" => array("*"),
        "filter" => array("UF_PHONE_NUMBER" => $phoneNumber, "UF_SMS_CODE" => $_REQUEST["code"]),
        "cacheTime" => 0
    ));
        \Webgk\Main\Tools::Log($resultData);

    if (!empty($resultData)) {
        if ($resultData[0]["UF_TIMESTAMP_X"] >= time() - 180) {
            if (strlen($userId) > 0 && $userId > 0) {
                $userObj = new CUser;
                $updUser = $userObj->Update((int)$userId, array("PERSONAL_PHONE" => $phoneNumber));
            }
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