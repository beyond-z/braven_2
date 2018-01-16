<div class="wrap">

	<?php bz_custom_breadcrumbs(); ?>
	<div id="primary" class="content-area">
		<?php //get_search_form(); ?>
		<main id="main" class="site-main" role="main">

			<?php
			/*
			 * Template for message to show when there are no results for a search, archive, or 404.
			 */

			// Show the sidebar:
			get_sidebar();


			if ( have_posts() ) {
				?>

				<div class="results-container">
					<span class="search-meta">
						<?php echo $wp_query->found_posts . __(' results found'); ?>
					</span>

					<?php
					// Run the loop for the search to output the results:
					while ( have_posts() ) {
						// Set the current post's data into $post:
						the_post();
						
						// Figure out how to link to the result:
						if( 'post' != $post->post_type ) { 
							
							// Get an array of Ancestors and Parents if they exist */
							$parents = get_post_ancestors( $post->ID );
						    
						    // Get the top Level page->ID (last in the array)
						    // Array base is 0 so we're looking for count-1 
							$top_id = ($parents) ? $parents[count($parents)-1]: $post->ID;
							
							// Get the parent post's url, unless it's the home container:
							$frontpage_id = get_option( 'page_on_front' );

							if ($frontpage_id == $top_id) {
								$permalink = '/';
							} else {
								$permalink = get_the_permalink( $top_id );	
							}
							
							// Add the result's slug as a hash to the url:
							$permalink .= '#'.$post->post_name;
						} else {

							// if the result is a post:
							$permalink = get_the_permalink();
						}

						// Print a search result:
						?>
						<section class="component post result">
							<div class="component-content">
								<h2 class="component-heading"><a href="<?php echo $permalink;?>"><?php the_title();?></a></h2>
								<?php the_excerpt();?>
							</div>
						</section>
						<?php
					} // END while
					?>
				</div>
				<?php bz_show_pagination(); 
			} else { ?>

				<section class="component default not-found no-results">
					<div class="component-content">

						<p><?php 
							// bring in the error message from a page (so staff can edit the message on their own):
							$message = get_page_by_path('404-message');
							echo apply_filters('the_content', $message->post_content);

						//_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'bz' ); ?></p>

					</div><!-- .page-content -->
				</section><!-- .no-results -->
				<?php 
			}

			?>


		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->