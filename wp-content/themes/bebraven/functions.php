<?php
/**
 * BeBraven2018 functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage BeBraven2018
 * @since 1.0
 */

/**
 * This theme only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function bz_setup() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	add_image_size( 'marquee', 2000, 1200, true );
	add_image_size( 'half', 1000, 1000, true );
	add_image_size( 'headshot', 400, 400, true );
	add_image_size( 'logo', 400, 400, false );

	// Set the default content width.

	// Register navigation menu locations
	register_nav_menus( array(
		'top-primary'    => __( 'Top Main Menu', 'bz' ),
		'top-secondary'    => __( 'Top Secondary Menu', 'bz' ),
		'social' => __( 'Social Links Menu', 'bz' ),
		'footer' => __( 'Footer Menu', 'bz' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, and column width.
 	 */
	if ( function_exists('bz_fonts_url') )
		add_editor_style( array( 'assets/css/editor-style.css', bz_fonts_url() ) );

	/*
	 * Add excerpts to pages (used for marquee headings)
	 */
	add_post_type_support( 'page', 'excerpt' );

}
add_action( 'after_setup_theme', 'bz_setup' );

/**
 * Hide the admin bar on the user-facing end 
 */
add_filter('show_admin_bar', '__return_false');

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function bz_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'bz' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'bz' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 1', 'bz' ),
		'id'            => 'footer-1',
		'description'   => __( 'Add widgets here to appear in your footer.', 'bz' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'bz' ),
		'id'            => 'footer-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'bz' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'bz_widgets_init' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $link Link to single post/page.
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function bz_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf( '<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'bz' ), get_the_title( get_the_ID() ) )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'bz_excerpt_more' );

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Seventeen 1.0
 */
function bz_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'bz_javascript_detection', 0 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function bz_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'bz_pingback_header' );

/**
 * Enqueue scripts and styles.
 */
function bz_scripts() {
	// Add custom fonts, used in the main stylesheet.
	if ( function_exists('bz_fonts_url') )
		wp_enqueue_style( 'bz-fonts', bz_fonts_url(), array(), null );

	// Theme stylesheet.
	wp_enqueue_style( 'bz-style', get_stylesheet_uri() );

	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if ( is_customize_preview() ) {
		wp_enqueue_style( 'bz-ie9', get_theme_file_uri( '/assets/css/ie9.css' ), array( 'bz-style' ), '1.0' );
		wp_style_add_data( 'bz-ie9', 'conditional', 'IE 9' );
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'bz-ie8', get_theme_file_uri( '/assets/css/ie8.css' ), array( 'bz-style' ), '1.0' );
	wp_style_add_data( 'bz-ie8', 'conditional', 'lt IE 9' );

	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_theme_file_uri( '/assets/js/html5.js' ), array(), '3.7.3' );
	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	// Load custom functions js (requires jQuery)
	wp_enqueue_script( 'bz-js-functions', get_theme_file_uri( '/functions.js' ), array( 'jquery' ), '1.0', true );


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bz_scripts' );

/* Custom template tags and functions: */

function bz_get_id_by_slug($page_slug) {
	$page = get_page_by_path($page_slug);
	if ($page) {
		return $page->ID;
	} else {
		return null;
	}
} 

/**
 * Add a custom formats selector, e.g. for the different home page components
 * and populate some initial values.
 * Then convert the dashboard UI from checklist to radiobutton.
 */

// create and register a new taxonomy called 'format'
function bz_define_custom_post_formats_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Formats', 'taxonomy general name', 'bz' ),
		'singular_name'     => _x( 'Format', 'taxonomy singular name', 'bz' ),
		'search_items'      => __( 'Search Formats', 'bz' ),
		'all_items'         => __( 'All Formats', 'bz' ),
		'parent_item'       => __( 'Parent Format', 'bz' ),
		'parent_item_colon' => __( 'Parent Format:', 'bz' ),
		'edit_item'         => __( 'Edit Format', 'bz' ),
		'update_item'       => __( 'Update Format', 'bz' ),
		'add_new_item'      => __( 'Add New Format', 'bz' ),
		'new_item_name'     => __( 'New Format Name', 'bz' ),
		'menu_name'         => __( 'Formats', 'bz' ),
	);

	$args = array(
		'hierarchical'      => true, // we'll turn the checkboxes into radios later
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'format' ),
		// Block it to accidental editing from the dashboard:
		'capabilities' => array(
			'manage_terms' => '',
			'edit_terms' => '',
			'delete_terms' => '',
			'assign_terms' => 'edit_posts'
		),
		'public' => true,
		'show_in_nav_menus' => false,
		'show_tagcloud' => false,
	);
	register_taxonomy( 'format', array( 'page' ), $args ); // our new 'format' taxonomy
}
add_action( 'init', 'bz_define_custom_post_formats_taxonomies', 0 );

// programmatically create a few format terms
	
function bz_populate_custom_formats() { 
	$formats_to_create = array(
		'default' => 'Default (full)',
		'half-left' => 'Pic and Story',
		'half-right' => 'Story and Pic',
		'centered' => 'Centered',
		'mosaic-three' => '3-wide Mosaic',
		'boxes' => 'Boxes (e.g. for donors)',
		'picbkg' => 'Picture Background',
	);
	foreach($formats_to_create as $slug => $title) {
		wp_insert_term(
			$title,
			'format',
			array(
			  'description'	=> '',
			  'slug' 		=> $slug
			)
		);
	}
}
add_action( 'init', 'bz_populate_custom_formats' );

// make sure there's a default Format type and that it's chosen if they didn't choose one
function bz_default_format_term( $post_id, $post ) {
    if ( 'publish' === $post->post_status ) {
        $defaults = array(
            'format' => 'default'
            );
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( (array) $taxonomies as $taxonomy ) {
            $terms = wp_get_post_terms( $post_id, $taxonomy );
            if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
                wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
            }
        }
    }
}
add_action( 'save_post', 'bz_default_format_term', 100, 2 );


// replace checkboxes for the format taxonomy with radio buttons and a custom meta box
function bz_convert_formats_taxonomy_to_radio_checklist( $args ) {
    if ( ! empty( $args['taxonomy'] ) && $args['taxonomy'] === 'format' ) {
        if ( empty( $args['walker'] ) || is_a( $args['walker'], 'Walker' ) ) { // Don't override 3rd party walkers.
            if ( ! class_exists( 'BZ_Walker_Category_Radio_Checklist' ) ) {
                class BZ_Walker_Category_Radio_Checklist extends Walker_Category_Checklist {
                    function walk( $elements, $max_depth, $args = array() ) {
                        $output = parent::walk( $elements, $max_depth, $args );
                        $output = str_replace(
                            array( 'type="checkbox"', "type='checkbox'" ),
                            array( 'type="radio"', "type='radio'" ),
                            $output
                        );
                        return $output;
                    }
                }
            }
            $args['walker'] = new BZ_Walker_Category_Radio_Checklist;
        }
    }
    return $args;
}

/**
 * Add a taxonomy for bio types, e.g. board member, team member, etc.
 * and populate some initial values
 */

// Create and register a new taxonomy called 'biotype':
function bz_define_biotypes() {
	$labels = array(
		'name'              => _x( 'Bio Types', 'taxonomy general name', 'bz' ),
		'singular_name'     => _x( 'Bio Type', 'taxonomy singular name', 'bz' ),
		'search_items'      => __( 'Search Bio Types', 'bz' ),
		'all_items'         => __( 'All Bio Types', 'bz' ),
		'parent_item'       => __( 'Parent Item', 'bz' ),
		'parent_item_colon' => __( 'Parent Item:', 'bz' ),
		'edit_item'         => __( 'Edit Bio Type', 'bz' ),
		'update_item'       => __( 'Update Bio Type', 'bz' ),
		'add_new_item'      => __( 'Add New Bio Type', 'bz' ),
		'new_item_name'     => __( 'New Bio Type Name', 'bz' ),
		'menu_name'         => __( 'Bio Types', 'bz' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'biotype' ),
		'public' 			=> true,
		'show_in_nav_menus' => true,
		'show_tagcloud' 	=> false,
	);
	register_taxonomy( 'biotype', array( 'bio' ), $args );
}
add_action( 'init', 'bz_define_biotypes', 0 );

// programmatically create a few bio types
	
function bz_populate_biotypes() { 
	$biotypes_to_create = array(
		'board' => 'Board Member',
		'staff' => 'Staff Member',
		'fellow' => 'Featured Fellow',
		'paf' => 'Featured PAF',
		'volunteer' => 'Featured Volunteer'
	);
	foreach($biotypes_to_create as $slug => $title) {
		wp_insert_term(
			$title,
			'biotype',
			array(
			  'description'	=> '',
			  'slug' 		=> $slug
			)
		);
	}
}
add_action( 'init', 'bz_populate_biotypes' );

add_filter( 'wp_terms_checklist_args', 'bz_convert_formats_taxonomy_to_radio_checklist' );

/**
 * Generate custom post types
 */
 
// Bios - for team members, board members, fellows, etc.
function bz_create_bio_cpt() {

	$labels = array(
		'name' => __( 'Bios', 'Post Type General Name', 'bz' ),
		'singular_name' => __( 'Bio', 'Post Type Singular Name', 'bz' ),
		'menu_name' => __( 'Bios', 'bz' ),
		'name_admin_bar' => __( 'Bio', 'bz' ),
		'archives' => __( 'Bio Archives', 'bz' ),
		'attributes' => __( 'Bio Attributes', 'bz' ),
		'parent_item_colon' => __( 'Parent Bio:', 'bz' ),
		'all_items' => __( 'All Bios', 'bz' ),
		'add_new_item' => __( 'Add New Bio', 'bz' ),
		'add_new' => __( 'Add New', 'bz' ),
		'new_item' => __( 'New Bio', 'bz' ),
		'edit_item' => __( 'Edit Bio', 'bz' ),
		'update_item' => __( 'Update Bio', 'bz' ),
		'view_item' => __( 'View Bio', 'bz' ),
		'view_items' => __( 'View Bios', 'bz' ),
		'search_items' => __( 'Search Bio', 'bz' ),
		'not_found' => __( 'Not found', 'bz' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'bz' ),
		'featured_image' => __( 'Featured Image', 'bz' ),
		'set_featured_image' => __( 'Set featured image', 'bz' ),
		'remove_featured_image' => __( 'Remove featured image', 'bz' ),
		'use_featured_image' => __( 'Use as featured image', 'bz' ),
		'insert_into_item' => __( 'Insert into Bio', 'bz' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Bio', 'bz' ),
		'items_list' => __( 'Bios list', 'bz' ),
		'items_list_navigation' => __( 'Bios list navigation', 'bz' ),
		'filter_items_list' => __( 'Filter Bios list', 'bz' ),
	);
	$args = array(
		'label' => __( 'Bio', 'bz' ),
		'description' => __( '', 'bz' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-admin-users',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields', ),
		'taxonomies' => array('biotype', 'category', 'post_tag'),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'bio', $args );

}
add_action( 'init', 'bz_create_bio_cpt', 0 );

/**
 * Make a shortcode that embeds bios lists into pages
 */
 
// Create Shortcode include-bios
// Use the shortcode: [include-bios biotype="staff" category=""]
function create_includebios_shortcode($atts) {
	
	// Attributes
	$atts = shortcode_atts(
		array(
			'biotype' => 'staff',
			'category' => '',
		),
		$atts,
		'include-bios'
	);
	// Attributes in var
	$biotype = $atts['biotype'];
	$category = $atts['category'];
	// Query Arguments
	$args = array(
		'post_type' => array('bio'),
		'post_status' => array('publish'),
		'posts_per_page' => -1, // no limit
		'nopaging' => true,
		'order' => 'ASC',
		'orderby' => 'menu_order',
		'tax_query' => array(
			array(
				'taxonomy' => 'biotype',
				'field' => 'slug',
				'terms' => array($biotype),
			),
		),
		'category_name' => $category,
	);

	if (!empty($category)) {
		//$args['category_name'] = $category;
	}

	// The Query
	$bios = new WP_Query( $args );

	// Loop through results
	if ( $bios->have_posts() ) { 
		?>
		<div class="mosaic bios <?php echo $biotype . ' ' . $category;?>">
			<?php

			while ( $bios->have_posts() ) {
				$bios->the_post();
				include 'single-bio.php';
			}

			// add placeholder bios until the total divides by 3:
			$totalbios = count($bios->posts);
			$missing = 3 - ($totalbios % 3);
			for ($i = 0; $i < $missing; $i++) {
				$placeholder = 'placeholder';
				include 'single-bio.php';
			}
			
			?>
		</div>
		<?php
	} else {
		// None found
	}
	// Restore original Post Data
	wp_reset_postdata();
	
	
}
add_shortcode( 'include-bios', 'create_includebios_shortcode' );

/*
 * Breadcrumbs
 */
function bz_custom_breadcrumbs() {
       
    // Settings
    $breadcrums_id      = 'breadcrumbs';
    $breadcrums_class   = 'breadcrumbs';
    $home_title         = __('Home','bz');
       
    // Get the query & post information
    global	$post,
    		$wp_query;
       
    // Do not display on the homepage
    if ( !is_front_page() ) {
       
        // Build the breadcrums
        echo '<ul id="' . $breadcrums_id . '" class="' . $breadcrums_class . '">';
           
        // Home page crumb
        echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
        
        if ( is_single() ) {
              
            // If post is a custom post type
            $post_type = get_post_type();
              
            // If it is a custom post type display name and link
            if($post_type != 'post') {
                  
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
              
                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                echo '<li class="separator"> ' . $separator . ' </li>';
              
            }
            
            /* 
            // Get post category info
            $category = get_the_category();
             
            if(!empty($category)) {
              
                // Get last category post is in
                $last_category = end(array_values($category));
                  
                // Get parent any categories and create array
                $get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
                $cat_parents = explode(',',$get_cat_parents);
                  
                // Loop through parent categories and store in variable $cat_display
                $cat_display = '';
                foreach($cat_parents as $parents) {
                    $cat_display .= '<li class="item-cat">'.$parents.'</li>';
                    $cat_display .= '<li class="separator"> ' . $separator . ' </li>';
                }
             
            }
            */

            /*
            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($custom_taxonomy);
            if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
                   
                $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
                $cat_id         = $taxonomy_terms[0]->term_id;
                $cat_nicename   = $taxonomy_terms[0]->slug;
                $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name       = $taxonomy_terms[0]->name;
               
            }
              
            // Check if the post is in a category
            if(!empty($last_category)) {
                echo $cat_display;
                echo '<li class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</span></li>';
                  
            // Else if post is in a custom taxonomy
            } else if(!empty($cat_id)) {
                  
                echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
                echo '<li class="separator"> ' . $separator . ' </li>';
                echo '<li class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</span></li>';

            } else {
                  
                echo '<li class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</span></li>';
                  
            } */
              
        } else if ( is_page() ) {
               
            // Standard page
            if( $post->post_parent ){
                   
                // If child page, get parents 
                $ancestors = get_post_ancestors( $post->ID );
                   
                // Get parents in the right order
                $ancestors = array_reverse($ancestors);
                   
                // Parent page loop
                if ( !isset( $parents ) ) $parents = null;
                foreach ( $ancestors as $ancestor ) {
                    $parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
                }
                   
                // Display parent pages
                echo $parents;
                   
                // Current page
                echo '<li class="item-current item-' . $post->ID . '"><span title="' . get_the_title() . '"> ' . get_the_title() . '</span></li>';
                   
            } else {
                   
                // Just display current page if not parents
                echo '<li class="item-current item-' . $post->ID . '"><span class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</span></li>';
                   
            }
               
        } /*else if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
              
            echo '<li class="item-current item-archive"><span class="bread-current bread-archive">' . post_type_archive_title($prefix, false) . '</span></li>';
              
        } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
              
            // If post is a custom post type
            $post_type = get_post_type();
              
            // If it is a custom post type display name and link
            if($post_type != 'post') {
                  
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
              
                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
                echo '<li class="separator"></li>';
              
            }
              
            $custom_tax_name = get_queried_object()->name;
            echo '<li class="item-current item-archive"><span class="bread-current bread-archive">' . $custom_tax_name . '</span></li>';
        
        } else if ( is_category() ) {
               
            // Category page
            echo '<li class="item-current item-cat"><span class="bread-current bread-cat">' . single_cat_title('', false) . '</span></li>';
               
        } else if ( is_tag() ) {
               
            // Tag page
               
            // Get tag information
            $term_id        = get_query_var('tag_id');
            $taxonomy       = 'post_tag';
            $args           = 'include=' . $term_id;
            $terms          = get_terms( $taxonomy, $args );
            $get_term_id    = $terms[0]->term_id;
            $get_term_slug  = $terms[0]->slug;
            $get_term_name  = $terms[0]->name;
               
            // Display the tag name
            echo '<li class="item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '"><span class="bread-current bread-tag-' . $get_term_id . ' bread-tag-' . $get_term_slug . '">' . $get_term_name . '</span></li>';
           
        } elseif ( is_day() ) {
               
            // Day archive
               
            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
               
            // Month link
            echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';
               
            // Day display
            echo '<li class="item-current item-' . get_the_time('j') . '"><span class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</span></li>';
               
        } else if ( is_month() ) {
               
            // Month Archive
               
            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
               
            // Month display
            echo '<li class="item-month item-month-' . get_the_time('m') . '"><span class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</span></li>';
               
        } else if ( is_year() ) {
               
            // Display year archive
            echo '<li class="item-current item-current-' . get_the_time('Y') . '"><span class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</span></li>';
               
        } else if ( is_author() ) {
               
            // Auhor archive
               
            // Get the author information
            global $author;
            $userdata = get_userdata( $author );
               
            // Display author name
            echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><span class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</span></li>';
           
        } else if ( get_query_var('paged') ) {
               
            // Paginated archives
            echo '<li class="item-current item-current-' . get_query_var('paged') . '"><span class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">'.__('Page') . ' ' . get_query_var('paged') . '</span></li>';
               
        } else if ( is_search() ) {
           
            // Search results page
            echo '<li class="item-current item-current-' . get_search_query() . '"><span class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</span></li>';
           
        } */ elseif ( is_404() ) {
               
            // 404 page
            echo '<li>' . 'Error 404' . '</li>';
        }
       
        echo '</ul>';
           
    }
       
}


