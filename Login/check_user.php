<?php
    // Wir öffnen die Datenbank blog.db. Danach setzten wir die Fehlermeldung bei falschen Eingaben und den Link zurück zum Login.
    // Damit diese Seite und die Loginseite indentisch sind (Aussehen), wird noch das Layout aufgebaut.
    $SQLiteDB = new SQLite3("..\SQL\blog.db");
    $error = "Email/Passwort ungültig<br>";
    $linkToLogin = "<a href='login.html'>Zurück zum login</a>";
    print('
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Information
                </a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">
    ');
    // Nun müssen wir überprüfen ob es sich um ein Login oder eine Registration handelt. Falls weniger als 3 Werte übergeben wurde ist es ein Login (Email, Passwort).
    if (count($_POST) < 3) {
        // Wir überprüfen ob der User existiert.
        // Dafür suchen wir aus der Datenbank alle UserID's bei denen die Email mit der Email die eingegeben wurde übereinstimmt.
        $result = $SQLiteDB->querySingle("select exists(select * from users where Email = '".$_POST["email"]."')");
        if (!$result) {
            // Wenn der User nicht existiert, wird eine Fehlermeldung ausgegeben mit einem Link zurück zum Login.
            print($error);
            print($linkToLogin);
        } else {
            // Wenn der User existiert, wird überprüft ob das eingegebene Passwort mit dem Passwort in der Datenbank  übereinstimmt.
            $password = $SQLiteDB->querySingle("select Password from users where Email = '".$_POST["email"]."'");
            // Dazu verschlüsseln wir das Passwort mit md5 und überprüfen ob der resultierende Code mit dem in der Datenbank übereinstimmt.
            if (md5($_POST["password"]) == $password) {
                // Wenn das Passwort übereinstimmt, wird eine dementsprechende Meldung ausgegeben und ein Link zur Hauptseite.
                print("<a href='../Main/main.html'>Hier gehts zur Homepage</a>");
                // Hier werden die Sessionvariabeln gesetzt und die Session gestartet.
                session_start();
                $_SESSION["EMAIL"] = $_POST["email"];
                $_SESSION["USERNAME"] = $SQLiteDB->querySingle("select Username from users where Email ='".$_POST["email"]."'");
                $_SESSION["USERID"] = $SQLiteDB->querySingle("select UserID from users where Email ='".$_POST["email"]."'");
            // Wenn das Passwort nicht übereinstimmt, wird eine dementsprechende Fehlermeldung ausgegeben mit einem Link zurück zum Login.
            } else {
                print($error);
                print($linkToLogin);
            }
        }
    // Wenn 3 oder mehr übergeben wurde ist es eine Registration (Email, Username, Passwort, Passwort Wiederholung).
    } else {
        // Wir überprüfen ob der User bereits exisitert.
        $result = $SQLiteDB->querySingle("select exists(select * from users where Email = '".$_POST["email"]."')");
        // Wenn der User existiert wird eine dementsprechende Fehlermeldung  ausgegeben mit einem Link zurück zum Login.
        if ($result) {
            print("Diesen User gibt es schon<br>");
            print("<a href='login.html'>Zurück zum login</a>");
        // Wenn der User noch nicht existiert, wird überprüft, ob die beiden Passworter übereinstimmen.
        } else {
            // Wenn die Passwörter übereinstimmen, wird das Passwort verschlüsselt und der User in die Datenbank hinzugefügt.
            if ($_POST["password"] == $_POST["password_retype"]) {
                $hash = md5($_POST["password"]);
                $SQLiteDB->exec("insert into users (Email, Username, Password) values ('".$_POST["email"]."', '".$_POST["username"]."','".$hash."')");
                // Dann wird noch eine Meldung mit einem Link zur Hauptseite ausgegeben.
                print("Vielen Dank<br>");
                print("<a href='..\Main\main.html'>Hier gehts zur Homepage</a>");
                session_start();
                // Hier werden die Sessionvariabeln gesetzt und die Session gestartet.
                $_SESSION["EMAIL"] = $_POST["email"];
                $_SESSION["USERNAME"] = $_POST["username"];
                $_SESSION["USERID"] = $SQLiteDB->querySingle("select UserID from users where Email ='".$_POST["email"]."'");
            /// Wenn die Passwörter nicht übereinstimmen, wird eine dementsprechende Fehlermeldung ausgegeben mit einem Link zurück zum Login.
            } else {
                print("Die Passwörter stimmen nicht überein<br>");
                print("<a href='login.html'>Zurück zum login</a>");
            }

        }
    }
    // Nun wird noch der rest des Layouts ausgegeben.
    print("
                </div>
            </div>
        </div>
    ");
?>
