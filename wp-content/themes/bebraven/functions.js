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

		// Add a hash to the URL in case someone wants to direct to the tab:
		history.pushState(null,null,target);
	});

	// Switch tabs based on hash in url, so we can land directly on the target tab:
	var hash = window.location.hash;
	if ('/' == hash.slice(-1)) { 
		hash = hash.slice(0, -1);
	}
	if (hash) {
		$('.tab'+hash).show().siblings('.tab').hide();
		$('#tabs-menu a').removeClass('active').each(function(){
			if (hash == $(this).attr('href')) {
				$(this).addClass('active');
			}
		});
	}

	// Remove has-content from small boxes, since we can't read it anyway:
	$('[data-bz-columns="5"] .has-content').removeClass('has-content');


	// Bar charts:
	$('.stats tr').each(function(){
		// Exctract the percentage value:
		var pct = $(this).children('td:nth-child(2)').text();
		$(this).children('td:last-child').append('<div class="statbar" style="width:'+pct+';"></div>');
	});


	/* Trigger bar chart animation when in viewport:
<<<<<<< HEAD

	$('.statbar').addClass('dim');
=======
>>>>>>> 22ca2f97ce29113cb00d1a222b30e06b79296d22
	function isElementInViewport(elem) {
	    var $elem = $(elem);

	    // Get the scroll position of the page.
	    var scrollElem = ((navigator.userAgent.toLowerCase().indexOf('webkit') != -1) ? 'body' : 'html');
	    var viewportTop = $(scrollElem).scrollTop();
	    var viewportBottom = viewportTop + $(window).height();

	    // Get the position of the element on the page.
	    var elemTop = Math.round( $elem.offset().top );
	    var elemBottom = elemTop + $elem.height();

	    return ((elemTop < viewportBottom) && (elemBottom > viewportTop));
	}

	// Check if it's time to start the animation.
	function checkAnimation() {
	    var $elem = $('.statbar');

	    // If the animation has already been started
	    if (!$elem.hasClass('dim')) return;

	    if (isElementInViewport($elem)) {
	        // Start the animation
	        $elem.removeClass('dim');
	    }
	}

	// Capture scroll events
	$(window).scroll(function(){
	    checkAnimation();
	});
	*/
<<<<<<< HEAD

=======
>>>>>>> 22ca2f97ce29113cb00d1a222b30e06b79296d22



});
})(jQuery);

