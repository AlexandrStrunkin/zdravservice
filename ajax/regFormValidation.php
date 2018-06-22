<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if ($_REQUEST["REGISTER"]["PERSONAL_PHONE"] && $_REQUEST["REGISTER"]["EMAIL"] && check_bitrix_sessid()) {
    $phoneNumber = \Webgk\Main\Tools::updateUserPhoneOnRegForm($_REQUEST["REGISTER"]["PERSONAL_PHONE"]);
    $rsUsers = CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array('LOGIC' => 'OR', array("PERSONAL_PHONE" => $phoneNumber), array("EMAIL" => $_REQUEST["REGISTER"]["EMAIL"])) );
    if ($rsUsers->SelectedRowsCount() <= 0) {
        $result = "ok";
    } else {
        $result = "Пользователь с данным номером телефона или e-mail уже существует.";
    }
    echo $result;
}
?>