<?php
if ( ! function_exists( 'pg_starter_setup' ) ) :

function pg_starter_setup() {


    /*
    * Make theme available for translation.
    * Translations can be filed in the /languages/ directory.
    */
    /* Pinegrow generated Load Text Domain Begin */

    load_theme_textdomain( 'pg_starter', get_template_directory() . '/languages' );

    /* Pinegrow generated Load Text Domain End */

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    /*
     * Let WordPress manage the document title.
     */
    add_theme_support( 'title-tag' );

    /*
     * Enable support for Post Thumbnails on posts and pages.
     */
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 825, 510, true );

    // Add menus.
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'pg_starter' ),
        'social'  => __( 'Social Links Menu', 'pg_starter' ),
    ) );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ) );

    /*
     * Enable support for Post Formats.
     */
    add_theme_support( 'post-formats', array(
        'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
    ) );
}
endif; // pg_starter_setup

add_action( 'after_setup_theme', 'pg_starter_setup' );


if ( ! function_exists( 'pg_starter_init' ) ) :

function pg_starter_init() {


    // Use categories and tags with attachments
    register_taxonomy_for_object_type( 'category', 'attachment' );
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );

    /*
     * Register custom post types. You can also move this code to a plugin.
     */
    /* Pinegrow generated Custom Post Types Begin */

    /* Pinegrow generated Custom Post Types End */

    /*
     * Register custom taxonomies. You can also move this code to a plugin.
     */
    /* Pinegrow generated Taxonomies Begin */

    /* Pinegrow generated Taxonomies End */

}
endif; // pg_starter_setup

add_action( 'init', 'pg_starter_init' );


if ( ! function_exists( 'pg_starter_widgets_init' ) ) :

function pg_starter_widgets_init() {

    /*
     * Register widget areas.
     */
     require 'includes/boatwidget.php';
     add_shortcode( 'boat_make', 'makeWidget' );
    /* Pinegrow generated Register Sidebars Begin */

    register_sidebar( array(
        'name' => __( 'Sidebar', 'pg_starter' ),
        'id' => 'right_sidebar',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Footer #01', 'pg_starter' ),
        'id' => 'footer01_sidebar',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Footer #02', 'pg_starter' ),
        'id' => 'footer02_sidebar',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Footer #03', 'pg_starter' ),
        'id' => 'footer03_sidebar',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    register_sidebar( array(
        'name' => __( 'Footer #04', 'pg_starter' ),
        'id' => 'footer04_sidebar',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widgettitle">',
        'after_title' => '</h3>'
    ) );

    /* Pinegrow generated Register Sidebars End */
}
add_action( 'widgets_init', 'pg_starter_widgets_init' );
endif;// pg_starter_widgets_init



if ( ! function_exists( 'pg_starter_customize_register' ) ) :

function pg_starter_customize_register( $wp_customize ) {
    // Do stuff with $wp_customize, the WP_Customize_Manager object.

    /* Pinegrow generated Customizer Controls Begin */

    /* Pinegrow generated Customizer Controls End */

}
add_action( 'customize_register', 'pg_starter_customize_register' );
endif;// pg_starter_customize_register


if ( ! function_exists( 'pg_starter_enqueue_scripts' ) ) :
    function pg_starter_enqueue_scripts() {

        /* Pinegrow generated Enqueue Scripts Begin */


    /* Pinegrow generated Enqueue Scripts End */

        /* Pinegrow generated Enqueue Styles Begin */



    /* Pinegrow generated Enqueue Styles End */

    }
    add_action( 'wp_enqueue_scripts', 'pg_starter_enqueue_scripts' );
endif;

/*
 * Resource files included by Pinegrow.
 */
/* Pinegrow generated Include Resources Begin */
require_once "inc/bootstrap/wp_bootstrap_navwalker.php";

    /* Pinegrow generated Include Resources End */

    /* Starter Theme Resources Start */

  // TGM_Plugin
  // http://tgmpluginactivation.com/


require_once dirname( __FILE__ ) . '/assets/tgmpa/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'pg_starter_register_required_plugins' );


function pg_starter_register_required_plugins() {
	$plugins = array(

		array(
			'name'               => 'WordPress Functionality Plugin', // The plugin name.
			'slug'               => 'wordpress-functionality-plugin', // The plugin slug (typically the folder name).
      'source'             => 'https://www.dropbox.com/s/dcvbkrqopsjtixo/wordpress-functionality-plugin_1.2.zip?dl=1', // The plugin source.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '1.2', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
		),

		// Plugins from the WordPress Plugin Repository.
		array(
			'name'      => 'Relevanssi - A Better Search',
			'slug'      => 'relevanssi',
			'required'  => false,
		),
    array(
      'name'      => 'bbPress',
      'slug'      => 'bbpress',
      'required'  => false,
    ),
    array(
			'name'      => 'WooCommerce',
			'slug'      => 'woocommerce',
			'required'  => false,
		),
    array(
			'name'      => 'Akismet',
			'slug'      => 'akismet',
			'required'  => false,
		),
    array(
			'name'      => 'Easy Digital Downloads',
			'slug'      => 'easy-digital-downloads',
			'required'  => false,
		),
    array(
			'name'      => 'Jetpack by WordPress.com',
			'slug'      => 'jetpack',
			'required'  => true,
		),
    array(
			'name'      => 'WooCommerce Menu Cart',
			'slug'      => 'woocommerce-menu-bar-cart',
			'required'  => false,
		),
    array(
			'name'      => 'Yoast SEO',
			'slug'      => 'wordpress-seo',
			'required'  => false,
		),
    array(
			'name'      => 'Unsplash WP',
			'slug'      => 'unsplash-stock-photo-library',
			'required'  => false,
		),
    array(
      'name'      => 'WP Retina 2x',
      'slug'      => 'wp-retina-2x',
      'required'  => false,
    ),
    array(
			'name'      => 'What The File',
			'slug'      => 'what-the-file',
			'required'  => false,
		),
    array(
			'name'      => 'Quick Featured Images',
			'slug'      => 'quick-featured-images',
			'required'  => false,
		),
    array(
			'name'      => 'Bootstrap Shortcodes for WordPress',
			'slug'      => 'bootstrap-3-shortcodes',
			'required'  => false,
		),
    array(
      'name'      => 'Theme Check',
      'slug'      => 'theme-check',
      'required'  => false,
    ),
    array(
			'name'      => 'Scroll Back To Top',
			'slug'      => 'scroll-back-to-top',
			'required'  => false,
		),
    array(
      'name'      => 'Developer',
      'slug'      => 'developer',
      'required'  => false,
    ),

	);

	// Array of configuration settings. (Amend each line as needed.)

	$config = array(
		'id'           => 'pg_starter',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'parent_slug'  => 'themes.php',            // Parent menu slug.
		'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => 'Starter Theme requirements:',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );

}

    // Add callback for custom TinyMCE editor stylesheets.

  function pg_starter_add_editor_styles() {
      add_editor_style( 'css/custom-editor-style.css' );
  }
  add_action( 'admin_init', 'pg_starter_add_editor_styles' );

    // Move the Jetpack Sharing and Like buttons //
    // https://jetpack.com/2013/06/10/moving-sharing-icons/ //

   function pg_starter_remove_share() {
   remove_filter( 'the_content', 'sharing_display',19 );
   remove_filter( 'the_excerpt', 'sharing_display',19 );
   if ( class_exists( 'Jetpack_Likes' ) ) {
    remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
   }
   }

   add_action( 'loop_start', 'pg_starter_remove_share' );

    // Jetpack Social Menu Support.
    // Social Menu allows site owners to create a new menu location, used to display links to Social Media Profiles.
    // https://jetpack.com/support/social-menu/
    // https://themeshaper.com/2016/02/12/jetpack-social-menu/

function pg_starter_jetpack_setup() {
    // Add theme support for Social Menu
add_theme_support( 'jetpack-social-menu' );
}
add_action( 'after_setup_theme', 'pg_starter_jetpack_setup' );

    // Return Early if Social Menu is not Available
function pg_starter_social_menu() {
 if ( ! function_exists( 'pg_starter_social_menu' ) ) {
     return;
 } else {
     jetpack_social_menu();
 }
}

    // Set Thumbnail size for Posts //
    add_image_size( 'post-thumbnail', '825', '510', array( "1", "") );

    // Set Thumbnail size for EDD plugin //

    add_image_size( 'edd-thumbnail', '768', '768', array( "1", "") );

    // Set Theme Content Width http://codex.wordpress.org/Content_Width //

    if ( ! isset( $content_width ) )
    $content_width = 960;

    // Woocommerce Code //
    // http://docs.pinegrow.com/wordpress-themes/how-to-get-woocommerce-to-run-in-a-wordpress-theme-created-with-pinegrow //

    add_action( 'after_setup_theme', 'pg_starter_woocommerce_support' );
function pg_starter_woocommerce_support() {
add_theme_support( 'woocommerce' ); }

    // Display 12 products per page //

add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );

    // Add Excerpts to Pages //

  add_action( 'init', 'pg_starter_add_excerpts_to_pages' );
function pg_starter_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}

    // WordPress bypass the front-page.php template file altogether when the blog posts index is being displayed.
    // https://codex.wordpress.org/Creating_a_Static_Front_Page

function pg_starter_filter_front_page_template( $template ) {
    return is_home() ? '' : $template;
}
add_filter( 'frontpage_template', 'pg_starter_filter_front_page_template' );

    // Replace SEARCH Text in Search Box Widget

add_filter('get_search_form', 'pg_starter_search_form');

function pg_starter_search_form($text) {
     $text = str_replace('value="Search"', 'value="Start Searching Now"', $text);
     return $text;
}

    // Prevent Scroll Back To Top Plugin to load Font Awesome
    // http://www.webtipblog.com/scroll-back-top-wordpress-plugin/

add_filter('sbtt_button_markup', 'pg_starter_scroll_back_to_top_filter');
function pg_starter_scroll_back_to_top_filter($text) {
  $text = str_replace(
    '<span class="scroll-back-to-top-inner">',
    '<span class="scroll-back-to-top-inner"><i class="fa fa-arrow-circle-up fa-3x"></i>',
    $text
  );

  return $text;
}

    // Breadcrumbs (Yoast SEO no more needed)
    // https://www.screenfeed.fr/blog/mon-fil-dariane-02697/
    // GRÉGORY VIGUIER @ScreenFeedFr

include( get_stylesheet_directory() . '/assets/breadcrumbs/functions-breadcrumb.php' );

add_filter( 'mash_breadcrumb_separator', 'my_breadcrumb_separator' );

function my_breadcrumb_separator() {
	return ' <span class="breadcrumb-separator" aria-hidden="true">&#187;</span> ';
}

// Theme Support for WordPress Custom Backgrounds
// https://codex.wordpress.org/Custom_Backgrounds

$custombackground_defaults = array(
	'default-color'          => '',
	'default-image'          => '',
	'default-repeat'         => '',
	'default-position-x'     => '',
	'default-attachment'     => '',
	'wp-head-callback'       => '_custom_background_cb',
	'admin-head-callback'    => '',
	'admin-preview-callback' => ''
);
add_theme_support( 'custom-background', $custombackground_defaults );

// Theme Support for WordPress Custom Headers
// https://codex.wordpress.org/Custom_Headers

$customheader_defaults = array(
	'default-image'          => '',
	'width'                  => 0,
	'height'                 => 0,
	'flex-height'            => false,
	'flex-width'             => false,
	'uploads'                => true,
	'random-default'         => false,
	'header-text'            => true,
	'default-text-color'     => '',
	'wp-head-callback'       => '',
	'admin-head-callback'    => '',
	'admin-preview-callback' => '',
);
add_theme_support( 'custom-header', $customheader_defaults );

// Add Custom Logo Support
// http://www.mavengang.com/2016/06/02/change-wordpress-custom-logo-class/

function pg_starter_custom_logo_setup() {

add_theme_support( 'custom-logo', array(
	'height'      => 100,
	'width'       => 400,
	'flex-height' => true,
	'flex-width'  => true,
	'header-text' => array( 'site-title', 'site-description' ),
) );

}
add_action( 'after_setup_theme', 'pg_starter_custom_logo_setup' );


// Bootstrap navbar with wordpress custom logo
// http://www.mavengang.com/2016/06/02/change-wordpress-custom-logo-class/

function pg_starter_the_custom_logo() {

	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}

}

add_filter('get_custom_logo','pg_starter_change_logo_class');

function pg_starter_change_logo_class($html)
{
  $html = str_replace('custom-logo-link', 'navbar-brand logo-navbar-brand', $html);
	return $html;
}
