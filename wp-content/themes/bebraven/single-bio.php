<?php
/**
 * The template for displaying single bios 
 */

$component_format = 'bio';

get_header(); ?>

<div class="wrap template-single">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php

			// Bring in the post's data from the main query:
			while ( have_posts() ) : the_post();
				

				// Get the person's image, if there is one.

				if (has_post_thumbnail()) {
					// Define a thumbsize based on the post's format
					$thumbsize = ('half');
					$thumb = get_the_post_thumbnail($post->ID, $thumbsize);
					$hasthumb = 'has-thumb';
				} else {
					$thumb = '';
					$hasthumb = 'no-thumb';
				}

				?>
				<section id="<?php echo $post->post_name; ?>" class="component <?php 
					echo $post->post_name . ' ' 
						 . $component_format . ' '
						 . $hasthumb;
					?>">
					<?php echo $thumb; ?>
					<div class="component-content">
						
						<?php the_content(); ?>

						<?php 
						// Show edit link to admins:
						bz_show_edit_link();
						?>

						<h2 class="component-heading"><?php echo $title; ?></h2>

					</div>
				</section>

			<?php endwhile; ?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer(); ?>