<?php
/**
 * Standalone section widget: reviews.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Reviews_Widget extends Section_Widget {

	protected function component_type() {
		return 'reviews';
	}

	protected function defaults_source() {
		return array( 'home', 'reviews' );
	}
}
