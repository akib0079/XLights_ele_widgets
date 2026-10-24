<?php
/**
 * Real projects supplied by the dealer, added to the project gallery once.
 *
 * Unlike the sample projects these are the dealer's own work: they are not
 * tagged as samples, so "Remove sample projects" never deletes them. A project
 * is skipped when one with the same title already exists (for example one
 * added by hand), and a deleted project is not added back.
 *
 * @package XLights
 */

namespace XLights\Setup;

use XLights\PostTypes\Projects;

defined( 'ABSPATH' ) || exit;

final class Client_Projects {

	const META     = '_xlights_client_project';
	const META_SRC = '_xlights_client_src';
	const OPTION   = 'xlights_client_projects_seeded';

	/**
	 * @return array
	 */
	public static function definitions() {
		return array(
			array(
				'key'      => 'cottonmouth-dispensary',
				'title'    => __( 'Cottonmouth Dispensary', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'X LIGHTS soffit lights and line lighting bring the brand’s signature colors into the building’s exterior — a polished storefront with a stronger nighttime presence, now visible from the highway.', 'xlights-elementor-widgets' ),
				'content'  => __( '<p>The owner of Cottonmouth Dispensary wanted to give his commercial property a more upscale appearance and bring the company’s signature colors into the building’s exterior.</p><p>Using X LIGHTS soffit lights and line lighting, we created a coordinated lighting design that highlights the architecture and carries the brand colors across the property.</p><p>With most customers visiting in the evening, the lighting makes an impression when it matters most. The result is a polished, distinctive storefront with a stronger nighttime presence — now visible from the highway.</p><p>This project shows how commercial lighting can enhance a building’s appearance while making its branding a visible part of the architecture.</p>', 'xlights-elementor-widgets' ),
				'service'  => array( 'permanent-soffit-lighting' ),
				'type'     => 'commercial',
				'featured' => true,
				'images'   => array(
					'commercial-cottonmouth-roofline'   => __( 'Cottonmouth Dispensary sign at dusk under green X LIGHTS soffit lights along the cornice', 'xlights-elementor-widgets' ),
					'commercial-cottonmouth-entrance'   => __( 'Cottonmouth Dispensary entrance at dusk with green line lighting on the columns and around the logo sign', 'xlights-elementor-widgets' ),
					'commercial-cottonmouth-storefront' => __( 'Wide view of the Cottonmouth Dispensary storefront at dusk with green lighting across the building', 'xlights-elementor-widgets' ),
				),
			),
		);
	}

	/**
	 * Add the projects once.
	 *
	 * @param bool $force Add missing projects even if they were added before.
	 * @return int Projects created.
	 */
	public static function seed( $force = false ) {
		if ( ! $force && get_option( self::OPTION ) ) {
			return 0;
		}
		if ( ! post_type_exists( Projects::POST_TYPE ) ) {
			Projects::register();
		}
		Projects::ensure_terms();
		$created = 0;
		foreach ( self::definitions() as $def ) {
			if ( self::exists( $def ) ) {
				continue;
			}
			if ( self::create( $def ) ) {
				$created++;
			}
		}
		update_option( self::OPTION, 1, false );
		return $created;
	}

	/**
	 * Whether the project is already there (added by us or by hand).
	 *
	 * @param array $def Definition.
	 * @return bool
	 */
	private static function exists( array $def ) {
		$by_key = get_posts(
			array(
				'post_type'      => Projects::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $def['key'], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		if ( $by_key ) {
			return true;
		}
		$by_title = new \WP_Query(
			array(
				'post_type'      => Projects::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'title'          => $def['title'],
			)
		);
		return (bool) $by_title->posts;
	}

	/**
	 * @param array $def Definition.
	 * @return int Post ID or 0.
	 */
	private static function create( array $def ) {
		$ids = array();
		foreach ( $def['images'] as $name => $alt ) {
			$aid = Sample_Projects::import_image( $name, $alt, self::META_SRC, false );
			if ( $aid ) {
				$ids[] = $aid;
			}
		}
		if ( ! $ids ) {
			return 0;
		}
		$post_id = wp_insert_post(
			array(
				'post_type'    => Projects::POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => $def['title'],
				'post_excerpt' => $def['excerpt'],
				'post_content' => wp_kses_post( $def['content'] ),
				'menu_order'   => 0,
			),
			true
		);
		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 0;
		}
		update_post_meta( $post_id, self::META, $def['key'] );
		set_post_thumbnail( $post_id, $ids[0] );
		update_post_meta( $post_id, Projects::META_GALLERY, implode( ',', $ids ) );
		update_post_meta( $post_id, Projects::META_FEATURED, $def['featured'] ? '1' : '0' );
		update_post_meta( $post_id, Projects::META_HAS_BA, '0' );
		wp_set_object_terms( $post_id, $def['service'], Projects::TAX_SERVICE );
		wp_set_object_terms( $post_id, array( $def['type'] ), Projects::TAX_PROPERTY );
		foreach ( $ids as $aid ) {
			if ( ! wp_get_post_parent_id( $aid ) ) {
				wp_update_post(
					array(
						'ID'          => $aid,
						'post_parent' => $post_id,
					)
				);
			}
		}
		return (int) $post_id;
	}
}
