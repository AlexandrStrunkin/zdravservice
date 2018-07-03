<?
    namespace Webgk\Main;
    use Webgk\Main\Tools;
    use Webgk\Main\Import;
    use Webgk\Main\CSV\CSVToArray;

    /**
    * класс для импорта цен
    */
    class price {

        const  PRICE_DIR = "/upload/1c_import/price/"; //папка с файлами импорта цен
        const  FILE_PREFIX = "TypePrice"; //префикс файлов с ценами
        const  FILE_FORMAT = "csv"; //формат файлов с ценами


        /**
        * функция для импорта цен из файла
        * 
        * @param mixed $file
        */
        public static function parcePriceData($file) {

            $itemsXmlId = array();               
            $result = array(); 
            $result["add"] = 0; //цен добавлено      
            $result["update"] = 0; //цен обновлено       
            $result["no_change"] = 0; //цен без изменений

            if (empty($file) || !\CModule::IncludeModule("catalog") || !\CModule::IncludeModule("iblock")) {
                $result["error"] .= "Файл не найден; \n";
                $result["count_errors"]++;
                return $result;
            }   

            $fullFilePath = self::PRICE_DIR . $file; //полный путь до файла

            //проверяем, не обрабатывается ли файл в данный момент
            if (Import::checkFileProcessing($fullFilePath)) {
                $result["error"] .= "Файл уже обрабатывается; \n";
                $result["count_errors"]++;
                return $result;
            } else {
                Import::addFileProcessing($fullFilePath); 
            }              


            //заменяем в названии файла префикс и формат, чтобы получить ID типа цены
            $replace = array(self::FILE_PREFIX . "-", "." . self::FILE_FORMAT);
            $priceId = str_replace($replace, "", $file); 

            //проверяем, есть ли цена с полученным ID 
            $arPrice = \CCatalogGroup::GetList(array(), array("XML_ID" => $priceId))->Fetch();
            if (!$arPrice["ID"]) {
                $result["error"] = "Цена с XML_ID " . $priceId . " не найдена!"; 
                $result["count_errors"]++;
                return $result;
            } else {
                $result["price_id"] = $priceId;    
            } 

            //получаем данные из файла
            $fileData = CSVToArray::CSVParse($fullFilePath, array("ITEM_ID", "PRICE", "OLD_PRICE"));
            
            if (empty($fileData)) {
                $result["error"] .= "Пустой файл; \n";
                $result["count_errors"]++;
                return $result;    
            }

            //формируем массив с xml_id товаров для выборки     
            foreach ($fileData as $item) {
                $itemsXmlId[] = $item["ITEM_ID"];
            }   

            $itemsXmlId = array_unique($itemsXmlId);    

            //собираем элементы, которые присутствуют в файле импорта
            $itemsList = array();
            $rsItems = \CIBlockElement::getList(array(), array("XML_ID" => $itemsXmlId), false, false, array("ID", "XML_ID", "PROPERTY_STARAYA_TSENA"));
            while($arItem = $rsItems->Fetch()) {
                $itemsList[$arItem["XML_ID"]] = $arItem;    
            }   

            //перебираем собранную информацию по ценам из файла
            foreach ($fileData as $item) {                   

                $arItem = $itemsList[$item["ITEM_ID"]];

                //получаем цены по данному товару
                $res = \CPrice::GetList(array(), array("PRODUCT_ID" => $arItem["ID"], "CATALOG_GROUP_ID" => $arPrice["ID"]))->Fetch();

                $arFields = Array(
                    "PRODUCT_ID" => $arItem["ID"],
                    "CATALOG_GROUP_ID" => $arPrice["ID"],
                    "PRICE" => $item["PRICE"],
                    "CURRENCY" => 'RUB'
                );

                if (!$res["ID"]) {    
                    if (\CPrice::Add($arFields)) {
                        $result["add"] ++;
                    }

                } else {
                    if ($res["PRICE"] != $arFields["PRICE"]) {
                        if(\CPrice::Update($res["ID"], $arFields)) {
                            $result["update"] ++;
                        }
                    } else {
                        $result["no_change"] ++; 
                    }
                }             

                //обработка свойства "старая цена"
                $oldPrices = array();
                if ($arItem["PROPERTY_STARAYA_TSENA_VALUE"]) {
                    $oldPrices = json_decode($arItem["PROPERTY_STARAYA_TSENA_VALUE"], true);
                }
                if ($item["OLD_PRICE"]) {
                    $oldPrices[$arPrice["XML_ID"]] = $item["OLD_PRICE"];    
                } else {
                    if (!empty($oldPrices[$arPrice["XML_ID"]])) {
                        unset ($oldPrices[$arPrice["XML_ID"]]);
                    }
                }                     

                if (empty($oldPrices)) {
                    $oldPrices = "";
                } else {
                    $oldPrices = json_encode($oldPrices);    
                }          

                if (!empty($arItem["PROPERTY_STARAYA_TSENA_VALUE"]) || !empty($item["OLD_PRICE"])) {
                    \CIBlockElement::SetPropertyValuesEx($arItem["ID"], false, array("STARAYA_TSENA" => $oldPrices));
                    $result["old_prices_upd"]++;  
                }        
            }   

            //удаляем файл из таблицы
            Import::deleteFileProcessing($fullFilePath);

            return $result;

        }


        /**
        * агент для обновления цен
        * 
        */
        function priceUpdateAgent() {

            $priceFiles = Tools::scanFileDir("/upload/1c_import/price/");
            if (empty($priceFiles)) {
                return "\\Webgk\\Main\\Price::priceUpdateAgent();";
            }  

            $logger = new Logger("Logger");
            $logger->StartLog(__FUNCTION__);

            //перебираем файлы в директории с файлами выгрузки цен и берем в обработку тот, который еще не обрабатывается
            foreach ($priceFiles as $file) {
                if (!Import::checkFileProcessing($file["PATH"])) {
                    $result = self::parcePriceData($file["NAME"]);
                    $result["file"] = $file["PATH"];

                    $logger->count = $result["update"] + $result["add"] + $result["no_change"];

                    //если нет ошибок, удаляем файл после обработки
                    if (!$result["error"]) {
                        unlink($file["FULL_PATH"]);                           
                    } else {
                        $logger->status = "fail";
                        $logger->count_errors = $result["count_errors"];    
                    }
                    break;
                }   
            }

            $logger->comment .= print_r($result, true);

            $logger->EndLog();

            return "\\Webgk\\Main\\Price::priceUpdateAgent();";            

        }




}