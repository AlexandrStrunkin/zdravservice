<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
    use \webgk\main\Tools;
    use \webgk\main\iblock\Prototype;
    use \webgk\main\Catalog;

    if (!empty($_POST["items"])) {
        $data = json_decode($_POST["items"], true);    


        $result = array();

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
                }
                if (empty($item["store_id"])) {
                    $errorText[] = "Отсутствует ID склада";    
                } 
                if (intval($item["quantity"]) <= 0) {
                    $errorText[] = "Отсутствует количество";
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
                    } else {
                        $storeTmp[$item["store_id"]] = $storeId;
                    }    
                }   

                if ($itemId && $storeId ) {
                    $updateRes = Catalog::updateItemStoreQuantity($itemId, $storeId, $item["quantity"]);
                    if ($updateRes) {
                        $status = "OK";
                        $data[$key]["total_quantity"] = $updateRes;  
                    } else {
                        $error = true;
                        $status = "error";
                        $errorText[] = "Ошибка обновления остатков товара";
                    } 
                }             

            } 

            if ($error){
                $status = "error";  
                $data[$key]["error_text"] = implode("; ", $errorText);  
            }     

            $data[$key]["status"] = $status; 
        }

    }      

    echo json_encode($data);
