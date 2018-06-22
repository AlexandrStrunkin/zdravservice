<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Entity;
use Bitrix\Main\Option;
use Webgk\Main\Hlblock\Prototype;
if ($_REQUEST["phoneNumber"] && check_bitrix_sessid()) {
    $phoneNumber = preg_replace("/\D/", "", $_REQUEST["phoneNumber"]);
    if (strlen($phoneNumber) == 11) {
        if (substr($phoneNumber, 0, 1) == "8") {
            $userPhone = substr_replace($phoneNumber, "7", 0, 1);
        }
        $phoneNumber = "+".$phoneNumber;    
    }                        
    $url = \COption::GetOptionString("grain.customsettings", "sms_sending_url"); 
    $login = \COption::GetOptionString("grain.customsettings", "sms_sending_login");
    $password = \COption::GetOptionString("grain.customsettings", "sms_sending_password");
    $confCode = rand(1000, 9999);
    $message = "��� �������������: ".$confCode;
    if ($url && $login && $password) {
        $hlblock = Prototype::getInstance("SMSConfirmationCodes");

            $resultData = $hlblock->getElements(array(
                "select" => array("*"),
                "filter" => array("UF_PHONE_NUMBER" => $phoneNumber),
                "cacheTime" => 0
            ));
            if (!empty($resultData)) {
                foreach ($resultData as $curResult) {

                    $result = $hlblock->updateData($curResult["ID"], array(
                        'UF_PHONE_NUMBER' => $phoneNumber,
                        'UF_SMS_CODE' => $confCode,
                        'UF_TIMESTAMP_X' => time()
                    ));
                }
            } else {

                $result = $hlblock->addData(array(
                    'UF_PHONE_NUMBER' => $phoneNumber,
                    'UF_SMS_CODE' => $confCode,
                    'UF_TIMESTAMP_X' => time()
                ));
            }
        if ($result->getId() > 0) {
            $curl = curl_init();
            echo $phoneNumber."<br>";
            $paramArr = ['login' => $login, 'psw' => $password, 'phones' => $phoneNumber, 'mes' => $message, 'sender' => 'ZdesApteka'];
            curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => 1, //1 - ������� ���������� � ���� ������, 0 - ����� ���������� � �������
                    CURLOPT_URL => $url, //��� ��� �������
                    CURLOPT_POST => 1, //����� POST
                    CURLOPT_POSTFIELDS => $paramArr
                ]);
            $res = curl_exec($curl);
            echo $res;
                curl_close($curl);
        }
    }
}
?>