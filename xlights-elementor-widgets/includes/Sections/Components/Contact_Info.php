<?php
/**
 * Contact details from dealer settings.
 *
 * @package XLights
 */

namespace XLights\Sections\Components;

use XLights\Sections\Component;

defined( 'ABSPATH' ) || exit;

class Contact_Info extends Component {

	public function type() {
		return 'contact_info';
	}

	public function label() {
		return __( 'Contact Information', 'xlights-elementor-widgets' );
	}

	public function icon() {
		return 'eicon-envelope';
	}

	protected function define_fields() {
		return array_merge(
			self::head_fields( true ),
			array(
				'notice'       => array(
					'type' => 'notice',
					'text' => __( 'Phone, email, address and hours come from X LIGHTS → Settings so they stay consistent sitewide.', 'xlights-elementor-widgets' ),
				),
				'show_phone'   => self::toggle( __( 'Phone', 'xlights-elementor-widgets' ), true ),
				'show_email'   => self::toggle( __( 'Email', 'xlights-elementor-widgets' ), true ),
				'show_address' => self::toggle( __( 'Address (when published in settings)', 'xlights-elementor-widgets' ), true ),
				'show_hours'   => self::toggle( __( 'Hours (when provided)', 'xlights-elementor-widgets' ), true ),
				'show_areas'   => self::toggle( __( 'Service areas', 'xlights-elementor-widgets' ), true ),
				'show_social'  => self::toggle( __( 'Social links (when provided)', 'xlights-elementor-widgets' ), true ),
			)
		);
	}
}
