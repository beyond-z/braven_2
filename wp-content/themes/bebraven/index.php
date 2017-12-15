<?php
/**
 * The main template file.
 *
 */


get_header(); 



?>

<div class="wrap">

	<?php bz_custom_breadcrumbs(); ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">


			<?php 

			if(is_home()) {

				/* 
				 *Is this the blog page?
				 *
				 */
				//print_r($wp_query);
				if ( have_posts() ) {
					
					while ( have_posts() ) {

						the_post();

						the_title();

					}

				}

			} else {

				/* In order to accomodate tabbed sub-navigation (e.g. on region pages)
				 * we collect all the content into the following vars and then print it.
				 */

				// Start building the parts of the page:
				$tabs_menu = '<nav id="tabs-menu" class="tabs-menu"><a href="#parent-tab" class="active">' . $post->post_title . '</a>';
				$parent_content = '';
				$tabs_content = '';

				global $container_ID; // $container_ID is defined in the header.php template 
				
				$args = array(
					'post_type' => 'page',
					'post_parent' => $container_ID, 
					'post_status' => 'publish',
					'orderby' => 'menu_order',
					'nopaging' => 'true',
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

							// start a buffer for this tab's content
							ob_start();

							$tabs_menu .= '<a href="#' . $post->post_name . '">' . $post->post_title . '</a>';


							echo '<div id="' . $post->post_name . '" class="tab sub-page-tab ' . $post->post_name . '">';

							$args = array(
								'post_type' => 'page',
								'post_parent' => $post->ID, 
								'post_status' => 'publish',
								'orderby' => 'menu_order',
								'nopaging' => 'true',
								'order' => 'ASC',
							);
				
							$tab_components_query = new WP_Query( $args );

							while ( $tab_components_query->have_posts() ) {
								$tab_components_query->the_post();

								$component_format = (wp_get_post_terms($post->ID, 'format')) ? wp_get_post_terms($post->ID, 'format')[0]->slug : '';

								// Run the data through the content template  
								// and add that to the contant var 
								// (via buffer so it doesn't print prematurely):

								include('content.php');
							}

							echo '</div>';


							$tabs_content .= ob_get_clean();


							/* Restore original Post Data */
							wp_reset_postdata();
						
						} else {
							// If this content isn't part of a tab, it must be for the parent page, so let's save it there (via a buffer again):

							ob_start();
							include('content.php');
							$parent_content .= ob_get_clean(); 

						}
					} // end while

					if ($tabs_content) { 
						
						echo $tabs_menu . '</nav>';
						?>
						<div id="parent-tab" class="tab parent-tab">
							<?php echo $parent_content;?>
						</div>
						<?php echo $tabs_content;
					} else {
						echo $parent_content;
					}

					/* Restore original Post Data */
					wp_reset_postdata();
				} else {
					// no posts found
				}

			} // END if( is_home() ) ELSE
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();
