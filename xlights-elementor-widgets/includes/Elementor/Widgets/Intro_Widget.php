<?php
/**
 * Standalone section widget: intro.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Intro_Widget extends Section_Widget {

	protected function component_type() {
		return 'intro';
	}

	protected function defaults_source() {
		return array( 'home', 'intro' );
	}
}
