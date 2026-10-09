<?php
/**
 * Standalone section widget: faq.
 *
 * @package XLights
 */

namespace XLights\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;

class Faq_Widget extends Section_Widget {

	protected function component_type() {
		return 'faq';
	}

	protected function defaults_source() {
		return array( 'soffit', 'faq' );
	}
}
