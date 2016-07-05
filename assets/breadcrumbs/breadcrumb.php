<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

if ( function_exists( 'yoast_breadcrumb' ) ) {
	$options = get_option( 'wpseo_internallinks' );

	if ( ! empty( $options['breadcrumbs-enable'] ) ) {
		yoast_breadcrumb( '<nav class="breadcrumb yoast-breadcrumb" aria-label="' . esc_attr__( 'You are here:', 'pg_starter' ) . '">', '</nav>' );
		return;
	}
}


if ( ! class_exists( 'Mash_Breadcrumb' ) ) :
class Mash_Breadcrumb {

	const VERSION = '1.0.1';

	public static $home_label       = '';
	public static $last_item_format = 'span_and_link_tags';
	public static $show_if_empty    = false;
	public static $sep              = ' <span class="breadcrumb-separator icon icon-arrow-right" aria-hidden="true"></span> ';
	public static $wrapper_tag      = 'nav';
	protected static $itemtype      = ' itemscope itemtype="http://schema.org/BreadcrumbList"';
	protected static $items         = null;
	protected static $count_items   = 0;
	protected static $position      = 1;

	public static function init() {
		global $paged, $page, $wp_locale;

		self::$home_label       = apply_filters( 'mash_breadcrumb_home_label', __( 'Home', 'pg_starter' ) );                      // if false, the link won't be printed.
		self::$last_item_format = apply_filters( 'mash_breadcrumb_current_item_format', self::$last_item_format );  // 'permalink', 'span_and_link_tags', 'span_only', 'hidden', 'none'. See `self::print_last_item()`.
		self::$show_if_empty    = apply_filters( 'mash_breadcrumb_show_if_empty', self::$show_if_empty );           // if true, the breadcrumb wrapper will be printed even if there's nothing to show.
		self::$sep              = apply_filters( 'mash_breadcrumb_separator', self::$sep );
		self::$wrapper_tag      = apply_filters( 'mash_breadcrumb_wrapper_tag', self::$wrapper_tag );

		if ( ! isset( self::$items ) ) {

			self::$items = array();

			if ( self::$home_label ) {
				self::add_item( self::$home_label, user_trailingslashit( home_url() ), get_bloginfo( 'name', 'display' ) );
			}

			if ( ! is_front_page() ) {
				// Blog
				if ( is_home() ) {
					self::add_blog_item();
				}
				// Post type archive
				elseif ( is_post_type_archive() && ! is_search() ) {
					self::add_post_type_archive_item( get_queried_object() );
				}
				// Post
				elseif ( is_singular() ) {
					$current_item = get_queried_object();

					// Post
					if ( is_singular( 'post' ) ) {
						self::add_blog_item();
					}
					// Page, CPT, etc.
					else {
						self::add_post_type_archive_item( $current_item->post_type );
					}

					// Parents
					if ( is_post_type_hierarchical( $current_item->post_type ) ) {
						$deepest_parent = $current_item->post_parent;
						$to_reverse     = array();

						// Get parent post recursively.
						while ( $deepest_parent ) {
							$t_page = get_post( $deepest_parent );

							if ( is_wp_post( $t_page ) ) {
								$to_reverse[] = array(
									'label'      => get_the_title( $deepest_parent ),
									'url'        => get_permalink( $deepest_parent ),
									'title_attr' => get_post_type_object( get_post_type( $deepest_parent ) )->labels->singular_name
								);
							}
							$deepest_parent = $t_page->post_parent;
						}

						if ( ! empty( $to_reverse ) ) {
							self::$items = array_merge( self::$items, array_reverse( $to_reverse ) );
						}
					}
					// Taxonomy Terms
					else {
						$args       = array( 'public' => true, 'show_in_nav_menus' => true, );
						$taxos      = get_taxonomies( $args, 'objects' );
						$taxonomies = array();

						// Find taxonomies attached to this post type.
						if ( ! empty( $taxos ) ) {
							foreach ( $taxos as $taxo ) {
								if ( in_array( $current_item->post_type, $taxo->object_type ) ) {
									$taxonomies[] = $taxo->name;
								}
							}
						}

						// Filter the taxonomy: it's an array of taxonomy names but only the first one will be chosen.
						$taxonomy = (array) apply_filters( 'mash_breadcrumb_post_taxonomies', $taxonomies, $current_item );
						$taxonomy = array_intersect( $taxonomy, $taxonomies );

						if ( ! empty( $taxonomy ) ) {
							$taxonomy = reset( $taxonomy );

							if ( $taxonomy !== 'category' || mash_categorized_blog() ) {
								$term_objects = get_the_terms( $current_item->ID, $taxonomy );
								$term_objects = is_array( $term_objects ) ? $term_objects : array();
								// Filter the post terms (objects).
								$term_objects = (array) apply_filters( 'mash_breadcrumb_the_post_terms', $term_objects, $taxonomy, $current_item );
								$term_objects = array_filter( $term_objects, '__not_empty' );

								if ( $term_objects ) {

									if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {

										// Not a hierarchical taxonmy, display the first term only.
										$term_objects = reset( $term_objects );
										self::add_taxonomy_term_item( $term_objects );

									}
									else {

										$all_parents = get_terms( $taxonomy, array( 'hide_empty' => false, 'fields' => 'id=>parent' ) );
										$all_parents = is_array( $all_parents ) ? $all_parents : array();

										// Find all parent terms.
										if ( ! empty( $all_parents ) ) {

											$i             = 0;
											$count         = 0;
											$the_terms     = array();
											$all_the_terms = array();

											// We'll keep the "branch" with the greater number of terms.
											foreach ( $term_objects as $term_object ) {
												$deepest_parent = $term_object->term_id;

												while ( $deepest_parent ) {
													$the_terms[ $i ][] = $deepest_parent;
													$all_the_terms[]   = $deepest_parent;
													$deepest_parent    = isset( $all_parents[ $deepest_parent ] ) ? $all_parents[ $deepest_parent ] : 0;
												}

												$tmp_count = isset( $the_terms[ $i ] ) ? count( $the_terms[ $i ] ) : 0;

												if ( $tmp_count > $count ) {
													$count = $tmp_count;
												}
												else {
													unset( $the_terms[ $i ] );
												}

												$i++;
											}

											$all_the_terms = $all_the_terms ? array_unique( $all_the_terms ) : array();
											$the_terms     = $the_terms ? array_reverse( end( $the_terms ) ) : array();
											// Filter the post terms (term IDs).
											$the_terms     = (array) apply_filters( 'mash_breadcrumb_hierarchical_post_terms', $the_terms, $taxonomy, $term_objects, $current_item );
											$the_terms     = array_filter( array_intersect( $the_terms, $all_the_terms ) );

											if ( ! empty( $the_terms ) ) {
												foreach ( $the_terms as $term ) {
													$term = isset( $term_objects[ $term ] ) ? $term_objects[ $term ] : get_term( $term, $taxonomy );

													if ( $term ) {
														self::add_taxonomy_term_item( $term );
													}
												}
											}

										}

									}

								}

							}
						}
					}

					self::add_item( get_the_title( $current_item ), get_permalink( $current_item ) );
				}
				// Search
				elseif ( is_search() ) {
					$label = sprintf( __( 'Search Results For: %s', 'pg_starter' ), esc_strip_attr( urldecode( get_search_query( false ) ) ) );
					self::add_item( $label, sf_get_current_url( 'raw' ) );
				}
				// 404
				elseif ( is_404() ) {
					$label = __( 'Page not found', 'pg_starter' );
					self::add_item( $label, sf_get_current_url( 'raw' ) );
				}
				// Author
				elseif ( is_author() ) {
					$author = get_queried_object();
					$label  = sprintf( _x( 'About %s', '%s: author name', 'pg_starter' ), '<span translate="no">' . esc_html( $author->data->display_name ) . '</span>' );
					self::add_item( $label, get_author_posts_url( $author->ID ), '', true );
				}
				// Date Archive
				elseif ( is_date() ) {
					if ( $m = get_query_var( 'm' ) ) {
						$year  = (int) substr( $m, 0, 4 );
						$month = (int) substr( $m, 4, 2 );
						$day   = (int) substr( $m, 6, 2 );
					}
					else {
						$year  = (int) get_query_var( 'year' );
						$month = (int) get_query_var( 'monthnum' );
						$day   = (int) get_query_var( 'day' );
					}

					if ( $year && $month && $day ) {
						// Month Day, Year
						$date       = __( 'F j, Y', 'pg_starter' );
						$label      = _x( 'Archives %s', 'daily archive title, %s = date', 'pg_starter' );
						$title_attr = __( 'Daily archives', 'pg_starter' );
					}
					elseif ( $year && $month ) {
						// Month, Year
						$date       = __( 'F, Y', 'pg_starter' );
						$label      = _x( 'Archives %s', 'monthly archive title, %s = date', 'pg_starter' );
						$title_attr = __( 'Monthly archives', 'pg_starter' );
					}
					else {
						// Year
						$date       = 'Y';
						$label      = _x( 'Archives %s', 'yearly archive title, %s = date', 'pg_starter' );
						$title_attr = __( 'Yearly archives', 'pg_starter' );
					}

					$date = str_replace( array( '\F', '\m', '\M', '\n', '\j', '\d', '\Y', '\o', '\y' ), array( '%1%', '%2%', '%3%', '%4%', '%5%', '%6%', '%7%', '%8%', '%9%' ), $date );
					$date = str_replace( array( 'F', 'm', 'M', 'n', 'j', 'd', 'Y', 'o', 'y' ), array( '%1$s', '%1$s', '%1$s', '%1$s', '%2$s', '%2$s', '%3$s', '%3$s', '%3$s' ), $date );
					$date = str_replace( array( '%1%', '%2%', '%3%', '%4%', '%5%', '%6%', '%7%', '%8%', '%9%' ), array( '\F', '\m', '\M', '\n', '\j', '\d', '\Y', '\o', '\y' ), $date );

					$label = sprintf( $label, sprintf(
						$date,
						$month ? $wp_locale->get_month( $month ) : 0,
						$day,
						$year
					) );

					self::add_blog_item();

					self::add_item( $label, sf_get_current_url( 'raw' ), $title_attr );
				}
				// Meta Archive.
				// See http://www.screenfeed.fr/plugin-wp/sf-meta-archives/
				elseif ( function_exists( 'is_post_meta_archive' ) && is_post_meta_archive() ) {
					$_paged     = max( $paged, $page, 1 );
					$query_var  = get_queried_object_id();
					$meta_value = get_query_var( $query_var );
					$url        = get_post_meta_archive_link( $query_var, $meta_value, $_paged );
					$label      = sf_meta_archive_title( '' );

					self::add_item( $label, $url );
				}
				// Taxonomy Term
				elseif ( is_category() || is_tag() || is_tax() ) {
					$term            = get_queried_object();
					$taxonomy        = $term->taxonomy;
					$taxonomy_object = get_taxonomy( $taxonomy );
					$post_types      = $taxonomy_object->object_type;

					// Archive page
					if ( ! empty( $post_types ) ) {
						// Keep only post types with an archive page.
						foreach ( $post_types as $i => $post_type ) {
							if ( ! post_type_exists( $post_type ) ) {
								unset( $post_types[ $i ] );
							}

							if ( $post_type === 'post' ) {
								continue;
							}

							$post_type = get_post_type_object( $post_type );

							if ( ! $post_type->has_archive || ! $post_type->public ) {
								unset( $post_types[ $i ] );
							}
						}
					}

					if ( ! empty( $post_types ) ) {
						// Filter the post type: it's an array but only the first one will be chosen.
						$post_type = (array) apply_filters( 'mash_breadcrumb_post_taxonomies', $post_types, $taxonomy, $term );
						$post_type = array_intersect( $post_type, $post_types );

						if ( ! empty( $post_type ) ) {
							$post_type	= reset( $post_type );

							if ( $post_type === 'post' ) {
								self::add_blog_item();
							}
							else {
								self::add_post_type_archive_item( $post_type );
							}
						}
					}

					// Term(s)
					if ( ! is_taxonomy_hierarchical( $taxonomy ) ) {
						self::add_taxonomy_term_item( $term );
					}
					else {
						$taxonomy_label = $taxonomy_object->labels->singular_name;
						$deepest_parent = $term->term_id;
						$to_reverse     = array();

						// Add terms recursively
						while ( $deepest_parent ) {
							$term = get_term( $deepest_parent, $taxonomy );
							$to_reverse[] = array(
								'label'      => self::single_term_title( $term ),
								'url'        => get_term_link( $term ),
								'title_attr' => $taxonomy_label,
							);
							$deepest_parent = $term->parent;
						}

						if ( ! empty( $to_reverse ) ) {
							self::$items = array_merge( self::$items, array_reverse( $to_reverse ) );
						}
					}
				}
				// Fallback
				else {
					do_action( 'mash_breadcrumb_custom_item' );
				}
			}

		}

		// Filter the items.
		self::$items       = apply_filters( 'mash_breadcrumb_items', self::$items );
		self::$count_items = is_array( self::$items ) ? count( self::$items ) : 0;

		if ( self::$count_items ) {
			self::print_start();

			foreach ( self::$items as $item ) {
				self::print_item( $item );
			}

			self::print_end();
		}
		elseif ( self::$show_if_empty ) {
			self::print_start();
			self::print_end();
		}

		do_action( 'mash_after_breadcrumb', self::$items );
	}


	// !FLUSH THE ITEMS CACHE. FOR A SECOND (AND DIFFERENT) BREADCRUMB?

	public static function remove_items() {
		self::$items = null;
	}


	// !ADD ITEMS

	public static function add_item( $label, $url, $title_attr = '', $escaped = false ) {
		/*
		 * Provide an array with the following keys:
		 * "label":      The link title (required).
		 * "url":        The link URL (required).
		 * "title_attr": The title attibute (facultative).
		 * "escaped":    If true, won't `esc_html()` the label (facultative).
		 */
		self::$items[] = array(
			'label'      => $label,
			'url'        => $url,
			'title_attr' => $title_attr,
			'escaped'    => $escaped,
		);
	}


	public static function add_blog_item() {
		if ( get_option( 'show_on_front' ) !== 'page' ) {
			return;
		}

		$page_for_posts = absint( get_option( 'page_for_posts' ) );

		if ( $page_for_posts && ( $page_for_posts_url = get_permalink( $page_for_posts ) ) && ( $page_for_posts_label = get_the_title( $page_for_posts ) ) ) {
			self::add_item( $page_for_posts_label, $page_for_posts_url, __( 'Posts page', 'pg_starter' ) );
		}
	}


	public static function add_post_type_archive_item( $post_type_obj ) {
		if ( ! is_object( $post_type_obj ) ) {
			$post_type_obj = get_post_type_object( $post_type_obj );
		}
		if ( ! $post_type_obj ) {
			return;
		}

		if ( $post_type_obj->has_archive ) {
			$label      = self::post_type_archive_title( $post_type_obj );
			$url        = get_post_type_archive_link( $post_type_obj->name );
			$title_attr = sprintf( _x( '%s page', 'post type archive title, %s = post type plural name', 'pg_starter' ), $post_type_obj->labels->name );
			$title_attr = apply_filters( 'mash_post_type_archive_title', $title_attr, $post_type_obj->name );

			if ( $label && $url ) {
				self::add_item( $label, $url, $title_attr );
			}
		}
		// If the post type has no archive, we still can provide something, like a page title + URL.
		elseif ( $atts = apply_filters( 'mash_breadcrumb_post_type_archive', false, $post_type_obj ) ) {

			if ( is_array( $atts ) && ! empty( $atts['label'] ) && ! empty( $atts['url'] ) ) {
				$title_attr = ! empty( $atts['title_attr'] ) ? $atts['title_attr'] : null;
				$escaped    = ! empty( $atts['escaped'] )    ? $atts['escaped']    : false;

				self::add_item( $atts['label'], $atts['url'], $title_attr, $escaped );
			}

		}
	}


	public static function add_taxonomy_term_item( $term ) {
		$label      = self::single_term_title( $term );
		$url        = get_term_link( $term );
		$title_attr = sprintf( __( '%1$s: %2$s', 'pg_starter' ), get_taxonomy( $term->taxonomy )->labels->singular_name, $term->name );

		if ( $label && $url ) {
			self::add_item( $label, $url, $title_attr );
		}
	}


	// !PRINT

	public static function print_start() {
		$itemprop = apply_filters( 'mash_breadcrumb_microdatas', true ) ? ' itemprop="breadcrumb"' : '';
		echo "\n<" . self::$wrapper_tag . ' class="breadcrumb" aria-label="' . esc_attr__( 'You are here:', 'pg_starter' ) . '"' . $itemprop . self::$itemtype . ">\n";
	}


	public static function print_end() {
		echo '</'. self::$wrapper_tag . ">\n";
	}


	public static function print_item( $item ) {
		if ( self::$position === self::$count_items ) {
			self::print_last_item( $item );
			return;
		}

		echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
			echo '<a href="' . esc_url( $item['url'] ) . '" itemprop="item"' . ( $item['title_attr'] ? ' title="' . esc_attr( $item['title_attr'] ) . '"' : '' ) . '>';
				echo '<span itemprop="name">' . ( ! empty( $item['escaped'] ) ? $item['label'] : esc_html( $item['label'] ) ) . '</span>';
				echo '<meta itemprop="position" content="' . self::$position . '" />';
			echo '</a>';
		echo '</span>';

		if ( self::$count_items === self::$position - 1 ) {
			if ( self::$last_item_format !== 'none' && self::$last_item_format !== 'hidden' ) {
				echo self::$sep . "\n";
			}
		}
		else {
			echo self::$sep . "\n";
		}

		++self::$position;
	}


	public static function print_last_item( $item ) {
		// $item['label'] = ! empty( $item['escaped'] ) ? $item['label'] : esc_html( $item['label'] );
		// Special ST Fix for EDD support > 02/06/2016 Grégory Viguier
		$item['label'] = ! empty( $item['escaped'] ) ? $item['label'] : esc_html( strip_tags( $item['label'] ) );		echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';

			switch ( self::$last_item_format ) {
				case 'span_and_link_tags':
					echo '<span itemprop="item" title="' . esc_attr__( 'Current page', 'pg_starter' ) . '">';
						echo '<span itemprop="name">' . $item['label'] . '</span>';
						echo '<link href="' . esc_url( $item['url'] ) . '" itemprop="url" />';
						echo '<meta itemprop="position" content="' . self::$position . '" />';
					echo '</span>';
					break;
				case 'span_only':
					echo '<span itemprop="item" title="' . esc_attr__( 'Current page', 'pg_starter' ) . '">';
						echo '<span itemprop="name">' . $item['label'] . '</span>';
						echo '<meta itemprop="position" content="' . self::$position . '" />';
					echo '</span>';
					break;
				case 'hidden':
					echo '<a href="' . esc_url( $item['url'] ) . '" itemprop="item" rel="bookmark" title="' . esc_attr__( 'Current page', 'pg_starter' ) . '" class="post-permalink screen-reader-text">';
						echo '<span itemprop="name">' . ( ! empty( $item['escaped'] ) ? $item['label'] : esc_html( $item['label'] ) ) . '</span>';
						echo '<meta itemprop="position" content="' . self::$position . '" />';
					echo '</a>';
					break;
				case 'none':
					break;
				default:
					echo '<a href="' . esc_url( $item['url'] ) . '" itemprop="item" rel="bookmark" title="' . esc_attr__( 'Current page', 'pg_starter' ) . '" class="post-permalink">';
						echo '<span itemprop="name">' . ( ! empty( $item['escaped'] ) ? $item['label'] : esc_html( $item['label'] ) ) . '</span>';
						echo '<meta itemprop="position" content="' . self::$position . '" />';
					echo '</a>';
			}

		echo '</span>';
	}


	// TITLES

	protected static function single_term_title( $term ) {
		global $wp_query;

		$tmp_object = get_queried_object();

		if ( $term === $tmp_object ) {
			return single_term_title( '', false );
		}

		// Trick <code>single_term_title()</code> with a fake queried_object.
		$wp_query->queried_object = $term;

		if ( $term->taxonomy === 'post_tag' ) {
			$prev_is = $wp_query->is_tag;
			$wp_query->is_tag = 1;
		}
		elseif ( $term->taxonomy === 'category' ) {
			$prev_is = $wp_query->is_category;
			$wp_query->is_category = 1;
		}
		else {
			$prev_is = $wp_query->is_tax;
			$wp_query->is_tax = 1;
		}

		$title = single_term_title( '', false );

		$wp_query->queried_object = $tmp_object;

		if ( $term->taxonomy === 'post_tag' ) {
			$wp_query->is_tag = $prev_is;
		}
		elseif ( $term->taxonomy === 'category' ) {
			$wp_query->is_category = $prev_is;
		}
		else {
			$wp_query->is_tax = $prev_is;
		}

		return $title;
	}


	protected static function post_type_archive_title( $post_type ) {
		global $wp_query;

		if ( is_post_type_archive() ) {
			return post_type_archive_title( '', false );
		}

		if ( ! is_object( $post_type ) ) {
			$post_type = get_post_type_object( $post_type );
		}

		// Trick <code>post_type_archive_title()</code> with a fake queried_object.
		$tmp_object    = get_queried_object();
		$tmp_post_type = $wp_query->get( 'post_type' );

		$wp_query->set( 'post_type', $post_type->name );
		$wp_query->queried_object       = $post_type;
		$wp_query->is_post_type_archive = 1;

		$title = post_type_archive_title( '', false );

		$wp_query->set( 'post_type', $tmp_post_type );
		$wp_query->queried_object       = $tmp_object;
		$wp_query->is_post_type_archive = 0;

		return $title;
	}

}
endif;


if ( did_action( 'wp_head' ) ) {
	Mash_Breadcrumb::init();
}

/**/
