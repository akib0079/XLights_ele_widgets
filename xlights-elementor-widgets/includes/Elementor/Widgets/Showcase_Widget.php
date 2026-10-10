<?php
/**
 * Standalone section widget: feature explorer.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Showcase_Widget extends Section_Widget {

	protected function component_type() {
		return 'showcase';
	}

	protected function defaults_source() {
		return array( 'deck', 'details' );
	}
}
