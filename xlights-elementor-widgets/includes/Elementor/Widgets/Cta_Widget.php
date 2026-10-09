<?php
/**
 * Standalone section widget: cta.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Cta_Widget extends Section_Widget {

	protected function component_type() {
		return 'cta';
	}

	protected function defaults_source() {
		return array( 'home', 'cta' );
	}
}
