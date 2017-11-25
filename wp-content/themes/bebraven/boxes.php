<?php 
/**
 * Template for boxes that display things like donor logos
 * This is called from index.php when the component format is 'boxes'
 */


$args = array(
	'post_type' => 'page',
	'post_parent' => $post->ID, 
	'post_status' => 'publish',
	'orderby' => 'menu_order',
	'order' => 'ASC',
);

$boxes_query = new WP_Query( $args );

if ( $boxes_query->have_posts() ) {
	?>
	<div class="mosaic">
		<?php
			while ( $boxes_query->have_posts() ) {
				$boxes_query->the_post();

				// Decide whether to display a logo or just the title of the post:
				$box_content = (has_post_thumbnail()) ? get_the_post_thumbnail($post->ID, 'logo') : get_the_title();

				?>
					<div class="mosaic-element">
						<div class="box">
							<div class="box-content"><?php echo $box_content; ?></div>
						</div>
					</div>
				<?php
			} // while
		?>
	</div>
	<?php
} //if
