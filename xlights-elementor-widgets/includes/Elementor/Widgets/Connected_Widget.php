<?php
/**
 * Standalone section widget: connected.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Connected_Widget extends Section_Widget {

	protected function component_type() {
		return 'connected';
	}

	protected function defaults_source() {
		return array( 'home', 'connected' );
	}
}
