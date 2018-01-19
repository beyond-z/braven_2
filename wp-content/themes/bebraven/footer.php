<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>

		</div><!-- #content -->

		<footer id="footer" class="site-footer">
			<div id="footer-cta" class="wrap">
				<?php // social nav menu:
				include 'nav-social.php';
				?>
				<div id="email-sign-up-btn">Sign up for emails</div>
				<div id="email-sign-up-form" class="centered overlay salesforce">
					<div class="close-this">&#x2715;</div>

					<script src="https://www.google.com/recaptcha/api.js"></script>
					<script>
					 function timestamp() { var response = document.getElementById("g-recaptcha-response"); if (response == null || response.value.trim() == "") {var elems = JSON.parse(document.getElementsByName("captcha_settings")[0].value);elems["ts"] = JSON.stringify(new Date().getTime());document.getElementsByName("captcha_settings")[0].value = JSON.stringify(elems); } } setInterval(timestamp, 500); 
					</script>
					
					<form action="https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8" method="POST">
						<input type=hidden name='captcha_settings' value='{"keyname":"reCaptcha_API_bebraven","fallback":"true","orgId":"00Do0000000YIOc","ts":""}'>
						<input type=hidden name="oid" value="00Do0000000YIOc">
						<input type=hidden name="retURL" value="https://bebraven.org/thank-you">
						<label class="screen-reader-text" for="first_name">First Name</label>
						<input  id="first_name" placeholder="First Name" maxlength="40" name="first_name" type="text" />
						<label class="screen-reader-text" for="last_name">Last Name</label>
						<input  id="last_name" placeholder="Last Name" maxlength="80" name="last_name" type="text" />
						<label class="screen-reader-text" for="email">Email</label><input id="email" placeholder="e-Mail" maxlength="80" name="email" type="email" />
						<input id="lead_source" type=hidden name="lead_source" value="BeBraven.org" />
						<div class="g-recaptcha" data-sitekey="6LeE5kAUAAAAACDVkHhZVfECpiVrDu7r4eUOcsId"></div>
						<input type="submit" name="submit" value="Submit">
					</form>

				</div>

			</div>
			<div id="nav-area" class="wrap">
				<?php
				if ( has_nav_menu( 'footer' ) ) { ?>
					<nav class="footer-nav" role="navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'bz' ); ?>">
						<?php wp_nav_menu( array( 'theme_location' => 'footer' ) ); ?>
					</nav><!-- .social-navigation -->
				<?php }
				if ( has_nav_menu( 'legal' ) ) { ?>
					<nav class="legal-info" role="navigation" aria-label="<?php esc_attr_e( 'Legal Info', 'bz' ); ?>">
						<?php 
						$copyright = '&copy;&nbsp;'. date("Y") . __('&nbsp;Braven, Inc. All rights reserved.', 'bz');
						wp_nav_menu( array( 'theme_location' => 'legal', 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s<li>'.$copyright.'</li></ul>' ) ); ?>
					</nav><!-- .social-navigation -->
				<?php }
				?>
			</div><!-- .wrap -->
		</footer><!-- #colophon -->
	</div><!-- .site-content-contain -->
</div><!-- #page -->
<?php wp_footer(); ?>

</body>
</html>
