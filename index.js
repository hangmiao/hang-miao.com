$(document).ready(function() {
    // arrow in the submenu
//    $("#ar").html("&#9652;");
//    $("#ar:first-child").addClass("intro");
    $("#ar").html("&#9652;");
    $("#ar").addClass("intro");

//  dropdown menu animation
    $("li").hover(function() {
        // $(this).find("ul>li").stop().slideToggle(300);
        $(this).find("#submenu_web_pro>ul>li, #ar").stop().fadeToggle(200);
//        $(this).find("#ar").stop().fadeToggle(200);
    });
});
