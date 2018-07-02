$(document).ready(function(){
    $(".wrap_md.birthday input").mask("99.99.9999");
    $(".phoneNumber").mask("+7(999)999-99-99")
    $(".changePhoneNumberButton").on("click", function(e){
        e.preventDefault();
        $(".jqmOverlay").show();
        $(".popup").css("top", (window.innerHeight - $(".popup").height()) / 2);
        $(".popup").show();
    });
    
        $(".sendConfirmSMS, .resendingSmsButton a").on("click", function(e){
        e.preventDefault();
        if ($(".phoneNumber").val() != "") {
            $(".smsCodeField").show();
            $(".resendingSmsInfo").show();
            $.ajax({
                type: "POST",
                url: "/ajax/smsconfirmation.php",
                data: {sessid: $("input[name='sessid']").val(), phoneNumber: $(".phoneNumber").val()},
            }).done(function(strResult){
                if (strResult == "existed_user_error") {
                    $(".existedPhoneNumber").show();    
                } else {
                    $(".existedPhoneNumber").hide();
                    $(".sendConfirmSMS, .resendingSmsButton").hide();
                    $(".sendedSmsNote").show();
                    var remain_bv = 60;
                    var refreshingTimerInterval = setInterval(function(){
                        remain_bv = remain_bv - 1;
                        parseTime_bv(remain_bv);
                        if(remain_bv <= 0){
                            $(".resendingSmsButton").show();
                            $(".resendingSmsInfo").hide();
                            clearInterval(refreshingTimerInterval);    
                        }
                        }, 1000);
                }    
            });
        } else {
            $(".phoneNumber").css("border", "1px solid red");
        }
    });
    /**
    * проверка введённого кода и сравнение с кодом подтверждения, присланным в SMS
    */
    $(".confirmCodeButton").on("click", function(e){
        e.preventDefault();
        //var form_data = "sessid=" + $("input[name='sessid']").val() + "&oldPhoneNumber=" + $(".PHONE_NUMBER").val() + "&phoneNumber=" + $(".phoneNumber").val() + "&code=" + $(".smsCode").val();
        $.ajax({
            type: "POST",
            url: "/ajax/smscodecheck.php",
            data: {sessid: $("input[name='sessid']").val(), oldPhoneNumber: $(".PHONE_NUMBER").val(), phoneNumber: $(".phoneNumber").val(), code: $(".smsCode").val()},
        }).done(function(strResult){
            if (strResult == "error") {
                $(".wrongCode").show();    
            } else if (strResult == "timestamp_error") {
                $(".wrongCode").html("Срок жизни подтверждаемой операции истек.");
                $(".wrongCode").show();
            } else {
                $(".popup").hide();
                $(".jqmOverlay").hide();
                $(".PHONE_NUMBER").val($(".phoneNumber").val());
                $(".hidden_PHONE_NUMBER").val($(".phoneNumber").val());
    
            }
        })
    });
    
    $(".changePhoneNumber, .popupClose").on("click", function(){
        $(".popup").hide();
        $(".jqmOverlay").hide();
    });
    
    $(".iblock_add").on("submit", function(){
        $(".iblock_add").append("<input type='hidden' name='PROPERTY[358][0]' class='hidden_PHONE_NUMBER' value='" + $("input[name='PROPERTY_PHONE_NUMBER']").val() + "'>")
    })
});