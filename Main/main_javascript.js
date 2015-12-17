// Diese Funktion setzt die XHTTP Request zusammen.
function setXHTTP(section) {
    var xhttp;
    xhttp = new XMLHttpRequest();
    // Wenn readyState 4 ist (request finished and response is ready) und status 200 ("Ok")
    // wird die als Parameter übergebene Variabel durch die Antwort der Anfrage ersetzt.
    // In diesem Fall wird also das Element "section" durch die Antwort vom Server ersetzt.
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            $(section).html(xhttp.responseText);
        }
    };
    // Wir geben "xhttp" nun fertig konfiguriert zurück
    return xhttp;
}

// In Dieser Methode wird die Anfrage an den Server gesendet.
// Da es zwei xhhtp gibt (menu und content), müssen wir festlegen welche der beiden gemeint ist.
// Ausserdem müssen die Daten übergeben werden die an den Server geschickt werden sollen.
function sendXHTTPRequest(data, xhttp) {
    xhttp.open("POST", "dynamic_elements.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data);
}

// Diese Methode lädt dynamisch die Menuinhalte wenn auf ein Sub-Menu gecklickt wird. (z.B. Die Kategorien oder das User-Menu)
function loadSubMenu() {
    // Wenn der Logout-Button im Menu gecklickt wurde, werden die session Variabeln zurückgesetzt und man wird zum Login zurück geleitet.
    if ($(this).attr("id") == "logout") {
        sendXHTTPRequest("submenu=logout", xhttp_content)
        //window.location = "../Login/login.html";
    } else{
    // Bei allen anderen Menu punkten (ausser home da das ein Link auf main.html ist) wird nun der Inhalt des Menus geladen.
    // Dafür rufen wir die Methode "sendXHTTPRequest" auf die uns die Anfrage zusammenstellt.
    // "submenu=" steht für welcher Code im PHP-File aufgerufen wird. In einem Switch wird dann überprüft welchen Wert "submenu" hat.
    sendXHTTPRequest("submenu="+$(this).attr("id"), xhttp_content);
    }
}

// Diese Methode wird aufgerufen wenn der Admin einen User löscht.
function deleteUser(id) {
    // Als bestätigung wird noch ein Fenster aufgerufen.
    if (confirm("Sind Sie sicher dass sie diesen User löschen wollen?")) {
        // Dann wird die Anfrage an den Server gesendet.
        sendXHTTPRequest("submenu=delete_user&user_id="+$(this).attr("name"), xhttp_content);
    }
}

// Diese Methode wird aufgerufen wenn ein User einen Artikel anlegt.
function addArticle(){
    // Dafür sammeln wir die Werte
    var article_title = $("#article_title").val();
    var article_category = $("#article_category").val();
    // Da das <textaera> bei mir ein Web-Editor ist hat der auch spezielle Methoden
    // .getContent() nimmt wie der Name schon sagt den Inhalt aus dem Editor.
    var article_content = tinyMCE.activeEditor.getContent();
    // Nun müssen alle "&" und "="-zeichen aus den Strings entfernt werden,
    // da xhttp "=" zum zuweisen von Werten bennötigt und "&" um anzugeben dass noch einen weitere Variabel folgt.
    var regex = new RegExp('&', 'g');
    // Ersetzt werden sie durch "%**%" un "%**%equals"
    article_content = article_content.replace(regex, '%**%');
    var regex = new RegExp('=', 'g');
    article_content = article_content.replace(regex, '%**%equals');
    // Nun werden die Einzelteile zu einem String zusammen gefügt.
    var data = "article_title="+article_title+"&article_category="+article_category+"&article_content="+article_content;
    // Dann wird die Anfrage an den Server gesendet.
    sendXHTTPRequest("submenu=add_article&"+data, xhttp_content);
}

// Diese Methode wird aufgerufen wenn ein User einen seiner Artikel bearbeiten will.
function changeArticle(id) {
    // Es wird eine Anfrage gesendet mit dem submenu und der ArticleID. Diese wird später bennötigt um im PHP den richtigen Artikel zu suchen.
    sendXHTTPRequest("submenu=edit_article&article_id="+id, xhttp_content);
}

// Diese Methode wird aufgerufen wenn ein User den Blog eines Anderen Users aufruft.
function showBlog() {
    // Es wird eine Anfrage gesendet mit dem submenu und dem Username.
    // Dieser wird später bennötigt um im PHP den richtigen Blog zu suchen. (Wobei Username dem Blognamen entspricht)
    sendXHTTPRequest("submenu=show_blog&user="+$(this).attr("name"), xhttp_content);
}

// Diese Methode wird aufgerufen, wenn ein User einen neuen Artikel erstellt hat und ihn speichern will
// oder ein User einen bestehenden Artikel geändert hat und diesen speichern will.
function checkArticleData() {
    // Wir setzten die beiden Variabeln dataOk, die auf false/true gesetzt wird wenn die input/textaera felder Text enthalten/leer sind,
    // und add, die zum überprüfen bennötigt wird ob eine Artikel erstellt oder geändert wird.
    var dataOk = false;
    var add = false;
    // Wenn das Feld "article-title" existiert, wissen wir, dass ein Artikel angelegt wird.
    // Denn beim bearbeiten kann der Titel nich mehr verädert werden.
    if ($("#article_title").length>0) {
        // Dementsprechend wird die Variabel "add" auf true gesezt.
        add = true;
        // Wenn das Titelfeld leer ist, wird eine dementsprechende Fehlermeldung angezeigt.
        if (!$("#article_title").val()) {
            $("#title_alarm").show();
            // Da kein Titel eingegeben wurde wird "dataOk" auf false gesetzt.
            dataOk = false;
        } else {
            // Wenn ein Titel eigegeben wurde, wird die Fehlermeldung versteckt und "dataOk" wird auf true gesezt.
            $("#title_alarm").hide();
            dataOk = true;
        }
    }
    // Wenn kein Inhalt eingegeben wurde wird eine dementsprechende Fehlermeldung angezeigt und "dataOk" wird auf false gesezt.
    if (!tinyMCE.activeEditor.getContent()) {
        $("#content_alarm").show();
        dataOk = false;
    } else {
        // Wenn Inhalt eigegeben wurde, wird die Fehlermeldung versteckt und "dataOk" wird auf true gesezt.
        $("#content_alarm").hide();
        dataOk = true;
    }
    // Wenn beide Überprüfungen "dataOk" auf true setzten heisst dass das überall etwas eingegeben wurde.
    if (dataOk) {
        // Nun wird überprüft ob ein Artikel erstellt oder geändert wird.
        // Da wenn es kein Titelfeld gibt es sich um eine Änderung handelt, wird "add" in diesem Fall auf false gesetzt. Falls es das Titelfeld gibt, auf true.
        if (add) {
            // Wenn es eine Erstellung ist, wird nun die Methode addArticle() aufgerufen.
            addArticle();
        } else {
            // Wenn es eine Bearbeitung ist, wird nun die Methode saveChanges() aufgerufen.
            saveChanges($("#article-title").attr("name"));
        }
    }
}

// Diese Methode wird aufgerufen wenn ein User einen Artikel editiert hat und speichert.
// Als Parameter wird die ID des Artikels mitgegeben da die Datenbank wissen muss, welchen Artikel sie ändern muss.
function saveChanges(id) {
    // Wir nehmen den Inhalt aus dem Web-Editor und filltern alle "&" und "=" heraus, da sie Zeichen sind die die POST methode stören würde.
    var article_content = tinyMCE.activeEditor.getContent();
    var regex = new RegExp('&', 'g');
    article_content = article_content.replace(regex, '%**%');
    var regex = new RegExp('=', 'g');
    article_content = article_content.replace(regex, '%**%equals');
    // Es wird eine Anfrage gesendet mit dem submenu, dem Inhalt und der ArticleID.
    // Diese wird später bennötigt um im PHP den richtigen Artikel zu suchen.
    sendXHTTPRequest("submenu=save_changes&article_content="+article_content+"&article_id="+id, xhttp_content);
}

// Diese Methode wird aufgerufen wenn ein Artikel geöffnet wird.
function showArticle(id) {
    // Es wird eine Anfrage gesendet mit dem submenu und der ArticleID.
    // Diese wird später bennötigt um im PHP den richtigen Artikel zu suchen.
    sendXHTTPRequest("submenu=article&id="+id, xhttp_content);
}

// Diese Methode wird aufgerufen wenn ein User auf den Bearbeiten-Button klickt.
function articleCreator() {
    // Es wird eine Anfrage gesendet.
    sendXHTTPRequest("submenu=article_creator", xhttp_content);
}

// Diese Methode wird aufgerufen wenn ein User einen Artikel löacht.
function deleteArticle(id) {
    // Es wird ein Bestätigungs Fenster angezeigt und danach die Anfrage an den Server gesendet.
    if (confirm("Sind Sie sicher dass sie diesen Eintrag löschen wollen?")) {
        sendXHTTPRequest("submenu=delete_article&article_id="+id, xhttp_content);
    }
}

// Wenn das HTML-Dokument ganz geladen ist, werden die zwei xhhtp konfiguriert und es wird die Startseite und das Menu aufgebaut.
$(document).ready(function(){
    xhttp_menu = setXHTTP("#menu");
    xhttp_content = setXHTTP("#content");
    sendXHTTPRequest("submenu=home_menu", xhttp_menu);
    sendXHTTPRequest("submenu=home",xhttp_content);
});
