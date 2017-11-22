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

	add_image_size( 'bz-featured-image', 2000, 1200, true );
	add_image_size( 'story', 1000, 1000, true );
	add_image_size( 'headshot', 372, 330, true );

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
	add_editor_style( array( 'assets/css/editor-style.css', bz_fonts_url() ) );

}
add_action( 'after_setup_theme', 'bz_setup' );

/**
 * Hide the admin bar on the user-facing end 
 */
add_filter('show_admin_bar', '__return_false');

/**
 * Register custom fonts.
 */
function bz_fonts_url() {
	$fonts_url = '';

	/*
	 * Translators: If there are characters in your language that are not
	 * supported by Libre Franklin, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$libre_franklin = _x( 'on', 'Libre Franklin font: on or off', 'bz' );

	if ( 'off' !== $libre_franklin ) {
		$font_families = array();

		$font_families[] = 'Libre Franklin:300,300i,400,400i,600,600i,800,800i';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Add preconnect for Google Fonts.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function bz_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'bz-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'bz_resource_hints', 10, 2 );

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

	// Load global js (requires jQuery)
	wp_enqueue_script( 'bz-global', get_theme_file_uri( '/assets/js/global.js' ), array( 'jquery' ), '1.0', true );


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
 * Add a custom formats selector, e.g. for the different home page components:
 */

// create a new taxonomy called 'format'
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
		'menu_name'         => __( 'Format', 'bz' ),
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
		'marquee' => 'Marquee',
		'half-left' => 'Pic and Story',
		'half-right' => 'Story and Pic',
		'centered' => 'Centered',
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

add_filter( 'wp_terms_checklist_args', 'bz_convert_formats_taxonomy_to_radio_checklist' );
