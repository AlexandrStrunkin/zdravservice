<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    use \webgk\main\Tools;
    use \webgk\main\iblock\Prototype;
    use \webgk\main\Catalog;
    use \webgk\main\Logger;

    if (!empty($_REQUEST["items"])) {

        $logger = new Logger("Logger");
        $logger->StartLog("/ws/rest/");

        $data = json_decode($_REQUEST["items"], true); 
        
        $resultData["request"] = $data;           

        $result = array();
        $result["count"] = 0;
        $result["count_errors"] = 0;

        $itemTmp = array(); //временный массив элементов
        $storeTmp = array(); //временный массив складов

        foreach ($data as $key => $item) {
            $error = false;
            $errorText = array();
            $item["quantity"] = intval($item["quantity"]);

            if (empty($item["id"]) || empty($item["store_id"]) || $item["quantity"] === null) {
                $error = true;
                if (empty($item["id"])) {
                    $errorText[] = "Отсутствует ID товара";
                    $result["count_errors"]++;    
                }
                if (empty($item["store_id"])) {
                    $errorText[] = "Отсутствует ID склада";
                    $result["count_errors"]++;    
                } 
                if (intval($item["quantity"]) <= 0) {
                    $errorText[] = "Отсутствует количество";
                    $result["count_errors"]++;
                }
            }
            if (!$error) {             

                if ($itemTmp[$item["id"]]) {
                    $itemId = $itemTmp[$item["id"]];    
                } else {
                    $itemId = Prototype::getItemIdByXmlId($item["id"]);
                    if (!$itemId) {
                        $error = true;
                        $errorText[] = "товар не найден";
                        $result["count_errors"]++;    
                    } else {
                        $itemTmp[$item["id"]] = $itemId;
                    }    
                }

                if ($storeTmp[$item["store_id"]]) {
                    $storeId = $storeTmp[$item["store_id"]];    
                } else {
                    $storeId = Catalog::getStoreIdByXmlId($item["store_id"]);
                    if (!$storeId) {
                        $error = true;
                        $errorText[] = "склад не найден";
                        $result["count_errors"]++;    
                    } else {
                        $storeTmp[$item["store_id"]] = $storeId;
                    }    
                }   

                if ($itemId && $storeId) {
                    $updateRes = Catalog::updateItemStoreQuantity($itemId, $storeId, $item["quantity"]);
                    if ($updateRes) {
                        $status = "OK";
                        $data[$key]["total_quantity"] = $updateRes;  
                    } else {
                        $error = true;
                        $status = "error";
                        $errorText[] = "Ошибка обновления остатков товара";
                        $result["count_errors"]++;
                    } 
                }      
            } 

            if ($error){
                $status = "error";  
                $data[$key]["error_text"] = implode("; ", $errorText);
                $resultData["error_items"][]  = $data[$key]; 
            }     

            $data[$key]["status"] = $status;
            $result["count"]++; 
        }

        $logger->count = $result["count"];
        $logger->count_errors = $result["count_errors"];

        $logger->comment .= print_r($resultData, true);

        $logger->EndLog();

    } else {
        $data = array("error" => "Неверный формат данных");   
    }        

    echo trim(json_encode($data));
