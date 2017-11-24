/* Braven Theme Functions */
/* Requires JQuery */

(function($){

$('document').ready(function(){

	console.log('Custom theme functions running');

	$('.hover-hint').click(function(e){
		e.preventDefault();
		$(this).toggleClass('reveal');
	});

});
})(jQuery);

