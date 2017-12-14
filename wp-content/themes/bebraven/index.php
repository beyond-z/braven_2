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

			/* In order to accomodate tabbed sub-navigation (e.g. on region pages)
			 * we collect all the content into the following vars and then print it.
			 */

			// Start building the parts of the page:
			$tabs_menu = '<nav class="tabs_menu"><a href="#parent-tab">' . $post->post_title . '</a>';
			$parent_content = '<div id="parent-tab" class="tab parent-tab">';
			$tabs_content = '';

			global $container_ID; // $container_ID is defined in the header.php template 
			
			$args = array(
				'post_type' => 'page',
				'post_parent' => $container_ID, 
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC',
			);
			
			$components_query = new WP_Query( $args );

			// Loop through the returned components (sub-pages of this page):
			if ( $components_query->have_posts() ) {
				
				while ( $components_query->have_posts() ) {
					$components_query->the_post();
					$component_format = (wp_get_post_terms($post->ID, 'format')) ? wp_get_post_terms($post->ID, 'format')[0]->slug : '';

					// NOTE: If you're looking for how the bios are generated, search 
					// for include-bios in functions.php and for a file called single-bio.php


					// If this sub-page is supposed to show up as a tab with its own components:
					if ('tab' == $component_format) {

						$tabs_menu .= '<a href="#' . $post->post_name . '">' . $post->post_title . '</a>';
						$tabs_content = '<div id="' . $post->post_name . '" class="tab sub-page-tab ' . $post->post_name . '">';


						$args = array(
							'post_type' => 'page',
							'post_parent' => $post->ID, 
							'post_status' => 'publish',
							'orderby' => 'menu_order',
							'order' => 'ASC',
						);
			
						$tab_components_query = new WP_Query( $args );

						while ( $tab_components_query->have_posts() ) {
							$tab_components_query->the_post();

							$component_format = (wp_get_post_terms($post->ID, 'format')) ? wp_get_post_terms($post->ID, 'format')[0]->slug : '';

							// Run the data through the content template  
							// and add that to the contant var 
							// (via buffer so it doesn't print prematurely):

							ob_start();
							include('content.php');
							echo ob_get_length();
							$tabs_content .= ob_get_contents();
							//ob_end_clean(); 
						}

						$tabs_content .= '</div>';

						/* Restore original Post Data */
						wp_reset_postdata();
					
					} else {
						// If this content isn't part of a tab, it must be for the parent page, so let's save it there (via a buffer again):

						ob_start();
						include('content.php');
						$parent_content .= ob_get_contents(); 
						ob_end_clean(); 

					}
				} // end while

				echo $tabs_menu . '</nav>';
				echo $parent_content . '</div>';
				echo $tabs_content;

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
