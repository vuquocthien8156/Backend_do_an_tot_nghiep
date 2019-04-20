$(document).ready(function() {
	setTimeout(function() {
		$(function(){
    $('p').hover(function(){
        $(this).css('color','blue');
    },function(){
        $(this).css('color','black');
});
});
	},2000);         
}); 