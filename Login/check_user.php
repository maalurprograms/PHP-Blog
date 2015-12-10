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
                $error = "Username/Passwort ungültig<br>";
                $back_to_login = "<a href='login.html'>Zurück zum login</a>";
                if (count($_POST) < 3) {
                    $result = $db->querySingle("select exists(select * from users where Username = '".$_POST["username"]."')");
                    if (!$result) {
                        print($error);
                        print($back_to_login);
                    } else {
                        $result = $db->querySingle("select Password from users where Username = '".$_POST["username"]."'");
                        if ($result == $_POST["password"]) {
                            print("Wilkommen ".$_POST["username"]."<br>");
                            print("<a href='../Main/main.php'>Hier gehts zur Homepage</a>");
                            session_start();
                            $_SESSION["USERNAME"] = $_POST["username"];
                            $_SESSION["USERID"] = $db->querySingle("select UserID from users where Username ='".$_POST["username"]."'");
                        } else {
                            print($error);
                            print($back_to_login);
                        }
                    }
                } else {
                    $result = $db->querySingle("select exists(select * from users where Username = '".$_POST["username"]."')");
                    if ($result) {
                        print("Diesen User gibt es schon<br>");
                        print("<a href='login.html'>Zurück zum login</a>");
                    } else {
                        if ($_POST["password"] == $_POST["password_retype"]) {
                            $db->exec("insert into users (Username, Password) values ('".$_POST["username"]."','".$_POST["password"]."')");
                            print("Vielen Dank für deine Registration<br>");
                            print("<a href='..\Main\main.php'>Hier kommst du zur Homepage</a>");
                            session_start();
                            $_SESSION["USERNAME"] = $_POST["username"];
                            $_SESSION["USERID"] = $db->querySingle("select UserID from users where Username ='".$_POST["username"]."'");
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
