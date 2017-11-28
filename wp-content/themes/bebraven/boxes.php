<?php 
/**
 * Template for boxes that display things like donor logos
 * This is called from index.php when the component format is 'boxes'
 */


$boxesargs = array(
	'post_type' => 'page',
	'post_parent' => $post->ID, 
	'post_status' => 'publish',
	'orderby' => 'menu_order',
	'order' => 'ASC',
);

$boxes_query = new WP_Query( $boxesargs );

if ( $boxes_query->have_posts() ) {


	// We use three colums but flex box leaves ugly gaps when used with space-between, so let's indicate when there are fewer than 3 items on the last row.
	$leftover = count($boxes_query->posts) % 3;
	$leftover_class = ($leftover) ? 'leftover-'.$leftover : '';

	?>
	<div class="mosaic <?php echo $leftover_class;?>">
		<?php
			while ( $boxes_query->have_posts() ) {
				$boxes_query->the_post();

				include 'single-box.php';

			} // while
		?>
	</div>
	<?php
} //if
