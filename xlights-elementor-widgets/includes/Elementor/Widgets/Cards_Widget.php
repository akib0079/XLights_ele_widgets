<?php
/**
 * Standalone section widget: cards.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Cards_Widget extends Section_Widget {

	protected function component_type() {
		return 'cards';
	}

	protected function defaults_source() {
		return array( 'home', 'why' );
	}
}
