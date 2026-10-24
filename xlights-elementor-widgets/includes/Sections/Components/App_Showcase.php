<?php
/**
 * Interactive app showcase: accessible tabs that swap the phone screen.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Helpers\Context;
use XLights\Helpers\Illustrations;
use XLights\Sections\Component;
use XLights\Sections\Render_Context;

defined( 'ABSPATH' ) || exit;

class App_Showcase extends Component {

	public function type() {
		return 'app_showcase';
	}

	public function label() {
		return __( 'App Showcase (tabs)', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-tabs';
	}

	public function backgrounds() {
		return array( 'dark', 'warm', 'light' );
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'layout'         => self::select(
					__( 'Layout', 'xlights-elementor-widgets' ),
					array(
						'tabs'    => __( 'Tabs beside one phone', 'xlights-elementor-widgets' ),
						'gallery' => __( 'Scrolling gallery of app screens', 'xlights-elementor-widgets' ),
					),
					'tabs',
					array( 'separator' => 'before' )
				),
				'phone_position' => self::select(
					__( 'Phone position', 'xlights-elementor-widgets' ),
					array(
						'left'  => __( 'Left', 'xlights-elementor-widgets' ),
						'right' => __( 'Right', 'xlights-elementor-widgets' ),
					),
					'right',
					array( 'description' => __( 'Tabs layout only.', 'xlights-elementor-widgets' ) )
				),
				'items'          => array(
					'type'        => 'repeater',
					'label'       => __( 'Features (tabs)', 'xlights-elementor-widgets' ),
					'title_field' => '{{{ label }}}',
					'fields'      => array(
						'label'      => self::text( __( 'Tab label', 'xlights-elementor-widgets' ) ),
						'icon'       => self::icon_field( 'smartphone' ),
						'title'      => self::text( __( 'Feature headline', 'xlights-elementor-widgets' ) ),
						'text'       => self::textarea( __( 'Description', 'xlights-elementor-widgets' ) ),
						'screen'     => self::select( __( 'App screen', 'xlights-elementor-widgets' ), Illustrations::screens(), 'zones' ),
						'screenshot' => self::media( __( 'Your own screenshot (optional)', 'xlights-elementor-widgets' ), '', array( 'description' => __( 'A plain phone screenshot; it is framed automatically. Leave empty to show the official X LIGHTS app screen chosen above.', 'xlights-elementor-widgets' ) ) ),
					),
					'default'     => array(),
				),
				'show_store'     => self::toggle( __( 'Show App Store / Google Play buttons', 'xlights-elementor-widgets' ), true ),
				'note'           => self::text( __( 'Small print', 'xlights-elementor-widgets' ) ),
				'link_label'     => self::text( __( 'Link label', 'xlights-elementor-widgets' ), '', array( 'separator' => 'before' ) ),
				'link'           => self::url( __( 'Link', 'xlights-elementor-widgets' ) ),
			)
		);
	}

	public function prepare( array $data, Render_Context $ctx ) {
		$data['items'] = array_values( array_filter( (array) $data['items'] ) );
		if ( ! $data['items'] ) {
			return $ctx->editor ? $data : null;
		}
		$data['store_visibility'] = 'yes' === $data['show_store'] ? Context::visibility( 'app_links' ) : Context::HIDE;
		return $data;
	}

	public function wrapper_classes( array $data ) {
		return array( 'xl-app-layout--' . ( 'gallery' === $data['layout'] ? 'gallery' : 'tabs' ) );
	}
}
