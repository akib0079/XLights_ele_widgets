<?php
/**
 * Financing callout. Text defaults come from dealer settings and the section is
 * hidden on the live site until financing is approved.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\Settings\Settings;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Financing extends Component {

	public function type() {
		return 'financing';
	}

	public function label() {
		return __( 'Financing Callout', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-price-table';
	}

	public function backgrounds() {
		return array( 'light', 'warm', 'dark' );
	}

	protected function define_fields() {
		return array(
			'notice'       => array(
				'type' => 'notice',
				'text' => __( 'Leave fields blank to use the approved financing copy from X LIGHTS → Settings → Credentials & Financing. Never add rates or payment amounts unless supplied by the lender.', 'xlights-elementor-widgets' ),
			),
			'layout'       => self::select(
				__( 'Layout', 'xlights-elementor-widgets' ),
				array(
					'banner' => __( 'Banner', 'xlights-elementor-widgets' ),
					'card'   => __( 'Card', 'xlights-elementor-widgets' ),
				),
				'banner'
			),
			'eyebrow'      => self::text( __( 'Eyebrow', 'xlights-elementor-widgets' ), __( 'Financing', 'xlights-elementor-widgets' ) ),
			'heading'      => self::text( __( 'Heading (blank = settings)', 'xlights-elementor-widgets' ) ),
			'text'         => self::textarea( __( 'Text (blank = settings)', 'xlights-elementor-widgets' ) ),
			'button_label' => self::text( __( 'Button label (blank = settings)', 'xlights-elementor-widgets' ) ),
			'button_link'  => self::url( __( 'Button link (blank = settings)', 'xlights-elementor-widgets' ) ),
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['visibility'] = Context::visibility( 'financing' );
		if ( Context::HIDE === $data['visibility'] ) {
			return null;
		}
		$s = Settings::all();
		foreach ( array(
			'heading'      => 'financing_heading',
			'text'         => 'financing_text',
			'button_label' => 'financing_cta_label',
		) as $field => $setting ) {
			if ( '' === trim( (string) $data[ $field ] ) ) {
				$data[ $field ] = $s[ $setting ];
			}
		}
		$link = is_array( $data['button_link'] ) ? $data['button_link'] : array( 'url' => (string) $data['button_link'] );
		if ( empty( $link['url'] ) ) {
			$link = array( 'url' => $s['financing_url'] );
		}
		$data['button_link'] = $link;
		$data['partner']     = $s['financing_partner'];
		$data['disclaimer']  = $s['financing_disclaimer'];
		return $data;
	}

	public function wrapper_classes( array $data ) {
		return array_filter( array( 'xl-financing--' . sanitize_html_class( $data['layout'] ), Context::PENDING === $data['visibility'] ? 'xl-is-pending' : '' ) );
	}
}
