<?php
/**
 * Standalone section widget: trust.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Trust_Widget extends Section_Widget {

	protected function component_type() {
		return 'trust';
	}

	protected function defaults_source() {
		return array( 'about', 'trust' );
	}
}
