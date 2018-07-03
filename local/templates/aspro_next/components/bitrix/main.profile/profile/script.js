$(document).ready(function(){
    $('a.cancel').click(function(e){
        e.preventDefault()
        document.form1.reset();
    });                      
    // отображение поп-апа смены телефона
    $(".changePhoneNumberButton").on("click", function(e){
        e.preventDefault();
        $(".jqmOverlay").show();
        $(".popup").css("top", (window.innerHeight - $(".popup").height()) / 2);
        $(".popup").show();
    });
    // отправка SMS с кодом по указанному номеру телефона
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
                
            });
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
        } else {
            $(".phoneNumber").css("border", "1px solid red");
        }
    });
    /**
    * проверка введённого кода и сравнение с кодом подтверждения, присланным в SMS
    */
    $(".confirmCodeButton").on("click", function(e){
        e.preventDefault();
        var form_data = "sessid=" + $("input[name='sessid']").val() + "&oldPhoneNumber=" + $(".PHONE_NUMBER").val() + "&phoneNumber=" + $(".phoneNumber").val() + "&code=" + $(".smsCode").val();
        $.ajax({
            type: "POST",
            url: "/ajax/smscodecheck.php",
            data: form_data,
        }).done(function(strResult){
            if (strResult == "error") {
                $(".wrongCode").show();    
            } else if (strResult == "timestamp_error") {
                $(".wrongCode").html("Срок жизни подтверждаемой операции истек.");
                $(".wrongCode").show();
            } else {
                $(".PHONE_NUMBER").val($(".phoneNumber").val());
                $(".hidden_PHONE_NUMBER").val($(".phoneNumber").val());
                $("form#registraion-page-form").submit();
    
            }
        })
    });
    
    $(".changePhoneNumber, .popupClose").on("click", function(){
        $(".popup").hide();
        $(".jqmOverlay").hide();
    });
    $(".main").on("submit", function(){
        $(".main").append("<input type='hidden' name='PERSONAL_PHONE' class='hidden_phone' maxlength='255' value='" + $("input[name='phone_number']").val() + "'>")
    })
});
