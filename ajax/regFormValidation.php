<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($_REQUEST["REGISTER"]["PERSONAL_PHONE"] && $_REQUEST["REGISTER"]["EMAIL"] && check_bitrix_sessid()) {
    $phoneNumber = \Webgk\Main\Tools::formatUserPhone($_REQUEST["REGISTER"]["PERSONAL_PHONE"]);
    $result = "";
    $error = array();
    $rsUsers = CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("PERSONAL_PHONE" => $phoneNumber));
    if ($rsUsers->SelectedRowsCount() > 0) {
        $error[] = "Пользователь с данным номером телефона уже существует.";
    }
    $rsUsersByEmail = CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("EMAIL" => $_REQUEST["REGISTER"]["EMAIL"]));
    if ($rsUsersByEmail->SelectedRowsCount() > 0) {
        $error[] = "Пользователь с данным e-mail уже существует.";    
    }
    if (!empty($error)) {
        $result = implode("<br>", $error);
    } else {
        $result = "ok";
    }
    echo $result;
}
?>