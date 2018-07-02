<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Бонусная программа");
global $USER;
if ($USER->IsAuthorized()) {
    $phoneNumber = "";
    $userInfo = CUser::GetByID($USER->GetID());
    while ($arUser = $userInfo -> Fetch()) {
        $phoneNumber = $arUser["PERSONAL_PHONE"];
    }
    $existedRecord = false;
    if ($phoneNumber) {
        $questionnaireRecords = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 28, "PROPERTY_PHONE_NUMBER" => $phoneNumber), false, false, array("ID", "PROPERTY_PHONE_NUMBER"));
        if ($questionnaireRecords -> SelectedRowsCount() > 0) {
            $existedRecord = true;
        }
        $bonusBalance = \Webgk\Main\ClientBonusInfo::gettingUserBalanceFromDB($phoneNumber);
        if ($existedRecord) {?>
            <div class="bonusBalanceBlock">
                    <?if ($bonusBalance || strlen($bonusBalance)) {?>
                        <h2>Доступно бонусов: <?= $bonusBalance ?></h2>
                    <?} else {?>
                        <h2>Бонусная карта изготавливается</h2>    
                    <?}?> 
            </div>    
        <?}   
    }?>
    <?if ($existedRecord) {?>
        <div>
        <ul>
            <li>Экономьте до 90% стоимости товаров, оплачивая покупки бонусными баллами, 1 балл = 1 рубль.</li>
            <li>Возвращаем до 3% стоимости покупки на карту бонусными баллами</li>
            <li>В 2 раза больше бонусов за 3 дня до и после дня рождения</li>
        </ul>
    </div>
    <div>
        <span>Для использования виртуальной бонусной каты в офлайн-аптеках "Здесь аптека" просто назовите формацевту номер своего мобильного телефона</span>
    </div>
    <?
        $APPLICATION->IncludeComponent(
            "bitrix:main.profile",
            "client_info",
            Array()
        );    
    } else {
        $APPLICATION->IncludeComponent(
	"bitrix:iblock.element.add.form", 
	"adding_to_questionnaire", 
	array(
		"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
		"CUSTOM_TITLE_DETAIL_PICTURE" => "",
		"CUSTOM_TITLE_DETAIL_TEXT" => "",
		"CUSTOM_TITLE_IBLOCK_SECTION" => "",
		"CUSTOM_TITLE_NAME" => "Фамилия Имя Отчество",
		"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
		"CUSTOM_TITLE_PREVIEW_TEXT" => "",
		"CUSTOM_TITLE_TAGS" => "",
		"DEFAULT_INPUT_SIZE" => "30",
		"DETAIL_TEXT_USE_HTML_EDITOR" => "N",
		"ELEMENT_ASSOC" => "CREATED_BY",
		"GROUPS" => array(
			0 => "1",
			1 => "3",
			2 => "4",
			3 => "6",
		),
		"IBLOCK_ID" => "28",
		"IBLOCK_TYPE" => "aspro_next_content",
		"LEVEL_LAST" => "Y",
		"LIST_URL" => "",
		"MAX_FILE_SIZE" => "0",
		"MAX_LEVELS" => "100000",
		"MAX_USER_ENTRIES" => "100000",
		"PREVIEW_TEXT_USE_HTML_EDITOR" => "N",
		"PROPERTY_CODES" => array(
			0 => "358",
			1 => "359",
			2 => "360",
			3 => "361",
			4 => "NAME",
		),
		"PROPERTY_CODES_REQUIRED" => array(
			0 => "358",
			1 => "359",
			2 => "360",
			3 => "361",
			4 => "NAME",
		),
		"RESIZE_IMAGES" => "N",
		"SEF_MODE" => "N",
		"STATUS" => "ANY",
		"STATUS_NEW" => "N",
		"USER_MESSAGE_ADD" => "",
		"USER_MESSAGE_EDIT" => "",
		"USE_CAPTCHA" => "N",
		"COMPONENT_TEMPLATE" => "adding_to_questionnaire"
	),
	false
);    
    }?>
    
<?}?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>