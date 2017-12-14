<?php

/* 
 * This template creates a content section of a page. 
 */

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
		<?php if ( 'picbkg' != $component_format ) { ?>
			<h2 class="component-heading"><?php the_title(); ?></h2>
		<?php } ?>
		<?php the_content(); ?>
	</div>
</section>