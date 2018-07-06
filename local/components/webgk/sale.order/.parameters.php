<?

$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
        "DEFAULT_RATIO_LENGTH" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_DEFAULT_RATIO_LENGTH"),
            "TYPE" => "INTEGER",
            "DEFAULT" => 6
        ],
        "DEFAULT_RATIO_MIN_QUANTITY" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_DEFAULT_RATIO_MIN_QUANTITY"),
            "TYPE" => "INTEGER",
            "DEFAULT" => 1
        ],
        "AJAX_UPDATE_PRODUCT_URL" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_AJAX_UPDATE_PRODUCT_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => "/ajax/index.php?controller=basket&action=updateProduct"
        ],
        "AJAX_CLEAR_BASKET_URL" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_AJAX_CLEAR_BASKET_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => "/ajax/index.php?controller=basket&action=clearBasket"
        ],
        "CATALOG_PAGE_URL" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_CATALOG_PAGE_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ],
        "ORDER_PAGE_URL" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_ORDER_PAGE_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ],
        "AJAX_ADD_COMPARE_URL" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_AJAX_ADD_COMPARE_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ],
        "AJAX_ADD_FAVORITE_URL" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_AJAX_ADD_FAVORITE_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ],
        "LOYALTY_PAGE_URL" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("COMP_PROP_LOYALTY_PAGE_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => ""
        ],
    )
);