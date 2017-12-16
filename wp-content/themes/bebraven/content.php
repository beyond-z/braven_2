<?php

/* 
 * This template creates a content section of a page. 
 */

global $component_format;

// Get the thumbnail, if there is one.

if (has_post_thumbnail()) {
	// Define a thumbsize based on the post's format
	$thumbsize = ('half-left' == $component_format || 'half-right' == $component_format) ? 'half' : $component_format;
	$thumb = get_the_post_thumbnail($post->ID, $thumbsize);
	$hasthumb = 'has-thumb';
} else {
	$thumb = '';
	$hasthumb = 'no-thumb';
}

// If it's the blog page, we want to set the title so it link to the post:
$title = get_the_title();
if ( is_home() ) {
	$title = '<a href="' . get_the_permalink() . '">' . $title . '</a>';
}

?>
<section id="<?php echo $post->post_name; ?>" class="component <?php 
	echo $post->post_name . ' ' 
		 . $component_format . ' '
		 . $hasthumb;

	?>">
	<?php 

	// Display the image above the content, unless it's a blog post:
	if ( 'post' != $component_format ) {
		echo $thumb;
	} 

	?>
	<div class="component-content">
		<?php 
		
		// Omit the title from picbkg items:
		if ( 'picbkg' != $component_format ) { 
			?>
				<h2 class="component-heading"><?php echo $title; ?></h2>
			<?php 
		} 

		// Show a date for posts (full content otherwise),
		if ( 'post' == $component_format ) { 
			?>
			<span class="date"><?php echo bz_get_publish_date(); ?></span>
			<div class="post-img"><?php echo $thumb;?></div>
			<?php 
			// and only an excerpt+link if it's one of the posts on a list:
			if ( is_home() || is_archive() || is_search() || is_404() ) { 
				the_excerpt(); 
				?>
				<a class="read-more" href="<?php the_permalink(); ?>">
					<?php echo __('Read the full story', 'bz');?>
				</a>
				<?php
			} else {
				// if it's not on a list view, show the full content:
				the_content();
			}
		} else {
			// If not a post:
			the_content(); 
		}

		?>
	</div>
</section>