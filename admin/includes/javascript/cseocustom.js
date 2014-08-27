$(document).ready(function(){
    $('#menu li').hover(function(){
            $(this).find('ul:first').css({visibility: "visible",display: "none"}).slideDown(200); // effect 2
        },function(){
            $(this).find('ul:first').css({visibility: "hidden"});
        });
});
