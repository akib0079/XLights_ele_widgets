<?php
/**
 * Intro / editorial text block.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Intro extends Component {

	public function type() {
		return 'intro';
	}

	public function label() {
		return __( 'Intro / Editorial Text', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-text';
	}

	protected function define_fields() {
		return array_merge(
			array(
				'layout' => self::select(
					__( 'Layout', 'xlights-elementor-widgets' ),
					array(
						'centered' => __( 'Centered', 'xlights-elementor-widgets' ),
						'split'    => __( 'Heading left, text right', 'xlights-elementor-widgets' ),
						'note'     => __( 'Compact note', 'xlights-elementor-widgets' ),
					),
					'centered'
				),
			),
			self::head_fields( true ),
			array(
				'points'     => self::textarea( __( 'Key points (one per line)', 'xlights-elementor-widgets' ), '', 4 ),
				'link_label' => self::text( __( 'Link label', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'link'       => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function wrapper_classes( array $data ) {
		return array( 'xl-intro--' . ( isset( $data['layout'] ) ? $data['layout'] : 'centered' ) );
	}
}
