<?php
/**
 * Public, read-only REST endpoint for gallery filtering and pagination.
 * Returns only published projects; no private data is exposed.
 *
 * GET /wp-json/xlights/v1/projects?filter=deck&page=2&count=9&featured=0
 *
 * @package XLights
 */

namespace XLights\Frontend;

use XLights\PostTypes\Projects;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

final class Projects_Ajax {

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function routes() {
		register_rest_route(
			'xlights/v1',
			'/projects',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'filter'   => array(
						'type'    => 'string',
						'default' => 'all',
						'enum'    => array_keys( Projects::filters() ),
					),
					'page'     => array(
						'type'    => 'integer',
						'default' => 1,
						'minimum' => 1,
						'maximum' => 200,
					),
					'count'    => array(
						'type'    => 'integer',
						'default' => 9,
						'minimum' => 1,
						'maximum' => 48,
					),
					'featured' => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
			)
		);
	}

	/**
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function get( WP_REST_Request $request ) {
		$page  = (int) $request['page'];
		$query = Projects::query(
			array(
				'filter'        => (string) $request['filter'],
				'count'         => (int) $request['count'],
				'page'          => $page,
				'featured_only' => (bool) $request['featured'],
			)
		);
		$html = '';
		foreach ( $query->posts as $post ) {
			$html .= Projects::render_card( $post );
		}
		$response = new WP_REST_Response(
			array(
				'html'     => $html,
				'page'     => $page,
				'has_more' => $page < (int) $query->max_num_pages,
				'total'    => (int) $query->found_posts,
			)
		);
		$response->header( 'Cache-Control', 'public, max-age=300' );
		return $response;
	}
}
