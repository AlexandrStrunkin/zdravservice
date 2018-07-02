<?php
    namespace Webgk\Main;
    use Webgk\Main\Import;
    use Webgk\Main\Tools;
    use Webgk\Main\CSV\CSVToArray;
    use Webgk\Main\Iblock\Prototype;

    /**
    * класс по работе с нормированными товарами, генерация массива из CSV и работа с HL-блоком
    */
    class normItems {


        const NORM_DIR = "/upload/1c_import/norm/"; //папка с файлами импорта акций
        const FILE_NAME = "Norma.csv"; //имя файлов с акциями
        const NORM_HLBLOCK_CODE = "GoodsQuantity";

        /**
        * функция для импорта дат доставки из файла
        * 
        * @param mixed $file
        */
        public static function parceNormData($file) {

            if (empty($file) || !\CModule::IncludeModule("iblock")) {
                return false;
            }   

            $fullFilePath = self::NORM_DIR . $file; //полный путь до файла                 

            //проверяем, не обрабатывается ли файл в данный момент
            if (Import::checkFileProcessing($fullFilePath)) {
                return false;
            } else {
                Import::addFileProcessing($fullFilePath); 
            }   

            $result = array();
            $result["update"] = 0;
            $result["add"] = 0;

            //получаем данные из файла
            $fileDataTmp = CSVToArray::CSVParse($fullFilePath, array("item_name", "item_quantity"));

            foreach ($fileDataTmp as $importedItemInfo) {
                if (!empty($importedItemInfo["item_name"])) {
                    $hlblock = Prototype::getInstance(self::NORM_HLBLOCK_CODE);
                    $resultData = $hlblock->getElements(array(
                        "select" => array("*"),
                        "filter" => array("UF_ITEM_ID" => $importedItemInfo["item_name"])
                    ));
                    if (!empty($resultData)) {
                        foreach ($resultData as $curResult) {

                            $res = $hlblock->updateData($curResult["ID"], array(
                                'UF_ITEM_ID' => $importedItemInfo["item_name"],
                                'UF_ITEM_QUANTITY' => $importedItemInfo["item_quantity"],
                            ));
                            if ($res->getId()) {
                                $result["update"]++;    
                            }
                        }
                    } else {

                        $res = $hlblock->addData(array(
                            'UF_ITEM_ID' => $importedItemInfo["item_name"],
                            'UF_ITEM_QUANTITY' => $importedItemInfo["item_quantity"],
                        ));
                        if ($res->getId()) {
                            $result["add"]++;    
                        }
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
        function normUpdateAgent() {    

            $filePath = self::NORM_DIR . self::FILE_NAME;
            $fileFullPath = $_SERVER["DOCUMENT_ROOT"] . $filePath;
            if (!file_exists($fileFullPath)) {
                return false;
            }  

            //преебираем файлы в директории с файлами выгрузки акций и берем в обработку тот, который еще не обрабатывается

            if (!Import::checkFileProcessing($filePath)) {
                $result = self::parceNormData(self::FILE_NAME);
                $result["file"] = $filePath;

                //если нет ошибок, удаляем файл после обработки
                if (!$result["error"]) {
                    unlink($fileFullPath);
                }                   

            }    

            //TODO log

            return "\\Webgk\\Main\\normItems::normUpdateAgent();";            

        }

}