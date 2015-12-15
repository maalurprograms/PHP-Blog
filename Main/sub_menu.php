<script>
    tinyMCE.init({
            // General options
        mode : "exact",
        elements : "editor",
        setup : function(ed) {
            ed.on("keyup", checkPostData);
        }
    });
    $(".getBlog").click(showBlog);
    $(".menu_elements").click(load_site);
    $(".delete_user").click(deleteUser);
</script>
<?php
    session_start();
    $clicked_sub_menu = $_POST["submenu"];
    $db = new SQLite3('..\SQL\blog.db');
    switch ($clicked_sub_menu) {
        case "create_post":
            print("
                <p>Titel:</p>
                <p id='title_alarm' class='alarm'>Bitte geben Sie einen Titel ein.</p>
                <input id='post_title' onkeyup='checkPostData()'><br><br>
                <p id='content_alarm' class='alarm'>Sie haben noch keinen Inhalt.</p>
                <textaera id='editor'/>
                <p>Kategorie:
                <select id='post_category'>
                   <option value='Computer'>Computer</option>
                   <option value='Natur'>Natur</option>
                   <option value='Fotografie'>Fotografie</option>
                   <option value='Malerei'>Malerei</option>
                   <option value='Games'>Games</option>
                   <option value='Musik'>Musik</option>
                   <option value='Reisen'>Reisen</option>
                </select></p><br>
                <button id='add_post_button' onclick='add_post()' style='display: none'>Erstellen</button>
        ");
        break;

        case "add_post":
            $content = str_replace("%**%equals", "=", $_POST["post_content"]);
            $content = str_replace("%**%", "&", $content);
            $result = $db->querySingle("select exists(select Title from articles
                where Title = '".$_POST["post_title"]."')");
            if ($result) {
                print("
                    <div id='error' class='content_div'>
                        <p>
                            <h1>Dieser Titel ist schon vergeben.</h1><br>
                            Bitte Versuchen Sie es mit einem anderen Titel.
                        </p>
                    </div>
                ");
            } else {
                $db->exec("insert into articles (Title, Content) values ('".$_POST["post_title"]."','".$content."')");
                $resultaid = $db->querySingle("select ArticleID from articles
                    where Title = '".$_POST["post_title"]."'");
                $resulttid = $db->querySingle("select ThemeID from themes
                    where ThemeName = '".$_POST["post_category"]."'");
                $db->exec("insert into articles_themes (IDArticle, IDTheme) values ('".$resultaid."', '".$resulttid."')");
                $db->exec("insert into users_articles values('".$resultaid."', '".$_SESSION["USERID"]."')");

                print("
                <div class='content_div'>
                    <h1>Ihr Artikel wurde erstellt</h1>
                    Sie können diesen nun unter der von Ihnen angegebenen Kategorie oder in Ihrem Benutzerprofil abrufen
                ");
            }
            break;

        case "uhome":
            if ($_SESSION["EMAIL"] != "admin@gibb.ch") {
                print("<div class='content_div'><h1>".$_SESSION["USERNAME"]."</h1></div>");

                $result = $db->querySingle("select count(ArticleID) from users_articles
                  inner join articles on articles.ArticleID=users_articles.IDArticle
                  inner join users on users.UserID=users_articles.IDUser
                  where UserId = '".$_SESSION["USERID"]."'");

                print("
                  Posts: ".$result."<br>
                  <button id='create_post_button' onclick='create_post()'>Post erstellen</button><br><br>
                  <h3>Meine Posts:</h3></div><br>
                ");

                $row = listPosts("UserId", $_SESSION["USERID"], "ArticleID", "desc", $db);
                for ($i=0; $i < count($row); $i++) {
                    print("<div class='content_div'><h2 id='".$row[$i]['id']."' onclick='load_post(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                    $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                    print($out."</div>");
                }
            } else {
                $result = $db->query("select Email from users");
                $row = array();
                $i = 0;
                while($res = $result->fetchArray(SQLITE3_ASSOC)){
                    $row[$i] = $res['Email'];
                    $i++;
                }
                print("
                <div class='content_div'>
                    <h3 class='textalign_center'>Klicken Sie auf einen User um ihn zu löschen:</h3>
                </div>
                <div class='content_div'>
                    <p>
                ");
                for ($i=0; $i < count($row); $i++) {
                    if ($row[$i] != "admin@gibb.ch") {
                        $userID = $db->querySingle("select UserID from users where Email = '".$row[$i]."'");
                        print("<span name=".$userID." class='delete_user'>".$row[$i]."</span> ");
                    }
                }
                print("
                    <p>
                </div>
                ");
            }
            break;

        case "delete_user":
            $db->exec("delete from users where UserID = ".$_POST["userid"]);
            $result = $db->query("select IDArticle from users_articles where IDUser = ".$_POST["userid"]);
            $row = array();
            $i = 0;
            while($res = $result->fetchArray(SQLITE3_ASSOC)){
                $row[$i] = $res['IDArticle'];
                $i++;
            }
            for ($i=0; $i < count($row); $i++) {
                $db->exec("delete from articles where ArticleID = ".$row[$i]);
                $db->exec("delete from articles_themes where IDArticle = ".$row[$i]);
            }
            $db->exec("delete from users_articles where IDUser = ".$_POST["userid"]);
            print("<div id='post_deleted' class='content_div'><h1>Der User wurde gelöscht</h1></div>");
            break;

        case "show_blog":
            $anzPosts = $db->querySingle("select count(ArticleID) from articles
            inner join users_articles on users_articles.IDArticle=articles.ArticleID
            inner join users on users.UserID=users_articles.IDUser
            where Username = '".$_POST["user"]."'");
            print("<div class='content_div'>
                    <h1>".$_POST["user"]."</h1>
                </div>
                <p>Anzahl Posts: ".$anzPosts."</p>");
            $row = listPosts("Username", $_POST["user"], "ArticleID", "desc", $db);
            for ($i=0; $i < count($row); $i++) {
                print("<div class='content_div'><h2 id='".$row[$i]['id']."' onclick='load_post(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                print($out."</div>");
            }
            break;

        case "post":
            $clicked_post = $_POST["id"];
            $result = $db->querySingle("select Title, Username, Content from articles_themes
                inner join articles on articles.ArticleID=articles_themes.IDArticle
                inner join themes on themes.ThemeID=articles_themes.IDTheme
                inner join users_articles on users_articles.IDArticle=articles.ArticleID
                inner join users on users.UserID=users_articles.IDUser
                where ArticleID = ".$clicked_post, true);

            print("
                 <div class='content_div' id='post'>
                    <h2 id='show_content'>".$result["Title"]."</h2>
                    <p name='".$result["Username"]."' class='getBlog'>Blog: ".$result["Username"]."</p>
                    ".$result["Content"]."
                </div>
            ");
            $owner = $db->querySingle("select UserID from users
                inner join users_articles on users.UserID=users_articles.IDUser
                inner join articles on articles.ArticleID=users_articles.IDArticle
                where ArticleID = '".$clicked_post."'");
            if ($_SESSION["USERID"] == $owner || $_SESSION["EMAIL"] == "admin@gibb.ch") {
                print("<button id='delete_button' onclick='deletePost(".'"'.$clicked_post.'"'.")'>Löschen</button>");
            }
            break;

        case "delete_post":
            $db->exec("delete from articles where ArticleID = ".$_POST["postid"]);
            $db->exec("delete from users_articles where IDArticle = ".$_POST["postid"]);
            $db->exec("delete from articles_themes where IDArticle = ".$_POST["postid"]);
            print("<div id='post_deleted' class='content_div'><h1>Ihr Post wurde gelöscht</h1></div>");
            break;

        case "home":
            print('
                <div class="content_div" id="welcome">
                    <h1>Wilkommen</h1>
                    <p>Hier sehen Sie alle Blogs:</p><br>
                </div>
            ');
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
            break;

        case "home_menu":
            print('
                <nav class="navbar">
                  <div>
                    <div>
                        <ul id="menu_bar" class="nav navbar-nav">
                            <li><a id="home" href="main.html">Home</a></li>
            ');
            $result = $db->query("select ThemeName from themes");
            $menu = array();
            $i = 0;
            while($res = $result->fetchArray(SQLITE3_ASSOC)){
                $menu[$i] = $res['ThemeName'];
                $i++;
            }
            for ($i=0; $i < count($menu); $i++) {
                print('<li><a class="menu_elements" id="'.strtolower($menu[$i]).'">'.$menu[$i].'</a></li>');
            }
            print('
                            <li id="logout"><a class="menu_elements" id="logout">Abmelden</a></li>
                            <li id="user_home"><a class="menu_elements" id="uhome">'.$_SESSION["USERNAME"].'</a></li>
                        </ul>
                    </div>
                  </div>
                </nav>
            ');
            break;

        default:
            $row = listPosts("ThemeName", $clicked_sub_menu, "ArticleID", "desc", $db);
            if (count($row)) {
                for ($i=0; $i < count($row); $i++) {
                    print("<div class='content_div'><h2 id='".$row[$i]['id']."' onclick='load_post(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                    print("<p name='".$row[$i]['username']."' class='getBlog'>Blog: ".$row[$i]['username']."<br></p><br>");
                    $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                    print($out."</div>");
                }
            } else {
                print("<div id='void_category' class='content_div'><h3>In dieser Kategorie gibt es noch keine Posts.</h3></div>");
            }
    }

    function listPosts($searchedRow, $searchedColumn, $orderByRow, $orderType, $db) {
      $result = $db->query("select ArticleID, Title, Username, Content from articles_themes
          inner join articles on articles.ArticleID=articles_themes.IDArticle
          inner join themes on themes.ThemeID=articles_themes.IDTheme
          inner join users_articles on users_articles.IDArticle=articles.ArticleID
          inner join users on users.UserID=users_articles.IDUser
          where lower(".$searchedRow.") = lower('".$searchedColumn."')
          order by ".$orderByRow." ".$orderType."");

      $row = array();

      $i = 0;

       while($res = $result->fetchArray(SQLITE3_ASSOC)){

           $row[$i]['id'] = $res['ArticleID'];
           $row[$i]['title'] = $res['Title'];
           $row[$i]['username'] = $res['Username'];
           $row[$i]['content'] = $res['Content'];

            $i++;

        }
        return $row;
    }
?>
