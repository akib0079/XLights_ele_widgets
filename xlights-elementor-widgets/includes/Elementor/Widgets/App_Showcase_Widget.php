<?php
/**
 * Standalone section widget: app_showcase.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class App_Showcase_Widget extends Section_Widget {

	protected function component_type() {
		return 'app_showcase';
	}

	protected function defaults_source() {
		return array( 'home', 'app' );
	}
}
