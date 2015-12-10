<script>
    tinyMCE.init({
            // General options
        mode : "exact",
        elements : "editor",
        setup : function(ed) {
            ed.on("keyup", checkPostData);
        }
    });
</script>
<?php
    session_start();
    $clicked_sub_menu = $_POST["submenu"];
    $db = new SQLite3('..\SQL\blog.db');
    if ($clicked_sub_menu == "create_post") {

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

    } elseif ($clicked_sub_menu == "add_post") {
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
                Sie können Ihn nun unter der von Ihnen angegebenen Kategorie oder in Ihrem Benutzerprofil abrufen
            ");
        }

    } elseif ($clicked_sub_menu == "uhome") {

          print("<div class='content_div'><h1>".$_SESSION["USERNAME"]."</h1></div>");

          $result = $db->querySingle("select sum(Rating) from users_articles
              inner join articles on articles.ArticleID=users_articles.IDArticle
              inner join users on users.UserID=users_articles.IDUser
              where UserId = '".$_SESSION["USERID"]."'");

          print("<div class='content_div'>Gesammte Bewertung: ".$result."<br>");

          $result = $db->querySingle("select count(ArticleID) from users_articles
              inner join articles on articles.ArticleID=users_articles.IDArticle
              inner join users on users.UserID=users_articles.IDUser
              where UserId = '".$_SESSION["USERID"]."'");

          print("
              Posts: ".$result."<br>
              <button id='create_post_button' onclick='create_post()'>Post erstellen</button><br><br>
              <h3>Meine Posts:</h3></div><br>
          ");

          $result = $db->query("select ArticleID, Title, Rating, Content from articles_themes
              inner join articles on articles.ArticleID=articles_themes.IDArticle
              inner join themes on themes.ThemeID=articles_themes.IDTheme
              inner join users_articles on users_articles.IDArticle=articles.ArticleID
              inner join users on users.UserID=users_articles.IDUser
              where UserId = '".$_SESSION["USERID"]."'
              order by ArticleID desc");

          $row = array();

          $i = 0;

           while($res = $result->fetchArray(SQLITE3_ASSOC)){

               $row[$i]['id'] = $res['ArticleID'];
               $row[$i]['title'] = $res['Title'];
               $row[$i]["rating"] = $res["Rating"];
               $row[$i]['content'] = $res['Content'];

                $i++;

            }
            for ($i=0; $i < count($row); $i++) {
                print("<div class='content_div'><h2 id='".$row[$i]['id']."' onclick='load_post(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                print("<p>Bewertung: ".$row[$i]["rating"]."</p>");
                $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                print($out."</div>");
            }
      } else {
          $result = $db->query("select ArticleID, Title, Username, Rating, Content from articles_themes
              inner join articles on articles.ArticleID=articles_themes.IDArticle
              inner join themes on themes.ThemeID=articles_themes.IDTheme
              inner join users_articles on users_articles.IDArticle=articles.ArticleID
              inner join users on users.UserID=users_articles.IDUser
              where lower(ThemeName) = '".$clicked_sub_menu."'
              order by ArticleID desc");

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
                print("<p>".$row[$i]['username']."<br>Bewertung: ".$row[$i]["rating"]."</p><br>");
                $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                print($out."</div>");
            }
      }
?>
