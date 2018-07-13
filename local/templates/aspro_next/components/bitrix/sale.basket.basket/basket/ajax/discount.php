<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if($_REQUEST["action"] == 'getActualDiscount') {
    $resultHtml = false;
    $basket = Bitrix\Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    foreach($basket as $basketItem) {
        $discountName = "";
        $discountValue = "";

        $discountName = $basketItem->getField("DISCOUNT_NAME");

        if(!empty($discountName)) {
            $resultHtml .= "<li>".$discountName."</li>";
        }
    }

    echo json_encode($resultHtml);
}
?>

