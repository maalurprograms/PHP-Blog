$(document).ready(function(){
    $("h1").click(function() {
        var xhttp;
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                $("#content").html(xhttp.responseText);
            }
        };
        xhttp.open("POST", "post.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("id="+$(this).attr("id"));
    });
});
