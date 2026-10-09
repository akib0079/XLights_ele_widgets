<?php
/**
 * The twelve dealer-site page templates: labels, URLs, hierarchy and the
 * mapping between templates and WordPress pages created by the setup routine.
 *
 * @package XLights
 */

namespace XLights\Pages;

defined( 'ABSPATH' ) || exit;

final class Page_Registry {

	/** Option holding template => page ID. */
	const OPTION = 'xlights_pages';

	/** @var array Loaded template definitions. */
	private static $definitions = array();

	/** @var array Resolved template => page ID (positive results only). */
	private static $resolved = array();

	/**
	 * @return array
	 */
	public static function all() {
		return array(
			'home'       => array(
				'label'       => __( 'Home', 'xlights-elementor-widgets' ),
				'title'       => __( 'Home', 'xlights-elementor-widgets' ),
				'slug'        => 'home',
				'parent'      => '',
				'path'        => '/',
				'seo_title'   => __( 'Permanent Soffit & Outdoor Lighting in {area} | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Permanent soffit lighting, bistro, deck and landscape lighting designed and installed by {dealer}. Warm white every night, color for every celebration.', 'xlights-elementor-widgets' ),
			),
			'about'      => array(
				'label'       => __( 'About Us', 'xlights-elementor-widgets' ),
				'title'       => __( 'About Us', 'xlights-elementor-widgets' ),
				'slug'        => 'about-us',
				'parent'      => '',
				'path'        => '/about-us/',
				'seo_title'   => __( 'About {dealer} | Outdoor Lighting in {area}', 'xlights-elementor-widgets' ),
				'description' => __( 'Meet {dealer}, your local X LIGHTS team for permanent soffit, bistro, deck and landscape lighting in {area}.', 'xlights-elementor-widgets' ),
			),
			'app'        => array(
				'label'       => __( 'X LIGHTS App', 'xlights-elementor-widgets' ),
				'title'       => __( 'X LIGHTS App', 'xlights-elementor-widgets' ),
				'slug'        => 'x-lights-app',
				'parent'      => '',
				'path'        => '/x-lights-app/',
				'seo_title'   => __( 'X LIGHTS App: Smart Outdoor Lighting Control | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Control compatible X LIGHTS soffit, deck, landscape and bistro lighting from one app: colors, brightness, schedules, seasonal patterns and shared access.', 'xlights-elementor-widgets' ),
			),
			'reviews'    => array(
				'label'       => __( 'Reviews', 'xlights-elementor-widgets' ),
				'title'       => __( 'Reviews', 'xlights-elementor-widgets' ),
				'slug'        => 'reviews',
				'parent'      => '',
				'path'        => '/reviews/',
				'seo_title'   => __( 'Customer Reviews | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Read what customers say about their X LIGHTS outdoor lighting, installed by {dealer}.', 'xlights-elementor-widgets' ),
			),
			'solutions'  => array(
				'label'       => __( 'Lighting Solutions', 'xlights-elementor-widgets' ),
				'title'       => __( 'Lighting Solutions', 'xlights-elementor-widgets' ),
				'slug'        => 'lighting-solutions',
				'parent'      => '',
				'path'        => '/lighting-solutions/',
				'seo_title'   => __( 'Outdoor Lighting Solutions in {area} | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Permanent soffit, hanging bulb bistro, deck and landscape lighting that work together through the X LIGHTS app. Designed and installed by {dealer}.', 'xlights-elementor-widgets' ),
			),
			'soffit'     => array(
				'label'       => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
				'title'       => __( 'Permanent Soffit Lighting', 'xlights-elementor-widgets' ),
				'slug'        => 'permanent-soffit-lighting',
				'parent'      => 'solutions',
				'path'        => '/lighting-solutions/permanent-soffit-lighting/',
				'service'     => __( 'Permanent soffit lighting', 'xlights-elementor-widgets' ),
				'seo_title'   => __( 'Permanent Soffit & Holiday Lighting in {area} | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Low-profile permanent soffit lighting that blends into your roofline by day and shines warm white or festive color at night, all from the X LIGHTS app.', 'xlights-elementor-widgets' ),
			),
			'bistro'     => array(
				'label'       => __( 'Hanging Bulb Bistro Lighting', 'xlights-elementor-widgets' ),
				'title'       => __( 'Hanging Bulb Bistro Lighting', 'xlights-elementor-widgets' ),
				'slug'        => 'bistro-lighting',
				'parent'      => 'solutions',
				'path'        => '/lighting-solutions/bistro-lighting/',
				'service'     => __( 'Bistro and patio lighting', 'xlights-elementor-widgets' ),
				'seo_title'   => __( 'Bistro & Patio Lighting in {area} | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Hanging bulb bistro lighting designed around your patio, outdoor dining area or restaurant. Professionally installed by {dealer}.', 'xlights-elementor-widgets' ),
			),
			'deck'       => array(
				'label'       => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
				'title'       => __( 'Deck Lighting', 'xlights-elementor-widgets' ),
				'slug'        => 'deck-lighting',
				'parent'      => 'solutions',
				'path'        => '/lighting-solutions/deck-lighting/',
				'service'     => __( 'Deck lighting', 'xlights-elementor-widgets' ),
				'seo_title'   => __( 'Deck & Step Lighting in {area} | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Subtle deck, step, railing and walkway lighting that makes outdoor spaces easier to enjoy after sunset. Designed and installed by {dealer}.', 'xlights-elementor-widgets' ),
			),
			'landscape'  => array(
				'label'       => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
				'title'       => __( 'Landscape Lighting', 'xlights-elementor-widgets' ),
				'slug'        => 'landscape-lighting',
				'parent'      => 'solutions',
				'path'        => '/lighting-solutions/landscape-lighting/',
				'service'     => __( 'Landscape lighting', 'xlights-elementor-widgets' ),
				'seo_title'   => __( 'Landscape & Pathway Lighting in {area} | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Tree, garden, pathway and architectural landscape lighting with app-based color and brightness control. Designed and installed by {dealer}.', 'xlights-elementor-widgets' ),
			),
			'commercial' => array(
				'label'       => __( 'Commercial Lighting', 'xlights-elementor-widgets' ),
				'title'       => __( 'Commercial Lighting', 'xlights-elementor-widgets' ),
				'slug'        => 'commercial-lighting',
				'parent'      => '',
				'path'        => '/commercial-lighting/',
				'service'     => __( 'Commercial outdoor lighting', 'xlights-elementor-widgets' ),
				'seo_title'   => __( 'Commercial Outdoor Lighting in {area} | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Restaurant patio, dealership, hospitality and commercial property lighting designed around your business. Request a commercial consultation with {dealer}.', 'xlights-elementor-widgets' ),
			),
			'projects'   => array(
				'label'       => __( 'Projects / Gallery', 'xlights-elementor-widgets' ),
				'title'       => __( 'Projects', 'xlights-elementor-widgets' ),
				'slug'        => 'projects',
				'parent'      => '',
				'path'        => '/projects/',
				'seo_title'   => __( 'Outdoor Lighting Projects & Gallery | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Browse permanent soffit, bistro, deck, landscape and commercial lighting projects completed by {dealer}.', 'xlights-elementor-widgets' ),
			),
			'contact'    => array(
				'label'       => __( 'Contact Us / Get a Quote', 'xlights-elementor-widgets' ),
				'title'       => __( 'Contact Us', 'xlights-elementor-widgets' ),
				'slug'        => 'contact-us',
				'parent'      => '',
				'path'        => '/contact-us/',
				'seo_title'   => __( 'Get a Quote | {dealer}', 'xlights-elementor-widgets' ),
				'description' => __( 'Request an outdoor lighting quote or schedule a consultation with {dealer}. Share your address and photos so we can prepare.', 'xlights-elementor-widgets' ),
			),
		);
	}

	/**
	 * @return array key => label
	 */
	public static function options() {
		$out = array();
		foreach ( self::all() as $key => $page ) {
			$out[ $key ] = $page['label'];
		}
		return $out;
	}

	/**
	 * @param string $key Template key.
	 * @return array|null
	 */
	public static function get( $key ) {
		$all = self::all();
		return isset( $all[ $key ] ) ? $all[ $key ] : null;
	}

	/**
	 * @param string $key Template key.
	 * @return bool
	 */
	public static function exists( $key ) {
		return null !== self::get( $key );
	}

	/**
	 * Section definitions for a template (from templates/pages/{key}.php).
	 *
	 * @param string $key Template key.
	 * @return array
	 */
	public static function sections( $key ) {
		if ( ! self::exists( $key ) ) {
			return array();
		}
		if ( ! isset( self::$definitions[ $key ] ) ) {
			$file = XLIGHTS_PATH . 'templates/pages/' . $key . '.php';
			$data = is_readable( $file ) ? include $file : array();
			/**
			 * Filter the section sequence and defaults of a page template.
			 *
			 * @param array  $data Section definitions.
			 * @param string $key  Template key.
			 */
			self::$definitions[ $key ] = apply_filters( 'xlights/page_template/sections', is_array( $data ) ? $data : array(), $key );
		}
		return self::$definitions[ $key ];
	}

	/**
	 * @return array template => page ID
	 */
	public static function map() {
		$map = get_option( self::OPTION, array() );
		return is_array( $map ) ? array_map( 'absint', $map ) : array();
	}

	/**
	 * @param string $key     Template key.
	 * @param int    $page_id Page ID.
	 */
	public static function set_page( $key, $page_id ) {
		$map         = self::map();
		$map[ $key ] = absint( $page_id );
		update_option( self::OPTION, $map, false );
		self::$resolved[ $key ] = absint( $page_id );
	}

	/**
	 * Page ID mapped to a template, falling back to the expected path.
	 *
	 * @param string $key Template key.
	 * @return int
	 */
	public static function page_id( $key ) {
		if ( ! empty( self::$resolved[ $key ] ) ) {
			return self::$resolved[ $key ];
		}
		$id  = 0;
		$map = self::map();
		if ( ! empty( $map[ $key ] ) ) {
			$post = get_post( $map[ $key ] );
			if ( $post && 'page' === $post->post_type && 'trash' !== $post->post_status ) {
				$id = (int) $post->ID;
			}
		}
		if ( ! $id && ( $page = self::get( $key ) ) ) {
			if ( 'home' === $key ) {
				$id = (int) get_option( 'page_on_front' );
			} else {
				$found = get_page_by_path( trim( $page['path'], '/' ) );
				$id    = $found ? (int) $found->ID : 0;
			}
		}
		if ( $id ) {
			self::$resolved[ $key ] = $id;
		}
		return $id;
	}

	/**
	 * Public URL for a template's page. Works before pages exist (path fallback).
	 *
	 * @param string $key Template key.
	 * @return string
	 */
	public static function url( $key ) {
		$page = self::get( $key );
		if ( ! $page ) {
			return home_url( '/' );
		}
		if ( 'home' === $key ) {
			return home_url( '/' );
		}
		$id = self::page_id( $key );
		if ( $id && 'publish' === get_post_status( $id ) ) {
			$link = get_permalink( $id );
			if ( $link ) {
				return $link;
			}
		}
		return home_url( $page['path'] );
	}

	/**
	 * Template key for a page ID (reverse map).
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	public static function key_for_post( $post_id ) {
		$post_id = (int) $post_id;
		if ( ! $post_id ) {
			return '';
		}
		$found = array_search( $post_id, self::map(), true );
		if ( false !== $found ) {
			return (string) $found;
		}
		if ( (int) get_option( 'page_on_front' ) === $post_id ) {
			return 'home';
		}
		return '';
	}
}
