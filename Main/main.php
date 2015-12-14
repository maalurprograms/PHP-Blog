<html>
    <head>
        <title>Projektauftrag Formular</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../CSS/main_stylesheet.css">
        <link href='https://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src='https://cdn.tinymce.com/4/tinymce.min.js'></script>
        <script type="text/javascript" src="main_javascript.js"></script>
        <!-- bootstrap -->
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    </head>
    <body>
        <div  id="case">
            <div id="menu">
                <nav class="navbar">
                  <div>
                    <div>
                        <ul id="menu_bar" class="nav navbar-nav">
                            <li><a id="home" href="main.php">Home</a></li>
                            <li><a id="computer">Computer</a></li>
                            <li><a id="natur">Natur</a></li>
                            <li><a id="fotografie">Fotografie</a></li>
                            <li><a id="malerei">Malerei</a></li>
                            <li><a id="games">Games</a></li>
                            <li><a id="musik">Musik</a></li>
                            <li><a id="reisen">Reisen</a></li>
                            <?php
                                session_start();
                                print("<li id='logout'><a id='logout'>Abmelden</a></li>");
                                print("<li id='user_home'><a id='uhome'>".$_SESSION["USERNAME"]."</a></li>");
                            ?>
                        </ul>
                    </div>
                  </div>
                </nav>
            </div>
            <div id="content">
                <div class="content_div" id="welcome">
                    <h1>Wilkommen</h1>
                    <p>Hier sehen Sie alle Blogs:</p><br>
                </div>
                <?php
                    $db = new SQLite3('..\SQL\blog.db');

                    $result = $db->query("select Username from users");

                    $row = array();

                    $i = 0;

                     while($res = $result->fetchArray(SQLITE3_ASSOC)){
                         $row[$i] = $res['Username'];
                         $i++;
                     }
                     print("<div id='blogs'>");
                      for ($i=0; $i < count($row); $i++) {
                          if ($row[$i] != "Admin") {
                              print("<h2 class='getBlog' name='".$row[$i]."'>".$row[$i]."</h2> ");
                          }
                      }
                      print("</div>");
                ?>
            </div>
        </div>
    </body>
</html>
