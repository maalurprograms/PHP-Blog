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

function checkLogIn(e, object) {
    e.preventDefault();
    xhhtp.open("POST", "check_user.php", true);
    xhhtp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    var dataLogin = "email="+$("#login-email").val()+"&password="+$("#login-password").val();
    var dataRegister = "email="+$("#register-email").val()+"&username="+$("#register-username").val()+"&password="+$("#register-password").val()+"&password_retype="+$("#register-password-retype").val();
    if (object.attr("name") == "login") {
        xhhtp.send(dataLogin);
    } else {
        xhhtp.send(dataRegister);
    }
    return false;
}

function checkPassword() {
    if ($("#register-password").val() != $("#register-password-retype").val() || !$("#register-password").val() || !$("#register-password-retype").val() ) {
        $('#alarmtextpw').show();
        $('#submit').hide();
    } else {
        $('#submit').show();
        $('#alarmtextpw').hide();
    }
}

$(document).ready(function(){
    xhhtp = setXHTTP("#case");
    $('#alarmtextpw').hide();
    $('#submit').hide();
    $('.password').on("keyup", checkPassword);
    $(".form").submit(function(e){checkLogIn(e, $(this))});
});
