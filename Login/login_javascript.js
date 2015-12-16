// Diese Funktion setzt die XHTTP Request zusammen.
function setXHTTP(section) {
    var xhttp;
    xhttp = new XMLHttpRequest();
    // Wenn readyState 4 ist (request finished and response is ready) und status 200 ("Ok") wird die als Parameter übergebene Variabel durch die Antwort der Anfrage ersetzt.
    // In diesem Fall wird also das Element "section" durch die Antwort vom Server ersetzt.
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            $(section).html(xhttp.responseText);
        }
    };
    // Wir geben "xhttp" nun fertig konfiguriert zurück
    return xhttp;
}

// Diese Funktion sendet die Login/Registration Daten an den Server damit dieser überprüfen kann ob die Wert stimmen.
function checkLogIn(e, object) {
    // Da die Werte von einem <form>-Tag kommen, muss verhindert werden, dass die Seite automatisch bei Submit neu lädt, da ansonsten alles zurückgesetzt wird.
    // Dies macht .reventDefault(). Es verhindert aber auch das die Daten an den Server mitels POST gesendet werden weshalb wir das nun manuel machen müssen.
    // Wiso bennötigt es dann hier ein <form>? Weil ich mit <form> die Eingaben überprüfe ( Also ob die Email einer Email entspricht).
    e.preventDefault();
    // Nun definieren wir die Anfrage an den Server. Wir setzten POST als Methode und als Rückgabewert wollen wir "check_login.php".
    // true wird gesetzt dammit mehrere Anfragen gleichzeitig bearbeitet werden können. Mit false würden alle Anfragen der Reihe und nicht gleichzeitig bearbeitet werden.
    xhhtp.open("POST", "check_login.php", true);
    // Nun setzten wir noch den Typ der Daten die wir im Header mitsenden werden.
    xhhtp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // Wir bereiten die Daten für Login und Register auf da wir zu diesem Zeitpunkt noch nicht wissen, ob der User ein Login oder eine Registration gemacht hat.
    var dataLogin = "email="+$("#login-email").val()+"&password="+$("#login-password").val();
    var dataRegister = "email="+$("#register-email").val()+"&username="+$("#register-username").val()+"&password="+$("#register-password").val()+"&password_retype="+$("#register-password-retype").val();
    // Nun überprüfen wir ob der User sich registriert oder angemeldet hat, und senden die Anfrage mit den entsprechenden Daten.
    if (object.attr("name") == "login") {
        xhhtp.send(dataLogin);
    } else {
        xhhtp.send(dataRegister);
    }
}

// Mit dieser Funktion wird überprüft, ob die beiden Passwörter bei der Registration gleich sind oder überhaupt etwas eingegeben wurde.
function checkPassword() {
    if ($("#register-password").val() != $("#register-password-retype").val() || !$("#register-password").val() || !$("#register-password-retype").val() ) {
        // Wenn nichts eingegeben wurde oder sie nicht übereinstimmen, werden dementsprechende Fehlermeldungen angezeigt und der Submit-Button verschwindet.
        $('#alarmtextpw').show();
        $('#submit').hide();
    } else {
        // Wenn sie übereinstimmen wird der Submit-Button angezeigt und die Fehlermeldung versteckt.
        $('#submit').show();
        $('#alarmtextpw').hide();
    }
}

// Wenn das Dokument geladen ist, wird die Fehlermeldung sowie der Submit-Button im Registrationsbereich versteckt.
// Ausserdem wird eine Anfrage vorbereitet.
$(document).ready(function(){
    xhhtp = setXHTTP("#case");
    $('#alarmtextpw').hide();
    $('#submit').hide();
    // Wenn in einem der Passwortfelder im Registrationsbereich etwas eingegeben wird, wird die Methode "checkPassword" aufgerufen
    $('.password').on("keyup", checkPassword);
    // Wenn der Submit-Button gedrückt wird, wird die Methode "checkLogIn" aufgerufen und als parameter wird das <form> mit gegeben,
    // damit in der Methode das neu laden der Seite verhindert werden kann.
    $(".form").submit(function(e){checkLogIn(e, $(this))});
});
