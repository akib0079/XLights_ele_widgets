<?php
/**
 * Split image/content feature section.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\Helpers\Html;
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
			array(
				'layout' => self::select(
					__( 'Layout', 'xlights-elementor-widgets' ),
					self::layouts(),
					'standard',
					array(
						'separator'   => 'before',
						'description' => __( 'Editorial and Cinematic need a photo; app mockups and illustrations always show side by side. Cinematic works best with photos at least 1600 px wide.', 'xlights-elementor-widgets' ),
					)
				),
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
				'image_fit'      => self::select(
					__( 'Photo fit', 'xlights-elementor-widgets' ),
					array(
						'cover'   => __( 'Fill the frame (crop edges)', 'xlights-elementor-widgets' ),
						'contain' => __( 'Show the whole image (logos, products)', 'xlights-elementor-widgets' ),
					),
					'cover',
					array( 'condition' => array( 'visual' => 'image' ) )
				),
				'badge'          => self::text( __( 'Visual badge', 'xlights-elementor-widgets' ) ),
				'caption'        => self::text( __( 'Visual caption', 'xlights-elementor-widgets' ) ),
				'gate'           => self::gate_field(),
			)
		);
	}

	/**
	 * @return array Layout key => label.
	 */
	public static function layouts() {
		return array(
			'standard'  => __( 'Side by side', 'xlights-elementor-widgets' ),
			'offset'    => __( 'Editorial — large photo, overlapping text card', 'xlights-elementor-widgets' ),
			'cinematic' => __( 'Cinematic — full-width photo, text panel', 'xlights-elementor-widgets' ),
		);
	}

	/**
	 * Layout to render: offset and cinematic only with a photo.
	 *
	 * @param array $data Section data.
	 * @return string standard|offset|cinematic
	 */
	public static function layout( array $data ) {
		$layouts = self::layouts();
		$layout  = isset( $data['layout'] ) && is_string( $data['layout'] ) && isset( $layouts[ $data['layout'] ] ) ? $data['layout'] : 'standard';
		$photo   = ( ! isset( $data['visual'] ) || 'image' === $data['visual'] ) && isset( $data['image'] ) && Html::has_image( $data['image'] );
		return $photo ? $layout : 'standard';
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['visibility'] = Context::visibility( isset( $data['gate'] ) ? $data['gate'] : '' );
		if ( Context::HIDE === $data['visibility'] ) {
			return null;
		}
		return $data;
	}

	public function wrapper_classes( array $data ) {
		$out = array( 'xl-split--' . ( isset( $data['image_position'] ) && 'left' === $data['image_position'] ? 'left' : 'right' ), 'xl-layout--' . self::layout( $data ) );
		if ( isset( $data['visual'] ) && 'none' === $data['visual'] ) {
			$out[] = 'xl-split--text';
		}
		if ( Context::PENDING === $data['visibility'] ) {
			$out[] = 'xl-is-pending';
		}
		return $out;
	}
}
