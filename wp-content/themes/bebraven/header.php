<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'bz' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<a class="logo" href="<?php echo get_home_url();?>">
			<img src="<?php echo get_template_directory_uri();?>/images/braven-logo.png" />
		</a>
		
		<?php if ( has_nav_menu( 'top' ) ) : ?>
			<div class="navigation-top">
				<div class="wrap">
					<?php //get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
				</div><!-- .wrap -->
			</div><!-- .navigation-top -->
		<?php endif; ?>
		<?php

		// Get page components based on the top-level page containing them:
		global $container_ID;
		if ( is_home() || is_front_page() ) {
			$container_ID = bz_get_id_by_slug('home-container');
		} else {
			$container_ID = $post->ID;
		}

		if ( has_post_thumbnail($container_ID) ) {
			?>
			<section class="component marquee">
				<?php echo get_the_post_thumbnail( $container_ID, 'marquee' ); ?>

				
				<div class="marquee-title">
					<h1>
						<?php echo apply_filters( 'the_content', get_the_excerpt($container_ID) ); ?>
					</h1>
					<div>
						<span class="caption-meta">
							<?php echo __('[Real Futures, Real Fellows]');?>
						</span>
						<span class="caption">
							<?php echo get_the_post_thumbnail_caption( $container_ID );?>
						</span>
							
					</div>			
				</div>
			</section>
			<?php
		}
		?>
	</header><!-- #masthead -->
	<div class="site-content-contain">
		<div id="content" class="site-content">
