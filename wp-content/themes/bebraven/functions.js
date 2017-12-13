/* Braven Theme Functions */
/* Requires JQuery */

(function($){

$('document').ready(function(){

	console.log('Custom theme functions running');

	// some elements reveal stuff on click:
	$('.bio .element-img').click(function(e){
		var containingBio = $(this).parents('.bio');
		$('.reveal').not(containingBio).removeClass('reveal');
		$(containingBio).addClass('reveal');
	});

	$('.close-this').click(function(e) {
		$(this).parents('.bio').removeClass('reveal');
	});

});
})(jQuery);

