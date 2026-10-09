<?php
/**
 * Local trust band: licensing/insurance statement from settings plus trust
 * points. Credential statements stay hidden until verified.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\Settings\Settings;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class Trust extends Component {

	public function type() {
		return 'trust';
	}

	public function label() {
		return __( 'Local Trust Band', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-lock-user';
	}

	public function backgrounds() {
		return array( 'dark', 'warm', 'light' );
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'show_license' => self::toggle(
					__( 'Show licensing/insurance statement from settings', 'xlights-elementor-widgets' ),
					true,
					array( 'description' => __( 'Shown publicly only after credentials are verified in settings.', 'xlights-elementor-widgets' ) )
				),
				'items'        => array(
					'type'        => 'repeater',
					'label'       => __( 'Trust points', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ title }}}',
					'fields'      => array(
						'title' => self::text( __( 'Title', 'xlights-elementor-widgets' ) ),
						'text'  => self::textarea( __( 'Text', 'xlights-elementor-widgets' ), '', 2 ),
						'icon'  => self::icon_field( 'shield-check' ),
						'gate'  => self::gate_field(),
					),
					'default'     => array(),
				),
				'link_label'   => self::text( __( 'Link label', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'link'         => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['license_visibility'] = 'yes' === $data['show_license'] ? Context::visibility( 'credentials' ) : Context::HIDE;
		$data['statement']          = Settings::get( 'trust_statement' );
		$data['license']            = Settings::get( 'license_number' );

		$items = array();
		foreach ( (array) $data['items'] as $item ) {
			$item = wp_parse_args(
				(array) $item,
				array(
					'title' => '',
					'text'  => '',
					'icon'  => 'shield-check',
					'gate'  => '',
				)
			);
			$item['visibility'] = Context::visibility( $item['gate'] );
			if ( Context::HIDE !== $item['visibility'] && '' !== trim( $item['title'] ) ) {
				$items[] = $item;
			}
		}
		$data['items'] = $items;
		if ( ! $items && Context::HIDE === $data['license_visibility'] ) {
			return null;
		}
		return $data;
	}
}
