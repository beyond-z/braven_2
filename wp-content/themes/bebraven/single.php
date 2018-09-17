<?php
/**
 * The template for displaying single posts 
 */

get_header(); ?>
<!-- single -->
<div class="wrap template-single">
	<?php bz_custom_breadcrumbs(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			// Since this is a blog post, let's show the sidebar:
			get_sidebar();

			// Bring in the post's data from the main query:
			while ( have_posts() ) : the_post();
				$component_format = 'post';
				include 'content.php';

				
				the_post_navigation( array(
					'prev_text' => '<span title="%title" class="nav-subtitle">' 
							. __( 'Previous Story', 'bz' ) 
						. '</span>',
					'next_text' => '<span title="%title" class="nav-subtitle">'
							. __( 'Next Story', 'bz' ) 
						.'</span>',
				) );
				
			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php if ( is_single() ) get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
