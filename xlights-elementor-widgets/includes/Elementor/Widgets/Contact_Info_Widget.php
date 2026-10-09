<?php
/**
 * Standalone section widget: contact_info.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Contact_Info_Widget extends Section_Widget {

	protected function component_type() {
		return 'contact_info';
	}

	protected function defaults_source() {
		return array( 'contact', 'contact_info' );
	}
}
