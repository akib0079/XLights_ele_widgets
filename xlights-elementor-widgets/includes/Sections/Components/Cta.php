<?php
/**
 * Call-to-action banner (quote, call, app download, review request).
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Cta extends Component {

	public function type() {
		return 'cta';
	}

	public function label() {
		return __( 'CTA Banner', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-call-to-action';
	}

	public function backgrounds() {
		return array( 'dark', 'warm', 'light' );
	}

	protected function define_fields() {
		$fields = array_merge(
			array(
				'style' => self::select(
					__( 'Style', 'xlights-elementor-widgets' ),
					array(
						'panel' => __( 'Panel', 'xlights-elementor-widgets' ),
						'image' => __( 'Background photo', 'xlights-elementor-widgets' ),
						'band'  => __( 'Full-width band', 'xlights-elementor-widgets' ),
					),
					'panel'
				),
				'image'   => self::media( __( 'Background photo', 'xlights-elementor-widgets' ), '', array( 'condition' => array( 'style' => 'image' ) ) ),
				'overlay' => array(
					'type'      => 'slider',
					'label'     => __( 'Overlay strength', 'xlights-elementor-widgets' ),
					'default'   => 65,
					'min'       => 0,
					'max'       => 95,
					'condition' => array( 'style' => 'image' ),
				),
			),
			self::head_fields( false ),
			self::button_fields( 'primary', __( 'Primary button', 'xlights-elementor-widgets' ) ),
			self::button_fields( 'secondary', __( 'Secondary button', 'xlights-elementor-widgets' ) ),
			array(
				'show_phone' => self::toggle( __( 'Show click-to-call button', 'xlights-elementor-widgets' ), true ),
				'show_store' => self::toggle( __( 'Show App Store / Google Play buttons', 'xlights-elementor-widgets' ), false ),
				'gate'       => self::select(
					__( 'Publication', 'xlights-elementor-widgets' ),
					array(
						''               => __( 'Always show', 'xlights-elementor-widgets' ),
						'app_links'      => __( 'Only when app store links exist', 'xlights-elementor-widgets' ),
						'google_reviews' => __( 'Only when a Google Reviews URL exists', 'xlights-elementor-widgets' ),
						'financing'      => __( 'Only when financing is approved', 'xlights-elementor-widgets' ),
						'pending'        => __( 'Pending client approval (hidden)', 'xlights-elementor-widgets' ),
					),
					''
				),
			)
		);
		$fields['primary_label']['default'] = '{cta}';
		$fields['primary_link']['default']  = '{quote_url}';
		return $fields;
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['visibility'] = Context::visibility( $data['gate'] );
		if ( Context::HIDE === $data['visibility'] ) {
			return null;
		}
		$data['store_visibility'] = 'yes' === $data['show_store'] ? Context::visibility( 'app_links' ) : Context::HIDE;
		return $data;
	}

	public function wrapper_classes( array $data ) {
		return array_filter( array( 'xl-cta--' . sanitize_html_class( $data['style'] ), Context::PENDING === $data['visibility'] ? 'xl-is-pending' : '' ) );
	}
}
