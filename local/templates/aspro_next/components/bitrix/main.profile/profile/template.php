<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="module-form-block-wr lk-page border_block">

<?ShowError($arResult["strProfileError"]);?>
<?if( $arResult['DATA_SAVED'] == 'Y' ) {?><?ShowNote(GetMessage('PROFILE_DATA_SAVED'))?><br /><?; }?>
<script>
    $(document).ready(function()
    {
        $(".form-block-wr form").validate({rules:{ EMAIL: { email: true }}    });
    })
</script>
    <?global $arTheme?>
    <div class="form-block-wr">
        <form method="post" name="form1" class="main" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
            <?=$arResult["BX_SESSION_CHECK"]?>
            <input type="hidden" name="lang" value="<?=LANG?>" />
            <input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
            <?if($arTheme["LOGIN_EQUAL_EMAIL"]["VALUE"] == "Y"):?>
                <input type="hidden" name="LOGIN" maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"]?>" />
            <?else:?>
                <div class="form-control">
                    <div class="wrap_md">
                        <div class="iblock label_block">
                            <label><?=GetMessage("PERSONAL_LOGIN")?><span class="star">*</span></label>
                            <input required type="text" name="LOGIN" required value="<?=$arResult["arUser"]["LOGIN"]?>" />
                        </div>
                    </div>
                </div>
            <?endif?>
            <?if (!empty($arResult["BONUS_BALANCE"])) {?>
                <div class="form-control">
                    <div class="wrap_md">
                        <div class="iblock label_block">
                            <label><?= GetMessage("BONUS_BALANCE") ?></label>
                            <span><?= $arResult["BONUS_BALANCE"] ?></span>
                        </div>
                        <div class="iblock text_block">
                        </div>
                    </div>
                </div>
            <?}?>
            <?if($arTheme["PERSONAL_ONEFIO"]["VALUE"] == "Y"):?>
                <div class="form-control">
                    <div class="wrap_md">
                        <div class="iblock label_block">
                            <label><?=GetMessage("PERSONAL_FIO")?><span class="star">*</span></label>
                            <?
                            $arName = array();
                            if(!$arResult["strProfileError"])
                            {
                                if($arResult["arUser"]["LAST_NAME"]){
                                    $arName[] = $arResult["arUser"]["LAST_NAME"];
                                }
                                if($arResult["arUser"]["NAME"]){
                                    $arName[] = $arResult["arUser"]["NAME"];
                                }
                                if($arResult["arUser"]["SECOND_NAME"]){
                                    $arName[] = $arResult["arUser"]["SECOND_NAME"];
                                }
                            }
                            else
                                $arName[] = htmlspecialcharsbx($_POST["NAME"]);
                            ?>
                            <input required type="text" name="NAME" maxlength="50" value="<?=implode(' ', $arName);?>" />
                        </div>
                        <div class="iblock text_block">
                            <?=GetMessage("PERSONAL_NAME_DESCRIPTION")?>
                        </div>
                    </div>
                </div>
            <?else:?>
                <div class="form-control">
                    <div class="wrap_md">
                        <div class="iblock label_block">
                            <label><?=GetMessage("PERSONAL_LASTNAME")?></label>
                            <input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"];?>" />
                        </div>
                    </div>
                </div>
                <div class="form-control">
                    <div class="wrap_md">
                        <div class="iblock label_block">
                            <label><?=GetMessage("PERSONAL_NAME")?></label>
                            <input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"];?>" />
                        </div>
                    </div>
                </div>
                <div class="form-control">
                    <div class="wrap_md">
                        <div class="iblock label_block">
                            <label><?=GetMessage("PERSONAL_SECONDNAME")?></label>
                            <input type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"];?>" />
                        </div>
                    </div>
                </div>
            <?endif;?>
            <div class="form-control">
                <div class="wrap_md">
                    <div class="iblock label_block">
                        <label><?=GetMessage("PERSONAL_PHONE")?><span class="star">*</span></label>
                        <?
                        $mask = \Bitrix\Main\Config\Option::get('aspro.next', 'PHONE_MASK', '+7 (999) 999-99-99');
                        if(strpos($arResult["arUser"]["PERSONAL_PHONE"], '+') === false && strpos($mask, '+') !== false)
                        {
                            $arResult["arUser"]["PERSONAL_PHONE"] = '+'.$arResult["arUser"]["PERSONAL_PHONE"];
                        }
                        ?>
                        <input required type="tel" name="phone_number" class="phone" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" disabled/>
                        <br>
                        <button name="changePhoneNumber" class="btn btn-default short changePhoneNumberButton">Сменить номер телефона</button>
                    </div>                                  
                    <div class="iblock text_block">
                        <?=GetMessage("PERSONAL_PHONE_DESCRIPTION")?>
                    </div>
                </div>
            </div>
            <div class="form-control">
                <div class="wrap_md">
                    <div class="iblock label_block">
                        <label><?=GetMessage("PERSONAL_EMAIL")?><span class="star">*</span></label>
                        <input required type="text" name="EMAIL" maxlength="50" placeholder="name@company.ru" value="<? echo $arResult["arUser"]["EMAIL"]?>" />
                    </div>
                    <div class="iblock text_block">
                        <?if($arTheme["LOGIN_EQUAL_EMAIL"]["VALUE"] != "Y"):?>
                            <?=GetMessage("PERSONAL_EMAIL_SHORT_DESCRIPTION")?>
                        <?else:?>
                            <?=GetMessage("PERSONAL_EMAIL_DESCRIPTION")?>
                        <?endif;?>
                    </div>
                </div>
            </div>
            <div class="but-r">
                <button class="btn btn-default" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE_TITLE") : GetMessage("MAIN_ADD_TITLE"))?>"><span><?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE_TITLE") : GetMessage("MAIN_ADD_TITLE"))?></span></button>
            </div>

        </form>
    </div>
</div>
<div class="jqmOverlay" style="height: 100%; width: 100%; position: fixed; left: 0px; top: 0px; z-index: 2999; opacity: 0.5; display: none;"></div>
<div class="popup">
    <div class="popupClose">
    <i></i>
    </div>
    <div class="form">
        <div class="form_head"><h2>Подтверждение мобильного телефона</h2></div>
        <form action="" method="POST" enctype="multipart/form-data" novalidate="novalidate">
            <?=bitrix_sessid_post()?>
            <div class="form_body">
                <span class="smsPopupNote">Укажите новый номер мобильного телефона</span>
                <input type="text" class="phoneNumber" name="phoneNumber">
                <br><br>
                <div class="sendedSmsNote" style="display: none;">
                    СМС отправлено. Введите код в поле ниже.<br>
                    <div class="resendingSmsInfo">Повторная отправка СМС возможна через <span class="secAmount">60</span> сек.</div>
                    <div class="resendingSmsButton" style="display: none;"><a href="javascript:void(0)" class="btn btn-default bold">Отправить повторно</a><br>
                </div>
                <div class="smsCodeField" style="display: none;">
                    <div class="wrongCode" style="display: none;">Код указан неверно</div>
                    <input type="text" name="smsCode" class="smsCode" maxlength="4" onkeypress="validate(event)">
                    <button class="btn btn-default bold confirmCodeButton">Подтвердить</button>
                </div>
            </div>
            <div class="form_footer">
                <button class="btn btn-default bold sendConfirmSMS">Отправить СМС для подтверждения</button>
            </div>
        </div>
    </form>
</div>
</div>