function setXHTTP(section) {
    var xhttp;
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            $(section).html(xhttp.responseText);
        }
    };
    return xhttp;
}

function checkLogIn() {
    xhhtp = setXHTTP("#content")
    xhttp_content.open("POST", "check_user.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send();
}

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
