head.ready(function() {     
$('.dropdown.accountmenu input, .dropdown.accountmenu label').click(function(e) {e.stopPropagation();});
$('.dropdown.accountmenu').hover(function() { $(this).addClass('open');}, function() {$(this).removeClass('open');});
$('.dropdown.menu').hover(function() { $(this).addClass('open');}, function() {$(this).removeClass('open');});
$('.dropdown.catmenu').hover(function() { $(this).addClass('open');}, function() {$(this).removeClass('open');});
}); 
