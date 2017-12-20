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
				<form id="footer-email-sign-up">
					<label>Sign up for emails</label>
					<input type="email" />
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
