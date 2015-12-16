function setXHTTP(section) {
    var xhttp;
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            $(section).html(xhttp.responseText);
        }
    };
    return xhttp;
}

function sendXHTTPRequest(data, xhttp) {
    xhttp.open("POST", "dynamic_elements.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data);
}

function loadSubMenu() {
    if ($(this).attr("id") == "logout") {
        sessionStorage.clear();
        localStorage.clear();
        window.location = "../Login/login.html";
    } else{
    sendXHTTPRequest("submenu="+$(this).attr("id"), xhttp_content);
    }
}

function deleteUser(id) {
    if (confirm("Sind Sie sicher dass sie diesen User löschen wollen?")) {
        sendXHTTPRequest("submenu=delete_user&user_id="+$(this).attr("name"), xhttp_content);
    }
}

function addArticle(){
    var article_title = $("#article_title").val();
    var article_category = $("#article_category").val();
    var article_content = tinyMCE.activeEditor.getContent();

    var regex = new RegExp('&', 'g');
    article_content = article_content.replace(regex, '%**%');
    var regex = new RegExp('=', 'g');
    article_content = article_content.replace(regex, '%**%equals');
    var data = "article_title="+article_title+"&article_category="+article_category+"&article_content="+article_content;

    sendXHTTPRequest("submenu=add_article&"+data, xhttp_content);
}

function changeArticle(id) {
    sendXHTTPRequest("submenu=edit_article&article_id="+id, xhttp_content);
}

function showBlog() {
    sendXHTTPRequest("submenu=show_blog&user="+$(this).attr("name"), xhttp_content);
}

function checkArticleData() {
    var dataOk = false;
    var addArticle = false;
    if ($("#article_title").length>0) {
        addArticle = true;
        if (!$("#article_title").val()) {
            $("#title_alarm").show();
            dataOk = false;
        } else {
            $("#title_alarm").hide();
            dataOk = true;
        }
    }
    if (!tinyMCE.activeEditor.getContent()) {
        $("#content_alarm").show();
        dataOk = false;
    } else {
        $("#content_alarm").hide();
        dataOk = true;
    }
    if (dataOk) {
        if (addArticle) {
            addArticle();
        } else {
            saveChanges($("#article-title").attr("name"));
        }
    }
}

function saveChanges(id) {
    var article_content = tinyMCE.activeEditor.getContent();
    var regex = new RegExp('&', 'g');
    article_content = article_content.replace(regex, '%**%');
    var regex = new RegExp('=', 'g');
    article_content = article_content.replace(regex, '%**%equals');

    sendXHTTPRequest("submenu=save_changes&article_content="+article_content+"&article_id="+id, xhttp_content);
}

function showArticle(id) {
    sendXHTTPRequest("submenu=article&id="+id, xhttp_content);
}

function articleCreator() {
    sendXHTTPRequest("submenu=article_creator", xhttp_content);
}

function deleteArticle(id) {
    if (confirm("Sind Sie sicher dass sie diesen Eintrag löschen wollen?")) {
        sendXHTTPRequest("submenu=delete_article&article_id="+id, xhttp_content);
    }
}

$(document).ready(function(){
    xhttp_menu = setXHTTP("#menu");
    xhttp_content = setXHTTP("#content");
    sendXHTTPRequest("submenu=home_menu", xhttp_menu);
    sendXHTTPRequest("submenu=home",xhttp_content);
});
