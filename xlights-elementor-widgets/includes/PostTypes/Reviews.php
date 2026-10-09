<?php
/**
 * Customer reviews. Reviews render only when published AND the editor has
 * confirmed the customer's permission to publish, which keeps placeholder or
 * unapproved testimonials off the live site.
 *
 * @package XLights
 */

namespace XLights\PostTypes;

use XLights\Helpers\Html;
use XLights\Helpers\Icons;
use WP_Query;

defined( 'ABSPATH' ) || exit;

final class Reviews {

	const POST_TYPE = 'xlights_review';

	const META_RATING      = '_xl_rating';
	const META_SOURCE      = '_xl_source';
	const META_SOURCE_URL  = '_xl_source_url';
	const META_DATE        = '_xl_review_date';
	const META_LOCATION    = '_xl_location';
	const META_PROJECT     = '_xl_project';
	const META_PHOTO       = '_xl_photo';
	const META_ATTRIBUTION = '_xl_attribution';
	const META_FEATURED    = '_xl_featured';
	const META_PERMISSION  = '_xl_permission';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'               => __( 'Reviews', 'xlights-elementor-widgets' ),
					'singular_name'      => __( 'Review', 'xlights-elementor-widgets' ),
					'add_new'            => __( 'Add Review', 'xlights-elementor-widgets' ),
					'add_new_item'       => __( 'Add New Review', 'xlights-elementor-widgets' ),
					'edit_item'          => __( 'Edit Review', 'xlights-elementor-widgets' ),
					'search_items'       => __( 'Search Reviews', 'xlights-elementor-widgets' ),
					'not_found'          => __( 'No reviews found.', 'xlights-elementor-widgets' ),
					'all_items'          => __( 'Reviews', 'xlights-elementor-widgets' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => 'xlights',
				'show_in_rest'        => false,
				'supports'            => array( 'title', 'editor', 'revisions' ),
				'capability_type'     => 'post',
				'rewrite'             => false,
			)
		);
	}

	/**
	 * @return array
	 */
	public static function sources() {
		return array(
			'google'   => 'Google',
			'facebook' => 'Facebook',
			'yelp'     => 'Yelp',
			'houzz'    => 'Houzz',
			'nextdoor' => 'Nextdoor',
			'direct'   => __( 'Direct to dealer', 'xlights-elementor-widgets' ),
			'other'    => __( 'Other', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * Approved reviews.
	 *
	 * @param array $args scope (local_first|local|brand), featured_only, count.
	 * @return array[]
	 */
	public static function get_reviews( array $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'scope'         => 'local_first',
				'featured_only' => false,
				'count'         => 6,
			)
		);

		$meta = array(
			'relation'    => 'AND',
			'permission'  => array(
				'key'   => self::META_PERMISSION,
				'value' => '1',
			),
			'attribution' => array(
				'key'     => self::META_ATTRIBUTION,
				'compare' => 'EXISTS',
			),
			'featured'    => array(
				'key'     => self::META_FEATURED,
				'compare' => 'EXISTS',
			),
		);
		if ( 'local' === $args['scope'] || 'brand' === $args['scope'] ) {
			$meta['attribution'] = array(
				'key'   => self::META_ATTRIBUTION,
				'value' => $args['scope'],
			);
		}
		if ( $args['featured_only'] ) {
			$meta['featured'] = array(
				'key'   => self::META_FEATURED,
				'value' => '1',
			);
		}

		$query = new WP_Query(
			array(
				'post_type'           => self::POST_TYPE,
				'post_status'         => 'publish',
				'posts_per_page'      => max( 1, min( 48, (int) $args['count'] ) ),
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
				'meta_query'          => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'orderby'             => array(
					'attribution' => 'DESC', // "local" sorts before "brand".
					'featured'    => 'DESC',
					'date'        => 'DESC',
				),
			)
		);

		$out = array();
		foreach ( $query->posts as $post ) {
			$out[] = self::data( $post );
		}
		return $out;
	}

	/**
	 * @param \WP_Post $post Review post.
	 * @return array
	 */
	public static function data( $post ) {
		$id      = $post->ID;
		$rating  = (int) get_post_meta( $id, self::META_RATING, true );
		$source  = (string) get_post_meta( $id, self::META_SOURCE, true );
		$sources = self::sources();
		$date    = (string) get_post_meta( $id, self::META_DATE, true );
		$project = (int) get_post_meta( $id, self::META_PROJECT, true );
		return array(
			'id'          => $id,
			'name'        => get_the_title( $id ),
			'text'        => (string) $post->post_content,
			'rating'      => max( 0, min( 5, $rating ) ),
			'source'      => isset( $sources[ $source ] ) ? $sources[ $source ] : '',
			'source_url'  => (string) get_post_meta( $id, self::META_SOURCE_URL, true ),
			'date'        => $date,
			'location'    => (string) get_post_meta( $id, self::META_LOCATION, true ),
			'project_url' => $project && 'publish' === get_post_status( $project ) ? get_permalink( $project ) : '',
			'project'     => $project ? get_the_title( $project ) : '',
			'photo'       => (int) get_post_meta( $id, self::META_PHOTO, true ),
			'attribution' => 'brand' === get_post_meta( $id, self::META_ATTRIBUTION, true ) ? 'brand' : 'local',
		);
	}

	/**
	 * Review card markup.
	 *
	 * @param array $r    Review data.
	 * @param array $opts rating, source, photo, large.
	 * @return string
	 */
	public static function render_card( array $r, array $opts = array() ) {
		$opts = wp_parse_args(
			$opts,
			array(
				'rating' => true,
				'source' => true,
				'photo'  => true,
				'large'  => false,
			)
		);
		$out = '<figure class="xl-review' . ( $opts['large'] ? ' xl-review--large' : '' ) . '">';
		if ( $opts['photo'] && $r['photo'] && wp_attachment_is_image( $r['photo'] ) ) {
			$out .= '<div class="xl-review__photo">' . Html::image(
				$r['photo'],
				array(
					'size'  => 'medium_large',
					'sizes' => '(max-width: 767px) 100vw, 33vw',
				)
			) . '</div>';
		}
		$out .= '<div class="xl-review__body">';
		if ( $opts['rating'] && $r['rating'] ) {
			/* translators: %d: rating out of five */
			$out .= '<p class="xl-review__stars" role="img" aria-label="' . esc_attr( sprintf( __( 'Rated %d out of 5', 'xlights-elementor-widgets' ), $r['rating'] ) ) . '">';
			for ( $i = 1; $i <= 5; $i++ ) {
				$out .= Icons::svg( $i <= $r['rating'] ? 'star-filled' : 'star', $i <= $r['rating'] ? 'is-on' : '' );
			}
			$out .= '</p>';
		}
		$out .= '<blockquote class="xl-review__text">' . wpautop( esc_html( wp_strip_all_tags( $r['text'] ) ) ) . '</blockquote>';
		$out .= '<figcaption class="xl-review__by"><strong>' . esc_html( $r['name'] ) . '</strong>';
		$meta = array_filter( array( $r['location'], $r['project'] ) );
		if ( $meta ) {
			$out .= '<span>' . esc_html( implode( ' · ', $meta ) ) . '</span>';
		}
		if ( $opts['source'] && ( $r['source'] || $r['date'] ) ) {
			$date = $r['date'] ? mysql2date( get_option( 'date_format' ), $r['date'] ) : '';
			/* translators: %s: review platform */
			$src  = $r['source'] ? sprintf( __( 'via %s', 'xlights-elementor-widgets' ), $r['source'] ) : '';
			$line = implode( ' · ', array_filter( array( $src, $date ) ) );
			if ( $r['source_url'] ) {
				$line = '<a href="' . esc_url( $r['source_url'] ) . '" target="_blank" rel="noopener nofollow">' . esc_html( $line ) . '<span class="xl-sr"> ' . esc_html__( '(opens in a new tab)', 'xlights-elementor-widgets' ) . '</span></a>';
			} else {
				$line = esc_html( $line );
			}
			$out .= '<small class="xl-review__source">' . $line . '</small>';
		}
		if ( 'brand' === $r['attribution'] ) {
			$out .= '<small class="xl-review__brand">' . esc_html__( 'Brand-level review from another X LIGHTS location', 'xlights-elementor-widgets' ) . '</small>';
		}
		$out .= '</figcaption></div></figure>';
		return $out;
	}
}
