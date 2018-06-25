<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if ($_REQUEST["REGISTER"]["PERSONAL_PHONE"] && $_REQUEST["REGISTER"]["EMAIL"] && check_bitrix_sessid()) {
    $phoneNumber = \Webgk\Main\Tools::formatUserPhone($_REQUEST["REGISTER"]["PERSONAL_PHONE"]);
    $rsUsers = CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array('LOGIC' => 'OR', array("PERSONAL_PHONE" => $phoneNumber), array("EMAIL" => $_REQUEST["REGISTER"]["EMAIL"])) );
    if ($rsUsers->SelectedRowsCount() <= 0) {
        $result = "ok";
    } else {
        while ($arUsers = $rsUsers -> Fetch()) {
            if ($arUsers["PERSONAL_PHONE"] == $phoneNumber) {
                $result = "Пользователь с данным номером телефона уже существует.";    
            }
            if ($arUsers["EMAIL"] == $_REQUEST["REGISTER"]["EMAIL"]) {
                $result .= "<br>Пользователь с данным e-mail уже существует.";    
            }
        }
    }
    echo $result;
}
?>