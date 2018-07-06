<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die;
}

use Bitrix\Sale;
use Bitrix\Sale\PriceMaths;
use Bitrix\Sale\Fuser as FUser;
use Bitrix\Sale\Basket;
use Bitrix\Main\Loader;
use Webgk\Main\Tools;


class WebgkOrderComponent extends CBitrixComponent
{
    /*** @var Bitrix\Sale\Basket\Storage */
    protected $basketStorage;
    protected $basketType = false;

    protected $basketResult = array();
    protected $basketItems = array();
    protected $basketData = array();

    protected $fUserId;     
    
    private $errorCollection;

    /**
     * Получение хранилище корзины текущего пользователя текущего сайта (C)
     *
     * @return object \Bitrix\Sale\Basket\Storage
     *
     * @throws \Bitrix\Main\ArgumentNullException
     */
    protected function getBasketStorage()
    {
        if (!isset($this->basketStorage)) {
            $this->basketStorage = Sale\Basket\Storage::getInstance($this->fUserId, $this->getSiteId());
        }

        return $this->basketStorage;
    }         
   

    /**
     * Инициализация параметров компонента
     *
     * @param $params array
     */
    protected function initializeComponentParams()
    {
        $this->fUserId = FUser::getId();
    }

    /**
     * Обработка входных параметров компонента
     *
     * @param $arParams array
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $this->initializeComponentParams($arParams);

        return $arParams;
    }

    /**
     * @return mixed|void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        $this->initializeModules();

        $this->basketItems = $this->getBasketItems();
        $this->basketResult = $this->getBasketResult($this->basketItems);
        $this->basketData = $this->getBasketData();

        $this->arResult["BASKET_ITEMS"] = $this->basketItems;
        $this->arResult["RESULT"] = $this->basketResult;
        $this->arResult["DATA"] = $this->basketData;

        $this->includeComponentTemplate();
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    protected function initializeModules()
    {
        Loader::includeModule('sale');
        Loader::includeModule('catalog');
    }

    /**
     * @param Basket $basket
     * @throws \Bitrix\Main\ObjectNotFoundException
     */
    protected function initializeBasketOrderIfNotExists(Sale\Basket $basket)
    {
        if (!$basket->getOrder()) {
            $userId = $this->getUserId() ?: \CSaleUser::GetAnonymousUserID();
            $order = Sale\Order::create($this->getSiteId(), $userId);

            $result = $order->appendBasket($basket);
            if (!$result->isSuccess()) {
                $this->errorCollection->add($result->getErrors());
            }
        }
    }

    /**
     * @return null
     */
    protected function getUserId()
    {
        global $USER;
        return $USER instanceof \CUser ? $USER->GetID() : null;
    }

    /**
     * @return array|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getBasketItems()
    {
        $basketItems = array();

        /** @var \Bitrix\Sale\Basket\Storage $basketStorage */
        $basketStorage = $this->getBasketStorage();
        /** @var \Bitrix\Sale\Basket $basket */
        $basket = $basketStorage->getBasket();

        if (!$basket->isEmpty()) {
            $orderableBasket = $basketStorage->getOrderableBasket();
            $this->initializeBasketOrderIfNotExists($orderableBasket);

            /** @var \Bitrix\Sale\BasketItem $item */
            foreach ($basket as $item) {
                if (($item->canBuy()) && (!$item->isDelay())) {
                    $item = $orderableBasket->getItemByBasketCode($item->getBasketCode());
                }

                $basketItems[$item->getId()] = $this->processBasketItem($item);
            }    
        }

        return $basketItems;
    }    
    

    /**
     * @param \Bitrix\Sale\BasketItem $item
     * @param $itemProperties
     * @return array
     * @throws \Bitrix\Main\ArgumentNullException
     */
    protected function processBasketItem($item)
    {
        $basketItem = array();
        $basketItemFields = $item->getFieldValues();     
        
        //$iblockElementData =           

        // Общие параметры
        $basketItem["ID"] = $basketItemFields["ID"];
        $basketItem["NAME"] = $basketItemFields["NAME"];
        $basketItem["PRODUCT_ID"] = $basketItemFields["PRODUCT_ID"];
        $basketItem["DETAIL_PAGE_URL"] = $basketItemFields["DETAIL_PAGE_URL"];
        $basketItem["CURRENCY"] = $basketItemFields["CURRENCY"];        
        $basketItem["QUANTITY"] = intval($basketItemFields["QUANTITY"]);        
        
        // Единицы измерения
        $basketItem["MEASURE"]["NAME"] = $basketItemFields["MEASURE_NAME"];
        $basketItem["MEASURE"]["CODE"] = $basketItemFields["MEASURE_CODE"];

        // Цена за единицу
        $basketItem["PRICE"]["VALUE"] = PriceMaths::roundPrecision($basketItemFields["PRICE"], 2);
        
        // За все количество
        $basketItem["PRICE"]["TOTAL"] = PriceMaths::roundPrecision($basketItem["PRICE"]["VALUE"] * $basketItem["QUANTITY"], 2);
                

        return $basketItem;
    }

    /**
     * Формирование данных для блока общая информация
     *
     * @param $basketItems array массив с элементами корзины
     * @return array
     */
    protected function getBasketResult($basketItems)
    {
        $basketResult = array(
            "PRICE" => array(
                "VALUE" => 0
            ),
            "QUANTITY" => 0
        );

        foreach ($basketItems as $basketItem) {    
            $basketResult["PRICE"]["VALUE"] += $basketItem["PRICE"]["VALUE"];
        }      

        return $basketResult;
    }

    /**
     * Заполнение параметров корзины
     *
     * @throws \Bitrix\Main\ArgumentNullException
     */
    protected function getBasketData()
    {
        $basketData = array();
        /** @var \Bitrix\Sale\Basket\Storage $storage */
        $storage = $this->getBasketStorage();

        $basketData["BASKET_ITEMS_COUNT"] = $storage->getBasket()->count();

        return $basketData;
    }
}