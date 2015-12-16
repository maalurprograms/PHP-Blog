<script>
    tinyMCE.init({
            // General options
        mode : "exact",
        elements : "editor",
    });
    $(".getBlog").click(showBlog);
    $(".menu-elements").click(loadSubMenu);
    $(".delete-user").click(deleteUser);
</script>
<?php
    session_start();
    $clickedSubMenu = $_POST["submenu"];
    $SQLiteDB = new SQLite3('..\SQL\blog.db');
    switch ($clickedSubMenu) {
        case "article_creator":
            print("
                <p>Titel:</p>
                <p id='title_alarm' class='alarm'>Bitte geben Sie einen Titel ein.</p>
                <input id='article_title'><br><br>
                <p id='content_alarm' class='alarm'>Sie haben noch keinen Inhalt.</p>
                <textaera id='editor'/>
                <p>Kategorie:
                <select id='article_category'>
                    <option value='Computer'>Computer</option>
                    <option value='Natur'>Natur</option>
                    <option value='Fotografie'>Fotografie</option>
                    <option value='Malerei'>Malerei</option>
                    <option value='Games'>Games</option>
                    <option value='Musik'>Musik</option>
                    <option value='Reisen'>Reisen</option>
                </select></p><br>
                <button id='save-article-button' onclick='checkArticleData()'>Erstellen</button>
            ");
            break;

        case "edit_article":
            $title = $SQLiteDB->querySingle("select Title from articles where ArticleID = ".$_POST["article_id"]);
            $content = $SQLiteDB->querySingle("select Content from articles where ArticleID = ".$_POST["article_id"]);
            print("
                <h3 id='article' name=".$_POST["article_id"].">".$title.":</h3>
                <p id='content_alarm' class='alarm' style='display: none;'>Sie haben noch keinen Inhalt.</p>
                <textaera id='editor'>".$content."</textaera>
                <button id='save-article-button' onclick='checkArticleData()'>Speichern</button>
            ");
            break;

        case "add_article":
            $content = str_replace("%**%equals", "=", $_POST["article_content"]);
            $content = str_replace("%**%", "&", $content);
            $result = $SQLiteDB->querySingle("select exists(select Title from articles
                where Title = '".$_POST["article_title"]."')");
            if ($result) {
                print("
                    <div id='error' class='content-div'>
                        <p>
                            <h1>Dieser Titel ist schon vergeben.</h1><br>
                            Bitte Versuchen Sie es mit einem anderen Titel.
                        </p>
                    </div>
                ");
            } else {
                $SQLiteDB->exec("insert into articles (Title, Content) values ('".$_POST["article_title"]."','".$content."')");
                $resultaid = $SQLiteDB->querySingle("select ArticleID from articles
                    where Title = '".$_POST["article_title"]."'");
                $resulttid = $SQLiteDB->querySingle("select ThemeID from themes
                    where ThemeName = '".$_POST["article_category"]."'");
                $SQLiteDB->exec("insert into articles_themes (IDArticle, IDTheme) values ('".$resultaid."', '".$resulttid."')");
                $SQLiteDB->exec("insert into users_articles values('".$resultaid."', '".$_SESSION["USERID"]."')");

                print("
                <div class='content-div'>
                    <h1>Ihr Artikel wurde erstellt</h1>
                    Sie können diesen nun unter der von Ihnen angegebenen Kategorie oder in Ihrem Benutzerprofil abrufen
                ");
            }
            break;

        case "save_changes":
            $content = str_replace("%**%equals", "=", $_POST["article_content"]);
            $content = str_replace("%**%", "&", $content);
            $SQLiteDB->exec("update articles set Content = '".$content."' where ArticleID = ".$_POST["article_id"]);

            print("
            <div class='content-div'>
                <h1>Ihre Änderungen wurden gespeichert</h1>
                Sie können den Artikel nun unter der von Ihnen angegebenen Kategorie oder in Ihrem Benutzerprofil abrufen
            ");
            break;

        case "uhome":
            if ($_SESSION["EMAIL"] != "admin@gibb.ch") {
                print("<div class='content-div'><h1>".$_SESSION["USERNAME"]."</h1></div>");

                $result = $SQLiteDB->querySingle("select count(ArticleID) from users_articles
                  inner join articles on articles.ArticleID=users_articles.IDArticle
                  inner join users on users.UserID=users_articles.IDUser
                  where UserId = '".$_SESSION["USERID"]."'");

                print("
                  Posts: ".$result."<br>
                  <button id='article-creator-button' onclick='articleCreator()'>Post erstellen</button><br><br>
                  <h3>Meine Posts:</h3></div><br>
                ");

                $row = listArticles("UserId", $_SESSION["USERID"], "ArticleID", "desc", $SQLiteDB);
                for ($i=0; $i < count($row); $i++) {
                    print("<div class='content-div'><h2 id='".$row[$i]['id']."' onclick='showArticle(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                    $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                    print($out."</div>");
                }
            } else {
                $result = $SQLiteDB->query("select Email from users");
                $row = querySingleToArray($result, "Email");
                print("
                <div class='content-div'>
                    <h3 class='textalign-center'>Klicken Sie auf einen User um ihn zu löschen:</h3>
                </div>
                <div class='content-div'>
                    <p>
                ");
                for ($i=0; $i < count($row); $i++) {
                    if ($row[$i] != "admin@gibb.ch") {
                        $userID = $SQLiteDB->querySingle("select UserID from users where Email = '".$row[$i]."'");
                        print("<span name=".$userID." class='delete-user'>".$row[$i]."</span> ");
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
            $row = querySingleToArray($result, "IDArticle");
            for ($i=0; $i < count($row); $i++) {
                $SQLiteDB->exec("delete from articles where ArticleID = ".$row[$i]);
                $SQLiteDB->exec("delete from articles_themes where IDArticle = ".$row[$i]);
            }
            $SQLiteDB->exec("delete from users_articles where IDUser = ".$_POST["user_id"]);
            print("<div id='article-deleted' class='content-div'><h1>Der User wurde gelöscht</h1></div>");
            break;

        case "show_blog":
            $numbArticles = $SQLiteDB->querySingle("select count(ArticleID) from articles
            inner join users_articles on users_articles.IDArticle=articles.ArticleID
            inner join users on users.UserID=users_articles.IDUser
            where Username = '".$_POST["user"]."'");
            print("<div class='content-div'>
                    <h1>".$_POST["user"]."</h1>
                </div>
                <p>Anzahl Posts: ".$numbArticles."</p>");
            $row = listArticles("Username", $_POST["user"], "ArticleID", "desc", $SQLiteDB);
            for ($i=0; $i < count($row); $i++) {
                print("<div class='content-div'><h2 id='".$row[$i]['id']."' onclick='showArticle(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                print($out."</div>");
            }
            break;

        case "article":
            $clickedArticle = $_POST["id"];
            $result = $SQLiteDB->querySingle("select Title, Username, Content from articles_themes
                inner join articles on articles.ArticleID=articles_themes.IDArticle
                inner join themes on themes.ThemeID=articles_themes.IDTheme
                inner join users_articles on users_articles.IDArticle=articles.ArticleID
                inner join users on users.UserID=users_articles.IDUser
                where ArticleID = ".$clickedArticle, true);

            print("
                 <div class='content-div' id='article'>
                    <h2 id='show_content'>".$result["Title"]."</h2>
                    <p name='".$result["Username"]."' class='getBlog'>Blog: ".$result["Username"]."</p>
                    ".$result["Content"]."
                </div>
            ");
            $owner = $SQLiteDB->querySingle("select UserID from users
                inner join users_articles on users.UserID=users_articles.IDUser
                inner join articles on articles.ArticleID=users_articles.IDArticle
                where ArticleID = '".$clickedArticle."'");
            if ($_SESSION["USERID"] == $owner || $_SESSION["EMAIL"] == "admin@gibb.ch") {
                $tinymceContent = $SQLiteDB->querySingle("select Content from articles where ArticleID = ".$clickedArticle);
                print("<button id='edit-article-button' onclick='changeArticle(".'"'.$clickedArticle.'"'.", ".'"'.$tinymceContent.'"'.")'>Bearbeiten</button>");
                print("<button id='delete-button' onclick='deleteArticle(".'"'.$clickedArticle.'"'.")'>Löschen</button>");
            }
            break;

        case "delete_article":
            $SQLiteDB->exec("delete from articles where ArticleID = ".$_POST["article_id"]);
            $SQLiteDB->exec("delete from users_articles where IDArticle = ".$_POST["article_id"]);
            $SQLiteDB->exec("delete from articles_themes where IDArticle = ".$_POST["article_id"]);
            print("<div id='article-deleted' class='content-div'><h1>Ihr Post wurde gelöscht</h1></div>");
            break;

        case "home":
            print('
                <div class="content-div" id="welcome">
                    <h1>Wilkommen</h1>
                    <p>Hier sehen Sie alle Blogs:</p><br>
                </div>
            ');
            $result = $SQLiteDB->query("select Username from users");
            $row = querySingleToArray($result, "Username");
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
                        <ul id="menu-bar" class="nav navbar-nav">
                            <li><a id="home" href="main.html">Home</a></li>
            ');
            $result = $SQLiteDB->query("select ThemeName from themes");
            $menu = querySingleToArray($result, "ThemeName");
            for ($i=0; $i < count($menu); $i++) {
                print('<li><a class="menu-elements" id="'.strtolower($menu[$i]).'">'.$menu[$i].'</a></li>');
            }
            print('
                            <li id="logout"><a class="menu-elements" id="logout">Abmelden</a></li>
                            <li id="user-home"><a class="menu-elements" id="uhome">'.$_SESSION["USERNAME"].'</a></li>
                        </ul>
                    </div>
                  </div>
                </nav>
            ');
            break;

        default:
            $row = listArticles("ThemeName", $clickedSubMenu, "ArticleID", "desc", $SQLiteDB);
            if (count($row)) {
                for ($i=0; $i < count($row); $i++) {
                    print("<div class='content-div'><h2 id='".$row[$i]['id']."' onclick='showArticle(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                    print("<p name='".$row[$i]['username']."' class='getBlog'>Blog: ".$row[$i]['username']."<br></p><br>");
                    $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                    print($out."</div>");
                }
            } else {
                print("<div id='void-category' class='content-div'><h3>In dieser Kategorie gibt es noch keine Posts.</h3></div>");
            }
    }

    function querySingleToArray($queryResult, $rowName){
        $result = array();
        $i = 0;
        while($row = $queryResult->fetchArray(SQLITE3_ASSOC)){
            $result[$i] = $row[$rowName];
            $i++;
        }
        return $result;
    }

    function listArticles($searchedRow, $searchedColumn, $orderByRow, $orderType, $SQLiteDB) {
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
