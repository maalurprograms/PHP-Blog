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

function deleteUser(id) {
    if (confirm("Sind Sie sicher dass sie diesen User löschen wollen?")) {
        xhttp_content.open("POST", "sub_menu.php", true);
        xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp_content.send("submenu=delete_user&user_id="+$(this).attr("name"));
    }
}

function changePost(id) {
    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=update_post&article_id="+id);
}

function loadSubMenu() {
    if ($(this).attr("id") == "logout") {
        sessionStorage.clear();
        localStorage.clear();
        window.location = "../Login/login.html";
    } else{
    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu="+$(this).attr("id"));
    }
}

function showBlog() {
    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=show_blog&user="+$(this).attr("name"));
}

function addPost(){
    var post_title = $("#post_title").val();
    var post_category = $("#post_category").val();
    var post_content = tinyMCE.activeEditor.getContent();

    var regex = new RegExp('&', 'g');
    post_content = post_content.replace(regex, '%**%');
    var regex = new RegExp('=', 'g');
    post_content = post_content.replace(regex, '%**%equals');
    var data = "post_title="+post_title+"&post_category="+post_category+"&post_content="+post_content;

    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=add_post&"+data);
}

function saveChanges(id) {
    var post_content = tinyMCE.activeEditor.getContent();
    var regex = new RegExp('&', 'g');
    post_content = post_content.replace(regex, '%**%');
    var regex = new RegExp('=', 'g');
    post_content = post_content.replace(regex, '%**%equals');

    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=save_changes&post_content="+post_content+"&article_id="+id);
}

function showPost(id) {
    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=post&id="+id);
}

function postCreator() {
    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=post_creator");
}

function deletePost(id) {
    if (confirm("Sind Sie sicher dass sie diesen Eintrag löschen wollen?")) {
        xhttp_content.open("POST", "sub_menu.php", true);
        xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp_content.send("submenu=delete_post&article_id="+id);
    }
}

$(document).ready(function(){
    xhttp_menu = setXHTTP("#menu");
    xhttp_content = setXHTTP("#content");

    xhttp_menu.open("POST", "sub_menu.php", true);
    xhttp_menu.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_menu.send("submenu=home_menu");

    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=home");
});
