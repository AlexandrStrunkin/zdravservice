<?
    namespace Webgk\Main;

    use Webgk\Main\Import;
    use Webgk\Main\Tools;
    use Webgk\Main\CSV\CSVToArray;
    use Webgk\Main\Logger;

    /**
    *
    */
    class StoresUpdate
    {

        const STORES_DIR = "/upload/1c_import/storecoords/"; //папка с файлами импорта акций
        const FILE_NAME = "storage_coords.csv"; //имя файлов с акциями

        public static function StoreUpd($file) {
            
            $result = [];
            $result["update"] = 0;
            $result["error"] = "";
            $result["count_errors"] = 0;

            if (empty($file) || !\CModule::IncludeModule("catalog")) {
                $result["error"] .= "Файл не найден; \n";
                $result["count_errors"]++;
                return $result;
            }

            $fullFilePath = self::STORES_DIR . $file; //полный путь до файла  

            //проверяем, не обрабатывается ли файл в данный момент
            if (Import::checkFileProcessing($fullFilePath)) { 
                $result["error"] .= "Файл уже обрабатывается; \n";
                $result["count_errors"]++;
                return $result;
            } else {
                Import::addFileProcessing($fullFilePath); 
            }

            $arStores = CSVToArray::CSVParse($fullFilePath, ['XML_ID', 'GPS_N', 'GPS_S' , 'SCHEDULE', 'PHONE', 'UF_REGION_STORAGE'] ,';');

            if (empty($arStores)) {
                $result["error"] .= "Пустой файл; \n";
                $result["count_errors"]++;
                return $result;    
            }    

            $arStores = Tools::getIndexedArray($arStores, 'XML_ID');

            $arSelect = ['ID', 'ACTIVE', 'TITLE', 'XML_ID', 'UF_REGION_STORAGE'];

            $dbResult = \CCatalogStore::GetList([], [], false, false, $arSelect);

            while ($arStore = $dbResult-> fetch()) {

                if ($arStores[$arStore['XML_ID']]) {

                    $arFields = [];
                    $arFields = [
                        "GPS_N" => $arStores[$arStore['XML_ID']]['GPS_N'],
                        "GPS_S" => $arStores[$arStore['XML_ID']]['GPS_S'],
                        "SCHEDULE" => $arStores[$arStore['XML_ID']]['SCHEDULE'],
                        "PHONE" => $arStores[$arStore['XML_ID']]['PHONE'],
                        'UF_REGION_STORAGE' => $arStores[$arStore['XML_ID']]['UF_REGION_STORAGE'],
                    ];

                    $res = \CCatalogStore::Update($arStore['ID'], $arFields);

                    if ($res) {
                        $result["update"]++;
                    } else {
                        $result["count_errors"]++;
                        $result["error"] .= "Ошибка обновления склада " . $arStore['XML_ID'] ."\n";
                    }
                }
            }
            
            //удаляем файл из таблицы
            Import::deleteFileProcessing($fullFilePath);

            return $result;
        }


        /**
        * агент для обновления информации по складам
        * 
        */
        function storeUpdateAgent() {                 

            $filePath = self::STORES_DIR . self::FILE_NAME;
            $fileFullPath = $_SERVER["DOCUMENT_ROOT"] . $filePath;
            if (!file_exists($fileFullPath)) {
                return "\\Webgk\\Main\\StoresUpdate::storeUpdateAgent();";
            }  

            $logger = new Logger("Logger");
            $logger->StartLog(__FUNCTION__);

            //преебираем файлы в директории с файлами выгрузки акций и берем в обработку тот, который еще не обрабатывается

            if (!Import::checkFileProcessing($filePath)) {
                $result = self::StoreUpd(self::FILE_NAME);
                $result["file"] = $filePath;

                $logger->count = $result["update"];
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

            return "\\Webgk\\Main\\StoresUpdate::storeUpdateAgent();";            

        } 

    }
