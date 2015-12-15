<script>
    tinyMCE.init({
            // General options
        mode : "exact",
        elements : "editor",
    });
    $(".getBlog").click(showBlog);
    $(".menu_elements").click(loadSubMenu);
    $(".delete_user").click(deleteUser);
</script>
<?php
    session_start();
    $clickedSubMenu = $_POST["submenu"];
    $SQLiteDB = new SQLite3('..\SQL\blog.db');
    switch ($clickedSubMenu) {
        case "post_creator":
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
                <button id='change_post_button' onclick='addPost()'>Erstellen</button>
        ");
        break;

        case "update_post":
            $title = $SQLiteDB->querySingle("select Title from articles where ArticleID = ".$_POST["article_id"]);
            $content = $SQLiteDB->querySingle("select Content from articles where ArticleID = ".$_POST["article_id"]);
            print("
                <p>".$title.":</p>
                <textaera id='editor'>".$content."</textaera>
                <button id='add_post_button' onclick='saveChanges(".'"'.$_POST["article_id"].'"'.")'>Speichern</button>
            ");
            break;

        case "add_post":
            $content = str_replace("%**%equals", "=", $_POST["post_content"]);
            $content = str_replace("%**%", "&", $content);
            $result = $SQLiteDB->querySingle("select exists(select Title from articles
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
                $SQLiteDB->exec("insert into articles (Title, Content) values ('".$_POST["post_title"]."','".$content."')");
                $resultaid = $SQLiteDB->querySingle("select ArticleID from articles
                    where Title = '".$_POST["post_title"]."'");
                $resulttid = $SQLiteDB->querySingle("select ThemeID from themes
                    where ThemeName = '".$_POST["post_category"]."'");
                $SQLiteDB->exec("insert into articles_themes (IDArticle, IDTheme) values ('".$resultaid."', '".$resulttid."')");
                $SQLiteDB->exec("insert into users_articles values('".$resultaid."', '".$_SESSION["USERID"]."')");

                print("
                <div class='content_div'>
                    <h1>Ihr Artikel wurde erstellt</h1>
                    Sie können diesen nun unter der von Ihnen angegebenen Kategorie oder in Ihrem Benutzerprofil abrufen
                ");
            }
            break;

        case "save_changes":
            $content = str_replace("%**%equals", "=", $_POST["post_content"]);
            $content = str_replace("%**%", "&", $content);
            $SQLiteDB->exec("update articles set Content = '".$content."' where ArticleID = ".$_POST["article_id"]);

            print("
            <div class='content_div'>
                <h1>Ihre Änderungen wurden gespeichert</h1>
                Sie können den Artikel nun unter der von Ihnen angegebenen Kategorie oder in Ihrem Benutzerprofil abrufen
            ");
            break;

        case "uhome":
            if ($_SESSION["EMAIL"] != "admin@gibb.ch") {
                print("<div class='content_div'><h1>".$_SESSION["USERNAME"]."</h1></div>");

                $result = $SQLiteDB->querySingle("select count(ArticleID) from users_articles
                  inner join articles on articles.ArticleID=users_articles.IDArticle
                  inner join users on users.UserID=users_articles.IDUser
                  where UserId = '".$_SESSION["USERID"]."'");

                print("
                  Posts: ".$result."<br>
                  <button id='post_creator_button' onclick='postCreator()'>Post erstellen</button><br><br>
                  <h3>Meine Posts:</h3></div><br>
                ");

                $row = listPosts("UserId", $_SESSION["USERID"], "ArticleID", "desc", $SQLiteDB);
                for ($i=0; $i < count($row); $i++) {
                    print("<div class='content_div'><h2 id='".$row[$i]['id']."' onclick='showPost(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                    $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                    print($out."</div>");
                }
            } else {
                $result = $SQLiteDB->query("select Email from users");
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
                        $userID = $SQLiteDB->querySingle("select UserID from users where Email = '".$row[$i]."'");
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
            $SQLiteDB->exec("delete from users where UserID = ".$_POST["user_id"]);
            $result = $SQLiteDB->query("select IDArticle from users_articles where IDUser = ".$_POST["user_id"]);
            $row = array();
            $i = 0;
            while($res = $result->fetchArray(SQLITE3_ASSOC)){
                $row[$i] = $res['IDArticle'];
                $i++;
            }
            for ($i=0; $i < count($row); $i++) {
                $SQLiteDB->exec("delete from articles where ArticleID = ".$row[$i]);
                $SQLiteDB->exec("delete from articles_themes where IDArticle = ".$row[$i]);
            }
            $SQLiteDB->exec("delete from users_articles where IDUser = ".$_POST["user_id"]);
            print("<div id='post_deleted' class='content_div'><h1>Der User wurde gelöscht</h1></div>");
            break;

        case "show_blog":
            $anzPosts = $SQLiteDB->querySingle("select count(ArticleID) from articles
            inner join users_articles on users_articles.IDArticle=articles.ArticleID
            inner join users on users.UserID=users_articles.IDUser
            where Username = '".$_POST["user"]."'");
            print("<div class='content_div'>
                    <h1>".$_POST["user"]."</h1>
                </div>
                <p>Anzahl Posts: ".$anzPosts."</p>");
            $row = listPosts("Username", $_POST["user"], "ArticleID", "desc", $SQLiteDB);
            for ($i=0; $i < count($row); $i++) {
                print("<div class='content_div'><h2 id='".$row[$i]['id']."' onclick='showPost(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                print($out."</div>");
            }
            break;

        case "post":
            $clickedPost = $_POST["id"];
            $result = $SQLiteDB->querySingle("select Title, Username, Content from articles_themes
                inner join articles on articles.ArticleID=articles_themes.IDArticle
                inner join themes on themes.ThemeID=articles_themes.IDTheme
                inner join users_articles on users_articles.IDArticle=articles.ArticleID
                inner join users on users.UserID=users_articles.IDUser
                where ArticleID = ".$clickedPost, true);

            print("
                 <div class='content_div' id='post'>
                    <h2 id='show_content'>".$result["Title"]."</h2>
                    <p name='".$result["Username"]."' class='getBlog'>Blog: ".$result["Username"]."</p>
                    ".$result["Content"]."
                </div>
            ");
            $owner = $SQLiteDB->querySingle("select UserID from users
                inner join users_articles on users.UserID=users_articles.IDUser
                inner join articles on articles.ArticleID=users_articles.IDArticle
                where ArticleID = '".$clickedPost."'");
            if ($_SESSION["USERID"] == $owner || $_SESSION["EMAIL"] == "admin@gibb.ch") {
                $tinymceContent = $SQLiteDB->querySingle("select Content from articles where ArticleID = ".$clickedPost);
                print("<button id='change_button' onclick='changePost(".'"'.$clickedPost.'"'.", ".'"'.$tinymceContent.'"'.")'>Bearbeiten</button>");
                print("<button id='delete_button' onclick='deletePost(".'"'.$clickedPost.'"'.")'>Löschen</button>");
            }
            break;

        case "delete_post":
            $SQLiteDB->exec("delete from articles where ArticleID = ".$_POST["article_id"]);
            $SQLiteDB->exec("delete from users_articles where IDArticle = ".$_POST["article_id"]);
            $SQLiteDB->exec("delete from articles_themes where IDArticle = ".$_POST["article_id"]);
            print("<div id='post_deleted' class='content_div'><h1>Ihr Post wurde gelöscht</h1></div>");
            break;

        case "home":
            print('
                <div class="content_div" id="welcome">
                    <h1>Wilkommen</h1>
                    <p>Hier sehen Sie alle Blogs:</p><br>
                </div>
            ');
            $result = $SQLiteDB->query("select Username from users");
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
            $result = $SQLiteDB->query("select ThemeName from themes");
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
            $row = listPosts("ThemeName", $clickedSubMenu, "ArticleID", "desc", $SQLiteDB);
            if (count($row)) {
                for ($i=0; $i < count($row); $i++) {
                    print("<div class='content_div'><h2 id='".$row[$i]['id']."' onclick='showPost(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                    print("<p name='".$row[$i]['username']."' class='getBlog'>Blog: ".$row[$i]['username']."<br></p><br>");
                    $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                    print($out."</div>");
                }
            } else {
                print("<div id='void_category' class='content_div'><h3>In dieser Kategorie gibt es noch keine Posts.</h3></div>");
            }
    }

    function listPosts($searchedRow, $searchedColumn, $orderByRow, $orderType, $SQLiteDB) {
      $result = $SQLiteDB->query("select ArticleID, Title, Username, Content from articles_themes
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
