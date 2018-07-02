/**
* проверка на ввод только чисел в поле кода подтверждения из SMS
* 
* @param evt
*/
function validate(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

$(document).ready(function(){
    /**
    * отправка SMS с кодом подтверждения
    */
    $(".sendConfirmSMS, .resendingSmsButton a").on("click", function(e){
        e.preventDefault();
        $(".smsCodeField").show();
        $(".resendingSmsInfo").show();
        $.ajax({
            type: "POST",
            url: "/ajax/smsconfirmation.php",
            data: {sessid: $("input[name='sessid']").val(), phoneNumber: $("#input_PERSONAL_PHONE").val()},
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
    });
    /**
    * проверка введённого кода и сравнение с кодом подтверждения, присланным в SMS
    */
    $(".confirmCodeButton").on("click", function(e){
        e.preventDefault();
        var form_data = $(".form form").serialize();
        form_data += "&phoneNumber=" + $("#input_PERSONAL_PHONE").val() + "&code=" + $(".smsCode").val();
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
                $("form#registraion-page-form").validate
                ({
                    submitHandler: function( form ) {
                        var eventdata = {type: 'form_submit', form: $("form#registraion-page-form"), form_name: 'REGISTER'};
                        BX.onCustomEvent('onSubmitForm', [eventdata]);
                    }
                });
                $("form#registraion-page-form").submit();
    
            }
        })
    });
    
    $(".changePhoneNumber, .popupClose").on("click", function(){
        $(".popup").hide();
        $(".jqmOverlay").hide();
    });

})