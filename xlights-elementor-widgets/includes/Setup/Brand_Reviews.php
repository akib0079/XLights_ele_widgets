<?php
/**
 * Brand-level Google reviews supplied by X LIGHTS (customers of X LIGHTS in
 * Canada). They are added once as ordinary reviews, labeled "brand-level" so
 * the site never presents them as reviews of the local dealer. Text is kept
 * exactly as written; no names, ratings or dates were supplied, so none are
 * shown. Editors can edit or delete them like any review; a deleted review is
 * not added back.
 *
 * @package XLights
 */

namespace XLights\Setup;

use XLights\PostTypes\Reviews;

defined( 'ABSPATH' ) || exit;

final class Brand_Reviews {

	const META   = '_xlights_brand_review';
	const OPTION = 'xlights_brand_reviews_seeded';

	/**
	 * The supplied reviews, verbatim.
	 *
	 * @return array key => text
	 */
	public static function definitions() {
		return array(
			'google-ca-backyard'     => 'We hired X lights to add lighting in our backyard. They worked closely with us, making sure we got exactly what we were looking for. Their system and the app is very user friendly - affordable and quality product as well! System was installed in an afternoon and have had zero issues at all. Another plus is that the system will allow for future add ons as our backyard continues renovation! Would highly recommend.',
			'google-ca-installed'    => 'Our X Lights were installed recently and they’re exactly what we’d hoped for. They look fantastic and with the simple app we can do whatever we want with them. The company had them installed really soon after we booked and they were nothing short of great to deal with. We’ll be recommending X Lights Outdoor Lighting to anyone who’s looking to light up their home.',
			'google-ca-fast-service' => 'Fast service. They worked around my schedule and got them on super quick right before Christmas! They showed up on time, taught me how to use them and program them and best of all the lights look great! The app is reliable and there are many colour and patter options. The price point was perfect and very much worth it. Highly recommend!',
		);
	}

	/**
	 * Add the reviews once (missing ones only, never after they were seeded).
	 *
	 * @param bool $force Add missing reviews even if they were seeded before.
	 * @return int Reviews created.
	 */
	public static function seed( $force = false ) {
		if ( ! $force && get_option( self::OPTION ) ) {
			return 0;
		}
		if ( ! post_type_exists( Reviews::POST_TYPE ) ) {
			Reviews::register();
		}
		$existing = array();
		$q        = new \WP_Query(
			array(
				'post_type'      => Reviews::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 50,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_key'       => self::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
		foreach ( $q->posts as $id ) {
			$existing[ (string) get_post_meta( $id, self::META, true ) ] = true;
		}
		$created = 0;
		$i       = 0;
		foreach ( self::definitions() as $key => $text ) {
			++$i;
			if ( isset( $existing[ $key ] ) ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'post_type'    => Reviews::POST_TYPE,
					'post_status'  => 'publish',
					'post_title'   => __( 'Google review', 'xlights-elementor-widgets' ),
					'post_content' => $text,
					'menu_order'   => $i,
				),
				true
			);
			if ( is_wp_error( $id ) || ! $id ) {
				continue;
			}
			update_post_meta( $id, self::META, $key );
			update_post_meta( $id, Reviews::META_SOURCE, 'google' );
			update_post_meta( $id, Reviews::META_LOCATION, __( 'Canada', 'xlights-elementor-widgets' ) );
			update_post_meta( $id, Reviews::META_ATTRIBUTION, 'brand' );
			update_post_meta( $id, Reviews::META_FEATURED, '1' );
			// Supplied by X LIGHTS for use on this site (public Google reviews of X LIGHTS).
			update_post_meta( $id, Reviews::META_PERMISSION, '1' );
			++$created;
		}
		update_option( self::OPTION, 1, false );
		return $created;
	}
}
