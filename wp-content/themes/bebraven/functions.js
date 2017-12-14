/* Braven Theme Functions */
/* Requires JQuery */

(function($){

$('document').ready(function(){

	console.log('Custom theme functions running');

	// Some elements reveal stuff on click:
	$('.bio .element-img').click(function(e){
		var containingBio = $(this).parents('.bio');
		$('.reveal').not(containingBio).removeClass('reveal');
		$(containingBio).addClass('reveal');
	});

	$('.close-this').click(function(e) {
		$(this).parents('.bio').removeClass('reveal');
	});


	// Tabbed content functionality:
	$('.tab').not('#parent-tab').hide();
	$('#tabs-menu a').click(function(e){
		e.preventDefault();
		$('#tabs-menu .active').removeClass('active');
		$('.tab').hide();
		var target = $(this).addClass('active').attr('href');
		$(target).show();
	});

});
})(jQuery);

