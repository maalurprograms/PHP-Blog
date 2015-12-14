var xhttp;
xhttp = new XMLHttpRequest();
xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
        $("#content").html(xhttp.responseText);
    }
};

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

    xhttp.open("POST", "sub_menu.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("submenu=add_post&"+data);
}

function load_post(id) {
    xhttp.open("POST", "sub_menu.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("submenu=post&id="+id);
}

function load_site() {
    if ($(this).attr("id") == "logout") {
        sessionStorage.clear();
        localStorage.clear();
        window.location = "../Login/login.html";
    } else{
    xhttp.open("POST", "sub_menu.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("submenu="+$(this).attr("id"));
    }
}

function create_post() {
    xhttp.open("POST", "sub_menu.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("submenu=create_post");
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

function showBlog() {
    xhttp.open("POST", "sub_menu.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("submenu=show_blog&user="+$(this).attr("name"));
}

function deletePost(id) {
    xhttp.open("POST", "sub_menu.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("submenu=delete_post&postid="+id);
}

$(document).ready(function(){
    $("a").click(load_site);
    $(".getBlog").click(showBlog);
});
