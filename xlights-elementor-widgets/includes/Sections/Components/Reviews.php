<?php
/**
 * Customer reviews from the xlights_review post type. Never fabricates content:
 * the section is hidden on the live site when no approved reviews exist.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\PostTypes\Reviews as Review_Type;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Reviews extends Component {

	public function type() {
		return 'reviews';
	}

	public function label() {
		return __( 'Customer Reviews', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-testimonial-carousel';
	}

	public function backgrounds() {
		return array( 'warm', 'light', 'dark' );
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'layout'         => self::select(
					__( 'Layout', 'xlights-elementor-widgets' ),
					array(
						'carousel' => __( 'Carousel', 'xlights-elementor-widgets' ),
						'grid'     => __( 'Grid', 'xlights-elementor-widgets' ),
						'featured' => __( 'Featured quote + grid', 'xlights-elementor-widgets' ),
					),
					'carousel',
					array( 'separator' => 'before' )
				),
				'scope'          => self::select(
					__( 'Which reviews', 'xlights-elementor-widgets' ),
					array(
						'local_first' => __( 'Local first, then brand-level', 'xlights-elementor-widgets' ),
						'local'       => __( 'Local dealer only', 'xlights-elementor-widgets' ),
						'brand'       => __( 'Brand-level only', 'xlights-elementor-widgets' ),
					),
					'local_first'
				),
				'featured_only'  => self::toggle( __( 'Featured reviews only', 'xlights-elementor-widgets' ), false ),
				'count'          => array(
					'type'    => 'number',
					'label'   => __( 'Number of reviews', 'xlights-elementor-widgets' ),
					'default' => 6,
					'min'     => 1,
					'max'     => 48,
				),
				'show_rating'    => self::toggle( __( 'Show star rating (when provided)', 'xlights-elementor-widgets' ), true ),
				'show_source'    => self::toggle( __( 'Show source & date', 'xlights-elementor-widgets' ), true ),
				'show_photo'     => self::toggle( __( 'Show approved photos', 'xlights-elementor-widgets' ), true ),
				'google_link'    => self::toggle( __( 'Link to Google Reviews', 'xlights-elementor-widgets' ), true ),
				'link_label'     => self::text( __( 'Link label', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'link'           => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
				'empty_behavior' => self::select(
					__( 'When no reviews exist', 'xlights-elementor-widgets' ),
					array(
						'hide'    => __( 'Hide the section', 'xlights-elementor-widgets' ),
						'message' => __( 'Show a message', 'xlights-elementor-widgets' ),
					),
					'hide'
				),
				'empty_text'     => self::text( __( 'Empty message', 'xlights-elementor-widgets' ), __( 'Reviews from local customers will appear here as projects are completed.', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['reviews'] = Review_Type::get_reviews(
			array(
				'scope'         => $data['scope'],
				'featured_only' => 'yes' === $data['featured_only'],
				'count'         => max( 1, min( 48, (int) $data['count'] ) ),
			)
		);
		if ( ! $data['reviews'] && 'hide' === $data['empty_behavior'] && ! $ctx->editor ) {
			return null;
		}
		return $data;
	}

	public function wrapper_classes( array $data ) {
		return array( 'xl-reviews--' . sanitize_html_class( $data['layout'] ) );
	}
}
