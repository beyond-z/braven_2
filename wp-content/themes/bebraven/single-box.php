<?php 
/**
 * Template for a single sub-page "square" and its content.
 * This is called from functions.php where a query is generated by a shortcode. 
 */

global $post;


// If the post has only a URL in the body content, let's make the "read more" link redirect to it instead of the single post view:
if (filter_var($post->post_content, FILTER_VALIDATE_URL) !== false) {
	$permalink = $post->post_content;
	$external = ' target="_blank"';
} else {
	$permalink = get_the_permalink();
	$external = '';
}

$post_title = get_the_title();

// Make the title a link (only in case of posts):
$post_title = ('post' == $post->post_type) ? '<a href="' . $permalink . '" '. $external . '>'.$post_title.'</a>' : $post_title;


// Decide whether to display a logo or just the title of the post:
$box_title_or_image = (has_post_thumbnail() && 'post' != $post->post_type) ? get_the_post_thumbnail($post->ID, 'logo', array ('title' => $post_title) ) : '<h3>'.$post_title.'</h3>';



// Indicate when the box has body content so we can add a visual cue in case it's only visible on hover/click
$content = get_the_content();
$has_more = ($content) ? 'has-content' : 'no-content';


?>
<article class="mosaic-element <?php echo $has_more; ?>">
	<div class="box">
		<div class="box-content">
			<?php 
			
			echo $box_title_or_image;

			?>
			<div class="excerpt"><?php the_excerpt(); ?></div>
			<?php

			if ( $content && 'page' == $post->post_type ) {
				?>
					<div class="box-text">
						<?php echo apply_filters('the_content', $content); ?>
					</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php bz_show_edit_link(); ?>
</article>