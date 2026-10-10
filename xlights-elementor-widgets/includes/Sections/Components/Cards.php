<?php
/**
 * Card grid: service grid, feature grid, benefits, icon cards, steps,
 * residential/commercial entry points, product options.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\Helpers\Illustrations;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Cards extends Component {

	public function type() {
		return 'cards';
	}

	public function label() {
		return __( 'Card Grid', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-gallery-grid';
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'style'        => self::select(
					__( 'Card style', 'xlights-elementor-widgets' ),
					array(
						'icon'    => __( 'Icon cards', 'xlights-elementor-widgets' ),
						'image'   => __( 'Image cards', 'xlights-elementor-widgets' ),
						'overlay' => __( 'Large photo cards (text over image)', 'xlights-elementor-widgets' ),
						'steps'   => __( 'Numbered steps', 'xlights-elementor-widgets' ),
						'stack'   => __( 'Stacked numbered cards (large, image right)', 'xlights-elementor-widgets' ),
						'minimal' => __( 'Minimal list', 'xlights-elementor-widgets' ),
						'bento'   => __( 'Bento (first card large)', 'xlights-elementor-widgets' ),
						'strip'   => __( 'Icon strip', 'xlights-elementor-widgets' ),
					),
					'icon',
					array( 'separator' => 'before' )
				),
				'columns'      => self::select(
					__( 'Columns (desktop)', 'xlights-elementor-widgets' ),
					array(
						'2' => '2',
						'3' => '3',
						'4' => '4',
					),
					'3'
				),
				'align'        => self::select(
					__( 'Heading alignment', 'xlights-elementor-widgets' ),
					array(
						'center' => __( 'Center', 'xlights-elementor-widgets' ),
						'left'   => __( 'Left', 'xlights-elementor-widgets' ),
					),
					'center'
				),
				'items'        => array(
					'type'        => 'repeater',
					'label'       => __( 'Cards', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ title }}}',
					'fields'      => array(
						'title'       => self::text( __( 'Title', 'xlights-elementor-widgets' ) ),
						'text'        => self::textarea( __( 'Text', 'xlights-elementor-widgets' ) ),
						'icon'        => self::icon_field( 'sparkles' ),
						'icon_custom' => array(
							'type'  => 'icons',
							'label' => __( 'Custom icon (overrides)', 'xlights-elementor-widgets' ),
						),
						'image'       => self::media( __( 'Image', 'xlights-elementor-widgets' ) ),
						'scene'       => self::select( __( 'Illustration fallback', 'xlights-elementor-widgets' ), Illustrations::scenes(), 'roofline' ),
						'palette'     => self::select( __( 'Illustration light color', 'xlights-elementor-widgets' ), Illustrations::palettes(), 'warm' ),
						'link_label'  => self::text( __( 'Link label', 'xlights-elementor-widgets' ) ),
						'link'        => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
						'gate'        => self::gate_field(),
					),
					'default'     => array(),
				),
				'note'         => self::text( __( 'Small print below the grid', 'xlights-elementor-widgets' ) ),
				'footer_label' => self::text( __( 'Button below grid', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'footer_link'  => self::url( __( 'Button link', 'xlights-elementor-widgets' ) ),
			)
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
					'icon'       => '',
					'image'      => '',
					'scene'      => 'roofline',
					'palette'    => 'warm',
					'link_label' => '',
					'link'       => '',
					'gate'       => '',
				)
			);
			$item['visibility'] = Context::visibility( $item['gate'] );
			if ( Context::HIDE === $item['visibility'] ) {
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
		return array( 'xl-cards--' . sanitize_html_class( $data['style'] ) );
	}
}
