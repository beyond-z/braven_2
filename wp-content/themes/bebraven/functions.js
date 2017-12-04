/* Braven Theme Functions */
/* Requires JQuery */

(function($){

$('document').ready(function(){

	console.log('Custom theme functions running');

	// some elements reveal stuff on click:
	$('.bio').click(function(e){
		e.preventDefault();
		$(this).toggleClass('reveal');
	});

});
})(jQuery);

