<?php
/**
 * Standalone section widget: specs.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Specs_Widget extends Section_Widget {

	protected function component_type() {
		return 'specs';
	}

	protected function defaults_source() {
		return array( 'soffit', 'specs' );
	}
}
