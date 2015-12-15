<?php
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
    if (count($_POST) < 3) {
        $result = $SQLiteDB->querySingle("select exists(select * from users where Email = '".$_POST["email"]."')");
        if (!$result) {
            print($error);
            print($linkToLogin);
        } else {
            $result = $SQLiteDB->querySingle("select Password from users where Email = '".$_POST["email"]."'");
            if (password_verify($_POST["password"], $result)) {
                print("<a href='../Main/main.html'>Hier gehts zur Homepage</a>");
                session_start();
                $_SESSION["EMAIL"] = $_POST["email"];
                $_SESSION["USERNAME"] = $SQLiteDB->querySingle("select Username from users where Email ='".$_POST["email"]."'");
                $_SESSION["USERID"] = $SQLiteDB->querySingle("select UserID from users where Email ='".$_POST["email"]."'");
            } else {
                print($error);
                print($linkToLogin);
            }
        }
    } else {
        $result = $SQLiteDB->querySingle("select exists(select * from users where Email = '".$_POST["email"]."')");
        if ($result) {
            print("Diesen User gibt es schon<br>");
            print("<a href='login.html'>Zurück zum login</a>");
        } else {
            if ($_POST["password"] == $_POST["password_retype"]) {
                $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
                $SQLiteDB->exec("insert into users (Email, Username, Password) values ('".$_POST["email"]."', '".$_POST["username"]."','".$hash."')");
                print("Vielen Dank<br>");
                print("<a href='..\Main\main.html'>Hier gehts zur Homepage</a>");
                session_start();
                $_SESSION["EMAIL"] = $_POST["email"];
                $_SESSION["USERNAME"] = $_POST["username"];
                $_SESSION["USERID"] = $SQLiteDB->querySingle("select UserID from users where Email ='".$_POST["email"]."'");
            } else {
                print("Die Passwörter stimmen nicht überein<br>");
                print("<a href='login.html'>Zurück zum login</a>");
            }

        }
    }
        print("
                    </div>
                </div>
            </div>
        ");
?>
