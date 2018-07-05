<?php 

    namespace Webgk\Main;
    use Webgk\Main\Import;
    use Webgk\Main\Tools;
    use Webgk\Main\CSV\CSVToArray;
    use Webgk\Main\Hlblock\Prototype;
    use Webgk\Main\Logger;

    /**
    * класс для работы с файлом импорта дат доставки
    */
    class deliveryDate {


        const DELIVERY_DIR = "/upload/1c_import/delivery/"; //папка с файлами импорта акций
        const FILE_NAME = "Delivery.csv"; //имя файлов с акциями
        const DELIVERY_HLBLOCK_CODE = "GoodsDeliveryDates";

        /**
        * функция для импорта дат доставки из файла
        * 
        * @param mixed $file
        */
        public static function parceDeliveryData($file) {

            $result = array();
            $result["update"] = 0;
            $result["add"] = 0;
            $result["count_errors"] = 0;

            if (empty($file) || !\CModule::IncludeModule("iblock")) {
                $result["error"] .= "Файл не найден; \n";
                $result["count_errors"]++;
                return $result;
            }   

            $fullFilePath = self::DELIVERY_DIR . $file; //полный путь до файла                 

            //проверяем, не обрабатывается ли файл в данный момент
            if (Import::checkFileProcessing($fullFilePath)) {
                $result["error"] .= "Файл уже обрабатывается; \n";
                $result["count_errors"]++;
                return $result;
            } else {
                Import::addFileProcessing($fullFilePath); 
            }                 

            //получаем данные из файла
            $fileDataTmp = CSVToArray::CSVParse($fullFilePath, array("item_name", "delivery_date"));        

            if (empty($fileDataTmp)) {
                $result["error"] .= "Пустой файл; \n";
                $result["count_errors"]++;
                return $result;    
            }

            //обрабатываем данные
            foreach ($fileDataTmp as $importedItemInfo) {
                if (!empty($importedItemInfo["item_name"])) {
                    $hlblock = Prototype::getInstance(self::DELIVERY_HLBLOCK_CODE);
                    $resultData = $hlblock->getElements(array(
                        "select" => array("*"),
                        "filter" => array("UF_ITEM_ID" => $importedItemInfo["item_name"])
                    ));

                    $formattedDate = date("d.m.Y H:i:s", strtotime($importedItemInfo["delivery_date"]));
                    if (!empty($resultData)) {
                        foreach ($resultData as $curResult) { 

                            $res = $hlblock->updateData($curResult["ID"], array(
                                'UF_ITEM_ID' => $importedItemInfo["item_name"],
                                'UF_DELIVERY_DATE' => $formattedDate,
                            ));

                            if ($res->getId()) {
                                $result["update"]++;    
                            }
                        }
                    } else {

                        $res = $hlblock->addData(array(
                            'UF_ITEM_ID' => $importedItemInfo["item_name"],
                            'UF_DELIVERY_DATE' => $formattedDate,
                        ));

                        if ($res->getId()) {
                            $result["add"]++;    
                        }
                    }
                }  
            }                 

            //удаляем файл из таблицы
            Import::deleteFileProcessing($fullFilePath);

            return $result;        

        }                          



        /**
        * агент для обновления акций
        * 
        */
        function deliveryUpdateAgent() {    

            $filePath = self::DELIVERY_DIR . self::FILE_NAME;
            $fileFullPath = $_SERVER["DOCUMENT_ROOT"] . $filePath;
            if (!file_exists($fileFullPath)) {
                return "\\Webgk\\Main\\DeliveryDate::deliveryUpdateAgent();";
            } 

            $logger = new Logger("Logger");
            $logger->StartLog(__FUNCTION__); 

            //проверяем файл выгрузки

            if (!Import::checkFileProcessing($filePath)) {
                $result = self::parceDeliveryData(self::FILE_NAME);
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

            return "\\Webgk\\Main\\DeliveryDate::deliveryUpdateAgent();";            

        }  
}