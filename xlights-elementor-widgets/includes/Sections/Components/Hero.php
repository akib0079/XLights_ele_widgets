<?php
/**
 * Hero: full-width media, split, or centered editorial.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Hero extends Component {

	public function type() {
		return 'hero';
	}

	public function label() {
		return __( 'Hero', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-header';
	}

	public function backgrounds() {
		return array( 'dark', 'warm', 'light' );
	}

	protected function define_fields() {
		$fields = array(
			'layout'        => self::select(
				__( 'Layout', 'xlights-elementor-widgets' ),
				array(
					'media'    => __( 'Photo background', 'xlights-elementor-widgets' ),
					'split'    => __( 'Split: text + visual', 'xlights-elementor-widgets' ),
					'centered' => __( 'Centered editorial', 'xlights-elementor-widgets' ),
				),
				'media'
			),
		);
		$fields = array_merge( $fields, self::head_fields( false ) );
		$fields['heading']['rows'] = 3;
		$fields['eyebrow_in_h1']   = self::toggle(
			__( 'Include eyebrow in the H1', 'xlights-elementor-widgets' ),
			false,
			array( 'description' => __( 'Adds the eyebrow keyword to the page heading for SEO.', 'xlights-elementor-widgets' ) )
		);
		$fields = array_merge(
			$fields,
			self::button_fields( 'primary', __( 'Primary button', 'xlights-elementor-widgets' ) ),
			self::button_fields( 'secondary', __( 'Secondary button', 'xlights-elementor-widgets' ) ),
			array(
				'show_phone' => self::toggle( __( 'Show "or call" phone link', 'xlights-elementor-widgets' ), true ),
				'highlights' => self::textarea(
					__( 'Highlights (one per line)', 'xlights-elementor-widgets' ),
					'',
					3,
					array( 'separator' => 'before' )
				),
			),
			self::visual_fields( 'image' ),
			array(
				'video_url'   => self::url(
					__( 'Background video (MP4, optional)', 'xlights-elementor-widgets' ),
					'',
					array(
						'condition'   => array( 'layout' => 'media' ),
						'description' => __( 'Self-hosted MP4. The photo is used as poster and on reduced-motion devices.', 'xlights-elementor-widgets' ),
					)
				),
				'overlay'     => array(
					'type'      => 'slider',
					'label'     => __( 'Image overlay strength', 'xlights-elementor-widgets' ),
					'default'   => 60,
					'min'       => 0,
					'max'       => 95,
					'condition' => array( 'layout' => 'media' ),
				),
				'focal'       => self::select(
					__( 'Image focal point', 'xlights-elementor-widgets' ),
					array(
						'center' => __( 'Center', 'xlights-elementor-widgets' ),
						'top'    => __( 'Top', 'xlights-elementor-widgets' ),
						'bottom' => __( 'Bottom', 'xlights-elementor-widgets' ),
						'left'   => __( 'Left', 'xlights-elementor-widgets' ),
						'right'  => __( 'Right', 'xlights-elementor-widgets' ),
					),
					'center',
					array( 'condition' => array( 'layout' => 'media' ) )
				),
				'frame'       => self::select(
					__( 'Frame', 'xlights-elementor-widgets' ),
					array(
						'inset' => __( 'Inset with rounded corners', 'xlights-elementor-widgets' ),
						'bleed' => __( 'Full bleed (edge to edge)', 'xlights-elementor-widgets' ),
					),
					'inset',
					array(
						'description' => __( 'The transparent header (X LIGHTS → Settings → Header & Footer) only overlays full-bleed heroes.', 'xlights-elementor-widgets' ),
					)
				),
				'height'      => self::select(
					__( 'Height', 'xlights-elementor-widgets' ),
					array(
						'full'   => __( 'Full screen', 'xlights-elementor-widgets' ),
						'tall'   => __( 'Tall', 'xlights-elementor-widgets' ),
						'medium' => __( 'Medium', 'xlights-elementor-widgets' ),
					),
					'tall',
					array( 'condition' => array( 'layout' => 'media' ) )
				),
				'align'       => array(
					'type'    => 'choose',
					'label'   => __( 'Text alignment', 'xlights-elementor-widgets' ),
					'options' => array(
						'left'   => array(
							'title' => __( 'Left', 'xlights-elementor-widgets' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'xlights-elementor-widgets' ),
							'icon'  => 'eicon-text-align-center',
						),
					),
					'default' => 'left',
				),
				'breadcrumbs' => self::toggle( __( 'Show breadcrumbs', 'xlights-elementor-widgets' ), true ),
				'string_line' => self::toggle( __( 'Decorative light-string accent', 'xlights-elementor-widgets' ), true ),
			)
		);
		$fields['primary_label']['default'] = '{cta}';
		$fields['primary_link']['default']  = '{quote_url}';
		return $fields;
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['tag'] = $ctx->primary_heading ? 'h1' : 'h2';
		if ( 'media' === $data['layout'] ) {
			$data['bg'] = 'dark';
		}
		return $data;
	}

	public function wrapper_classes( array $data ) {
		$layout = isset( $data['layout'] ) ? $data['layout'] : 'media';
		$out    = array( 'xl-hero--' . $layout );
		$out[]  = 'bleed' === ( isset( $data['frame'] ) ? $data['frame'] : 'inset' ) ? 'xl-hero--bleed' : 'xl-hero--inset';
		if ( 'media' === $layout ) {
			$out[] = 'xl-hero--h-' . ( isset( $data['height'] ) ? $data['height'] : 'tall' );
		}
		return $out;
	}
}
