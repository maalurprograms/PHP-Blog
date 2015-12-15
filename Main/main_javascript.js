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
        xhttp_content.send("submenu=delete_user&userid="+$(this).attr("name"));
    }
}

function load_site() {
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

function add_post(){
    var post_title = $("#post_title").val();
    var post_category = $("#post_category").val();
    var post_content = tinyMCE.activeEditor.getContent();

    if (!post_title) {
        $("#title_alarm").show()
    } if (!post_content) {
        $("#content_alarm").show()
    }

    var regex = new RegExp('&', 'g');
    post_content = post_content.replace(regex, '%**%');
    var regex = new RegExp('=', 'g');
    post_content = post_content.replace(regex, '%**%equals');
    var data = "post_title="+post_title+"&post_category="+post_category+"&post_content="+post_content;

    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=add_post&"+data);
}

function load_post(id) {
    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=post&id="+id);
}

function create_post() {
    xhttp_content.open("POST", "sub_menu.php", true);
    xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp_content.send("submenu=create_post");
}

function checkPostData(){
    if (!$("#post_title").val()) {
        $('#title_alarm').show();
    } else {
        $('#title_alarm').hide();
    }

    if (!tinyMCE.activeEditor.getContent()) {
        $('#content_alarm').show();
    } else {
        $('#content_alarm').hide();
    }

    if (tinyMCE.activeEditor.getContent() && $("#post_title").val()) {
        $("#add_post_button").show();
    } else{
        $("#add_post_button").hide();
    }
}

function deletePost(id) {
    if (confirm("Sind Sie sicher dass sie diesen Eintrag löschen wollen?")) {
        xhttp_content.open("POST", "sub_menu.php", true);
        xhttp_content.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp_content.send("submenu=delete_post&postid="+id);
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
