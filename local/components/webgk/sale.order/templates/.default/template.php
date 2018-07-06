<?
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    use \Webgk\Main\Tools;

    //tools::arshow($arResult);
?>
<div id="bx-soa-order" class="row bx-blue">
    <!--    MAIN BLOCK    -->
    <div class="col-sm-9 bx-soa">
        <div id="bx-soa-main-notifications">
            <div data-type="informer" style="" class="alert alert-warning">
                <div class="row">
                    <div class="col-xs-12" style="position: relative; padding-left: 48px;">
                        <div class="icon-warning"></div>
                        <div>Вы заказывали в нашем интернет-магазине, поэтому мы заполнили все данные автоматически.<br>
                            Обратите внимание на развернутый блок с информацией о заказе. Здесь вы можете внести необходимые изменения или 
                            оставить как есть и нажать кнопку "Оформить заказ".
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--    AUTH BLOCK    -->        

        <!--    DUPLICATE MOBILE ORDER SAVE BLOCK    -->
        <div id="bx-soa-total-mobile" style="margin-bottom: 6px;" class="visible-xs">
            <div class="bx-soa-cart-total">
                <div class="change_basket">Ваш заказ</div>
                <div class="bx-soa-cart-total-line">
                    <span class="bx-soa-cart-t">Товаров на:</span>
                    <span class="bx-soa-cart-d"><?=$arResult["RESULT"]["PRICE"]["VALUE"]?> руб.</span>
                </div>                  
                <div class="bx-soa-cart-total-line bx-soa-cart-total-line-total">
                    <span class="bx-soa-cart-t">Итого:</span>
                    <span class="bx-soa-cart-d"><?=$arResult["RESULT"]["PRICE"]["VALUE"]?> руб.</span>
                </div>
                <div class="bx-soa-cart-total-button-container lic_condition">
                    <a href="javascript:void(0)" class="btn btn-default btn-lg btn-order-save">Оформить заказ</a>
                </div>
            </div>
        </div>


        <!--    REGION BLOCK    -->
        <div id="bx-soa-region" data-visited="true" class="bx-soa-section  ">
            <div class="bx-soa-section-title-container">
                <h2 class="bx-soa-section-title col-sm-9">
                    <span class="bx-soa-section-title-count"></span>Получение
                </h2>
                <div class="col-xs-12 col-sm-3 text-right"><a href="javascript:void(0)" class="bx-soa-editstep js-set-pharmacy">изменить</a></div>
            </div>

            <div class="bx-soa-section-content"><div class="alert alert-danger" style="display: none;"></div>
                <br>
                <strong>Аптека:</strong> Тула Вильямса 32 аптека №21                
            </div> 


            <div class="bx-soa-section-content container-fluid">
                <div class="order-pharmacy-list">
                    <div class="order-pharmacy-list-headers">
                        <div class="order-pharmacy-list-headers-title farmacia-cell first-cell">Дата</div>
                        <div class="order-pharmacy-list-headers-title farmacia-cell second-cell">Доступность</div>
                        <div class="order-pharmacy-list-headers-title farmacia-cell third-cell">Аптека</div>
                        <div class="order-pharmacy-list-headers-title farmacia-cell last-cell">Выбрать</div>
                    </div>
                    
                    <div class="order-pharmacy-list-item active-item">
                        <div class="farmacia-cell first-cell farmacia-date">Завтра - 27.07.2018</div>
                        <div class="farmacia-cell second-cell farmacia-available">Все довары доступны</div>
                        <div class="farmacia-cell third-cell farmacia-name">Тула Ак. Павлова 1д</div>
                        <div class="farmacia-cell last-cell farmacia-button"><button>Выбрано</button></div>
                    </div>
                    
                    <div class="order-pharmacy-list-item">
                        <div class="farmacia-cell first-cell farmacia-date">Завтра - 27.07.2018</div>
                        <div class="farmacia-cell second-cell farmacia-available available">Все довары доступны</div>
                        <div class="farmacia-cell third-cell farmacia-name">Тула Ак. Павлова 1д</div>
                        <div class="farmacia-cell last-cell farmacia-list-button"><button>Выбрать</button></div>
                    </div>
                    
                    <div class="order-pharmacy-list-item not-available">
                        <div class="farmacia-cell first-cell farmacia-date">Недоступно</div>
                        <div class="farmacia-cell second-cell farmacia-available">Все довары недоступны</div>
                        <div class="farmacia-cell third-cell farmacia-name">Тула Ак. Павлова 1д</div>
                        <div class="farmacia-cell last-cell farmacia-button"><button>Выбрать</button></div>
                    </div>
                    
                    <div class="order-pharmacy-list-item">
                        <div class="farmacia-cell first-cell farmacia-date">Завтра - 27.07.2018</div>
                        <div class="farmacia-cell second-cell farmacia-available">Все довары доступны</div>
                        <div class="farmacia-cell third-cell farmacia-name">Тула Ак. Павлова 1д</div>
                        <div class="farmacia-cell last-cell farmacia-button"><button>Выбрать</button></div>
                    </div>
                    
                    <div class="order-pharmacy-list-item">
                        <div class="farmacia-cell first-cell farmacia-date">Завтра - 27.07.2018</div>
                        <div class="farmacia-cell second-cell farmacia-available">Все довары доступны</div>
                        <div class="farmacia-cell third-cell farmacia-name">Тула Ак. Павлова 1д</div>
                        <div class="farmacia-cell last-cell farmacia-button"><button>Выбрать</button></div>
                    </div>
                    
                </div>
            </div>

        </div>

        <!--    PICKUP BLOCK    -->
        <div id="bx-soa-pickup" data-visited="false" class="bx-soa-section" style="display:none">
            <div class="bx-soa-section-title-container">
                <h2 class="bx-soa-section-title col-sm-9">
                    <span class="bx-soa-section-title-count"></span>
                </h2>
                <div class="col-xs-12 col-sm-3 text-right"><a href="" class="bx-soa-editstep">изменить</a></div>
            </div>
            <div class="bx-soa-section-content container-fluid"></div>
        </div>

        <!--    BUYER PROPS BLOCK    -->
        <div id="bx-soa-properties" data-visited="false" class="bx-soa-section bx-active">
            <div class="bx-soa-section-title-container">
                <h2 class="bx-soa-section-title col-sm-9">
                    <span class="bx-soa-section-title-count"></span>
                    Покупатель
                </h2>
                <div class="col-xs-12 col-sm-3 text-right">
                    <a href="" class="bx-soa-editstep">изменить</a>
                </div>
            </div>

            <div class="bx-soa-section-content container-fluid">
                <div class="alert alert-danger" style="display: none;"></div>
                <div><strong>Ф.И.О.:</strong> &lt;Без имени&gt;вйцвйцв</div>
                <div><strong>E-Mail:</strong> support@webgk.ru</div>
                <div><strong>Мобильный телефон:</strong> +7 (123) 123-12-31</div>
                <div><strong>Адрес доставки:</strong> 12312312уцвввс sedwefwefwefwef</div>
            </div>
        </div>

        <!--    BASKET ITEMS BLOCK    -->
        <div id="bx-soa-basket" data-visited="false" class="bx-soa-section bx-active">
            <div class="bx-soa-section-title-container">
                <h2 class="bx-soa-section-title col-sm-9">
                    <span class="bx-soa-section-title-count"></span>
                    Товары в заказе
                </h2>
                <div class="col-xs-12 col-sm-3 text-right"></div>
            </div>

            <div class="bx-soa-section-content container-fluid">
                <div class="bx-soa-table-fade">
                    <div style="overflow-x: auto; overflow-y: hidden;">
                        <div class="bx-soa-item-table">
                            <div class="bx-soa-item-tr hidden-sm hidden-xs">
                                <div class="bx-soa-item-td" style="padding-bottom: 5px;">
                                    <div class="bx-soa-item-td-title">Наименование</div>
                                </div>
                                <div class="bx-soa-item-td bx-soa-item-properties bx-text-right" style="padding-bottom: 5px;">
                                    <div class="bx-soa-item-td-title">Цена</div>
                                </div>                                 
                                <div class="bx-soa-item-td bx-soa-item-properties bx-text-right" style="padding-bottom: 5px;">
                                    <div class="bx-soa-item-td-title">Количество</div>
                                </div>
                                <div class="bx-soa-item-nth-4p1"></div>
                                <div class="bx-soa-item-td bx-soa-item-properties bx-text-right" style="padding-bottom: 5px;">
                                    <div class="bx-soa-item-td-title">Сумма</div>
                                </div>
                            </div>

                            <?foreach ($arResult["BASKET_ITEMS"] as $item) {?>
                                <div class="bx-soa-item-tr bx-soa-basket-info bx-soa-item-tr-first">
                                    <div class="bx-soa-item-td" style="min-width: 300px;">
                                        <div class="bx-soa-item-block">
                                            <div class="bx-soa-item-img-block">
                                                <a href="<?=$item["DETAIL_PAGE_URL"]?>">
                                                    <div class="bx-soa-item-imgcontainer" style="background-image: url(/bitrix/components/bitrix/sale.order.ajax/templates/.default/images/product_logo.png);background-image: -webkit-image-set(url(/upload/resize_cache/iblock/cf0/160_160_1/cf0bed5168ec1a6417fb32c33b315759.jpg) 1x, url(/upload/resize_cache/iblock/cf0/320_320_1/cf0bed5168ec1a6417fb32c33b315759.jpg) 2x)"></div>
                                                    <?// /bitrix/components/bitrix/sale.order.ajax/templates/.default/images/product_logo.png?>
                                                </a>
                                            </div>
                                            <div class="bx-soa-item-content">
                                                <div class="bx-soa-item-title">
                                                    <a href="<?=$item["DETAIL_PAGE_URL"]?>"><?=$item["NAME"]?></a></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Цена</div>
                                        <div class="bx-soa-item-td-text"><strong class="bx-price"><?=$item["PRICE"]["VALUE"]?> руб.</strong>
                                        </div>
                                    </div>                                   
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Количество</div>
                                        <div class="bx-soa-item-td-text">
                                            <span><?=$item["QUANTITY"]?>&nbsp;шт</span>
                                        </div>
                                    </div>

                                    <div class="bx-soa-item-nth-4p1"></div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Сумма</div>
                                        <div class="bx-soa-item-td-text"><strong class="bx-price all"><?=$item["PRICE"]["TOTAL"]?> руб.</strong></div>
                                    </div>

                                </div>
                                <?}?>    

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--    ORDER SAVE BLOCK    -->
        <div class="form">
            <div class="licence_block filter label_block">
                <label data-for="licenses_order" class="hidden error">Согласитесь с условиями</label>
                <input type="checkbox" name="licenses_order" required="" value="Y">
                <label data-for="licenses_order" class="license">Я согласен на <a href="/include/licenses_detail.php" target="_blank">обработку персональных данных</a></label>
            </div>
        </div>

        <div id="bx-soa-orderSave" class="lic_condition">          
            <a href="javascript:void(0)" style="margin: 10px 0" class="pull-right btn btn-default btn-lg hidden-xs" data-save-button="true">Оформить заказ</a>
        </div>

        <div style="display: none;">
            <div id="bx-soa-basket-hidden" class="bx-soa-section">
                <div class="bx-soa-section-content container-fluid">
                    <div class="bx-soa-table-fade">
                        <div style="overflow-x: auto; overflow-y: hidden;">

                            <div class="bx-soa-item-table">

                                <div class="bx-soa-item-tr hidden-sm hidden-xs"><div class="bx-soa-item-td" style="padding-bottom: 5px;">
                                        <div class="bx-soa-item-td-title">Наименование</div>
                                    </div>

                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right" style="padding-bottom: 5px;">
                                        <div class="bx-soa-item-td-title">Цена</div>
                                    </div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right" style="padding-bottom: 5px;">
                                        <div class="bx-soa-item-td-title">Количество</div>
                                    </div>
                                    <div class="bx-soa-item-nth-4p1"></div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right" style="padding-bottom: 5px;">
                                        <div class="bx-soa-item-td-title">Сумма</div>
                                    </div>
                                </div>

                                <div class="bx-soa-item-tr bx-soa-basket-info bx-soa-item-tr-first">
                                    <div class="bx-soa-item-td" style="min-width: 300px;">
                                        <div class="bx-soa-item-block">
                                            <div class="bx-soa-item-img-block">
                                                <a href="/catalog/spg/preparaty-dlya-lecheniya-zabolevaniy-gorla-polosti-rta/stomatologicheskie-geli/kholisal-gel-stomatologicheskiy-15g-tuba-alyum-valeant/">
                                                    <div class="bx-soa-item-imgcontainer" style="background-image: url(/bitrix/components/bitrix/sale.order.ajax/templates/.default/images/product_logo.png);background-image: -webkit-image-set(url(/bitrix/components/bitrix/sale.order.ajax/templates/.default/images/product_logo.png) 1x, url(/bitrix/components/bitrix/sale.order.ajax/templates/.default/images/product_logo.png) 2x)">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="bx-soa-item-content">
                                                <div class="bx-soa-item-title">
                                                    <a href="/catalog/spg/preparaty-dlya-lecheniya-zabolevaniy-gorla-polosti-rta/stomatologicheskie-geli/kholisal-gel-stomatologicheskiy-15g-tuba-alyum-valeant/">Холисал гель стоматологический 15г туба алюм, Валеант</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Цена</div>
                                        <div class="bx-soa-item-td-text"><strong class="bx-price">465 руб.</strong>
                                            <br>
                                            <small>Московская область</small>
                                        </div>
                                    </div>

                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Количество</div>
                                        <div class="bx-soa-item-td-text">
                                            <span>1&nbsp;шт</span>
                                        </div>
                                    </div>

                                    <div class="bx-soa-item-nth-4p1"></div>

                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Сумма</div>
                                        <div class="bx-soa-item-td-text">
                                            <strong class="bx-price all">465 руб.</strong>
                                        </div>
                                    </div>
                                </div>   

                                <div class="bx-soa-item-tr bx-soa-basket-info">
                                    <div class="bx-soa-item-td" style="min-width: 300px;">
                                        <div class="bx-soa-item-block">
                                            <div class="bx-soa-item-img-block">
                                                <a href="/catalog/aromaterapiya/aromaterapiya458/efirnye-masla/maslo-bergamota-10ml-efirnoe-tm-pellesana-rino-grupp/">
                                                    <div class="bx-soa-item-imgcontainer" style="background-image: url(/bitrix/components/bitrix/sale.order.ajax/templates/.default/images/product_logo.png);"></div>
                                                </a>
                                            </div>
                                            <div class="bx-soa-item-content"><div class="bx-soa-item-title"><a href="/catalog/aromaterapiya/aromaterapiya458/efirnye-masla/maslo-bergamota-10ml-efirnoe-tm-pellesana-rino-grupp/">Масло бергамота 10мл эфирное TM Pellesana, Рино групп</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Цена</div>
                                        <div class="bx-soa-item-td-text">
                                            <strong class="bx-price">103 руб.</strong>
                                            <br>
                                            <small>Калужская обл</small>
                                        </div>
                                    </div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Количество</div>
                                        <div class="bx-soa-item-td-text"><span>1&nbsp;шт</span>
                                        </div>
                                    </div>
                                    <div class="bx-soa-item-nth-4p1"></div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Сумма</div>
                                        <div class="bx-soa-item-td-text"><strong class="bx-price all">103 руб.</strong>
                                        </div>
                                    </div>
                                </div> 

                                <div class="bx-soa-item-tr bx-soa-basket-info">
                                    <div class="bx-soa-item-td" style="min-width: 300px;">
                                        <div class="bx-soa-item-block">
                                            <div class="bx-soa-item-img-block">
                                                <a href="/catalog/dermatologicheskie-preparaty/ranozazhivlyayushchie-i-prochie/prochie-dermatologicheskie/vazelin-maz-25g-tulskaya-farmfabrika/">
                                                    <div class="bx-soa-item-imgcontainer" style="background-image: url(/bitrix/components/bitrix/sale.order.ajax/templates/.default/images/product_logo.png);"></div>
                                                </a>
                                            </div>
                                            <div class="bx-soa-item-content">
                                                <div class="bx-soa-item-title">
                                                    <a href="/catalog/dermatologicheskie-preparaty/ranozazhivlyayushchie-i-prochie/prochie-dermatologicheskie/vazelin-maz-25g-tulskaya-farmfabrika/">Вазелин мазь 25г, Тульская Фармфабрика</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Цена</div>
                                        <div class="bx-soa-item-td-text">
                                            <strong class="bx-price">11 руб.</strong>
                                            <br>
                                            <small>Московская область</small>
                                        </div>
                                    </div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Количество</div>
                                        <div class="bx-soa-item-td-text">
                                            <span>1&nbsp;шт</span>
                                        </div>
                                    </div>
                                    <div class="bx-soa-item-nth-4p1">
                                    </div>
                                    <div class="bx-soa-item-td bx-soa-item-properties bx-text-right">
                                        <div class="bx-soa-item-td-title visible-xs visible-sm">Сумма</div>
                                        <div class="bx-soa-item-td-text">
                                            <strong class="bx-price all">11 руб.</strong>
                                        </div>
                                    </div>
                                </div>                                   

                            </div>
                        </div>
                    </div>                     

                    <div class="row bx-soa-more">
                        <div class="bx-soa-more-btn col-xs-12">
                            <a href="javascript:void(0)" class="pull-left btn btn-default btn-md">Назад</a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="bx-soa-region-hidden" class="bx-soa-section">
                <div class="bx-soa-section-content container-fluid">
                    <div class="alert alert-danger" style="display: none;"></div>
                    <div class="bx_soa_location row">
                        <div class="col-xs-12"><div class="form-group">
                                <label class="bx-soa-custom-label">Тип плательщика</label>
                                <br>
                                <div class="radio-inline checked">
                                    <label>
                                        <input checked="true" type="radio" name="PERSON_TYPE" value="1">Физическое лицо</label>
                                </div>
                                <br>
                                <div class="radio-inline"><label>
                                    <input type="radio" name="PERSON_TYPE" value="2">Юридическое лицо</label>
                                </div>
                                <input type="hidden" name="PERSON_TYPE_OLD" value="1">
                            </div>
                            <div class="form-group bx-soa-location-input-container">
                                <label class="bx-soa-custom-label">Выберите профиль</label>
                                <input type="hidden" value="N" id="profile_change" name="profile_change">
                                <select class="form-control" name="PROFILE_ID">
                                    <option value="0">Новый профиль</option><option value="2">&lt;Без имени&gt;вйцвйцв</option>
                                </select>
                            </div>
                            <div data-property-id-row="6" class="form-group bx-soa-location-input-container">
                                <label class="bx-soa-custom-label" for="soa-property-6"> Местоположение<span class="bx-authform-starrequired">                                  *</span></label>

                                <div id="sls-26144" class="bx-sls ">          

                                    <div class="dropdown-block bx-ui-sls-input-block form-control">   

                                        <span class="dropdown-icon"></span>
                                        <input type="text" autocomplete="off" name="ORDER_PROP_6" value="0000250453" class="dropdown-field" placeholder="Введите название ..." style="display: none;">
                                        <div class="bx-ui-sls-container" style="margin: 0px; padding: 0px; border: none; position: relative;">
                                            <input type="text" disabled="disabled" autocomplete="off" class="bx-ui-sls-route" style="padding: 0px; margin: 0px;">
                                            <input type="text" autocomplete="off" value="0000250453" class="bx-ui-sls-fake" placeholder="Введите название ..." title="Тула, Тульская область, Центр, Россия">
                                        </div>

                                        <div class="dropdown-fade2white"></div>
                                        <div class="bx-ui-sls-loader"></div>
                                        <div class="bx-ui-sls-clear" title="Отменить выбор" style="display: block;">
                                        </div>
                                        <div class="bx-ui-sls-pane" style="overflow-y: auto; overflow-x: hidden; display: none;">
                                            <div class="bx-ui-sls-variants"></div>
                                        </div>

                                    </div>         

                                    <div class="bx-ui-sls-error-message"></div>

                                </div>   

                            </div>

                            <input type="hidden" name="RECENT_DELIVERY_VALUE" value="0000250453">
                            <div data-property-id-row="5" class="form-group bx-soa-location-input-container">
                                <label for="altProperty" class="bx-soa-custom-label">Город</label>
                                <input id="altProperty" type="text" placeholder="" autocomplete="city" class="form-control bx-soa-customer-input bx-ios-fix" name="ORDER_PROP_5">
                            </div>
                            <div class="form-group bx-soa-location-input-container" data-property-id-row="4">
                                <label for="zipProperty" class="bx-soa-custom-label"> Индекс<span class="bx-authform-starrequired">*</span></label>
                                <input id="zipProperty" type="text" placeholder="" autocomplete="zip" class="form-control bx-soa-customer-input bx-ios-fix" name="ORDER_PROP_4">
                            </div>
                            <input id="ZIP_PROPERTY_CHANGED" name="ZIP_PROPERTY_CHANGED" type="hidden" value="Y">
                            <div class="bx-soa-reference">Выберите свой город в списке. Если вы не нашли свой город, выберите "другое местоположение", а город впишите в поле "Город"</div>
                        </div>
                    </div>

                    <div class="row bx-soa-more">
                        <div class="bx-soa-more-btn col-xs-12">
                            <a href="javascript:void(0)" class="pull-right btn btn-default btn-md">Далее</a>
                        </div>
                    </div>

                </div>
            </div>

            <div id="bx-soa-paysystem-hidden" class="bx-soa-section"></div>

            <div id="bx-soa-delivery-hidden" class="bx-soa-section">
                <div class="bx-soa-section-content container-fluid">
                    <div class="alert alert-danger" style="display: none;"></div>
                    <div class="bx-soa-pp row">
                        <div class="col-sm-7 bx-soa-pp-item-container">
                            <div class="bx-soa-pp-company col-lg-4 col-sm-4 col-xs-6 bx-selected">
                                <div class="bx-soa-pp-company-graf-container">
                                    <input id="ID_DELIVERY_ID_3" name="DELIVERY_ID" type="checkbox" class="bx-soa-pp-company-checkbox" value="3">
                                    <div class="bx-soa-pp-company-image" style="background-image: url(/upload/resize_cache/sale/delivery/logotip/86e/95_55_1/86ee2cf11162e55bcd408a29c089672d.png);"></div>
                                    <div class="bx-soa-pp-delivery-cost">0 руб.</div>
                                </div>
                                <div class="bx-soa-pp-company-smalltitle">Самовывоз</div>
                            </div>
                        </div>
                        <div class="col-sm-5 bx-soa-pp-desc-container">
                            <div class="bx-soa-pp-company"><div class="bx-soa-pp-company-subTitle">Самовывоз</div>
                                <div class="bx-soa-pp-company-logo"><div class="bx-soa-pp-company-graf-container">
                                    <div class="bx-soa-pp-company-image" style="background-image: url(/upload/resize_cache/sale/delivery/logotip/86e/95_55_1/86ee2cf11162e55bcd408a29c089672d.png);"></div></div>
                                </div>
                                <div class="bx-soa-pp-company-block">
                                    <div class="bx-soa-pp-company-desc">Вы можете самостоятельно забрать заказ из нашего магазина.</div>
                                </div>
                                <div style="clear: both;"></div>
                                <ul class="bx-soa-pp-list">
                                    <li>
                                        <div class="bx-soa-pp-list-termin">Стоимость:</div>
                                        <div class="bx-soa-pp-list-description">0 руб.</div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="bx-soa-coupon">
                        <div class="bx-soa-coupon-label"><label>Применить купон:</label></div>
                        <div class="bx-soa-coupon-block">
                            <div class="bx-soa-coupon-input">
                                <input class="form-control bx-ios-fix" type="text"></div>
                            <span class="bx-soa-coupon-item"></span>
                        </div>
                    </div>
                    <div class="row bx-soa-more">
                        <div class="bx-soa-more-btn col-xs-12">
                            <a href="javascript:void(0)" class="pull-left btn btn-default btn-md">Назад</a>
                            <a href="javascript:void(0)" class="pull-right btn btn-default btn-md">Далее</a>
                        </div>
                    </div>
                </div>

            </div>

            <div id="bx-soa-pickup-hidden" class="bx-soa-section"></div>

            <div id="bx-soa-properties-hidden" class="bx-soa-section">
                <div class="bx-soa-section-content container-fluid">
                    <div class="alert alert-danger" style="display: none;"></div>
                    <div class="row">
                        <div class="col-sm-12 bx-soa-customer">
                            <div class="form-group bx-soa-customer-field" data-property-id-row="1">
                                <label for="soa-property-1" class="bx-soa-custom-label"> Ф.И.О.<span class="bx-authform-starrequired">*</span></label>
                                <div class="soa-property-container">
                                    <input type="text" size="40" name="ORDER_PROP_1" id="soa-property-1" autocomplete="name" placeholder="" class="form-control bx-soa-customer-input bx-ios-fix">
                                </div>
                            </div>
                            <div class="form-group bx-soa-customer-field" data-property-id-row="2">
                                <label for="soa-property-2" class="bx-soa-custom-label"> E-Mail<span class="bx-authform-starrequired">*</span></label>
                                <div class="soa-property-container">
                                    <input type="text" size="40" name="ORDER_PROP_2" id="soa-property-2" autocomplete="email" placeholder="" class="form-control bx-soa-customer-input bx-ios-fix">
                                </div>
                            </div>
                            <div class="form-group bx-soa-customer-field" data-property-id-row="3">
                                <label for="soa-property-3" class="bx-soa-custom-label"> Мобильный телефон<span class="bx-authform-starrequired">*</span></label>
                                <div class="soa-property-container">
                                    <input type="text" size="30" name="ORDER_PROP_3" id="soa-property-3" autocomplete="tel" placeholder="" class="form-control bx-soa-customer-input bx-ios-fix" style="display: none;">
                                    <input type="tel" value="+7 (123) 123-12-31" size="30" name="ORDER_PROP_3" id="soa-property-3" autocomplete="tel" placeholder="" class="form-control bx-soa-customer-input bx-ios-fix">
                                </div>
                            </div>
                            <div class="form-group bx-soa-customer-field" data-property-id-row="7">
                                <label for="soa-property-7" class="bx-soa-custom-label"> Адрес доставки<span class="bx-authform-starrequired">*</span></label>
                                <div class="soa-property-container">
                                    <textarea cols="30" rows="3" name="ORDER_PROP_7" id="soa-property-7" placeholder="" class="form-control bx-ios-fix"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group bx-soa-customer-field">
                                <label for="orderDescription" class="bx-soa-customer-label">Комментарии к заказу:</label>
                                <textarea id="orderDescription" cols="4" class="form-control bx-soa-customer-textarea bx-ios-fix" name="ORDER_DESCRIPTION"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row bx-soa-more">
                        <div class="bx-soa-more-btn col-xs-12">
                            <a href="javascript:void(0)" class="pull-left btn btn-default btn-md">Назад</a>
                            <a href="javascript:void(0)" class="pull-right btn btn-default btn-md">Далее</a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="bx-soa-auth-hidden" class="bx-soa-section">
                <div class="bx-soa-section-content container-fluid reg"></div>
            </div>
        </div>
    </div>

    <!--    SIDEBAR BLOCK    -->
    <div id="bx-soa-total" class="col-sm-3 bx-soa-sidebar">
        <div class="bx-soa-cart-total-ghost" style="padding-top: 0px;"></div>
        <div class="bx-soa-cart-total" style="">
            <div class="change_basket">Ваш заказ</div>
            <div class="bx-soa-cart-total-line">
                <span class="bx-soa-cart-t">Товаров на:</span>
                <span class="bx-soa-cart-d"><?=$arResult["RESULT"]["PRICE"]["VALUE"]?> руб.</span>
            </div>             
            <div class="bx-soa-cart-total-line bx-soa-cart-total-line-total">
                <span class="bx-soa-cart-t">Итого:</span><span class="bx-soa-cart-d" style="font-size: 18px;"><?=$arResult["RESULT"]["PRICE"]["VALUE"]?> руб.</span>
            </div>
            <div class="bx-soa-cart-total-button-container lic_condition">
                <a href="javascript:void(0)" class="btn btn-default btn-lg btn-order-save" style="font-size: 17px;">Оформить заказ</a>
            </div>
        </div>
    </div>

</div>