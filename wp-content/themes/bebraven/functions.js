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

	$('#email-sign-up-btn').click(function(e){
		$('#email-sign-up-form').addClass('reveal');
	});

	$('.model-more').each(function(){
		// Add a close button to overlays:
		$(this).addClass('overlay').prepend('<div class="close-this">&#x2715;</div>');
	});

	$('.model-more-link').each(function(){
		// Remember the target of this button:
		var target = $(this).attr('href');

		// Make the parent link to the target:
		$(this).parents('.model-box').addClass('has-content').click(function(e){
			$(target).addClass('reveal');
		});

		// Remove the original link button since the whole box is a link now:
		$(this).remove();
	});

	$('.close-this').click(function(e) {
		$(this).parents('.bio, .overlay').removeClass('reveal');
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

	$('.statbar').addClass('dim');

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

	// Mobile class to assist in menu displays etc.:
	$(window).resize(function(){
		// Figure out whether we're in mobile mode based on a CSS rule, in order to avoid hard-coding a pixel width breakpoint here: 
		if( $('#masthead .menu li').css('display') != 'inline' ) {
			$('body').addClass('mobile');

			// Move social nav to top nav when in mobile menu mode:
			$('.navigation-top .wrap').append( $('#masthead .social-nav').show() );
			
		} else {
			$('body').removeClass('mobile');

			// Move social nav back under the masthead:
			$('#masthead').append( $('#masthead .social-nav') );
		}
	}).resize();

	//// Make the top nav menu appear upon clicking the menu icon: 
	$('#top-nav-btn').click(function(e){
		e.preventDefault();
		$('body').toggleClass('mobile-nav-expanded');
	});


	// Make a click outside the nav to close it:
	/* Disabled for now because it only works once...
	$(window).click(function(event){
		var $box = $('#masthead nav');
		if( $('body').hasClass('mobile-nav-expanded') 
			&& !$('#top-nav-btn').is(event.target)
			&& $box.has(event.target).length == 0 //checks if descendants were clicked
			&& !$box.is(event.target) //checks if the $box itself was clicked
		
		) {
			$('body').removeClass('mobile-nav-expanded');
		}
	});
	*/

	// Escape closes nav menu:
	$(window).on('keydown', function(e){
	if (e.which === 27) {
		$('body').removeClass('mobile-nav-expanded');
			e.preventDefault();
		}
	});

}); // end document ready

})(jQuery);

