<?php
/**
 * Projects portfolio: post type, taxonomies, queries and card markup.
 *
 * @package XLights
 */

namespace XLights\PostTypes;

use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use WP_Post;
use WP_Query;

defined( 'ABSPATH' ) || exit;

final class Projects {

	const POST_TYPE    = 'xlights_project';
	const TAX_SERVICE  = 'xlights_project_category';
	const TAX_PROPERTY = 'xlights_property_type';

	const META_CITY      = '_xl_city';
	const META_FEATURED  = '_xl_featured';
	const META_GALLERY   = '_xl_gallery';
	const META_BEFORE    = '_xl_before';
	const META_AFTER     = '_xl_after';
	const META_HAS_BA    = '_xl_has_ba';
	const META_COMPLETED = '_xl_completed';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'init', array( __CLASS__, 'image_sizes' ) );
	}

	public static function image_sizes() {
		add_image_size( 'xlights-card', 900, 675, true );
	}

	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'          => array(
					'name'               => __( 'Projects', 'xlights-elementor-widgets' ),
					'singular_name'      => __( 'Project', 'xlights-elementor-widgets' ),
					'add_new'            => __( 'Add Project', 'xlights-elementor-widgets' ),
					'add_new_item'       => __( 'Add New Project', 'xlights-elementor-widgets' ),
					'edit_item'          => __( 'Edit Project', 'xlights-elementor-widgets' ),
					'new_item'           => __( 'New Project', 'xlights-elementor-widgets' ),
					'view_item'          => __( 'View Project', 'xlights-elementor-widgets' ),
					'search_items'       => __( 'Search Projects', 'xlights-elementor-widgets' ),
					'not_found'          => __( 'No projects found.', 'xlights-elementor-widgets' ),
					'not_found_in_trash' => __( 'No projects found in Trash.', 'xlights-elementor-widgets' ),
					'all_items'          => __( 'Projects', 'xlights-elementor-widgets' ),
					'featured_image'     => __( 'Main project photo', 'xlights-elementor-widgets' ),
					'set_featured_image' => __( 'Set main project photo', 'xlights-elementor-widgets' ),
				),
				'public'          => true,
				'show_in_menu'    => 'xlights',
				'show_in_rest'    => true,
				'has_archive'     => false,
				'menu_icon'       => 'dashicons-format-gallery',
				'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions' ),
				'rewrite'         => array(
					'slug'       => 'projects',
					'with_front' => false,
				),
				'capability_type' => 'post',
			)
		);

		register_taxonomy(
			self::TAX_SERVICE,
			self::POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Lighting Categories', 'xlights-elementor-widgets' ),
					'singular_name' => __( 'Lighting Category', 'xlights-elementor-widgets' ),
					'menu_name'     => __( 'Lighting Categories', 'xlights-elementor-widgets' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'project-category',
					'with_front' => false,
				),
			)
		);

		register_taxonomy(
			self::TAX_PROPERTY,
			self::POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Property Types', 'xlights-elementor-widgets' ),
					'singular_name' => __( 'Property Type', 'xlights-elementor-widgets' ),
					'menu_name'     => __( 'Property Types', 'xlights-elementor-widgets' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'project-type',
					'with_front' => false,
				),
			)
		);

		$auth = static function ( $allowed, $meta_key, $post_id ) {
			return current_user_can( 'edit_post', $post_id );
		};
		foreach ( array(
			self::META_CITY      => 'string',
			self::META_FEATURED  => 'string',
			self::META_GALLERY   => 'string',
			self::META_BEFORE    => 'integer',
			self::META_AFTER     => 'integer',
			self::META_HAS_BA    => 'string',
			self::META_COMPLETED => 'string',
		) as $key => $type ) {
			register_post_meta(
				self::POST_TYPE,
				$key,
				array(
					'type'          => $type,
					'single'        => true,
					'show_in_rest'  => false,
					'auth_callback' => $auth,
				)
			);
		}
	}

	/**
	 * Default terms created by activation/setup.
	 *
	 * @return array
	 */
	public static function default_terms() {
		return array(
			self::TAX_SERVICE  => array(
				'permanent-soffit-lighting' => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
				'bistro-patio-lighting'     => __( 'Bistro / Patio Lighting', 'xlights-elementor-widgets' ),
				'deck-lighting'             => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
				'landscape-lighting'        => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
			),
			self::TAX_PROPERTY => array(
				'residential' => __( 'Residential', 'xlights-elementor-widgets' ),
				'commercial'  => __( 'Commercial', 'xlights-elementor-widgets' ),
			),
		);
	}

	/**
	 * Create default terms if missing (never renames or deletes existing terms).
	 *
	 * @return int Number of terms created.
	 */
	public static function ensure_terms() {
		if ( ! taxonomy_exists( self::TAX_SERVICE ) ) {
			self::register();
		}
		$created = 0;
		foreach ( self::default_terms() as $tax => $terms ) {
			foreach ( $terms as $slug => $name ) {
				if ( ! term_exists( $slug, $tax ) ) {
					$r = wp_insert_term( $name, $tax, array( 'slug' => $slug ) );
					if ( ! is_wp_error( $r ) ) {
						$created++;
					}
				}
			}
		}
		return $created;
	}

	/**
	 * Gallery filters required by the brief.
	 *
	 * @return array key => label
	 */
	public static function filters() {
		return array(
			'all'          => __( 'All Projects', 'xlights-elementor-widgets' ),
			'residential'  => __( 'Residential', 'xlights-elementor-widgets' ),
			'soffit'       => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
			'bistro'       => __( 'Bistro / Patio Lighting', 'xlights-elementor-widgets' ),
			'deck'         => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
			'landscape'    => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
			'commercial'   => __( 'Commercial', 'xlights-elementor-widgets' ),
			'before-after' => __( 'Before & After', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * @param string $filter Filter key.
	 * @return array Partial WP_Query args.
	 */
	private static function filter_args( $filter ) {
		$service = array(
			'soffit'    => 'permanent-soffit-lighting',
			'bistro'    => 'bistro-patio-lighting',
			'deck'      => 'deck-lighting',
			'landscape' => 'landscape-lighting',
		);
		if ( isset( $service[ $filter ] ) ) {
			return array(
				'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => self::TAX_SERVICE,
						'field'    => 'slug',
						'terms'    => $service[ $filter ],
					),
				),
			);
		}
		if ( 'residential' === $filter || 'commercial' === $filter ) {
			return array(
				'tax_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => self::TAX_PROPERTY,
						'field'    => 'slug',
						'terms'    => $filter,
					),
				),
			);
		}
		if ( 'before-after' === $filter ) {
			return array(
				'meta' => array(
					'key'   => self::META_HAS_BA,
					'value' => '1',
				),
			);
		}
		return array();
	}

	/**
	 * Query published projects. Featured first, then display order, then newest.
	 *
	 * @param array $args filter, count, page, featured_only.
	 * @return WP_Query
	 */
	public static function query( array $args ) {
		$args   = wp_parse_args(
			$args,
			array(
				'filter'        => 'all',
				'count'         => 9,
				'page'          => 1,
				'featured_only' => false,
			)
		);
		$filter = isset( self::filters()[ $args['filter'] ] ) ? $args['filter'] : 'all';
		$extra  = self::filter_args( $filter );

		$meta = array(
			'relation'      => 'OR',
			'featured_flag' => array(
				'key'     => self::META_FEATURED,
				'compare' => 'EXISTS',
			),
			'featured_none' => array(
				'key'     => self::META_FEATURED,
				'compare' => 'NOT EXISTS',
			),
		);
		if ( $args['featured_only'] ) {
			$meta = array(
				'featured_flag' => array(
					'key'   => self::META_FEATURED,
					'value' => '1',
				),
			);
		}
		if ( isset( $extra['meta'] ) ) {
			$meta = array(
				'relation' => 'AND',
				$extra['meta'],
				$meta,
			);
			unset( $extra['meta'] );
		}

		$query_args = array_merge(
			array(
				'post_type'           => self::POST_TYPE,
				'post_status'         => 'publish',
				'posts_per_page'      => max( 1, min( 48, (int) $args['count'] ) ),
				'paged'               => max( 1, (int) $args['page'] ),
				'ignore_sticky_posts' => true,
				'meta_query'          => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'orderby'             => array(
					'featured_flag' => 'DESC',
					'menu_order'    => 'ASC',
					'date'          => 'DESC',
				),
			),
			$extra
		);
		/**
		 * Filter project gallery query args.
		 *
		 * @param array $query_args WP_Query args.
		 * @param array $args       Requested args.
		 */
		return new WP_Query( apply_filters( 'xlights/projects/query_args', $query_args, $args ) );
	}

	/**
	 * Normalized data for a project.
	 *
	 * @param WP_Post|int $post Post.
	 * @return array
	 */
	public static function card_data( $post ) {
		$post    = get_post( $post );
		$id      = $post->ID;
		$thumb   = (int) get_post_thumbnail_id( $id );
		$gallery = self::gallery_ids( $id );
		$images  = array_values( array_unique( array_filter( array_merge( array( $thumb ), $gallery ) ) ) );
		$before  = (int) get_post_meta( $id, self::META_BEFORE, true );
		$after   = (int) get_post_meta( $id, self::META_AFTER, true );

		$services = get_the_terms( $id, self::TAX_SERVICE );
		$types    = get_the_terms( $id, self::TAX_PROPERTY );

		return array(
			'id'       => $id,
			'title'    => get_the_title( $id ),
			'url'      => get_permalink( $id ),
			'city'     => (string) get_post_meta( $id, self::META_CITY, true ),
			'image'    => $images ? $images[0] : ( $after ? $after : 0 ),
			'images'   => $images,
			'before'   => $before && wp_attachment_is_image( $before ) ? $before : 0,
			'after'    => $after && wp_attachment_is_image( $after ) ? $after : 0,
			'services' => is_array( $services ) ? wp_list_pluck( $services, 'name' ) : array(),
			'types'    => is_array( $types ) ? wp_list_pluck( $types, 'name' ) : array(),
			'excerpt'  => has_excerpt( $id ) ? get_the_excerpt( $id ) : '',
			'featured' => '1' === get_post_meta( $id, self::META_FEATURED, true ),
		);
	}

	/**
	 * @param int $post_id Post ID.
	 * @return int[]
	 */
	public static function gallery_ids( $post_id ) {
		$raw = (string) get_post_meta( $post_id, self::META_GALLERY, true );
		return array_values( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
	}

	/**
	 * Lightbox payload for a list of attachment IDs.
	 *
	 * @param int[]  $ids   Attachments.
	 * @param string $title Fallback alt/caption.
	 * @return array
	 */
	public static function lightbox_items( array $ids, $title ) {
		$items = array();
		foreach ( array_slice( $ids, 0, 30 ) as $aid ) {
			$src = wp_get_attachment_image_src( $aid, 'large' );
			if ( ! $src ) {
				continue;
			}
			$full    = wp_get_attachment_image_src( $aid, 'full' );
			$alt     = trim( (string) get_post_meta( $aid, '_wp_attachment_image_alt', true ) );
			$caption = wp_get_attachment_caption( $aid );
			$items[] = array(
				'src'     => $src[0],
				'srcset'  => (string) wp_get_attachment_image_srcset( $aid, 'large' ),
				'w'       => (int) ( $full ? $full[1] : $src[1] ),
				'h'       => (int) ( $full ? $full[2] : $src[2] ),
				'alt'     => '' !== $alt ? $alt : $title,
				'caption' => $caption ? $caption : '',
			);
		}
		return $items;
	}

	/**
	 * Gallery card markup (<li>).
	 *
	 * @param WP_Post|int $post Post.
	 * @return string
	 */
	public static function render_card( $post ) {
		$p = self::card_data( $post );
		if ( ! $p['image'] ) {
			return '';
		}
		$lightbox = self::lightbox_items( $p['images'] ? $p['images'] : array( $p['image'] ), $p['title'] );
		$meta     = array_filter( array( $p['city'], $p['services'] ? $p['services'][0] : '' ) );

		$out  = '<li class="xl-project" data-xl-reveal>';
		$out .= '<article class="xl-project__card">';
		$out .= '<button type="button" class="xl-project__open" data-xl-lightbox="' . esc_attr( wp_json_encode( $lightbox ) ) . '" aria-label="' . esc_attr( sprintf( /* translators: 1: project title, 2: photo count */ _n( 'View %2$d photo of %1$s', 'View %2$d photos of %1$s', count( $lightbox ), 'xlights-elementor-widgets' ), $p['title'], count( $lightbox ) ) ) . '">';
		$out .= Html::image(
			$p['image'],
			array(
				'size'  => 'xlights-card',
				'sizes' => '(max-width: 767px) 100vw, (max-width: 1024px) 50vw, 33vw',
				'alt'   => '',
			)
		);
		$out .= '<span class="xl-project__zoom">' . Icons::svg( 'zoom' ) . ( count( $lightbox ) > 1 ? '<b>' . (int) count( $lightbox ) . '</b>' : '' ) . '</span>';
		if ( $p['before'] && $p['after'] ) {
			$out .= '<span class="xl-project__tag">' . esc_html__( 'Before & After', 'xlights-elementor-widgets' ) . '</span>';
		}
		$out .= '</button>';
		$out .= '<div class="xl-project__meta">';
		$out .= '<h3 class="xl-project__title"><a href="' . esc_url( $p['url'] ) . '">' . esc_html( $p['title'] ) . '</a></h3>';
		if ( $meta ) {
			$out .= '<p class="xl-project__info">' . esc_html( implode( ' · ', $meta ) ) . '</p>';
		}
		$out .= '</div></article></li>';
		return $out;
	}

	/**
	 * Recompute the before/after flag on save.
	 *
	 * @param int $post_id Post.
	 */
	public static function sync_flags( $post_id ) {
		$before = (int) get_post_meta( $post_id, self::META_BEFORE, true );
		$after  = (int) get_post_meta( $post_id, self::META_AFTER, true );
		update_post_meta( $post_id, self::META_HAS_BA, ( $before && $after ) ? '1' : '0' );
		if ( '' === get_post_meta( $post_id, self::META_FEATURED, true ) ) {
			update_post_meta( $post_id, self::META_FEATURED, '0' );
		}
	}
}
