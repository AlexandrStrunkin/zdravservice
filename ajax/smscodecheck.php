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
                $updatingUserFilter = array();
                if ($phoneNumber) {
                    $updatingUserFilter["PERSONAL_PHONE"] = $phoneNumber;
                }
                if ($_REQUEST["clientName"]) {
                    $explodedNameArr = explode(" ", $_REQUEST["clientName"]);
                    if (!empty($explodedNameArr[0])) {
                        $updatingUserFilter["LAST_NAME"] = $explodedNameArr[0];
                    }
                    if (!empty($explodedNameArr[1])) {
                        $updatingUserFilter["NAME"] = $explodedNameArr[1];
                    }
                    if (!empty($explodedNameArr[2])) {
                        $updatingUserFilter["SECOND_NAME"] = $explodedNameArr[2];
                    }
                }
                if ($_REQUEST["clientEmail"]) {
                    $updatingUserFilter["EMAIL"] = $_REQUEST["clientEmail"];
                }
                if ($_REQUEST["clientBirthday"]) {
                    $updatingUserFilter["PERSONAL_BIRTHDAY"] = $_REQUEST["clientBirthday"];
                }
                if (!empty($updatingUserFilter)) {
                    $userObj = new CUser;
                    $updUser = $userObj->Update((int)$userId, $updatingUserFilter);
                }
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