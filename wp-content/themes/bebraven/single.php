<?php
/**
 * The template for displaying single posts 
 */

get_header(); ?>

<div class="wrap template-single">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();
				$component_format = 'post';
				include 'content.php';

				/*
				the_post_navigation( array(
					'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Post', 'bz' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Previous', 'bz' ) . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper">' . bz_get_svg( array( 'icon' => 'arrow-left' ) ) . '</span>%title</span>',
					'next_text' => '<span class="screen-reader-text">' . __( 'Next Post', 'bz' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next', 'bz' ) . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper">' . bz_get_svg( array( 'icon' => 'arrow-right' ) ) . '</span></span>',
				) );
				*/
			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();
