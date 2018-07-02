<?php 

namespace Webgk\Main;
use Bitrix\Main\Loader;
use Bitrix\Main as MainModule;
use Bitrix\Iblock;    
use Bitrix\Main\Entity;
use Bitrix\Main\Option;
use Webgk\Main\Hlblock\Prototype; 
class ClientBonusInfo {
    
    /**
    * получение бонусного баланса клиента и добавление/обновление соответствующей записи HL-блока
    * 
    * @param string $phoneNumber
    */
    public function ClientsInfo($phoneNumber = ""){
        $curl = curl_init();
    if (!empty($phoneNumber)) {
        $paramArr = json_encode(['phone' => $phoneNumber]);
    } else {
        return false;
    }
    $url = \COption::GetOptionString("grain.customsettings", "ws_bonus_url"); 
    $login = \COption::GetOptionString("grain.customsettings", "ws_bonus_login");
    $password = \COption::GetOptionString("grain.customsettings", "ws_bonus_password");
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1, //1 - возврат результата в виде строки, 0 - вывод результата в браузер
        CURLOPT_URL => $url, //урл для запроса
        CURLOPT_PORT => 333,
        CURLOPT_POST => 1, //метод POST
        CURLOPT_USERPWD => $login.":".$password,
        CURLOPT_POSTFIELDS => $paramArr
    ]);
    // Отправляем запроса и сохраняем его в $res
    $res = curl_exec($curl);
    curl_close($curl);
    if ($res) {
        $clientInfo = simplexml_load_string($res);
    }
    $clientInfoArr = array();
    // Закрываем запрос и удаляем инициализацию $curl
    if ($clientInfo) {
        $cardNumber = "";
        $totalBalance = 0; 
        foreach ($clientInfo->attributes() as $attrKey => $attrVal) {
            if ($attrKey == "Number") {
                $clientInfoArr["PHONE_NUMBER"] = (string)$attrVal;
            }
        }
        foreach ($clientInfo->phone->Gender as $gender) {
            $genderSymbol = iconv($gender);
        }
        foreach ($clientInfo->phone->BDate as $bdate) {
            $birthday = $bdate;
        }
        $totalBalance = floatval($clientInfo->Balance[0]);
        $clientInfoArr["USER_BALANCE"] = $totalBalance;  
        $hlblock = Prototype::getInstance("ClientsBonusCards");
        
           $resultData = $hlblock->getElements(array(
                    "select" => array("*"),
                    "filter" => array("UF_PHONE_NUMBER" => $clientInfoArr["PHONE_NUMBER"])
               ));
           if (!empty($resultData)) {
               foreach ($resultData as $curResult) {
                   
               $result = $hlblock->updateData($curResult["ID"], array(
                           'UF_PHONE_NUMBER' => $clientInfoArr["PHONE_NUMBER"],
                           'UF_TOTAL_BALANCE' => $clientInfoArr["USER_BALANCE"],
                           'UF_TIMESTAMP_X' => time()
                       ));
               }
           } else {
               
               $result = $hlblock->addData(array(
                           'UF_PHONE_NUMBER' => $clientInfoArr["PHONE_NUMBER"],
                           'UF_TOTAL_BALANCE' => $clientInfoArr["USER_BALANCE"],
                           'UF_TIMESTAMP_X' => time()
                       ));
           }
           $userId = "";
           $userList = \CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("PERSONAL_PHONE" => $phoneNumber));
           while ($arUsers = $userList -> Fetch()) {
               $userId = $arUsers["ID"];
           }
           $updUserFields = array();
           if (isset($_SESSION["SERVICE_DATA"]["UPDATE_BONUS"])) {
               if ($userId) {
                   if (strlen($birthday)) {
                       $updUserFields["PERSONAL_BIRTHDAY"] = date("d.m.Y", strtotime($birthday));
                   }
                   if (strlen($genderSymbol)) {
                        $updUserFields["PERSONAL_GENDER"] = $genderSymbol;    
                   }
                   if (!empty($updUserFields)) {
                        unset($_SESSION["SERVICE_DATA"]["UPDATE_BONUS"]);
                       $userObj = new \CUser;
                       $userObj -> Update($userId, $updUserFields);
                   }
               }
                unset($_SESSION["SERVICE_DATA"]["UPDATE_BONUS"]);
           }
    }
    $resultId = $result->getId();
    if ($resultId) {
        return $resultId;
    } else {
        return false;
    }
    }

    /**
    * обновление записей HL-блока бонусов, 
    * последнее добавление/обновление которых было произведено в течение последних суток
    * 
    */
    public function checkUpdatedClientsInfo() {
          
           $hlblock = Prototype::getInstance("ClientsBonusCards");
           $resultData = $hlblock->getElements(array(
                "select" => array("*"),
                "filter" => array(">UF_TIMESTAMP_X" => time() - 86400),
                "limit" => 100
           ));
           $phonesArr = array();
           foreach ($resultData as $curResult) {
                $phonesArr[] = $curResult["UF_PHONE_NUMBER"];
           }
           foreach ($phonesArr as $phoneNumber) {
               ClientBonusInfo::ClientsInfo($phoneNumber);
           }
    }
    
    /**
    * запуск агента, обновляющего записи HL-блока информации о бонусах
    * 
    */
    function gettingClientsInfoAgent() {
        if (ClientBonusInfo::checkUpdatedClientsInfo()) {
            return "\\Webgk\\Main\\ClientBonusInfo::gettingClientsInfoAgent();";
        }    
    }
    
    /**
    * получение баланса пользователя по номеру телефона из БД
    * 
    * @param string $phoneNumber
    */
    function gettingUserBalanceFromDB($phoneNumber) {
        if (!empty($phoneNumber)) {
            $hlblock = Prototype::getInstance("ClientsBonusCards");
            $resultData = $hlblock->getElements(array(
                "select" => array("*"),
                "filter" => array("UF_PHONE_NUMBER" => $phoneNumber)
            ));
            if (!empty($resultData)) {
                $bonusBalance = $resultData[0]["UF_TOTAL_BALANCE"];
            }
            if (!empty($bonusBalance)) {
                return $bonusBalance;
            }
        }
    }
}