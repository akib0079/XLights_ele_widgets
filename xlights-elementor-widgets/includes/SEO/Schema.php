<?php
/**
 * Structured data (JSON-LD).
 *
 * - LocalBusiness on the home and contact pages (only verified fields).
 * - Service on service pages, referencing the LocalBusiness.
 * - FAQPage from visible FAQ sections (Google limits FAQ rich results, but the
 *   markup remains valid and accurate).
 * - BreadcrumbList, skipped when a major SEO plugin already outputs breadcrumbs.
 *
 * Review/AggregateRating markup is intentionally NOT emitted: self-serving
 * reviews of a LocalBusiness on its own site are not eligible for review rich
 * results, and incorrect review markup risks a manual action.
 *
 * @package XLights
 */

namespace XLights\SEO;

use XLights\Frontend\Social_Links;
use XLights\Pages\Page_Registry;
use XLights\Settings\Settings;
use XLights\Helpers\Tokens;

defined( 'ABSPATH' ) || exit;

final class Schema {

	/** @var array[] FAQ items collected during render. */
	private static $faq = array();

	/** @var array|null Breadcrumb items. */
	private static $breadcrumbs = null;

	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'print_collected' ), 99 );
		add_action( 'wp_head', array( __CLASS__, 'print_business' ), 30 );
	}

	/**
	 * @param array $items FAQ items with question/answer.
	 */
	public static function add_faq( array $items ) {
		foreach ( $items as $item ) {
			self::$faq[ md5( $item['question'] ) ] = array(
				'q' => wp_strip_all_tags( Tokens::replace( $item['question'] ) ),
				'a' => wp_kses( Tokens::replace( $item['answer'] ), array( 'a' => array( 'href' => true ), 'br' => array(), 'p' => array(), 'strong' => array(), 'em' => array(), 'ul' => array(), 'li' => array() ) ),
			);
		}
	}

	/**
	 * @param array $items [ [label, url], ... ].
	 */
	public static function add_breadcrumbs( array $items ) {
		if ( null === self::$breadcrumbs ) {
			self::$breadcrumbs = $items;
		}
	}

	/**
	 * @return bool
	 */
	public static function seo_plugin_active() {
		return defined( 'WPSEO_VERSION' ) || class_exists( '\RankMath' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
	}

	/**
	 * @return bool
	 */
	private static function enabled() {
		return Settings::enabled( 'schema_enabled' ) && ! is_admin() && ! \XLights\Helpers\Context::is_editor();
	}

	/**
	 * @return string
	 */
	public static function business_id() {
		return trailingslashit( home_url( '/' ) ) . '#localbusiness';
	}

	/**
	 * LocalBusiness graph node built from verified settings only.
	 *
	 * @return array
	 */
	public static function business() {
		$s    = Settings::all();
		$node = array(
			'@type' => $s['business_type'] ? $s['business_type'] : 'LocalBusiness',
			'@id'   => self::business_id(),
			'name'  => $s['dealer_name'],
			'url'   => home_url( '/' ),
		);
		if ( '' !== trim( $s['legal_name'] ) ) {
			$node['legalName'] = $s['legal_name'];
		}
		if ( '' !== trim( $s['phone'] ) ) {
			$node['telephone'] = $s['phone'];
		}
		if ( ! empty( $s['show_email'] ) && is_email( $s['email'] ) ) {
			$node['email'] = $s['email'];
		}
		$desc = trim( Tokens::replace( $s['dealer_intro'] ) );
		if ( '' !== $desc ) {
			$node['description'] = $desc;
		}
		$logo = (int) $s['logo'];
		if ( $logo && ( $src = wp_get_attachment_image_url( $logo, 'full' ) ) ) {
			$node['logo']  = $src;
			$node['image'] = $src;
		}
		if ( ! empty( $s['show_address'] ) && '' !== trim( $s['address_street'] ) ) {
			$node['address'] = array_filter(
				array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => $s['address_street'],
					'addressLocality' => $s['address_city'],
					'addressRegion'   => $s['address_state'],
					'postalCode'      => $s['address_zip'],
					'addressCountry'  => 'US',
				)
			);
		}
		$areas = Settings::lines( 'service_areas' );
		if ( $areas ) {
			$node['areaServed'] = array_map(
				static function ( $a ) {
					return array(
						'@type' => 'Place',
						'name'  => $a,
					);
				},
				$areas
			);
		}
		if ( is_numeric( $s['geo_lat'] ) && is_numeric( $s['geo_lng'] ) ) {
			$node['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $s['geo_lat'],
				'longitude' => (float) $s['geo_lng'],
			);
		}
		if ( '' !== trim( $s['price_range'] ) ) {
			$node['priceRange'] = $s['price_range'];
		}
		$same = Social_Links::urls();
		if ( $same ) {
			$node['sameAs'] = $same;
		}
		if ( '' !== $s['corporate_url'] ) {
			$node['brand'] = array(
				'@type' => 'Brand',
				'name'  => $s['brand_name'],
				'url'   => $s['corporate_url'],
			);
		}
		/**
		 * Filter the LocalBusiness node.
		 *
		 * @param array $node Node.
		 */
		return apply_filters( 'xlights/schema/business', $node );
	}

	/**
	 * Head output: LocalBusiness (home/contact) and Service (service pages).
	 */
	public static function print_business() {
		if ( ! self::enabled() || ! is_singular( 'page' ) && ! is_front_page() ) {
			return;
		}
		$key   = is_front_page() ? 'home' : Page_Registry::key_for_post( get_queried_object_id() );
		$graph = array();
		if ( in_array( $key, array( 'home', 'contact', 'about' ), true ) ) {
			$graph[] = self::business();
		}
		$def = $key ? Page_Registry::get( $key ) : null;
		if ( $def && ! empty( $def['service'] ) ) {
			$areas   = Settings::lines( 'service_areas' );
			$graph[] = array_filter(
				array(
					'@type'       => 'Service',
					'name'        => Tokens::replace( $def['title'] ),
					'serviceType' => $def['service'],
					'description' => Tokens::replace( $def['description'] ),
					'url'         => get_permalink( get_queried_object_id() ),
					'provider'    => array( '@id' => self::business_id() ),
					'areaServed'  => $areas ? implode( ', ', $areas ) : null,
				)
			);
			if ( ! in_array( $key, array( 'home', 'contact', 'about' ), true ) ) {
				$graph[] = self::business();
			}
		}
		if ( $graph ) {
			self::print_json(
				array(
					'@context' => 'https://schema.org',
					'@graph'   => $graph,
				)
			);
		}
	}

	/**
	 * Footer output: FAQPage and BreadcrumbList collected during render.
	 */
	public static function print_collected() {
		if ( ! self::enabled() ) {
			return;
		}
		if ( self::$faq ) {
			$entities = array();
			foreach ( self::$faq as $item ) {
				$entities[] = array(
					'@type'          => 'Question',
					'name'           => $item['q'],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $item['a'],
					),
				);
			}
			self::print_json(
				array(
					'@context'   => 'https://schema.org',
					'@type'      => 'FAQPage',
					'mainEntity' => $entities,
				)
			);
		}
		if ( self::$breadcrumbs && ! self::seo_plugin_active() ) {
			$list = array();
			foreach ( self::$breadcrumbs as $i => $item ) {
				$list[] = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'name'     => $item[0],
					'item'     => $item[1],
				);
			}
			self::print_json(
				array(
					'@context'        => 'https://schema.org',
					'@type'           => 'BreadcrumbList',
					'itemListElement' => $list,
				)
			);
		}
	}

	/**
	 * @param array $data Data.
	 */
	private static function print_json( array $data ) {
		echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
