<?php
    $clicked_post = $_POST["id"];
    $db = new SQLite3("..\SQL\blog.db");
    $result = $db->querySingle("select Title, Username, Content from articles_themes
        inner join articles on articles.ArticleID=articles_themes.IDArticle
        inner join themes on themes.ThemeID=articles_themes.IDTheme
        inner join users_articles on users_articles.IDArticle=articles.ArticleID
        inner join users on users.UserID=users_articles.IDUser
        where ArticleID = ".$clicked_post, true);

    print("
        <div class='content_div' id='post'>
            <h2 id='show_content'>".$result["Title"]."</h2>
            <p>".$result["Username"]."</p>
            ".$result["Content"]."
        </div>
    ");
?>
