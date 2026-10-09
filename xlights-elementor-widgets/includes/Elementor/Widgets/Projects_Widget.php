<?php
/**
 * Standalone section widget: projects.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Projects_Widget extends Section_Widget {

	protected function component_type() {
		return 'projects';
	}

	protected function defaults_source() {
		return array( 'home', 'projects' );
	}
}
