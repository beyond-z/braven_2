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
	add_image_size( 'post', 1500, 800, true );

	// Set the default content width.

	// Register navigation menu locations
	register_nav_menus( array(
		'top-primary'    => __( 'Top Main Nav', 'bz' ),
		'top-secondary'    => __( 'Top Secondary Nav', 'bz' ),
		//'social' => __( 'Social Links Menu', 'bz' ),
		'footer' => __( 'Footer Nav', 'bz' ),
		'legal' => __( 'Legal and Copyright Nav', 'bz' ),
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
		'id'            => 'sidebar-blog',
		'description'   => __( 'Add widgets here to appear in the sidebar on blog posts and archive pages.', 'bz' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Search', 'bz' ),
		'id'            => 'footer-search',
		'description'   => __( 'This is what enables search in the footer.', 'bz' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

}
add_action( 'widgets_init', 'bz_widgets_init' );

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
		// Block it to accidental editing from the dashboard by non-admins:
		'capabilities' => array(
			'manage_terms' => 'manage_categories',
			'edit_terms' => 'manage_categories',
			'delete_terms' => 'manage_categories',
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
		'default' => 'Default',
		'half-left' => 'Pic and Story',
		'half-right' => 'Story and Pic',
		'centered' => 'Centered',
		'boxes' => 'Boxes with centered heading',
		'boxes-left' => 'Boxes with left-aligned heading',
		'picbkg' => 'Picture Background',
		'full' => 'Full (no margin or header)',
		'tab' => 'Tab on parent page',
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

// Register Taxonomy Bio Type
// Taxonomy Key: biotype:
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

// Register Taxonomy Donor/Partner Category
// Taxonomy Key: donorpartnercategory
function bz_create_donorpartnercategory_tax() {

	$labels = array(
		'name'              => _x( 'Donor/Partner Categories', 'taxonomy general name', 'bz' ),
		'singular_name'     => _x( 'Donor/Partner Category', 'taxonomy singular name', 'bz' ),
		'search_items'      => __( 'Search Donor/Partner Categories', 'bz' ),
		'all_items'         => __( 'All Donor/Partner Categories', 'bz' ),
		'parent_item'       => __( 'Parent Donor/Partner Category', 'bz' ),
		'parent_item_colon' => __( 'Parent Donor/Partner Category:', 'bz' ),
		'edit_item'         => __( 'Edit Donor/Partner Category', 'bz' ),
		'update_item'       => __( 'Update Donor/Partner Category', 'bz' ),
		'add_new_item'      => __( 'Add New Donor/Partner Category', 'bz' ),
		'new_item_name'     => __( 'New Donor/Partner Category Name', 'bz' ),
		'menu_name'         => __( 'Donor/Partner Category', 'bz' ),
	);
	$args = array(
		'labels' => $labels,
		'description' => __( '', 'bz' ),
		'hierarchical' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_rest' => false,
		'show_tagcloud' => false,
		'show_in_quick_edit' => true,
		'show_admin_column' => true,
	);
	register_taxonomy( 'donorpartnercategory', array('donororpartner', ), $args );

}
add_action( 'init', 'bz_create_donorpartnercategory_tax' );


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

// Register Custom Post Type Donor or Partner
// Post Type Key: donororpartner
function bz_create_donororpartner_cpt() {

	$labels = array(
		'name' => __( 'Donors and Partners', 'Post Type General Name', 'bz' ),
		'singular_name' => __( 'Donor or Partner', 'Post Type Singular Name', 'bz' ),
		'menu_name' => __( 'Donors and Partners', 'bz' ),
		'name_admin_bar' => __( 'Donor or Partner', 'bz' ),
		'archives' => __( 'Donor or Partner Archives', 'bz' ),
		'attributes' => __( 'Donor or Partner Attributes', 'bz' ),
		'parent_item_colon' => __( 'Parent Donor or Partner:', 'bz' ),
		'all_items' => __( 'All Donors and Partners', 'bz' ),
		'add_new_item' => __( 'Add New Donor or Partner', 'bz' ),
		'add_new' => __( 'Add New', 'bz' ),
		'new_item' => __( 'New Donor or Partner', 'bz' ),
		'edit_item' => __( 'Edit Donor or Partner', 'bz' ),
		'update_item' => __( 'Update Donor or Partner', 'bz' ),
		'view_item' => __( 'View Donor or Partner', 'bz' ),
		'view_items' => __( 'View Donors and Partners', 'bz' ),
		'search_items' => __( 'Search Donor or Partner', 'bz' ),
		'not_found' => __( 'Not found', 'bz' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'bz' ),
		'featured_image' => __( 'Featured Image', 'bz' ),
		'set_featured_image' => __( 'Set featured image', 'bz' ),
		'remove_featured_image' => __( 'Remove featured image', 'bz' ),
		'use_featured_image' => __( 'Use as featured image', 'bz' ),
		'insert_into_item' => __( 'Insert into Donor or Partner', 'bz' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Donor or Partner', 'bz' ),
		'items_list' => __( 'Donors and Partners list', 'bz' ),
		'items_list_navigation' => __( 'Donors and Partners list navigation', 'bz' ),
		'filter_items_list' => __( 'Filter Donors and Partners list', 'bz' ),
	);
	$args = array(
		'label' => __( 'Donor or Partner', 'bz' ),
		'description' => __( '', 'bz' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-heart',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', ),
		'taxonomies' => array('donorpartnercategory', 'category', ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => false,
		'can_export' => true,
		'has_archive' => false,
		'hierarchical' => false,
		'exclude_from_search' => true,
		'show_in_rest' => true,
		'publicly_queryable' => false,
		'capability_type' => 'post',
	);
	register_post_type( 'donororpartner', $args );

}
add_action( 'init', 'bz_create_donororpartner_cpt', 0 );


/**
 * Make shortcodes to embed inline templates/functionality into a page's content
 */
 
// Create Shortcode include-bios
// Use the shortcode in a post like so: 
// [include-bios biotype="staff" category="product"] 
// or [include-bios biotype="fellow" category="chicago" limit="6" class="" orderby="post_title"]
// The 'limit' parameter controls how many results to show. -1 means no limit.

function bz_create_includebios_shortcode($atts) {
	
	// Attributes
	$atts = shortcode_atts(
		array(
			'biotype' => 'staff',
			'category' => '',
			'columns' => 3,
			'class' => '',
			'orderby' => 'menu_order post_title',
			'limit' => -1 // no limit
		),
		$atts,
		'include-bios'
	);

	// Attributes in var
	$biotype = $atts['biotype'];
	$category = $atts['category'];	
	$columns = $atts['columns'];
	$class = $atts['class'];
	$orderby = $atts['orderby'];
	$query_limit = $atts['limit'];

	//buffer this stuff so it doesn't just print it all on top:
	ob_start();

	// Query Arguments
	$args = array(
		'post_type' => array('bio'),
		'post_status' => array('publish'),
		'posts_per_page' => $query_limit,
		'order' => 'ASC',
		'orderby' => $orderby,
		'tax_query' => array(
			array(
				'taxonomy' => 'biotype',
				'field' => 'slug',
				'terms' => array($biotype),
			),
		),
		'category_name' => $category,
	);

	// The Query
	$bios = new WP_Query( $args );

	// Loop through results
	if ( $bios->have_posts() ) { 
		
		// figure out if there would be any leftovers if we divide by 3:
		$count = count($bios->posts);
		$modulo = ($count % 3);

		?>

		<div data-bz-columns="<?php echo $columns;?>" data-bz-count="<?php echo $count; ?>" data-bz-leftover="<?php echo $modulo; ?>" class="mosaic bios <?php echo $class . ' ' . $biotype . ' ' . $category;?>">
			<?php

			while ( $bios->have_posts() ) {
				$bios->the_post();
				include 'single-bio.php';
			}

			// Add placeholder empty items to complete the last row if needed:

			if($modulo && $columns) {
				for($i = 0; $i < ($columns - $modulo); $i++) {
					?>
					<article class="mosaic-element bio placeholder">
						&nbsp;
					</article>
					<?php
				}
			}

			?>
		</div>
		<?php
	} else {
		// None found
	}
	// Restore original Post Data
	wp_reset_postdata();

	// Now return the buffer:
    $result = ob_get_contents(); // get everything in to $result variable
    ob_end_clean();
    return $result;
	
}

add_shortcode( 'include-bios', 'bz_create_includebios_shortcode' );

// Create Shortcode to include sub page or donors/partners as boxes
// Use the shortcode in a post like so: 
// [include-subpages-as-boxes class='some-class other-class' columns='3']
function bz_create_includesubpages_shortcode($atts, $content = null) {
	
	// pass $post data for function's internal use:
	global $post;

	// Attributes
	$atts = shortcode_atts(
		array(
			'class' => '',
			'columns' => 3,
			'type' => 'page',
			'donorcats' => '',
			'category' => '',
		),
		$atts,
		'include-subpages-as-boxes'
	);

	$type = $atts['type'];
	$category = $atts['category'];
	$boxes_class = $atts['class'];
	$columns = $atts['columns'];
	$donorcats = $atts['donorcats'];

	$donor = ('donororpartner' == $atts['type']) ? 'donororpartner' : '';
	if ($donor && !empty($donorcats)) {
		$tax_query = array(
			array(
				'taxonomy' => 'donorpartnercategory',
				'field' => 'slug',
				'terms' => array($donorcats),
			),
		);
		$parent = null;
	} else {
		$tax_query = null;
		$parent = $post->ID;
	}

	//buffer the following stuff so it doesn't just print it all on top:
	ob_start();

	// Query Arguments
	$spargs = array(
		'post_parent' => $parent,
		'post_type' => array($type),
		'post_status' => array('publish'),
		'nopaging' => true,
		'order' => 'ASC',
		'orderby' => 'menu_order post_title', 
		'tax_query' => $tax_query,
		'category_name' => $category,
	);

	// The Query
	$subboxes = new WP_Query( $spargs );

	//print_r($subboxes);

	// Loop through results
	if ( $subboxes->have_posts() ) { 

		// figure out if there would be any leftovers if we divide by 3:
		$count = count($subboxes->posts);
		if ($columns) $modulo = $count % $columns;

		?>
		<div data-bz-columns="<?php echo $columns; ?>" data-bz-leftover="<?php echo $modulo; ?>" data-bz-count="<?php echo $count; ?>" class="mosaic boxes sub-boxes <?php echo $boxes_class . ' ' . $donor; ?>">
			<?php

			while ( $subboxes->have_posts() ) {
				$subboxes->the_post();
				if ($donor) {
					include 'single-logo-box.php';
				} else {
					include 'single-box.php';
				}
			}
			
			// Add placeholder empty items to complete the last row if needed:

			if($modulo && $columns) {
				for($i = 0; $i < ($columns - $modulo); $i++) {
					?>
					<article class="mosaic-element placeholder">
						&nbsp;
					</article>
					<?php
				}
			}

			?>
		</div>
		<?php
	} else {
		// None found
	}
	// Restore original Post Data
	wp_reset_postdata();

	// Now return the buffer:
    $result = ob_get_contents(); // get everything into $result variable
    ob_end_clean();
    return $result;
	
}

add_shortcode( 'include-subpages-as-boxes', 'bz_create_includesubpages_shortcode' );


// Create Shortcode to include posts from the blog by category
// Use the shortcode in a post like so: 
// [include-posts category="whatever,something" class="some-class another-class" limit="4"]
function bz_create_includeposts_shortcode($atts, $content = null) {
	
	// pass $post data for function's internal use:
	global $post;

	// Attributes
	$atts = shortcode_atts(
		array(
			'category' => '',
			'class' => '',
			'limit' => 3,
		),
		$atts,
		'include-posts'
	);

	$boxes_class = $atts['class'];	
	$category = $atts['category'];
	$limit = $atts['limit'];

	// Query Arguments
	$pargs = array(
		'post_type' => array('post'),
		'post_status' => array('publish'),
		'posts_per_page' => $limit, 
		'nopaging' => false,
		'paged' => 0,
		'order' => 'DESC',
		'orderby' => 'date',
		'category_name' => $category,
		/*'tax_query' => array(
			array(
				'taxonomy' => 'biotype',
				'field' => 'slug',
				'terms' => array($biotype),
			),
		),*/
		
	);

	// The Query
	$ps = new WP_Query( $pargs );

	// Loop through results
	if ( $ps->have_posts() ) { 

		?>
		<div class="mosaic boxes selected-posts">
			<?php

			while ( $ps->have_posts() ) {
				$ps->the_post();
				include 'single-box.php';
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

add_shortcode( 'include-posts', 'bz_create_includeposts_shortcode' );




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

        // blog page crumb:
        $blog_crumb = '<li class="item-blog"><a href="' . get_permalink( get_option( 'page_for_posts' ) ) . '" class="bread-link bread-blog bread-blog">'.__('Blog','bz').'</a></li>';
        
        if ( is_home() ) {
                          
            // Need to get the post ID from the header 
        	// b/c the main query on the "home" (blog) page 
        	// only contains blog posts...
        	global $container_ID;
        	echo '<li class="item-current item-' . $container_ID . '"><span class="bread-current bread-' . $container_ID . '"> ' . get_the_title($container_ID) . '</span></li>';

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
               
        } else if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
            
            // show it under /blog:
            echo $blog_crumb;

            echo '<li class="item-current item-archive"><span class="bread-current bread-archive">' . post_type_archive_title($prefix, false) . '</span></li>';
              
        } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
              
            /*
            // If post is a custom post type
            $post_type = get_post_type();
              
            // If it is a custom post type display name and link
            if($post_type != 'post') {
                  
                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
              
                echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
              
            }
            */

            $custom_tax_name = get_queried_object()->name;

            // show it under /blog:
            echo $blog_crumb;

            echo '<li class="item-current item-archive"><span class="bread-current bread-archive">' . $custom_tax_name . '</span></li>';
        
        } else if ( is_category() ) {
               
            // Category page

        	// show it under /blog:
        	echo $blog_crumb;

            echo '<li class="item-current item-cat"><span class="bread-current bread-cat">' . single_cat_title('', false) . '</span></li>';
               
        } else if ( is_tag() ) {
               
            // Tag page

        	// show it under /blog:
        	echo '<li class="item-blog"><a href="' . get_permalink( get_option( 'page_for_posts' ) ) . '" class="bread-link bread-blog bread-blog">'.__('Blog','bz').'</a></li>';

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
            
            // show it under /blog:
            echo $blog_crumb;

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
              
        	// show it under /blog:
        	echo $blog_crumb;

            // Year link
            echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
            echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
               
            // Month display
            echo '<li class="item-month item-month-' . get_the_time('m') . '"><span class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</span></li>';
               
        } else if ( is_year() ) {
             
            // show it under /blog:
        	echo $blog_crumb;

            // Display year archive
            echo '<li class="item-current item-current-' . get_the_time('Y') . '"><span class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</span></li>';
               
        } else if ( is_author() ) {
               
            // Author archive
               
            // Get the author information
            global $author;
            $userdata = get_userdata( $author );
               
            // Display author name
            echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><span class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</span></li>';
           
        } else if ( is_single() ) {
        	// A single blog post:
        	// show it under /blog:
            echo $blog_crumb;


    	} else if ( is_search() ) {
           
            // Search results page
            echo '<li class="item-current item-current-' . get_search_query() . '"><span class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</span></li>';
           
        } else if ( is_404() ) {
               
            // 404 page
            echo '<li>' . 'Error 404' . '</li>';
        } 
       

       	if ( get_query_var('paged') ) {

            // Paginated archives
            echo '<li class="item-current item-current-' . get_query_var('paged') . '"><span class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">'.__('Page') . ' ' . get_query_var('paged') . '</span></li>';
               
        }


        echo '</ul>';
           
    }
       
}


/**
 * Get a nicely formatted string for the publication date.
 */
function bz_get_publish_date() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	$time_string = sprintf( $time_string,
		get_the_date( DATE_W3C ),
		get_the_date()
	);
	// Preface it with 'Posted on' for screen readers and return it.
	return '<span class="screen-reader-text">' . _x( 'Posted on', 'post date', 'bz' ) . '</span>' . $time_string;
}

/**
 * Set up pagination buttons for search results, lists of posts, etc.:
 */

function bz_show_pagination() {
	the_posts_pagination( array(
		'prev_text' => '<span class="prev">' . __( 'Previous', 'bz' ) . '</span>',
		'next_text' => '<span class="next">' . __( 'Next', 'bz' ) . '</span>',
		'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'bz' ) . ' </span>',
	) );
}


/**
 * Set up a search filter so results don't include stuff like images etc.:
 */

function bz_setup_search_filter($query) {
 
    if ($query->is_search && !is_admin() ) {
        $query->set('post_type',array('post','page'));
    }
 
return $query;
}
 
add_filter('pre_get_posts','bz_setup_search_filter');

/*
 * Edit buttons on the front end for admin:
 */

function bz_show_edit_link() {
	global $post;
	//if ( current_user_can('editor') || current_user_can('administrator') ) {
		?>
			<a class="edit-link" href="/wp-admin/post.php?post=<?php echo $post->ID;?>&action=edit">
				<?php echo __('Edit','bz');?>
			</a>
		<?php
	//}
}
