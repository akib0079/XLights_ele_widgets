<?php
/**
 * Standalone section widget: hero.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Hero_Widget extends Section_Widget {

	protected function component_type() {
		return 'hero';
	}

	protected function defaults_source() {
		return array( 'home', 'hero' );
	}
}
