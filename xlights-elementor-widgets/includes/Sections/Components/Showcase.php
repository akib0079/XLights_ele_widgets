<?php
/**
 * Feature explorer: numbered tabs beside a large photo that changes with the
 * selected feature. Replaces a run of similar image/text sections with one
 * interactive block. Without JavaScript every feature is shown in a list.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Illustrations;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Showcase extends Component {

	public function type() {
		return 'showcase';
	}

	public function label() {
		return __( 'Feature Explorer', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-tabs';
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'items'          => array(
					'type'        => 'repeater',
					'label'       => __( 'Features', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ title }}}',
					'fields'      => array(
						'title'      => self::text( __( 'Title', 'xlights-elementor-widgets' ) ),
						'text'       => self::textarea( __( 'Text', 'xlights-elementor-widgets' ) ),
						'points'     => self::textarea( __( 'Checklist (one per line)', 'xlights-elementor-widgets' ) ),
						'image'      => self::media( __( 'Photo', 'xlights-elementor-widgets' ) ),
						'image_alt'  => self::text(
							__( 'Photo description', 'xlights-elementor-widgets' ),
							'',
							array( 'description' => __( 'Used for the bundled photos. Media Library photos use their own alt text.', 'xlights-elementor-widgets' ) )
						),
						'scene'      => self::select( __( 'Illustration fallback', 'xlights-elementor-widgets' ), Illustrations::scenes(), 'landscape' ),
						'link_label' => self::text( __( 'Link label', 'xlights-elementor-widgets' ) ),
						'link'       => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
					),
					'default'     => array(),
				),
				'media_position' => self::select(
					__( 'Photo position', 'xlights-elementor-widgets' ),
					array(
						'right' => __( 'Right', 'xlights-elementor-widgets' ),
						'left'  => __( 'Left', 'xlights-elementor-widgets' ),
					),
					'right',
					array( 'separator' => 'before' )
				),
			),
			self::button_fields( 'primary', __( 'Button below the list', 'xlights-elementor-widgets' ) )
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$items = array();
		foreach ( (array) $data['items'] as $item ) {
			$item = wp_parse_args(
				(array) $item,
				array(
					'title'      => '',
					'text'       => '',
					'points'     => '',
					'image'      => '',
					'image_alt'  => '',
					'scene'      => 'landscape',
					'link_label' => '',
					'link'       => '',
				)
			);
			if ( '' === trim( (string) $item['title'] ) ) {
				continue;
			}
			$items[] = $item;
		}
		if ( ! $items && ! $ctx->editor ) {
			return null;
		}
		$data['items'] = $items;
		return $data;
	}

	public function wrapper_classes( array $data ) {
		return array( 'xl-showcase-section' );
	}
}
