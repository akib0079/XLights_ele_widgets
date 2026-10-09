<?php
/**
 * Standalone section widget: financing.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Financing_Widget extends Section_Widget {

	protected function component_type() {
		return 'financing';
	}

	protected function defaults_source() {
		return array( 'home', 'financing' );
	}
}
