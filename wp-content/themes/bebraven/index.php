<?php
/**
 * The home page
 *
 * @package WordPress
 * @subpackage bebraven
 * @since 1.0
 * @version 1.0
 */


get_header(); 



?>

<div class="wrap">


	<?php bz_custom_breadcrumbs(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php

			global $container_ID; // $container_ID is defined in the header.php template 
			
			$args = array(
				'post_type' => 'page',
				'post_parent' => $container_ID, 
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC',
			);
			
			$components_query = new WP_Query( $args );

			// Loop through the returned components:
			if ( $components_query->have_posts() ) {
				
				// loop:
				while ( $components_query->have_posts() ) {
					$components_query->the_post();
					$component_format = (wp_get_post_terms($post->ID, 'format')) ? wp_get_post_terms($post->ID, 'format')[0]->slug : '';

					// NOTE: If you're looking for how the bios are generated, search 
					// for include-bios in functions.php and for a file called single-bio.php

					?>
					<section class="component <?php echo $post->post_name . ' ' . $component_format;?>">
						<?php 

						// Define a thumbsize based on the post's format
						$thumbsize = ('half-left' == $component_format || 'half-right' == $component_format) ? 'half' : $component_format;

						if (has_post_thumbnail()) {
							the_post_thumbnail($thumbsize);
						}
						?>
						<div class="component-content">
							<h2 class="component-heading"><?php the_title(); ?></h2>
							<?php the_content(); ?>

							<?php
							if ( 'boxes' == $component_format ) {
								include 'boxes.php';
							}
							?>
						</div>
					</section>
					<?php 
				}
				/* Restore original Post Data */
				wp_reset_postdata();
			} else {
				// no posts found
			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
