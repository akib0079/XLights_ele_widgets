<?php
/**
 * Split image/content feature section.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Split extends Component {

	public function type() {
		return 'split';
	}

	public function label() {
		return __( 'Split Image & Content', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-image-box';
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'points' => self::textarea( __( 'Checklist (one per line)', 'xlights-elementor-widgets' ), '', 4 ),
				'note'   => self::text( __( 'Small print / note', 'xlights-elementor-widgets' ) ),
			),
			self::button_fields( 'primary', __( 'Button', 'xlights-elementor-widgets' ) ),
			array(
				'secondary_label' => self::text( __( 'Text link', 'xlights-elementor-widgets' ) ),
				'secondary_link'  => self::url( __( 'Text link URL', 'xlights-elementor-widgets' ) ),
			),
			self::visual_fields( 'image', true ),
			array(
				'image_position' => array(
					'type'    => 'choose',
					'label'   => __( 'Visual position', 'xlights-elementor-widgets' ),
					'options' => array(
						'left'  => array(
							'title' => __( 'Left', 'xlights-elementor-widgets' ),
							'icon'  => 'eicon-h-align-left',
						),
						'right' => array(
							'title' => __( 'Right', 'xlights-elementor-widgets' ),
							'icon'  => 'eicon-h-align-right',
						),
					),
					'default' => 'right',
				),
				'badge'          => self::text( __( 'Visual badge', 'xlights-elementor-widgets' ) ),
				'caption'        => self::text( __( 'Visual caption', 'xlights-elementor-widgets' ) ),
				'gate'           => self::gate_field(),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['visibility'] = Context::visibility( isset( $data['gate'] ) ? $data['gate'] : '' );
		if ( Context::HIDE === $data['visibility'] ) {
			return null;
		}
		return $data;
	}

	public function wrapper_classes( array $data ) {
		$out = array( 'xl-split--' . ( isset( $data['image_position'] ) && 'left' === $data['image_position'] ? 'left' : 'right' ) );
		if ( isset( $data['visual'] ) && 'none' === $data['visual'] ) {
			$out[] = 'xl-split--text';
		}
		if ( Context::PENDING === $data['visibility'] ) {
			$out[] = 'xl-is-pending';
		}
		return $out;
	}
}
