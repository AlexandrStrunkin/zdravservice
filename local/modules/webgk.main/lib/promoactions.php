<?php

    namespace Webgk\Main;
    use Webgk\Main\Import;
    use Webgk\Main\Tools;
    use Webgk\Main\CSV\CSVToArray;
    use Webgk\Main\Iblock\Prototype;

    /**
    * класс для работы с импортом "маркетинговых активностей" (промо акций)
    */
    Class promoActions {


        const PROMO_DIR = "/upload/1c_import/statusactions/"; //папка с файлами импорта акций
        const FILE_NAME = "StatusActions.csv"; //имя файлов с акциями
        const PROMO_IBLOCK_CODE = "promo_actions";


        /**
        * функция для импорта акций из файла
        * 
        * @param mixed $file
        */
        public static function parcePromoData($file) {

            global $DB;

            if (empty($file) || !\CModule::IncludeModule("iblock")) {
                return false;
            }   

            $fullFilePath = self::PROMO_DIR . $file; //полный путь до файла                 

            //проверяем, не обрабатывается ли файл в данный момент
            if (Import::checkFileProcessing($fullFilePath)) {
                return false;
            } else {
                Import::addFileProcessing($fullFilePath); 
            }   

            $result = array();
            $result["delete"] = 0;
            $result["update"] = 0;
            $result["add"] = 0;

            //получаем данные из файла
            $fileDataTmp = CSVToArray::CSVParse($fullFilePath, array("XML_ID", "REGION", "NAME", "DESCRIPTION", "ACTIVE_FROM", "ACTIVE_TO"));
            $importData = array();

            //формируем массив в удобном вимде для дальнейшей работы
            foreach ($fileDataTmp as $item) {
                $importData[$item["XML_ID"]] = $item;    
            }

            //собираем текущие акции
            $promoList = array();
            $rsPromo = \CIBlockElement::getList(array(), array("IBLOCK_CODE" => self::PROMO_IBLOCK_CODE), false, false, array("ID", "XML_ID", "IBLOCK_ID"));
            while($arPromoItem = $rsPromo->Fetch()) {
                $promoList[$arPromoItem["XML_ID"]] = $arPromoItem["ID"];
            }              

            //перебираем акции с сайта и удаляем те, которых нет в выгрузке
            foreach ($promoList as $xmlId => $id) {
                if (!$importData[$xmlId]) {
                    \CIBlockElement::Delete($id);
                    $result["delete"]++;
                }
            }    

            //получаем ID инфоблока акций
            $promoIblockid = Prototype::getIdByCode(self::PROMO_IBLOCK_CODE);            

            //перебираем акции из файла, импортируем новые и обновляем старые
            foreach ($importData as $promoItem) {
                //если акция существует, обновляем дату окончания активности
                if ($promoList[$promoItem["XML_ID"]]) {

                    $promoId = $promoList[$promoItem["XML_ID"]];                         

                    $el = new \CIBlockElement;                           
                    // переведем дату из одного формата в другой
                    $dateActiveTo = $DB->FormatDate($promoItem["ACTIVE_TO"], "YYYY-MM-DD", "DD.MM.YYYY");
                    if ($dateActiveTo) {
                        if ($el->Update($promoId, array("DATE_ACTIVE_TO" => $dateActiveTo))) {
                            $result["update"]++;    
                        } else {
                            $result["error"] .= $el->LAST_ERROR . "(" . $promoId . ")" . ";";    
                        }
                    }

                } else {//если акции нет, добавляем
                    $el = new \CIBlockElement;                           
                    // переведем дату из одного формата в другой
                    $dateActiveFrom = $DB->FormatDate($promoItem["ACTIVE_FROM"], "YYYY-MM-DD", "DD.MM.YYYY");
                    $dateActiveTo = $DB->FormatDate($promoItem["ACTIVE_TO"], "YYYY-MM-DD", "DD.MM.YYYY");
                    
                    //массив данных для создания акции
                    $arPromo = array(
                        "IBLOCK_ID" => $promoIblockid,
                        "XML_ID" => $promoItem["XML_ID"],
                        "PROPERTY_VALUES"=> array("REGION" => $promoItem["REGION"]), 
                        "NAME" => $promoItem["NAME"],
                        "PREVIEW_TEXT" => $promoItem["DESCRIPTION"],
                        "DATE_ACTIVE_FROM" => $dateActiveFrom,
                        "DATE_ACTIVE_TO" => $dateActiveTo                     
                    );
                    if ($el->Add($arPromo)) {
                        $result["add"]++;    
                    } else {
                        $result["error"] .= $el->LAST_ERROR . "(" . $promoItem["NAME"] . ")" . ";";
                    } 
                }     
            }         

            //TODO log

            //удаляем файл из таблицы
            Import::deleteFileProcessing($fullFilePath);

            return $result;

        }


        /**
        * агент для обновления акций
        * 
        */
        function promoUpdateAgent() {    

            $filePath = self::PROMO_DIR . self::FILE_NAME;
            $fileFullPath = $_SERVER["DOCUMENT_ROOT"] . $filePath;
            if (!file_exists($fileFullPath)) {
                return false;
            }  

            //преебираем файлы в директории с файлами выгрузки акций и берем в обработку тот, который еще не обрабатывается

            if (!Import::checkFileProcessing($filePath)) {
                $result = self::parcePromoData(self::FILE_NAME);
                $result["file"] = $filePath;

                //если нет ошибок, удаляем файл после обработки
                if (!$result["error"]) {
                    unlink($fileFullPath);
                }                   

            }    

            //TODO log

            return "\\Webgk\\Main\\Promoactions::promoUpdateAgent();";            

        }   

}