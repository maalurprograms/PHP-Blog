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
                    <h1>Wilkommen auf diesem Blog</h1>
                    <p>Hier siehst du die 5 Blogs mit den besten Bewertungen:</p><br>
                </div>
                <?php
                    $db = new SQLite3('..\SQL\blog.db');

                    $result = $db->query("select ArticleID, Title, Username, Content, Rating from articles_themes
                        inner join articles on articles.ArticleID=articles_themes.IDArticle
                        inner join themes on themes.ThemeID=articles_themes.IDTheme
                        inner join users_articles on users_articles.IDArticle=articles.ArticleID
                        inner join users on users.UserID=users_articles.IDUser
                        order by Rating desc
                        limit 5");

                    $row = array();

                    $i = 0;

                     while($res = $result->fetchArray(SQLITE3_ASSOC)){

                         //if(!isset($res['user_id'])) continue;

                         $row[$i]['id'] = $res['ArticleID'];
                         $row[$i]['title'] = $res['Title'];
                         $row[$i]['username'] = $res['Username'];
                         $row[$i]["rating"] = $res["Rating"];
                         $row[$i]['content'] = $res['Content'];

                          $i++;

                      }
                      for ($i=0; $i < count($row); $i++) {
                          print("<div class='content_div'><h2 id='".$row[$i]['id']."' onclick='load_post(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                          print("<p>".$row[$i]['username']."<br>Bewertung: ".$row[$i]["rating"]."</p>");
                          $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                          print($out."</div>");
                      }
                ?>
            </div>
        </div>
    </body>
</html>
