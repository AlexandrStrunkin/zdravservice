<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Entity;
use Bitrix\Main\Option;
use Webgk\Main\Hlblock\Prototype;
if ($_REQUEST["phoneNumber"] && check_bitrix_sessid()) {  
    $curl = curl_init();
    $url = \COption::GetOptionString("grain.customsettings", "sms_sending_url"); 
    $login = \COption::GetOptionString("grain.customsettings", "sms_sending_login");
    $password = \COption::GetOptionString("grain.customsettings", "sms_sending_password");
    $confCode = rand(1000, 9999);
    $message = "Код подтверждения: ".$confCode;
    if ($url && $login && $password) {
        $paramArr = json_encode(['login' => $login, 'psw' => $password, 'phones' => $_REQUEST["phoneNumber"], 'mes' => $message]);
        curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1, //1 - возврат результата в виде строки, 0 - вывод результата в браузер
                CURLOPT_URL => $url, //урл для запроса
                CURLOPT_POST => 1, //метод POST
                CURLOPT_POSTFIELDS => $paramArr
            ]);
        $res = curl_exec($curl);
            curl_close($curl);
            $hlblock = Prototype::getInstance("SMSConfirmationCodes");

            $resultData = $hlblock->getElements(array(
                "select" => array("*"),
                "filter" => array("UF_PHONE_NUMBER" => $_REQUEST["phoneNumber"])
            ));
            if (!empty($resultData)) {
                foreach ($resultData as $curResult) {

                    $result = $hlblock->updateData($curResult["ID"], array(
                        'UF_PHONE_NUMBER' => $_REQUEST["phoneNumber"],
                        'UF_SMS_CODE' => $confCode,
                        'UF_TIMESTAMP_X' => time()
                    ));
                }
            } else {

                $result = $hlblock->addData(array(
                    'UF_PHONE_NUMBER' => $_REQUEST["phoneNumber"],
                    'UF_SMS_CODE' => $confCode,
                    'UF_TIMESTAMP_X' => time()
                ));
            }
    }
}
?>