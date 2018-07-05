<?php
    namespace Webgk\Main;
    use Webgk\Main\Import;
    use Webgk\Main\Tools;
    use Webgk\Main\CSV\CSVToArray;
    use Webgk\Main\Hlblock\Prototype;
    use Webgk\Main\Logger;

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

            $result = array();
            $result["update"] = 0;
            $result["add"] = 0;
            $result["count_errors"] = 0;
            
            if (empty($file) || !\CModule::IncludeModule("iblock")) {
                $result["error"] .= "Файл не найден; \n";
                $result["count_errors"]++;
                return $result;
            }   

            $fullFilePath = self::NORM_DIR . $file; //полный путь до файла                 

            //проверяем, не обрабатывается ли файл в данный момент
            if (Import::checkFileProcessing($fullFilePath)) {
                $result["error"] .= "Файл уже обрабатывается; \n";
                $result["count_errors"]++;
                return $result;
            } else {
                Import::addFileProcessing($fullFilePath); 
            }      

            //получаем данные из файла
            $fileDataTmp = CSVToArray::CSVParse($fullFilePath, array("item_name", "item_quantity"));
            
            if (empty($fileDataTmp)) {
                $result["error"] .= "Пустой файл; \n";
                $result["count_errors"]++;
                return $result;    
            }

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
                return "\\Webgk\\Main\\normItems::normUpdateAgent();";
            }  
            
            $logger = new Logger("Logger");
            $logger->StartLog(__FUNCTION__);

            //преебираем файл в директории с файлами выгрузки 

            if (!Import::checkFileProcessing($filePath)) {
                $result = self::parceNormData(self::FILE_NAME);
                $result["file"] = $filePath;
                
                $logger->count = $result["update"] + $result["add"];
                $logger->count_errors = $result["count_errors"]; 

                //если нет ошибок, удаляем файл после обработки
                if (!$result["error"]) {
                    unlink($fileFullPath);
                } else {
                    $logger->status = "fail";                       
                }                   

            }    

            $logger->comment .= print_r($result, true);

            $logger->EndLog();

            return "\\Webgk\\Main\\normItems::normUpdateAgent();";             

        }

}