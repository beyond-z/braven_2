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
				<form id="email-sign-up-form" class="centered overlay">
					<div class="close-this">&#x2715;</div>
					<h2>Sign up for emails</h2>
					<input type="text" name="web_signup_first_name" placeholder="First Name" />
					<input type="text" name="web_signup_last_name" placeholder="Last Name" />
					<input type="email" name="web_signup_email" placeholder="e-Mail" />
					<input type="submit" name="email-submit" value="submit"/>
				</form>
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
