<?php 

    namespace Webgk\Main;
    use Bitrix\Main\Loader;
    use Bitrix\Main as MainModule;
    use Bitrix\Iblock;    
    use Bitrix\Main\Entity;
    use Bitrix\Main\Option;
    use Webgk\Main\Hlblock\Prototype; 
    use Webgk\Main\Logger;

    class ClientBonusInfo {

        const BONUS_HLBLOCK_CODE = "ClientsBonusCards";

        /**
        * получение бонусного баланса клиента и добавление/обновление соответствующей записи HL-блока
        * 
        * @param string $phoneNumber
        */
        public function ClientsInfo($phoneNumber = ""){

            $result = array();
            $result["error"] = "";
            $result["phone"] = $phoneNumber;           

            $curl = curl_init();
            if (!empty($phoneNumber)) {
                $paramArr = json_encode(['phone' => $phoneNumber]);
            } else {
                $result["error"] .= "не указан телефон! \n";
                return $result;
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
            } else {
                $result["error"] .= "не удалось соединиться с вебсервисом! \n";
                return $result;
            }        

            $clientInfoArr = array();
            // Закрываем запрос и удаляем инициализацию $curl
            if ($clientInfo) {
                $cardNumber = "";
                $totalBalance = 0; 

                foreach ($clientInfo->phone->attributes() as $attrKey => $attrVal) {
                    if ($attrKey == "Number") {
                        $clientInfoArr["PHONE_NUMBER"] = "+" . (string)$attrVal;
                    }
                }    

                if (!$clientInfoArr["PHONE_NUMBER"]) {  
                    $result["error"] .= "Данные не получены (" . $phoneNumber . ")\n";
                    return $result;
                }  

                foreach ($clientInfo->phone->Gender as $gender) {
                    $genderSymbol = iconv($gender);
                }         

                foreach ($clientInfo->phone->BDate as $bdate) {
                    $birthday = $bdate;
                }

                $totalBalance = floatval($clientInfo->phone->Balance);
                $clientInfoArr["USER_BALANCE"] = $totalBalance;  
                $hlblock = Prototype::getInstance(self::BONUS_HLBLOCK_CODE);

                $resultData = $hlblock->getElements(array(
                    "select" => array("*"),
                    "filter" => array("UF_PHONE_NUMBER" => $clientInfoArr["PHONE_NUMBER"])
                ));

                if (!empty($resultData)) {
                    foreach ($resultData as $curResult) {

                        $rsResult = $hlblock->updateData($curResult["ID"], array(
                            'UF_PHONE_NUMBER' => $clientInfoArr["PHONE_NUMBER"],
                            'UF_TOTAL_BALANCE' => $clientInfoArr["USER_BALANCE"],
                            'UF_TIMESTAMP_X' => time()
                        ));
                    }
                } else {

                    $rsResult = $hlblock->addData(array(
                        'UF_PHONE_NUMBER' => $clientInfoArr["PHONE_NUMBER"],
                        'UF_TOTAL_BALANCE' => $clientInfoArr["USER_BALANCE"],
                        'UF_TIMESTAMP_X' => time()
                    ));
                }  

                $resultId = $rsResult->getId(); 

                $updUserFields = array();
                if (isset($_SESSION["SERVICE_DATA"]["UPDATE_BONUS"])) {

                    $userList = \CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("PERSONAL_PHONE" => $phoneNumber));
                    while ($arUsers = $userList -> Fetch()) {
                        $userId = $arUsers["ID"];
                    }

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

            } else {
                $result["error"] .= "Ошибка данных\n";
            }       

            if ($resultId) {
                $result["id"] = $resultId;
            } else {
                $result["error"] .= "Ошибка обновления бонусов \n";
            } 

            return $result;
        }         

        /**
        * обновление записей HL-блока бонусов, 
        * последнее добавление/обновление которых было произведено в течение последних суток
        * 
        */
        public function checkUpdatedClientsInfo() {

            $result = array();             
            $result["update"] = 0;
            $result["count_errors"] = 0;

            $hlblock = Prototype::getInstance(self::BONUS_HLBLOCK_CODE);
            $resultData = $hlblock->getElements(array(
                "select" => array("*"),
                "filter" => array("<UF_TIMESTAMP_X" => time() - 86400),
                "limit" => 100
            ));

            if (!empty($resultData)) {

                $logger = new Logger("Logger");
                $logger->StartLog(__FUNCTION__);

                $phonesArr = array();
                foreach ($resultData as $curResult) {
                    if (!empty($curResult["UF_PHONE_NUMBER"])) {
                        $phonesArr[] = $curResult["UF_PHONE_NUMBER"];
                    }
                }
                foreach ($phonesArr as $phoneNumber) {
                    $resultData = ClientBonusInfo::ClientsInfo($phoneNumber);
                    if (!$resultData["error"] && $resultData["id"]) {
                        $result["update"]++;    
                    } else {
                        $result["error"] .= $resultData["error"] . "\n"; 
                        $result["count_errors"]++; 
                    }
                }

                $logger->count = $result["update"];
                $logger->count_errors = $result["count_errors"];

                $logger->comment .= print_r($result, true);

                $logger->EndLog();

            }

            return $result;
        }

        /**
        * запуск агента, обновляющего записи HL-блока информации о бонусах
        * 
        */
        function gettingClientsInfoAgent() { 
            $result = ClientBonusInfo::checkUpdatedClientsInfo();  
            return "\\Webgk\\Main\\ClientBonusInfo::gettingClientsInfoAgent();"; 
        }

        /**
        * получение баланса пользователя по номеру телефона из БД
        * 
        * @param string $phoneNumber
        */
        function gettingUserBalanceFromDB($phoneNumber) {
            if (!empty($phoneNumber)) {
                $hlblock = Prototype::getInstance(self::BONUS_HLBLOCK_CODE);
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