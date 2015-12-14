<html>
    <head>
        <title>Projektauftrag Formular</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../CSS/login_stylesheet.css">
        <link href='https://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script type="text/javascript" src="login_javascript.js"></script>
        <!-- bootstrap -->
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    </head>
    <body>
        <div id="case">
            <div id="header">
                Information
            </div>
            <div id="content">
              <!-- -->
              <?php
                $db = new SQLite3("..\SQL\blog.db");
                $error = "Email/Passwort ungültig<br>";
                $back_to_login = "<a href='login.html'>Zurück zum login</a>";
                if (count($_POST) < 3) {
                    $result = $db->querySingle("select exists(select * from users where Email = '".$_POST["email"]."')");
                    if (!$result) {
                        print($error);
                        print($back_to_login);
                    } else {
                        $result = $db->querySingle("select Password from users where Email = '".$_POST["email"]."'");
                        if (password_verify($_POST["password"], $result)) {
                            print("<a href='../Main/main.html'>Hier gehts zur Homepage</a>");
                            session_start();
                            $_SESSION["EMAIL"] = $_POST["email"];
                            $_SESSION["USERNAME"] = $db->querySingle("select Username from users where Email ='".$_POST["email"]."'");
                            $_SESSION["USERID"] = $db->querySingle("select UserID from users where Email ='".$_POST["email"]."'");
                        } else {
                            print($error);
                            print($back_to_login);
                        }
                    }
                } else {
                    $result = $db->querySingle("select exists(select * from users where Email = '".$_POST["email"]."')");
                    if ($result) {
                        print("Diesen User gibt es schon<br>");
                        print("<a href='login.html'>Zurück zum login</a>");
                    } else {
                        if ($_POST["password"] == $_POST["password_retype"]) {
                            $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
                            $db->exec("insert into users (Email, Username, Password) values ('".$_POST["email"]."', '".$_POST["username"]."','".$hash."')");
                            print("Vielen Dank<br>");
                            print("<a href='..\Main\main.html'>Hier gehts zur Homepage</a>");
                            session_start();
                            $_SESSION["EMAIL"] = $_POST["email"];
                            $_SESSION["USERNAME"] = $_POST["username"];
                            $_SESSION["USERID"] = $db->querySingle("select UserID from users where Email ='".$_POST["email"]."'");
                        } else {
                            print("Die Passwörter stimmen nicht überein<br>");
                            print("<a href='login.html'>Zurück zum login</a>");
                        }

                    }
                }
              ?>
              <!-- -->
          </div>
        </div>
    </body>
</html>
