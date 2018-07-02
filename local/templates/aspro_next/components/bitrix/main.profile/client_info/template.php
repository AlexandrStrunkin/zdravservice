<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="module-form-block-wr lk-page border_block">

<??>
	<div class="form-block-wr">
	    <table>
            <?if ($arResult["arUser"]["NAME"]) {?>
                <tr>
                    <td>Фамилия Имя Отчество</td>
                    <td><?= $arResult["arUser"]["LAST_NAME"] . " " . $arResult["arUser"]["NAME"] . " " . $arResult["arUser"]["SECOND_NAME"] ?></td>
                </tr>
            <?}?>
            <?if ($arResult["arUser"]["PERSONAL_PHONE"]) {?>
                <tr>
                    <td>Мобильный телефон</td>
                    <td><?= $arResult["arUser"]["PERSONAL_PHONE"] ?></td>
                </tr>
            <?}?>
            <?if ($arResult["arUser"]["EMAIL"]) {?>
                <tr>
                    <td>E-mail</td>
                    <td><?= $arResult["arUser"]["EMAIL"] ?></td>
                </tr>
            <?}?>
            <?if ($arResult["arUser"]["PERSONAL_GENDER"]) {?>
                <tr>
                    <td>Пол</td>
                    <td><?= $arResult["arUser"]["PERSONAL_GENDER"] ?></td>
                </tr>
            <?}?>
            <?if ($arResult["arUser"]["PERSONAL_BIRTHDAY"]) {?>
                <tr>
                    <td>Дата рождения</td>
                    <td><?= $arResult["arUser"]["PERSONAL_BIRTHDAY"] ?></td>
                </tr>
            <?}?>
        </table>	
	</div>
</div>
<span>Для изменения данных анкеты обратитесь в контактный центр 8 800 700-44-03, ежедневно с 9:00 до 21:00 (МСК)</span>