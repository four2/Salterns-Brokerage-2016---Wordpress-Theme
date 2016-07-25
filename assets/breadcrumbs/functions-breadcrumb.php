<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

/*-----------------------------------------------------------------------------------*/
/* !GENERIC TOOLS ================================================================== */
/*-----------------------------------------------------------------------------------*/

// !Tells if a "thing" is a post.

if ( ! function_exists( 'is_wp_post' ) ) :
function is_wp_post( $thing ) {
	return $thing && is_object( $thing ) && is_a( $thing, 'WP_Post' );
}
endif;


// !Get current URL.

if ( ! function_exists( 'sf_get_current_url' ) ) :
function sf_get_current_url( $mode = 'base' ) {
	$mode = (string) $mode;
	$url  = ! empty( $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] ) ? $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] : ( ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );
	$url  = 'http' . ( is_ssl() ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'] . $url;

	switch( $mode ) :
		case 'raw' :
			return $url;
		case 'uri' :
			$url = reset( ( explode( '?', $url ) ) );
			$url = reset( ( explode( '&', $url ) ) );
			return trim( str_replace( home_url(), '', $url ), '/' );
		default :
			$url = reset( ( explode( '?', $url ) ) );
			return reset( ( explode( '&', $url ) ) );
	endswitch;
}
endif;


// !`esc_attr()` and `strip_tags()` had a baby.

if ( ! function_exists( 'esc_strip_attr' ) ) :
function esc_strip_attr( $content = '' ) {
	return esc_attr( strip_tags( $content ) );
}
endif;


// !Do I really need to explain this one?

if ( ! function_exists( '__not_empty' ) ) :
function __not_empty( $whateva ) {
	$whateva = is_object( $whateva ) ? (array) $whateva : $whateva;
	return ! empty( $whateva );
}
endif;


// !Get the ID of the page displayed on front.

if ( ! function_exists( 'get_page_on_front' ) ) :
function get_page_on_front() {
	if ( get_option( 'show_on_front' ) === 'page' ) {
		return absint( get_option( 'page_on_front' ) );
	}
	return false;
}
endif;


/*-----------------------------------------------------------------------------------*/
/* !SPECIFIC TOOLS ================================================================= */
/*-----------------------------------------------------------------------------------*/

// !Determine whether blog/site has more than one category.

if ( ! function_exists( 'mash_categorized_blog' ) ) :
function mash_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'mash_categories' ) ) ) {
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			'number'     => 2,
		) );
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'mash_categories', $all_the_cool_cats );
	}

	return $all_the_cool_cats > 1;
}
endif;


// !WooCommerce: tell if the shop is displayed on the front page.

if ( ! function_exists( 'mash_shop_is_on_front' ) ) :
function mash_shop_is_on_front() {
	$page_on_front = get_page_on_front();
	$shop_id       = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : false;
	return $page_on_front && $page_on_front === $shop_id;
}
endif;


/*-----------------------------------------------------------------------------------*/
/* !FILTERS ======================================================================== */
/*-----------------------------------------------------------------------------------*/

// !Localize the breadcrumb.

add_action( 'after_setup_theme', 'mash_breadcrumb_i18n' );

if ( ! function_exists( 'mash_breadcrumb_i18n' ) ) :
function mash_breadcrumb_i18n() {
	// Hey, children theme here!
	load_theme_textdomain( 'pg_starter', get_stylesheet_directory() . '/languages/pg_starter' );
}
endif;


// !Flush out the transients used in `mash_categorized_blog()`.

add_action( 'edit_category', 'mash_category_transient_flusher' );
add_action( 'save_post',     'mash_category_transient_flusher' );

if ( ! function_exists( 'mash_category_transient_flusher' ) ) :
function mash_category_transient_flusher() {
	delete_transient( 'mash_categories' );
}
endif;


/*
 * WooCommerce
 *
 * If the front page and the products page are the same, remove the link to the products archive.
 *If we're displaying an endpoint, add an item.
 *
 */

add_filter( 'mash_breadcrumb_items', 'mash_woocommerce_breadcrumb_items' );

if ( ! function_exists( 'mash_woocommerce_breadcrumb_items' ) ) :
function mash_woocommerce_breadcrumb_items( $items ) {
	static $home_done = false;
	$count = Mash_Breadcrumb::$home_label ? 1 : 0;

	if ( ! function_exists( 'is_wc_endpoint_url' ) ) {
		return $items;
	}

	if ( count( $items ) > $count && mash_shop_is_on_front() ) {
		$products_url = untrailingslashit( get_post_type_archive_link( 'product' ) );

		if ( $products_url ) {
			foreach ( $items as $i => $item ) {
				if ( untrailingslashit( $item['url'] ) === $products_url ) {
					// The first one we meet is the real "Home".
					if ( ! $home_done ) {
						$home_done = true;
						continue;
					}
					unset( $items[ $i ] );
					break;
				}
			}
		}
	}

	if ( is_wc_endpoint_url() && ( $endpoint = WC()->query->get_current_endpoint() ) && ( $endpoint_title = WC()->query->get_endpoint_title( $endpoint ) ) ) {
		$items[] = array(
			'label'      => $endpoint_title,
			'url'        => wc_get_endpoint_url( $endpoint, '', wc_get_page_permalink( get_queried_object_id() ) ),
			'title_attr' => '',
		);
	}

	return $items;
}
endif;

/**/
