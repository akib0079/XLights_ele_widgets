<?php
/**
 * Private lead storage. Leads are never public, never in REST, and only users
 * with the manage_xlights_leads capability (administrators by default) can
 * view, edit, export or delete them.
 *
 * @package XLights
 */

namespace XLights\PostTypes;

defined( 'ABSPATH' ) || exit;

final class Leads {

	const POST_TYPE = 'xlights_lead';
	const CAP       = 'manage_xlights_leads';

	/** Meta keys (all protected / underscore-prefixed). */
	const META_FIELDS = array(
		'first_name',
		'last_name',
		'phone',
		'email',
		'address',
		'city',
		'state',
		'zip',
		'property_type',
		'company',
		'business_type',
		'interests',
		'message',
		'files',
		'dealer',
		'variant',
		'source_url',
		'ip_hash',
		'user_agent',
		'mail_status',
		'mail_error',
		'status',
		'notes',
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_filter( 'user_has_cap', array( __CLASS__, 'grant_to_admins' ), 10, 4 );
		add_action( 'before_delete_post', array( __CLASS__, 'on_delete' ) );
	}

	/**
	 * Remove stored photos when a lead is permanently deleted.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function on_delete( $post_id ) {
		if ( self::POST_TYPE === get_post_type( $post_id ) ) {
			self::delete_files( $post_id );
		}
	}

	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'          => __( 'Leads', 'xlights-elementor-widgets' ),
					'singular_name' => __( 'Lead', 'xlights-elementor-widgets' ),
					'edit_item'     => __( 'Lead Details', 'xlights-elementor-widgets' ),
					'search_items'  => __( 'Search Leads', 'xlights-elementor-widgets' ),
					'not_found'     => __( 'No leads yet.', 'xlights-elementor-widgets' ),
					'all_items'     => __( 'Leads', 'xlights-elementor-widgets' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => 'xlights',
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'show_in_rest'        => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title' ),
				'map_meta_cap'        => false,
				'capabilities'        => array(
					'edit_post'              => self::CAP,
					'read_post'              => self::CAP,
					'delete_post'            => self::CAP,
					'edit_posts'             => self::CAP,
					'edit_others_posts'      => self::CAP,
					'publish_posts'          => self::CAP,
					'read_private_posts'     => self::CAP,
					'delete_posts'           => self::CAP,
					'delete_private_posts'   => self::CAP,
					'delete_others_posts'    => self::CAP,
					'delete_published_posts' => self::CAP,
					'edit_private_posts'     => self::CAP,
					'edit_published_posts'   => self::CAP,
					'create_posts'           => 'do_not_allow',
				),
			)
		);
	}

	/**
	 * Users who can manage options always manage leads (covers sites where the
	 * activation hook did not add the role capability, e.g. network activation).
	 *
	 * @param array $allcaps All caps.
	 * @param array $caps    Required caps.
	 * @param array $args    Args.
	 * @param mixed $user    User.
	 * @return array
	 */
	public static function grant_to_admins( $allcaps, $caps, $args, $user ) {
		if ( in_array( self::CAP, (array) $caps, true ) && ! empty( $allcaps['manage_options'] ) ) {
			$allcaps[ self::CAP ] = true;
		}
		return $allcaps;
	}

	/**
	 * @return array status => label
	 */
	public static function statuses() {
		return array(
			'new'       => __( 'New', 'xlights-elementor-widgets' ),
			'contacted' => __( 'Contacted', 'xlights-elementor-widgets' ),
			'scheduled' => __( 'Consultation scheduled', 'xlights-elementor-widgets' ),
			'quoted'    => __( 'Quote sent', 'xlights-elementor-widgets' ),
			'won'       => __( 'Won', 'xlights-elementor-widgets' ),
			'lost'      => __( 'Closed – not proceeding', 'xlights-elementor-widgets' ),
			'spam'      => __( 'Spam', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * Persist a validated lead.
	 *
	 * @param array $lead Sanitized lead data keyed by META_FIELDS.
	 * @return int|\WP_Error Post ID.
	 */
	public static function create( array $lead ) {
		$name  = trim( $lead['first_name'] . ' ' . $lead['last_name'] );
		$title = $name . ( '' !== $lead['city'] ? ' — ' . $lead['city'] : '' );

		$post_id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'private',
				'post_title'  => $title,
				'post_author' => 0,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}
		$lead['status'] = 'new';
		foreach ( self::META_FIELDS as $key ) {
			if ( array_key_exists( $key, $lead ) ) {
				update_post_meta( $post_id, '_xl_' . $key, $lead[ $key ] );
			}
		}
		return (int) $post_id;
	}

	/**
	 * Read a lead.
	 *
	 * @param int $post_id Lead ID.
	 * @return array
	 */
	public static function get( $post_id ) {
		$out = array(
			'id'   => (int) $post_id,
			'date' => get_post_time( 'U', true, $post_id ),
		);
		foreach ( self::META_FIELDS as $key ) {
			$out[ $key ] = get_post_meta( $post_id, '_xl_' . $key, true );
		}
		$out['interests'] = is_array( $out['interests'] ) ? $out['interests'] : array();
		$out['files']     = is_array( $out['files'] ) ? $out['files'] : array();
		$out['status']    = $out['status'] ? $out['status'] : 'new';
		return $out;
	}

	/**
	 * Delete a lead and its uploaded files.
	 *
	 * @param int $post_id Lead ID.
	 */
	public static function delete_files( $post_id ) {
		$files = get_post_meta( $post_id, '_xl_files', true );
		if ( ! is_array( $files ) ) {
			return;
		}
		foreach ( $files as $file ) {
			$path = \XLights\Forms\Upload_Handler::absolute_path( isset( $file['path'] ) ? $file['path'] : '' );
			if ( $path && is_file( $path ) ) {
				wp_delete_file( $path );
			}
		}
	}
}
