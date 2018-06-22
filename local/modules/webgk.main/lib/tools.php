<?php

namespace Webgk\Main;

Class Tools {

    public static function arshow($array, $adminCheck = false){
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
    }

    public static function dumpshow($data, $adminCheck = false)
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
    
    public static function updateUserPhoneOnRegForm($phoneNumber) {
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

}