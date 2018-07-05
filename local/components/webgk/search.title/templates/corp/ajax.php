<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if (empty($arResult["CATEGORIES"])) return;?>
<div class="bx_searche scrollbar">
    <? global $arRegion;
    foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
        <?foreach($arCategory["ITEMS"] as $i => $arItem):
            //$totalCount = Webgk\Main\AsproExtend\ExtendClass::GetTotalCount($arItem, $arParams);
            //$regionalStoreQuantity = Webgk\Main\AsproExtend\ExtendClass::GetTotalCount($arItem, $arParams, true);
            //$arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount +$regionalStoreQuantity, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], ($bLinkedItems ? true : false), $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
            //echo "<pre>"; print_r($arResult["ELEMENTS"][]); echo "</pre>";?>
            <?//=$arCategory["TITLE"]?>
            <?if($category_id === "all"):?>
                <div class="bx_item_block all_result">
                    <div class="maxwidth-theme">
                        <div class="bx_item_element">
                            <a class="all_result_title btn btn-default white bold" href="<?=$arItem["URL"]?>"><?=$arItem["NAME"]?></a>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </div>
            <?elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
                $arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];?>
                <a class="bx_item_block" href="<?=$arItem["URL"]?>">
                    <div class="maxwidth-theme">
                        <div class="bx_img_element">
                            <?if(is_array($arElement["PICTURE"])):?>
                                <img src="<?=$arElement["PICTURE"]["src"]?>">
                            <?else:?>
                                <img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_small.png" width="38" height="38">
                            <?endif;?>
                        </div>
                        <div class="bx_item_element">
                            <span><?=$arItem["NAME"]?></span>
                            <div class="price cost prices">
                                <div class="title-search-price">
                                    <?if($arElement["MIN_PRICE"]){?>
                                        <?if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] < $arElement["MIN_PRICE"]["VALUE"]):?>
                                            <div class="price"><?=$arElement["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"]?></div>
                                            <div class="price discount">
                                                <strike><?=$arElement["MIN_PRICE"]["PRINT_VALUE"]?></strike>
                                            </div>
                                        <?else:?>
                                            <div class="price"><?=$arElement["MIN_PRICE"]["PRINT_VALUE"]?></div>
                                        <?endif;?>
                                    <?}else{?>
                                        <?foreach($arElement["PRICES"] as $code=>$arPrice):?>
                                            <?if($arPrice["CAN_ACCESS"]):?>
                                                <?if (count($arElement["PRICES"])>1):?>
                                                    <div class="price_name"><?=$arResult["PRICES"][$code]["TITLE"];?></div>
                                                <?endif;?>
                                                <?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
                                                    <div class="price"><?=$arPrice["PRINT_DISCOUNT_VALUE"]?></div>
                                                    <div class="price discount">
                                                        <strike><?=$arPrice["PRINT_VALUE"]?></strike>
                                                    </div>
                                                <?else:?>
                                                    <div class="price"><?=$arPrice["PRINT_VALUE"]?></div>
                                                <?endif;?>
                                            <?endif;?>
                                        <?endforeach;?>
                                    <?}?>
                                </div>
                            </div>
                        </div>
                        <?if (intval($arElement["AMOUNT"]) > 0) {?>
                            <span data-value="<?= $arElement["MIN_PRICE"] ?>" 
                                data-currency="RUB" 
                                class="small to-cart btn btn-default transition_bg animate-load" 
                                data-item="<?= $arElement["ID"] ?>" 
                                data-float_ratio="" 
                                data-ratio="1" 
                                data-bakset_div="bx_basket_div_<?= $arElement["ID"] ?>" 
                                data-props="" 
                                data-part_props="Y" 
                                data-add_props="Y" 
                                data-empty_props="Y" 
                                data-offers="" 
                                data-iblockid="26" 
                                data-quantity="1">
                                    <i></i>
                                    <span>В корзину</span>
                            </span>
                        <?}?>
                        <div style="clear:both;"></div>
                    </div>
                </a>
            <?else:?>
                <?if($arItem["MODULE_ID"]):?>
                    <a class="bx_item_block others_result" href="<?=$arItem["URL"]?>">
                        <div class="maxwidth-theme">
                            <div class="bx_item_element">
                                <span><?=$arItem["NAME"]?></span>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                    </a>
                <?endif;?>
            <?endif;?>
        <?endforeach;?>
    <?endforeach;?>
</div>
<script>
$(document).ready(function(){
    $(".bx_item_element").each(function(){
        $(this).find("span").html(truncate($(this).find("span").html(), 60));
    })
});
</script>