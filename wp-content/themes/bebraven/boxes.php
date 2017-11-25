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
				?>
					<div class="mosaic-element">
						<div class="box">
							<div class="box-content"><?php the_title();?></div>
						</div>
					</div>
				<?php
			} // while
		?>
	</div>
	<?php
} //if
