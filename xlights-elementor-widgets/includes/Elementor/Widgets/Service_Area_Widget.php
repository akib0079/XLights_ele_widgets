<?php
/**
 * Standalone section widget: service_area.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Service_Area_Widget extends Section_Widget {

	protected function component_type() {
		return 'service_area';
	}

	protected function defaults_source() {
		return array( 'home', 'service_area' );
	}
}
