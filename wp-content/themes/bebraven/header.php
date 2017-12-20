<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
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

		<?php 
		if ( has_nav_menu( 'top-primary' ) ) { 
			?>
			<nav class="navigation-top">
				<div class="wrap">

					<?php 
					if ( has_nav_menu( 'top-secondary' ) ) wp_nav_menu( array( 'theme_location' => 'top-secondary' ) );
					wp_nav_menu( array( 'theme_location' => 'top-primary' ) );
					?> 
				</div><!-- .wrap -->
			</nav><!-- .navigation-top -->
			<?php 
		}

		 // social nav menu:
		include 'nav-social.php';

		// Get page components based on the top-level page containing them:
		global $container_ID;

		if ( is_front_page() || is_404() || is_search() ) {
			// The main home page or other "gloabl" pages like search and "page not found":
			$container_ID = bz_get_id_by_slug('home-container');
		} elseif ( is_home() || is_single() || is_archive() ) {
			// The blog (which wp calls "home"), a list of posts ("archive"), or a single blog post:
			$container_ID = bz_get_id_by_slug('blog');
		} elseif ( !empty($post->ID) ) {
			$container_ID = $post->ID;
		} 

		
		if (has_post_thumbnail($container_ID) ) {

			// Get the format so we can control where the h1 is positioned
			if ( is_page() ) {
			$marquee_format = (wp_get_post_terms($post->ID, 'format')) ? wp_get_post_terms($post->ID, 'format')[0]->slug : '';
			}
			?>
			<section class="component marquee <?php echo $marquee_format; ?>">
				<?php echo get_the_post_thumbnail( $container_ID, 'marquee' ); ?>

				
				<div class="marquee-title">
					<h1>
						<?php echo apply_filters( 'the_content', get_the_excerpt($container_ID) ); ?>
					</h1>
					<?php 
						$caption = get_the_post_thumbnail_caption( $container_ID );
						if ($caption) {
							?>
							<div>
								<span class="caption-meta">
									<?php echo __('[Real Futures, Real Fellows]');?>
								</span>
								<span class="caption">
									<?php echo $caption;?>
								</span>
									
							</div>			
							<?php
						}
					?>
				</div>
			</section>
			<?php
		}
		?>
	</header><!-- #masthead -->
	<div class="site-content-contain">
		<div id="content" class="site-content">
