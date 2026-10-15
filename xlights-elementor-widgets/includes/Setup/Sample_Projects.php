<?php
/**
 * Default project gallery built from the bundled X LIGHTS photos.
 *
 * On a site with no projects, the plugin adds a set of ordinary, editable
 * projects (Media Library images, categories, gallery, featured flag) so the
 * gallery and project sections are populated from day one. Titles are neutral
 * and no city is set: these are brand photos, not the dealer's own jobs, and
 * should be replaced or edited as real projects are added.
 *
 * Import runs in small batches on WP-Cron so a slow host never times out an
 * admin request. Everything it creates is tagged so it can be removed again.
 *
 * @package XLights
 */

namespace XLights\Setup;

use XLights\PostTypes\Projects;

defined( 'ABSPATH' ) || exit;

final class Sample_Projects {

	const META      = '_xlights_sample';
	const META_SRC  = '_xlights_sample_src';
	const STATE     = 'xlights_samples_state';
	const CRON_HOOK = 'xlights_import_samples';
	const BATCH     = 3;

	public static function init() {
		add_action( self::CRON_HOOK, array( __CLASS__, 'run_batch' ) );
		add_action( 'admin_post_xlights_samples', array( __CLASS__, 'handle' ) );
	}

	/**
	 * Projects to create. Images are bundled file names: "photos/<name>" (.webp)
	 * or a legacy file in assets/images.
	 *
	 * @return array
	 */
	public static function definitions() {
		return array(
			array(
				'key'      => 'modern-warm-holiday',
				'title'    => __( 'Modern home: warm white and holiday color', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Warm white for everyday evenings and full color for the holidays — the same permanent roofline lighting, changed from the X LIGHTS app.', 'xlights-elementor-widgets' ),
				'service'  => array( 'permanent-soffit-lighting' ),
				'type'     => 'residential',
				'featured' => true,
				'images'   => array( 'roofline-warm-white-modern', 'roofline-holiday-color-modern' ),
			),
			array(
				'key'      => 'roofline-dusk',
				'title'    => __( 'Two-story rooflines at dusk', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Permanent soffit lighting that follows every roofline, gable and garage.', 'xlights-elementor-widgets' ),
				'service'  => array( 'permanent-soffit-lighting' ),
				'type'     => 'residential',
				'featured' => false,
				'images'   => array( 'soffit-two-story-dusk', 'soffit-house-dusk-wide', 'roofline-two-story-modern', 'home-two-story-night.jpg', 'home-single-story-dusk.jpg' ),
			),
			array(
				'key'      => 'winter-season',
				'title'    => __( 'Roofline lighting through the winter', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Installed once and ready for every season — no ladders and no tangled strands when it snows.', 'xlights-elementor-widgets' ),
				'service'  => array( 'permanent-soffit-lighting' ),
				'type'     => 'residential',
				'featured' => false,
				'images'   => array( 'soffit-winter-warm', 'soffit-winter-night', 'holiday-color-family' ),
			),
			array(
				'key'      => 'garage-entry',
				'title'    => __( 'Garage and entry lighting', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Roofline lights above the garage and entry, plus color for celebrations.', 'xlights-elementor-widgets' ),
				'service'  => array( 'permanent-soffit-lighting' ),
				'type'     => 'residential',
				'featured' => false,
				'images'   => array( 'soffit-garage-night', 'steps-color-celebration', 'installer-roofline' ),
			),
			array(
				'key'      => 'deck-stairs',
				'title'    => __( 'Deck stairs with step lights', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'A light under every tread makes the stairs easy to use after dark, controlled from the X LIGHTS app.', 'xlights-elementor-widgets' ),
				'service'  => array( 'deck-lighting' ),
				'type'     => 'residential',
				'featured' => true,
				'images'   => array( 'deck-stairs-step-lights', 'deck-steps-app', 'deck-stairs-dot-lights' ),
			),
			array(
				'key'      => 'covered-deck',
				'title'    => __( 'Covered deck with flex and deck lights', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Flex lighting around the pergola and round deck lights set flush into the boards.', 'xlights-elementor-widgets' ),
				'service'  => array( 'deck-lighting' ),
				'type'     => 'residential',
				'featured' => false,
				'images'   => array( 'deck-flex-pergola', 'deck-flex-detail', 'deck-dot-lights-boards', 'deck-dot-light' ),
			),
			array(
				'key'      => 'patio-steps',
				'title'    => __( 'Patio steps and fire pit', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Glowing step edges around a raised patio and fire pit.', 'xlights-elementor-widgets' ),
				'service'  => array( 'deck-lighting', 'landscape-lighting' ),
				'type'     => 'residential',
				'featured' => false,
				'images'   => array( 'deck-steps-desert', 'floating-steps', 'patio-pool-firepit' ),
			),
			array(
				'key'      => 'uplit-trees',
				'title'    => __( 'Uplit trees and garden walls', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Spot and in-ground lights that bring trees and walls to life after sunset.', 'xlights-elementor-widgets' ),
				'service'  => array( 'landscape-lighting' ),
				'type'     => 'residential',
				'featured' => true,
				'images'   => array( 'landscape-uplit-trees', 'landscape-tree-wall', 'landscape-tree-deck-dots', 'tree-spotlight' ),
			),
			array(
				'key'      => 'garden-steps',
				'title'    => __( 'Garden steps and walkways', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Step and path lighting that guides the way through the garden.', 'xlights-elementor-widgets' ),
				'service'  => array( 'landscape-lighting' ),
				'type'     => 'residential',
				'featured' => false,
				'images'   => array( 'landscape-stone-steps', 'landscape-walkway-pergola', 'entry-steps-night', 'garden-steps-path-lights', 'path-lights-walkway' ),
			),
			array(
				'key'      => 'landscape-fixtures',
				'title'    => __( 'Path, spot and in-ground lights', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Landscape fixtures up close: path lights, adjustable spotlights, in-ground lights and wall lights.', 'xlights-elementor-widgets' ),
				'service'  => array( 'landscape-lighting' ),
				'type'     => 'residential',
				'featured' => false,
				'images'   => array( 'landscape-path-pagoda', 'landscape-inground-green', 'landscape-spot-green', 'landscape-path-ring', 'landscape-inground-blue', 'landscape-inground-mulch', 'landscape-path-garden', 'landscape-wall-light' ),
			),
			array(
				'key'      => 'backyard-string',
				'title'    => __( 'Backyard string lights', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Hanging bulb lights over the backyard, deck and fire pit lounge.', 'xlights-elementor-widgets' ),
				'service'  => array( 'bistro-patio-lighting' ),
				'type'     => 'residential',
				'featured' => true,
				'images'   => array( 'bistro-string-backyard', 'backyard-overview', 'deck-string-lights', 'pergola-string-lights', 'firepit-lounge-string-lights' ),
			),
			array(
				'key'      => 'outdoor-kitchen',
				'title'    => __( 'Outdoor kitchen, pergola and pool', 'xlights-elementor-widgets' ),
				'excerpt'  => __( 'Warm light for an outdoor bar, lounge and pool patio.', 'xlights-elementor-widgets' ),
				'service'  => array( 'bistro-patio-lighting' ),
				'type'     => 'residential',
				'featured' => false,
				'images'   => array( 'pergola-bar', 'pergola-lounge', 'pool-patio-dusk' ),
			),
		);
	}

	/**
	 * Alt text for bundled photos.
	 *
	 * @param string $name Image name.
	 * @return string
	 */
	private static function alt( $name ) {
		$alts = array(
			'roofline-warm-white-modern'    => __( 'Modern home at dusk with warm white permanent roofline lighting', 'xlights-elementor-widgets' ),
			'roofline-holiday-color-modern' => __( 'The same modern home with red and green holiday roofline lighting', 'xlights-elementor-widgets' ),
			'soffit-two-story-dusk'         => __( 'Two-story home at dusk with permanent soffit lights along every roofline', 'xlights-elementor-widgets' ),
			'soffit-house-dusk-wide'        => __( 'Two-story home with warm white roofline lighting at dusk', 'xlights-elementor-widgets' ),
			'roofline-two-story-modern'     => __( 'Modern two-story home with roofline lighting at night', 'xlights-elementor-widgets' ),
			'home-two-story-night.jpg'      => __( 'Two-story home at night with permanent roofline lighting', 'xlights-elementor-widgets' ),
			'home-single-story-dusk.jpg'    => __( 'Family in front of their home with warm white roofline lighting', 'xlights-elementor-widgets' ),
			'soffit-winter-warm'            => __( 'Snow-covered roof with warm white soffit lighting', 'xlights-elementor-widgets' ),
			'soffit-winter-night'           => __( 'Home in the snow at night with soft white roofline lighting', 'xlights-elementor-widgets' ),
			'holiday-color-family'          => __( 'Family in the snow looking at their home lit in holiday colors', 'xlights-elementor-widgets' ),
			'soffit-garage-night'           => __( 'Garage and gable roofline lit at night', 'xlights-elementor-widgets' ),
			'steps-color-celebration'       => __( 'Home with purple roofline and step lighting at sunset', 'xlights-elementor-widgets' ),
			'installer-roofline'            => __( 'X LIGHTS installer fitting roofline lights above a garage', 'xlights-elementor-widgets' ),
			'deck-stairs-step-lights'       => __( 'Deck stairs at dusk with a step light under each tread', 'xlights-elementor-widgets' ),
			'deck-steps-app'                => __( 'Holding the X LIGHTS app in front of deck stairs lit with step lights', 'xlights-elementor-widgets' ),
			'deck-stairs-dot-lights'        => __( 'Wood deck at night with warm step lights and small recessed deck lights', 'xlights-elementor-widgets' ),
			'deck-flex-pergola'             => __( 'Covered deck with flex lighting around the pergola and a fire table', 'xlights-elementor-widgets' ),
			'deck-flex-detail'              => __( 'Flex light channel along the edge of a composite deck', 'xlights-elementor-widgets' ),
			'deck-dot-lights-boards'        => __( 'Small round deck lights set flush into composite deck boards', 'xlights-elementor-widgets' ),
			'deck-dot-light'                => __( 'Round deck light glowing blue in a deck board', 'xlights-elementor-widgets' ),
			'deck-steps-desert'             => __( 'Raised patio with glowing step edges around a fire pit at dusk', 'xlights-elementor-widgets' ),
			'floating-steps'                => __( 'Floating garden steps with light under each tread at night', 'xlights-elementor-widgets' ),
			'patio-pool-firepit'            => __( 'Patio at dusk with a fire pit, lit steps, an uplit palm and a pool', 'xlights-elementor-widgets' ),
			'landscape-uplit-trees'         => __( 'A row of tall trees uplit in warm white along a lawn at dusk', 'xlights-elementor-widgets' ),
			'landscape-tree-wall'           => __( 'Uplit tree beside a lit garden wall at dusk', 'xlights-elementor-widgets' ),
			'landscape-tree-deck-dots'      => __( 'Uplit tree behind a deck with small deck lights', 'xlights-elementor-widgets' ),
			'tree-spotlight'                => __( 'Spotlight in a rock bed lighting up a tree', 'xlights-elementor-widgets' ),
			'landscape-stone-steps'         => __( 'Stone garden steps and retaining walls with lights under each step', 'xlights-elementor-widgets' ),
			'landscape-walkway-pergola'     => __( 'Wooden walkway with in-ground lights leading to a pergola', 'xlights-elementor-widgets' ),
			'entry-steps-night'             => __( 'Entry walkway at night with lit stair treads and uplit olive trees', 'xlights-elementor-widgets' ),
			'garden-steps-path-lights'      => __( 'Stone garden steps with step lights, path lights and uplit shrubs', 'xlights-elementor-widgets' ),
			'path-lights-walkway'           => __( 'Path light casting a warm pool of light across a walkway at dusk', 'xlights-elementor-widgets' ),
			'landscape-path-pagoda'         => __( 'Architectural path light in a rock bed', 'xlights-elementor-widgets' ),
			'landscape-inground-green'      => __( 'In-ground light glowing green among rocks at the base of a tree', 'xlights-elementor-widgets' ),
			'landscape-spot-green'          => __( 'Adjustable spotlight glowing green in a rock bed', 'xlights-elementor-widgets' ),
			'landscape-path-ring'           => __( 'Round-top path light beside a garden walkway', 'xlights-elementor-widgets' ),
			'landscape-inground-blue'       => __( 'In-ground light glowing blue among rocks', 'xlights-elementor-widgets' ),
			'landscape-inground-mulch'      => __( 'In-ground spotlight set in mulch at the base of a tree', 'xlights-elementor-widgets' ),
			'landscape-path-garden'         => __( 'Path light in a planted garden bed beside a walkway', 'xlights-elementor-widgets' ),
			'landscape-wall-light'          => __( 'Wall light washing green light down a wood fence', 'xlights-elementor-widgets' ),
			'bistro-string-backyard'        => __( 'Backyard at night with string lights above a lawn and path lights', 'xlights-elementor-widgets' ),
			'backyard-overview'             => __( 'Landscaped backyard at dusk with string lights and a lit pergola', 'xlights-elementor-widgets' ),
			'deck-string-lights'            => __( 'Deck with string lights along the roofline and posts at night', 'xlights-elementor-widgets' ),
			'pergola-string-lights'         => __( 'String lights next to a louvered pergola at night', 'xlights-elementor-widgets' ),
			'firepit-lounge-string-lights'  => __( 'Fire pit lounge with lights along the seating at dusk', 'xlights-elementor-widgets' ),
			'pergola-bar'                   => __( 'Outdoor bar under a lit pergola at dusk', 'xlights-elementor-widgets' ),
			'pergola-lounge'                => __( 'Pergola lounge and outdoor kitchen with warm lighting', 'xlights-elementor-widgets' ),
			'pool-patio-dusk'               => __( 'Pool and patio at dusk with warm landscape lighting', 'xlights-elementor-widgets' ),
		);
		return isset( $alts[ $name ] ) ? $alts[ $name ] : '';
	}

	/**
	 * Absolute path of a bundled image.
	 *
	 * @param string $name Name ("photos/<name>" without extension, or a legacy file).
	 * @return string Path, or '' if missing.
	 */
	private static function source_path( $name ) {
		$file = false !== strpos( $name, '.' ) ? $name : 'photos/' . $name . '.webp';
		$path = XLIGHTS_PATH . 'assets/images/' . $file;
		return is_readable( $path ) ? $path : '';
	}

	/**
	 * True when the site has no projects of its own (any status).
	 *
	 * @return bool
	 */
	public static function site_is_empty() {
		$q = new \WP_Query(
			array(
				'post_type'      => Projects::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		return ! $q->posts;
	}

	/**
	 * Queue the import (background, batched). Only for sites without projects
	 * unless forced, and only once unless forced.
	 *
	 * @param bool $force Import even if projects exist or it ran before.
	 * @return bool Whether an import was queued.
	 */
	public static function schedule( $force = false ) {
		$state = get_option( self::STATE, '' );
		if ( ! $force && ( '' !== $state || ! self::site_is_empty() ) ) {
			return false;
		}
		update_option( self::STATE, 'queued', false );
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_single_event( time() + 5, self::CRON_HOOK );
		}
		return true;
	}

	/**
	 * Import the next few projects; reschedules itself until done.
	 *
	 * @param int $limit Projects per run (0 = all).
	 * @return int Projects created in this run.
	 */
	public static function run_batch( $limit = self::BATCH ) {
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
		Projects::ensure_terms();
		$done    = self::existing_keys();
		$created = 0;
		$order   = 0;
		foreach ( self::definitions() as $def ) {
			++$order;
			if ( isset( $done[ $def['key'] ] ) ) {
				continue;
			}
			if ( $limit && $created >= $limit ) {
				wp_schedule_single_event( time() + 10, self::CRON_HOOK );
				return $created;
			}
			if ( self::create( $def, $order ) ) {
				++$created;
			}
		}
		update_option( self::STATE, 'done', false );
		return $created;
	}

	/**
	 * Sample project keys already created.
	 *
	 * @return array key => post ID
	 */
	private static function existing_keys() {
		$out = array();
		$q   = new \WP_Query(
			array(
				'post_type'      => Projects::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 100,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_key'       => self::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
		foreach ( $q->posts as $id ) {
			$out[ (string) get_post_meta( $id, self::META, true ) ] = $id;
		}
		return $out;
	}

	/**
	 * Create one project with its images.
	 *
	 * @param array $def   Definition.
	 * @param int   $order Menu order.
	 * @return int Post ID or 0.
	 */
	private static function create( array $def, $order ) {
		$ids = array();
		foreach ( $def['images'] as $name ) {
			$aid = self::attachment( $name );
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
				'post_content' => '<p>' . esc_html( $def['excerpt'] ) . '</p>',
				'menu_order'   => (int) $order,
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

	/**
	 * Copy a bundled image into the Media Library (reused if already imported).
	 *
	 * @param string $name Image name.
	 * @return int Attachment ID or 0.
	 */
	private static function attachment( $name ) {
		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::META_SRC, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $name, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		if ( $existing ) {
			return (int) $existing[0];
		}
		$path = self::source_path( $name );
		if ( '' === $path ) {
			return 0;
		}
		$bits = wp_upload_bits( 'xlights-' . basename( $path ), null, (string) file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! empty( $bits['error'] ) ) {
			return 0;
		}
		$type = wp_check_filetype( $bits['file'] );
		$alt  = self::alt( $name );
		$aid  = wp_insert_attachment(
			array(
				'post_mime_type' => $type['type'],
				'post_title'     => '' !== $alt ? $alt : sanitize_text_field( pathinfo( $path, PATHINFO_FILENAME ) ),
				'post_status'    => 'inherit',
			),
			$bits['file'],
			0,
			true
		);
		if ( is_wp_error( $aid ) || ! $aid ) {
			return 0;
		}
		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		wp_update_attachment_metadata( $aid, wp_generate_attachment_metadata( $aid, $bits['file'] ) );
		update_post_meta( $aid, '_wp_attachment_image_alt', $alt );
		update_post_meta( $aid, self::META, '1' );
		update_post_meta( $aid, self::META_SRC, $name );
		return (int) $aid;
	}

	/**
	 * Delete sample projects and the images imported for them. Projects and
	 * images an editor created are never touched; an imported image that is now
	 * used by a non-sample project is kept.
	 *
	 * @return int Projects removed.
	 */
	public static function remove() {
		$removed = 0;
		foreach ( self::existing_keys() as $id ) {
			if ( wp_delete_post( $id, true ) ) {
				++$removed;
			}
		}
		$images = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 200,
				'fields'         => 'ids',
				'meta_key'       => self::META_SRC, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);
		foreach ( $images as $aid ) {
			if ( ! self::attachment_in_use( (int) $aid ) ) {
				wp_delete_attachment( $aid, true );
			}
		}
		wp_clear_scheduled_hook( self::CRON_HOOK );
		update_option( self::STATE, 'removed', false );
		return $removed;
	}

	/**
	 * Whether an imported image is still used after the samples are gone: by a
	 * project (thumbnail or gallery) or picked in an Elementor widget.
	 *
	 * @param int $aid Attachment.
	 * @return bool
	 */
	private static function attachment_in_use( $aid ) {
		$q = new \WP_Query(
			array(
				'post_type'      => Projects::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 200,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					'relation' => 'OR',
					array(
						'key'   => '_thumbnail_id',
						'value' => (string) $aid,
					),
					array(
						'key'     => Projects::META_GALLERY,
						'value'   => (string) $aid,
						'compare' => 'LIKE',
					),
				),
			)
		);
		foreach ( $q->posts as $pid ) {
			if ( (int) get_post_thumbnail_id( $pid ) === $aid || in_array( $aid, Projects::gallery_ids( $pid ), true ) ) {
				return true;
			}
		}
		global $wpdb;
		$used = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data' AND ( meta_value LIKE %s OR meta_value LIKE %s )",
				'%"id":' . (int) $aid . ',%',
				'%"id":"' . (int) $aid . '"%'
			)
		);
		return (int) $used > 0;
	}

	/**
	 * Status for the admin screen.
	 *
	 * @return array{state:string,count:int}
	 */
	public static function status() {
		return array(
			'state' => (string) get_option( self::STATE, '' ),
			'count' => count( self::existing_keys() ),
		);
	}

	/**
	 * Admin button: add or remove sample projects.
	 */
	public static function handle() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'xlights-elementor-widgets' ), 403 );
		}
		check_admin_referer( 'xlights_samples' );
		$do = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : '';
		if ( 'remove' === $do ) {
			$n   = self::remove();
			$msg = sprintf( /* translators: %d: number of projects */ _n( '%d sample project removed.', '%d sample projects removed.', $n, 'xlights-elementor-widgets' ), $n );
		} else {
			// Run the first batch now so the editor sees results right away; cron finishes the rest.
			update_option( self::STATE, 'queued', false );
			$n = self::run_batch();
			if ( 'done' !== get_option( self::STATE ) && ! wp_next_scheduled( self::CRON_HOOK ) ) {
				wp_schedule_single_event( time() + 5, self::CRON_HOOK );
			}
			$msg = 'done' === get_option( self::STATE )
				? __( 'Sample projects added. Edit or replace them under X LIGHTS → Projects.', 'xlights-elementor-widgets' )
				: __( 'Adding sample projects — the first ones are ready and the rest are being added in the background.', 'xlights-elementor-widgets' );
		}
		set_transient( 'xlights_setup_report_' . get_current_user_id(), array( array( 'ok', $msg ) ), 5 * MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'admin.php?page=' . \XLights\Admin\Setup_Page::SLUG ) );
		exit;
	}
}
