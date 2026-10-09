<?php
/**
 * "One connected lighting system" hub diagram.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Sections\Component;

defined( 'ABSPATH' ) || exit;

class Connected extends Component {

	public function type() {
		return 'connected';
	}

	public function label() {
		return __( 'Connected System Diagram', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-flow';
	}

	public function backgrounds() {
		return array( 'dark', 'warm', 'light' );
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'hub_label'  => self::text( __( 'Hub label', 'xlights-elementor-widgets' ), __( 'X LIGHTS App', 'xlights-elementor-widgets' ), array( 'separator' => 'before' ) ),
				'hub_text'   => self::text( __( 'Hub caption', 'xlights-elementor-widgets' ), __( 'One app for every zone', 'xlights-elementor-widgets' ) ),
				'items'      => array(
					'type'        => 'repeater',
					'label'       => __( 'Connected products', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ title }}}',
					'fields'      => array(
						'title' => self::text( __( 'Title', 'xlights-elementor-widgets' ) ),
						'text'  => self::textarea( __( 'Text', 'xlights-elementor-widgets' ), '', 2 ),
						'icon'  => self::icon_field( 'lightbulb' ),
						'link'  => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
					),
					'default'     => array(),
				),
				'note'       => self::text( __( 'Compatibility note', 'xlights-elementor-widgets' ) ),
				'link_label' => self::text( __( 'Link label', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'link'       => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
			)
		);
	}
}
