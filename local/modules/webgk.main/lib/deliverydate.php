<?php 

    namespace Webgk\Main;
    use Webgk\Main\Import;
    use Webgk\Main\Tools;
    use Webgk\Main\CSV\CSVToArray;
    use Webgk\Main\Iblock\Prototype;

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

            if (empty($file) || !\CModule::IncludeModule("iblock")) {
                return false;
            }   

            $fullFilePath = self::DELIVERY_DIR . $file; //полный путь до файла                 

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
            $fileDataTmp = CSVToArray::CSVParse($fullFilePath, array("item_name", "delivery_date"));

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
            
             //TODO log

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
                return false;
            }  

            //преебираем файлы в директории с файлами выгрузки акций и берем в обработку тот, который еще не обрабатывается

            if (!Import::checkFileProcessing($filePath)) {
                $result = self::parceDeliveryData(self::FILE_NAME);
                $result["file"] = $filePath;

                //если нет ошибок, удаляем файл после обработки
                if (!$result["error"]) {
                    unlink($fileFullPath);
                }                   

            }    

            //TODO log

            return "\\Webgk\\Main\\DeliveryDate::deliveryUpdateAgent();";            

        }  
}