<script>
    // Da dieses PHP immer dynamisch in das main.html geladen wird,
    // müssen die Konfigurationen für Elemente die erst erstellt werden wenn dieses File aufgerufen wird, hier gemacht werden.
    // Wenn sie im Javascript wären würde zum Beispiel einen click-Handler für ein Element erstellt das noch nicht existiert.

    // Der Web-EditorTinyMCE wird konfiguriert und das Element das durch den Editor ersezt wird (editor) wird festgelegt.
    tinyMCE.init({
            // General options
        mode : "exact",
        elements : "editor",
    });
    // Hier werden click-Eventhandler definiert für:
    //  - Wenn jemand auf den Link zu einem Blog klickt.
    //  - Wenn jemand auf ein Menu klickt.
    //  - Wenn der Admin auf einen User klickt um ihn zu löschen.
    $(".getBlog").click(showBlog);
    $(".menu-elements").click(loadSubMenu);
    $(".delete-user").click(deleteUser);
</script>
<?php
    // Die Session wird gestartet und damit werden die Variabeln die im Login gesetzt worden sin übernommen.
    session_start();
    // Dann wird das submenu gsesetzt. $_POST inst eine vordefinierte Variabel von PHP.
    // Sie beinhaltet die Daten die an den Server geschickt wurden. z.B. per Submit-Form oder per AJAX.
    $clickedSubMenu = $_POST["submenu"];
    // Es wird die DB geöffnet.
    $SQLiteDB = new SQLite3('..\SQL\blog.db');
    // In dieser Switch-Anweisung, wird bestummen, was jetzt auf eine Anfrage vom Javascriptfile her zurückgesendet wird.
    // Im Javascript wird bei jeder Anfrage noch "sub_menu=beispiel" mitgegeben. Aufgrund von dem wird nun bestummen was gesendet wird.
    switch ($clickedSubMenu) {
        case "article_creator":
            // Wenn submenu article_creator ist, wird die Seite aufgebaut, in der ein Post beareitet werden kann.
            // Es wird das Titel-Inputfeld, das Inhaltsfeld, das durch den Editor ersetzt wird und die Kategorieasuwahl gesendet.
            // Mit einem click auf den Button wird de Methode checkArticleData() aufgerufen.
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
            // Wenn submenu edit_article ist, wird eine Seite zum editieren von Artikel gebaut.
            // Es wird in der Datenbank mit querySingle (querySingle giebt nur ein Wert zurück, nur query gibt ein objekt zurück)
            // nach dem Titel und Inhalt des Artikels mit der ArtikelID $_POST["article_id"] gesucht.
            $title = $SQLiteDB->querySingle("select Title from articles where ArticleID = ".$_POST["article_id"]);
            $content = $SQLiteDB->querySingle("select Content from articles where ArticleID = ".$_POST["article_id"]);
            // Danach wird der Artikeltitel und der Editor , mit dem aktuellen Artikelinhalt, angezeigt.
            // Bei klicken auf den Button, wird die Javascriptmethode checkArticleData aufgerufen.
            print("
                <h3 id='article-title' name=".$_POST["article_id"].">".$title.":</h3>
                <p id='content_alarm' class='alarm' style='display: none;'>Sie haben noch keinen Inhalt.</p>
                <textaera id='editor'>".$content."</textaera>
                <button id='save-article-button' onclick='checkArticleData()'>Speichern</button>
            ");
            break;

        case "add_article":
            // Wenn submenu add_article ist, wird eine Seite generiert auf der ein Artikel erstellt werden kann.
            // Als erstes werden die Zeichen %**%equals und "%**%" durch "=" und "&" ersetzt.
            // Diese wurden im Javascript so ersetzt damit die Zeichen "&" und "=" der POST-Methode keine schwierigkeiten machen.
            $content = str_replace("%**%equals", "=", $_POST["article_content"]);
            $content = str_replace("%**%", "&", $content);
            // Dann wird geprüft ob der Titel schon vergeben ist.
            $result = $SQLiteDB->querySingle("select exists(select Title from articles
                where Title = '".$_POST["article_title"]."')");
            // Wenn ja, wird eine Fehlermeldung ausgegeben.
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
                // Wenn der Titel noch nicht vergeben ist, werden die Daten in die Datenbank eingefügt und die ervorderlichen verknüpfungen gemacht.
                $SQLiteDB->exec("insert into articles (Title, Content) values ('".$_POST["article_title"]."','".$content."')");
                $resultaid = $SQLiteDB->querySingle("select ArticleID from articles
                    where Title = '".$_POST["article_title"]."'");
                $resulttid = $SQLiteDB->querySingle("select ThemeID from themes
                    where ThemeName = '".$_POST["article_category"]."'");
                $SQLiteDB->exec("insert into articles_themes (IDArticle, IDTheme) values ('".$resultaid."', '".$resulttid."')");
                $SQLiteDB->exec("insert into users_articles values('".$resultaid."', '".$_SESSION["USERID"]."')");
                // Danach wird eine Iformation ausgegeben.
                print("
                <div class='content-div'>
                    <h1>Ihr Artikel wurde erstellt</h1>
                    Sie können diesen nun unter der von Ihnen angegebenen Kategorie oder in Ihrem Benutzerprofil abrufen
                ");
            }
            break;

        case "save_changes":
            // Wenn submenu save_changes ist, heisst das das ein Artikel geändert wurde.
            // Als erstes werden die Zeichen %**%equals und "%**%" durch "=" und "&" ersetzt.
            // Diese wurden im Javascript so ersetzt damit die Zeichen "&" und "=" der POST-Methode keine schwierigkeiten machen.
            $content = str_replace("%**%equals", "=", $_POST["article_content"]);
            $content = str_replace("%**%", "&", $content);
            // Dann wird der Artikel mit der ID $_POST["article_id"] geändert und es wird der neue Inhalt eingefügt.
            $SQLiteDB->exec("update articles set Content = '".$content."' where ArticleID = ".$_POST["article_id"]);
            // Es wird eine Information ausgegeben.
            print("
            <div class='content-div'>
                <h1>Ihre Änderungen wurden gespeichert</h1>
                Sie können den Artikel nun unter der von Ihnen angegebenen Kategorie oder in Ihrem Benutzerprofil abrufen
            ");
            break;

        case "uhome":
            // Wenn submenu uhome ist, hat ein user im Menu auf seinen Usernamen geklickt. (Userübersicht oben rechts im Menu)
            // Nun wird überprüft ob der user Admin ist.
            if ($_SESSION["EMAIL"] != "admin@gibb.ch") {
                // Wenn nicht, wird das normale Userhome geladen. Heisst, Ein Übersicht all seiner Artikel sowie wie viele Artikel der User erstellt hat,
                // sowie einen Button um neue Artikel zu erstellen.
                print("<div class='content-div'><h1>".$_SESSION["USERNAME"]."</h1></div>");
                // Hier werden gezählt wieviele Artikel der User mit der ID $_SESSION["USERID"] hat.
                $result = $SQLiteDB->querySingle("select count(ArticleID) from users_articles
                    inner join articles on articles.ArticleID=users_articles.IDArticle
                    inner join users on users.UserID=users_articles.IDUser
                    where UserId = '".$_SESSION["USERID"]."'");
                // Hier wird nun die Anzahl Artikel sowie der Button zum Erstellen ausgegeben.
                // Bei clicken auf den Button wird die Javascriptmethode articleCreator() aufgerufen.
                print("
                    Posts: ".$result."<br>
                    <button id='article-creator-button' onclick='articleCreator()'>Post erstellen</button><br><br>
                    <h3>Meine Posts:</h3></div><br>
                    ");
                // Die Methode listArticles gibt id, title, username und content in einem Array von allen Artikel zurück,
                // die UserID == $_SESSION["USERID"] haben, sortiert nach ArticleID desc.
                // Heisst die Artikel mit der kleinsten ID sind zuoberst.
                $row = listArticles("UserId", $_SESSION["USERID"], "ArticleID", "desc", $SQLiteDB);
                // Nun wird jeder Artikel aus $row ausgegeben. Wenn auf den Titel des Artikels geklickt wird, wird die Javascriptmethode showArticle() ausgeführt.
                for ($i=0; $i < count($row); $i++) {
                    print("<div class='content-div'><h2 id='".$row[$i]['id']."' onclick='showArticle(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                    // Die Artikel werden auf 500 Zeichen gekürtzt.
                    $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                    print($out."</div>");
                }
            } else {
                // Wenn der User Admin ist, wird eine Seite geladen auf der alle User/Blogs aufgelistet sind. Da der Admin "nur" Posts löschen und User löschen kann,
                // wird für ihn nicht das normale userhome generiert.
                // Wir suchen zuerst alle Users aus der Datenbank.
                $result = $SQLiteDB->query("select Email from users");
                // Nun wird die Methode querySingleToArray() aufgerufen.
                // Diese wandelt ein SQLite Onjekt in ein Array mit den gewünschten Daten um.
                $row = querySingleToArray($result, "Email");
                print("
                <div class='content-div'>
                    <h3 class='textalign-center'>Klicken Sie auf einen User um ihn zu löschen:</h3>
                </div>
                <div class='content-div'>
                    <p>
                ");
                // Es werden alle Emails ausgegeben. Wenn auf einen User gecklickt wird, wird er gelöscht. Der click-Handler oben führt dann die Javascriptmethode deleteUser() aus.
                for ($i=0; $i < count($row); $i++) {
                    // Da wir den Admin nicht löschen wollen, wird er übersprungen.
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
            // Wenn submenu delete_user ist, wird der User aus der Datenbank gelöscht.
            $SQLiteDB->exec("delete from users where UserID = ".$_POST["user_id"]);
            // Danach werden alle Artikel diese User auch gelöscht.
            // Dafür wird in der Tabelle users_articles geschaut welche Artikel zu dem User gehören.
            $result = $SQLiteDB->query("select IDArticle from users_articles where IDUser = ".$_POST["user_id"]);
            // Es werden die Artikel in der Tabelle articles gelöscht.
            $row = querySingleToArray($result, "IDArticle");
            for ($i=0; $i < count($row); $i++) {
                $SQLiteDB->exec("delete from articles where ArticleID = ".$row[$i]);
                // Auch aus der Tabelle articles_themes werden alle Einträge gelöscht die einen der Artikel des Users beinhalten.
                $SQLiteDB->exec("delete from articles_themes where IDArticle = ".$row[$i]);
            }
            // Es werden auch noch die Einträge aus users_articles gelöscht.
            // Nun sind alle Anhängigkeiten gelöscht.
            $SQLiteDB->exec("delete from users_articles where IDUser = ".$_POST["user_id"]);
            // Es wird eine Information ausgegeben.
            print("<div id='article-deleted' class='content-div'><h1>Der User wurde gelöscht</h1></div>");
            break;

        case "show_blog":
            // Wenn submenu show_blog ist, wird eine Seite generiert in der der Username/Blogname sowie die anzahl Artikel und alle Artikel agezeigt werden.
            // Es werden alle Artikel des Users gezählt.
            $numbArticles = $SQLiteDB->querySingle("select count(ArticleID) from articles
            inner join users_articles on users_articles.IDArticle=articles.ArticleID
            inner join users on users.UserID=users_articles.IDUser
            where Username = '".$_POST["user"]."'");
            print("<div class='content-div'>
                    <h1>".$_POST["user"]."</h1>
                </div>
                <p>Anzahl Posts: ".$numbArticles."</p>");
            // Es werden Inhalt und Titel aller Artikel geholt, bei denen Username $_POST["user"] entspricht.
            // Die Artikel werden nach ArticleID desc sotiert. Heisst die mit der kleinsten ID zuerst.
            $row = listArticles("Username", $_POST["user"], "ArticleID", "desc", $SQLiteDB);
            for ($i=0; $i < count($row); $i++) {
                // Nun werden di Artikel ausgegeben. Bei einem Klick auf den Titel eines Artickels wird die Javascriptmethode showArticle() aufgerufen.
                print("<div class='content-div'><h2 id='".$row[$i]['id']."' onclick='showArticle(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                // Die Artikel werden auf 500 Zeichen gekürtzt.
                $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                print($out."</div>");
            }
            break;

        case "article":
            // Wenn submenu article ist, wird eine Seite gerneriert in der ein einziger Artikel gezeigt wird. Dafür aber in voller länge.
            $clickedArticle = $_POST["id"];
            // Es werden Titel, Inhalt, Ersteller des geklickten Artikels aus der Datenbank geholt.
            $result = $SQLiteDB->querySingle("select Title, Username, Content from articles
                inner join users_articles on users_articles.IDArticle=articles.ArticleID
                inner join users on users.UserID=users_articles.IDUser
                where ArticleID = ".$clickedArticle, true);
            // Der Artikel wird nun ausgegeben. Bei einem Klick auf den Ersteller (Blog: beispiel) wird der
            // Javascripthandler für getBlog augerufen und dieser führt dann die  Javascriptmethode showBlog() aus.
            print("
                 <div class='content-div' id='article'>
                    <h2 id='show_content'>".$result["Title"]."</h2>
                    <p name='".$result["Username"]."' class='getBlog'>Blog: ".$result["Username"]."</p>
                    ".$result["Content"]."
                </div>
            ");
            // Nun wird noch der Besitzer des Artikels aus der Datenbank geholt.
            $owner = $SQLiteDB->querySingle("select UserID from users
                inner join users_articles on users.UserID=users_articles.IDUser
                inner join articles on articles.ArticleID=users_articles.IDArticle
                where ArticleID = '".$clickedArticle."'");
            // Wenn der User der Ersteller des Blogs ist oder Admin, werden die Buttons Bearbeiten und Löschen angezeigt.
            // Bei einem Klick auf Löschen wird die Javascriptmethode leteArticle() aufgerufen und bei Bearbeiten changeArticle().
            if ($_SESSION["USERID"] == $owner || $_SESSION["EMAIL"] == "admin@gibb.ch") {
                $tinymceContent = $SQLiteDB->querySingle("select Content from articles where ArticleID = ".$clickedArticle);
                print("<button id='edit-article-button' onclick='changeArticle(".'"'.$clickedArticle.'"'.", ".'"'.$tinymceContent.'"'.")'>Bearbeiten</button>");
                print("<button id='delete-button' onclick='deleteArticle(".'"'.$clickedArticle.'"'.")'>Löschen</button>");
            }
            break;

        case "delete_article":
            // Wenn submenu delete_article ist, wird der Artikel gelöscht, sowie alle seine bhängigkeiten.
            $SQLiteDB->exec("delete from articles where ArticleID = ".$_POST["article_id"]);
            $SQLiteDB->exec("delete from users_articles where IDArticle = ".$_POST["article_id"]);
            $SQLiteDB->exec("delete from articles_themes where IDArticle = ".$_POST["article_id"]);
            // Es wird eine Information ausgegeben
            print("<div id='article-deleted' class='content-div'><h1>Ihr Post wurde gelöscht</h1></div>");
            break;

        case "home":
            // Wenn submenu home ist, wird die Startseite generiert. Es werden alle Blogs angezeigt.
            print('
                <div class="content-div" id="welcome">
                    <h1>Wilkommen</h1>
                    <p>Hier sehen Sie alle Blogs:</p><br>
                </div>
            ');
            // Es werden alle Blogs/User aus der Datebank geholt.
            $result = $SQLiteDB->query("select Username from users");
            $row = querySingleToArray($result, "Username");
            print("<div id='blogs'>");
            // Jeder Blog wird augegeben. Bei einem Klick auf den Blog/Username, wird der Javascripthandler von getBlog augerufen.
            // Dieser führt dann die Javascriptmethode showBlog() aus.
            for ($i=0; $i < count($row); $i++) {
                if ($row[$i] != "Admin") {
                    print("<h2 class='getBlog' name='".$row[$i]."'>".$row[$i]."</h2> ");
                }
            }
            print("</div>");
            break;

        case "home_menu":
            // Wenn sub_menu == "home_menu" ist, wird das Menu generiert.
            print('
                <nav class="navbar">
                  <div>
                    <div>
                        <ul id="menu-bar" class="nav navbar-nav">
                            <li><a id="home" href="main.html">Home</a></li>
            ');
            // Es werden alle Themen aus der Datenbank geholt
            $result = $SQLiteDB->query("select ThemeName from themes");
            $menu = querySingleToArray($result, "ThemeName");
            for ($i=0; $i < count($menu); $i++) {
                // Für jedes Thema wird ein Menueintrag erstellt. Bei einem Klick auf den Menueintrag, wird der Javascripthandler von menu-elements augerufen.
                // Dieser führt dann die Javascriptmethode loadSubMenu() aus.
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
            // Wenn keine der oberen case eintrifft wird default ausgeführt.
            // Das bedeutet das auf einen Menueintrag zu einem Thema gecklickt wurde, da alle anderen Varianten schon geprüft wurden.
            // Es wird die Methode listArticles() aufgerufen und alle Einträge bei denen ThemeName $clickedSubMenu entspricht,
            // werden sortiert nach ArticleID desc ausgegeben. Bedeutet die mit der kleineren ID oben.
            $row = listArticles("ThemeName", $clickedSubMenu, "ArticleID", "desc", $SQLiteDB);
            if (count($row)) {
                // Es werden alle Posts ausgegeben die als Thema den das geklickte Thema haben.
                // Wenn auf den Titel des Artikels geklickt wird, wird die Javascriptmethode showArticle() ausgeführt. Bei einem Click auf den Titel eines Artikels,
                // wird der Javascripthandler von getBlog aufgerufen der darauf die Javascriptmethode showBlog() ausführt.
                for ($i=0; $i < count($row); $i++) {
                    print("<div class='content-div'><h2 id='".$row[$i]['id']."' onclick='showArticle(".$row[$i]["id"].")'>".$row[$i]['title']."</h2>");
                    print("<p name='".$row[$i]['username']."' class='getBlog'>Blog: ".$row[$i]['username']."<br></p><br>");
                    $out = strlen($row[$i]['content']) > 500 ? substr($row[$i]['content'],0,500)."..." : $row[$i]['content'];
                    print($out."</div>");
                }
            } else {
                // Falls es keine Artikel in dieser Kategorie gibt. Wird eine Iformation ausgegeben.
                print("<div id='void-category' class='content-div'><h3>In dieser Kategorie gibt es noch keine Posts.</h3></div>");
            }
    }

    // Diese Methode nimmt ein SQL Objekt $queryResult und der Reihenname $rowName und gibt ein Array zurück.
    function querySingleToArray($queryResult, $rowName){
        // $result wird als array definiert.
        $result = array();
        $i = 0;
        // Für jedes Element in queryResult wird das SQL Object in ein mehrdimensionales Array umgewandelt.
        // Denn es konnte ja nicht nur eine Reihe gefragt sein sonden z.B. nocht die ID.
        // Diese Methode ist aber nur für ein Element.
        while($row = $queryResult->fetchArray(SQLITE3_ASSOC)){
            // Dieses Element wird dann in $result gespeichert.
            $result[$i] = $row[$rowName];
            $i++;
        }
        // Am Schluss wird $result zurückgegeben.
        return $result;
    }

    // Diese Methode giebt alle Artikel zurück, bei denen $searchedRow gleich $searchedColumn.
    // Die Artikel werden geordnet nach $orderByRow (Typ: $orderType).
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
      // Für jedes Element in queryResult wird das SQL Object in ein mehrdimensionales Array umgewandelt.
       while($res = $result->fetchArray(SQLITE3_ASSOC)){

           $row[$i]['id'] = $res['ArticleID'];
           $row[$i]['title'] = $res['Title'];
           $row[$i]['username'] = $res['Username'];
           $row[$i]['content'] = $res['Content'];

            $i++;

        }
        // Am Schluss wird ein mehrdimensionales Array zurück gegeben.
        return $row;
    }
?>
