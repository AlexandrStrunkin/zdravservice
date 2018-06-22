<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if ($_REQUEST["phoneNumber"] && $_REQUEST["email"]) {
    $phoneNumber = preg_replace("/\D/", "", $_REQUEST["phoneNumber"]);
    if (strlen($phoneNumber) == 11) {
        if (substr($phoneNumber, 0, 1) == "8") {
            $userPhone = substr_replace($phoneNumber, "7", 0, 1);
        }
        $phoneNumber = "+".$phoneNumber;    
    }
    $rsUsers = CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("PERSONAL_PHONE" => $phoneNumber));
    if ($rsUsers->SelectedRowsCount() <= 0) {
        $rsUsersByEmail = CUser::GetList(($by = "timestamp_x"), ($order = "desc"), array("EMAIL" => $_REQUEST["email"]));
        if ($rsUsersByEmail->SelectedRowsCount() <= 0) {
            $result = "ok";
        } else {
            $result = "Пользователь с данным e-mail уже существует.";
        }
    } else {
        $result = "Пользователь с данным номером телефона уже существует.";
    }
    echo $result;
}
?>