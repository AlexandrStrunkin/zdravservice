<?

    namespace Webgk\Main;
    use Webgk\Main\Tools;
    use Webgk\Main\Hlblock\Prototype; 


    /**
    * класс для работы с функциями импорта
    */
    Class import {

        /**
        * проверка файла. Если файл в данный момент обрабатывается каким-то процессом, возвращает Y, иначе N (ХЛ блок FileProcessing)
        * 
        * @param string $file
        */
        public static function checkFileProcessing($file) {

            $result = false;

            $hlblock = Prototype::getInstance("FileProcessing");
            $resultData = $hlblock->getElements(array(
                "select" => array("*"),
                "filter" => array("UF_FILE_PATH" => $file),
                "cacheTime" => 0
            ));                 

            if (!empty($resultData[0])) {
                $result = $resultData[0]["ID"];
            } 

            return $result;   
        }


        /**
        * добавление файла в ХЛ блок с текущими обратываемыми файлами
        * 
        * @param string $file
        */
        public static function addFileProcessing($file) {

            $result = false;
            
            $checkFile = self::checkFileProcessing($file);

            if (!$checkFile) {
                $hlblock = Prototype::getInstance("FileProcessing");
                $result = $hlblock->addData(array(
                    'UF_FILE_PATH' => $file,
                ));
                $resultId = $result->getId();
                if ($resultId) {
                    $result = $resultId;
                }                       
            }   
            
            return $result;

        }



        /**
        * удаление файла из ХЛ блока с текущими обратываемыми файлами
        * 
        * @param string $file
        */
        public static function deleteFileProcessing($file) {
            
            $checkFile = self::checkFileProcessing($file);
            if ($checkFile) {
                $hlblock = Prototype::getInstance("FileProcessing");
                $hlblock->deleteData($checkFile);                                          
            }   
        }


}