<?php 

/* 
 * Sidebar for the blog and its posts
 * See wordpress widget API for details https://codex.wordpress.org/Widgets_API
 */

if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
	<div id="sidebar" class="sidebar-blog primary-sidebar widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-blog' ); ?>
	</div><!-- #primary-sidebar -->
<?php endif; ?>