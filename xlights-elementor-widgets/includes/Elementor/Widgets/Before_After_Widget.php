<?php
/**
 * Standalone section widget: before_after.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Before_After_Widget extends Section_Widget {

	protected function component_type() {
		return 'before_after';
	}

	protected function defaults_source() {
		return array( 'projects', 'before_after' );
	}
}
