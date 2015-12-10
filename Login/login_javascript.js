$(document).ready(function(){
    $('#alarmtextpw').hide();
    $('#submit').hide();
    $('.password').on("keyup", function(){
        if ($("#password").val() != $("#password_retype").val() || !$("#password").val() || !$("#password_retype").val() ) {
            $('#alarmtextpw').show();
            $('#submit').hide();
        } else {
            $('#submit').show();
            $('#alarmtextpw').hide();
        }
    });
});
