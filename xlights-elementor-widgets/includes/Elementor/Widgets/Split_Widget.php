<?php
/**
 * Standalone section widget: split.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Split_Widget extends Section_Widget {

	protected function component_type() {
		return 'split';
	}

	protected function defaults_source() {
		return array( 'home', 'soffit' );
	}
}
