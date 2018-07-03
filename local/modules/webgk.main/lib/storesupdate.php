<?
namespace Webgk\Main;

use Webgk\Main\CSV\CSVToArray;

/**
 *
 */
class StoresUpdate
{

    public static function StoreUpd()
    {
        $result = [];

        if (!file_exists($_SERVER['DOCUMENT_ROOT'].'upload/1c_import/storecoords/storage_coords.csv')) {
            $result['error'] = 'Файл не найден';
        return $result;
        }

        $arStores = \Webgk\Main\CSV\CSVToArray::CSVParse("upload/1c_import/storecoords/storage_coords.csv",['XML_ID', 'GPS_N', 'GPS_S' , 'SCHEDULE', 'PHONE', 'UF_REGION_STORAGE'] ,';');

        $arStores = Tools::getIndexedArray($arStores, 'XML_ID');

        $arSelect = ['ID', 'ACTIVE', 'TITLE', 'XML_ID', 'UF_REGION_STORAGE'];

        $dbResult = CCatalogStore::GetList([],[],false,false,$arSelect);

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
                    $result['upd'][] =  $arStore['ID'];
                } else {
                    $result['error'][] = $arStore['ID'];
                }
            }
        }

        return $result;
    }
}
