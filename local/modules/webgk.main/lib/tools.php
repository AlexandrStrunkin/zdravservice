<?php

namespace Webgk\Main;

use \Webgk\Main\Iblock\Prototype as IblockPrototype;

Class Tools {

    public static function arshow($array, $adminCheck = false, $die = false){
        global $USER;
        $USER = new \Cuser;
        if ($adminCheck) {
            if (!$USER->IsAdmin()) {
                return false;
            }
        }
        echo "<pre>";
        print_r($array);
        echo "</pre>";
        
        if ($die) {
            die();
        }
    }

    public static function dumpshow($data, $adminCheck = false, $die = false)
    {
        global $USER;
        $USER = new \Cuser;
        if ($adminCheck) {
            if (!$USER->IsAdmin()) {
                return false;
            }
        }
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        
        if ($die) {
            die();
        }
    }

    /**
     * Пишет данные в лог
     *
     * @param string $message Данные для вывода
     * @param string $file Имя файла относительно DOCUMENT_ROOT (по-умолчанию log.txt)
     * @param boolean $backtrace Выводить ли информацию о том, откуда был вызван лог
     * @return void
     */
    public static function log($message, $file = '', $backtrace = false) {
        if(!$file) {
            $file = 'log.txt';
        }
        $file = $_SERVER['DOCUMENT_ROOT']."/".$file;
        $text = date('Y-m-d H:i:s').' ';

        if(is_array($message)) {
            $text .= print_r($message, true);
        } else {
            $text .= $message;
        }

        $text .= "\n";
        if($backtrace) {
            $backtrace = reset(debug_backtrace());
            $text = "Called in file: ".$backtrace["file"]." in line: ".$backtrace["line"]." \n".$text;
        }
        if($fh = fopen($file, 'a')) {
            fwrite($fh, $text);
            fclose($fh);
        }
    }


    /**
     * Обрезает текст, превыщающий заданную длину
     *
     * @param string $text Текст
     * @param array $config Конфигурация
     * @return string
     */
    public static function getEllipsis($text, $config = [])
    {
        $config = array_merge([
            'mode' => 'word',
            'count' => 255,
            'suffix' => '&hellip;',
            'stripTags' => true,
        ], $config);

        if ($config['stripTags']) {
            $text = preg_replace([
                '/(\r?\n)+/',
                '/^(\r?\n)+/',
            ], [
                "\n",
                '',
            ], strip_tags($text));
        }

        if (strlen($text) > $config['count']) {
            $text = substr($text, 0, $config['count']);
            switch ($config['mode']) {
                case 'direct':
                    break;
                case 'word':
                    $word = '[^ \t\n\.,:]+';
                    $text = preg_replace('/(' . $word . ')$/D', '', $text);
                    break;
                case 'sentence':
                    $sentence = '[\.\!\?]+[^\.\!\?]+';
                    $text = preg_replace('/(' . $sentence . ')$/D', '', $text);
                    break;
            }

            $text = preg_replace('/[ \.,;]+$/D', '', $text) . $config['suffix'];
        }

        if ($config['stripTags']) {
            $text = nl2br($text);
        }
        return $text;
    }

    /**
     * Возвращает массив значений указанного ключа исходного массива
     * Например, нужно, чтобы получать из мссива array(array("ID" => 1), array("ID" => 2), array("ID" => 3))
     * массив array(1, 2, 3)
     *
     *
     * @param array $arr
     * @param string $key
     * @param bool $notNull
     * @return array
     */

    public static function getAssocArrItemsKey($arr, $key = "ID", $notNull = false)
    {
        $resArr = array();
        foreach ($arr as $item) {
            if ($notNull && !$item[$key]) {
                continue;
            }
            $resArr[] = $item[$key];
        }
        return $resArr;
    }

    /**
     * Индексирует массив по заданному ключу
     * @param $arr
     * @param string $key
     *
     * @return array
     */
    public static function getIndexedArray($arr, $key = "ID")
    {

        $arRes = array();
        foreach ($arr as $index => $arrItem) {
            $arrItem['INDEX'] = $index;
            $arRes[$arrItem[$key]] = $arrItem;
        }

        return $arRes;
    }

    /**
     * Формирует строку для вывода размера файла
     *
     * @param integer $bytes Размер в байтах
     * @param integer $precision Кол-во знаков после запятой
     * @param array $types Приставки СИ
     * @return string
     */
    public static function getFileSize($bytes, $precision = 0, array $types = array('B', 'kB', 'MB', 'GB', 'TB'))
    {
        for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $bytes /= 1024, $i++) ;

        return round($bytes, $precision) . ' ' . $types[$i];
    }
    
    public static function updateUserPhone(&$arFields) {
        $userPhone = "";
        if (isset($arFields["PERSONAL_PHONE"])) {
            $userPhone = $arFields["PERSONAL_PHONE"];
            if (strlen($userPhone) > 0) {
                $userPhone = preg_replace("/\D/", "", $userPhone);
                if (strlen($userPhone) == 11) {
                    if (substr($userPhone, 0, 1) == "8") {
                        $userPhone = substr_replace($userPhone, "7", 0, 1);
                    }
                    $userPhone = "+".$userPhone;    
                }
            }
            $arFields["PERSONAL_PHONE"] = $userPhone;
        }                                         
    }
    
    /**
    * функция для форматирования телефона
    * 
    * @param mixed $path
    */
    public static function formatUserPhone($phoneNumber) {
        $phoneNumber = preg_replace("/\D/", "", $phoneNumber);
        if (strlen($phoneNumber) == 11) {
            if (substr($phoneNumber, 0, 1) == "8") {
                $userPhone = substr_replace($phoneNumber, "7", 0, 1);
            }
            $phoneNumber = "+".$phoneNumber;    
        }
        return $phoneNumber;                                          
    }
    
    
    /**
    * получение информации по бонусам нового пользователя
    * 
    * @param array $arFields
    */
    public static function gettingNewClientInfo(&$arFields) {
        if ($arFields["ID"]) {
            $userID = $arFields["ID"];
        } else {
            $userID = $arFields["USER_ID"];
        }
        $userInfo = \CUser::GetByID($userID);
        while ($user = $userInfo -> Fetch()) {
            if ($user["PERSONAL_PHONE"]) {
                ClientBonusInfo::ClientsInfo($user["PERSONAL_PHONE"]);
            }
        }
    }
    
    /*
    *Форматирование свойства "Действующее вещество" от '*', '(' 
    */
    public static function explodeProperty($valueToExplode){
        if(!empty($valueToExplode)){
            $explodeThis = $valueToExplode;
            $explodeThis = str_replace("*", "", $explodeThis);
                if(strripos($explodeThis, "(")){
                    $explodeThis = explode('(', $explodeThis);
                    $explodeThis = trim($explodeThis[0], " ");
                }
            return $explodeThis; 
        }
    }
    
    /**
    * функция сканирует указанную директорию и возвращает массив названий файлов/папок из данной директории
    * 
    */
    public static function scanFileDir($path) {
        
        if (empty($path)) {
            return false;
        }
        
        //добавляем слеш вначале
        if (substr($path, 0, 1) != "/") {
            $path = "/" . $path;    
        }
        
        //добавляем слеш вконце
        if (substr($path, -1) != "/") {
            $path = $path . "/";    
        }
        
        //проверяем наличие пути до папки сайта в указанном пути $dir
        $root = $_SERVER["DOCUMENT_ROOT"];
        
        $sourcePath = $path;
        if (!substr_count($path, $root)) {
            $path = $root . $path;    
        }
        
        $dirData = scandir($path);
        
        $result = array();
        foreach ($dirData as $key => $dirItem) {
            if ($key >= 2) {
                $result[] = array(
                    "NAME" => $dirItem,
                    "PATH" => $sourcePath . $dirItem, 
                    "FULL_PATH" => $path . $dirItem 
                );    
            }
        }
        
        return $result;           
                
    }
    /**
    * добавление флага в сессию для подключения к веб-сервису по получению пола клиента и даты рождения
    * 
    * @param mixed $arFields
    */
    public static function updatingBonus(&$arFields) {
        if (isset($arFields["PERSONAL_PHONE"]) && !isset($_SESSION["UPDATE_FROM_QUESTIONNAIRE"])) {
            $_SESSION["SERVICE_DATA"]["UPDATE_BONUS"] = "Y";
        }    
    }
    /**
    * форматирование номера телефона при добавлении в свойство инфоблока "Анкета"
    * 
    * @param mixed $arFields
    */
    public function fixPhoneNumberForIBlock(&$arFields) {
        $iblockObj = IblockPrototype::getInstanceByCode('questionnaire');
        $questionnaireIblockId = $iblockObj->getId();
        if ($arFields["IBLOCK_ID"] == $questionnaireIblockId) {
            $propertyList = $iblockObj->getProperties(
                array(
                    "filter" => array(
                        "IBLOCK_ID" => $questionnaireIblockId,
                        "CODE" => "PHONE_NUMBER"
                    )
                )
            );
            $phoneNumberPropId = "";
            foreach ($propertyList as $propertyInfo) {
                $phoneNumberPropId = $propertyInfo['ID'];    
            }
            if ($phoneNumberPropId) {
                if ($arFields["PROPERTY_VALUES"][$phoneNumberPropId]["n0"]["VALUE"]) {
                    $arFields["PROPERTY_VALUES"][$phoneNumberPropId]["n0"]["VALUE"] = \Webgk\Main\Tools::formatUserPhone($arFields["PROPERTY_VALUES"][$phoneNumberPropId]["n0"]["VALUE"]);
                }
            }
        }    
    }
    /**
    * обновление полей профиля пользователя после применения формы заполнения анкеты
    * 
    * @param mixed $arFields
    */
    public function updatingUserFieldsFromQuestionnaire (&$arFields) {
        $iblockObj = IblockPrototype::getInstanceByCode('questionnaire');
        $questionnaireIblockId = $iblockObj->getId();
        if ($arFields["IBLOCK_ID"] == $questionnaireIblockId) {
            $propertyList = $iblockObj->getProperties(
                array(
                    "filter" => array(
                        "IBLOCK_ID" => $questionnaireIblockId
                    )
                )
            );
            $propsArr = array();
            foreach ($propertyList as $propertyInfo) {
                $propsArr[$propertyInfo["CODE"]] = $propertyInfo['ID'];    
            }
            $maleEnumId = \COption::GetOptionString("grain.customsettings", "male_gender_property_enum"); 
            $femaleEnumId = \COption::GetOptionString("grain.customsettings", "female_gender_property_enum");
            $userId = "";
            if ($arFields["PROPERTY_VALUES"][$propsArr["PHONE_NUMBER"]]) {
                $userList = \CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("PERSONAL_PHONE" => $arFields["PROPERTY_VALUES"][$propsArr["PHONE_NUMBER"]]));
                while ($arUsers = $userList -> Fetch()) {
                    $userId = $arUsers["ID"];
                }
                $updatingUserFilter["PERSONAL_PHONE"] = $arFields["PROPERTY_VALUES"][$propsArr["PHONE_NUMBER"]];
            }
            if ($arFields["NAME"]) {
                $explodedNameArr = explode(" ", $arFields["NAME"]);
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
            if ($arFields["PROPERTY_VALUES"][$propsArr["EMAIL"]]) {
                $updatingUserFilter["EMAIL"] = $arFields["PROPERTY_VALUES"][$propsArr["EMAIL"]];
            }
            if ($arFields["PROPERTY_VALUES"][$propsArr["GENDER"]]) {
                if ($arFields["PROPERTY_VALUES"][$propsArr["GENDER"]] == $maleEnumId) {
                    $updatingUserFilter["PERSONAL_GENDER"] = "M";
                } else if ($arFields["PROPERTY_VALUES"][$propsArr["GENDER"]] == $femaleEnumId) {
                    $updatingUserFilter["PERSONAL_GENDER"] = "F";
                }
            }
            if ($arFields["PROPERTY_VALUES"][$propsArr["BIRTHDAY"]]["VALUE"]) {
                $updatingUserFilter["PERSONAL_BIRTHDAY"] = date("d.m.Y", strtotime($arFields["PROPERTY_VALUES"][$propsArr["BIRTHDAY"]]["VALUE"]));
            }
            if (!empty($updatingUserFilter) && !empty($userId)) {
                unset($_SESSION["SERVICE_DATA"]["UPDATE_BONUS"]);
                $_SESSION["UPDATE_FROM_QUESTIONNAIRE"] = "Y";
                $userObj = new \CUser;
                $updUser = $userObj->Update((int)$userId, $updatingUserFilter);
                unset($_SESSION["SERVICE_DATA"]["UPDATE_BONUS"]);
            }
        }
    }
    
    public function addClientBonusInfoFromQuestionnaire(&$arFields) {
        $iblockObj = IblockPrototype::getInstanceByCode('questionnaire');
        $questionnaireIblockId = $iblockObj->getId();
        if ($arFields["IBLOCK_ID"] == $questionnaireIblockId) {
            $elementsList = $iblockObj->getElements(
                array(
                    "filter" => array(
                        "IBLOCK_ID" => $questionnaireIblockId,
                        "ID" => $arFields["ID"]
                    ),
                    "select" => array(
                        "ID",
                        "PROPERTY_PHONE_NUMBER"
                    )
                )
            );
            $propsArr = array();
            $phoneNumber = "";
            foreach ($elementsList as $elementInfo) {
                $phoneNumber = $elementInfo["PROPERTY_PHONE_NUMBER_VALUE"];    
            }
            if (strlen($phoneNumber)) {
                \Webgk\Main\ClientBonusInfo::ClientsInfo($phoneNumber);
            }
        }        
    }  

}